<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingRequest;
use App\Models\Setting;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(
        protected FileUploadService $fileUploadService
    ) {}

    public function index(): View
    {
        $settings = Setting::first();

        if (!$settings) {
            $settings = Setting::create([
                'school_name' => 'École de Formation Audiovisuelle',
            ]);
        }

        return view('admin.settings.index', compact('settings'));
    }

    public function update(UpdateSettingRequest $request): RedirectResponse
    {
        $settings = Setting::first() ?? new Setting();

        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            $validated['logo'] = $this->fileUploadService->replace(
                $request->file('logo'),
                $settings->logo,
                'settings'
            );
        }

        $settings->fill($validated);
        $settings->save();

        return redirect()
            ->route('settings.index')
            ->with('success', 'Paramètres enregistrés avec succès.');
    }
}