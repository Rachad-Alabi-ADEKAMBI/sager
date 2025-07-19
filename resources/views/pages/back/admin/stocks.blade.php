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
                                <span>Total: {{ $products->count() }}</span>
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
                @foreach($products as $product)
                <tr>
                    <td data-label="Nom"><strong>{{ $product->name }}</strong></td>
                    <td data-label="Catégorie">{{ $product->category ?? 'N/A' }}</td>
                    <td data-label="Prix">{{ number_format($product->price_detail, 2) }}€</td>
                    <td data-label="Quantité">{{ $product->quantity }}</td>
                    <td data-label="Statut">
                        @if($product->quantity > 0)
                            <span class="status-badge status-in-stock">En stock</span>
                        @else
                            <span class="status-badge status-out-stock">Rupture</span>
                        @endif
                    </td>
                    <td data-label="Dernière MAJ">{{ $product->updated_at->format('d/m/Y') }}</td>
                    <td data-label="Actions">
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
                @endforeach
            </tbody>


                            </table>
            </div>
        </div>
    </main>

 <!-- Modal Ajouter Produit -->
<div id="productModal" class="modal" style="max-width: 700px; margin: auto;">
  <div class="modal-content" style="padding: 2rem;">
    <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center;">
      <h3>Ajouter un nouveau produit</h3>
      <span class="close" onclick="closeModal()" style="cursor: pointer; font-size: 1.5rem;">&times;</span>
    </div>
    <form enctype="multipart/form-data" method="POST" action="/products">
      @csrf
      <div class="form-group" style="margin-bottom: 1rem;">
        <label>Nom du produit</label>
        <input name="name" type="text" class="form-control" required>
      </div>

      <div class="row" style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem;">
        <div class="form-group" style="flex: 1 1 45%;">
          <label>Prix d'achat</label>
          <input name="purchase_price" type="number" class="form-control" step="0.01" required>
        </div>

        <div class="form-group" style="flex: 1 1 45%;">
          <label>Quantité</label>
          <input name="quantity" type="number" class="form-control" required>
        </div>
      </div>

      <div class="row" style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem;">
        <div class="form-group" style="flex: 1 1 30%;">
          <label>Prix détail</label>
          <input name="price_detail" type="number" class="form-control" step="0.01" required>
        </div>

        <div class="form-group" style="flex: 1 1 30%;">
          <label>Prix semi gros</label>
          <input name="price_semi_bulk" type="number" class="form-control" step="0.01" required>
        </div>

        <div class="form-group" style="flex: 1 1 30%;">
          <label>Prix gros</label>
          <input name="price_bulk" type="number" class="form-control" step="0.01" required>
        </div>
      </div>

      <div class="form-group" style="margin-bottom: 1rem;">
        <label>Photo</label>
        <input name="photo" type="file" accept="image/*" class="form-control">
      </div>

      <div style="display: flex; gap: 1rem; margin-top: 2rem; flex-wrap: wrap;">
        <button type="submit" class="btn-primary" style="flex: 1 1 45%; min-width: 120px;">
          <i class="fas fa-save"></i> Enregistrer
        </button>
        <button type="button" onclick="closeModal()" style="flex: 1 1 45%; min-width: 120px; background: #6c757d; color: white; border: none; padding: 0.75rem; border-radius: 10px; cursor: pointer;">
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
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  display: none; /* caché par défaut */
  justify-content: center;
  align-items: center;
  background: rgba(0, 0, 0, 0.15); /* fond sombre léger */
  z-index: 1000;
}

.modal-content {
  background: #fff;
  border-radius: 10px;
  max-width: 700px;
  width: 90%;
  padding: 2rem;
  box-shadow: 0 4px 20px rgba(0,0,0,0.25); /* ombre plus marquée */
  position: relative;
  transition: transform 0.3s ease, opacity 0.3s ease;
  transform: translateY(0);
  opacity: 1;
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

    <style>
        .table-container {
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th, .table td {
    padding: 0.75rem;
    border: 1px solid #ddd;
    text-align: left;
}

@media (max-width: 600px) {
    .table thead {
        display: none;
    }
    .table, .table tbody, .table tr, .table td {
        display: block;
        width: 100%;
    }
    .table tr {
        margin-bottom: 1rem;
        border-bottom: 2px solid #ddd;
        padding-bottom: 1rem;
    }
    .table td {
        text-align: right;
        padding-left: 50%;
        position: relative;
        border: none;
        border-bottom: 1px solid #eee;
    }
    .table td::before {
        content: attr(data-label);
        position: absolute;
        left: 0;
        width: 50%;
        padding-left: 1rem;
        font-weight: bold;
        text-align: left;
    }
}

    </style>

    <style>
        /* Fond semi-transparent */
.modal {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  display: none; /* caché par défaut */
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background: #fff;
  border-radius: 10px;
  max-width: 700px;
  width: 90%;
  padding: 2rem;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  position: relative;
}


/* Header modal */
.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.modal-header h3 {
  margin: 0;
  font-weight: 600;
  font-size: 1.5rem;
  color: #333;
}

.modal-header .close {
  font-size: 1.8rem;
  cursor: pointer;
  color: #888;
  transition: color 0.3s ease;
}

.modal-header .close:hover {
  color: #333;
}

/* Form groups */
.form-group {
  display: flex;
  flex-direction: column;
  margin-bottom: 1rem;
}

.form-group label {
  margin-bottom: 0.4rem;
  font-weight: 500;
  color: #555;
}

.form-group input,
.form-group select,
.form-group textarea {
  padding: 0.5rem 0.75rem;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 1rem;
  transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #007bff;
}

/* Ligne de formulaire (row) */
.row {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
  margin-bottom: 1rem;
}

.row .form-group {
  flex: 1 1 30%;
  min-width: 150px;
}

/* Boutons */
button.btn-primary {
  background-color: #007bff;
  border: none;
  color: white;
  font-weight: 600;
  padding: 0.75rem 1rem;
  border-radius: 10px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

button.btn-primary:hover {
  background-color: #0056b3;
}

button[type="button"] {
  background: #6c757d;
  color: white;
  border: none;
  padding: 0.75rem 1rem;
  border-radius: 10px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

button[type="button"]:hover {
  background-color: #5a6268;
}

/* Responsive mobile : empilement vertical */
@media (max-width: 600px) {
  .row {
    flex-direction: column;
  }
  
  .row .form-group {
    flex: 1 1 100%;
  }
  
  button.btn-primary,
  button[type="button"] {
    flex: 1 1 100%;
    margin-bottom: 0.75rem;
  }
}

    </style>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }

      function openModal() {
    const modal = document.getElementById('productModal');
    modal.style.display = 'flex'; // affiché en flex pour centrage
}

function closeModal() {
    const modal = document.getElementById('productModal');
    modal.style.display = 'none'; // caché
}

// Fermer modal en cliquant à l’extérieur
window.onclick = function(event) {
    const modal = document.getElementById('productModal');
    if (event.target == modal) {
        closeModal();
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
</body>
