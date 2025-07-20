@extends('layouts.app')

@section('title', 'Accueil')

@section('content')
    <main class="main">
        <!-- Hero Section -->
        <section id="accueil" class="hero">
            <div class="hero-content">
                <h1>Bienvenue chez SAGER</h1>
                <p>Votre distributeur de confiance pour boissons, eaux, sodas, bières et gaz domestique. Vente en gros et
                    détail avec service de livraison rapide dans toute la région.</p>
                <a href="#contact" class="cta-button">
                    <i class="fas fa-phone"></i> Nous contacter
                </a>
            </div>
        </section>

        <!-- About Section -->
        <section id="apropos" class="about">
            <div class="container">
                <h2 class="section-title">À Propos de SAGER</h2>
                <div class="about-content">
                    <div class="about-text">
                        <h3>Notre Histoire</h3>
                        <p>Depuis plus de 10 ans, SAGER s'est imposé comme le leader de la distribution de boissons et de
                            gaz domestique dans la région. Notre engagement envers la qualité et le service client nous a
                            permis de bâtir une réputation solide.</p>

                        <h3>Notre Mission</h3>
                        <p>Fournir à nos clients des produits de qualité supérieure à des prix compétitifs, avec un service
                            de livraison rapide et fiable. Nous nous efforçons de répondre aux besoins de nos clients
                            particuliers et professionnels.</p>
                    </div>
                    <div class="about-image">
                        <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/WhatsApp%20Image%202025-07-16%20at%207.07.40%20AM-uBRukbqKQHM4lRHsRRU8kv42xg0E9S.jpeg"
                            alt="Entrepôt SAGER avec stock de boissons">
                    </div>
                </div>

                <div class="features-grid">
                    <div class="feature-card">
                        <i class="fas fa-truck"></i>
                        <h3>Livraison Rapide</h3>
                        <p>Service de livraison dans toute la région avec des délais respectés et un service client de
                            qualité.</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-shield-alt"></i>
                        <h3>Produits de Qualité</h3>
                        <p>Nous sélectionnons rigoureusement nos fournisseurs pour garantir la fraîcheur et la qualité de
                            nos produits.</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-users"></i>
                        <h3>Service Client</h3>
                        <p>Une équipe dédiée à votre service pour répondre à toutes vos questions et besoins spécifiques.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Gallery Section -->
        <section id="produits" class="gallery">
            <div class="container">
                <h2 class="section-title">Nos Produits</h2>
                <div class="gallery-grid">
                    <div class="gallery-item">
                        <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/WhatsApp%20Image%202025-07-16%20at%207.07.39%20AM-wW9qqwdT0BcVm47Uh1EaH1z58cWO0E.jpeg"
                            alt="Packs d'eau en bouteilles">
                        <div class="gallery-overlay">
                            <h3>Eaux en Bouteilles</h3>
                            <p>Large gamme d'eaux minérales et de source, disponibles en différents formats</p>
                        </div>
                    </div>

                    <div class="gallery-item">
                        <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/WhatsApp%20Image%202025-07-16%20at%207.07.43%20AM-Lx9tURBN0Lq4fNyWWXqRuVQUGoEEAf.jpeg"
                            alt="Caisses de bières et sodas">
                        <div class="gallery-overlay">
                            <h3>Bières et Sodas</h3>
                            <p>Toutes les marques populaires de bières et sodas, fraîches et disponibles en gros</p>
                        </div>
                    </div>

                    <div class="gallery-item">
                        <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/WhatsApp%20Image%202025-07-16%20at%207.07.43%20AM%20%281%29-iib34BZ7jw03tNcux6rdpnybcEwPbr.jpeg"
                            alt="Stock de boissons diverses">
                        <div class="gallery-overlay">
                            <h3>Boissons Diverses</h3>
                            <p>Jus de fruits, boissons énergisantes, et autres rafraîchissements</p>
                        </div>
                    </div>

                    <div class="gallery-item">
                        <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/WhatsApp%20Image%202025-07-16%20at%207.07.40%20AM-uBRukbqKQHM4lRHsRRU8kv42xg0E9S.jpeg"
                            alt="Étalage de boissons organisé">
                        <div class="gallery-overlay">
                            <h3>Vente au Détail</h3>
                            <p>Espace de vente organisé pour faciliter vos achats au détail</p>
                        </div>
                    </div>

                    <div class="gallery-item">
                        <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/WhatsApp%20Image%202025-07-16%20at%207.07.42%20AM-6sNjtAG3G1VI8OfO8kYHh0b3rpiZ3p.jpeg"
                            alt="Bouteilles de gaz domestique">
                        <div class="gallery-overlay">
                            <h3>Gaz Domestique</h3>
                            <p>Bouteilles de gaz de différentes tailles pour tous vos besoins domestiques</p>
                        </div>
                    </div>

                    <div class="gallery-item">
                        <div
                            style="height: 250px; background: linear-gradient(45deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="gallery-overlay">
                            <h3>Et Bien Plus...</h3>
                            <p>Découvrez notre gamme complète en magasin ou contactez-nous pour plus d'informations</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="contact">
            <div class="container">
                <h2 class="section-title">Contactez-Nous</h2>
                <div class="contact-content">
                    <div class="contact-info">
                        <h3 style="margin-bottom: 2rem; color: #333;">Informations de Contact</h3>

                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <strong>Adresse</strong><br>
                                Ayélawadjè, 1ère vons à droite en venant de Midombo<br>
                                Cotonou, Bénin
                            </div>
                        </div>

                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <div>
                                <strong>Téléphone</strong><br>
                                +229 0196466625<br>
                            </div>
                        </div>

                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <strong>Email</strong><br>
                                contact@sager.bj<br>
                            </div>
                        </div>

                        <div class="contact-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <strong>Horaires d'ouverture</strong><br>
                                Lundi - Samedi: 08h00 - 19h00<br>
                                Dimanche: Fermé
                            </div>
                        </div>

                        <a href="https://wa.me/2290196466625" class="whatsapp-btn" target="_blank">
                            <i class="fab fa-whatsapp"></i>
                            Contactez-nous sur WhatsApp
                        </a>
                    </div>

                    <div class="map-container">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3979.8234567890123!2d9.7678901234567!3d4.0123456789012!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNMKwMDAnMjguNCJOIDnCsDQ2JzA0LjQiRQ!5e0!3m2!1sfr!2scm!4v1234567890123!5m2!1sfr!2scm"
                            allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                            title="Localisation SAGER">
                        </iframe>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-menu a {
            color: white;
            text-decoration: none;
            transition: color 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 5px;
        }

        .nav-menu a:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8rem 0 4rem;
            text-align: center;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            animation: fadeInUp 1s ease-out;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            animation: fadeInUp 1s ease-out 0.2s both;
        }

        .cta-button {
            display: inline-block;
            background: white;
            color: #667eea;
            padding: 1rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            animation: fadeInUp 1s ease-out 0.4s both;
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        /* About Section */
        .about {
            padding: 4rem 0;
            background: #f8f9fa;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 3rem;
            color: #333;
        }

        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
            margin-bottom: 3rem;
        }

        .about-text {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #666;
        }

        .about-text h3 {
            color: #333;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .about-image {
            text-align: center;
        }

        .about-image img {
            max-width: 100%;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-card i {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }

        /* Gallery Section */
        .gallery {
            padding: 4rem 0;
            background: white;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .gallery-item {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .gallery-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .gallery-item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .gallery-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
            color: white;
            padding: 2rem 1rem 1rem;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }

        .gallery-item:hover .gallery-overlay {
            transform: translateY(0);
        }

        /* Contact Section */
        .contact {
            padding: 4rem 0;
            background: #f8f9fa;
        }

        .contact-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            margin-top: 2rem;
        }

        .contact-info {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .contact-item:hover {
            background: #667eea;
            color: white;
            transform: translateX(5px);
        }

        .contact-item i {
            font-size: 1.5rem;
            margin-right: 1rem;
            width: 30px;
            color: #667eea;
        }

        .contact-item:hover i {
            color: white;
        }

        .whatsapp-btn {
            background: #25D366;
            color: white;
            padding: 1rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .whatsapp-btn:hover {
            background: #128C7E;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 211, 102, 0.3);
        }

        .map-container {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            height: 400px;
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        /* Footer */
        .footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 3rem 0;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: #667eea;
        }

        /* Animations */
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

        /* Responsive */
        @media (max-width: 768px) {
            .nav-menu {
                display: none;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .nav-container {
                padding: 0 1rem;
            }

            .about-content,
            .contact-content {
                grid-template-columns: 1fr;
            }

            .gallery-grid {
                grid-template-columns: 1fr;
            }

            .footer-links {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
@endsection
