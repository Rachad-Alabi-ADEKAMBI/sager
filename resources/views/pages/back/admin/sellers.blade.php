@extends('layouts.app')

@section('title', "Accueil")

@section('content')

 @include('pages.back.admin.sidebar')
  <!-- Main Content -->
    <main class="main-content">
        <header class="header">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <button class="menu-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>Gestion des Utilisateurs</h1>
            </div>
            <div class="user-info">
                <span>Administrateur</span>
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
            </div>
        </header>

        <div class="users-content">
            <div class="users-header">
                <h2>Équipe SAGER</h2>
                <button class="btn-primary" onclick="openModal()">
                    <i class="fas fa-user-plus"></i> Ajouter un utilisateur
                </button>
            </div>

            <!-- Users Grid -->
           <div class="users-grid">
                @foreach($users as $user)
                <div class="user-card">
                    <div class="user-status status-active">Actif</div>
                    <div class="user-card-header">
                        <div class="user-card-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="user-card-info">
                            <h3>{{ $user->name }}</h3>
                            <p>{{ $user->role ?? 'Rôle non défini' }}</p>
                        </div>
                    </div>
                    <div class="user-details">
                        <div class="user-detail-item">
                            <span class="user-detail-label">Email:</span>
                            <span class="user-detail-value">{{ $user->email }}</span>
                        </div>
                        <div class="user-detail-item">
                            <span class="user-detail-label">Dernière connexion:</span>
                            <span class="user-detail-value">{{ $user->last_login ?? 'Non disponible' }}</span>
                        </div>
                        <div class="user-detail-item">
                            <span class="user-detail-label">Opérations:</span>
                            <span class="user-detail-value">{{ $user->operations_count ?? '0' }} cette semaine</span>
                        </div>
                    </div>
                    <div class="user-actions">
                        <button class="btn-sm btn-view" onclick="viewUserActivity('{{ $user->name }}')">
                            <i class="fas fa-eye"></i> Voir activité
                        </button>
                        <button class="btn-sm btn-ban">
                            <i class="fas fa-ban"></i> Bannir
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Activity Section -->
            <div class="activity-section">
                <div class="activity-header">
                    <h3>Activité récente des utilisateurs</h3>
                    <select style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px;">
                        <option>Toutes les activités</option>
                        <option>Connexions</option>
                        <option>Ventes</option>
                        <option>Gestion stock</option>
                    </select>
                </div>
                <ul class="activity-list">
                    <li class="activity-item">
                        <div class="activity-icon login">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <div class="activity-content">
                            <div>
                                <span class="activity-user">Marie Dubois</span>
                                <span class="activity-action">s'est connectée au système</span>
                            </div>
                            <div class="activity-time">Il y a 5 minutes</div>
                        </div>
                    </li>
                    <li class="activity-item">
                        <div class="activity-icon sale">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="activity-content">
                            <div>
                                <span class="activity-user">Pierre Martin</span>
                                <span class="activity-action">a effectué une vente de 899€</span>
                            </div>
                            <div class="activity-time">Il y a 15 minutes</div>
                        </div>
                    </li>
                    <li class="activity-item">
                        <div class="activity-icon stock">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div class="activity-content">
                            <div>
                                <span class="activity-user">Marie Dubois</span>
                                <span class="activity-action">a mis à jour le stock - Smartphone Samsung</span>
                            </div>
                            <div class="activity-time">Il y a 1 heure</div>
                        </div>
                    </li>
                    <li class="activity-item">
                        <div class="activity-icon sale">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="activity-content">
                            <div>
                                <span class="activity-user">Sophie Laurent</span>
                                <span class="activity-action">a traité une commande client</span>
                            </div>
                            <div class="activity-time">Il y a 2 heures</div>
                        </div>
                    </li>
                    <li class="activity-item">
                        <div class="activity-icon login">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <div class="activity-content">
                            <div>
                                <span class="activity-user">Pierre Martin</span>
                                <span class="activity-action">s'est connecté au système</span>
                            </div>
                            <div class="activity-time">Il y a 3 heures</div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </main>

    <!-- Modal Ajouter Utilisateur -->
    <div id="userModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Ajouter un nouveau vendeur</h3>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Nom complet</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe temporaire</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <input type="hidden" name="role" value="seller">
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn-primary" style="flex: 1;">
                    <i class="fas fa-user-plus"></i> Ajouter l'utilisateur
                </button>
                <button type="button" onclick="closeModal()" style="flex: 1; background: #6c757d; color: white; border: none; padding: 0.75rem; border-radius: 10px; cursor: pointer;">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>




    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }

        function openModal() {
            document.getElementById('userModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('userModal').style.display = 'none';
        }

        function viewUserActivity(userName) {
            alert(`Affichage de l'activité de ${userName}`);
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('userModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
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


@endsection


 <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f6fa;
            color: #333;
        }

        /* Sidebar (same as previous pages) */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 2rem 1rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header h2 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .sidebar-menu {
            list-style: none;
            padding: 1rem 0;
        }

        .sidebar-menu li {
            margin: 0.5rem 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.1);
            border-left-color: white;
        }

        .sidebar-menu i {
            margin-right: 1rem;
            width: 20px;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        .header {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            color: #333;
            font-size: 1.8rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: #667eea;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        /* Users Content */
        .users-content {
            padding: 2rem;
        }

        .users-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        /* Users Grid */
        .users-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .user-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: relative;
        }

        .user-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }

        .user-card-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .user-card-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-right: 1rem;
        }

        .user-card-info h3 {
            margin-bottom: 0.25rem;
            color: #333;
        }

        .user-card-info p {
            color: #666;
            font-size: 0.9rem;
        }

        .user-status {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-banned {
            background: #f8d7da;
            color: #721c24;
        }

        .user-details {
            margin: 1rem 0;
        }

        .user-detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .user-detail-label {
            color: #666;
        }

        .user-detail-value {
            font-weight: 500;
        }

        .user-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            flex: 1;
        }

        .btn-view {
            background: #17a2b8;
            color: white;
        }

        .btn-ban {
            background: #dc3545;
            color: white;
        }

        .btn-unban {
            background: #28a745;
            color: white;
        }

        .btn-sm:hover {
            transform: translateY(-1px);
            opacity: 0.9;
        }

        /* Activity Section */
        .activity-section {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .activity-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .activity-list {
            list-style: none;
        }

        .activity-item {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 0.9rem;
        }

        .activity-icon.login { background: #e8f5e8; color: #4CAF50; }
        .activity-icon.sale { background: #e3f2fd; color: #2196F3; }
        .activity-icon.stock { background: #fff3e0; color: #FF9800; }

        .activity-content {
            flex: 1;
        }

        .activity-user {
            font-weight: 600;
            color: #333;
        }

        .activity-action {
            color: #666;
            font-size: 0.9rem;
        }

        .activity-time {
            font-size: 0.8rem;
            color: #999;
            margin-top: 0.25rem;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            animation: fadeIn 0.3s ease;
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 2rem;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            animation: slideIn 0.3s ease;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .close {
            font-size: 1.5rem;
            cursor: pointer;
            color: #999;
        }

        .close:hover {
            color: #333;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .users-header {
                flex-direction: column;
                align-items: stretch;
            }
            
            .users-grid {
                grid-template-columns: 1fr;
            }
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #333;
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }
        }
    </style>