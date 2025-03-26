<?php

namespace App\Http\Controllers;

use App\Models\AppTopPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppTopCategoryController extends Controller
{
    public function getPositions(Request $request)
    {
        // Валидация параметра date
        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => 'Invalid date format. Please use YYYY-MM-DD format.',
                'errors' => $validator->errors()
            ], 400);
        }

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