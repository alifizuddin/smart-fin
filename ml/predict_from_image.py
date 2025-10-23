import sys
import joblib
from PIL import Image
import pytesseract

# --- Model Paths ---
MODEL_PATH = 'expense_classifier.joblib' # Path to your saved scikit-learn model
# Set this if Tesseract isn't in your system's PATH
# pytesseract.pytesseract.tesseract_cmd = r'/usr/local/bin/tesseract' 
#---------------------# Point this to your Tesseract installation folder (the one containing 'tessdata')
pytesseract.pytesseract.tesseract_cmd = r'C:\Program Files\Tesseract-OCR\tesseract.exe'

# You might ALSO need to point to the tessdata directory itself
# --- ADD THIS LINE ---
TESSDATA_PREFIX = r'C:\Program Files\Tesseract-OCR'

def extract_text_from_image(image_path):
    """
    Uses Tesseract OCR to extract all text from an image.
    """
    try:
        img = Image.open(image_path)
        text = pytesseract.image_to_string(img)
        return text
    except Exception as e:
        print(f"Error reading image or extracting text: {e}", file=sys.stderr)
        return ""

def predict_category(text, model):
    """
    Predicts the category from text using the loaded scikit-learn model.
    """
    if not text.strip():
        return "Uncategorized (No text found)"
        
    try:
        # The model expects a list of descriptions
        prediction = model.predict([text])
        return prediction[0]
    except Exception as e:
        print(f"Error during prediction: {e}", file=sys.stderr)
        return "Uncategorized (Prediction Error)"

# --- Main execution ---
if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Usage: python predict_from_image.py <path_to_image>", file=sys.stderr)
        sys.exit(1)

    image_path = sys.argv[1]

    # 1. Load the NLP model
    try:
        model = joblib.load(MODEL_PATH)
    except FileNotFoundError:
        print(f"Error: Model file '{MODEL_PATH}' not found.", file=sys.stderr)
        print("Please run train.py first.", file=sys.stderr)
        sys.exit(1)
    
    # 2. Extract text from the image
    extracted_text = extract_text_from_image(image_path)
    
    # 3. Predict category from the extracted text
    category = predict_category(extracted_text, model)
    
    # 4. Print the final category
    # This is the *only* thing PHP will capture from stdout
    print(category)