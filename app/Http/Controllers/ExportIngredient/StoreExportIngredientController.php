<?php

namespace App\Http\Controllers\ExportIngredient;

use App\Http\Controllers\Controller;
use App\Models\ExportIngredient;
use App\Models\ExportIngredientDetail;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StoreExportIngredientController extends Controller
{
    public function store(Request $request): JsonResponse
    {

        $auth = Auth::user();
        $validator = Validator::make($request->all(), [
            'note' => 'nullable|string|max:255',
            'details' => 'required|array',

            'details.*.ingredient_id' => 'required|integer',
            'details.*.quantity' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        try {
            // Tạo nguyên liệu mới
            $exportIngredient = ExportIngredient::create([
                'creator_id' => $auth->id,
                'note' => $request->note,
            ]);
            if($exportIngredient){
                foreach ($request->details as $detail) {
                    ExportIngredientDetail::create([
                        'export_ingredient_id' => $exportIngredient->id,
                        'ingredient_id' => $detail['ingredient_id'],
                        'quantity' => $detail['quantity'],
                    ]);
                    $ingredient = Ingredient::find($detail['ingredient_id']);
                    $ingredient->quantity -= $detail['quantity'];
                    $ingredient->save();
                }
            }
            return new JsonResponse([
                'success' => true,
                'message' => 'Phiếu xuất nguyên liệu được tạo thành công',
                'data' => $exportIngredient
            ], JsonResponse::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tạo phiếu xuất nguyên liệu thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}