 <!-- Sidebar -->
 <aside class="sidebar" id="sidebar">
     <div class="sidebar-header">
         <h2>
             <a href="{{ route('home') }}" style='color: white; text-decoration: none;'>
                 SAGER
             </a>
         </h2>
     </div>
     <ul class="sidebar-menu">
         <li><a href="{{ route('dashboardAdmin') }}" class="active"><i class="fas fa-tachometer-alt"></i> Tableau de
                 bord</a></li>
         <li><a href="{{ route('sales') }}"><i class="fas fa-shopping-cart"></i> Ventes</a></li>
         <li><a href="{{ route('products') }}"><i class="fas fa-cubes"></i> Produits</a></li>
         <!--stocks-->

         <li><a href="{{ route('sellers') }}"><i class="fas fa-users"></i> Vendeurs</a></li>

         <li><a href="{{ route('home') }}"><i class="fas fa-home"></i> Accueil</a></li>
         <li><a href="{{ route('settingsAdmin') }}"><i class="fas fa-cog"></i> Paramètres du compte</a></li>

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
