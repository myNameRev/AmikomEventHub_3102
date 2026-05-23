@extends('layouts.admin')

@section('content')
<main class="flex-1 p-10 overflow-y-auto">
    <header class="flex justify-between items-center mb-10">
        <div>
            <h1 class="text-3xl font-black">Tambah Kategori Baru</h1>
            <p class="text-slate-500 font-medium">Tambahkan kategori event baru ke dalam sistem.</p>
        </div>
        <a href="{{ route('admin.categories.index') }}"
            class="px-6 py-3 bg-slate-200 text-slate-700 rounded-2xl font-bold hover:bg-slate-300 active:scale-95 transition">
            ← Kembali
        </a>
    </header>

    <div class="max-w-2xl bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-10">
        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Input Nama Kategori -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-3">
                    Nama Kategori <span class="text-red-600">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       value="{{ old('name') }}"
                       class="w-full px-5 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition"
                       placeholder="Masukkan nama kategori (contoh: Musik, Workshop, Olahraga)"
                       required>
                @error('name')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit & Cancel Buttons -->
            <div class="flex gap-4 pt-6 border-t">
                <button type="submit" 
                        class="flex-1 px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 active:scale-95 transition">
                    Simpan Kategori
                </button>
                <a href="{{ route('admin.categories.index') }}"
                   class="flex-1 px-6 py-3 bg-slate-100 text-slate-700 rounded-xl font-bold hover:bg-slate-200 active:scale-95 transition text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</main>
@endsection
