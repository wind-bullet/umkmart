@extends('layouts.app')

@section('title', 'Checkout - UMKMART')

@section('content')
<h1 class="text-2xl font-extrabold text-slate-800 dark:text-white mb-6 flex items-center gap-2 text-left">
    <span class="material-icons text-emerald-600 dark:text-emerald-400">payments</span>
    Konfirmasi Pemesanan & Pembayaran
</h1>

<form action="{{ route('checkout.place') }}" method="POST" id="checkout-form">
    @csrf
    
    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- Form Left: Options Selection -->
        <div class="flex-grow flex flex-col gap-6">
            
            <!-- Items summary -->
            <div class="bg-white dark:bg-slate-950 border border-slate-100 dark:border-slate-850 p-6 rounded-3xl text-left shadow-sm">
                <h3 class="font-bold text-slate-800 dark:text-white text-sm mb-4 pb-2 border-b border-slate-100 dark:border-slate-850">1. Ringkasan Pembelian</h3>
                <div class="flex flex-col gap-3">
                    @foreach($cartItems as $item)
                        <div class="flex justify-between items-center text-xs">
                            <div class="min-w-0 pr-4">
                                <p class="font-bold text-slate-800 dark:text-white truncate">{{ $item->product->name }}</p>
                                <p class="text-slate-400 mt-0.5">{{ $item->qty }}x @ Rp {{ number_format($item->product->price, 0, ',', '.') }}</p>
                            </div>
                            <span class="font-bold text-slate-700 dark:text-slate-350">Rp {{ number_format($item->qty * $item->product->price, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Shipping Methods -->
            <div class="bg-white dark:bg-slate-950 border border-slate-100 dark:border-slate-850 p-6 rounded-3xl text-left shadow-sm">
                <h3 class="font-bold text-slate-800 dark:text-white text-sm mb-4 pb-2 border-b border-slate-100 dark:border-slate-850">2. Metode Pengiriman</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($shippingOptions as $index => $opt)
                        <label class="relative flex flex-col p-4 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-emerald-500 dark:hover:border-emerald-500 cursor-pointer transition-colors bg-slate-50/50 dark:bg-slate-900/40">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="shipping_option_id" value="{{ $opt->id }}" data-fee="{{ $opt->fee_value }}" data-name="{{ $opt->name }}" {{ $index === 0 ? 'checked' : '' }} onchange="updateShippingCost(this)" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-slate-300">
                                <div>
                                    <p class="text-xs font-bold text-slate-850 dark:text-white leading-none">{{ $opt->name }}</p>
                                    <p class="text-[10px] text-slate-400 mt-1">Rp {{ number_format($opt->fee_value, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Identitas & Alamat Pengiriman -->
            <div id="shipping-address-section" class="hidden bg-white dark:bg-slate-950 border border-slate-100 dark:border-slate-850 p-6 rounded-3xl text-left shadow-sm">
                <h3 class="font-bold text-slate-800 dark:text-white text-sm mb-4 pb-2 border-b border-slate-100 dark:border-slate-850">3. Identitas & Alamat Pengiriman</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <!-- Nama Penerima -->
                    <div>
                        <label for="recipient_name" class="block text-xs font-bold text-slate-500 uppercase mb-2">Nama Penerima</label>
                        <input type="text" name="recipient_name" id="recipient_name" value="{{ Auth::user()->name }}" placeholder="Nama Lengkap Penerima" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl py-2.5 px-4 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 text-slate-800 dark:text-white">
                    </div>
                    
                    <!-- Nomor Telepon Penerima -->
                    <div>
                        <label for="recipient_phone" class="block text-xs font-bold text-slate-500 uppercase mb-2">Nomor Telepon Penerima</label>
                        <input type="text" name="recipient_phone" id="recipient_phone" value="{{ Auth::user()->phone_number }}" placeholder="Nomor Telepon Penerima" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl py-2.5 px-4 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 text-slate-800 dark:text-white">
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    <label for="shipping_address" class="block text-xs font-bold text-slate-500 uppercase">Alamat Lengkap Pengiriman</label>
                    <textarea name="shipping_address" id="shipping_address" rows="3" placeholder="Masukkan alamat lengkap pengiriman (Jalan, RT/RW, Kelurahan, Kecamatan, Kota, Kode Pos)..." class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl py-2.5 px-4 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 text-slate-800 dark:text-white">{{ Auth::user()->address }}</textarea>
                    <p class="text-[10px] text-slate-400">Pengiriman barang memerlukan informasi identitas dan alamat tujuan yang lengkap.</p>
                </div>
            </div>
            
            <!-- Payment Methods -->
            <div class="bg-white dark:bg-slate-950 border border-slate-100 dark:border-slate-850 p-6 rounded-3xl text-left shadow-sm">
                <h3 class="font-bold text-slate-800 dark:text-white text-sm mb-4 pb-2 border-b border-slate-100 dark:border-slate-850">4. Metode Pembayaran</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @foreach($paymentMethods as $index => $pay)
                        <label class="relative flex flex-col p-4 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-emerald-500 dark:hover:border-emerald-500 cursor-pointer transition-colors bg-slate-50/50 dark:bg-slate-900/40">
                            <div class="flex items-center gap-2">
                                <input type="radio" name="payment_method_id" value="{{ $pay->id }}" data-name="{{ $pay->name }}" {{ $index === 0 ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-slate-300">
                                <span class="text-xs font-bold text-slate-850 dark:text-white leading-none" id="payment-label-{{ $pay->id }}">{{ $pay->name }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Summary Sidebar (Right) -->
        <div class="w-full lg:w-80 flex-shrink-0">
            <div class="bg-white dark:bg-slate-950 border border-slate-100 dark:border-slate-850 rounded-3xl p-6 shadow-sm sticky top-24 text-left">
                <h3 class="font-bold text-slate-800 dark:text-white text-sm mb-4 pb-3 border-b border-slate-100 dark:border-slate-850">Ringkasan Pembayaran</h3>
                
                <div class="flex flex-col gap-3 mb-6">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-500">Subtotal Belanja</span>
                        <span class="font-bold text-slate-700 dark:text-slate-350">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-500">Ongkos Kirim</span>
                        <span class="font-bold text-slate-700 dark:text-slate-350">Rp <span id="summary-shipping">0</span></span>
                    </div>
                    <hr class="border-slate-100 dark:border-slate-850 my-1">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-bold text-slate-800 dark:text-white">Total Tagihan</span>
                        <span class="text-lg font-black text-emerald-600 dark:text-emerald-400">Rp <span id="summary-total">{{ number_format($subtotal, 0, ',', '.') }}</span></span>
                    </div>
                </div>
                
                <!-- Disclaimer for Prototype -->
                <div class="mb-6 p-3 rounded-2xl bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900 text-[10px] text-amber-800 dark:text-amber-400 flex items-start gap-2">
                    <span class="material-icons text-sm mt-0.5">warning</span>
                    <div>
                        <p class="font-bold">Simulasi Checkout</p>
                        <p class="mt-0.5">Ini adalah sistem simulasi untuk keperluan demo prototipe. Tidak ada transaksi uang asli yang terjadi.</p>
                    </div>
                </div>
                
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 px-4 rounded-xl text-center text-xs transition-colors shadow-lg shadow-emerald-600/15 flex items-center justify-center gap-1.5">
                    <span class="material-icons text-sm">payment</span>
                    <span>Bayar Sekarang (Simulasi)</span>
                </button>
            </div>
        </div>
    </div>
</form>

<!-- Checkout Success Modal -->
<div id="checkout-success-modal" class="fixed inset-0 z-[100] flex items-center justify-center invisible opacity-0 transition-all duration-300" style="display: none;">
    <!-- Overlay -->
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
    <!-- Card -->
    <div class="relative bg-white dark:bg-slate-900 rounded-3xl p-8 max-w-sm w-full mx-4 text-center shadow-2xl border border-slate-100 dark:border-slate-800 transform scale-95 transition-all duration-300" id="success-modal-card">
        <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/50 rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce">
            <span class="material-icons text-emerald-600 dark:text-emerald-400 text-3xl">check_circle</span>
        </div>
        <h2 class="text-xl font-extrabold text-slate-800 dark:text-white mb-2">Pesanan Berhasil!</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
            Pemesanan Anda telah tercatat. Rincian pesanan telah dikirimkan ke chat admin.
        </p>
        <div class="flex flex-col gap-3">
            <a id="modal-btn-orders" href="/orders" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl text-sm transition-all shadow-md shadow-emerald-600/10">
                Lihat Pesanan
            </a>
            <a href="/user/chat" class="w-full border border-emerald-600/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/20 font-bold py-3 px-4 rounded-xl text-sm transition-all">
                Chat Admin
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const subtotal = {{ $subtotal }};

    function updateShippingCost(input) {
        if (!input) return;
        const fee = parseFloat(input.getAttribute('data-fee')) || 0;
        const name = input.getAttribute('data-name') || '';
        
        document.getElementById('summary-shipping').textContent = fee.toLocaleString('id-ID');
        document.getElementById('summary-total').textContent = (subtotal + fee).toLocaleString('id-ID');

        // Toggle address input based on delivery option
        const addressSection = document.getElementById('shipping-address-section');
        const addressTextarea = document.getElementById('shipping_address');
        const nameInput = document.getElementById('recipient_name');
        const phoneInput = document.getElementById('recipient_phone');
        if (addressSection && addressTextarea && nameInput && phoneInput) {
            if (name.toLowerCase().includes('ambil')) {
                addressSection.classList.add('hidden');
                addressTextarea.removeAttribute('required');
                nameInput.removeAttribute('required');
                phoneInput.removeAttribute('required');
            } else {
                addressSection.classList.remove('hidden');
                addressTextarea.setAttribute('required', 'required');
                nameInput.setAttribute('required', 'required');
                phoneInput.setAttribute('required', 'required');
            }
        }

        // Dynamic payment label: "Bayar di Toko" ↔ "COD (Cash on Delivery)"
        document.querySelectorAll('input[name="payment_method_id"]').forEach(function(payInput) {
            const originalName = payInput.getAttribute('data-name');
            const labelEl = document.getElementById('payment-label-' + payInput.value);
            if (originalName === 'Bayar di Toko' && labelEl) {
                if (name.toLowerCase().includes('ambil')) {
                    labelEl.textContent = 'Bayar di Toko';
                } else {
                    labelEl.textContent = 'COD (Cash on Delivery)';
                }
            }
        });
    }

    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed top-24 left-1/2 transform -translate-x-1/2 bg-slate-900/90 text-white text-xs font-bold py-3 px-6 rounded-full shadow-2xl z-[200] transition-all duration-300 opacity-0 flex items-center gap-2 backdrop-blur-sm';
        toast.innerHTML = `<span class="material-icons text-sm text-emerald-400">check_circle</span> <span>${message}</span>`;
        document.body.appendChild(toast);
        setTimeout(() => { toast.classList.remove('opacity-0'); toast.classList.add('opacity-100'); }, 10);
        setTimeout(() => {
            toast.classList.remove('opacity-100');
            toast.classList.add('opacity-0');
            setTimeout(() => { toast.remove(); }, 300);
        }, 4000);
    }

    function showCheckoutModal(orderCode) {
        document.getElementById('modal-btn-orders').href = '/order/' + orderCode;
        const modal = document.getElementById('checkout-success-modal');
        const card = document.getElementById('success-modal-card');
        modal.style.display = 'flex';
        // Force reflow
        modal.offsetHeight;
        modal.classList.remove('invisible', 'opacity-0');
        modal.classList.add('opacity-100');
        setTimeout(() => {
            card.classList.remove('scale-95');
            card.classList.add('scale-100');
        }, 50);
    }

    // Intercept form submit with AJAX
    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showToast("Info pesanan sudah terkirim ke akun admin.");
                showCheckoutModal(data.order_code);
            } else {
                alert(data.message || 'Terjadi kesalahan. Silakan coba lagi.');
                if (submitBtn) submitBtn.disabled = false;
            }
        })
        .catch(err => {
            alert('Terjadi kesalahan koneksi atau server. Silakan coba lagi.');
            if (submitBtn) submitBtn.disabled = false;
        });
    });

    // Trigger initial load cost calculation
    document.addEventListener('DOMContentLoaded', () => {
        const checkedInput = document.querySelector('input[name="shipping_option_id"]:checked');
        if (checkedInput) {
            updateShippingCost(checkedInput);
        }
    });
</script>
@endsection
