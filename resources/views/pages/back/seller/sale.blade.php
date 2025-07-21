@section('title', 'Vente - Sager')


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
<main class="main-content" id="app">
    <header class="header">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <button class="menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
            <h1>Nouvelle vente
            </h1>


        </div>
        <div class="user-info">
            <span>@{{ seller_name }}</span>
            <div class="user-avatar">
                <i class="fas fa-user"></i>
            </div>
        </div>
    </header>

    <div class="sales-content">
        <div class="sales-header">
        </div>

        <!-- Sales Form -->
        <div class="sales-form">
            <h3 style="margin-bottom: 1.5rem; color: #333;">Informations de la vente</h3>


            <form @submit.prevent="submitForm">

                <!-- Customer Information -->
                <div class="customer-info">
                    <div class="form-group">
                        <label>Nom du client *</label>
                        <input type="text" class="form-control" id="customerName" v-model="customer_name"
                            placeholder="Nom complet du client" required>
                    </div>
                    <div class="form-group">
                        <label>Téléphone</label>
                        <input type="tel" class="form-control" v-model="customer_phone" id="customerPhone"
                            placeholder="Numéro de téléphone">
                    </div>
                </div>

                <!-- Product Lines -->
                <div class="product-lines">
                    <h4 style="margin-bottom: 1rem; color: #333;">Produits à vendre</h4>
                    <div id="productLinesContainer">
                        <div class="product-line first-line">


                            <div class="form-group">
                                <label>Produit</label>
                                <select v-model="selectedProductId" @change="onProductChange"
                                    class="form-control product-select">
                                    <option value="">Sélectionner un produit</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group" v-if="product">
                                <label>Type de prix</label>
                                <select class="form-control price-type" v-model="selectedPrice"
                                    @change="updateUnitPrice">
                                    <option value="">Sélectionner le type</option>
                                    <option :value="product.price_detail">Détail @{{ product.price_detail }}</option>
                                    <option :value="product.price_semi_bulk">Semi gros @{{ product.price_semi_bulk }}</option>
                                    <option :value="product.price_bulk">Gros @{{ product.price_bulk }}</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Quantité</label>
                                <input type="number" class="form-control quantity-input" min="1" value="1"
                                    style="width: 100px;" v-model="quantity" @input="onQuantityChange">
                            </div>


                            <div class="form-group">
                                <label>Prix unitaire</label>
                                <input type="number" class="form-control" style="width: 100px;" step="0.01"
                                    v-model="unitPrice" readonly>
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
                            Total: @{{ total }} FCFA
                        </div>
                    </div>
                    <div style="text-align: center; margin-top: 1.5rem;">
                        <button class="btn-primary" type='submit'>
                            <i class="fas fa-check"></i> Finaliser la vente
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/vue@3.4.21/dist/vue.global.prod.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>


<script>
    const {
        createApp
    } = Vue;

    createApp({
        data() {
            return {
                message: "Bonjour depuis Vue !",
                products: [],
                seller_name: "{{ auth()->user()->name }}",
                selectedProductId: '',
                product: [],
                unitPrice: '',
                quantity: '',
                total: '',
                customer_name: 'oklm',
                customer_phone: '65454'

            };
        },
        mounted() {
            this.fetchSalesData();
        },
        methods: {
            fetchSalesData() {
                axios.get('http://127.0.0.1:8000/productsList')
                    .then(response => {
                        console.log(response.data);
                        this.products = response.data;
                    })
                    .catch(error => {
                        console.error('Erreur lors de la récupération des données :', error);
                    });
            },
            onProductChange() {
                axios.get('http://127.0.0.1:8000/product/' + this.selectedProductId)
                    .then(response => {
                        this.product = response.data;
                        console.log('Produit sélectionné:', this.product);
                        // Mettre à jour le prix unitaire dans la ligne de produit
                    })
                    .catch(error => {
                        console.error('Erreur lors de la récupération du produit :', error);
                    });
            },
            updateUnitPrice() {
                this.unitPrice = parseFloat(this.selectedPrice) || 0;
            },
            onQuantityChange(event) {
                this.quantity = parseFloat(event.target.value) || 0;
                this.total = this.unitPrice * this.quantity;
            },

            submitForm() {
                if (!this.customer_name || this.customer_name.trim() === '') {
                    alert('Veuillez entrer le nom du client.');
                    return;
                }

                if (!this.quantity || this.quantity <= 0) {
                    alert('Veuillez entrer une quantité valide.');
                    return;
                }

                if (!this.selectedProductId) {
                    alert('Veuillez sélectionner un produit.');
                    return;
                }

                const saleData = {
                    customer_name: this.customer_name,
                    customer_phone: this.customer_phone,
                    product_id: this.selectedProductId,
                    quantity: this.quantity,
                    unit_price: this.unitPrice,
                    total: (this.unitPrice * this.quantity).toFixed(2)
                };

                console.log('Données à envoyer:', saleData); // 

                axios.post('http://127.0.0.1:8000/sale', saleData)
                    .then(response => {
                        console.log('Vente enregistrée avec succès:', response.data);
                        alert('Vente enregistrée avec succès !');
                        this.resetForm();
                    })
                    .catch(error => {
                        console.error('Erreur lors de l\'enregistrement de la vente :', error);
                        alert('Une erreur est survenue lors de l\'enregistrement de la vente.');
                    });

            }





        } // <--- Correctly closed methods object
    }).mount('#app');
</script>
