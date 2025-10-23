import os
import pytesseract
from flask import Flask, request, jsonify
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

def categorize_with_ai(description):
    """Categorizes description using the Gemini AI."""
    
    # --- NEW DEBUGGING CHECKS ---
    if not model:
        print("--- AI check failed: API key is not set (model is None). ---")
        return 'Uncategorized'
    
    if not description.strip():
        print("--- AI check failed: Extracted text is empty. ---")
        return 'Uncategorized'
    # ----------------------------
        
    # --- PROMPT (Copied directly from ExpenseCategorizationService.php) ---
    category_list = ", ".join(CATEGORIES)
    prompt = (
        f"Please classify the following expense description into one of these categories: [{category_list}].\n"
        f"Respond with only the category name and nothing else.\n"
        f"Description: \"{description}\"\n"
        f"Category:"
    )

    try:
        response = model.generate_content(prompt)
        category = response.text.strip()
        
        # Ensure AI gives a valid category
        if category in CATEGORIES:
            return category
        else:
            print(f"--- AI Warning: AI returned an invalid category: {category} ---")
            return 'Uncategorized' # AI gave a weird response
    except Exception as e:
        print(f"--- AI ERROR: Error calling Gemini API: {e} ---")
        return 'Uncategorized'

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
        else:
            # --- 3. AI Categorization (Level 3) ---
            print("--- Calling AI for categorization... ---")
            category = categorize_with_ai(extracted_text)
            
            # If AI fails or returns 'Uncategorized', try keywords as a backup
            if category == 'Uncategorized':
                print("--- AI failed or uncategorized, falling back to keywords... ---")
                category = categorize_with_keywords(extracted_text)

        # 4. Return the data as JSON
        print(f"--- Final Category: {category} ---")
        return jsonify({
            'category': category,
            'description': extracted_text
        })

    except Exception as e:
        print(f"An error occurred during processing: {e}")
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True, port=5010)