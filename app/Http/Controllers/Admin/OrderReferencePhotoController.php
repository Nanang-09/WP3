<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderReferencePhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrderReferencePhotoController extends Controller
{
    public function store(Request $request, Order $order)
    {
        $request->validate([
            'photo'   => 'required|image|mimes:jpg,jpeg,png,webp|max:5120', // max 5MB
            'caption' => 'nullable|string|max:255',
        ], [
            'photo.required' => 'Foto wajib dipilih.',
            'photo.image'    => 'File harus berupa gambar.',
            'photo.max'      => 'Ukuran foto maksimal 5MB.',
        ]);

        $path = $request->file('photo')->store('order-photos/' . $order->id, 'public');

        $order->referencePhotos()->create([
            'uploaded_by' => auth()->id(),
            'photo_path'  => $path,
            'caption'     => $request->caption,
        ]);

        return redirect()->back()->with('success', 'Foto referensi berhasil diupload.');
    }

    public function destroy(OrderReferencePhoto $photo)
    {
        // Delete physical file from storage
        Storage::disk('public')->delete($photo->photo_path);
        $photo->delete();

        return redirect()->back()->with('success', 'Foto referensi berhasil dihapus.');
    }
}
