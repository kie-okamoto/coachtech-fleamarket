<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProfileRequest;


class UserController extends Controller
{
    /**
     * マイページ（プロフィール表示）
     */
    public function profile(Request $request)
    {
        $page = $request->query('page');

        if ($page === 'buy') {
            $items = Auth::user()->purchasedItems()->with('order')->latest()->get();
        } else {
            $items = Auth::user()->items()->with('order')->latest()->get();
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
    public function updateProfile(ProfileRequest $request)
    {
        $user = Auth::user();

        $validated = $request->validated();

        // ユーザー名更新
        $user->name = $validated['name'];

        // プロフィール画像の処理
        if ($request->hasFile('profile_image')) {
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            // 新しい画像を保存
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        // ユーザー情報を保存
        $user->save();

        // 住所情報を更新または作成
        $user->address()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'postal_code' => $validated['postal_code'],
                'address'     => $validated['address'],
                'building'    => $validated['building'],
            ]
        );

        return redirect()->route('index')->with('success', 'プロフィールを更新しました');
    }
}
