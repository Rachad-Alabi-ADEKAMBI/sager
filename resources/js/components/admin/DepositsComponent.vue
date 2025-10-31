<template>
    <div>
        <div class="sales-content">
            <!-- Main deposits list view -->
            <div class="sales-history" v-if="!showHistoryView">
                <!-- Added header with buttons at the top -->
                <div class="page-header">
                    <h2>Historique des Consignations</h2>
                    <div class="header-actions">
                        <button
                            @click="showAddStockModal = true"
                            class="btn-add-stock"
                        >
                            <i class="fas fa-plus"></i>
                            Ajouter du Stock
                        </button>
                        <button @click="printAllDeposits" class="btn-print-all">
                            <i class="fas fa-print"></i>
                            Imprimer
                        </button>
                    </div>
                </div>

                <!-- Added table container with blue gradient header -->
                <div class="table-container">
                    <div class="table-header">
                        <h3>Liste des Produits en Stock</h3>
                        <div class="header-info">
                            <strong>
                                Total: {{ deposits.length }} produits
                            </strong>
                        </div>
                    </div>

                    <!-- Deposits Table -->
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Quantité</th>
                                <th>Dernière MAJ</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="deposit in paginatedDeposits"
                                :key="deposit.id"
                            >
                                <td data-label="Produit">
                                    {{ deposit.product_name }}
                                </td>
                                <td data-label="Quantité">
                                    {{ formatQuantity(deposit.quantity) }}
                                </td>
                                <td data-label="Dernière MAJ">
                                    {{ formatDateTime(deposit.updated_at) }}
                                </td>
                                <td data-label="Actions">
                                    <button
                                        @click="
                                            viewProductHistory(
                                                deposit.product_id,
                                                deposit.product_name
                                            )
                                        "
                                        class="action-btn history-btn"
                                        title="Voir l'historique"
                                    >
                                        <i
                                            class="fas fa-clock"
                                            style="color: white"
                                        ></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Empty State -->
                    <div v-if="deposits.length === 0" class="empty-state">
                        <i class="fas fa-box-open"></i>
                        <p>Aucun dépôt trouvé</p>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="totalPages > 1" class="pagination">
                    <button
                        @click="goToPage(currentPage - 1)"
                        :disabled="currentPage === 1"
                        class="page-btn"
                    >
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span class="page-info">
                        Page {{ currentPage }} sur {{ totalPages }}
                    </span>
                    <button
                        @click="goToPage(currentPage + 1)"
                        :disabled="currentPage === totalPages"
                        class="page-btn"
                    >
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>

            <!-- History view for specific product -->
            <div class="showStock" v-if="showHistoryView">
                <div class="table-container">
                    <div class="table-header">
                        <h3
                            style="
                                display: flex;
                                align-items: center;
                                gap: 0.5rem;
                            "
                        >
                            <span
                                @click="closeHistoryView()"
                                style="
                                    display: flex;
                                    align-items: center;
                                    cursor: pointer;
                                    color: white;
                                    font-weight: bold;
                                "
                            >
                                <i
                                    class="fas fa-arrow-left"
                                    style="color: white"
                                ></i>
                            </span>
                            |
                            <strong>{{ selectedProductName }}</strong>
                        </h3>
                        <div
                            style="
                                display: flex;
                                gap: 10px;
                                align-items: center;
                            "
                        >
                            <strong>Total: {{ productHistory.length }}</strong>
                            <button
                                @click="printProductHistory"
                                class="btn-print-history"
                            >
                                <i class="fas fa-print"></i>
                            </button>
                        </div>
                    </div>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Stock initial</th>
                                <th>Quantité</th>
                                <th>Stock final</th>
                                <th>Commentaire</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(operation, index) in productHistory"
                                :key="index"
                            >
                                <td data-label="Date">
                                    {{ formatDateTime(operation.created_at) }}
                                </td>
                                <td data-label="Stock initial">
                                    <strong>
                                        {{
                                            formatQuantity(
                                                operation.initial_stock
                                            )
                                        }}
                                    </strong>
                                </td>
                                <td data-label="Quantité">
                                    <strong style="color: #28a745">
                                        {{ formatQuantity(operation.quantity) }}
                                    </strong>
                                </td>
                                <td data-label="Stock final">
                                    <strong>
                                        {{
                                            formatQuantity(
                                                operation.final_stock
                                            )
                                        }}
                                    </strong>
                                </td>
                                <td data-label="Commentaire">
                                    {{ operation.comment }}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-if="productHistory.length === 0" class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Aucune opération trouvée pour ce produit</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Stock Modal -->
        <div
            v-if="showAddStockModal"
            class="modal-overlay"
            @click.self="closeAddStockModal"
        >
            <div class="modal-container">
                <div class="modal-header">
                    <h5>Ajouter du Stock</h5>
                    <button @click="closeAddStockModal" class="modal-close">
                        &times;
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Produit</label>
                        <select
                            v-model="newStock.product_id"
                            class="form-control"
                        >
                            <option value="">Sélectionner un produit</option>
                            <option
                                v-for="product in uniqueProducts"
                                :key="product.product_id"
                                :value="product.product_id"
                            >
                                {{ product.product_name }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Quantité</label>
                        <input
                            v-model.number="newStock.quantity"
                            type="number"
                            min="0"
                            step="0.01"
                            class="form-control"
                            placeholder="Entrer la quantité"
                        />
                    </div>
                    <div class="form-group">
                        <label>Commentaire</label>
                        <textarea
                            v-model="newStock.comment"
                            class="form-control"
                            rows="3"
                            placeholder="Ajouter un commentaire (optionnel)"
                        ></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button @click="closeAddStockModal" class="btn-cancel">
                        Annuler
                    </button>
                    <button @click="addStock" class="btn-submit">
                        Ajouter
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'DepositsComponent',

        data() {
            return {
                deposits: [],
                showAddStockModal: false,
                showHistoryView: false,
                productHistory: [],
                selectedProductName: '',
                selectedProductId: null,
                newStock: {
                    product_id: '',
                    quantity: '',
                    comment: '',
                },

                // Pagination
                currentPage: 1,
                perPage: 10,
            };
        },

        mounted() {
            this.fetchDepositsData();
        },

        methods: {
            fetchDepositsData() {
                axios
                    .get('/depositsList')
                    .then((response) => {
                        if (
                            response.data.success &&
                            Array.isArray(response.data.data)
                        ) {
                            this.deposits = response.data.data;
                        } else {
                            this.deposits = [];
                        }
                    })
                    .catch((error) => {
                        console.error('Error fetching deposits:', error);
                        this.deposits = [];
                        alert('Erreur lors de la récupération des dépôts');
                    });
            },

            viewProductHistory(productId, productName) {
                this.selectedProductId = productId;
                this.selectedProductName = productName;
                this.showHistoryView = true;

                // Fetch history from the API
                axios
                    .get(`/deposits/${productId}/history`)
                    .then((response) => {
                        if (
                            response.data.history &&
                            Array.isArray(response.data.history)
                        ) {
                            this.productHistory = response.data.history;
                        } else {
                            this.productHistory = [];
                        }
                    })
                    .catch((error) => {
                        console.error('Error fetching product history:', error);
                        this.productHistory = [];
                        alert("Erreur lors de la récupération de l'historique");
                    });
            },

            closeHistoryView() {
                this.showHistoryView = false;
                this.productHistory = [];
                this.selectedProductName = '';
                this.selectedProductId = null;
            },

            printProductHistory() {
                const printWindow = window.open('', '_blank');
                const printContent = this.generateProductHistoryPrint();

                printWindow.document.write(printContent);
                printWindow.document.close();
                printWindow.focus();

                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 250);
            },

            generateProductHistoryPrint() {
                let html = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Historique - ${this.selectedProductName}</title>
                    <style>
                        * {
                            margin: 0;
                            padding: 0;
                            box-sizing: border-box;
                        }
                        body {
                            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                            padding: 30px;
                            color: #333;
                            line-height: 1.6;
                        }
                        .header {
                            text-align: center;
                            margin-bottom: 40px;
                            padding-bottom: 20px;
                            border-bottom: 3px solid #667eea;
                        }
                        .header h1 {
                            color: #667eea;
                            font-size: 2rem;
                            margin-bottom: 10px;
                        }
                        .header .product-name {
                            font-size: 1.5rem;
                            color: #555;
                            font-weight: 600;
                        }
                        .print-info {
                            text-align: center;
                            margin-bottom: 30px;
                            padding: 15px;
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            color: white;
                            border-radius: 8px;
                        }
                        .print-info p {
                            margin: 5px 0;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-bottom: 20px;
                        }
                        th {
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            color: white;
                            padding: 12px;
                            text-align: left;
                            font-weight: 600;
                        }
                        td {
                            padding: 12px;
                            border-bottom: 1px solid #e9ecef;
                        }
                        tr:nth-child(even) {
                            background-color: #f8f9fa;
                        }
                        .footer {
                            margin-top: 40px;
                            text-align: center;
                            padding-top: 20px;
                            border-top: 2px solid #e9ecef;
                            color: #666;
                        }
                        @media print {
                            body { padding: 20px; }
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>Historique des Opérations</h1>
                        <div class="product-name">${
                            this.selectedProductName
                        }</div>
                    </div>
                    <div class="print-info">
                        <p><strong>Date d'impression:</strong> ${new Date().toLocaleString(
                            'fr-FR'
                        )}</p>
                        <p><strong>Nombre d'opérations:</strong> ${
                            this.productHistory.length
                        }</p>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Stock initial</th>
                                <th>Quantité</th>
                                <th>Stock final</th>
                                <th>Commentaire</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

                this.productHistory.forEach((operation) => {
                    html += `
                    <tr>
                        <td>${this.formatDateTime(operation.created_at)}</td>
                        <td><strong>${this.formatQuantity(
                            operation.initial_stock
                        )}</strong></td>
                        <td><strong style="color: #28a745;">${this.formatQuantity(
                            operation.quantity
                        )}</strong></td>
                        <td><strong>${this.formatQuantity(
                            operation.final_stock
                        )}</strong></td>
                        <td>${operation.comment}</td>
                    </tr>
                `;
                });

                html += `
                        </tbody>
                    </table>
                    <div class="footer">
                        <p>Document généré automatiquement - Système de Gestion des Dépôts</p>
                    </div>
                </body>
                </html>
            `;

                return html;
            },

            addStock() {
                if (!this.newStock.product_id) {
                    alert('Veuillez sélectionner un produit');
                    return;
                }
                if (this.newStock.quantity <= 0) {
                    alert('La quantité doit être supérieure à 0');
                    return;
                }

                axios
                    .post('/deposits/add', {
                        product_id: this.newStock.product_id,
                        quantity: this.newStock.quantity,
                        comment: this.newStock.comment || 'Ajout de stock',
                    })
                    .then((response) => {
                        alert('Stock ajouté avec succès');
                        this.closeAddStockModal();
                        this.fetchDepositsData();
                    })
                    .catch((error) => {
                        console.error('Error adding stock:', error);
                        alert("Erreur lors de l'ajout du stock");
                    });
            },

            closeAddStockModal() {
                this.showAddStockModal = false;
                this.newStock = {
                    product_id: '',
                    quantity: 0,
                    comment: '',
                };
            },

            printAllDeposits() {
                const printWindow = window.open('', '_blank');
                const printContent = this.generateAllDepositsPrint();

                printWindow.document.write(printContent);
                printWindow.document.close();
                printWindow.focus();

                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 250);
            },

            generateAllDepositsPrint() {
                let html = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Liste des Produits en Stock</title>
                    <style>
                        * {
                            margin: 0;
                            padding: 0;
                            box-sizing: border-box;
                        }
                        body {
                            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                            padding: 30px;
                            color: #333;
                        }
                        .header {
                            text-align: center;
                            margin-bottom: 30px;
                            padding-bottom: 20px;
                            border-bottom: 3px solid #667eea;
                        }
                        .header h1 {
                            color: #667eea;
                            font-size: 2.5rem;
                            margin-bottom: 10px;
                        }
                        .print-info {
                            text-align: center;
                            margin-bottom: 30px;
                            padding: 20px;
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            color: white;
                            border-radius: 10px;
                        }
                        .print-info p {
                            margin: 5px 0;
                            font-size: 1.1rem;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 20px;
                            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                        }
                        th {
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            color: white;
                            padding: 15px;
                            text-align: left;
                            font-weight: 600;
                            font-size: 1rem;
                        }
                        td {
                            padding: 12px 15px;
                            border-bottom: 1px solid #e9ecef;
                        }
                        tr:nth-child(even) {
                            background-color: #f8f9fa;
                        }
                        tr:hover {
                            background-color: #e9ecef;
                        }
                        .footer {
                            margin-top: 40px;
                            text-align: center;
                            padding-top: 20px;
                            border-top: 2px solid #e9ecef;
                            color: #666;
                        }
                        @media print {
                            body { padding: 20px; }
                            table { box-shadow: none; }
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>Liste des Produits en Stock</h1>
                    </div>
                    <div class="print-info">
                        <p><strong>Date d'impression:</strong> ${new Date().toLocaleString(
                            'fr-FR'
                        )}</p>
                        <p><strong>Total des produits:</strong> ${
                            this.deposits.length
                        }</p>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Quantité Actuelle</th>
                                <th>Dernière Mise à Jour</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

                this.deposits.forEach((deposit) => {
                    html += `
                    <tr>
                        <td><strong>${deposit.product_name}</strong></td>
                        <td><strong style="color: #28a745;">${this.formatQuantity(
                            deposit.quantity
                        )}</strong></td>
                        <td>${this.formatDateTime(deposit.updated_at)}</td>
                    </tr>
                `;
                });

                html += `
                        </tbody>
                    </table>
                    <div class="footer">
                        <p>Document généré automatiquement - Système de Gestion des Dépôts</p>
                    </div>
                </body>
                </html>
            `;

                return html;
            },

            formatDateTime(datetime) {
                const date = new Date(datetime);
                const options = {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                };
                return date.toLocaleString('fr-FR', options);
            },

            formatQuantity(value) {
                return Number(value).toLocaleString('fr-FR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                });
            },

            goToPage(page) {
                if (page >= 1 && page <= this.totalPages) {
                    this.currentPage = page;
                }
            },
        },

        computed: {
            uniqueProducts() {
                const productMap = new Map();
                this.deposits.forEach((deposit) => {
                    if (!productMap.has(deposit.product_id)) {
                        productMap.set(deposit.product_id, {
                            product_id: deposit.product_id,
                            product_name: deposit.product_name,
                        });
                    }
                });
                return Array.from(productMap.values());
            },

            totalPages() {
                return Math.ceil(this.deposits.length / this.perPage);
            },

            paginatedDeposits() {
                const start = (this.currentPage - 1) * this.perPage;
                const end = start + this.perPage;
                return this.deposits.slice(start, end);
            },
        },
    };
</script>

<style scoped>
    .sales-content {
        padding: 20px;
    }

    /* Added page header styling */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .page-header h2 {
        margin: 0;
        color: #333;
        font-size: 1.8rem;
        font-weight: 600;
    }

    .header-actions {
        display: flex;
        gap: 10px;
    }

    .btn-add-stock,
    .btn-print-all {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-add-stock {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-add-stock:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-print-all {
        background: #17a2b8;
        color: white;
    }

    .btn-print-all:hover {
        background: #138496;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(23, 162, 184, 0.3);
    }

    .sales-history {
        background: transparent;
    }

    /* Updated table container with blue gradient header */
    .table-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .table-header h3 {
        margin: 0;
        font-size: 1.3rem;
        font-weight: 600;
    }

    .header-info {
        color: white;
    }

    /* Updated table styling to match clients page */
    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table thead {
        background: #f8f9fa;
    }

    .table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
    }

    .table td {
        padding: 1rem;
        border-bottom: 1px solid #dee2e6;
    }

    .table tbody tr:hover {
        background: #f8f9fa;
    }

    .action-btn {
        padding: 8px 12px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-right: 5px;
    }

    .history-btn {
        background: #28a745;
        color: white;
    }

    .history-btn:hover {
        background: #218838;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .empty-state p {
        font-size: 1.2rem;
    }

    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 15px;
        margin-top: 20px;
    }

    .page-btn {
        padding: 8px 12px;
        border: 1px solid #ddd;
        background: white;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .page-btn:hover:not(:disabled) {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .page-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .page-info {
        font-weight: 600;
        color: #333;
    }

    .showStock {
        background: transparent;
    }

    .btn-print-history {
        padding: 8px 16px;
        background: white;
        color: #667eea;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
    }

    .btn-print-history:hover {
        background: rgba(255, 255, 255, 0.9);
        transform: translateY(-2px);
    }

    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 2000;
        animation: fadeIn 0.3s ease;
    }

    .modal-container {
        background: white;
        border-radius: 15px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        animation: slideIn 0.3s ease;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid #eee;
    }

    .modal-header h5 {
        margin: 0;
        font-size: 1.5rem;
        color: #333;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #999;
        transition: color 0.3s ease;
    }

    .modal-close:hover {
        color: #333;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #333;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 1rem 1.5rem;
        border-top: 1px solid #eee;
    }

    .btn-cancel,
    .btn-submit {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-cancel {
        background: #f8f9fa;
        color: #333;
    }

    .btn-cancel:hover {
        background: #e9ecef;
    }

    .btn-submit {
        background: #667eea;
        color: white;
    }

    .btn-submit:hover {
        background: #5568d3;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
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

    /* Responsive Styles */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .header-actions {
            width: 100%;
            flex-direction: column;
        }

        .btn-add-stock,
        .btn-print-all {
            width: 100%;
            justify-content: center;
        }

        .table-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        table.table,
        thead,
        tbody,
        th,
        td,
        tr {
            display: block !important;
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
        }

        tbody td {
            position: relative;
            padding-left: 50% !important;
            text-align: left !important;
            border: none !important;
            border-bottom: 1px solid #eee !important;
            min-height: 40px;
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

        .modal-container {
            width: 95%;
        }
    }
</style>
