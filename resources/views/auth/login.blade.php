<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/logoblack.ico" type="image/x-icon">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wght@6..144,1..1000&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>

<body>

    <div class="auth-container">

        <div class="auth-left">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="auth-brand">
            <img src="{{ asset('images/login-bg.png') }}" alt="Login Illustration" class="auth-illustration">
        </div>

        <div class="auth-right">
            <div class="auth-wrapper">
                <h1 class="auth-title">Xin chào</h1>
                <p class="auth-subtitle">Vui lòng đăng nhập</p>

                @if(session('warning'))
                <div class="alert alert-warning">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ session('warning') }}
                </div>
                @endif

                {{-- Alert 'message' đã được xử lý bởi SweetAlert ở layout --}}

                @if($errors->has('locked'))
                <div class="alert alert-danger" id="lock-msg">
                    <div>{{ $errors->first('locked') }}</div>
                    <div class="mt-2">Vui lòng thử lại sau: <span id="countdown" class="timer-count">{{ $errors->first('seconds_remaining') }}</span> giây</div>
                </div>
                @endif

                <form action="{{ route('auth.handleLogin') }}" method="POST" id="loginForm">
                    @csrf

                    <div class="auth-form-group">
                        <label class="auth-label">Tên tài khoản</label>
                        <div class="auth-input-wrapper">
                            <input type="text" name="username" class="auth-input @error('username') is-invalid @enderror" placeholder="Nhập tên tài khoản" required autofocus value="{{ old('username') }}">
                            @error('username')
                                <span class="text-danger" style="color: #dc2626; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="auth-form-group">
                        <label class="auth-label">Mật khẩu</label>
                        <div class="auth-input-wrapper" style="position: relative;">
                            {{-- Thêm ID password để JS tìm chính xác --}}
                            <input type="password" name="password" class="auth-input @error('password') is-invalid @enderror" placeholder="Nhập mật khẩu" required id="password">
                            
                            {{-- Nút icon con mắt --}}
                            <span id="togglePassword" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #64748b;">
                                <i class="fa-solid fa-eye" id="eyeIcon"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="auth-btn" id="btnSubmit">Đăng nhập</button>

                    <div class="auth-footer" style="text-align: center; margin-top: 15px;">
                        Chưa có tài khoản? <a href="{{ route('auth.register') }}" class="auth-link">Đăng ký ngay</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. XỬ LÝ ĐẾM NGƯỢC KHÓA ĐĂNG NHẬP
    // Sử dụng cú pháp Blade chuẩn để tránh lỗi Syntax JS
    let seconds = {{ $errors->has('seconds_remaining') ? ($errors->first('seconds_remaining') ?: 0) : 0 }};
    
    if (seconds > 0) {
        const btnSubmit = document.getElementById('btnSubmit');
        const inputs = document.querySelectorAll('.auth-input');
        const timerSpan = document.getElementById('countdown');
        const lockMsg = document.getElementById('lock-msg');

        if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.innerText = "Đang bị khóa tạm thời";
        }
        
        inputs.forEach(input => input.disabled = true);

        let interval = setInterval(function() {
            seconds--;
            if (timerSpan) timerSpan.innerText = seconds;
            if (seconds <= 0) {
                clearInterval(interval);
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                    btnSubmit.innerText = "Đăng nhập";
                }
                inputs.forEach(input => input.disabled = false);
                if (lockMsg) lockMsg.style.display = 'none';
            }
        }, 1000);
    }

    // 2. XỬ LÝ ẨN/HIỆN MẬT KHẨU (FIX LỖI NHẤN ĐÚP)
    const toggleBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    if (toggleBtn && passwordInput && eyeIcon) {
        toggleBtn.addEventListener('click', function () {
            // Chuyển đổi qua lại giữa 'password' và 'text'
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            
            // Đổi icon tương ứng
            if (isPassword) {
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
    }
});
</script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Thành công!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: "{{ session('error') }}",
            });
        @endif
    </script>
</body>
</html>