<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if (! $request->user()->hasVerifiedEmail() && $request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        $destination = $request->user()->isAdmin()
            ? route('admin.dashboard')
            : route('user.dashboard');

        return redirect()->intended($destination)->with('success', 'Email berhasil diverifikasi. Selamat datang di Z-Airlines.');
    }
}
