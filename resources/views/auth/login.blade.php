@extends('layouts.app')
@section('title', 'Login - PT Gema Bumi Arta')
@section('content')
<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background: linear-gradient(135deg, #1e293b, #0f172a);
        min-height: 100vh;
        position: relative;
        overflow: hidden;
    }

    /* subtle glow */
    body::before {
        content: "";
        position: absolute;
        width: 500px;
        height: 500px;
        background: rgba(13,110,253,0.15);
        border-radius: 50%;
        top: -150px;
        left: -150px;
        filter: blur(150px);
    }

    .login-container {
        min-height: 100vh;
        position: relative;
        z-index: 2;
    }

    .login-card {
        width: 100%;
        max-width: 560px; /* 🔥 DIPERBESAR */
        padding: 60px; /* 🔥 LEBIH LEGA */
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 25px 70px rgba(0,0,0,0.5);
        animation: fadeIn 0.6s ease;
    }

    .login-title {
        font-weight: 600;
        font-size: 28px; /* 🔥 LEBIH BESAR */
        color: #222;
    }

    .login-subtitle {
        font-size: 14px;
        color: #777;
    }

    .form-control {
        border-radius: 10px;
        padding: 15px; /* 🔥 LEBIH BESAR */
        font-size: 15px;
        border: 1px solid #ddd;
    }

    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 2px rgba(13,110,253,0.1);
    }

    .btn-login {
        border-radius: 10px;
        padding: 14px;
        font-weight: 600;
        font-size: 15px;
    }

    .show-password {
        font-size: 13px;
        cursor: pointer;
        color: #666;
    }

    .show-password:hover {
        color: #0d6efd;
    }

    .footer-text {
        font-weight: 600;
        color: #555;
        font-size: 14px;
    }

    .footer-text small {
        display: block;
        color: #888;
        font-size: 12px;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(25px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="container login-container d-flex align-items-center justify-content-center">
    <div class="login-card">

        <div class="text-center mb-4">
            <div class="login-title">PT Gema Bumi Arta</div>
            <div class="login-subtitle">Sistem Inventory Pertamina Gas</div>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label class="mb-1">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="mb-1">Password</label>
                <input id="password" type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-4 text-end">
                <span class="show-password" onclick="togglePassword()">
                    Tampilkan Password
                </span>
            </div>

            <div class="d-grid">
                <button class="btn btn-primary btn-login">
                    Login
                </button>
            </div>
        </form>

        <div class="text-center mt-4 footer-text">
            © {{ date('Y') }} PT Gema Bumi Arta
            <small>Inventory System</small>
        </div>

    </div>
</div>

<script>
function togglePassword() {
    let input = document.getElementById("password");
    input.type = input.type === "password" ? "text" : "password";
}
</script>

@endsection