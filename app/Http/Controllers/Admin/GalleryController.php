<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGalleryRequest;
use App\Http\Requests\Admin\UpdateGalleryRequest;
use App\Models\Gallery;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function __construct(
        protected FileUploadService $fileUploadService
    ) {}

    public function index(): View
    {
        $galleries = Gallery::latest()->paginate(12);

        return view('admin.galleries.index', compact('galleries'));
    }

    public function create(): View
    {
        return view('admin.galleries.create');
    }

    public function store(StoreGalleryRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $data = [
            'title'       => $validated['title'],
            'type'        => $validated['type'],
            'description' => $validated['description'] ?? null,
            'is_active'   => $request->boolean('is_active'),
        ];

        if ($request->input('type') === 'image' && $request->hasFile('file')) {
            $data['file_path'] = $this->fileUploadService->upload($request->file('file'), 'gallery');
            $data['youtube_url'] = null;
        } elseif ($request->input('type') === 'video') {
            $data['file_path'] = null;
            $data['youtube_url'] = $validated['youtube_url'];
        }

        Gallery::create($data);

        return redirect()
            ->route('galleries.index')
            ->with('success', 'Média ajouté à la galerie.');
    }

    public function show(Gallery $gallery): View
    {
        return view('admin.galleries.show', compact('gallery'));
    }

    public function edit(Gallery $gallery): View
    {
        return view('admin.galleries.edit', compact('gallery'));
    }

    public function update(UpdateGalleryRequest $request, Gallery $gallery): RedirectResponse
    {
        $validated = $request->validated();

        $data = [
            'title'       => $validated['title'],
            'type'        => $validated['type'],
            'description' => $validated['description'] ?? null,
            'is_active'   => $request->boolean('is_active'),
        ];

        if ($request->input('type') === 'image') {
            $data['youtube_url'] = null;
            if ($request->hasFile('file')) {
                $data['file_path'] = $this->fileUploadService->replace(
                    $request->file('file'),
                    $gallery->file_path,
                    'gallery'
                );
            }
        } elseif ($request->input('type') === 'video') {
            if ($gallery->file_path) {
                $this->fileUploadService->delete($gallery->file_path);
            }
            $data['file_path'] = null;
            $data['youtube_url'] = $validated['youtube_url'];
        }

        $gallery->update($data);

        return redirect()
            ->route('galleries.index')
            ->with('success', 'Média modifié avec succès.');
    }

    public function destroy(Gallery $gallery): RedirectResponse
    {
        if ($gallery->file_path) {
            $this->fileUploadService->delete($gallery->file_path);
        }

        $gallery->delete();

        return redirect()
            ->route('galleries.index')
            ->with('success', 'Média supprimé.');
    }
}