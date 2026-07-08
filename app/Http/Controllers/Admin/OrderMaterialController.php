<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderMaterial;
use Illuminate\Http\Request;

class OrderMaterialController extends Controller
{
    public function store(Request $request, Order $order)
    {
        $validated = $request->validate([
            'material_name' => 'required|string|max:255',
            'length' => 'nullable|string|max:100',
            'shape' => 'nullable|string|max:100',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $validated['subtotal'] = $validated['quantity'] * $validated['unit_price'];

        $order->materials()->create($validated);

        // Update total material cost
        $totalMaterialCost = $order->materials()->sum('subtotal');
        $order->update(['total_material_cost' => $totalMaterialCost]);

        return redirect()->back()->with('success', 'Bahan berhasil ditambahkan.');
    }

    public function destroy(OrderMaterial $material)
    {
        $order = $material->order;
        $material->delete();

        // Update total material cost
        $totalMaterialCost = $order->materials()->sum('subtotal');
        $order->update(['total_material_cost' => $totalMaterialCost]);

        return redirect()->back()->with('success', 'Bahan berhasil dihapus.');
    }
}
