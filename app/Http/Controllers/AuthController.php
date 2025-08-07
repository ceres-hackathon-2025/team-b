<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

/**
 * 認証コントローラー
 *
 * ユーザーのログイン、登録、ログアウト処理を管理する
 *
 */
class AuthController extends Controller
{
    /**
     * ログインフォーム表示
     *
     * @return View
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }


    /**
     * ログイン処理
     *
     * @param Request $request リクエストオブジェクト
     *
     * @return RedirectResponse
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['email' => 'メールアドレスまたはパスワードが正しくありません。']);
    }

    /**
     * ユーザー登録処理
     *
     * @return View
     */
    public function showSignupForm(): View
    {
        return view('auth.signup');
    }

    /**
     * ユーザー登録処理
     *
     * @param Request $request リクエストオブジェクト
     *
     * @return RedirectResponse
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate(
            [
                'name' => 'required|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6|confirmed',
            ]
        );

        User::create(
            [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]
        );

        return redirect('/login')->with('success', '登録が完了しました。ログインしてください。');
    }

    /**
     * ログアウト処理
     *
     * @param Request $request リクエストオブジェクト
     *
     * @return RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
