<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::latest()->paginate(12);

        return view('admin.partners.index', compact('partners'));
    }

    public function create()
    {
        return view('admin.partners.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:5120',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request
                ->file('logo')
                ->store('partners', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        Partner::create($validated);

        return redirect()
            ->route('partners.index')
            ->with('success', 'Partenaire ajouté avec succès.');
    }

    public function show(Partner $partner)
    {
        return view('admin.partners.show', compact('partner'));
    }

    public function edit(Partner $partner)
    {
        return view('admin.partners.edit', compact('partner'));
    }

    public function update(Request $request, Partner $partner)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:5120',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('logo')) {

            if ($partner->logo) {
                Storage::disk('public')->delete($partner->logo);
            }

            $validated['logo'] = $request
                ->file('logo')
                ->store('partners', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $partner->update($validated);

        return redirect()
            ->route('partners.index')
            ->with('success', 'Partenaire modifié avec succès.');
    }

    public function destroy(Partner $partner)
    {
        if ($partner->logo) {
            Storage::disk('public')->delete($partner->logo);
        }

        $partner->delete();

        return redirect()
            ->route('partners.index')
            ->with('success', 'Partenaire supprimé.');
    }
}