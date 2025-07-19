@extends('layouts.app')

@section('title', "Vente - Tableau de Bord")

@section('content')


   @include('pages.back.seller.sidebar')

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

        /* Sales Content */
        .sales-content {
            padding: 2rem;
        }

        .sales-header {
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

        /* Sales Form */
        .sales-form {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .customer-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid #eee;
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

        /* Product Lines Section */
        .product-lines {
            margin-top: 2rem;
        }

        .product-line {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr auto;
            gap: 1rem;
            align-items: end;
            margin-bottom: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .product-line:hover {
            border-color: #667eea;
            background: #f0f4ff;
        }

        .product-line.first-line {
            background: white;
            border-color: #667eea;
        }

        .btn-remove {
            background: #dc3545;
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-remove:hover {
            background: #c82333;
            transform: scale(1.05);
        }

        .btn-add-line {
            background: #28a745;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .btn-add-line:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }

        /* Cart Section */
        .cart-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-top: 2rem;
        }

        .cart-summary {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 1rem;
            align-items: center;
            margin-bottom: 1rem;
        }

        .cart-total {
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea;
            text-align: right;
        }

        .cart-items-count {
            color: #666;
            font-size: 0.9rem;
        }

        /* Sales History */
        .sales-history {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .history-header {
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
        }

        .table tbody tr {
            transition: background 0.3s ease;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
        }

        .invoice-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            margin-right: 0.5rem;
            transition: all 0.3s ease;
        }

        .invoice-btn:hover {
            background: #218838;
            transform: translateY(-1px);
        }

        .print-btn {
            background: #17a2b8;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .print-btn:hover {
            background: #138496;
            transform: translateY(-1px);
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
            margin: 2% auto;
            padding: 0;
            border-radius: 15px;
            width: 90%;
            max-width: 800px;
            animation: slideIn 0.3s ease;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
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

        /* Invoice Styles */
        .invoice {
            padding: 2rem;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #667eea;
        }

        .company-info h2 {
            color: #667eea;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .invoice-info {
            text-align: right;
        }

        .invoice-number {
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
        }

        .invoice-date {
            color: #666;
            margin-top: 0.5rem;
        }

        .invoice-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }

        .invoice-table th,
        .invoice-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .invoice-table th {
            background: #f8f9fa;
            font-weight: 600;
        }

        .invoice-total {
            text-align: right;
            font-size: 1.3rem;
            font-weight: bold;
            color: #667eea;
            margin-top: 1rem;
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
            
            .sales-header {
                flex-direction: column;
                align-items: stretch;
            }
            
            .customer-info {
                grid-template-columns: 1fr;
            }

            .product-line {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }
            
            .invoice-details {
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

    <!-- Main Content -->
    <main class="main-content">
        <header class="header">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <button class="menu-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>Gestion des Ventes</h1>
            </div>
            <div class="user-info">
                <span>Administrateur</span>
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
            </div>
        </header>

        <div class="sales-content">
            <div class="sales-header">
                <h2>Nouvelle Vente</h2>
                <div>
                    <button class="btn-primary" onclick="resetSale()">
                        <i class="fas fa-refresh"></i> Nouvelle vente
                    </button>
                </div>
            </div>

            <!-- Sales Form -->
            <div class="sales-form">
                <h3 style="margin-bottom: 1.5rem; color: #333;">Informations de la vente</h3>
                
                <!-- Customer Information -->
                <div class="customer-info">
                    <div class="form-group">
                        <label>Nom du client *</label>
                        <input type="text" class="form-control" id="customerName" placeholder="Nom complet du client" required>
                    </div>
                    <div class="form-group">
                        <label>Téléphone</label>
                        <input type="tel" class="form-control" id="customerPhone" placeholder="Numéro de téléphone">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" id="customerEmail" placeholder="Adresse email">
                    </div>
                    <div class="form-group">
                        <label>Vendeur</label>
                        <select class="form-control" id="salesperson">
                            <option>Pierre Martin</option>
                            <option>Marie Dubois</option>
                            <option>Sophie Laurent</option>
                        </select>
                    </div>
                </div>

                <!-- Product Lines -->
                <div class="product-lines">
                    <h4 style="margin-bottom: 1rem; color: #333;">Produits à vendre</h4>
                    <div id="productLinesContainer">
                        <div class="product-line first-line">
                            <div class="form-group">
                                <label>Produit</label>
                                <select class="form-control product-select" onchange="updatePriceOptions(this)">
                                    <option value="">Sélectionner un produit</option>
                                    <option value="eau-pack">Pack d'eau (24 bouteilles)</option>
                                    <option value="coca-caisse">Caisse Coca-Cola (24 bouteilles)</option>
                                    <option value="biere-caisse">Caisse de bière (24 bouteilles)</option>
                                    <option value="gaz-6kg">Bouteille de gaz 6kg</option>
                                    <option value="gaz-12kg">Bouteille de gaz 12kg</option>
                                    <option value="sprite-caisse">Caisse Sprite (24 bouteilles)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Type de prix</label>
                                <select class="form-control price-type" onchange="updateUnitPrice(this)">
                                    <option value="">Sélectionner le type</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Quantité</label>
                                <input type="number" class="form-control quantity-input" min="1" value="1" onchange="calculateLineTotal(this)">
                            </div>
                            <div class="form-group">
                                <label>Prix unitaire (FCFA)</label>
                                <input type="number" class="form-control unit-price" step="0.01" readonly>
                            </div>
                            <div class="form-group">
                                <label style="opacity: 0;">Action</label>
                                <button type="button" class="btn-remove" onclick="removeProductLine(this)" style="opacity: 0.3; cursor: not-allowed;" disabled>
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-add-line" onclick="addProductLine()">
                        <i class="fas fa-plus"></i> Ajouter une ligne
                    </button>
                </div>

                <!-- Cart Section -->
                <div class="cart-section">
                    <h4 style="margin-bottom: 1rem; color: #333;">Résumé de la commande</h4>
                    <div class="cart-summary">
                        <div>
                            <div class="cart-items-count" id="itemsCount">0 article(s)</div>
                        </div>
                        <div class="cart-total" id="cartTotal">
                            Total: 0 FCFA
                        </div>
                    </div>
                    <div style="text-align: center; margin-top: 1.5rem;">
                        <button class="btn-primary" onclick="completeSale()" id="completeSaleBtn" disabled>
                            <i class="fas fa-check"></i> Finaliser la vente
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sales History -->
            <div class="sales-history">
                <div class="history-header">
                    <h3>Historique des ventes</h3>
                    <div>
                        <select style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px; margin-right: 1rem;">
                            <option>Aujourd'hui</option>
                            <option>Cette semaine</option>
                            <option>Ce mois</option>
                            <option>Toutes les ventes</option>
                        </select>
                    </div>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>N° Facture</th>
                            <th>Client</th>
                            <th>Vendeur</th>
                            <th>Date</th>
                            <th>Montant</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>#INV-2024-001</strong></td>
                            <td>Jean Dupont</td>
                            <td>Pierre Martin</td>
                            <td>15/01/2024 14:30</td>
                            <td><strong>25,000 FCFA</strong></td>
                            <td>
                                <button class="invoice-btn" onclick="viewInvoice('INV-2024-001')">
                                    <i class="fas fa-eye"></i> Voir
                                </button>
                                <button class="print-btn" onclick="printInvoice('INV-2024-001')">
                                    <i class="fas fa-print"></i> Imprimer
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>#INV-2024-002</strong></td>
                            <td>Marie Leblanc</td>
                            <td>Sophie Laurent</td>
                            <td>15/01/2024 11:15</td>
                            <td><strong>18,500 FCFA</strong></td>
                            <td>
                                <button class="invoice-btn" onclick="viewInvoice('INV-2024-002')">
                                    <i class="fas fa-eye"></i> Voir
                                </button>
                                <button class="print-btn" onclick="printInvoice('INV-2024-002')">
                                    <i class="fas fa-print"></i> Imprimer
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>#INV-2024-003</strong></td>
                            <td>Paul Durand</td>
                            <td>Pierre Martin</td>
                            <td>14/01/2024 16:45</td>
                            <td><strong>42,000 FCFA</strong></td>
                            <td>
                                <button class="invoice-btn" onclick="viewInvoice('INV-2024-003')">
                                    <i class="fas fa-eye"></i> Voir
                                </button>
                                <button class="print-btn" onclick="printInvoice('INV-2024-003')">
                                    <i class="fas fa-print"></i> Imprimer
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
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

     <script>
        let invoiceCounter = 4;
        let lineCounter = 1;

        // Product prices database
        const productPrices = {
            'eau-pack': {
                'detail': 3000,
                'demi-gros': 2700,
                'gros': 2500
            },
            'coca-caisse': {
                'detail': 8000,
                'demi-gros': 7500,
                'gros': 7000
            },
            'biere-caisse': {
                'detail': 12000,
                'demi-gros': 11000,
                'gros': 10000
            },
            'gaz-6kg': {
                'detail': 4500,
                'demi-gros': 4200,
                'gros': 4000
            },
            'gaz-12kg': {
                'detail': 8500,
                'demi-gros': 8000,
                'gros': 7500
            },
            'sprite-caisse': {
                'detail': 7500,
                'demi-gros': 7000,
                'gros': 6500
            }
        };

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }

        function updatePriceOptions(selectElement) {
            const productValue = selectElement.value;
            const priceTypeSelect = selectElement.closest('.product-line').querySelector('.price-type');
            const unitPriceInput = selectElement.closest('.product-line').querySelector('.unit-price');
            
            // Clear previous options
            priceTypeSelect.innerHTML = '<option value="">Sélectionner le type</option>';
            unitPriceInput.value = '';
            
            if (productValue && productPrices[productValue]) {
                const prices = productPrices[productValue];
                
                priceTypeSelect.innerHTML = `
                    <option value="">Sélectionner le type</option>
                    <option value="detail">Détail (${prices.detail} FCFA)</option>
                    <option value="demi-gros">Demi-gros (${prices['demi-gros']} FCFA)</option>
                    <option value="gros">Gros (${prices.gros} FCFA)</option>
                `;
            }
            
            calculateLineTotal(selectElement);
        }

        function updateUnitPrice(selectElement) {
            const priceType = selectElement.value;
            const productSelect = selectElement.closest('.product-line').querySelector('.product-select');
            const unitPriceInput = selectElement.closest('.product-line').querySelector('.unit-price');
            
            if (productSelect.value && priceType && productPrices[productSelect.value]) {
                unitPriceInput.value = productPrices[productSelect.value][priceType];
            } else {
                unitPriceInput.value = '';
            }
            
            calculateLineTotal(selectElement);
        }

        function calculateLineTotal(element) {
            updateCartSummary();
        }

        function addProductLine() {
            lineCounter++;
            const container = document.getElementById('productLinesContainer');
            const newLine = document.createElement('div');
            newLine.className = 'product-line';
            newLine.innerHTML = `
                <div class="form-group">
                    <label>Produit</label>
                    <select class="form-control product-select" onchange="updatePriceOptions(this)">
                        <option value="">Sélectionner un produit</option>
                        <option value="eau-pack">Pack d'eau (24 bouteilles)</option>
                        <option value="coca-caisse">Caisse Coca-Cola (24 bouteilles)</option>
                        <option value="biere-caisse">Caisse de bière (24 bouteilles)</option>
                        <option value="gaz-6kg">Bouteille de gaz 6kg</option>
                        <option value="gaz-12kg">Bouteille de gaz 12kg</option>
                        <option value="sprite-caisse">Caisse Sprite (24 bouteilles)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Type de prix</label>
                    <select class="form-control price-type" onchange="updateUnitPrice(this)">
                        <option value="">Sélectionner le type</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Quantité</label>
                    <input type="number" class="form-control quantity-input" min="1" value="1" onchange="calculateLineTotal(this)">
                </div>
                <div class="form-group">
                    <label>Prix unitaire (FCFA)</label>
                    <input type="number" class="form-control unit-price" step="0.01" readonly>
                </div>
                <div class="form-group">
                    <label style="opacity: 0;">Action</label>
                    <button type="button" class="btn-remove" onclick="removeProductLine(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            
            container.appendChild(newLine);
            updateRemoveButtons();
        }

        function removeProductLine(button) {
            const line = button.closest('.product-line');
            line.remove();
            updateRemoveButtons();
            updateCartSummary();
        }

        function updateRemoveButtons() {
            const lines = document.querySelectorAll('.product-line');
            lines.forEach((line, index) => {
                const removeBtn = line.querySelector('.btn-remove');
                if (lines.length === 1) {
                    removeBtn.style.opacity = '0.3';
                    removeBtn.style.cursor = 'not-allowed';
                    removeBtn.disabled = true;
                } else {
                    removeBtn.style.opacity = '1';
                    removeBtn.style.cursor = 'pointer';
                    removeBtn.disabled = false;
                }
            });
        }

        function updateCartSummary() {
            const lines = document.querySelectorAll('.product-line');
            let totalAmount = 0;
            let totalItems = 0;
            let hasValidItems = false;

            lines.forEach(line => {
                const productSelect = line.querySelector('.product-select');
                const priceTypeSelect = line.querySelector('.price-type');
                const quantityInput = line.querySelector('.quantity-input');
                const unitPriceInput = line.querySelector('.unit-price');

                if (productSelect.value && priceTypeSelect.value && quantityInput.value && unitPriceInput.value) {
                    const quantity = parseInt(quantityInput.value);
                    const unitPrice = parseFloat(unitPriceInput.value);
                    totalAmount += quantity * unitPrice;
                    totalItems += quantity;
                    hasValidItems = true;
                }
            });

            document.getElementById('itemsCount').textContent = `${totalItems} article(s)`;
            document.getElementById('cartTotal').textContent = `Total: ${totalAmount.toLocaleString()} FCFA`;
            
            const completeSaleBtn = document.getElementById('completeSaleBtn');
            completeSaleBtn.disabled = !hasValidItems || !document.getElementById('customerName').value.trim();
        }

        function completeSale() {
            const customerName = document.getElementById('customerName').value.trim();
            const salesperson = document.getElementById('salesperson').value;

            if (!customerName) {
                alert('Veuillez saisir le nom du client');
                return;
            }

            const lines = document.querySelectorAll('.product-line');
            let validLines = 0;
            let totalAmount = 0;

            lines.forEach(line => {
                const productSelect = line.querySelector('.product-select');
                const priceTypeSelect = line.querySelector('.price-type');
                const quantityInput = line.querySelector('.quantity-input');
                const unitPriceInput = line.querySelector('.unit-price');

                if (productSelect.value && priceTypeSelect.value && quantityInput.value && unitPriceInput.value) {
                    validLines++;
                    const quantity = parseInt(quantityInput.value);
                    const unitPrice = parseFloat(unitPriceInput.value);
                    totalAmount += quantity * unitPrice;
                }
            });

            if (validLines === 0) {
                alert('Veuillez ajouter au moins un produit valide à la vente');
                return;
            }

            // Generate invoice number
            const invoiceNumber = `INV-2024-${String(invoiceCounter).padStart(3, '0')}`;
            invoiceCounter++;

            alert(`Vente finalisée!\nFacture: #${invoiceNumber}\nClient: ${customerName}\nMontant: ${totalAmount.toLocaleString()} FCFA`);

            // Reset form
            resetSale();
        }

        function resetSale() {
            // Reset customer info
            document.getElementById('customerName').value = '';
            document.getElementById('customerPhone').value = '';
            document.getElementById('customerEmail').value = '';
            
            // Reset all product lines to just one empty line
            const container = document.getElementById('productLinesContainer');
            container.innerHTML = `
                <div class="product-line first-line">
                    <div class="form-group">
                        <label>Produit</label>
                        <select class="form-control product-select" onchange="updatePriceOptions(this)">
                            <option value="">Sélectionner un produit</option>
                            <option value="eau-pack">Pack d'eau (24 bouteilles)</option>
                            <option value="coca-caisse">Caisse Coca-Cola (24 bouteilles)</option>
                            <option value="biere-caisse">Caisse de bière (24 bouteilles)</option>
                            <option value="gaz-6kg">Bouteille de gaz 6kg</option>
                            <option value="gaz-12kg">Bouteille de gaz 12kg</option>
                            <option value="sprite-caisse">Caisse Sprite (24 bouteilles)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Type de prix</label>
                        <select class="form-control price-type" onchange="updateUnitPrice(this)">
                            <option value="">Sélectionner le type</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Quantité</label>
                        <input type="number" class="form-control quantity-input" min="1" value="1" onchange="calculateLineTotal(this)">
                    </div>
                    <div class="form-group">
                        <label>Prix unitaire (FCFA)</label>
                        <input type="number" class="form-control unit-price" step="0.01" readonly>
                    </div>
                    <div class="form-group">
                        <label style="opacity: 0;">Action</label>
                        <button type="button" class="btn-remove" onclick="removeProductLine(this)" style="opacity: 0.3; cursor: not-allowed;" disabled>
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            
            lineCounter = 1;
            updateCartSummary();
        }

        function viewInvoice(invoiceNumber) {
            document.getElementById('invoiceModal').style.display = 'block';
        }

        function closeInvoiceModal() {
            document.getElementById('invoiceModal').style.display = 'none';
        }

        function printInvoice(invoiceNumber) {
            if (invoiceNumber) {
                viewInvoice(invoiceNumber);
                setTimeout(() => {
                    window.print();
                }, 500);
            } else {
                window.print();
            }
        }

        function downloadInvoice() {
            alert('Téléchargement de la facture PDF...');
        }

        // Event listeners
        document.getElementById('customerName').addEventListener('input', updateCartSummary);

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('invoiceModal');
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

        // Initialize
        updateRemoveButtons();
        updateCartSummary();
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