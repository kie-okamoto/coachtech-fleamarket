<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function store(Item $item)
    {
        $user = Auth::user();

        if (!$user->favorites()->where('item_id', $item->id)->exists()) {
            $user->favorites()->attach($item->id);
        }

        return response()->json([
            'status' => 'liked',
            'count' => $item->favoritedUsers()->count(),
        ]);
    }

    public function destroy(Item $item)
    {
        $user = Auth::user();

        if ($user->favorites()->where('item_id', $item->id)->exists()) {
            $user->favorites()->detach($item->id);
        }

        return response()->json([
            'status' => 'unliked',
            'count' => $item->favoritedUsers()->count(),
        ]);
    }
}
