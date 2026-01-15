<?php

namespace App\Services;

use App\Models\Forecast;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class N8NService
{
    /**
     * Trigger the N8N workflow to generate a forecast.
     * 
     * @param string|null $category
     * @return bool
     */
    public function generateForecast($category = null)
    {
        // 1. Gather Historical Data
        $expenses = Expense::query()
            ->when($category, fn($q) => $q->where('category', $category))
            ->where('status', 'approved')
            ->where('created_at', '>=', Carbon::now()->subMonths(12)) // Last 12 months
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m-01") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $payload = [
            'historical_data' => $expenses->toArray(),
            'category' => $category ?? 'all',
            'target_date' => Carbon::now()->addMonth()->format('Y-m-d')
        ];

        // 2. Call N8N Webhook (Placeholder URL)
        // In production, fetch this from config('services.n8n.forecast_url')
        try {
            // Check if we have a configured URL, otherwise mock it for demo purposes
            $webhookUrl = config('services.n8n.forecast_url');
            
            if ($webhookUrl) {
                $response = Http::post($webhookUrl, $payload);
                $data = $response->json();
            } else {
                // MOCK RESPONSE for Demo
                $data = $this->mockN8NResponse($payload);
            }

            // 3. Save Forecast to DB
            // 3. Save Forecast to DB
            if (isset($data['projections']) && is_array($data['projections'])) {
                foreach ($data['projections'] as $projection) {
                    Forecast::updateOrCreate(
                        [
                            'forecast_date' => Carbon::parse($projection['date']),
                            'category' => $category
                        ],
                        [
                            'amount' => $projection['amount'],
                            'confidence_score' => $data['confidence_score'] ?? 0.85,
                            'insights' => $projection['reasoning'] ?? 'Generated via N8N Service'
                        ]
                    );
                }
                return true;
            } elseif (isset($data['forecast_amount'])) {
                // Fallback for single month
                Forecast::updateOrCreate(
                    [
                        'forecast_date' => Carbon::parse($data['target_date'] ?? Carbon::now()->addMonth()),
                        'category' => $category
                    ],
                    [
                        'amount' => $data['forecast_amount'],
                        'confidence_score' => $data['confidence'] ?? 0.85,
                        'insights' => $data['reasoning'] ?? 'Generated via Mock N8N Service'
                    ]
                );
                return true;
            }

            return false;
        } catch (\Exception $e) {
            \Log::error('N8N Forecast Error: ' . $e->getMessage());
            return false;
        }
    }

    private function mockN8NResponse($payload)
    {
        // Simple mock logic: Average of last 3 months with slight growth
        $history = collect($payload['historical_data']);
        $last3 = $history->take(-3);
        $avg = $last3->avg('total') ?: 5000;
        
        $projections = [];
        $startDate = Carbon::parse($payload['target_date']);
        $growthRate = 1.05; // 5% monthly growth

        for ($i = 0; $i < 3; $i++) {
            $monthDate = $startDate->copy()->addMonths($i);
            $growthFactor = 1 + ($i * 0.02) + 0.05; // Base 5% + 2% per month
            
            $projections[] = [
                'date' => $startDate->format('Y-m-01'),
                'amount' => round($avg * pow($growthRate, $i), 2),
                'reasoning' => sprintf('Projected %s based on historical average with %.1f%% monthly growth', 
                    $startDate->format('F Y'), 
                    ($growthRate - 1) * 100
                )
            ];
            $startDate->addMonth();
        }
        
        return [
            'projections' => $projections,
            'confidence_score' => 0.88,
        ];
    }
}
