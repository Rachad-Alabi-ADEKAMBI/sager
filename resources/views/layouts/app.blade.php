

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Sager')</title>

    @vite('resources/js/app.js')

    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.css') }}">

    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <meta name="description"
        content="SAGER, votre distributeur de confiance pour boissons, eaux, sodas, bières et gaz domestique. Vente en gros et détail avec livraison rapide. Contactez-nous au +237 XXX XXX XXX">
    <meta name="keywords"
        content="distributeur boissons, vente gaz domestique, eau en gros, sodas, bières, livraison boissons, SAGER Cameroun">
    <meta name="author" content="SAGER">
    <meta name="robots" content="index, follow">


    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="SAGER - Distributeur de Boissons et Gaz Domestique">
    <meta property="og:description"
        content="Votre distributeur de confiance pour boissons et gaz domestique. Vente en gros et détail avec livraison rapide.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.sager-distribution.com">
    <meta property="og:image" content="https://www.sager-distribution.com/images/sager-logo.jpg">

    <link rel="icon" type="image/x-icon" href="/favicon.ico">

</head>

<body>

   <div id='app'>
         <!-- Header -->
    <header class="header">
        <div class="nav-container">
            <div class="logo">SAGER</div>
            <nav>
                <ul class="nav-menu">

                    @auth
                        @if (auth()->user() && auth()->user()->role == 'admin')
                            <li><a href="{{ route('dashboardAdmin') }}"><i class="fas fa-tachometer-alt"></i> Tableau de
                                    bord</a></li>
                        @else
                            <li><a href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
                            </li>
                        @endif


                        <li>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="logout-button"
                                    style="background: none; border: none; color: inherit; cursor: pointer; font-weight: bold;
                                    font-size: 0.9rem; padding: 0;">
                                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                                </button>
                            </form>
                        </li>
                    @endauth


                </ul>
            </nav>
            @guest
                <div class="user-info">
                    <a href="/login" style="color: white;"><i class="fas fa-sign-in-alt"></i>
                        Connexion</a>
                </div>
            @endguest
        </div>
    </header>


    @yield('content')

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-links">
                <a href="{{ route('home') }}">Accueil</a>
                <a href="#apropos">À propos</a>
                <a href="#produits">Produits</a>
                <a href="#contact">Contact</a>
                @auth
                    @if (auth()->user()->role == 'admin')
                        <a href="{{ route('dashboardAdmin') }}">Tableau de bord</a>
                    @else
                        <a href="{{ route('dashboard') }}">Tableau de bord</a>
                    @endif
                @endauth
            </div>
            <p>&copy; 2025 SAGER. Tous droits réservés. | Distributeur de Boissons et Gaz Domestique</p>
            <p style="margin-top: 1rem; font-size: 0.9rem; opacity: 0.8;">
                Vente en gros et détail • Livraison rapide • Service client de qualité
            </p>
        </div>
    </footer>
   </div>

    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>
