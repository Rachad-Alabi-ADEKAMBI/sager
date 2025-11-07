<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Sager Market')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    @vite('resources/js/app.js')

    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.css') }}">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <meta name="description"
        content="SAGER MARKET, votre distributeur de confiance pour boissons, eaux, sodas, bières et gaz domestique. Vente en gros et détail avec livraison rapide. Contactez-nous au +237 XXX XXX XXX">
    <meta name="keywords"
        content="distributeur boissons, vente gaz domestique, eau en gros, sodas, bières, livraison boissons, SAGER MARKET Benin">
    <meta name="author" content="SAGER MARKET">
    <meta name="robots" content="index, follow">


    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="SAGER MARKET - Distributeur de Boissons et Gaz Domestique">
    <meta property="og:description"
        content="Votre distributeur de confiance pour boissons et gaz domestique. Vente en gros et détail avec livraison rapide.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.sagermarket.com">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

</head>

<main class="main">
    <header class="header">
        <div class="nav-container">
            <div class="logo">SAGE<a href="{{ route('login') }}"
                    style="color: white; text-decoration: none; margin-left: -7px">R</a> MARKET
            </div>

        </div>
    </header>
    <!-- Hero Section -->
    <section id="accueil" class="hero">
        <div class="hero-background"></div>
        <div class="hero-content">
            <h1>Bienvenue chez SAGER MARKET</h1>
            <p class="hero-subtitle">Votre partenaire de confiance pour tous vos besoins en boissons et gaz domestique
            </p>
            <p class="hero-description">Distribution professionnelle • Vente en gros et détail </p>
            <div class="hero-buttons">
                <a href="#contact" class="cta-button primary">
                    <i class="fas fa-phone"></i> Nous contacter
                </a>
                <a href="#produits" class="cta-button secondary">
                    <i class="fas fa-eye"></i> Voir nos produits
                </a>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services">
        <div class="container">
            <h2 class="section-title">Nos Services</h2>
            <p class="section-subtitle">Des solutions complètes pour répondre à tous vos besoins</p>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <h3>Vente en Gros</h3>
                    <p>Tarifs préférentiels pour les professionnels, restaurants, hôtels et revendeurs.</p>
                    <ul class="service-features">
                        <li><i class="fas fa-check"></i> Prix dégressifs par quantité</li>
                        <li><i class="fas fa-check"></i> Facturation professionnelle</li>
                        <li><i class="fas fa-check"></i> Remises et ristournes</li>
                    </ul>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <h3>Vente au Détail</h3>
                    <p>Magasin ouvert au public avec un large choix de produits vendus au pix du détaillant.</p>
                    <ul class="service-features">
                        <li><i class="fas fa-check"></i> Prix avantageux</li>
                        <li><i class="fas fa-check"></i> Articles de qualité</li>
                        <li><i class="fas fa-check"></i> Promotions régulières</li>
                    </ul>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-fire"></i>
                    </div>
                    <h3>Gaz Domestique</h3>
                    <p>Distribution de bouteilles de gaz de toutes tailles et origine avec service de consigne .</p>
                    <ul class="service-features">
                        <li><i class="fas fa-check"></i> Toutes tailles disponibles</li>
                        <li><i class="fas fa-check"></i> Échange de bouteilles</li>
                        <li><i class="fas fa-check"></i> Consignation </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>


    <!-- À propos simplifié -->
    <section id="apropos" class="about">
        <div class="container">
            <h2 class="section-title">À propos de SAGER MARKET</h2>
            <p class="section-subtitle">Distributeur local de boissons et gaz à Cotonou</p>

            <div class="about-content">
                <div class="about-text">
                    <h3><i class="fas fa-info-circle"></i> Qui sommes-nous ?</h3>
                    <p>SAGER MARKET est une boutique locale spécialisée dans la distribution de boissons et de gaz domestique.
                        Nous
                        servons particuliers et professionnels avec des produits fiables et un service simple.</p>

                    <h3><i class="fas fa-bullseye"></i> Ce que nous proposons</h3>
                    <p>Livraison rapide, produits suivis, prix accessibles. Notre équipe est disponible pour toute
                        commande
                        ou renseignement.</p>

                    <h3><i class="fas fa-handshake"></i> Nos engagements</h3>
                    <ul class="simple-list">
                        <li><i class="fas fa-check-circle"></i> Produits disponibles</li>
                        <li><i class="fas fa-clock"></i> Promotions régulières</li>
                        <li><i class="fas fa-phone"></i> Facturation professionnelle</li>
                    </ul>
                </div>

                <div class="about-image">
                    <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/WhatsApp%20Image%202025-07-16%20at%207.07.40%20AM-uBRukbqKQHM4lRHsRRU8kv42xg0E9S.jpeg"
                        alt="Entrepôt SAGER">
                    <div class="image-overlay">
                        <div class="overlay-content">
                            <h4>Notre stock</h4>
                            <p>Produits disponibles en quantité</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <i class="fas fa-gas-pump"></i>
                    <h3>Consignation</h3>
                    <p>Reprise et échange de bouteilles consignées.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-box"></i>
                    <h3>Stock local</h3>
                    <p>Produits disponibles en dépôt, visibles sur place.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-phone"></i>
                    <h3>Commande simple</h3>
                    <p>Appelez ou envoyez un message pour réserver.</p>
                </div>
            </div>

        </div>
    </section>


    <!-- Products Section -->
    <section id="produits" class="gallery">
        <div class="container">
            <h2 class="section-title">Nos Produits</h2>
            <p class="section-subtitle">Une gamme complète pour tous vos besoins</p>

            <!-- Product Categories -->
            <div class="product-categories">
                <button class="category-btn active" data-category="all">Tous les produits</button>
                <button class="category-btn" data-category="water">Eaux</button>
                <button class="category-btn" data-category="beverages">Boissons</button>
                <button class="category-btn" data-category="beer">Bières</button>
                <button class="category-btn" data-category="gas">Gaz</button>
            </div>

            <div class="gallery-grid">
                <div class="gallery-item" data-category="water">
                    <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/WhatsApp%20Image%202025-07-16%20at%207.07.39%20AM-wW9qqwdT0BcVm47Uh1EaH1z58cWO0E.jpeg"
                        alt="Packs d'eau en bouteilles">
                    <div class="gallery-overlay">
                        <h3>Eaux en Bouteilles</h3>
                        <p>Large gamme d'eaux minérales et de source, disponibles en différents formats (0.5L, 1L, 1.5L)
                        </p>
                        <div class="product-brands">
                            <span class="brand">Possotomé</span>
                            <span class="brand">Awé</span>
                            <span class="brand">Voltic</span>
                        </div>
                        <div class="product-price">À partir de 250 FCFA</div>
                    </div>
                </div>
                <div class="gallery-item" data-category="beer beverages">
                    <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/WhatsApp%20Image%202025-07-16%20at%207.07.43%20AM-Lx9tURBN0Lq4fNyWWXqRuVQUGoEEAf.jpeg"
                        alt="Caisses de bières et sodas">
                    <div class="gallery-overlay">
                        <h3>Bières et Sodas</h3>
                        <p>Toutes les marques populaires de bières et sodas, fraîches et disponibles en gros et détail
                        </p>
                        <div class="product-brands">
                            <span class="brand">Béninoise</span>
                            <span class="brand">Coca-Cola</span>
                            <span class="brand">Fanta</span>
                        </div>
                        <div class="product-price">À partir de 500 FCFA</div>
                    </div>
                </div>
                <div class="gallery-item" data-category="beverages">
                    <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/WhatsApp%20Image%202025-07-16%20at%207.07.43%20AM%20%281%29-iib34BZ7jw03tNcux6rdpnybcEwPbr.jpeg"
                        alt="Stock de boissons diverses">
                    <div class="gallery-overlay">
                        <h3>Boissons Diverses</h3>
                        <p>Jus de fruits, boissons énergisantes, thés glacés et autres rafraîchissements</p>
                        <div class="product-brands">
                            <span class="brand">Tampico</span>
                            <span class="brand">Red Bull</span>
                            <span class="brand">Lipton</span>
                        </div>
                        <div class="product-price">À partir de 300 FCFA</div>
                    </div>
                </div>
                <div class="gallery-item" data-category="all">
                    <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/WhatsApp%20Image%202025-07-16%20at%207.07.40%20AM-uBRukbqKQHM4lRHsRRU8kv42xg0E9S.jpeg"
                        alt="Étalage de boissons organisé">
                    <div class="gallery-overlay">
                        <h3>Vente au Détail</h3>
                        <p>Espace de vente organisé pour faciliter vos achats au détail avec conseil personnalisé</p>
                        <div class="product-features">
                            <span class="feature">Produits frais</span>
                            <span class="feature">Conseil expert</span>
                            <span class="feature">Promotions</span>
                        </div>
                    </div>
                </div>
                <div class="gallery-item" data-category="gas">
                    <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/WhatsApp%20Image%202025-07-16%20at%207.07.42%20AM-6sNjtAG3G1VI8OfO8kYHh0b3rpiZ3p.jpeg"
                        alt="Bouteilles de gaz domestique">
                    <div class="gallery-overlay">
                        <h3>Gaz Domestique</h3>
                        <p>Bouteilles de gaz de différentes tailles pour tous vos besoins domestiques et professionnels
                        </p>
                        <div class="product-sizes">
                            <span class="size">6kg</span>
                            <span class="size">12kg</span>
                            <span class="size">35kg</span>
                        </div>
                        <div class="product-price">À partir de 3 500 FCFA</div>
                    </div>
                </div>
                <div class="gallery-item special-offer" data-category="all">
                    <div class="special-content">
                        <i class="fas fa-gift"></i>
                        <h3>Offres Spéciales</h3>
                        <p>Découvrez nos promotions du moment et nos packs avantageux</p>
                        <div class="offer-list">
                            <div class="offer-item">Pack famille eau + sodas</div>
                            <div class="offer-item">Remise quantité gros</div>
                            <div class="offer-item">Fidélité récompensée</div>
                        </div>
                        <a href="#contact" class="offer-btn">Voir les offres</a>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- FAQ Section -->
    <section class="faq">
        <div class="container">
            <h2 class="section-title">Questions Fréquentes</h2>
            <p class="section-subtitle">Trouvez rapidement les réponses à vos questions</p>
            <div class="faq-grid">
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Délivrez vous des factures ? </h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Oui nous délivrons une facture normalisée pour chaque achat.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Proposez-vous des tarifs préférentiels pour les gros volumes ?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Oui, nous avons des tarifs dégressifs selon les quantités achetées. Contactez-nous pour
                            obtenir
                            un devis personnalisé adapté à vos besoins professionnels.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Comment fonctionne le système de consigne pour les bouteilles de gaz ?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Lors de votre premier achat, vous payez une consigne pour la bouteille. Ensuite, vous ne
                            payez
                            que le gaz lors des recharges. La consigne vous est remboursée si vous ne souhaitez plus
                            utiliser nos services.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Acceptez-vous les paiements par mobile money ?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Oui, nous acceptons tous les moyens de paiement : espèces, mobile money (MTN, Moov),
                            virements
                            bancaires et cartes bancaires pour les professionnels.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
        <div class="container">
            <h2 class="section-title">Contactez-Nous</h2>
            <p class="section-subtitle">Nous sommes là pour répondre à tous vos besoins</p>
            <div class="contact-content">
                <div class="contact-info">
                    <h3 style="margin-bottom: 2rem; color: #333;">Informations de Contact</h3>
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <strong>Adresse</strong><br>
                            Ayélawadjè, 1ère voie à droite en venant de Midombo<br>
                            Cotonou, Bénin
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <strong>Téléphone</strong><br>
                            +229 01 96 46 66 25<br>
                            <small>Disponible 7j/7 de 8h à 19h</small>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <strong>Email</strong><br>
                            contact@sager-market.com<br>
                            <small>Réponse sous 48h garantie</small>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong>Horaires d'ouverture</strong><br>
                            Lundi - Samedi: 08h00 - 22h00
                        </div>
                    </div>
                    <div class="contact-buttons">
                        <a href="https://wa.me/2290196466625" class="whatsapp-btn" target="_blank">
                            <i class="fab fa-whatsapp"></i>
                            WhatsApp
                        </a>
                        <a href="tel:+2290196466625" class="call-btn">
                            <i class="fas fa-phone"></i>
                            Appeler
                        </a>
                    </div>
                </div>


                <div class="contact-form-container">
                    <div class="map-container">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3965.1321266232458!2d2.4449721749917126!3d6.3769397936133405!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zNsKwMjInMzcuMCJOIDLCsDI2JzUxLjIiRQ!5e0!3m2!1sfr!2sbj!4v1762189056842!5m2!1sfr!2sbj"
                            width="600" height="750" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>

        </div>
    </section>
</main>

<!-- Footer -->
<footer class="footer">
    <div class="footer-content">
        <div class="footer-main">
            <div class="footer-section">
                <h3><i class="fas fa-store"></i> SAGER MARKET</h3>
                <p>Votre distributeur de confiance pour boissons, eaux, sodas, bières et gaz domestique</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="https://wa.me/2290196466625"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h4>Nos Services</h4>
                <ul>
                    <li><a href="#services">Livraison Express</a></li>
                    <li><a href="#services">Vente en Gros</a></li>
                    <li><a href="#services">Vente au Détail</a></li>
                    <li><a href="#services">Gaz Domestique</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Contact</h4>
                <div class="footer-contact">
                    <p><i class="fas fa-map-marker-alt"></i> Ayélawadjè, Cotonou</p>
                    <p><i class="fas fa-phone"></i> +229 01 96 46 66 25</p>
                    <p><i class="fas fa-envelope"></i> contact@sager-market.com</p>
                    <p><i class="fas fa-clock"></i> Lun-Sam: 8h-19h</p>
                </div>
            </div>
        </div>

        <nav>

            @auth
            @if (auth()->user() && auth()->user()->role == 'admin')
            <a href="{{ route('dashboardAdmin') }}" style="color: white; text-decoration: none;"><i
                    class="fas fa-tachometer-alt"></i>
                Tableau de
                bord |</a>
            @else
            <a href="{{ route('dashboard') }}" style="color: white; text-decoration: none;"><i
                    class="fas fa-tachometer-alt"></i>
                Tableau de
                bord |</a>
            @endif

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="logout-button"
                    style="background: none; border: none; color: inherit; cursor: pointer; font-weight: bold;
                                    font-size: 0.9rem; padding: 0;">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </button>
            </form>
            @endauth

            @guest
            <div class="">
                <a href="/login" style="color: white; text-decoration: none;"><i class="fas fa-sign-in-alt"></i>
                    Connexion</a>
            </div>
            @endguest
            <div class="footer-bottom">
                <p>&copy; 2025 SAGER MARKET. Tous droits réservés. <br>
                    Built with Blood, Sweat and Tears by
                    <a href="https://rachad-alabi-adekambi.github.io/portfolio/#/"
                        style="color:  #667eea; text-decoration: none; font-weight: bold;"><span>CC</span></a>
                </p>
                <div class="footer-links">
                </div>
            </div>
    </div>
</footer>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    @media screen and (max-width: 400px) {

        /* Turn all major grids into simple block stacks */
        .services-grid,
        .gallery-grid,
        .about-content,
        .contact-content,
        .newsletter-content,
        .features-grid,
        .testimonials-grid,
        .stats-grid,
        .footer-main,
        .form-row {
            display: block !important;
            display: none;
        }

        /* Ensure children fill the width and stack */
        .services-grid>*,
        .gallery-grid>*,
        .about-content>*,
        .contact-content>*,
        .newsletter-content>*,
        .features-grid>*,
        .testimonials-grid>*,
        .stats-grid>*,
        .footer-main>*,
        .form-row>* {
            width: 100% !important;
            max-width: 100% !important;
        }

        /* Hero: stack buttons vertically and make full width */
        .hero-buttons {
            display: flex !important;
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.75rem !important;
        }

        .hero-buttons .cta-button {
            width: 100% !important;
            justify-content: center !important;
        }

        /* Categories: compact and scrollable if overflow */
        .product-categories {
            justify-content: flex-start !important;
            overflow-x: auto !important;
            gap: 0.5rem !important;
            padding-bottom: 0.5rem !important;
            -webkit-overflow-scrolling: touch;
        }

        .category-btn {
            white-space: nowrap !important;
        }

        /* Gallery visuals on tiny screens */
        .gallery-item img {
            width: 100% !important;
            height: 220px !important;
            object-fit: cover !important;
        }

        .gallery-overlay {
            padding: 2rem 1rem 1rem !important;
        }

        /* Contact: stack action buttons */
        .contact-buttons {
            display: flex !important;
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.75rem !important;
        }

        .whatsapp-btn,
        .call-btn,
        .submit-btn {
            width: 100% !important;
        }

        /* Footer: stack sections and links */
        .footer-bottom {
            display: block !important;
            text-align: center !important;
            padding-top: 1.25rem !important;
        }

        .footer-links a {
            display: block !important;
            margin: 0.5rem 0 !important;
        }
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
        color: #333;
        overflow-x: hidden;
    }

    .gallery-item img {
        margin: 3px;
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
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
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
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .nav-menu {
        display: flex;
        list-style: none;
        gap: 2rem;
    }

    .nav-menu a {
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
        padding: 0.5rem 1rem;
        border-radius: 25px;
        position: relative;
    }

    .nav-menu a:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: translateY(-2px);
    }

    .phone-btn {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 25px;
        text-decoration: none;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .phone-btn:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }

    /* Hero Section */
    .hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 10rem 0 6rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .hero-background {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="rgba(255,255,255,0.05)" points="0,0 1000,300 1000,1000 0,700"/></svg>');
        background-size: cover;
    }

    .hero-content {
        max-width: 900px;
        margin: 0 auto;
        padding: 0 2rem;
        position: relative;
        z-index: 2;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255, 255, 255, 0.1);
        padding: 0.5rem 1rem;
        border-radius: 25px;
        margin-bottom: 2rem;
        animation: fadeInUp 1s ease-out;
    }

    .hero h1 {
        font-size: 3.5rem;
        margin-bottom: 1rem;
        animation: fadeInUp 1s ease-out 0.2s both;
        font-weight: 700;
    }

    .hero-subtitle {
        font-size: 1.4rem;
        margin-bottom: 1rem;
        animation: fadeInUp 1s ease-out 0.4s both;
        font-weight: 300;
    }

    .hero-description {
        font-size: 1.1rem;
        margin-bottom: 3rem;
        animation: fadeInUp 1s ease-out 0.6s both;
        opacity: 0.9;
    }

    .hero-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-bottom: 4rem;
        animation: fadeInUp 1s ease-out 0.8s both;
    }

    .cta-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 1rem 2rem;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        font-size: 1.1rem;
    }

    .cta-button.primary {
        background: white;
        color: #667eea;
    }

    .cta-button.secondary {
        background: transparent;
        color: white;
        border: 2px solid white;
    }

    .cta-button:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .hero-stats {
        display: flex;
        justify-content: center;
        gap: 3rem;
        animation: fadeInUp 1s ease-out 1s both;
    }

    .stat-item {
        text-align: center;
    }

    .stat-number {
        display: block;
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 0.9rem;
        opacity: 0.8;
    }

    /* Services Section */
    .services {
        padding: 6rem 0;
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
        margin-bottom: 1rem;
        color: #333;
        font-weight: 700;
    }

    .section-subtitle {
        text-align: center;
        font-size: 1.2rem;
        color: #666;
        margin-bottom: 4rem;
    }

    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
    }

    .service-card {
        background: white;
        padding: 2.5rem 2rem;
        border-radius: 20px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .service-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, #667eea, #764ba2);
    }

    .service-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .service-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: white;
        font-size: 2rem;
    }

    .service-card h3 {
        font-size: 1.5rem;
        margin-bottom: 1rem;
        color: #333;
    }

    .service-card p {
        color: #666;
        margin-bottom: 1.5rem;
        line-height: 1.6;
    }

    .service-features {
        list-style: none;
        text-align: left;
    }

    .service-features li {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        color: #555;
    }

    .service-features i {
        color: #667eea;
        font-size: 0.8rem;
    }

    /* Stats Section */
    .stats-section {
        padding: 4rem 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 2rem;
    }

    .stat-card {
        text-align: center;
        padding: 2rem 1rem;
    }

    .stat-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.8;
    }

    .stat-content .stat-number {
        font-size: 3rem;
        font-weight: bold;
        display: block;
        margin-bottom: 0.5rem;
    }

    .stat-content .stat-label {
        font-size: 1rem;
        opacity: 0.9;
    }

    /* About Section */
    .about {
        padding: 6rem 0;
        background: white;
    }

    .about-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        align-items: center;
        margin-bottom: 4rem;
    }

    .about-text {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #666;
    }

    .about-story,
    .about-mission,
    .about-values {
        margin-bottom: 2rem;
    }

    .about-text h3 {
        color: #333;
        margin-bottom: 1rem;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .about-text h3 i {
        color: #667eea;
    }

    .values-list {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-top: 1rem;
    }

    .value-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem;
        background: #f8f9fa;
        border-radius: 10px;
    }

    .value-item i {
        color: #667eea;
    }

    .about-image {
        position: relative;
        text-align: center;
    }

    .about-image img {
        max-width: 100%;
        border-radius: 20px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
    }

    .image-overlay {
        position: absolute;
        bottom: 20px;
        left: 20px;
        right: 20px;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 1rem;
        border-radius: 10px;
        backdrop-filter: blur(10px);
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-top: 4rem;
    }

    .feature-card {
        background: white;
        padding: 2.5rem 2rem;
        border-radius: 20px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: 1px solid #f0f0f0;
    }

    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .feature-card i {
        font-size: 3rem;
        color: #667eea;
        margin-bottom: 1.5rem;
    }

    .feature-card h3 {
        font-size: 1.5rem;
        margin-bottom: 1rem;
        color: #333;
    }

    .feature-card p {
        color: #666;
        margin-bottom: 1.5rem;
        line-height: 1.6;
    }

    .feature-details {
        display: flex;
        justify-content: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .detail-item {
        background: #f8f9fa;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        color: #555;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    .detail-item i {
        font-size: 0.8rem;
        color: #667eea;
    }

    /* Gallery Section */
    .gallery {
        padding: 6rem 0;
        background: #f8f9fa;
    }

    .product-categories {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-bottom: 3rem;
        flex-wrap: wrap;
    }

    .category-btn {
        background: white;
        border: 2px solid #e0e0e0;
        padding: 0.8rem 1.5rem;
        border-radius: 25px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .category-btn.active,
    .category-btn:hover {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 2rem;
        margin-top: 3rem;
    }

    .gallery-item {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        background: white;
    }

    .gallery-item:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
    }

    .gallery-item img {
        width: 100%;
        height: 280px;
        object-fit: cover;
    }

    .gallery-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0, 0, 0, 0.9));
        color: white;
        padding: 3rem 2rem 2rem;
        transform: translateY(100%);
        transition: transform 0.3s ease;
    }

    .gallery-item:hover .gallery-overlay {
        transform: translateY(0);
    }

    .gallery-overlay h3 {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .gallery-overlay p {
        margin-bottom: 1rem;
        opacity: 0.9;
    }

    .product-brands,
    .product-features,
    .product-sizes {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    .brand,
    .feature,
    .size {
        background: rgba(255, 255, 255, 0.2);
        padding: 0.3rem 0.8rem;
        border-radius: 15px;
        font-size: 0.8rem;
    }

    .product-price {
        font-weight: bold;
        font-size: 1.1rem;
        color: #ffd700;
    }

    .special-offer {
        background: linear-gradient(135deg, #667eea, #764ba2);
    }

    .special-content {
        padding: 3rem 2rem;
        text-align: center;
        color: white;
        height: 280px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .special-content i {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .offer-list {
        margin: 1rem 0;
    }

    .offer-item {
        background: rgba(255, 255, 255, 0.1);
        padding: 0.5rem 1rem;
        margin: 0.5rem 0;
        border-radius: 20px;
        font-size: 0.9rem;
    }

    .offer-btn {
        background: white;
        color: #667eea;
        padding: 0.8rem 1.5rem;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-block;
        margin-top: 1rem;
    }

    .offer-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 255, 255, 0.3);
    }

    /* Testimonials Section */
    .testimonials {
        padding: 6rem 0;
        background: white;
    }

    .testimonials-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
    }

    .testimonial-card {
        background: #f8f9fa;
        padding: 2rem;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .testimonial-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .testimonial-content {
        margin-bottom: 1.5rem;
    }

    .stars {
        color: #ffd700;
        margin-bottom: 1rem;
    }

    .testimonial-content p {
        font-style: italic;
        color: #555;
        line-height: 1.6;
    }

    .testimonial-author {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .author-avatar {
        width: 50px;
        height: 50px;
        background: #667eea;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .author-info h4 {
        margin-bottom: 0.2rem;
        color: #333;
    }

    .author-info span {
        color: #666;
        font-size: 0.9rem;
    }

    /* FAQ Section */
    .faq {
        padding: 6rem 0;
        background: #f8f9fa;
    }

    .faq-grid {
        max-width: 800px;
        margin: 0 auto;
    }

    .faq-item {
        background: white;
        margin-bottom: 1rem;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .faq-question {
        padding: 1.5rem 2rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s ease;
    }

    .faq-question:hover {
        background: #f8f9fa;
    }

    .faq-question h3 {
        font-size: 1.1rem;
        color: #333;
    }

    .faq-question i {
        color: #667eea;
        transition: transform 0.3s ease;
    }

    .faq-item.active .faq-question i {
        transform: rotate(180deg);
    }

    .faq-answer {
        padding: 0 2rem;
        max-height: 0;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .faq-item.active .faq-answer {
        padding: 0 2rem 1.5rem;
        max-height: 200px;
    }

    .faq-answer p {
        color: #666;
        line-height: 1.6;
    }

    /* Newsletter Section */
    .newsletter {
        padding: 4rem 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .newsletter-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
        align-items: center;
    }

    .newsletter-text h2 {
        font-size: 2rem;
        margin-bottom: 1rem;
    }

    .newsletter-form {
        max-width: 400px;
    }

    .form-group {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .newsletter-form input {
        flex: 1;
        padding: 1rem;
        border: none;
        border-radius: 25px;
        font-size: 1rem;
    }

    .newsletter-form button {
        background: white;
        color: #667eea;
        border: none;
        padding: 1rem 2rem;
        border-radius: 25px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .newsletter-form button:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 255, 255, 0.3);
    }

    .newsletter-privacy {
        font-size: 0.8rem;
        opacity: 0.8;
    }

    /* Contact Section */
    .contact {
        padding: 6rem 0;
        background: white;
    }

    .contact-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        margin-bottom: 3rem;
    }

    .contact-info {
        background: #f8f9fa;
        padding: 2.5rem;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .contact-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 2rem;
        padding: 1.5rem;
        background: white;
        border-radius: 15px;
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
        margin-top: 0.2rem;
    }

    .contact-item:hover i {
        color: white;
    }

    .contact-item strong {
        display: block;
        margin-bottom: 0.3rem;
    }

    .contact-item small {
        opacity: 0.8;
        font-size: 0.9rem;
    }

    .contact-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .whatsapp-btn,
    .call-btn {
        flex: 1;
        text-align: center;
        padding: 1rem 1.5rem;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .whatsapp-btn {
        background: #25D366;
        color: white;
    }

    .call-btn {
        background: #667eea;
        color: white;
    }

    .whatsapp-btn:hover,
    .call-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .contact-form-container {
        background: white;
        padding: 2.5rem;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .contact-form-container h3 {
        margin-bottom: 2rem;
        color: #333;
        font-size: 1.5rem;
    }

    .contact-form {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        margin-bottom: 0.5rem;
        color: #333;
        font-weight: 500;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        padding: 1rem;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .submit-btn {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: 25px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    .map-container {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        height: 400px;
        margin-top: 2rem;
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
        padding: 4rem 0 2rem;
    }

    .footer-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    .footer-main {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 3rem;
        margin-bottom: 3rem;
    }

    .footer-section h3 {
        margin-bottom: 1.5rem;
        font-size: 1.3rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .footer-section h4 {
        margin-bottom: 1rem;
        color: #667eea;
    }

    .footer-section p {
        line-height: 1.6;
        opacity: 0.9;
        margin-bottom: 1.5rem;
    }

    .footer-section ul {
        list-style: none;
    }

    .footer-section ul li {
        margin-bottom: 0.5rem;
    }

    .footer-section ul li a {
        color: #ccc;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .footer-section ul li a:hover {
        color: #667eea;
    }

    .social-links {
        display: flex;
        gap: 1rem;
    }

    .social-links a {
        width: 40px;
        height: 40px;
        background: #667eea;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .social-links a:hover {
        background: #764ba2;
        transform: translateY(-2px);
    }

    .footer-contact p {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.8rem;
        color: #ccc;
    }

    .footer-contact i {
        color: #667eea;
        width: 20px;
    }

    .footer-bottom {
        border-top: 1px solid #555;
        padding-top: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .footer-links {
        display: flex;
        gap: 2rem;
    }

    .footer-links a {
        color: #ccc;
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

    @keyframes countUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .nav-menu {
            display: none;
        }

        .hero h1 {
            font-size: 2.5rem;
        }

        .hero-subtitle {
            font-size: 1.2rem;
        }

        .hero-description {
            font-size: 1rem;
        }

        .hero-buttons {
            flex-direction: column;
            align-items: center;
        }

        .hero-stats {
            flex-direction: column;
            gap: 1.5rem;
        }

        .nav-container {
            padding: 0 1rem;
        }

        .about-content,
        .contact-content,
        .newsletter-content {
            grid-template-columns: 1fr;
        }

        .gallery-grid {
            grid-template-columns: 1fr;
        }

        .services-grid {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .features-grid {
            grid-template-columns: 1fr;
        }

        .testimonials-grid {
            grid-template-columns: 1fr;
        }

        .footer-main {
            grid-template-columns: 1fr;
            text-align: center;
        }

        .footer-bottom {
            flex-direction: column;
            text-align: center;
        }

        .footer-links {
            flex-direction: column;
            gap: 1rem;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .contact-buttons {
            flex-direction: column;
        }

        .values-list {
            grid-template-columns: 1fr;
        }

        .product-categories {
            justify-content: flex-start;
            overflow-x: auto;
            padding-bottom: 1rem;
        }

        .category-btn {
            white-space: nowrap;
        }
    }

    @media (max-width: 480px) {
        .hero {
            padding: 8rem 0 4rem;
        }

        .hero h1 {
            font-size: 2rem;
        }

        .section-title {
            font-size: 2rem;
        }

        .container {
            padding: 0 1rem;
        }

        .gallery-grid {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Smooth scrolling */
    html {
        scroll-behavior: smooth;
    }

    /* Loading animations */
    .gallery-item,
    .service-card,
    .feature-card,
    .testimonial-card {
        opacity: 0;
        animation: fadeInUp 0.6s ease-out forwards;
    }

    .gallery-item:nth-child(1) {
        animation-delay: 0.1s;
    }

    .gallery-item:nth-child(2) {
        animation-delay: 0.2s;
    }

    .gallery-item:nth-child(3) {
        animation-delay: 0.3s;
    }

    .gallery-item:nth-child(4) {
        animation-delay: 0.4s;
    }

    .gallery-item:nth-child(5) {
        animation-delay: 0.5s;
    }

    .gallery-item:nth-child(6) {
        animation-delay: 0.6s;
    }

    .service-card:nth-child(1) {
        animation-delay: 0.1s;
    }

    .service-card:nth-child(2) {
        animation-delay: 0.2s;
    }

    .service-card:nth-child(3) {
        animation-delay: 0.3s;
    }

    .service-card:nth-child(4) {
        animation-delay: 0.4s;
    }
</style>



<script>
    // FAQ Toggle
    document.querySelectorAll('.faq-question').forEach(question => {
        question.addEventListener('click', () => {
            const faqItem = question.parentElement;
            const isActive = faqItem.classList.contains('active');

            // Close all FAQ items
            document.querySelectorAll('.faq-item').forEach(item => {
                item.classList.remove('active');
            });

            // Open clicked item if it wasn't active
            if (!isActive) {
                faqItem.classList.add('active');
            }
        });
    });

    // Product Category Filter
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const category = btn.dataset.category;

            // Update active button
            document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            // Filter products
            document.querySelectorAll('.gallery-item').forEach(item => {
                if (category === 'all' || item.dataset.category.includes(category)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // Counter Animation
    function animateCounters() {
        const counters = document.querySelectorAll('.stat-number[data-target]');

        counters.forEach(counter => {
            const target = parseInt(counter.dataset.target);
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;

            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    counter.textContent = target;
                    clearInterval(timer);
                } else {
                    counter.textContent = Math.floor(current);
                }
            }, 16);
        });
    }

    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                if (entry.target.classList.contains('stats-section')) {
                    animateCounters();
                }
            }
        });
    }, observerOptions);

    // Observe stats section
    const statsSection = document.querySelector('.stats-section');
    if (statsSection) {
        observer.observe(statsSection);
    }

    // Smooth scroll for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Form submission
    document.querySelector('.contact-form').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Merci pour votre message ! Nous vous répondrons dans les plus brefs délais.');
    });

    document.querySelector('.newsletter-form').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Merci pour votre inscription à notre newsletter !');
    });
</script>