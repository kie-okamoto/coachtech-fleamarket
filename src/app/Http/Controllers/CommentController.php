<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;

class CommentController extends Controller
{
    public function store(CommentRequest $request, $item_id)
    {
        if (!Auth::check()) {
            return redirect('/login'); // Fortifyで保護されたログイン画面へ
        }

        Comment::create([
            'item_id' => $item_id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        return redirect()->route('items.show', $item_id)->with('message', 'コメントを投稿しました。');
    }
}
