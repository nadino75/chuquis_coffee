<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a Chuquis Coffee - Presentación Laravel</title>
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
        }
        .navbar {
            background: linear-gradient(90deg, #1a1a1a, #3e2723);
            box-shadow: 0 4px 12px rgba(0,0,0,0.5);
            transition: all 0.3s ease;
        }
        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: #daa520 !important;
            font-size: 1.5rem;
        }
        .navbar-nav .nav-link {
            color: #f8f9fa !important;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }
        .navbar-nav .nav-link:hover {
            color: #daa520 !important;
            transform: translateY(-2px);
        }
        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: #daa520;
            transition: all 0.3s ease;
        }
        .navbar-nav .nav-link:hover::after {
            width: 100%;
            left: 0;
        }
        .hero-section {
            background: linear-gradient(135deg, #1a1a1a 0%, #3e2723 100%);
            color: #f8f9fa;
            padding: 120px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('https://images.unsplash.com/photo-1509042239860-f550ce710b93?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') no-repeat center center;
            background-size: cover;
            opacity: 0.15;
            z-index: 1;
        }
        .hero-section .container {
            position: relative;
            z-index: 2;
        }
        .hero-section h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            margin-bottom: 20px;
            text-shadow: 2px 2px 6px rgba(0,0,0,0.7);
        }
        .hero-section p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }
        .carousel-item img {
            height: 500px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .carousel-item:hover img {
            transform: scale(1.05);
        }
        .card {
            background: #2c2c2c;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.5);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            color: #f8f9fa;
        }
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.7);
        }
        .card-img-top {
            border-radius: 15px 15px 0 0;
        }
        .btn-custom {
            background: linear-gradient(45deg, #daa520, #b8860b);
            border: none;
            color: #1a1a1a;
            padding: 12px 25px;
            border-radius: 30px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(218, 165, 32, 0.3);
        }
        .btn-custom:hover {
            background: linear-gradient(45deg, #b8860b, #daa520);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(218, 165, 32, 0.5);
        }
        .footer {
            background: linear-gradient(90deg, #1a1a1a, #3e2723);
            color: #f8f9fa;
            padding: 30px 0;
            text-align: center;
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
        }
        .form-label {
            color: #daa520;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="/"><i class="fas fa-coffee"></i> Chuquis Coffee</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#menu">Menú</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contacto</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/dashboard') }}">Dashboard</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">Registro</a>
                            </li>
                        @endif
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sección Hero -->
    <section class="hero-section">
        <div class="container">
            <h1>Bienvenido a Chuquis Coffee</h1>
            <p class="lead">Descubre el aroma perfecto en cada taza. Café artesanal, ambiente acogedor y momentos inolvidables. <br> <small>Presentación elegante con Laravel</small></p>
            <a href="#menu" class="btn btn-custom">Ver Menú</a>
        </div>
    </section>

    <!-- Carrusel de Imágenes -->
    <div id="coffeeCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#coffeeCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#coffeeCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#coffeeCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" class="d-block w-100" alt="Café recién preparado">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Café Fresco y Aromático</h5>
                    <p>Disfruta de nuestros granos seleccionados a mano.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://images.unsplash.com/photo-1559056199-641a0ac8b55e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" class="d-block w-100" alt="Ambiente acogedor">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Ambiente Elegante</h5>
                    <p>Un lugar perfecto para relajarte y trabajar.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" class="d-block w-100" alt="Especialidades de Chuquis">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Especialidades Únicas</h5>
                    <p>Prueba nuestros lattes y pasteles caseros.</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#coffeeCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#coffeeCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- Sección de Menú -->
    <section id="menu" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5" style="font-family: 'Playfair Display', serif;">Nuestro Menú</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <img src="https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" class="card-img-top" alt="Café Espresso">
                        <div class="card-body">
                            <h5 class="card-title">Espresso Clásico</h5>
                            <p class="card-text">Un shot intenso y puro. $2.50</p>
                            <a href="#" class="btn btn-custom">Ordenar</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <img src="https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" class="card-img-top" alt="Latte">
                        <div class="card-body">
                            <h5 class="card-title">Latte de Vainilla</h5>
                            <p class="card-text">Suave y cremoso con un toque dulce. $4.00</p>
                            <a href="#" class="btn btn-custom">Ordenar</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <img src="https://images.unsplash.com/photo-1551024506-0bccd828d307?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" class="card-img-top" alt="Pastel">
                        <div class="card-body">
                            <h5 class="card-title">Pastel de Chocolate</h5>
                            <p class="card-text">Hecho en casa, irresistible. $3.50</p>
                            <a href="#" class="btn btn-custom">Ordenar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección de Contacto -->
    <section id="contact" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5" style="font-family: 'Playfair Display', serif;">Contáctanos</h2>
            <div class="row">
                <div class="col-md-6">
                    <form action="{{ route('contact.submit') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Mensaje</label>
                            <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-custom">Enviar Mensaje</button>
                    </form>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Nuestra Ubicación</h5>
                            <p class="card-text">Calle Principal #123<br>Ciudad, Estado 12345</p>
                            <h5 class="card-title mt-4">Teléfono</h5>
                            <p class="card-text">+1 (555) 123-4567</p>
                            <h5 class="card-title mt-4">Email</h5>
                            <p class="card-text">info@chuquiscoffee.com</p>
                            <h5 class="card-title mt-4">Horario</h5>
                            <p class="card-text">Lunes a Viernes: 7:00 AM - 9:00 PM<br>Sábado y Domingo: 8:00 AM - 10:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Chuquis Coffee</h5>
                    <p>El mejor café artesanal con el toque perfecto.</p>
                </div>
                <div class="col-md-4">
                    <h5>Enlaces Rápidos</h5>
                    <ul class="list-unstyled">
                        <li><a href="#menu" class="text-light">Menú</a></li>
                        <li><a href="#contact" class="text-light">Contacto</a></li>
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