<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    /**
     * マイページ（プロフィール表示）
     */
    public function profile(Request $request)
    {
        $page = $request->query('page');

        if ($page === 'buy') {
            $items = Auth::user()->purchasedItems()->latest()->get();
        } else {
            $items = Auth::user()->items()->latest()->get();
        }

        return view('users.profile', compact('items'));
    }

    /**
     * プロフィール編集画面の表示
     */
    public function editProfile()
    {
        $user = Auth::user();
        $address = $user->address;

        return view('users.edit_profile', compact('user', 'address'));
    }

    /**
     * プロフィール更新処理
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // バリデーション
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        // ユーザー情報更新（名前は先に代入）
        $user->name = $validated['name'];

        // プロフィール画像の処理
        if ($request->hasFile('profile_image')) {
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        // ✅ 画像の有無にかかわらず必ずここで保存
        $user->save();

        // 住所情報更新
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
