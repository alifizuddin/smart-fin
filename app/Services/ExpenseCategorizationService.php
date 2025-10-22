<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExpenseCategorizationService
{
    /**
     * A list of your company's official expense categories.
     */
    private $categories = [
        'Travel',
        'Food & Drink',
        'Office Supplies',
        'Software & Subscriptions',
        'Utilities',
        'Client Entertainment',
        'Other',
    ];

    /**
     * Categorizes an expense description using the Google Gemini API.
     */
    public function categorizeWithAI(string $description): string
    {
        $apiKey = config('services.gemini.key');
        if (!$apiKey) {
            Log::error('GEMINI_API_KEY is not set.');
            return 'Uncategorized'; // Fallback
        }

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $apiKey;
        
        // Create the prompt for the AI
        $prompt = sprintf(
            "Please classify the following expense description into one of these categories: [%s].
            Respond with only the category name and nothing else.
            Description: \"%s\"
            Category:",
            implode(', ', $this->categories),
            $description
        );

        try {
            $response = Http::timeout(10)->post($url, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ]
            ]);

            if ($response->successful()) {
                // Extract the text from the AI's response
                $category = $response->json('candidates.0.content.parts.0.text');
                
                // Clean up the response (e.g., remove extra whitespace)
                $category = trim($category);
                
                // Ensure the AI gave us a valid category
                if (in_array($category, $this->categories)) {
                    return $category;
                }
            }
            
            // If the API fails or gives a weird response, log it and fallback.
            Log::warning('Gemini API call failed or returned invalid category.', [
                'response' => $response->body()
            ]);
            return 'Uncategorized';

        } catch (\Exception $e) {
            Log::error('Error calling Gemini API: ' . $e->getMessage());
            return 'Uncategorized';
        }
    }
    private $keywordMap = [
        'Travel'               => ['flight', 'airline', 'hotel', 'uber', 'grab', 'taxi', 'mileage'],
        'Client Entertainment' => ['client lunch', 'client dinner', 'entertainment'],
        'Food & Drink'         => ['lunch', 'dinner', 'coffee', 'starbucks', 'mcdonalds', 'restaurant'],
        'Software & Subscriptions' => ['saas', 'adobe', 'subscription', 'aws', 'github'],
        'Office Supplies'      => ['stationery', 'paper', 'staples', 'office depot'],
        'Utilities'            => ['electric bill', 'water bill', 'internet'],
    ];

    /**
     * Categorizes an expense description using simple keyword matching.
     */
    public function categorizeWithKeywords(string $description): string
    {
        $description = strtolower($description);

        foreach ($this->keywordMap as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($description, $keyword)) {
                    return $category;
                }
            }
        }

        return 'Uncategorized'; // Fallback
    }
}