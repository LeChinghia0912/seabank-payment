<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán đơn hàng</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="bg-white p-8 rounded-lg shadow-lg text-center max-w-md w-full">
        <h2 class="text-2xl font-bold mb-4 text-blue-600">Quét mã để thanh toán</h2>
        
        <div class="mb-6 flex justify-center">
            <img src="{{ $qrUrl }}" alt="Mã QR Thanh Toán" class="border-4 border-blue-200 rounded-lg shadow-sm">
        </div>
        
        <p class="text-gray-700 font-semibold mb-2">Số tiền: <span class="text-xl text-red-500">{{ number_format($order->amount) }} VND</span></p>
        <p class="text-gray-500 text-sm mb-6">Nội dung: <span class="font-mono bg-gray-100 px-2 py-1 rounded">DH{{ $order->id }}</span></p>

        <div id="status-message" class="text-left text-sm text-gray-600 bg-blue-50 p-4 rounded mb-6">
            <p>1. Mở ứng dụng ngân hàng của bạn.</p>
            <p>2. Chọn tính năng Quét mã QR.</p>
            <p>3. Quét mã trên để chuyển khoản nhanh.</p>
            <p>4. Hệ thống sẽ tự động cập nhật sau vài phút.</p>
        </div>

        <a href="{{ route('dashboard') }}" class="block w-full py-2 px-4 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded transition duration-200">
            Quay về trang chủ
        </a>
    </div>

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>

    <script>
        window.Pusher = Pusher;

        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: '{{ env('VITE_REVERB_APP_KEY') }}',
            wsHost: '{{ env('VITE_REVERB_HOST', 'localhost') }}',
            wsPort: {{ env('VITE_REVERB_PORT', 8080) }},
            wssPort: {{ env('VITE_REVERB_PORT', 443) }},
            forceTLS: false,
            enabledTransports: ['ws', 'wss'],
        });

        const userId = {{ $order->user_id }};

        console.log('Listening on channel: user.' + userId);

        window.Echo.private(`user.${userId}`)
            .listen('.payment.completed', (e) => {
                console.log('Payment received:', e);
                
                const statusDiv = document.getElementById('status-message');
                statusDiv.className = "text-center text-sm text-green-700 bg-green-100 p-4 rounded mb-6";
                statusDiv.innerHTML = `
                    <p class="font-bold text-lg mb-2">✅ Thanh toán thành công!</p>
                    <p>Số dư: ${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(e.user.balance)}</p>
                    <p class="text-xs mt-2">Đang chuyển hướng...</p>
                `;

                setTimeout(() => {
                    window.location.href = "{{ route('dashboard') }}";
                }, 3000);
            });
    </script>

</body>
</html>
