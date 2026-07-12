<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ServiceManagementController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('sort_order')->get();
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'description'       => 'required|string',
            'icon'              => 'nullable|string|max:100',
            'image'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'price_start'       => 'required|numeric|min:0',
            'price_unit'        => 'required|string|max:50',
            'is_featured'       => 'boolean',
            'is_active'         => 'boolean',
            'sort_order'        => 'integer',
        ]);

        $validated['slug']        = Str::slug($validated['name']);
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active']   = $request->has('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = $this->storeImage($request);
        }

        Service::create($validated);

        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil ditambahkan!');
    }

    public function edit(Service $service)
    {
        return view('admin.services.form', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'description'       => 'required|string',
            'icon'              => 'nullable|string|max:100',
            'image'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'price_start'       => 'required|numeric|min:0',
            'price_unit'        => 'required|string|max:50',
            'is_featured'       => 'boolean',
            'is_active'         => 'boolean',
            'sort_order'        => 'integer',
        ]);

        $validated['slug']        = Str::slug($validated['name']);
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active']   = $request->has('is_active');

        if ($request->boolean('remove_image')) {
            $this->deleteImage($service->image);
            $validated['image'] = null;
        }

        if ($request->hasFile('image')) {
            $this->deleteImage($service->image);
            $validated['image'] = $this->storeImage($request);
        }

        $service->update($validated);

        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil diperbarui!');
    }

    public function destroy(Service $service)
    {
        $this->deleteImage($service->image);
        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil dihapus!');
    }

    protected function storeImage(Request $request): string
    {
        $directory = public_path('uploads/services');

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $file     = $request->file('image');
        $filename = now()->format('YmdHis') . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $filename);

        return 'uploads/services/' . $filename;
    }

    protected function deleteImage(?string $imagePath): void
    {
        if (blank($imagePath)) {
            return;
        }

        $relativePath = ltrim($imagePath, '/');
        $absolutePath = public_path($relativePath);

        if (
            str_starts_with(str_replace('\\', '/', $absolutePath), str_replace('\\', '/', public_path('uploads/services')))
            && File::exists($absolutePath)
        ) {
            File::delete($absolutePath);
        }
    }
}
