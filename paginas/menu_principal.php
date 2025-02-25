<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Transport - Menu Principal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a365d;
            --secondary-color: #2a4f7e;
            --accent-color: #38b2ac;
        }

        .hero-section {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 100px 0 50px;
            margin-bottom: 30px;
        }

        .hero-section h1 {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .hero-section p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }

        .btn-hero {
            background: var(--accent-color);
            color: white;
            padding: 15px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-hero:hover {
            background: #2c7a7b;
            color: white;
            transform: scale(1.05);
        }

        .feature-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s;
            margin-bottom: 25px;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .feature-icon {
            font-size: 2rem;
            color: var(--accent-color);
            margin-bottom: 20px;
        }

        .feature-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .feature-description {
            font-size: 1rem;
            color: #666;
            margin-bottom: 20px;
        }

        .btn-feature {
            background: var(--accent-color);
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-feature:hover {
            background: #2c7a7b;
            color: white;
            transform: scale(1.05);
        }
        
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top"> 
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-ambulance me-2"></i>
                MedQ
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="aba_entrar.php">Entrar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Empresas</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container text-center">
            <h1>Transporte médico seguro e confiável</h1>
            <p>Conectamos pacientes a empresas de transporte especializado</p>
            <button class="btn btn-hero">Agendar Agora</button>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card p-4">
                    <div class="feature-icon">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <h2 class="feature-title">Agendamento Rápido</h2>
                    <p class="feature-description">Agende seu transporte em poucos minutos</p>
                    <button class="btn btn-feature">Saiba Mais</button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card p-4">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h2 class="feature-title">Segurança Garantida</h2>
                    <p class="feature-description">Profissionais treinados e veículos adaptados</p>
                    <button class="btn btn-feature">Saiba Mais</button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card p-4">
                    <div class="feature-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h2 class="feature-title">Rastreamento em Tempo Real</h2>
                    <p class="feature-description">Acompanhe seu transporte em tempo real</p>
                    <button class="btn btn-feature">Saiba Mais</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>