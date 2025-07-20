@section('title', 'Vente - Tableau de Bord')


<link rel="stylesheet" href="{{ asset('fontawesome/css/all.css') }}">




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
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
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
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
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
        background: rgba(0, 0, 0, 0.5);
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
<!-- Main Content -->
<main class="main-content">
    <header class="header">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <button class="menu-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <h1>Nouvelle vente</h1>
        </div>
        <div class="user-info">
            <span>Vendeur</span>
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
                    <input type="text" class="form-control" id="customerName" placeholder="Nom complet du client"
                        required>
                </div>
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="tel" class="form-control" id="customerPhone" placeholder="Numéro de téléphone">
                </div>
                <div class="form-group">
                    <label>Vendeur</label>
                    <select class="form-control" id="salesperson" name="seller_email" required>
                        <!-- Options dynamiques ou statiques ici -->
                    </select>
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
                                <input type="number" class="form-control quantity-input" min="1" value="1"
                                    onchange="calculateLineTotal(this)">
                            </div>
                            <div class="form-group">
                                <label>Prix unitaire (FCFA)</label>
                                <input type="number" class="form-control unit-price" step="0.01" readonly>
                            </div>
                            <div class="form-group">
                                <label style="opacity: 0;">Action</label>
                                <button type="button" class="btn-remove" onclick="removeProductLine(this)"
                                    style="opacity: 0.3; cursor: not-allowed;" disabled>
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
            </div> <!-- Fermeture de sales-form -->

        </div> <!-- Fermeture de sales-content -->
    </div>
</main>


<script>
    let invoiceCounter = 4;
    let lineCounter = 1;

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('active');
    }

    function updatePriceOptions(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];

        const priceTypeSelect = selectElement.closest('.product-line').querySelector('.price-type');
        const unitPriceInput = selectElement.closest('.product-line').querySelector('.unit-price');
        const quantityInput = selectElement.closest('.product-line').querySelector('.quantity-input');

        // Reset options and inputs
        priceTypeSelect.innerHTML = '<option value="">Sélectionner le type</option>';
        unitPriceInput.value = '';
        quantityInput.max = '';

        if (selectedOption.value) {
            const prices = {
                detail: parseFloat(selectedOption.getAttribute('data-price-detail')),
                'demi-gros': parseFloat(selectedOption.getAttribute('data-price-semi-bulk')),
                gros: parseFloat(selectedOption.getAttribute('data-price-bulk')),
            };
            const stock = parseInt(selectedOption.getAttribute('data-quantity'));

            priceTypeSelect.innerHTML = `
                <option value="">Sélectionner le type</option>
                <option value="detail">Détail (${prices.detail.toLocaleString()} FCFA)</option>
                <option value="demi-gros">Demi-gros (${prices['demi-gros'].toLocaleString()} FCFA)</option>
                <option value="gros">Gros (${prices.gros.toLocaleString()} FCFA)</option>
            `;

            quantityInput.max = stock;
            if (quantityInput.value > stock) quantityInput.value = stock;
        }

        calculateLineTotal(selectElement);
    }

    function updateUnitPrice(selectElement) {
        const priceType = selectElement.value;
        const productSelect = selectElement.closest('.product-line').querySelector('.product-select');
        const unitPriceInput = selectElement.closest('.product-line').querySelector('.unit-price');

        if (productSelect.value && priceType) {
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            let price = 0;
            switch (priceType) {
                case 'detail':
                    price = parseFloat(selectedOption.getAttribute('data-price-detail'));
                    break;
                case 'demi-gros':
                    price = parseFloat(selectedOption.getAttribute('data-price-semi-bulk'));
                    break;
                case 'gros':
                    price = parseFloat(selectedOption.getAttribute('data-price-bulk'));
                    break;
            }
            unitPriceInput.value = price || '';
        } else {
            unitPriceInput.value = '';
        }

        calculateLineTotal(selectElement);
    }

    function calculateLineTotal(element) {
        const line = element.closest('.product-line');
        const productSelect = line.querySelector('.product-select');
        const quantityInput = line.querySelector('.quantity-input');

        if (productSelect.value) {
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const maxQty = parseInt(selectedOption.getAttribute('data-quantity')) || Infinity;

            if (quantityInput.value > maxQty) {
                alert(`Quantité maximale disponible : ${maxQty}`);
                quantityInput.value = maxQty;
            }
        }

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
                    @foreach ($products as $product)
                        <option 
                            value="{{ $product->id }}" 
                            data-price-detail="{{ $product->price_detail }}" 
                            data-price-semi-bulk="{{ $product->price_semi_bulk }}" 
                            data-price-bulk="{{ $product->price_bulk }}" 
                            data-quantity="{{ $product->quantity }}">
                            {{ $product->name }} (Stock: {{ $product->quantity }})
                        </option>
                    @endforeach
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
        lines.forEach((line) => {
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
        const customerPhone = document.getElementById('customerPhone').value.trim();
        const customerEmail = document.getElementById('customerEmail').value.trim();
        const salesperson = document.getElementById('salesperson').value;

        if (!customerName) {
            alert('Veuillez saisir le nom du client');
            return;
        }

        const lines = document.querySelectorAll('.product-line');
        let validLines = 0;
        let totalAmount = 0;
        let saleDetails = [];

        for (const line of lines) {
            const productSelect = line.querySelector('.product-select');
            const priceTypeSelect = line.querySelector('.price-type');
            const quantityInput = line.querySelector('.quantity-input');
            const unitPriceInput = line.querySelector('.unit-price');

            if (productSelect.value && priceTypeSelect.value && quantityInput.value && unitPriceInput.value) {
                validLines++;
                const quantity = parseInt(quantityInput.value);
                const unitPrice = parseFloat(unitPriceInput.value);
                totalAmount += quantity * unitPrice;

                saleDetails.push({
                    product_id: productSelect.value,
                    price_type: priceTypeSelect.value,
                    quantity: quantity,
                    unit_price: unitPrice
                });
            }
        }

        if (validLines === 0) {
            alert('Veuillez ajouter au moins un produit valide à la vente');
            return;
        }

        // TODO: envoyer les données au serveur via AJAX ou formulaire classique

        // Exemple d'affichage de confirmation
        const invoiceNumber = `INV-2024-${String(invoiceCounter).padStart(3, '0')}`;
        invoiceCounter++;

        alert(
            `Vente finalisée!\nFacture: #${invoiceNumber}\nClient: ${customerName}\nMontant: ${totalAmount.toLocaleString()} FCFA`
        );

        resetSale();
    }

    function resetSale() {
        document.getElementById('customerName').value = '';
        document.getElementById('customerPhone').value = '';
        document.getElementById('customerEmail').value = '';

        const container = document.getElementById('productLinesContainer');
        container.innerHTML = `
            <div class="product-line first-line">
                <div class="form-group">
                    <label>Produit</label>
                    <select class="form-control product-select" onchange="updatePriceOptions(this)">
                        <option value="">Sélectionner un produit</option>
                        @foreach ($products as $product)
                            <option 
                                value="{{ $product->id }}" 
                                data-price-detail="{{ $product->price_detail }}" 
                                data-price-semi-bulk="{{ $product->price_semi_bulk }}" 
                                data-price-bulk="{{ $product->price_bulk }}" 
                                data-quantity="{{ $product->quantity }}">
                                {{ $product->name }} (Stock: {{ $product->quantity }})
                            </option>
                        @endforeach
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
        updateRemoveButtons();
        updateCartSummary();
    }

    function viewInvoice(invoiceNumber) {
        // À implémenter
        alert(`Voir la facture ${invoiceNumber}`);
    }

    function printInvoice(invoiceNumber) {
        viewInvoice(invoiceNumber);
        setTimeout(() => window.print(), 500);
    }

    // Initialisation
    document.getElementById('customerName').addEventListener('input', updateCartSummary);
    updateRemoveButtons();
    updateCartSummary();
</script>
