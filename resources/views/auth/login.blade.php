<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - GesBoutique</title>

    <!-- PWA -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#2563eb">
    <link rel="icon" href="{{ asset('icons/icon.svg') }}" type="image/svg+xml">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="GesBoutique">

    @vite(['resources/css/vendor.css', 'resources/js/vendor.js'])

    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1e40af;
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-dark: linear-gradient(135deg, #1f2937 0%, #374151 100%);
        }

        body {
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 1000px;
            width: 100%;
            min-height: 600px;
            display: flex;
            flex-direction: row;
        }
        
        .login-left {
            background: var(--gradient-dark);
            padding: 60px 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .login-left::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.03)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            pointer-events: none;
        }
        
        .login-right {
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex: 1;
        }
        
        .brand-logo {
            font-family: 'Inter', system-ui, sans-serif;
            font-weight: 700;
            font-size: 2rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }
        
        .brand-subtitle {
            color: rgba(255,255,255,0.8);
            font-size: 1rem;
            margin-bottom: 40px;
            position: relative;
            z-index: 1;
        }
        
        .features {
            position: relative;
            z-index: 1;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            color: rgba(255,255,255,0.9);
        }
        
        .feature-icon {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 10px;
            margin-right: 15px;
            min-width: 40px;
            text-align: center;
        }
        
        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 16px;
            font-weight: 500;
            transition: all 0.3s ease;
            background: white;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            transform: translateY(-1px);
        }
        
        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            border-radius: 12px;
            padding: 14px 28px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            color: white;
            width: 100%;
            font-size: 1rem;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.2);
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            color: white;
        }
        
        .btn-primary:hover::before {
            left: 0;
        }
        
        .alert {
            border: none;
            border-radius: 12px;
            padding: 16px 20px;
            font-weight: 500;
            box-shadow: var(--shadow-sm);
            margin-bottom: 20px;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            color: #166534;
            border-left: 4px solid #22c55e;
        }
        
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 400px;
            }
            
            .login-left {
                padding: 40px 20px;
                text-align: center;
            }
            
            .login-right {
                padding: 40px 20px;
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fadeInUp {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>
<body>
    <div class="login-container animate-fadeInUp">
        <!-- Côté gauche - Présentation -->
        <div class="login-left">
            <!-- Espace vide -->
        </div>
        
        <!-- Côté droit - Formulaire de connexion -->
        <div class="login-right">
            <div class="text-center mb-4">
                <h2 class="fw-bold mb-3">Connexion</h2>
                <p class="text-muted">Accédez à votre espace de gestion</p>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif
            
                        
            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                
                <div class="mb-4">
                    <label for="username" class="form-label fw-semibold">
                        <i class="fas fa-user me-2 text-primary"></i>
                        Nom d'utilisateur ou Email
                    </label>
                    <input type="text" class="form-control form-control-lg" 
                           id="username" name="username" 
                           value="{{ old('username') }}" 
                           placeholder="Entrez votre nom d'utilisateur ou email" 
                           required autofocus>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label fw-semibold">
                        <i class="fas fa-lock me-2 text-primary"></i>
                        Mot de passe
                    </label>
                    <input type="password" class="form-control form-control-lg" 
                           id="password" name="password" 
                           placeholder="Entrez votre mot de passe" 
                           required>
                </div>
                
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Se souvenir de moi
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Se connecter
                </button>
            </form>
            
            <div class="text-center mt-4">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Utilisez vos identifiants pour accéder à l'application
                </small>
            </div>
        </div>
    </div>
    
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('{{ asset('sw.js') }}').catch(() => {});
            });
        }
    </script>
</body>
</html>
