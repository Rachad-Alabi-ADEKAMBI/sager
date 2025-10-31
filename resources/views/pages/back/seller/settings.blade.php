@section('title', 'Paramètres du compte - SAGER')

<link rel="stylesheet" href="{{ asset('fontawesome/css/all.css') }}">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

@include('pages.back.seller.sidebar')

<!-- Main Content -->
<main class="main-content">
    <header class="header">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <button class="menu-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <h1>Paramètres</h1>
        </div>
        <div class="user-info">
            <span>{{ auth()->user()->name }}</span>
            <div class="user-avatar">
                <i class="fas fa-user"></i>
            </div>
        </div>
    </header>

    <section
        style="max-width: 500px; margin: 3rem auto; background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);">
        <h3 style="margin-bottom: 1.5rem;">Modifier les Paramètres du compte</h3>
        <form action="{{ route('settings.updatePassword') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="old_password">Ancien mot de passe</label>
                <input type="password" id="old_password" name="old_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="new_password">Nouveau mot de passe</label>
                <input type="password" id="new_password" name="new_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input type="password" id="password_confirmation" name="new_password_confirmation" class="form-control"
                    required>
            </div>
            <div style="display: flex; justify-content: center; margin-top: 2rem;">
                <button type="submit" class="btn-primary" style="min-width: 200px;">
                    <i class="fas fa-save"></i> Modifier le mot de passe
                </button>
            </div>
        </form>
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
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
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
        background: rgba(255, 255, 255, 0.1);
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
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        position: relative;
    }

    .user-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
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
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
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

    .activity-icon.login {
        background: #e8f5e8;
        color: #4CAF50;
    }

    .activity-icon.sale {
        background: #e3f2fd;
        color: #2196F3;
    }

    .activity-icon.stock {
        background: #fff3e0;
        color: #FF9800;
    }

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
        background: rgba(0, 0, 0, 0.5);
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
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes slideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
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