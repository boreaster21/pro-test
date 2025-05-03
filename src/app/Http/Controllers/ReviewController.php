<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Store;
use App\Http\Requests\ReviewRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    use AuthorizesRequests;

    public function create(Store $store): View|RedirectResponse
    {
        $this->authorize('create', [Review::class, $store]);
        return view('reviews.create', compact('store'));
    }

    public function store(ReviewRequest $request, Store $store): RedirectResponse
    {
        $this->authorize('create', [Review::class, $store]);

        $validated = $request->validated();
        $validated['user_id'] = auth()->id();
        $validated['store_id'] = $store->id;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('reviews', 'public');
            $validated['image_path'] = $imagePath;
        } else {
            $validated['image_path'] = null;
        }

        Review::create($validated);

        return redirect()->route('stores.show', $store)->with('success', '口コミを投稿しました。');
    }

    public function edit(Review $review): View
    {
        $this->authorize('update', $review);

        $store = $review->store;
        return view('reviews.edit', compact('review', 'store'));
    }

    public function update(ReviewRequest $request, Review $review): RedirectResponse
    {
        $this->authorize('update', $review);

        $validated = $request->validated();

        if ($request->hasFile('image')) {
            if ($review->image_path) {
                Storage::disk('public')->delete($review->image_path);
            }
            $path = $request->file('image')->store('reviews', 'public');
            $validated['image_path'] = $path;
        } else {
        }

        $review->update($validated);

        return redirect()->route('stores.show', $review->store)->with('success', '口コミを更新しました。');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $this->authorize('delete', $review);

        $store = $review->store;
        $review->delete();

        return redirect()->route('stores.show', $store)->with('success', '口コミを削除しました。');
    }
}
