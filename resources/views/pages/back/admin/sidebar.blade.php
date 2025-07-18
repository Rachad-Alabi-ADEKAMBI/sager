 <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>
                <a href="{{ route('home') }}">
                    SAGER
                </a>
            </h2>
            <p>Dashboard</p>
        </div>
        <ul class="sidebar-menu">
    <li><a href="{{ route('dashboardAdmin') }}" class="active"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
    <li><a href="{{ route('stocks') }}"><i class="fas fa-boxes"></i> Stock</a></li>
    <li><a href="{{ route('sellers') }}"><i class="fas fa-users"></i> Utilisateurs</a></li>
    <li><a href="{{ route('sales') }}"><i class="fas fa-shopping-cart"></i> Ventes</a></li>
    <li><a href="{{ route('home') }}"><i class="fas fa-home"></i> Accueil</a></li>
    <li><a href="{{ route('logout') }}"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
</ul>
    </aside>