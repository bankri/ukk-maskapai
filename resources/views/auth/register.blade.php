@extends('layouts.app')

@section('title', 'Daftar')

@if($recaptchaEnabled)
    @push('head')
        <script src="https://www.google.com/recaptcha/api.js?hl=id" async defer></script>
    @endpush
@endif

@section('content')
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center bg-gradient-to-br from-slate-100 via-blue-50 to-sky-100 py-10 px-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-3xl shadow-xl border border-white p-6 sm:p-8">
            <div class="text-center mb-8">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-blue-600 text-white grid place-items-center shadow-lg shadow-blue-200 mb-4"><i class="fas fa-user-plus text-xl"></i></div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Buat Akun Penumpang</h2>
                <p class="text-slate-500 mt-2">Daftar, verifikasi email, lalu mulai booking penerbangan.</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name"
                        class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Nama sesuai identitas">
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email Aktif</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                        class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="nama@email.com">
                    <p class="text-xs text-slate-500 mt-1">Tautan verifikasi akan dikirim ke email ini.</p>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password"
                        class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Minimal 8 karakter">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">Konfirmasi Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                        class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Ulangi password">
                </div>

                @if($recaptchaEnabled)
                    <div class="overflow-x-auto">
                        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                    </div>
                    @error('captcha')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                    @error('g-recaptcha-response')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                @endif

                <button type="submit" class="w-full bg-blue-600 text-white py-3.5 rounded-xl font-bold hover:bg-blue-700 transition focus:ring-4 focus:ring-blue-200">
                    Daftar dan Kirim Verifikasi
                </button>
            </form>

            <div class="mt-6 text-center text-sm">
                <p class="text-slate-600">Sudah punya akun? <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline">Masuk di sini</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
