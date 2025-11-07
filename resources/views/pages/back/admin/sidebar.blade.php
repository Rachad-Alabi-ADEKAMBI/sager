 <!-- Sidebar -->
 <aside class="sidebar" id="sidebar" style="height: 100vh; overflow-y: auto;">
     <div class="sidebar-header">
         <h2>
             <a href="{{ route('home') }}" style='color: white; text-decoration: none;'>
                 SAGER MARKET
             </a>
         </h2>
     </div>
     <ul class="sidebar-menu">
         <li>
             <a href="{{ route('dashboardAdmin') }}" class="{{ request()->routeIs('dashboardAdmin') ? 'active' : '' }}">
                 <i class="fas fa-tachometer-alt"></i> Tableau de bord
             </a>
         </li>
         <li>
             <a href="{{ route('sales') }}" class="{{ request()->routeIs('sales') ? 'active' : '' }}">
                 <i class="fas fa-shopping-cart"></i> Ventes
             </a>
         </li>

         <li>
             <a href="{{ route('deposits') }}" class="{{ request()->routeIs('deposits.*') ? 'active' : '' }}">
                 <i class="fas fa-box"></i> Consignations
             </a>
         </li>


         <li>
             <a href="{{ url('/proformas') }}" class="{{ request()->is('proformas') ? 'active' : '' }}">
                 <i class="fas fa-file-invoice"></i> Factures Proformas
             </a>
         </li>
         <li>
             <a href="{{ route('products') }}" class="{{ request()->routeIs('products') ? 'active' : '' }}">
                 <i class="fas fa-cubes"></i> Produits
             </a>
         </li>
         <li>
             <a href="{{ route('rentability') }}" class="{{ request()->routeIs('rentability') ? 'active' : '' }}">
                 <i class="fas fa-money-bill"></i> Rentabilité
             </a>
         </li>

         <li>
             <a href="{{ route('claims') }}" class="{{ request()->routeIs('claims') ? 'active' : '' }}">
                 <i class="fas fa-briefcase"></i> Créances
             </a>
         </li>


         <li>
             <a href="{{ route('sellers') }}" class="{{ request()->routeIs('sellers') ? 'active' : '' }}">
                 <i class="fas fa-users"></i> Vendeurs
             </a>
         </li>

         <li>
             <a href="{{ route('settingsAdmin') }}" class="{{ request()->routeIs('settingsAdmin') ? 'active' : '' }}">
                 <i class="fas fa-cog"></i> Paramètres du compte
             </a>
         </li>

         <li style="margin-top: -10px;">
             <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                 <i class="fas fa-sign-out-alt"></i> Déconnexion
             </a>
             <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                 @csrf
             </form>
         </li>
     </ul>

 </aside>

 <script>
     function toggleSidebar() {
         const sidebar = document.getElementById('sidebar');
         sidebar.classList.toggle('active');
     }

     // Close sidebar when clicking outside on mobile
     document.addEventListener('click', function(event) {
         const sidebar = document.getElementById('sidebar');
         const menuToggle = document.querySelector('.menu-toggle');

         if (window.innerWidth <= 768 &&
             !sidebar.contains(event.target) &&
             !menuToggle.contains(event.target)) {
             sidebar.classList.remove('active');
         }
     });
 </script>

 <style>
     /* Par défaut sur desktop */
     .sidebar {
         width: 250px;
         position: fixed;
         left: 0;
         top: 0;
         bottom: 0;
         color: white;
         transition: transform 0.3s ease;
     }

     /* En mode mobile, sidebar cachée par défaut */
     @media (max-width: 768px) {
         .sidebar {
             transform: translateX(-100%);
         }

         .sidebar.active {
             transform: translateX(0);
         }
     }
 </style>