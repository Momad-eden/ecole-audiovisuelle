<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::latest()->paginate(12);

        return view('admin.galleries.index', compact('galleries'));
    }

    public function create()
    {
        return view('admin.galleries.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'type' => 'required|in:image,video',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];

        if ($request->type === 'image') {
            $rules['file'] = 'required|image|mimes:jpg,jpeg,png,webp|max:10240';
        }

        if ($request->type === 'video') {
            $rules['youtube_url'] = [
                'required',
                'url',
                'regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\//i',
            ];
        }

        $validated = $request->validate($rules);

        $data = [
            'title' => $validated['title'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->type === 'image') {

            $data['file_path'] = $request
                ->file('file')
                ->store('gallery', 'public');

            $data['youtube_url'] = null;
        }

        if ($request->type === 'video') {

            $data['file_path'] = null;

            $data['youtube_url'] = $validated['youtube_url'];
        }

        Gallery::create($data);

        return redirect()
            ->route('galleries.index')
            ->with('success', 'Média ajouté à la galerie.');
    }

    public function show(Gallery $gallery)
    {
        return view('admin.galleries.show', compact('gallery'));
    }

    public function edit(Gallery $gallery)
    {
        return view('admin.galleries.edit', compact('gallery'));
    }

    public function update(Request $request, Gallery $gallery)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'type' => 'required|in:image,video',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];

        if ($request->type === 'image') {
            $rules['file'] = 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240';
        }

        if ($request->type === 'video') {
            $rules['youtube_url'] = [
                'required',
                'url',
                'regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\//i',
            ];
        }

        $validated = $request->validate($rules);

        $data = [
            'title' => $validated['title'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
        ];

        /*
        |--------------------------------------------------------------------------
        | IMAGE
        |--------------------------------------------------------------------------
        */

        if ($request->type === 'image') {

            $data['youtube_url'] = null;

            if ($request->hasFile('file')) {

                if ($gallery->file_path) {
                    Storage::disk('public')->delete($gallery->file_path);
                }

                $data['file_path'] = $request
                    ->file('file')
                    ->store('gallery', 'public');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | VIDEO YOUTUBE
        |--------------------------------------------------------------------------
        */

        if ($request->type === 'video') {

            if ($gallery->file_path) {
                Storage::disk('public')->delete($gallery->file_path);
            }

            $data['file_path'] = null;
            $data['youtube_url'] = $validated['youtube_url'];
        }

        $gallery->update($data);

        return redirect()
            ->route('galleries.index')
            ->with('success', 'Média modifié avec succès.');
    }

    public function destroy(Gallery $gallery)
    {
        if ($gallery->file_path) {
            Storage::disk('public')->delete($gallery->file_path);
        }

        $gallery->delete();

        return redirect()
            ->route('galleries.index')
            ->with('success', 'Média supprimé.');
    }
}