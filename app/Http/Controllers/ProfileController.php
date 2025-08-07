<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /** プロフィール表示 */
    public function show()
    {
        $user  = Auth::user();
        return view('profile.test', compact('user'));
    }

    /** プロフィール更新 */
    public function update(Request $request)
    {
        $user = Auth::user();

        // ---------- バリデーション ----------
        $validated = $request->validate([
            'name'   => 'required|max:255',
            'email'  => 'required|email|unique:users,email,' . $user->id,
            'bio'    => 'nullable|max:500',
            'avatar' => 'nullable|image|max:2048', // 2MB
        ]);

        // ---------- アバター画像の保存 ----------
        if ($request->hasFile('avatar')) {
            // 古いアバターがあれば削除
            if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
                Storage::disk('public')->delete($user->avatar_url);
            }

            $path = $request->file('avatar')
                            ->store('images', 'public');
            $validated['avatar_url'] = $path;
        }

        // ---------- ユーザー更新 ----------
        $user->update($validated);

        return back()->with('success', 'プロフィールを更新しました。');
    }
}