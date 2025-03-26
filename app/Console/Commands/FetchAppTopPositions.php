<?php

namespace App\Console\Commands;

use App\Models\AppTopPosition;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchAppTopPositions extends Command
{
    protected $signature = 'app:fetch-top-positions {date? : Date in Y-m-d format}';
    protected $description = 'Fetch app top positions data from Apptica API';

    public function handle()
    {
        $date = $this->argument('date') ? Carbon::parse($this->argument('date')) : Carbon::yesterday();
        $dateFormatted = $date->format('Y-m-d');
        $appId = '1421444'; // Among Us
        $countryId = '1';   // United States

        $this->info("Fetching data for date: {$dateFormatted}");

        try {
            $response = Http::get("https://api.apptica.com/package/top_history/{$appId}/{$countryId}", [
                'date_from' => $dateFormatted,
                'date_to' => $dateFormatted,
                'B4NKGg' => 'fVN5Q9KVOlOHDx9mOsKPAQsFBlEhBOwguLkNEDTZvKzJzT3l'
            ]);

            if ($response->successful()) {
                $data = $response->json()['data'];
                $this->processApiData($data, $dateFormatted, $appId, $countryId);
                $this->info('Data successfully fetched and processed.');
            } else {
                $this->error('API request failed: ' . $response->body());
                Log::error('Apptica API request failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('Error fetching data: ' . $e->getMessage());
            Log::error('Error fetching Apptica data: ' . $e->getMessage());
        }
    }

    protected function processApiData($data, $date, $appId, $countryId)
    {
        // Удаляем старые данные за эту дату, чтобы избежать дубликатов
        AppTopPosition::where([
            'date' => $date,
            'app_id' => $appId,
            'country_id' => $countryId
        ])->delete();

        // Для каждой категории находим лучшую позицию
        foreach ($data as $categoryId => $subCategories) {
            $bestPosition = PHP_INT_MAX;
            
            foreach ($subCategories as $subCategory) {
                if (isset($subCategory[$date])) {
                    $position = (int) $subCategory[$date];
                    if ($position < $bestPosition) {
                        $bestPosition = $position;
                    }
                }
            }
            
            if ($bestPosition < PHP_INT_MAX) {
                AppTopPosition::create([
                    'date' => $date,
                    'app_id' => $appId,
                    'country_id' => $countryId,
                    'category_id' => $categoryId,
                    'position' => $bestPosition
                ]);
            }
        }
    }
}