<?php 
// app/Http/Controllers/Auth/LoginController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class LoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
            $this->loginOrRegister($user, 'google');
            return redirect('/');
        } catch (\Exception $e) {
            return redirect('/login');
        }
    }

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $user = Socialite::driver('facebook')->user();
            $this->loginOrRegister($user, 'facebook');
            return redirect('/');
        } catch (\Exception $e) {
            return redirect('/login');
        }
    }

    protected function loginOrRegister($socialUser, $provider)
    {
        $user = User::where('provider_id', $socialUser->getId())->first();
        if ($user) {
            Auth::login($user);
        } else {
            $user = User::create([
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
            ]);
            Auth::login($user);
        }
    }
}
