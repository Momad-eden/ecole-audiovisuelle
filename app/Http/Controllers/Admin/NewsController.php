<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::latest()->paginate(10);

        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request
                ->file('image')
                ->store('news', 'public');
        }

        $validated['is_published'] = $request->has('is_published');

        if ($validated['is_published'] && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        News::create($validated);

        return redirect()
            ->route('news.index')
            ->with('success', 'Actualité créée avec succès.');
    }

    public function show(News $news)
    {
        return view('admin.news.show', compact('news'));
    }

    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        if ($request->hasFile('image')) {

            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }

            $validated['image'] = $request
                ->file('image')
                ->store('news', 'public');
        }

        $validated['is_published'] = $request->has('is_published');

        if ($validated['is_published'] && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        if (!$validated['is_published']) {
            $validated['published_at'] = null;
        }

        $news->update($validated);

        return redirect()
            ->route('news.index')
            ->with('success', 'Actualité modifiée avec succès.');
    }

    public function destroy(News $news)
    {
        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }

        $news->delete();

        return redirect()
            ->route('news.index')
            ->with('success', 'Actualité supprimée.');
    }
}