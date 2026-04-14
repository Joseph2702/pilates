@extends('layouts.app')
@section('title', 'Checkout - Femm Pilates')

@section('content')

{{-- Page Header --}}
<section class="border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-6 py-8">
        <h1 class="text-2xl md:text-3xl font-black text-gray-900">Finalize Your Vitality</h1>
        <p class="text-gray-400 text-sm mt-1">Complete your purchase to secure your place in our precision studio.</p>
    </div>
</section>

<section class="max-w-7xl mx-auto px-6 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Left: Promo Code --}}
        <div class="lg:col-span-2">
            <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-4">Promotions</p>
            <div class="flex gap-3">
                <input type="text" id="kode_promo" placeholder="Enter a promo code"
                    class="flex-1 border border-gray-300 px-4 py-3 text-sm focus:ring-2 focus:ring-purple-300 focus:border-purple-400 outline-none transition">
                <button type="button" onclick="applyPromo()"
                    class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-3 text-sm font-semibold tracking-widest uppercase transition">
                    Apply
                </button>
            </div>
            <div id="promo-message" class="mt-2 text-xs hidden"></div>

            <div class="mt-8 flex items-center gap-2 text-gray-400">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                <span class="text-xs">Payment secured by <span class="font-bold text-gray-600">MIDTRANS</span>. You will be redirected to complete payment.</span>
            </div>
        </div>

        {{-- Right: Order Summary --}}
        <div class="lg:col-span-1">
            <div class="border border-gray-200 overflow-hidden sticky top-20">
                {{-- Package Image --}}
                <div class="relative h-32 bg-gray-800 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=600&q=80" alt="{{ $package->nama_package }}" class="w-full h-full object-cover opacity-60">
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-4">
                        <p class="text-white font-black text-sm uppercase tracking-tight">{{ $package->nama_package }}</p>
                        <p class="text-purple-300 text-xs mt-0.5">One Time Purchase</p>
                    </div>
                </div>

                <div class="p-5 space-y-3 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Credits</span>
                        <span class="font-semibold text-gray-900">{{ $package->jumlah_kredit }} sessions</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Valid for</span>
                        <span class="font-semibold text-gray-900">{{ $package->masa_berlaku }} days</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Class types</span>
                        <span class="font-semibold text-gray-900">All types</span>
                    </div>
                    <div class="border-t border-gray-100 pt-3">
                        <div class="flex justify-between text-gray-600 text-xs">
                            <span>Subtotal</span>
                            <span id="subtotal">Rp{{ number_format($package->harga, 0, ',', '.') }}</span>
                        </div>
                        <div id="diskon-row" class="flex justify-between text-green-600 text-xs mt-1 hidden">
                            <span>Discount</span>
                            <span id="diskon-amount">-Rp0</span>
                        </div>
                        <div class="flex justify-between font-black text-gray-900 text-xl mt-3">
                            <span>Total</span>
                            <span id="total-harga">Rp{{ number_format($package->harga, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('packages.process', $package->id_package) }}">
                        @csrf
                        <input type="hidden" name="id_package" value="{{ $package->id_package }}">
                        <input type="hidden" name="kode_promo" id="hidden_promo">
                        <button type="submit"
                            class="w-full mt-3 flex items-center justify-center gap-2 bg-purple-500 hover:bg-purple-600 text-white py-3 text-sm font-semibold tracking-widest uppercase transition">
                            Proceed to Payment
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </button>
                    </form>
                    <p class="text-xs text-gray-400 text-center leading-relaxed mt-2">
                        By completing your purchase you agree to our Terms of Service.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
const basePrice = {{ $package->harga }};

async function applyPromo() {
    const code = document.getElementById('kode_promo').value.trim();
    const msg = document.getElementById('promo-message');
    if (!code) return;

    try {
        const res = await fetch('/promo/check', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content},
            body: JSON.stringify({kode_promo: code})
        });
        const data = await res.json();
        msg.classList.remove('hidden');
        if (data.success && data.promo) {
            const diskon = basePrice * (data.promo.persenan_diskon / 100);
            const total = basePrice - diskon;
            document.getElementById('diskon-row').classList.remove('hidden');
            document.getElementById('diskon-amount').textContent = '-Rp' + diskon.toLocaleString('id-ID');
            document.getElementById('total-harga').textContent = 'Rp' + total.toLocaleString('id-ID');
            document.getElementById('hidden_promo').value = code;
            msg.className = 'mt-2 text-xs text-green-600';
            msg.textContent = '✓ Promo applied: ' + data.promo.persenan_diskon + '% discount';
        } else {
            msg.className = 'mt-2 text-xs text-red-500';
            msg.textContent = data.message ?? 'Kode promo tidak valid.';
            document.getElementById('hidden_promo').value = '';
            document.getElementById('total-harga').textContent = 'Rp' + basePrice.toLocaleString('id-ID');
            document.getElementById('diskon-row').classList.add('hidden');
        }
    } catch(e) {
        msg.className = 'mt-2 text-xs text-red-500';
        msg.textContent = 'Gagal memeriksa promo.';
    }
}
</script>
@endsection
