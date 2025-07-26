@section('title', 'Ventes - Tableau de Bord')

<link rel="stylesheet" href="{{ asset('fontawesome/css/all.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">




@include('pages.back.admin.sidebar')

<!-- Main Content -->
<main class="main-content" id='app'>


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
        <tr v-for="(sale, index) in sales" <tr v-for="(sale, index) in sales" :key="sale.id">
            <td data-label="N° Facture">
                <strong>N° {{ sale.id }} /FR-N</strong>
            </td>
            <td data-label="Client">{{ sale.buyer_name }}</td>
            <td data-label="Vendeur">{{ sale.seller_name }}</td>
            <td data-label="Date">{{ formatDateTime(sale.created_at) }}</td>
            <td data-label="Montant">
                <strong>{{ formatAmount(sale.total) }} FCFA</strong>
            </td>
            <td data-label="Actions">
                <button class="invoice-btn" @click="showSaleDetails(sale.id)">
                    <i class="fas fa-eye"></i> Voir
                </button>
            </td>
        </tr>
    </tbody>
        </table>

                
                    <div class="alert alert-info text-center" style="margin: 20px;">
                        Aucun enregistrement de vente trouvé.

                    </div>
    </div>
</main>



<!-- Modal Détails Vente -->
<div class="modal fade" id="saleDetailModal" tabindex="-1" aria-labelledby="saleDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="saleDetailLabel">Détails de la vente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p><strong>Client :</strong> <span id="modalBuyerName"></span></p>
                <p><strong>Vendeur :</strong> <span id="modalSellerName"></span></p>
                <p><strong>Date :</strong> <span id="modalDate"></span></p>
                <p><strong>Total :</strong> <span id="modalTotal"></span> FCFA</p>
                <hr>
                <h6>Produits achetés :</h6>
                <ul id="modalProductList"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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
                sales: []

            };
        },
        mounted() {
            this.fetchSalesData();
        },
        methods: {
            fetchSalesData() {
                axios.get('/salesList')
                    .then(response => {
                        console.log(response.data);
                        this.sales = response.data;
                    })
                    .catch(error => {
                        console.error('Erreur lors de la récupération des données :', error);
                    });

              
            },

            submitForm() {
                if (!this.customer_name || this.customer_name.trim() === '') {
                    alert('Veuillez entrer le nom du client.');
                    return;
                }

                if (!this.customer_phone || this.customer_phone.trim() === '') {
                    alert('Veuillez entrer le téléphone du client.');
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

                if (!this.unitPrice || this.unitPrice <= 0) {
                    alert('Veuillez entrer un prix unitaire valide.');
                    return;
                }

                // Préparer les données dans le format attendu
                const saleData = {
                    seller_name: this.seller_name,
                    buyer_name: this.customer_name,
                    buyer_phone: this.customer_phone,
                    products: [{
                        product_id: this.selectedProductId,
                        quantity: this.quantity,
                        price: this.unitPrice
                    }]
                };

                console.log('Données à envoyer:', saleData);

                axios.post('http://127.0.0.1:8000/sale', saleData)
                    .then(response => {
                        console.log('Vente enregistrée avec succès:', response.data);
                        alert('Vente enregistrée avec succès !');

                        // Redirection vers la page /newInvoice avec l'ID de la vente
                        window.location.href = '/newInvoice?sale_id=' + response.data.sale_id;
                    })
                    .catch(error => {
                        console.error('Erreur lors de l\'enregistrement de la vente :', error);

                        let message = 'Une erreur est survenue lors de l\'enregistrement de la vente.';

                        if (error.response && error.response.data) {
                            const data = error.response.data;

                            if (data.error) {
                                message = data.error;
                            } else if (data.message) {
                                message = data.message;
                            }
                        }

                        alert(message);
                    });
            },
            formatDateTime(datetime) {
                const date = new Date(datetime);
                const options = {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                };
                return date.toLocaleString('fr-FR', options);
            },
            formatAmount(value) {
                return Number(value).toLocaleString('fr-FR');
            },
            viewInvoice(sale) {
                // Code pour voir la facture
                console.log('Voir facture:', sale.id);
            },
            printInvoice(sale) {
                // Code pour imprimer la facture
                console.log('Imprimer facture:', sale.id);
            }

        } // <--- Correctly closed methods object
    }).mount('#app');
</script>

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

<style>
    /* Table responsive : sur petits écrans */
    @media (max-width: 768px) {

        table.table,
        thead,
        tbody,
        th,
        td,
        tr {
            display: block;
            width: 100%;
        }

        thead tr {
            display: none;
            /* Masquer l'en-tête sur mobile */
        }

        tbody tr {
            margin-bottom: 1.5rem;
            border: 1px solid #ccc;
            padding: 15px 10px;
            border-radius: 8px;
            background-color: #fff;
            box-sizing: border-box;
        }

        tbody td {
            position: relative;
            padding-left: 50%;
            text-align: left;
            border: none;
            border-bottom: 1px solid #eee;
            box-sizing: border-box;
            min-height: 40px;
            vertical-align: top;
        }

        tbody td:last-child {
            border-bottom: 0;
        }

        tbody td::before {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            width: 45%;
            white-space: nowrap;
            font-weight: 600;
            content: attr(data-label);
            color: #333;
        }
    }
</style>

<style>
    @media (max-width: 768px) {

        table.table,
        thead,
        tbody,
        th,
        td,
        tr {
            display: block !important;
            /* forcer block */
            width: 100% !important;
        }

        thead tr {
            display: none !important;
        }

        tbody tr {
            margin-bottom: 1.5rem;
            border: 1px solid #ccc;
            padding: 15px 10px;
            border-radius: 8px;
            background-color: #fff;
            box-sizing: border-box;
        }

        tbody td {
            position: relative;
            padding-left: 50% !important;
            text-align: left !important;
            border: none !important;
            border-bottom: 1px solid #eee !important;
            box-sizing: border-box;
            min-height: 40px;
            vertical-align: top;
        }

        tbody td:last-child {
            border-bottom: 0 !important;
        }

        tbody td::before {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            width: 45%;
            white-space: nowrap;
            font-weight: 600;
            content: attr(data-label);
            color: #333;
        }

        /* Boutons en block et marge */
        tbody td button {
            display: inline-flex;
            margin-right: 10px;
            margin-bottom: 5px;
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

<script>
    const salesData = @json($sales); // Passe les ventes du backend vers JS

    function showSaleDetails(saleId) {
        const sale = salesData.find(s => s.id === saleId);
        if (!sale) return;

        document.getElementById('modalBuyerName').textContent = sale.buyer_name;
        document.getElementById('modalSellerName').textContent = sale.seller_name;
        document.getElementById('modalDate').textContent = new Date(sale.created_at).toLocaleString('fr-FR');
        document.getElementById('modalTotal').textContent = Number(sale.total).toLocaleString('fr-FR', {
            minimumFractionDigits: 0
        });

        const productList = document.getElementById('modalProductList');
        productList.innerHTML = ''; // reset

        sale.products.forEach(product => {
            const li = document.createElement('li');
            li.textContent =
                `${product.name} - Qté: ${product.pivot.quantity} - Prix: ${Number(product.pivot.price).toLocaleString('fr-FR')} FCFA`;
            productList.appendChild(li);
        });

        const modal = new bootstrap.Modal(document.getElementById('saleDetailModal'));
        modal.show();
    }
</script>


<script>
    function printInvoice(invoiceId) {
        const content = document.getElementById(invoiceId).innerHTML;
        const printWindow = window.open('', '', 'height=600,width=800');
        printWindow.document.write('<html><head><title>Facture</title>');

        // Inclure styles nécessaires, par exemple bootstrap/fontawesome si utilisés
        printWindow.document.write(`
        <style>
            body { font-family: Arial, sans-serif; font-size: 14px; padding: 20px; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            td, th { padding: 5px; border: 1px solid #ccc; }
            .right { text-align: right; }
            .total { font-weight: bold; }
            .footer { margin-top: 20px; font-size: 12px; }
        </style>
    `);

        printWindow.document.write('</head><body>');
        printWindow.document.write(content);
        printWindow.document.write('</body></html>');

        printWindow.document.close();
        printWindow.focus();

        printWindow.print();
        printWindow.close();
    }
</script>
