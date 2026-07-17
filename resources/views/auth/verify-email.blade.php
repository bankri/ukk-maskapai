@extends('layouts.app')

@section('title', 'Verifikasi Email')

@section('content')
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center px-4 py-12 bg-gradient-to-br from-slate-100 via-blue-50 to-sky-100">
    <div class="max-w-lg w-full bg-white rounded-3xl shadow-xl p-6 sm:p-9 text-center">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-blue-100 text-blue-600 grid place-items-center mb-5"><i class="fas fa-envelope-circle-check text-2xl"></i></div>
        <p class="text-blue-600 font-bold text-sm mb-2">SATU LANGKAH LAGI</p>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Verifikasi Email Anda</h1>
        <p class="text-slate-600 mt-4 leading-relaxed">
            Kami telah mengirim tautan verifikasi ke <strong>{{ auth()->user()->email }}</strong>. Buka email tersebut dan klik tombol verifikasi sebelum mengakses booking dan pembayaran.
        </p>

        @if(session('status') === 'verification-link-sent')
            <div class="mt-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">Tautan verifikasi baru berhasil dikirim.</div>
        @endif

        <div class="mt-7 grid grid-cols-1 sm:grid-cols-2 gap-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700"><i class="fas fa-paper-plane mr-2"></i>Kirim Ulang</button>
            </form>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full border border-slate-300 text-slate-700 py-3 rounded-xl font-bold hover:bg-slate-50">Keluar</button>
            </form>
        </div>

        <p class="text-xs text-slate-500 mt-5">Periksa folder Spam atau Promosi jika email belum terlihat.</p>
    </div>
</div>
@endsection
