@extends('layouts.app')

@section('content')
<main class="max-w-3xl mx-auto px-6 py-20">
    <div class="mb-12">
        <a href="{{ route('home') }}" class="text-indigo-600 font-bold flex items-center gap-2 mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali ke Event
        </a>
        <h1 class="text-4xl font-extrabold">Checkout</h1>
        <p class="text-slate-500 mt-2">Lengkapi data Anda untuk mendapatkan tiket.</p>
    </div>

    <div class="grid grid-cols-1 gap-8">
        <!-- Summary Card -->
        <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
            <h3 class="text-xl font-bold mb-6 border-b pb-4">Pesanan Anda</h3>
                <div class="flex gap-6 items-start">
                <img src="{{ $event->poster_url }}" alt="{{ $event->title }}" class="w-28 h-28 rounded-2xl object-cover">
                <div>

                    <h4 class="font-extrabold text-lg">{{ $event->title }}</h4>
                    <p class="text-slate-500">{{ $event->date ? $event->date->format('d M Y H:i') : 'Tanggal belum diatur' }} • {{ $event->location }}</p>
                    <p class="text-indigo-600 font-bold mt-2" id="ticketSummaryLine">1 x Rp {{ number_format($event->price, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="mt-8 pt-6 border-t space-y-3">
                <div class="flex justify-between text-slate-500">
                    <span>Harga Tiket</span>
                    <span>Rp {{ number_format($event->price, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-slate-500">
                    <span>Biaya Layanan</span>
                    <span>Rp 0</span>
                </div>
                <div class="flex justify-between text-2xl font-black mt-4 pt-4 border-t">
                    <span>Total Bayar</span>
                    <span class="text-indigo-600" id="totalPriceText">Rp {{ number_format($event->price, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
            <h3 class="text-xl font-bold mb-6 italic text-indigo-600 underline underline-offset-8">📦 Data Pemesan
                (Tanpa Login)</h3>
            <form id="checkoutForm" class="space-y-6">
                @csrf
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Nama
                        Lengkap</label>
                    <input type="text" name="customer_name" placeholder="Masukkan nama sesuai identitas"
                        class="w-full px-5 py-4 bg-white border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium"
                        required>
                    <span class="text-red-500 text-sm error-message" id="error-customer_name"></span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Email
                            Aktif</label>
                        <input type="email" name="customer_email" placeholder="contoh@gmail.com"
                            class="w-full px-5 py-4 bg-white border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium"
                            required>
                        <p class="text-[10px] text-slate-400 mt-2 font-bold uppercase tracking-tighter">*E-Ticket
                            akan dikirim ke email ini</p>
                        <span class="text-red-500 text-sm error-message" id="error-customer_email"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">No.
                            WhatsApp</label>
                        <input type="tel" name="customer_phone" placeholder="08xxxxxxx"
                            class="w-full px-5 py-4 bg-white border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium"
                            required>
                        <span class="text-red-500 text-sm error-message" id="error-customer_phone"></span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Jumlah Tiket</label>
                    <input type="number" name="quantity" value="1" min="1" 
                        class="w-full px-5 py-4 bg-white border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium"
                        required>
                    <span class="text-red-500 text-sm error-message" id="error-quantity"></span>
                </div>

                <div class="bg-indigo-50 border-l-4 border-indigo-600/80 p-4 rounded">
                    <p class="text-sm font-bold text-indigo-900 mb-2">Metode pembayaran</p>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="radio" name="payment_method" value="qris" checked class="w-4 h-4 text-indigo-600">
                        <span class="text-sm font-medium text-indigo-800">QRIS</span>
                    </label>
                </div>

                @if(config('midtrans.is_demo'))
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                    <p class="text-sm font-bold text-blue-900 mb-3">Pilih status pembayaran (demo):</p>

                    <div class="space-y-2">

                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="payment_status" value="success" checked class="w-4 h-4 text-green-600">
                            <span class="text-sm font-medium text-green-700">✅ Pembayaran Berhasil (Settlement)</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="payment_status" value="pending" class="w-4 h-4 text-yellow-600">
                            <span class="text-sm font-medium text-yellow-700">⏳ Pembayaran Tertunda (Pending)</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="payment_status" value="failed" class="w-4 h-4 text-red-600">
                            <span class="text-sm font-medium text-red-700">❌ Pembayaran Gagal (Failed)</span>
                        </label>
                    </div>
                </div>
                @endif

                <button type="button" id="payButton"
                    class="block text-center w-full py-5 bg-indigo-600 text-white rounded-2xl font-black text-xl shadow-xl shadow-indigo-200 hover:bg-indigo-700 active:scale-95 transition-all">
                    Bayar Sekarang
                </button>
                <p class="text-center text-xs text-slate-400">Dengan menekan tombol di atas, Anda menyetujui Syarat
                    & Ketentuan kami.</p>
            </form>
        </div>

    </div>
</main>

<!-- Include Midtrans Snap SDK -->
@if(!config('midtrans.is_demo'))
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
@endif

<script>
// Live update total bayar berdasarkan jumlah tiket
(function () {
    const quantityInput = document.querySelector('input[name="quantity"]');
    const eventPrice = {{ (int)$event->price }};

    const totalPriceEl = document.getElementById('totalPriceText');
    const ticketSummaryEl = document.getElementById('ticketSummaryLine');

    const formatIDR = (value) => {
        try {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
        } catch (e) {
            return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
    };

    const updateSummary = () => {
        const qty = Math.max(1, parseInt(quantityInput?.value || '1', 10));
        const total = eventPrice * qty;

        if (totalPriceEl) totalPriceEl.textContent = formatIDR(total);
        if (ticketSummaryEl) ticketSummaryEl.textContent = qty + ' x Rp ' + Number(eventPrice).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        // Total Bayar sudah menggunakan formatter IDR
        // (tetap di-override saat qty berubah)

    };

    if (quantityInput) {
        quantityInput.addEventListener('input', updateSummary);
        updateSummary();
    }
})();


document.getElementById('payButton').addEventListener('click', async function(e) {
    e.preventDefault();
    
    const form = document.getElementById('checkoutForm');
    const formData = new FormData(form);
    
    // Clear previous errors
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    
    try {
        // Process checkout
            const payload = {
            customer_name: formData.get('customer_name'),
            customer_email: formData.get('customer_email'),
            customer_phone: formData.get('customer_phone'),
            quantity: parseInt(formData.get('quantity')),
            payment_method: formData.get('payment_method') || 'qris'
        };

        // Add payment status for demo mode
        @if(config('midtrans.is_demo'))
        payload.payment_status = document.querySelector('input[name="payment_status"]:checked')?.value || 'success';
        @endif

        const response = await fetch('{{ route("checkout.process", $event) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': formData.get('_token'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (!response.ok) {
            // Show validation errors
            if (data.errors) {
                Object.keys(data.errors).forEach(key => {
                    const errorElement = document.getElementById('error-' + key);
                    if (errorElement) {
                        errorElement.textContent = data.errors[key][0];
                    }
                });
            }
            throw new Error(data.message || 'Checkout failed');
        }

        // Demo mode: direct redirect to status page
        @if(config('midtrans.is_demo'))
        setTimeout(() => {
            window.location.href = '{{ route("payment.status", ":id") }}'.replace(':id', data.transaction_id);
        }, 800);
        @else
        // Production: Open Midtrans Snap popup
        snap.pay(data.snap_token, {
            onSuccess: function(result) {
                window.location.href = '{{ route("payment.status", ":id") }}'.replace(':id', result.order_id);
            },
            onPending: function(result) {
                console.log('Pending payment:', result);
            },
            onError: function(result) {
                alert('Payment failed. Please try again.');
                console.log('Payment error:', result);
            },
            onClose: function() {
                console.log('Payment popup closed');
            }
        });
        @endif
    } catch (error) {
        console.error('Error:', error);
        alert('Error: ' + error.message);
    }
});
</script>
@endsection