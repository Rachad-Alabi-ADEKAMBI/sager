@section('title', 'Tableau de bord administrateur')

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="{{ asset('fontawesome/css/all.css') }}">

@include('pages.back.admin.sidebar')

<!-- Main Content -->
<main class="main-content">
    <header class="header">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <button class="menu-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <h1>Gestion Sager</h1>
        </div>
        <div class="user-info">
            <span>Admin</span>
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
                        <div class="stat-value">{{ $products->count() }}</div>
                        <div class="stat-label">Produits en stock</div>
                    </div>
                    <div class="stat-icon products">
                        <a href="{{ route('products') }}" style="hover: font-weight: bold">
                            <i class="fas fa-boxes" style="color: white"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <div class="stat-value">
                            {{ $salesCountToday }}
                        </div>
                        <div class="stat-label">
                            Vente{{ $salesCountToday > 1 ? 's' : '' }} aujourd'hui
                        </div>
                    </div>
                    <div class="stat-icon sales">
                        <a href="{{ route('sales') }}" style="hover: font-weight: bold">
                            <i class="fas fa-shopping-cart" style="color: white"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div>
                        <div class="stat-value">
                            {{ number_format($salesAmountToday, 0, ',', ' ') }} FCFA
                        </div>
                        <div class="stat-label">Chiffre d'affaires</div>
                    </div>
                    <div class="stat-icon revenue">
                        <a href="{{ route('sales') }}" style="hover: font-weight: bold">
                            <i class="fas fa-wallet" style="color: white"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-section">
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Évolution des ventes</h3>
                    <select id="dateRange" style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px;">
                        <option value="7">7 derniers jours</option>
                        <option value="30">30 derniers jours</option>
                        <option value="90">3 derniers mois</option>
                    </select>
                </div>
                <canvas id="salesChart" style="width: 100%; height: 300px;"></canvas>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <h3>Activité récente</h3>
                </div>
                <ul class="activity-list">
                    @foreach ($notifications as $notification)
                        <li class="activity-item">
                            <div class="activity-icon sale">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="activity-content">
                                <div>{{ $notification->description }}</div>
                                <div class="activity-time">
                                    {{ $notification->created_at->format('H:i:s') }}
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>

            </div>
        </div>

        <!-- Inclure Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        @php
            // Préparer les données PHP: ventes groupées par date (format jj/mm)
            $dateLimit = now()->subDays(90);
            $salesFiltered = $sales->filter(fn($s) => $s->created_at >= $dateLimit);

            // Regrouper par date formatée
            $grouped = $salesFiltered->groupBy(fn($s) => $s->created_at->format('d/m'));

            // Construire tableau date => total ventes
            $salesData = [];
            foreach ($grouped as $date => $items) {
                $salesData[$date] = $items->sum('total');
            }

            // Trier par date (optionnel)
            ksort($salesData);

            // Rendre JSON utilisable en JS
            $salesJson = json_encode($salesData);
        @endphp

        <script>
            // Récupérer données PHP en JS
            const salesData = {!! $salesJson !!};

            // Fonction pour filtrer par nombre de jours
            function filterData(days) {
                const allDates = Object.keys(salesData);
                const allValues = Object.values(salesData);

                // Dates triées, on conserve les 'days' derniers
                const slicedDates = allDates.slice(-days);
                const slicedValues = allValues.slice(-days);

                return {
                    dates: slicedDates,
                    values: slicedValues
                };
            }

            const ctx = document.getElementById('salesChart').getContext('2d');

            let currentRange = 7; // par défaut 7 jours

            let chartInstance = null;

            function renderChart(days) {
                const filtered = filterData(days);

                if (chartInstance) {
                    chartInstance.destroy();
                }

                chartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: filtered.dates,
                        datasets: [{
                            label: 'Chiffre d\'affaires (FCFA)',
                            data: filtered.values,
                            borderColor: '#667eea',
                            backgroundColor: 'rgba(102, 126, 234, 0.3)',
                            fill: true,
                            tension: 0.3,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            borderWidth: 3,
                            animation: {
                                duration: 1000,
                                easing: 'easeOutQuart'
                            }
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return value.toLocaleString('fr-FR') + ' FCFA';
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                labels: {
                                    font: {
                                        size: 14
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.parsed.y.toLocaleString('fr-FR') + ' FCFA';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Initial render
            renderChart(currentRange);

            // Changement de filtre date
            document.getElementById('dateRange').addEventListener('change', e => {
                currentRange = parseInt(e.target.value, 10);
                renderChart(currentRange);
            });
        </script>

    </div>
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
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
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

    .stat-icon.products {
        background: #4CAF50;
    }

    .stat-icon.users {
        background: #2196F3;
    }

    .stat-icon.sales {
        background: #FF9800;
    }

    .stat-icon.revenue {
        background: #9C27B0;
    }

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
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
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

    .activity-icon.sale {
        background: #e8f5e8;
        color: #4CAF50;
    }

    .activity-icon.stock {
        background: #e3f2fd;
        color: #2196F3;
    }

    .activity-icon.user {
        background: #fff3e0;
        color: #FF9800;
    }

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


<style>
    #salesChart {
        max-width: 600px;
        width: 100%;
        height: 300px;
    }

    @media (max-width: 480px) {
        #salesChart {
            height: 200px;
            /* hauteur réduite pour mobile */
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
