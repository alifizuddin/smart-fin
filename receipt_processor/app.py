import os
import pytesseract
from flask import Flask, request, jsonify
import json
from PIL import Image
import cv2
import numpy as np
import google.generativeai as genai

# --- TESSERACT CONFIG (Your fix) ---
# This ensures Python can find your Tesseract installation
os.environ['TESSDATA_PREFIX'] = r'C:\Program Files\Tesseract-OCR\tessdata'
pytesseract.tesseract_cmd = r'C:\Program Files\Tesseract-OCR\tesseract.exe'

app = Flask(__name__)

# --- AI CONFIG ---
# Load the API key you set in the terminal
# API_KEY = os.getenv('GEMINI_API_KEY')
API_KEY = "AIzaSyCkC5_Gjj-9Mtk9RqozeCxsZy75KzOzR78"

model = None
if API_KEY:
    genai.configure(api_key=API_KEY)
    # Use the same model as your PHP file
    model = genai.GenerativeModel('gemini-2.5-pro') 
else:
    print("WARNING: GEMINI_API_KEY environment variable is not set. AI categorization will be skipped.")

# --- CATEGORIES (Copied from ExpenseCategorizationService.php) ---
CATEGORIES = [
    'Travel',
    'Food & Drink',
    'Office Supplies',
    'Software & Subscriptions',
    'Utilities',
    'Client Entertainment',
    'Other',
]

# --- FALLBACK KEYWORDS (Copied from ExpenseCategorizationService.php) ---
KEYWORD_MAP = {
    'Travel': ['flight', 'airline', 'hotel', 'uber', 'grab', 'taxi', 'mileage'],
    'Client Entertainment': ['client lunch', 'client dinner', 'entertainment'],
    'Food & Drink': ['lunch', 'dinner', 'coffee', 'starbucks', 'mcdonalds', 'restaurant'],
    'Software & Subscriptions': ['saas', 'adobe', 'subscription', 'aws', 'github'],
    'Office Supplies': ['stationery', 'paper', 'staples', 'office depot'],
    'Utilities': ['electric bill', 'water bill', 'internet'],
}

def categorize_with_keywords(description):
    """Fallback categorizer using simple keywords."""
    description_lower = description.lower()
    for category, keywords in KEYWORD_MAP.items():
        for keyword in keywords:
            if keyword in description_lower:
                return category
    return 'Uncategorized' # Fallback

def analyze_receipt_with_ai(description):
    """Analyzes receipt text using Gemini AI to extract key information and categorize.

    Returns a tuple: (category, summary, data_dict)
    data_dict will contain keys: amount, payment_method, date, merchant, items
    """

    # --- NEW DEBUGGING CHECKS ---
    if not model:
        print("--- AI check failed: API key is not set (model is None). ---")
        return 'Uncategorized', '', {}

    if not description.strip():
        print("--- AI check failed: Extracted text is empty. ---")
        return 'Uncategorized', '', {}
    # ----------------------------

    # --- Enhanced AI PROMPT (request JSON output) ---
    category_list = ", ".join(CATEGORIES)
    prompt = f'''You are a helpful assistant that reads OCR'd receipt text and returns structured JSON.
Output ONLY a single JSON object (no extra text) with this shape:
{{
  "category": "<one of: {category_list} or 'Uncategorized'>",
  "description": "<1-2 sentence human-readable summary>",
  "data": {{
    "amount": "<numeric amount, e.g. 123.45>",
    "payment_method": "<cash|credit card|debit card|bank transfer|not specified>",
    "date": "<YYYY-MM-DD if available>",
    "merchant": "<merchant name if available>",
    "items": "<short list of important items or services>"
  }}
}}

Receipt Text:
"""{description}"""

If a field cannot be determined, output an empty string for it. Keep the JSON valid and compact.'''

    try:
        response = model.generate_content(prompt)
        result = response.text.strip()

        # Try to load JSON directly
        try:
            payload = json.loads(result)
        except Exception:
            # Attempt to extract JSON substring if model included extra text
            start = result.find('{')
            end = result.rfind('}')
            if start != -1 and end != -1 and end > start:
                try:
                    payload = json.loads(result[start:end+1])
                except Exception as e:
                    print(f"--- JSON parse failed: {e} ---")
                    return 'Uncategorized', '', {}
            else:
                print("--- No JSON found in AI response ---")
                return 'Uncategorized', '', {}

        category = payload.get('category', 'Uncategorized')
        description_summary = payload.get('description', '')
        data = payload.get('data', {}) if isinstance(payload.get('data', {}), dict) else {}

        # Normalize amount to numeric-ish string
        amount = data.get('amount', '')
        if isinstance(amount, (int, float)):
            data['amount'] = float(amount)
        else:
            # try to strip non-numeric characters
            import re
            m = re.search(r"[0-9]+(?:\.[0-9]{1,2})?", str(amount))
            data['amount'] = float(m.group(0)) if m else None

        return category, description_summary, data
    except Exception as e:
        print(f"--- AI ERROR: Error calling Gemini API: {e} ---")
        return 'Uncategorized', '', {}

@app.route('/process-receipt', methods=['POST'])
def process_receipt():
    if 'receipt' not in request.files:
        return jsonify({'error': 'No receipt file found'}), 400

    file = request.files['receipt']

    try:
        # --- 1. Image Preprocessing (Level 2) ---
        filestr = file.read()
        npimg = np.frombuffer(filestr, np.uint8)
        image = cv2.imdecode(npimg, cv2.IMREAD_COLOR)
        gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
        thresh = cv2.threshold(gray, 0, 255, cv2.THRESH_BINARY + cv2.THRESH_OTSU)[1]
        
        # --- 2. Pytesseract Extraction ---
        extracted_text = pytesseract.image_to_string(thresh).strip()
        
        print("====================================")
        print("--- PYTESSERACT EXTRACTED TEXT ---")
        print(extracted_text)
        print("------------------------------------")

        if not extracted_text:
            extracted_text = "Could not read receipt image."
            category = "Uncategorized"
            summary = "Receipt text could not be extracted from image."
        else:
            # --- 3. Enhanced AI Analysis ---
            print("--- Calling AI for analysis... ---")
            category, summary, data = analyze_receipt_with_ai(extracted_text)

            # If AI categorization fails, try keywords as a backup
            if category == 'Uncategorized':
                print("--- AI categorization failed, falling back to keywords... ---")
                keyword_category = categorize_with_keywords(extracted_text)
                if keyword_category != 'Uncategorized':
                    category = keyword_category

        # 4. Return the enhanced data as JSON
        print(f"--- Final Category: {category} ---")
        print(f"--- AI Summary: {summary} ---")
        return jsonify({
            'category': category,
            'description': summary if summary else extracted_text,
            'data': data if 'data' in locals() else {},
            'raw_text': extracted_text,  # Include original text for reference
        })

    except Exception as e:
        print(f"An error occurred during processing: {e}")
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True, port=5010)