@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-white py-12 px-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <i class="fas fa-plane text-blue-600 text-4xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900">Selamat Datang Kembali</h2>
                <p class="text-gray-500 mt-2">Masuk ke akun Z-Airlines Anda</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="nama@email.com">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600">
                        <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Masuk
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600">Belum punya akun? <a href="{{ route('register') }}" class="text-blue-600 font-semibold hover:underline">Daftar sekarang</a></p>
            </div>
        </div>
    </div>
</div>
@endsection