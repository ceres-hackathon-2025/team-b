<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
     * @return \Illuminate\Contracts\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }


    /**
     * ログイン処理
     *
     * @param Request $request リクエストオブジェクト
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
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
     * @return \Illuminate\Contracts\View\View
     */
    public function showSignupForm()
    {
        return view('auth.signup');
    }

    /**
     * ユーザー登録処理
     *
     * @param Request $request リクエストオブジェクト
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
