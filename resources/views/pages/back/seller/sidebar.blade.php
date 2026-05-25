 <!-- Sidebar -->
 <aside class="sidebar" id="sidebar">
     <div class="sidebar-header">
         <h2>
             <a href="{{ route('home') }}" style='color: white; text-decoration: none;'>
                 SAGER <br> MARKET
             </a>
         </h2>
         <button type="button" class="sidebar-close" onclick="closeSidebar()" aria-label="Fermer le menu">
             <i class="fas fa-times"></i>
         </button>
     </div>
     <ul class="sidebar-menu">
         <li><a href="{{ route('dashboard') }}" class="active"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
         </li>
         <li class="seller-bottom-source"><a href="{{ route('sale') }}" class="{{ request()->routeIs('sale') ? 'active' : '' }}"><i class="fas fa-shopping-cart"></i> Vente</a></li>
         <li class="seller-bottom-source"><a href="{{ route('proforma') }}" class="{{ request()->routeIs('proforma') ? 'active' : '' }}"><i class="fas fa-file-invoice"></i> Proformas</a></li>

         <li class="seller-bottom-source"><a href="{{ route('settings') }}" class="{{ request()->routeIs('settings') ? 'active' : '' }}"><i class="fas fa-cog"></i> Paramètres du compte</a></li>
         <li><a href="{{ route('home') }}"><i class="fas fa-home"></i> Accueil</a></li>

         <li>
             <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                 <i class="fas fa-sign-out-alt"></i> Déconnexion
             </a>

             <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                 @csrf
             </form>
         </li>


     </ul>
 </aside>

 <nav class="seller-mobile-bottom-menu" aria-label="Navigation vendeur mobile">
     <a href="{{ route('proforma') }}" class="{{ request()->routeIs('proforma') ? 'active' : '' }}">
         <i class="fas fa-file-invoice"></i>
         <span>Proforma</span>
     </a>
     <a href="{{ route('sale') }}" class="{{ request()->routeIs('sale') ? 'active' : '' }}">
         <i class="fas fa-shopping-cart"></i>
         <span>Vente</span>
     </a>
     <a href="{{ route('settings') }}" class="{{ request()->routeIs('settings') ? 'active' : '' }}">
         <i class="fas fa-cog"></i>
         <span>Paramètres</span>
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
     .seller-mobile-bottom-menu,
     .sidebar-close {
         display: none;
     }

     .sidebar-header {
         position: relative;
     }

     .sidebar {
         z-index: 1500;
     }

     @media (max-width: 1024px) {
         .seller-bottom-source {
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
             padding-top: 68px !important;
             padding-bottom: 76px !important;
             overflow-x: hidden !important;
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

         .seller-mobile-bottom-menu {
             position: fixed;
             left: 0;
             right: 0;
             bottom: 0;
             z-index: 1200;
             display: grid;
             grid-template-columns: repeat(3, minmax(0, 1fr));
             height: 68px;
             padding: 6px 8px calc(6px + env(safe-area-inset-bottom));
             background: #ffffff;
             border-top: 1px solid #e7eaf0;
             box-shadow: 0 -8px 24px rgba(15, 23, 42, 0.12);
         }

         .seller-mobile-bottom-menu a {
             min-width: 0;
             display: flex;
             flex-direction: column;
             align-items: center;
             justify-content: center;
             gap: 4px;
             padding: 6px 4px;
             border-radius: 10px;
             color: #6b7280;
             text-decoration: none;
             font-size: 12px;
             font-weight: 700;
             line-height: 1.1;
             text-align: center;
             transition: background 0.2s ease, color 0.2s ease;
         }

         .seller-mobile-bottom-menu a i {
             font-size: 18px;
         }

         .seller-mobile-bottom-menu a.active,
         .seller-mobile-bottom-menu a:hover {
             background: #eef1ff;
             color: #667eea;
         }
     }

     @media (max-width: 768px) {
         .header-actions {
             flex-direction: row !important;
             flex-wrap: wrap !important;
             align-items: stretch !important;
             gap: 10px !important;
         }

         .header-actions > button,
         .header-actions > .btn,
         .header-actions > a.btn,
         .header-actions > .btn-primary,
         .header-actions > .btn-print,
         .header-actions > .btn-secondary,
         .btn-primary,
         .btn-secondary {
             width: calc(50% - 5px) !important;
             max-width: calc(50% - 5px) !important;
             flex: 0 0 calc(50% - 5px) !important;
             justify-content: center !important;
             white-space: nowrap;
         }

         .header-actions > input,
         .header-actions > select,
         .header-actions > .search-input,
         .header-actions > .filter-select {
             width: 100% !important;
             max-width: 100% !important;
             flex: 1 0 100% !important;
         }

         .sidebar {
             transform: translateX(-100%);
             transition: transform 0.3s ease;
             z-index: 1500 !important;
         }

         .sidebar.active {
             transform: translateX(0);
             z-index: 1500 !important;
         }

         .sidebar-header {
             padding-left: 8px;
             padding-right: 54px;
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
     }
 </style>
