@extends('layouts.app')

@section('title', "Gestion des Stocks - SAGER")



@section('content')

  @include('pages.back.admin.sidebar')

  <!-- Main Content -->
    <main class="main-content">
        <header class="header">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <button class="menu-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>Gestion du Stock</h1>
            </div>
            <div class="user-info">
                <span>Administrateur</span>
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
            </div>
        </header>

        <div class="stock-content">
            <div class="stock-header">
                <h2>Inventaire des Produits</h2>
                <button class="btn-primary" onclick="openModal()">
                    <i class="fas fa-plus"></i> Ajouter un produit
                </button>
            </div>

            <!-- Filters -->
            <div class="filters">
                <div class="filters-grid">
                    <div class="form-group">
                        <label>Rechercher</label>
                        <input type="text" class="form-control" placeholder="Nom du produit...">
                    </div>
                    <div class="form-group">
                        <label>Catégorie</label>
                        <select class="form-control">
                            <option>Toutes les catégories</option>
                            <option>Électronique</option>
                            <option>Informatique</option>
                            <option>Accessoires</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Statut</label>
                        <select class="form-control">
                            <option>Tous les statuts</option>
                            <option>En stock</option>
                            <option>Stock faible</option>
                            <option>Rupture de stock</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Trier par</label>
                        <select class="form-control">
                            <option>Nom (A-Z)</option>
                            <option>Nom (Z-A)</option>
                            <option>Prix (croissant)</option>
                            <option>Prix (décroissant)</option>
                            <option>Quantité</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Stock Table -->
            <div class="table-container">
                <div class="table-header">
                    <h3>Liste des Produits</h3>
                    <span>Total: 1,234 produits</span>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-sort"></i> Nom</th>
                            <th><i class="fas fa-sort"></i> Catégorie</th>
                            <th><i class="fas fa-sort"></i> Prix</th>
                            <th><i class="fas fa-sort"></i> Quantité</th>
                            <th><i class="fas fa-sort"></i> Statut</th>
                            <th><i class="fas fa-sort"></i> Dernière MAJ</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Ordinateur Portable HP</strong></td>
                            <td>Informatique</td>
                            <td>899€</td>
                            <td>25</td>
                            <td><span class="status-badge status-in-stock">En stock</span></td>
                            <td>15/01/2024</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-sm btn-edit" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-sm btn-delete" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Smartphone Samsung</strong></td>
                            <td>Électronique</td>
                            <td>599€</td>
                            <td>8</td>
                            <td><span class="status-badge status-low-stock">Stock faible</span></td>
                            <td>14/01/2024</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-sm btn-edit" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-sm btn-delete" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Casque Audio Sony</strong></td>
                            <td>Accessoires</td>
                            <td>199€</td>
                            <td>0</td>
                            <td><span class="status-badge status-out-of-stock">Rupture</span></td>
                            <td>13/01/2024</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-sm btn-edit" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-sm btn-delete" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Appareil Photo Canon</strong></td>
                            <td>Électronique</td>
                            <td>1299€</td>
                            <td>12</td>
                            <td><span class="status-badge status-in-stock">En stock</span></td>
                            <td>12/01/2024</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-sm btn-edit" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-sm btn-delete" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Tablette iPad</strong></td>
                            <td>Informatique</td>
                            <td>449€</td>
                            <td>18</td>
                            <td><span class="status-badge status-in-stock">En stock</span></td>
                            <td>11/01/2024</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-sm btn-edit" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-sm btn-delete" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Ajouter Produit -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Ajouter un nouveau produit</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form>
                <div class="form-group">
                    <label>Nom du produit</label>
                    <input type="text" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Catégorie</label>
                    <select class="form-control" required>
                        <option value="">Sélectionner une catégorie</option>
                        <option>Électronique</option>
                        <option>Informatique</option>
                        <option>Accessoires</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Prix (€)</label>
                    <input type="number" class="form-control" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Quantité</label>
                    <input type="number" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control" rows="3"></textarea>
                </div>
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn-primary" style="flex: 1;">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                    <button type="button" onclick="closeModal()" style="flex: 1; background: #6c757d; color: white; border: none; padding: 0.75rem; border-radius: 10px; cursor: pointer;">
                        Annuler
                    </button>
                </div>
            </form>
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

        /* Sidebar (same as dashboard) */
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

        /* Stock Content */
        .stock-content {
            padding: 2rem;
        }

        .stock-header {
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

        /* Filters */
        .filters {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }

        .form-control {
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

        /* Stock Table */
        .table-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .table-header {
            padding: 1.5rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .table th:hover {
            background: #e9ecef;
        }

        .table tbody tr {
            transition: background 0.3s ease;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-in-stock {
            background: #d4edda;
            color: #155724;
        }

        .status-low-stock {
            background: #fff3cd;
            color: #856404;
        }

        .status-out-of-stock {
            background: #f8d7da;
            color: #721c24;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-sm {
            padding: 0.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .btn-edit {
            background: #ffc107;
            color: white;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-sm:hover {
            transform: scale(1.1);
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
            
            .stock-header {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
            }
            
            .table-container {
                overflow-x: auto;
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
