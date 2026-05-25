 <!-- Sidebar -->
 <aside class="sidebar" id="sidebar" style="height: 100vh; overflow-y: auto;">
     <div class="sidebar-header">
         <h2>
             <a href="{{ route('home') }}" style="color: white; text-decoration: none;">
                 SAGER <br> MARKET
             </a>
         </h2>
         <button type="button" class="sidebar-close" onclick="closeSidebar()" aria-label="Fermer le menu">
             <i class="fas fa-times"></i>
         </button>
     </div>

     <ul class="sidebar-menu">

         <li>
             <a href="{{ route('dashboardAdmin') }}" class="{{ request()->routeIs('dashboardAdmin') ? 'active' : '' }}">
                 <i class="fas fa-chart-line"></i> Tableau de bord
             </a>
         </li>

         <li class="responsive-bottom-source">
             <a href="{{ route('sales') }}" class="{{ request()->routeIs('sales') ? 'active' : '' }}">
                 <i class="fas fa-shopping-cart"></i> Ventes
             </a>
         </li>

         <li>
             <a href="{{ url('/proformas') }}" class="{{ request()->is('proformas') ? 'active' : '' }}">
                 <i class="fas fa-file-invoice"></i> Proformas
             </a>
         </li>


         <li class="responsive-bottom-source">
             <a href="{{ route('deposits') }}" class="{{ request()->routeIs('deposits') || request()->routeIs('deposits.*') ? 'active' : '' }}">
                 <i class="fas fa-warehouse"></i> Consignations
             </a>
         </li>

         <li class="responsive-bottom-source">
             <a href="{{ route('returnableProducts') }}" class="{{ request()->routeIs('returnableProducts') || request()->routeIs('returnableProducts.*') ? 'active' : '' }}">
                 <i class="fas fa-recycle"></i> Emballages
             </a>
         </li>


         <li class="responsive-bottom-source">
             <a href="{{ route('products') }}" class="{{ request()->routeIs('products') ? 'active' : '' }}">
                 <i class="fas fa-boxes"></i> Produits
             </a>
         </li>

         <li class="responsive-bottom-source">
             <a href="{{ route('rentability') }}" class="{{ request()->routeIs('rentability') ? 'active' : '' }}">
                 <i class="fas fa-chart-pie"></i> Rentabilité
             </a>
         </li>

         <li>
             <a href="{{ route('claims') }}" class="{{ request()->routeIs('claims') ? 'active' : '' }}">
                 <i class="fas fa-hand-holding-usd"></i> Créances
             </a>
         </li>

         <li>
             <a href="{{ route('expenses') }}" class="{{ request()->routeIs('expenses') ? 'active' : '' }}">
                 <i class="fas fa-money-bill-wave"></i> Dépenses
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

 <nav class="mobile-bottom-menu" aria-label="Navigation principale mobile">
     <a href="{{ route('deposits') }}" class="{{ request()->routeIs('deposits') || request()->routeIs('deposits.*') ? 'active' : '' }}">
         <i class="fas fa-warehouse"></i>
         <span>Consignations</span>
     </a>
     <a href="{{ route('returnableProducts') }}" class="{{ request()->routeIs('returnableProducts') || request()->routeIs('returnableProducts.*') ? 'active' : '' }}">
         <i class="fas fa-recycle"></i>
         <span>Emballages</span>
     </a>
     <a href="{{ route('sales') }}" class="{{ request()->routeIs('sales') ? 'active' : '' }}">
         <i class="fas fa-shopping-cart"></i>
         <span>Ventes</span>
     </a>
     <a href="{{ route('products') }}" class="{{ request()->routeIs('products') ? 'active' : '' }}">
         <i class="fas fa-boxes"></i>
         <span>Produits</span>
     </a>
     <a href="{{ route('rentability') }}" class="{{ request()->routeIs('rentability') ? 'active' : '' }}">
         <i class="fas fa-chart-pie"></i>
         <span>Rentabilite</span>
     </a>
 </nav>


 <script>
     function toggleSidebar() {
         const sidebar = document.getElementById('sidebar');
         sidebar.classList.toggle('active');
     }

     function closeSidebar() {
         const sidebar = document.getElementById('sidebar');
         sidebar.classList.remove('active');
     }

     // Close sidebar when clicking outside on mobile
     document.addEventListener('click', function(event) {
         const sidebar = document.getElementById('sidebar');
         const menuToggle = document.querySelector('.menu-toggle');

         if (window.innerWidth <= 768 &&
             !sidebar.contains(event.target) &&
             menuToggle &&
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
         z-index: 1500;
         color: white;
         transition: transform 0.3s ease;
     }

     .sidebar-header {
         position: relative;
     }

     .sidebar-close {
         display: none;
     }

     /* En mode mobile, sidebar cachée par défaut */
     .mobile-bottom-menu {
         display: none;
     }

     @media (max-width: 1024px) {
         .responsive-bottom-source {
             display: none;
         }

         body {
             padding-bottom: 76px;
             overflow-x: hidden;
         }

         .main-content,
         .content {
             width: 100% !important;
             max-width: 100vw !important;
             margin-left: 0 !important;
             padding-top: 86px !important;
             padding-bottom: 76px !important;
             overflow-x: hidden !important;
         }

         .main-content > .header ~ *,
         .content > .header ~ * {
             margin-top: 12px !important;
         }

         .main-content .statistics-section,
         .main-content .stats-grid,
         .main-content .dashboard-content,
         .content .statistics-section,
         .content .stats-grid,
         .content .dashboard-content {
             margin-top: 12px !important;
         }

         .main-content > .header,
         .content > .header {
             position: fixed !important;
             top: 0 !important;
             left: 0 !important;
             right: 0 !important;
             z-index: 1300 !important;
             min-height: 68px !important;
             background: #ffffff !important;
             box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08) !important;
             isolation: isolate;
         }

         .mobile-bottom-menu {
             position: fixed;
             left: 0;
             right: 0;
             bottom: 0;
             width: 100%;
             max-width: 100vw;
             z-index: 1200;
             display: grid;
             grid-template-columns: repeat(5, minmax(0, 1fr));
             height: 68px;
             padding: 6px 6px calc(6px + env(safe-area-inset-bottom));
             background: #ffffff;
             border-top: 1px solid #e7eaf0;
             box-shadow: 0 -8px 24px rgba(15, 23, 42, 0.12);
         }

         .mobile-bottom-menu a {
             min-width: 0;
             display: flex;
             flex-direction: column;
             align-items: center;
             justify-content: center;
             gap: 4px;
             padding: 6px 2px;
             border-radius: 10px;
             color: #6b7280;
             text-decoration: none;
             font-size: clamp(9px, 2.35vw, 11px);
             font-weight: 700;
             line-height: 1.1;
             text-align: center;
             transition: background 0.2s ease, color 0.2s ease;
         }

         .mobile-bottom-menu a i {
             font-size: clamp(15px, 4vw, 18px);
         }

         .mobile-bottom-menu a span {
             max-width: 100%;
             overflow: hidden;
             text-overflow: ellipsis;
             white-space: nowrap;
         }

         .mobile-bottom-menu a.active,
         .mobile-bottom-menu a:hover {
             background: #eef1ff;
             color: #667eea;
         }
     }

     @media (max-width: 768px) {
         .sidebar {
             transform: translateX(-100%);
             z-index: 1500 !important;
         }

         .sidebar.active {
             transform: translateX(0);
             z-index: 1500 !important;
         }

         .sidebar-close {
             position: absolute;
             top: 50%;
             right: 12px;
             width: 34px;
             height: 34px;
             display: inline-flex;
             align-items: center;
             justify-content: center;
             transform: translateY(-50%);
             border: 1px solid rgba(255, 255, 255, 0.24);
             border-radius: 50%;
             background: rgba(255, 255, 255, 0.14);
             color: #ffffff;
             cursor: pointer;
         }

         .sidebar-close:hover {
             background: rgba(255, 255, 255, 0.22);
         }

         .sidebar-header {
             padding-left: 8px;
             padding-right: 54px;
         }

         .sidebar-header h2 {
             margin-left: 0;
             font-size: 1.35rem;
         }
     }
 </style>
