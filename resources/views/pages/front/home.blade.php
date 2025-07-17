@extends('layouts.app')

@section('title', "Accueil")

@section('content')
<main class="main">
    <!-- Hero Section -->
    <section class="hero" id="hero">
        <div class="hero-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
            <div class="shape shape-4"></div>
        </div>

        <i class="fas fa-home floating-icon"></i>
        <i class="fas fa-building floating-icon"></i>
        <i class="fas fa-map-marker-alt floating-icon"></i>
        <i class="fas fa-key floating-icon"></i>

        <div class="hero-container">
            <h1 class="hero-title">
                Trouvez votre <span class="highlight">propriété idéale</span><br class="desktop-break">
                au Bénin
            </h1>
            <p class="hero-subtitle">
                Découvrez les meilleures opportunités immobilières au Bénin.
                Maisons, appartements, terrains - tout ce dont vous avez besoin pour votre projet immobilier.
            </p>

            <div class="search-form">
                <div class="form-group">
                    <label><i class="fas fa-home"></i> <span class="label-text">Catégorie</span></label>
                    <select class="form-control">
                        <option>Type de bien</option>
                        <option>🏠 Maison</option>
                        <option>🏢 Appartement</option>
                        <option>🌳 Terrain</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> <span class="label-text">Type</span></label>
                    <select class="form-control">
                        <option>Achat ou Location</option>
                        <option>💰 Achat</option>
                        <option>🏠 Location</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-map-marker-alt"></i> <span class="label-text">Zone</span></label>
                    <input type="text" class="form-control" placeholder="Ville ou quartier">
                </div>
                <div class="form-group search-btn-group">
                    <label>&nbsp;</label>
                    <button class="search-button">
                        <i class="fas fa-search"></i>
                        <span class="btn-text">Rechercher</span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="section about" id="about">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <h2>Votre partenaire immobilier de confiance en Afrique</h2>
                    <p>
                        ImmobilierBenin est la plateforme leader pour l'immobilier en Afrique de l'Ouest.
                        Nous connectons acheteurs, vendeurs et locataires avec les meilleures opportunités
                        immobilières de la région.
                    </p>
                    <div class="stats">
                        <div class="stat">
                            <span class="stat-number">500+</span>
                            <span class="stat-label">
                                <i class="fas fa-home"></i> Propriétés actives
                            </span>
                        </div>
                        <div class="stat">
                            <span class="stat-number">1000+</span>
                            <span class="stat-label">
                                <i class="fas fa-users"></i> Clients satisfaits
                            </span>
                        </div>
                    </div>
                    <a href="#featured" class="cta-button">
                        <i class="fas fa-arrow-right"></i> En savoir plus
                    </a>
                </div>
                <div class="about-image">
                    <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="À propos d'ImmobilierBenin">
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Properties -->
    <section class="section featured" id="featured">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-star"></i> Propriétés en vedette
                </h2>
                <p class="section-subtitle">
                    Découvrez notre sélection de propriétés exceptionnelles
                </p>
            </div>

            <div class="properties-grid">
                <div class="property-card" onclick="window.location.href='single-ad.html?id=1'">
                    <div class="property-image">
                        <img src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="Villa moderne">
                        <div class="property-badge">
                            <i class="fas fa-star"></i> Vedette
                        </div>
                        <div class="property-type">Vente</div>
                    </div>
                    <div class="property-content">
                        <h3 class="property-title">Villa moderne à Cotonou</h3>
                        <div class="property-location">
                            <i class="fas fa-map-marker-alt"></i>
                            Cotonou, Bénin
                        </div>
                        <div class="property-price">85,000,000 FCFA</div>
                        <div class="property-features">
                            <div class="feature">
                                <i class="fas fa-bed"></i> <span class="feature-text">4 ch.</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-bath"></i> <span class="feature-text">3 sdb</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-ruler-combined"></i> <span class="feature-text">250 m²</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="property-card" onclick="window.location.href='single-ad.html?id=2'">
                    <div class="property-image">
                        <img src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="Appartement de luxe">
                        <div class="property-badge">
                            <i class="fas fa-star"></i> Vedette
                        </div>
                        <div class="property-type">Location</div>
                    </div>
                    <div class="property-content">
                        <h3 class="property-title">Appartement de luxe</h3>
                        <div class="property-location">
                            <i class="fas fa-map-marker-alt"></i>
                            Porto-Novo, Bénin
                        </div>
                        <div class="property-price">450,000 FCFA/mois</div>
                        <div class="property-features">
                            <div class="feature">
                                <i class="fas fa-bed"></i> <span class="feature-text">3 ch.</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-bath"></i> <span class="feature-text">2 sdb</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-ruler-combined"></i> <span class="feature-text">120 m²</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="property-card" onclick="window.location.href='single-ad.html?id=3'">
                    <div class="property-image">
                        <img src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="Terrain constructible">
                        <div class="property-badge">
                            <i class="fas fa-star"></i> Vedette
                        </div>
                        <div class="property-type">Vente</div>
                    </div>
                    <div class="property-content">
                        <h3 class="property-title">Terrain constructible</h3>
                        <div class="property-location">
                            <i class="fas fa-map-marker-alt"></i>
                            Abomey-Calavi, Bénin
                        </div>
                        <div class="property-price">15,000,000 FCFA</div>
                        <div class="property-features">
                            <div class="feature">
                                <i class="fas fa-tree"></i> <span class="feature-text">Arboré</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-road"></i> <span class="feature-text">Accès route</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-ruler-combined"></i> <span class="feature-text">500 m²</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section-cta">
                <a href="ads.html" class="cta-button outline">
                    <i class="fas fa-eye"></i> Voir toutes les propriétés
                </a>
            </div>
        </div>
    </section>

    <!-- Random Ads -->
    <section class="section random-ads">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-home"></i> Autres opportunités
                </h2>
                <p class="section-subtitle">
                    Explorez plus de propriétés disponibles
                </p>
            </div>

            <div class="random-grid">
                <div class="random-card" onclick="window.location.href='single-ad.html?id=4'">
                    <div class="random-image">
                        <img src="https://images.unsplash.com/photo-1570129477492-45c003edd2be?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Maison familiale">
                        <div class="property-type">Vente</div>
                    </div>
                    <div class="random-content">
                        <h4 class="random-title">Maison familiale</h4>
                        <div class="property-location">
                            <i class="fas fa-map-marker-alt"></i>
                            Parakou, Bénin
                        </div>
                        <div class="random-price">35,000,000 FCFA</div>
                    </div>
                </div>

                <div class="random-card" onclick="window.location.href='single-ad.html?id=5'">
                    <div class="random-image">
                        <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Studio moderne">
                        <div class="property-type">Location</div>
                    </div>
                    <div class="random-content">
                        <h4 class="random-title">Studio moderne</h4>
                        <div class="property-location">
                            <i class="fas fa-map-marker-alt"></i>
                            Cotonou, Bénin
                        </div>
                        <div class="random-price">180,000 FCFA/mois</div>
                    </div>
                </div>

                <div class="random-card" onclick="window.location.href='single-ad.html?id=6'">
                    <div class="random-image">
                        <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Duplex spacieux">
                        <div class="property-type">Vente</div>
                    </div>
                    <div class="random-content">
                        <h4 class="random-title">Duplex spacieux</h4>
                        <div class="property-location">
                            <i class="fas fa-map-marker-alt"></i>
                            Bohicon, Bénin
                        </div>
                        <div class="random-price">65,000,000 FCFA</div>
                    </div>
                </div>

                <div class="random-card" onclick="window.location.href='single-ad.html?id=7'">
                    <div class="random-image">
                        <img src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Terrain commercial">
                        <div class="property-type">Vente</div>
                    </div>
                    <div class="random-content">
                        <h4 class="random-title">Terrain commercial</h4>
                        <div class="property-location">
                            <i class="fas fa-map-marker-alt"></i>
                            Ouidah, Bénin
                        </div>
                        <div class="random-price">25,000,000 FCFA</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="section contact" id="contact">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-envelope"></i> Contactez-nous
                </h2>
                <p class="section-subtitle">
                    Notre équipe est là pour vous accompagner dans votre projet immobilier
                </p>
            </div>

            <div class="contact-content">
                <div class="contact-form">
                    <h3><i class="fas fa-paper-plane"></i> Envoyez-nous un message</h3>
                    <p>Nous vous répondrons dans les plus brefs délais</p>

                    <form id="contactForm">
                        <div class="form-row">
                            <input type="text" class="form-control" placeholder="Prénom" required>
                            <input type="text" class="form-control" placeholder="Nom" required>
                        </div>
                        <input type="email" class="form-control" placeholder="Email" required>
                        <input type="tel" class="form-control" placeholder="Téléphone">
                        <select class="form-control">
                            <option>Sujet</option>
                            <option>💰 Achat de propriété</option>
                            <option>🏠 Vente de propriété</option>
                            <option>🏢 Location</option>
                            <option>❓ Autre</option>
                        </select>
                        <textarea class="form-control" rows="4" placeholder="Votre message..."></textarea>
                        <button type="submit" class="search-button full-width">
                            <i class="fas fa-paper-plane"></i>
                            <span class="btn-text">Envoyer le message</span>
                        </button>
                    </form>
                </div>

                <div class="contact-info">
                    <h3><i class="fas fa-info-circle"></i> Nos coordonnées</h3>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="info-content">
                            <strong>Téléphone</strong><br>
                            +229 XX XX XX XX
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-content">
                            <strong>Email</strong><br>
                            contact@immobilierbenin.com
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="info-content">
                            <strong>Adresse</strong><br>
                            123 Avenue de l'Indépendance<br>
                            Cotonou, Bénin
                        </div>
                    </div>

                    <div class="hours">
                        <h4><i class="fas fa-clock"></i> Horaires d'ouverture</h4>
                        <div class="hour-item">
                            <span>Lundi - Vendredi</span>
                            <span>8h00 - 18h00</span>
                        </div>
                        <div class="hour-item">
                            <span>Samedi</span>
                            <span>9h00 - 16h00</span>
                        </div>
                        <div class="hour-item">
                            <span>Dimanche</span>
                            <span>Fermé</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection