<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function index(): View
    {
        Gate::authorize('brands.view');

        $brands = Brand::latest()->paginate(10);

        return view('admin.brands.index', compact('brands'));
    }

    public function create(): View
    {
        Gate::authorize('brands.create');

        return view('admin.brands.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('brands.create');

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        Brand::create($validated);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Marca creada correctamente.');
    }

    public function edit(Brand $brand): View
    {
        Gate::authorize('brands.edit');

        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand): RedirectResponse
    {
        Gate::authorize('brands.edit');

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,'.$brand->id,
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $brand->update($validated);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Marca actualizada correctamente.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        Gate::authorize('brands.delete');

        $brand->delete();

        return redirect()->route('admin.brands.index')
            ->with('success', 'Marca eliminada correctamente.');
    }
}
