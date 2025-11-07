<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Sager')</title>

    @vite('resources/js/app.js')

    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.css') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <meta name="description"
        content="SAGER, votre distributeur de confiance pour boissons, eaux, sodas, bières et gaz domestique. 
        Vente en gros et détail avec livraison rapide. Contactez-nous au +229  01 96 46 66 25">
    <meta name="keywords"
        content="distributeur boissons, vente gaz domestique, eau en gros, sodas, bières, livraison boissons, SAGER Bénin">
    <meta name="author" content="SAGER">
    <meta name="robots" content="index, follow">
    <link rel="shortcut icon" href="https://i.ibb.co/Kjy8JcZF/logo-final.png" type="image/png">
    <link rel="icon" href="https://i.ibb.co/Kjy8JcZF/logo-final.png" sizes="32x32">
    <link rel="apple-touch-icon" href="https://i.ibb.co/Kjy8JcZF/logo-final.png">




    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="SAGER - Distributeur de Boissons et Gaz Domestique">
    <meta property="og:description"
        content="Votre distributeur de confiance pour boissons et gaz domestique. Vente en gros et détail avec livraison rapide.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.sager-distribution.com">


</head>

<body>

    <div id='app'>
        <!-- Header -->


        @yield('content')
    </div>


    <script src="https://cdn.jsdelivr.net/npm/vue@3.4.21/dist/vue.global.prod.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        axios.defaults.headers.common['X-CSRF-TOKEN'] =
            document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>

</body>

</html>