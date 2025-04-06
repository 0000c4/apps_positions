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
        $appId = env('APP_ID');
        $countryId = env('COUNTRY_ID');

        $this->info("Fetching data for date: {$dateFormatted}");

        try {
            $response = Http::get("https://api.apptica.com/package/top_history/{$appId}/{$countryId}", [
                'date_from' => $dateFormatted,
                'date_to' => $dateFormatted
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
        $batchData = [];
        
        // Для каждой категории находим лучшую позицию
        foreach ($data as $categoryId => $subCategories) {
            $bestPosition = null;
            
            foreach ($subCategories as $subCategory) {
                if (isset($subCategory[$date])) {
                    $position = (int) $subCategory[$date];
                    if ($bestPosition === null || $position < $bestPosition) {
                        $bestPosition = $position;
                    }
                }
            }
            
            if ($bestPosition !== null) {
                $batchData[] = [
                    'date' => $date,
                    'app_id' => $appId,
                    'country_id' => $countryId,
                    'category_id' => $categoryId,
                    'position' => $bestPosition,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        if (!empty($batchData)) {
            AppTopPosition::upsert(
                $batchData,
                ['date', 'app_id', 'country_id', 'category_id'], 
                ['position', 'updated_at']
            );
        }
    }
}