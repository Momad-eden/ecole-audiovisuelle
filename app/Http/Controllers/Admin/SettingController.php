<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::first();

        if (!$settings) {
            $settings = Setting::create([
                'school_name' => 'École de Formation Audiovisuelle',
            ]);
        }

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = Setting::first();

        if (!$settings) {
            $settings = new Setting();
        }

        $validated = $request->validate([
            'school_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'whatsapp' => 'nullable|string|max:50',
        ]);

        if ($request->hasFile('logo')) {

            if ($settings->logo) {
                Storage::disk('public')->delete($settings->logo);
            }

            $validated['logo'] = $request
                ->file('logo')
                ->store('settings', 'public');
        }

        $settings->fill($validated);
        $settings->save();

        return redirect()
            ->route('settings.index')
            ->with('success', 'Paramètres enregistrés avec succès.');
    }
}