<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Sager')</title>
    <!--
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}"> -->

    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.css') }}">
    

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <meta name="description" content="SAGER, votre distributeur de confiance pour boissons, eaux, sodas, bières et gaz domestique. Vente en gros et détail avec livraison rapide. Contactez-nous au +237 XXX XXX XXX">
    <meta name="keywords" content="distributeur boissons, vente gaz domestique, eau en gros, sodas, bières, livraison boissons, SAGER Cameroun">
    <meta name="author" content="SAGER">
    <meta name="robots" content="index, follow">


      <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="SAGER - Distributeur de Boissons et Gaz Domestique">
    <meta property="og:description" content="Votre distributeur de confiance pour boissons et gaz domestique. Vente en gros et détail avec livraison rapide.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.sager-distribution.com">
    <meta property="og:image" content="https://www.sager-distribution.com/images/sager-logo.jpg">

     <link rel="icon" type="image/x-icon" href="/favicon.ico">
  
</head>

<body>

  


    <main style="height: 100vh; overflow-y: auto; padding: 20px;">
        @yield('content')
    </main>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <div class="footer-logo">
                    <i class="fas fa-home"></i>
                    <h3>ImmobilierBenin</h3>
                </div>
                <p>Votre partenaire de confiance pour l'immobilier au Bénin. Nous vous accompagnons dans tous vos projets immobiliers.</p>
            </div>

            <div class="footer-section">
                <h3><i class="fas fa-cogs"></i> Services</h3>
                <ul>
                    <li><a href="/ads"><i class="fas fa-shopping-cart"></i> Achat</a></li>
                    <li><a href="/ads"><i class="fas fa-tag"></i> Vente</a></li>
                    <li><a href="/ads"><i class="fas fa-home"></i> Location</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3><i class="fas fa-map-marker-alt"></i> Zones</h3>
                <ul>
                    <li><a href="/ads">Cotonou</a></li>
                    <li><a href="/ads">Porto-Novo</a></li>
                    <li><a href="/ads">Parakou</a></li>
                    <li><a href="/ads">Abomey-Calavi</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3><i class="fas fa-phone"></i> Contact</h3>
                <ul class="contact-list">
                    <li><i class="fas fa-phone"></i> +229 01 41 59 76 42</li>
                    <li><i class="fas fa-envelope"></i> contact@immobilierbenin.com</li>
                    <li><i class="fas fa-map-marker-alt"></i> Cotonou, Bénin</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2024 ImmobilierBenin. Tous droits réservés. | Conçu avec ❤️ pour l'Afrique</p>
        </div>
    </footer>




    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>