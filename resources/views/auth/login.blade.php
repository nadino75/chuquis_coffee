<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Chuquis Coffee</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts para elegancia -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%);
            color: #f8f9fa;
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .logo-header {
            background: linear-gradient(90deg, #1a1a1a, #3e2723);
            padding: 20px 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.5);
        }
        .logo-brand {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: #daa520 !important;
            font-size: 2rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .logo-brand:hover {
            color: #f8f9fa !important;
            transform: scale(1.05);
        }
        .login-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 50px 0;
        }
        .login-card {
            background: #2c2c2c;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.5);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            color: #f8f9fa;
            width: 100%;
            max-width: 400px;
        }
        .login-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.7);
        }
        .login-header {
            background: linear-gradient(90deg, #1a1a1a, #3e2723);
            border-radius: 15px 15px 0 0;
            padding: 20px;
            text-align: center;
        }
        .login-header h2 {
            font-family: 'Playfair Display', serif;
            color: #daa520;
            margin: 0;
        }
        .login-body {
            padding: 30px;
        }
        .form-control {
            background: #2c2c2c;
            border: 1px solid #daa520;
            border-radius: 10px;
            color: #f8f9fa;
            transition: border-color 0.3s ease;
        }
        .form-control:focus {
            border-color: #daa520;
            box-shadow: 0 0 8px rgba(218, 165, 32, 0.5);
            background: #2c2c2c;
            color: #f8f9fa;
        }
        .form-label {
            color: #daa520;
        }
        .btn-login {
            background: linear-gradient(45deg, #daa520, #b8860b);
            border: none;
            color: #1a1a1a;
            padding: 12px 25px;
            border-radius: 30px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(218, 165, 32, 0.3);
            width: 100%;
        }
        .btn-login:hover {
            background: linear-gradient(45deg, #b8860b, #daa520);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(218, 165, 32, 0.5);
        }
        .form-check-input:checked {
            background-color: #daa520;
            border-color: #daa520;
        }
        .form-check-label {
            color: #f8f9fa;
        }
        .forgot-password {
            color: #daa520;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .forgot-password:hover {
            color: #f8f9fa;
            text-decoration: underline;
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #f8f9fa;
        }
        .register-link a {
            color: #daa520;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .register-link a:hover {
            color: #f8f9fa;
            text-decoration: underline;
        }
        .footer {
            background: linear-gradient(90deg, #1a1a1a, #3e2723);
            color: #f8f9fa;
            padding: 20px 0;
            text-align: center;
            margin-top: auto;
        }
        .invalid-feedback {
            display: block;
        }
    </style>
</head>
<body>

    <!-- Logo Header -->
    <div class="logo-header">
        <div class="container">
            <div class="d-flex align-items-center">
                <a class="logo-brand" href="{{ url('/') }}">
                    <i class="fas fa-coffee"></i> Chuquis Coffee
                </a>
            </div>
        </div>
    </div>

    <!-- Sección de Login -->
    <section class="login-section">
        <div class="login-card">
            <div class="login-header">
                <h2><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</h2>
            </div>
            <div class="login-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            Recordarme
                        </label>
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-login">
                            Iniciar Sesión
                        </button>
                    </div>

                    <div class="text-center">
                        @if (Route::has('password.request'))
                            <a class="forgot-password" href="{{ route('password.request') }}">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>
                </form>

                @if (Route::has('register'))
                    <div class="register-link">
                        ¿No tienes una cuenta? <a href="{{ route('register') }}">Regístrate aquí</a>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="fas fa-coffee"></i> Chuquis Coffee</h5>
                    <p>El mejor café artesanal con el toque perfecto.</p>
                </div>
                <div class="col-md-4">
                    <h5>Enlaces Rápidos</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ url('/') }}#menu" class="text-light">Menú</a></li>
                        <li><a href="{{ url('/') }}#testimonials" class="text-light">Testimonios</a></li>
                        <li><a href="{{ url('/') }}#contact" class="text-light">Contacto</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Síguenos</h5>
                    <div class="social-links">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook fa-2x"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram fa-2x"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-twitter fa-2x"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <p>&copy; 2024 Chuquis Coffee. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>