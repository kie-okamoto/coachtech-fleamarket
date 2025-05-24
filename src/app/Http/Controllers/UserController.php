<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function profile(Request $request)
    {
        return view('users.profile');
    }

    public function editProfile()
    {
        return view('users.edit_profile');
    }

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

        // プロフィール画像の保存
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        // ユーザー名のみ更新（住所は別テーブル）
        $user->name = $validated['name'];
        $user->save();

        // addresses テーブルを update または create
        $user->address()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'postal_code' => $validated['postal_code'] ?? '',
                'address'     => $validated['address'] ?? '',
                'building'    => $validated['building'] ?? '',
            ]
        );

        return redirect()->route('index')->with('success', 'プロフィールを更新しました');
    }
}
