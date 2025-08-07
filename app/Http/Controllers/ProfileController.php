<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show(User $user)
    {
        $myId  = Auth::id();
        $posts = \App\Models\Post::where('user_id', $myId)->latest()->get();
        return view('profile.test', compact('user', 'posts'));
    }
}
