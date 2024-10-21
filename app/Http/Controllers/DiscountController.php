<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Discount;
use Illuminate\Http\Response;

class DiscountController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        $discount = Discount::create([
            'name' => $request->name,
            'percentage' => $request->percentage,
        ]);

        return response()->json([
            'message' => 'Descuento creado correctamente',
            'discount' => $discount
        ], Response::HTTP_CREATED);
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = $request->input('query');
        if ($query) {
            $discounts = Discount::search($query)->paginate($perPage);
        } else {
            $discounts = Discount::paginate($perPage);
        }
        return response()->json($discounts, Response::HTTP_OK);
    }


    public function show($id)
    {
        $discount = Discount::findOrFail($id);
        return response()->json($discount, Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $discount = Discount::findOrFail($id);

        $request->validate([
            'name' => 'nullable|string|max:100',
            'percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $discount->update($request->all());

        return response()->json([
            'message' => 'Descuento actualizado correctamente',
            'discount' => $discount
        ], Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $discount = Discount::findOrFail($id);
        $discount->delete();

        return response()->json([
            'message' => 'Descuento eliminado correctamente'
        ], Response::HTTP_OK);
    }
}
