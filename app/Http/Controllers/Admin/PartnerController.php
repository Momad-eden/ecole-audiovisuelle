<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePartnerRequest;
use App\Http\Requests\Admin\UpdatePartnerRequest;
use App\Models\Partner;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PartnerController extends Controller
{
    public function __construct(
        protected FileUploadService $fileUploadService
    ) {}

    public function index(): View
    {
        $partners = Partner::latest()->paginate(12);

        return view('admin.partners.index', compact('partners'));
    }

    public function create(): View
    {
        return view('admin.partners.create');
    }

    public function store(StorePartnerRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            $validated['logo'] = $this->fileUploadService->upload($request->file('logo'), 'partners');
        }

        $validated['is_active'] = $request->boolean('is_active');

        Partner::create($validated);

        return redirect()
            ->route('partners.index')
            ->with('success', 'Partenaire ajouté avec succès.');
    }

    public function show(Partner $partner): View
    {
        return view('admin.partners.show', compact('partner'));
    }

    public function edit(Partner $partner): View
    {
        return view('admin.partners.edit', compact('partner'));
    }

    public function update(UpdatePartnerRequest $request, Partner $partner): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            $validated['logo'] = $this->fileUploadService->replace(
                $request->file('logo'),
                $partner->logo,
                'partners'
            );
        }

        $validated['is_active'] = $request->boolean('is_active');

        $partner->update($validated);

        return redirect()
            ->route('partners.index')
            ->with('success', 'Partenaire modifié avec succès.');
    }

    public function destroy(Partner $partner): RedirectResponse
    {
        if ($partner->logo) {
            $this->fileUploadService->delete($partner->logo);
        }

        $partner->delete();

        return redirect()
            ->route('partners.index')
            ->with('success', 'Partenaire supprimé.');
    }
}