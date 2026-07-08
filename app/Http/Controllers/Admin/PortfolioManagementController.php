<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PortfolioManagementController extends Controller
{
    public function index()
    {
        $portfolios = Portfolio::latest('completion_date')->latest()->get();

        return view('admin.portfolios.index', compact('portfolios'));
    }

    public function create()
    {
        return view('admin.portfolios.form');
    }

    public function store(Request $request)
    {
        $validated = $this->validatePortfolio($request);
        $validated['slug'] = $this->generateUniqueSlug($validated['title']);
        $validated['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            $validated['image'] = $this->storeImage($request);
        }

        Portfolio::create($validated);

        return redirect()
            ->route('admin.portfolios.index')
            ->with('success', 'Portofolio berhasil ditambahkan.');
    }

    public function edit(Portfolio $portfolio)
    {
        return view('admin.portfolios.form', compact('portfolio'));
    }

    public function update(Request $request, Portfolio $portfolio)
    {
        $validated = $this->validatePortfolio($request, $portfolio);
        $validated['slug'] = $this->generateUniqueSlug($validated['title'], $portfolio->id);
        $validated['is_featured'] = $request->boolean('is_featured');

        if ($request->boolean('remove_image')) {
            $this->deleteImage($portfolio->image);
            $validated['image'] = null;
        }

        if ($request->hasFile('image')) {
            $this->deleteImage($portfolio->image);
            $validated['image'] = $this->storeImage($request);
        }

        $portfolio->update($validated);

        return redirect()
            ->route('admin.portfolios.index')
            ->with('success', 'Portofolio berhasil diperbarui.');
    }

    public function destroy(Portfolio $portfolio)
    {
        $this->deleteImage($portfolio->image);
        $portfolio->delete();

        return redirect()
            ->route('admin.portfolios.index')
            ->with('success', 'Portofolio berhasil dihapus.');
    }

    protected function validatePortfolio(Request $request, ?Portfolio $portfolio = null): array
    {
        return $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('portfolios', 'title')->ignore($portfolio?->id),
            ],
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'completion_date' => 'required|date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'is_featured' => 'nullable|boolean',
            'remove_image' => 'nullable|boolean',
        ]);
    }

    protected function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $suffix = 1;

        while (
            Portfolio::query()
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }

    protected function storeImage(Request $request): string
    {
        $directory = public_path('uploads/portfolios');

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $file = $request->file('image');
        $filename = now()->format('YmdHis') . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $filename);

        return 'uploads/portfolios/' . $filename;
    }

    protected function deleteImage(?string $imagePath): void
    {
        if (blank($imagePath)) {
            return;
        }

        $relativePath = ltrim($imagePath, '/');
        $absolutePath = public_path($relativePath);

        if (str_starts_with(str_replace('\\', '/', $absolutePath), str_replace('\\', '/', public_path('uploads/portfolios'))) && File::exists($absolutePath)) {
            File::delete($absolutePath);
        }
    }
}
