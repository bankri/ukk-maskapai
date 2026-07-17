<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\RecaptchaService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin(RecaptchaService $recaptcha)
    {
        return view('auth.login', ['recaptchaEnabled' => $recaptcha->enabled()]);
    }

    public function login(Request $request, RecaptchaService $recaptcha)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'g-recaptcha-response' => [
                Rule::requiredIf($recaptcha->enabled()),
                'nullable',
                'string',
            ],
        ]);

        $this->ensureCaptchaIsValid($request, $recaptcha);
        unset($credentials['g-recaptcha-response']);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = $request->user();

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            if (! $user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            return redirect()->route('user.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function showRegister(RecaptchaService $recaptcha)
    {
        return view('auth.register', ['recaptchaEnabled' => $recaptcha->enabled()]);
    }

    public function register(Request $request, RecaptchaService $recaptcha)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'g-recaptcha-response' => [
                Rule::requiredIf($recaptcha->enabled()),
                'nullable',
                'string',
            ],
        ]);

        $this->ensureCaptchaIsValid($request, $recaptcha);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
        ]);

        event(new Registered($user));
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('verification.notice');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function ensureCaptchaIsValid(Request $request, RecaptchaService $recaptcha): void
    {
        if (! $recaptcha->verify($request->input('g-recaptcha-response'), $request->ip())) {
            throw ValidationException::withMessages([
                'captcha' => 'Verifikasi captcha gagal atau kedaluwarsa. Silakan centang captcha dan coba lagi.',
            ]);
        }
    }
}
