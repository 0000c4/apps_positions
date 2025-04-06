<?php

namespace App\Http\Controllers;

use App\Models\AppTopPosition;
use Illuminate\Http\Request;
use App\Http\Requests\getPositionsRequest;
use Illuminate\Support\Facades\Validator;

class AppTopCategoryController extends Controller
{
    public function getPositions(getPositionsRequest $request)
    {
        $date = $request->input('date');
        $appId = '1421444'; // Among Us
        $countryId = '1';   // United States

        // Получаем позиции для всех категорий за указанную дату
        $positions = AppTopPosition::where([
            'date' => $date,
            'app_id' => $appId,
            'country_id' => $countryId
        ])->get();

        if ($positions->isEmpty()) {
            return response()->json([
                'status_code' => 404,
                'message' => 'No data found for the specified date',
                'data' => []
            ], 404);
        }

        // Форматируем данные для вывода
        $formattedData = $positions->pluck('position', 'category_id')->toArray();

        return response()->json([
            'status_code' => 200,
            'message' => 'ok',
            'data' => $formattedData
        ]);
    }
}