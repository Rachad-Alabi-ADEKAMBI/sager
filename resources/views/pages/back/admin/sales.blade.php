@extends('layouts.app')

@section('title', "Venntes - Tableau de Bord")



@section('content')


   @include('pages.back.admin.sidebar')

    <!-- Main Content -->
    <main class="main-content">
        <header class="header">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <button class="menu-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>Tableau de Bord</h1>
            </div>
            <div class="user-info">
                <span>Administrateur</span>
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
            </div>
        </header>

        <div class="dashboard-content">
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value">1,234</div>
                            <div class="stat-label">Produits en stock</div>
                        </div>
                        <div class="stat-icon products">
                            <i class="fas fa-boxes"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value">45</div>
                            <div class="stat-label">Utilisateurs actifs</div>
                        </div>
                        <div class="stat-icon users">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value">892</div>
                            <div class="stat-label">Ventes ce mois</div>
                        </div>
                        <div class="stat-icon sales">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value">€45,678</div>
                            <div class="stat-label">Chiffre d'affaires</div>
                        </div>
                        <div class="stat-icon revenue">
                            <i class="fas fa-euro-sign"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-section">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Évolution des ventes</h3>
                        <select style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px;">
                            <option>7 derniers jours</option>
                            <option>30 derniers jours</option>
                            <option>3 derniers mois</option>
                        </select>
                    </div>
                    <div class="chart-placeholder">
                        <i class="fas fa-chart-line" style="margin-right: 0.5rem;"></i>
                        Graphique des ventes
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Activité récente</h3>
                    </div>
                    <ul class="activity-list">
                        <li class="activity-item">
                            <div class="activity-icon sale">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="activity-content">
                                <div>Nouvelle vente - Ordinateur Portable</div>
                                <div class="activity-time">Il y a 5 minutes</div>
                            </div>
                        </li>
                        <li class="activity-item">
                            <div class="activity-icon stock">
                                <i class="fas fa-boxes"></i>
                            </div>
                            <div class="activity-content">
                                <div>Stock mis à jour - Smartphone</div>
                                <div class="activity-time">Il y a 15 minutes</div>
                            </div>
                        </li>
                        <li class="activity-item">
                            <div class="activity-icon user">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="activity-content">
                                <div>Nouvel utilisateur ajouté</div>
                                <div class="activity-time">Il y a 1 heure</div>
                            </div>
                        </li>
                        <li class="activity-item">
                            <div class="activity-icon sale">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="activity-content">
                                <div>Vente - Casque Audio</div>
                                <div class="activity-time">Il y a 2 heures</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </main>

        <!-- Invoice Modal -->
    <div id="invoiceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Facture</h3>
                <span class="close" onclick="closeInvoiceModal()">&times;</span>
            </div>
            <div class="invoice" id="invoiceContent">
                <div class="invoice-header">
                    <div class="company-info">
                        <h2>SAGER</h2>
                        <p>Distributeur de Boissons et Gaz</p>
                        <p>Quartier Commercial, Douala</p>
                        <p>Cameroun</p>
                        <p>Tél: +237 XXX XXX XXX</p>
                    </div>
                    <div class="invoice-info">
                        <div class="invoice-number">Facture #INV-2024-001</div>
                        <div class="invoice-date">Date: 15/01/2024</div>
                    </div>
                </div>

                <div class="invoice-details">
                    <div>
                        <h4>Facturé à:</h4>
                        <p><strong>Jean Dupont</strong></p>
                        <p>jean.dupont@email.com</p>
                        <p>+237 6 XX XX XX XX</p>
                    </div>
                    <div>
                        <h4>Vendeur:</h4>
                        <p><strong>Pierre Martin</strong></p>
                        <p>Responsable des ventes</p>
                    </div>
                </div>

                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Type</th>
                            <th>Quantité</th>
                            <th>Prix unitaire</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Pack d'eau (24 bouteilles)</td>
                            <td>Gros</td>
                            <td>10</td>
                            <td>2,500 FCFA</td>
                            <td>25,000 FCFA</td>
                        </tr>
                    </tbody>
                </table>

                <div class="invoice-total">
                    Total: 25,000 FCFA
                </div>

                <div style="text-align: center; margin-top: 2rem;">
                    <button class="print-btn" onclick="printInvoice()">
                        <i class="fas fa-print"></i> Imprimer cette facture
                    </button>
                    <button class="invoice-btn" onclick="downloadInvoice()" style="margin-left: 1rem;">
                        <i class="fas fa-download"></i> Télécharger PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
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

        /* Sidebar */
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

        /* Dashboard Content */
        .dashboard-content {
            padding: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .stat-icon.products { background: #4CAF50; }
        .stat-icon.users { background: #2196F3; }
        .stat-icon.sales { background: #FF9800; }
        .stat-icon.revenue { background: #9C27B0; }

        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }

        /* Charts Section */
        .charts-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-top: 2rem;
        }

        .chart-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .chart-placeholder {
            height: 300px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 1.2rem;
        }

        /* Recent Activity */
        .activity-list {
            list-style: none;
        }

        .activity-item {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
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

        .activity-icon.sale { background: #e8f5e8; color: #4CAF50; }
        .activity-icon.stock { background: #e3f2fd; color: #2196F3; }
        .activity-icon.user { background: #fff3e0; color: #FF9800; }

        .activity-content {
            flex: 1;
        }

        .activity-time {
            font-size: 0.8rem;
            color: #666;
        }

        /* Mobile Responsive */
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
            
            .charts-section {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Menu Toggle */
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



@section('scripts')
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

@endsection