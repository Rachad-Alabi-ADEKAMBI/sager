<!DOCTYPE html>
<html lang="fr">

@if(session('error'))
<script>
    alert("{!! session('error') !!}");
</script>
@endif


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - SAGER</title>
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo h1 {
            color: #667eea;
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .logo p {
            color: #666;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group .form-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
            z-index: 1;
        }

        .form-control {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .forgot-password {
            text-align: center;
            margin-top: 1rem;
        }

        .forgot-password a {
            color: #667eea;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: #764ba2;
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 1rem;
                padding: 1.5rem;
            }

            .logo h1 {
                font-size: 2rem;
            }
        }

        /* Styles spécifiques pour l'icône de l'œil */
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: #667eea;
        }
    </style>

    <style>
        @media screen and (max-width: 400px) {
            .login-container {
                margin: 0.5rem;
                padding: 1rem;
                max-width: 90%;
                /* Réduit la largeur */
            }

            .logo h1 {
                font-size: 1.8rem;
                /* Texte plus petit */
            }

            .form-control {
                padding: 12px 12px 12px 40px;
                /* Champs un peu plus compacts */
                font-size: 0.9rem;
            }

            .btn-login {
                padding: 12px;
                font-size: 0.9rem;
            }

            .forgot-password a {
                font-size: 0.8rem;
            }
        }
    </style>
</head>

<body>

    <main class="main-content">
        <div class="login-container">
            <div class="logo">
                <h1>SAGER MARKET</h1>
                <p>Système de Gestion de Stock</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <i class="fas fa-envelope form-icon"></i>
                    <input type="text" name="login" class="form-control" placeholder="Nom d’utilisateur ou email" required>

                </div>

                <div class="form-group">
                    <i class="fas fa-lock form-icon"></i>
                    <input type="password" name="password" id="password" class="form-control"
                        placeholder="Mot de passe" required>
                    <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                </div>

                <!-- Case à cocher Se souvenir de moi -->
                <div class="form-group remember-me">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember" style="color: grey">Se souvenir de moi</label>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>

                <div class="forgot-password">
                    <a href="{{ route('reset_password') }}">Mot de passe oublié ?</a>
                </div>
            </form>


        </div>
    </main>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Change l'icône de l'œil
            togglePassword.classList.toggle('fa-eye');
            togglePassword.classList.toggle('fa-eye-slash');
        });
    </script>

</body>

</html>