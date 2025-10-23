import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.naive_bayes import MultinomialNB
from sklearn.pipeline import Pipeline
from sklearn.metrics import accuracy_score
import joblib # Used to save the model

# 1. Load Data
try:
    data = pd.read_csv('expenses.csv')
except FileNotFoundError:
    print("Error: expenses.csv not found.")
    print("Please create it with 'description' and 'category' columns.")
    exit()

# Drop any rows with missing data
data.dropna(subset=['description', 'category'], inplace=True)

# 2. Define Features (X) and Labels (y)
X = data['description']
y = data['category']

# 3. Split data for training and testing
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

print(f"Training on {len(X_train)} samples, testing on {len(X_test)} samples.")

# 4. Create the Model Pipeline
# The pipeline bundles the vectorizer and the classifier together
model_pipeline = Pipeline([
    ('vectorizer', TfidfVectorizer(stop_words='english')),
    ('classifier', MultinomialNB())
])

# 5. Train the Model
print("Training model...")
model_pipeline.fit(X_train, y_train)

# 6. (Optional) Evaluate the Model
predictions = model_pipeline.predict(X_test)
accuracy = accuracy_score(y_test, predictions)
print(f"Model trained! Accuracy on test data: {accuracy * 100:.2f}%")

# 7. Save the trained model
model_filename = 'expense_classifier.joblib'
joblib.dump(model_pipeline, model_filename)

print(f"Model saved as '{model_filename}'")