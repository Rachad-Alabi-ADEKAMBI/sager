<main class="main-content">
    <div class="login-container">
        <div class="logo">
            <h1>SAGER MARKET</h1>
            <p>Système de Gestion de Stock</p>
        </div>

        <form method="POST" action="">
            @csrf
            <div class="form-group">
                <i class="fas fa-user"></i>
                <input type="email" name="email" class="form-control" placeholder="Adresse email" required>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Réinitialiser le mot de passe
            </button>

            <div class="forgot-password">
                <a href="/login">Se connecter</a>
            </div>
        </form>

    </div>
</main>

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

    .form-group i {
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
</style>