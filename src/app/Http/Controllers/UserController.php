<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * プロフィール画面の表示
     */
    public function mypage(Request $request)
    {
        return view('users.profile');
    }

    /**
     * プロフィール編集フォーム表示（省略してmypage使ってもOK）
     */
    public function editProfile()
    {
        return view('users.profile');
    }

    /**
     * プロフィール更新処理
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        // 画像がアップロードされた場合
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        $user->name = $validated['name'];
        $user->postal_code = $validated['postal_code'] ?? null;
        $user->address = $validated['address'] ?? null;
        $user->building = $validated['building'] ?? null;
        $user->save();

        return redirect()->back()->with('success', 'プロフィールを更新しました');
    }
}
