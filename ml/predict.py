import sys
import joblib

# 1. Get the description from the command line argument
# sys.argv[0] is the script name, sys.argv[1] is the first argument
if len(sys.argv) < 2:
    print("Usage: python predict.py \"<expense description>\"")
    sys.exit(1)

new_description = sys.argv[1]

# 2. Load the saved model
try:
    model = joblib.load('expense_classifier.joblib')
except FileNotFoundError:
    print("Error: Model file 'expense_classifier.joblib' not found.")
    print("Please run train.py first.")
    sys.exit(1)


# 3. Predict the category
# The model expects a list or array, so we pass [new_description]
try:
    prediction = model.predict([new_description])
    
    # 4. Print the result (this is what PHP will capture)
    print(prediction[0])
    
except Exception as e:
    print(f"Error during prediction: {e}")
    sys.exit(1)