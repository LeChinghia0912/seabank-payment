<style>
    .payment-card { background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); width: 100%; max-width: 400px; margin: 2rem auto; }
    .payment-title { font-size: 1.5rem; font-weight: bold; margin-bottom: 1.5rem; text-align: center; color: #1f2937; }
    .payment-form-group { margin-bottom: 1rem; }
    .payment-label { display: block; margin-bottom: 0.5rem; color: #4b5563; font-weight: 500; }
    .payment-input { width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; box-sizing: border-box; font-size: 1rem; }
    .payment-input:focus { outline: none; border-color: #3b82f6; ring: 2px solid #93c5fd; }
    .payment-button { width: 100%; background-color: #2563eb; color: white; padding: 0.75rem; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; transition: background-color 0.2s; }
    .payment-button:hover { background-color: #1d4ed8; }
    .payment-balance { text-align: center; margin-bottom: 1.5rem; color: #059669; font-weight: 600; }
</style>

<div class="payment-card">
    <h1 class="payment-title">Nạp Tiền</h1>
    <div class="payment-balance">Số dư hiện tại: {{ number_format(Auth::user()->balance ?? 0) }} VND</div>
    <form action="{{ route('payment.initiate') }}" method="POST">
        @csrf
        <div class="payment-form-group">
            <label class="payment-label" for="amount">Số tiền muốn nạp (VND)</label>
            <input class="payment-input" type="number" id="amount" name="amount" min="10000" step="1000" required placeholder="Nhập số tiền (tối thiểu 10,000)">
        </div>
        <button class="payment-button" type="submit">Thanh toán ngay</button>
    </form>
</div>
