@extends('layouts.app')

@section('title', 'Masuk')

@if($recaptchaEnabled)
    @push('head')
        <script src="https://www.google.com/recaptcha/api.js?hl=id" async defer></script>
    @endpush
@endif

@section('content')
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center bg-gradient-to-br from-blue-50 to-white py-10 px-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8">
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4"><i class="fas fa-plane text-blue-600 text-4xl"></i></div>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Selamat Datang Kembali</h2>
                <p class="text-gray-500 mt-2">Masuk ke akun Z-Airlines Anda</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="nama@email.com">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="••••••••">
                </div>

                <label class="flex items-center gap-2">
                    <input type="checkbox" name="remember" value="1" class="rounded border-gray-300 text-blue-600" {{ old('remember') ? 'checked' : '' }}>
                    <span class="text-sm text-gray-600">Ingat saya</span>
                </label>

                @if($recaptchaEnabled)
                    <div class="overflow-x-auto">
                        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                    </div>
                    @error('captcha')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                    @error('g-recaptcha-response')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                @endif

                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition focus:ring-4 focus:ring-blue-200">
                    Masuk
                </button>
            </form>

            <div class="mt-6 text-center text-sm">
                <p class="text-gray-600">Belum punya akun? <a href="{{ route('register') }}" class="text-blue-600 font-semibold hover:underline">Daftar sekarang</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
