<template>
    <div>
        <div class="sales-content">
            <div class="sales-history">
                <!-- Ajout d'un header stylisé en haut avec les filtres et bouton d'impression -->
                <div class="page-header">
                    <div class="header-content">
                        <div class="header-filters">
                            <select
                                v-model="filterPeriod"
                                class="filter-select"
                            >
                                <option>Aujourd'hui</option>
                                <option>Hier</option>
                                <option>Cette semaine</option>
                                <option>Ce mois</option>
                                <option>Toutes les ventes</option>
                                <option>À une date précise</option>
                            </select>

                            <select
                                v-model="filterStatus"
                                class="filter-select"
                            >
                                <option value="">Tous les statuts</option>
                                <option value="done">Terminée</option>
                                <option value="cancelled">Annulée</option>
                            </select>

                            <div
                                v-if="filterPeriod === 'À une date précise'"
                                style="display: inline-block"
                            >
                                <input
                                    type="date"
                                    v-model="selectedDate"
                                    class="date-picker"
                                />
                            </div>
                        </div>

                        <button
                            @click="printCurrentList"
                            class="btn-print-header"
                        >
                            <i class="fas fa-print"></i>
                            Imprimer la liste
                        </button>
                    </div>
                </div>

                <!-- Ajout d'un joli header de table avec gradient bleu -->
                <div class="table-header-styled">
                    <h3>Historique des ventes</h3>
                    <span class="table-count">
                        {{ filteredSales.length }} vente(s)
                    </span>
                </div>

                <table class="table" v-if="paginatedSales.length > 0">
                    <thead>
                        <tr>
                            <th>N° Facture</th>
                            <th>Client</th>
                            <th>Vendeur</th>
                            <th>Date</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(sale, index) in paginatedSales"
                            :key="sale.id"
                        >
                            <td data-label="N° Facture">
                                <strong>N° {{ sale.id }} /FR-N</strong>
                            </td>
                            <td data-label="Client">{{ sale.buyer_name }}</td>
                            <td data-label="Vendeur">{{ sale.seller_name }}</td>
                            <td data-label="Date">
                                {{ formatDateTime(sale.created_at) }}
                            </td>
                            <td data-label="Montant">
                                <strong>
                                    {{ formatAmount(sale.total) }} FCFA
                                </strong>
                            </td>
                            <td data-label="Statut">
                                <span
                                    :class="getStatusClass(sale.status)"
                                    style="
                                        padding: 0.25rem 0.75rem;
                                        border-radius: 20px;
                                        font-size: 0.85rem;
                                        font-weight: 600;
                                        display: inline-block;
                                    "
                                >
                                    {{
                                        sale.status === 'done'
                                            ? 'Terminée'
                                            : sale.status === 'cancelled'
                                            ? 'Annulée'
                                            : sale.status
                                    }}
                                </span>
                            </td>
                            <td data-label="Actions">
                                <button
                                    class="invoice-btn"
                                    @click="showSaleDetails(sale.id)"
                                >
                                    <i
                                        class="fas fa-eye"
                                        style="margin: 2px"
                                    ></i>
                                    Voir
                                </button>

                                <button
                                    v-if="sale.status === 'done'"
                                    class="cancel-btn"
                                    @click="cancelInvoice(sale.id)"
                                    style="
                                        background: #dc3545;
                                        color: white;
                                        border: none;
                                        padding: 0.5rem 1rem;
                                        border-radius: 5px;
                                        cursor: pointer;
                                        margin: 5px;
                                    "
                                >
                                    <i class="fas fa-times"></i>
                                    Annuler
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-else class="no-sales-message">
                    <strong>
                        Aucune vente disponible pour la période sélectionnée
                    </strong>
                </div>

                <div class="pagination-container" v-if="totalPages > 1">
                    <ul class="pagination">
                        <li
                            class="page-item"
                            :class="{ disabled: currentPage === 1 }"
                        >
                            <a
                                class="page-link"
                                href="#"
                                @click.prevent="goToPage(currentPage - 1)"
                            >
                                Précédent
                            </a>
                        </li>
                        <li
                            class="page-item"
                            v-for="page in totalPages"
                            :key="page"
                            :class="{ active: currentPage === page }"
                        >
                            <a
                                class="page-link"
                                href="#"
                                @click.prevent="goToPage(page)"
                            >
                                {{ page }}
                            </a>
                        </li>
                        <li
                            class="page-item"
                            :class="{ disabled: currentPage === totalPages }"
                        >
                            <a
                                class="page-link"
                                href="#"
                                @click.prevent="goToPage(currentPage + 1)"
                            >
                                Suivant
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Modal Détails de la vente -->
                <div v-if="showSaleModal" class="modal-overlay">
                    <div class="modal-container">
                        <div class="modal-header">
                            <h5>Détails de la vente</h5>
                            <button @click="closeSaleModal" class="modal-close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>
                                <strong>Client :</strong>
                                {{ selectedSale.buyer_name }}
                            </p>
                            <p>
                                <strong>Vendeur :</strong>
                                {{ selectedSale.seller_name }}
                            </p>
                            <p>
                                <strong>Date :</strong>
                                {{ formatDateTime(selectedSale.created_at) }}
                            </p>
                            <p>
                                <strong>Total :</strong>
                                {{ formatAmount(selectedSale.total) }} FCFA
                            </p>
                            <p>
                                <strong>Statut :</strong>
                                <span
                                    :class="getStatusClass(selectedSale.status)"
                                    style="
                                        padding: 0.25rem 0.75rem;
                                        border-radius: 20px;
                                        font-size: 0.85rem;
                                        font-weight: 600;
                                        display: inline-block;
                                        margin-left: 0.5rem;
                                    "
                                >
                                    {{
                                        selectedSale.status === 'done'
                                            ? 'Terminée'
                                            : selectedSale.status ===
                                              'cancelled'
                                            ? 'Annulée'
                                            : selectedSale.status
                                    }}
                                </span>
                            </p>
                            <hr />
                            <h6>Produits achetés :</h6>
                            <ul>
                                <li
                                    v-for="(
                                        product, i
                                    ) in selectedSale.products"
                                    :key="i"
                                >
                                    {{ product.name }} -
                                    {{ product.pivot.quantity }} ×
                                    {{
                                        formatAmount(
                                            Number(product.pivot.price)
                                        )
                                    }}
                                    =
                                    {{
                                        formatAmount(
                                            Number(product.pivot.price) *
                                                product.pivot.quantity
                                        )
                                    }}
                                    FCFA
                                </li>
                            </ul>
                        </div>
                        <div class="modal-footer">
                            <button
                                @click="printInvoice(selectedSale.id)"
                                class="btn-download"
                            >
                                <i class="fas fa-print"></i>
                                Imprimer Facture
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'SalesComponent',

        data() {
            return {
                message: 'Bonjour depuis Vue !',
                sales: [],
                selectedSale: null,
                selectedDate: null,
                saleModalInstance: null,
                showSaleModal: false,
                filterPeriod: 'Toutes les ventes',
                filterStatus: '',
                currentPage: 1,
                perPage: 10,
            };
        },

        mounted() {
            this.fetchSalesData();
        },

        methods: {
            fetchSalesData() {
                axios
                    .get('/salesList')
                    .then((response) => {
                        console.log(response.data);
                        this.sales = response.data;
                    })
                    .catch((error) => {
                        console.error(
                            'Erreur lors de la récupération des données :',
                            error
                        );
                    });
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
            formatAmount(value) {
                return Number(value).toLocaleString('fr-FR');
            },
            showSaleDetails(saleId) {
                axios
                    .get(`/saleDetails/${saleId}`)
                    .then((response) => {
                        this.selectedSale = response.data;
                        this.showSaleModal = true;
                        console.log(response.data);
                    })
                    .catch((error) => {
                        console.error(
                            'Erreur chargement détails vente:',
                            error
                        );
                    });
            },
            cancelInvoice(id) {
                if (!confirm('Voulez-vous vraiment annuler cette facture ?')) {
                    return;
                }

                axios
                    .post(`/invoices/${id}/cancel`)
                    .then(() => {
                        alert('Facture annulée avec succès.');
                        this.fetchSalesData();
                    })
                    .catch((error) => {
                        alert("Erreur lors de l'annulation de la facture.");
                        console.error(error);
                    });
            },

            closeSaleModal() {
                this.showSaleModal = false;
                this.selectedSale = null;
            },
            printInvoice(saleId) {
                window.location.href = `/newInvoice/${saleId}`;
            },

            // AJOUTÉ POUR LA PAGINATION: Méthode pour changer de page
            goToPage(page) {
                if (page >= 1 && page <= this.totalPages) {
                    this.currentPage = page;
                }
            },

            printCurrentList() {
                const printWindow = window.open('', '_blank');
                const printContent = this.generatePrintContent();

                printWindow.document.write(printContent);
                printWindow.document.close();
                printWindow.focus();

                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 250);
            },

            generatePrintContent() {
                let html = `
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Liste des Ventes</title>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                padding: 20px;
                            }
                            h1 {
                                text-align: center;
                                color: #333;
                                margin-bottom: 20px;
                            }
                            .filter-info {
                                text-align: center;
                                margin-bottom: 20px;
                                color: #666;
                            }
                            table {
                                width: 100%;
                                border-collapse: collapse;
                                margin-top: 20px;
                            }
                            th, td {
                                border: 1px solid #ddd;
                                padding: 12px;
                                text-align: left;
                            }
                            th {
                                background-color: #667eea;
                                color: white;
                                font-weight: bold;
                            }
                            tr:nth-child(even) {
                                background-color: #f9f9f9;
                            }
                            .status-badge {
                                padding: 4px 12px;
                                border-radius: 12px;
                                font-size: 0.85em;
                                font-weight: 600;
                                display: inline-block;
                            }
                            .status-completed {
                                background-color: #d4edda;
                                color: #155724;
                            }
                            .status-cancelled {
                                background-color: #f8d7da;
                                color: #721c24;
                            }
                            .total-row {
                                font-weight: bold;
                                background-color: #f0f0f0;
                            }
                            @media print {
                                body { padding: 0; }
                            }
                        </style>
                    </head>
                    <body>
                        <h1>Liste des Ventes</h1>
                        <div class="filter-info">
                            <p><strong>Période:</strong> ${
                                this.filterPeriod
                            }</p>
                            ${
                                this.filterStatus
                                    ? `<p><strong>Statut:</strong> ${
                                          this.filterStatus === 'done'
                                              ? 'Terminée'
                                              : 'Annulée'
                                      }</p>`
                                    : ''
                            }
                            <p><strong>Date d'impression:</strong> ${new Date().toLocaleString(
                                'fr-FR'
                            )}</p>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th>N° Facture</th>
                                    <th>Client</th>
                                    <th>Vendeur</th>
                                    <th>Date</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                let totalAmount = 0;
                this.filteredSales.forEach((sale) => {
                    totalAmount += parseFloat(sale.total);
                    const statusClass = this.getStatusClass(sale.status);
                    const statusText =
                        sale.status === 'done'
                            ? 'Terminée'
                            : sale.status === 'cancelled'
                            ? 'Annulée'
                            : sale.status;
                    html += `
                        <tr>
                            <td>N° ${sale.id} /FR-N</td>
                            <td>${sale.buyer_name}</td>
                            <td>${sale.seller_name}</td>
                            <td>${this.formatDateTime(sale.created_at)}</td>
                            <td>${this.formatAmount(sale.total)} FCFA</td>
                            <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                        </tr>
                    `;
                });

                html += `
                            <tr class="total-row">
                                <td colspan="4" style="text-align: right;">TOTAL GÉNÉRAL:</td>
                                <td colspan="2">${this.formatAmount(
                                    totalAmount
                                )} FCFA</td>
                            </tr>
                        </tbody>
                    </table>
                    </body>
                    </html>
                `;

                return html;
            },

            getStatusClass(status) {
                if (status === 'done') {
                    return 'status-completed';
                } else if (status === 'cancelled') {
                    return 'status-cancelled';
                }
                return '';
            },
        },
        computed: {
            filteredSales() {
                const now = new Date();
                this.currentPage = 1; // Réinitialise la pagination à la page 1 à chaque changement de filtre

                return this.sales.filter((sale) => {
                    const saleDate = new Date(sale.created_at);

                    if (
                        this.filterStatus &&
                        sale.status !== this.filterStatus
                    ) {
                        return false;
                    }

                    switch (this.filterPeriod) {
                        case 'Hier': {
                            const yesterday = new Date(now);
                            yesterday.setDate(now.getDate() - 1);
                            return (
                                saleDate.toDateString() ===
                                yesterday.toDateString()
                            );
                        }
                        case "Aujourd'hui":
                            return (
                                saleDate.toDateString() === now.toDateString()
                            );

                        case 'Cette semaine': {
                            const firstDayOfWeek = new Date(now);
                            firstDayOfWeek.setDate(
                                now.getDate() - now.getDay() + 1
                            );
                            firstDayOfWeek.setHours(0, 0, 0, 0);

                            const lastDayOfWeek = new Date(firstDayOfWeek);
                            lastDayOfWeek.setDate(firstDayOfWeek.getDate() + 6);
                            lastDayOfWeek.setHours(23, 59, 59, 999);

                            return (
                                saleDate >= firstDayOfWeek &&
                                saleDate <= lastDayOfWeek
                            );
                        }

                        case 'Ce mois': {
                            return (
                                saleDate.getMonth() === now.getMonth() &&
                                saleDate.getFullYear() === now.getFullYear()
                            );
                        }

                        case 'À une date précise': {
                            if (!this.selectedDate) return true;

                            const selected = new Date(this.selectedDate);
                            selected.setHours(0, 0, 0, 0);

                            const saleDay = new Date(sale.created_at);
                            saleDay.setHours(0, 0, 0, 0);

                            return saleDay.getTime() === selected.getTime();
                        }

                        case 'Toutes les ventes':
                        default:
                            return true;
                    }
                });
            },

            // AJOUTÉ: Propriété calculée pour le nombre total de pages
            totalPages() {
                return Math.ceil(this.filteredSales.length / this.perPage);
            },

            // AJOUTÉ: Propriété calculée pour les ventes de la page actuelle
            paginatedSales() {
                const start = (this.currentPage - 1) * this.perPage;
                const end = start + this.perPage;
                return this.filteredSales.slice(start, end);
            },
        },
    };
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

<style>
    /* Style général du modal */
    .modal-content {
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        padding: 1.5rem;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #fff;
        color: #333;
    }

    /* Titre du modal */
    .modal-header {
        border-bottom: 1px solid #ddd;
        padding-bottom: 0.75rem;
        position: relative;
    }

    /* Bouton croix fermer dans coin supérieur droit */
    .btn-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 28px;
        height: 28px;
        opacity: 0.7;
        transition: opacity 0.2s ease;
        cursor: pointer;
    }
    .btn-close:hover {
        opacity: 1;
    }

    /* Contenu modal-body */
    .modal-body p {
        margin-bottom: 0.6rem;
        font-size: 1rem;
    }

    /* Liste produits */
    .modal-body ul {
        list-style-type: disc;
        margin-left: 1.2rem;
        margin-top: 0.4rem;
        color: #444;
    }

    /* Bouton "Télécharger Facture" */
    .modal-footer {
        display: flex;
        justify-content: flex-end;
        border-top: 1px solid #ddd;
        padding-top: 1rem;
    }

    .modal-footer .btn-download {
        background-color: #764ba2;
        color: white;
        padding: 0.5rem 1.25rem;
        border: none;
        border-radius: 5px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .modal-footer .btn-download:hover {
        background-color: #0056b3;
    }
</style>

<style>
    .filter-select {
        padding: 0.5rem 1rem;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 1rem;
        margin-bottom: 1rem;
        min-width: 180px;
    }

    .date-picker-container {
        margin-bottom: 1.5rem;
    }

    .date-picker {
        padding: 0.5rem 0.8rem;
        font-size: 1rem;
        border: 1px solid #ccc;
        border-radius: 6px;
        width: 180px;
        box-shadow: inset 0 1px 3px rgb(0 0 0 / 0.1);
        transition: border-color 0.3s ease;
    }

    .date-picker:focus {
        outline: none;
        border-color: #764ba2;
        box-shadow: 0 0 5px #764ba2aa;
    }
</style>

<style>
    /* Styles pour la pagination */
    .pagination-container {
        display: flex;
        justify-content: center;
        margin-top: 2rem;
    }

    .pagination {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        background-color: #fff;
    }

    .page-item {
        display: inline;
    }

    .page-link {
        color: #495057;
        padding: 10px 15px;
        text-decoration: none;
        transition: background-color 0.3s;
        border: 1px solid #dee2e6;
    }

    .page-item:first-child .page-link {
        border-top-left-radius: 5px;
        border-bottom-left-radius: 5px;
    }

    .page-item:last-child .page-link {
        border-top-right-radius: 5px;
        border-bottom-right-radius: 5px;
    }

    .page-item.active .page-link {
        background-color: #764ba2;
        color: white;
        border-color: #764ba2;
        font-weight: bold;
    }

    .page-item.disabled .page-link {
        color: #6c757d;
        cursor: not-allowed;
        background-color: #e9ecef;
    }

    .page-item:not(.active) .page-link:hover {
        background-color: #f8f9fa;
    }
</style>

<style>
    /* Ajout des styles pour les badges de statut colorés */
    .status-completed {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .status-cancelled {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    /* Ajout des styles pour le modal overlay */
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
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        animation: slideIn 0.3s ease;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
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

    .modal-body p {
        margin-bottom: 1rem;
        font-size: 1rem;
        line-height: 1.6;
    }

    .modal-body strong {
        color: #333;
        font-weight: 600;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        padding: 1rem 1.5rem;
        border-top: 1px solid #eee;
    }

    .btn-download {
        background: #667eea;
        color: white;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-download:hover {
        background: #5568d3;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }

    .page-header {
        background: white;
        padding: 1.5rem 2rem;
        border-radius: 15px 15px 0 0;
        border-bottom: 1px solid #eee;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .header-filters {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .filter-select {
        padding: 0.6rem 1rem;
        border: 2px solid #e1e5e9;
        border-radius: 8px;
        font-size: 0.95rem;
        background: white;
        cursor: pointer;
        transition: border-color 0.3s ease;
    }

    .filter-select:focus {
        outline: none;
        border-color: #667eea;
    }

    .date-picker {
        padding: 0.6rem 1rem;
        border: 2px solid #e1e5e9;
        border-radius: 8px;
        font-size: 0.95rem;
        background: white;
        cursor: pointer;
        transition: border-color 0.3s ease;
    }

    .date-picker:focus {
        outline: none;
        border-color: #667eea;
    }

    .btn-print-header {
        background: #17a2b8;
        color: white;
        border: none;
        padding: 0.6rem 1.5rem;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.95rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .btn-print-header:hover {
        background: #138496;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(23, 162, 184, 0.3);
    }

    /* Ajout du style pour le header de table avec gradient bleu */
    .table-header-styled {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-header-styled h3 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .table-count {
        background: rgba(255, 255, 255, 0.2);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .sales-history {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .table thead {
        background: #f8f9fa;
    }

    .table thead th {
        color: #495057;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
</style>
