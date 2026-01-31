<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đang chuyển hướng...</title>
</head>
<body>
    <p style="text-align: center; margin-top: 20%; font-family: sans-serif;">Đang kết nối đến cổng thanh toán SePay...</p>
    <p style="text-align: center; font-family: sans-serif; font-size: 12px; color: #666;" id="status-msg">Vui lòng không tắt trình duyệt.</p>
    
    <!-- Correct URL from User Docs: https://pay-sandbox.sepay.vn/v1/checkout/init -->
    <form id="sepay-form" action="https://pay-sandbox.sepay.vn/v1/checkout/init" method="POST">
        @foreach ($paymentData as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
    </form>

    <script>
        // Auto-submit the form
        document.getElementById('sepay-form').submit();
    </script>
</body>
</html>
