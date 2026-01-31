<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
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
                <h1 class="auth-title">Tạo tài khoản mới 🚀</h1>
                <p class="auth-subtitle">Điền thông tin bên dưới để tham gia cùng Kuchen</p>

                <form action="{{ route('auth.handleRegister') }}" method="POST">
                    @csrf

                    <div class="auth-form-group">
                        <label class="auth-label">Họ và tên</label>
                        <div class="auth-input-wrapper">
                            <input type="text" name="full_name" class="auth-input" placeholder="Ví dụ: Nguyễn Văn A" required autofocus>
                        </div>
                    </div>
                    


                    <div class="auth-form-group">
                        <label class="auth-label">Tên tài khoản</label>
                        <div class="auth-input-wrapper">
                            <input type="text" name="username" class="auth-input" placeholder="Nhập tên tài khoản" required>
                        </div>
                    </div>

                    <div class="auth-form-group">
                        <label class="auth-label">Địa chỉ Email</label>
                        <div class="auth-input-wrapper">
                            <input type="email" name="email" class="auth-input" placeholder="Nhập địa chỉ email" required>
                        </div>
                    </div>

                    <div class="auth-form-group">
                        <label class="auth-label">Mật khẩu</label>
                        <div class="auth-input-wrapper">
                            <input type="password" name="password" class="auth-input" placeholder="Tạo mật khẩu" required id="password-field">
                            <i class="fa-solid fa-eye-slash auth-toggle-pass" onclick="togglePassword('password-field', this)"></i>
                        </div>
                    </div>

                    <div class="auth-form-group">
                        <label class="auth-label">Xác nhận mật khẩu</label>
                        <div class="auth-input-wrapper">
                            <input type="password" name="password_confirmation" class="auth-input" placeholder="Nhập lại mật khẩu" required id="password-confirm">
                            <i class="fa-solid fa-eye-slash auth-toggle-pass" onclick="togglePassword('password-confirm', this)"></i>
                        </div>
                    </div>

                    <button type="submit" class="auth-btn">Đăng ký ngay</button>

                    <div class="auth-footer">
                        Đã có tài khoản? <a href="{{ route('auth.login') }}" class="auth-link">Đăng nhập tại đây</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId, iconElement) {
            const passwordField = document.getElementById(fieldId);
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                iconElement.classList.remove('fa-eye-slash');
                iconElement.classList.add('fa-eye');
            } else {
                passwordField.type = 'password';
                iconElement.classList.remove('fa-eye');
                iconElement.classList.add('fa-eye-slash');
            }
        }
    </script>

</body>
</html>