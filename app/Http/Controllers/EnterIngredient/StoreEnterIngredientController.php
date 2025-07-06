<?php

namespace App\Http\Controllers\EnterIngredient;

use App\Http\Controllers\Controller;
use App\Models\EnterIngredient;
use App\Models\EnterIngredientDetail;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StoreEnterIngredientController extends Controller
{
    public function store(Request $request): JsonResponse
    {

        $auth = Auth::user();
        $validator = Validator::make($request->all(), [
            'total_amount' => 'required|integer',
            'note' => 'nullable|string|max:255',
            'details' => 'required|array',

            'details.*.ingredient_id' => 'required|integer',
            'details.*.quantity' => 'required|integer',
            'details.*.unit_price' => 'required|integer',
            'details.*.supplier_name' => 'required|string|max:255',
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
            $enterIngredient = EnterIngredient::create([
                'creator_id' => $auth->id,
                'total_amount' => $request->total_amount,
                'note' => $request->note,
            ]);
            if($enterIngredient){
                foreach ($request->details as $detail) {
                    EnterIngredientDetail::create([
                        'enter_ingredient_id' => $enterIngredient->id,
                        'ingredient_id' => $detail['ingredient_id'],
                        'quantity' => $detail['quantity'],
                        'unit_price' => $detail['unit_price'],
                        'supplier_name' => $detail['supplier_name'],
                    ]);
                    $ingredient = Ingredient::find($detail['ingredient_id']);
                    $ingredient->quantity += $detail['quantity'];
                    $ingredient->save();
                }
            }
            return new JsonResponse([
                'success' => true,
                'message' => 'Phiếu nhập nguyên liệu được tạo thành công',
                'data' => $enterIngredient
            ], JsonResponse::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tạo phiếu nhập nguyên liệu thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}