@extends('layouts.app')
@section('title', 'Payment - Femm Pilates')

@section('content')
<section class="max-w-2xl mx-auto px-6 py-20 text-center">
    <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-4">Secure Payment</p>
    <h1 class="text-3xl font-black text-gray-900 mb-2">Complete Your Payment</h1>
    <p class="text-gray-500 text-sm mb-8">You will be redirected to Midtrans secure payment portal.</p>

    <div class="border border-gray-200 p-6 mb-6 text-left">
        <div class="flex justify-between text-sm text-gray-600 mb-2">
            <span>Package</span>
            <span class="font-medium text-gray-900">{{ $package->nama_package }}</span>
        </div>
        <div class="flex justify-between text-sm text-gray-600 mb-2">
            <span>Order ID</span>
            <span class="font-mono text-xs text-gray-700">{{ $orderId }}</span>
        </div>
        <div class="flex justify-between font-bold text-gray-900 text-base pt-3 border-t border-gray-100">
            <span>Total</span>
            <span>Rp{{ number_format($package->harga, 0, ',', '.') }}</span>
        </div>
    </div>

    <button id="pay-btn" onclick="payNow()"
        class="w-full bg-gray-900 hover:bg-gray-800 text-white py-3.5 text-sm font-semibold tracking-wide transition flex items-center justify-center gap-2">
        PAY NOW
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </button>
    <p class="text-xs text-gray-400 mt-3">
        Secured by <span class="font-bold text-gray-700 tracking-widest">MIDTRANS</span>
    </p>
</section>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
function payNow() {
    const btn = document.getElementById('pay-btn');
    btn.disabled = true;
    btn.textContent = 'Loading...';

    snap.pay('{{ $snapToken }}', {
        onSuccess: function(result) {
            window.location.href = '{{ route("profile.transactions") }}';
        },
        onPending: function(result) {
            window.location.href = '{{ route("profile.transactions") }}';
        },
        onError: function(result) {
            btn.disabled = false;
            btn.innerHTML = 'PAY NOW <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>';
            alert('Payment failed. Please try again.');
        },
        onClose: function() {
            btn.disabled = false;
            btn.innerHTML = 'PAY NOW <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>';
        }
    });
}

// Auto-open Snap popup on page load
window.addEventListener('load', function() {
    setTimeout(payNow, 500);
});
</script>
@endsection
