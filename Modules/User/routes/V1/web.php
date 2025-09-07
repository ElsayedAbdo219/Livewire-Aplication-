<?php

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Modules\User\Http\Controllers\UserController;

Route::middleware(['auth', 'verified'])->group(function () {
  Route::resource('users', UserController::class)->names('user');
});


Route::get('/auth/redirect/{provider}', function ($provider) {
  // dd('test only!');
  return Socialite::driver($provider)->redirect();
});

Route::get('/auth/callback/{provider}', function ($provider) {
  $providerUser = Socialite::driver($provider)->user();
  $email = $providerUser->getEmail();
  // Check if the user already exists in the database
  $user = User::updateOrCreate([
    'email' => $email,
  ], [
    'name' => $providerUser->name,
    'email' => $providerUser->email,
    'password' => Hash::make(Str::random(16)), // Generate a random password
  ]);

  Auth::login($user);

  return redirect('/dashboard');
});
