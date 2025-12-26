<template>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <main class="main-content" id="app">
        <div class="returnables-content">
            <!-- Header Section -->
            <div class="header-section">
                <div class="section-header">
                    <h2 style="margin: 0; color: #333">
                        <i class="fas fa-box"></i>
                        Gestion des Produits Consignés
                    </h2>
                </div>

                <!-- Statistics -->
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-number">{{ totalPurchased }}</div>
                        <div class="stat-label">Total Achetés</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">{{ totalReturned }}</div>
                        <div class="stat-label">Total Retournés</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">{{ totalPending }}</div>
                        <div class="stat-label">En Attente</div>
                    </div>
                </div>
            </div>

            <!-- Controls Section -->
            <div class="controls-section">
                <div class="search-box">
                    <input
                        type="text"
                        v-model="searchQuery"
                        placeholder="Rechercher par client ou produit..."
                        class="search-input"
                    />
                    <i class="fas fa-search"></i>
                </div>

                <div class="filters-group">
                    <select v-model="filterClient" class="select-filter">
                        <option value="">Tous les clients</option>
                        <option
                            v-for="client in uniqueClients"
                            :key="client"
                            :value="client"
                        >
                            {{ client }}
                        </option>
                    </select>

                    <select v-model="filterStatus" class="select-filter">
                        <option value="">Tous les statuts</option>
                        <option value="pending">Non retourné</option>
                        <option value="partial">Partiellement retourné</option>
                        <option value="complete">Tous retournés</option>
                    </select>

                    <select v-model="sortOption" class="select-filter">
                        <option>Date (récent)</option>
                        <option>Date (ancien)</option>
                        <option>Client (A-Z)</option>
                        <option>Client (Z-A)</option>
                        <option>Quantité restante (croissant)</option>
                        <option>Quantité restante (décroissant)</option>
                    </select>
                </div>

                <div class="action-buttons">
                    <button @click="openCreateInvoiceModal" class="btn-primary">
                        <i class="fas fa-file-invoice"></i>
                        Créer Facture
                    </button>
                    <button @click="printAllInvoices" class="btn-secondary">
                        <i class="fas fa-print"></i>
                        Imprimer Tous
                    </button>
                </div>
            </div>

            <!-- Products Table -->
            <div class="table-container">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Produit</th>
                            <th>Qté Achetée</th>
                            <th>Qté Retournée</th>
                            <th>Qté Restante</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="item in paginatedItems"
                            :key="item.id"
                            :class="getRowClass(item)"
                        >
                            <td>{{ item.client_name }}</td>
                            <td>{{ item.product_name }}</td>
                            <td style="text-align: center">
                                {{ item.quantity_purchased }}
                            </td>
                            <td style="text-align: center">
                                {{ item.quantity_returned }}
                            </td>
                            <td style="text-align: center; font-weight: bold">
                                {{
                                    item.quantity_purchased -
                                    item.quantity_returned
                                }}
                            </td>
                            <td>
                                <span :style="getStatusBadgeStyle(item)">
                                    {{ getStatusText(item) }}
                                </span>
                            </td>
                            <td>{{ formatDate(item.created_at) }}</td>
                            <td class="actions-cell">
                                <button
                                    @click="openReturnModal(item)"
                                    class="btn-small btn-action"
                                    title="Enregistrer un retour"
                                >
                                    <i class="fas fa-reply"></i>
                                </button>
                                <button
                                    @click="viewHistory(item.sale_id)"
                                    class="btn-small btn-action"
                                    title="Voir l'historique"
                                >
                                    <i class="fas fa-history"></i>
                                </button>
                                <button
                                    @click="openDeleteConfirm(item)"
                                    class="btn-small btn-danger"
                                    title="Supprimer"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination" v-if="totalPages > 1">
                    <button
                        @click="previousPage"
                        :disabled="currentPage === 1"
                        class="btn-pagination"
                    >
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span class="pagination-info">
                        Page {{ currentPage }} / {{ totalPages }}
                    </span>
                    <button
                        @click="nextPage"
                        :disabled="currentPage === totalPages"
                        class="btn-pagination"
                    >
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>

            <!-- Modal: Créer Facture -->
            <div
                v-if="showCreateInvoiceModal"
                class="modal-overlay"
                @click.self="closeCreateInvoiceModal"
            >
                <div class="modal-container">
                    <div class="modal-header">
                        <h5>
                            <i class="fas fa-file-invoice"></i>
                            Créer une Facture Consignés
                        </h5>
                        <button
                            @click="closeCreateInvoiceModal"
                            class="modal-close"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Client Selection -->
                        <div class="form-group">
                            <label>Sélectionner le client *</label>
                            <div class="customer-search-container">
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="invoiceCustomerSearch"
                                    @input="filterInvoiceCustomers"
                                    placeholder="Rechercher un client..."
                                    required
                                />

                                <div
                                    v-if="
                                        showInvoiceCustomerDropdown &&
                                        filteredInvoiceCustomers.length > 0
                                    "
                                    class="customer-dropdown"
                                >
                                    <div
                                        v-for="client in filteredInvoiceCustomers"
                                        :key="client"
                                        @click="selectInvoiceCustomer(client)"
                                        class="customer-dropdown-item"
                                    >
                                        {{ client }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Products Selection -->
                        <div class="form-group" v-if="invoiceSelectedCustomer">
                            <label>Produits consignés à reprendre *</label>
                            <div class="products-selection">
                                <div
                                    v-for="item in getCustomerReturnables(
                                        invoiceSelectedCustomer
                                    )"
                                    :key="item.id"
                                    class="product-checkbox"
                                >
                                    <input
                                        type="checkbox"
                                        :id="'product-' + item.id"
                                        v-model="invoiceSelectedProducts"
                                        :value="item.id"
                                    />
                                    <label :for="'product-' + item.id">
                                        {{ item.product_name }} - ({{
                                            item.quantity_purchased -
                                            item.quantity_returned
                                        }}
                                        restants)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            @click="closeCreateInvoiceModal"
                            class="btn-secondary"
                        >
                            <i class="fas fa-times"></i>
                            Annuler
                        </button>
                        <button
                            @click="generateInvoice"
                            :disabled="
                                !invoiceSelectedCustomer ||
                                invoiceSelectedProducts.length === 0
                            "
                            class="btn-primary"
                        >
                            <i class="fas fa-print"></i>
                            Générer Facture
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal: Enregistrer Retour -->
            <div
                v-if="showReturnModal"
                class="modal-overlay"
                @click.self="closeReturnModal"
            >
                <div class="modal-container">
                    <div class="modal-header">
                        <h5>
                            <i class="fas fa-reply"></i>
                            Enregistrer un Retour
                        </h5>
                        <button @click="closeReturnModal" class="modal-close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body" v-if="returnItem">
                        <p>
                            <strong>Client:</strong>
                            {{ returnItem.client_name }}
                        </p>
                        <p>
                            <strong>Produit:</strong>
                            {{ returnItem.product_name }}
                        </p>
                        <p>
                            <strong>Quantité Achetée:</strong>
                            {{ returnItem.quantity_purchased }}
                        </p>
                        <p>
                            <strong>Quantité Retournée:</strong>
                            {{ returnItem.quantity_returned }}
                        </p>
                        <p>
                            <strong>Peut Retourner:</strong>
                            {{
                                returnItem.quantity_purchased -
                                returnItem.quantity_returned
                            }}
                        </p>

                        <div class="form-group">
                            <label>Quantité à retourner *</label>
                            <input
                                type="number"
                                v-model.number="returnQuantity"
                                min="1"
                                :max="
                                    returnItem.quantity_purchased -
                                    returnItem.quantity_returned
                                "
                                class="form-control"
                                placeholder="Entrez la quantité"
                            />
                        </div>

                        <div class="form-group">
                            <label>Date de retour *</label>
                            <input
                                type="date"
                                v-model="returnDate"
                                class="form-control"
                            />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button @click="closeReturnModal" class="btn-secondary">
                            <i class="fas fa-times"></i>
                            Annuler
                        </button>
                        <button @click="submitReturn" class="btn-primary">
                            <i class="fas fa-save"></i>
                            Enregistrer
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal: Confirmation Suppression -->
            <div
                v-if="showDeleteConfirmation"
                class="modal-overlay"
                @click.self="closeDeleteConfirm"
            >
                <div class="modal-container modal-confirm">
                    <div class="modal-header">
                        <h5>
                            <i class="fas fa-exclamation-triangle"></i>
                            Confirmer la suppression
                        </h5>
                    </div>
                    <div class="modal-body">
                        <p>
                            Êtes-vous sûr de vouloir supprimer cet
                            enregistrement ?
                        </p>
                        <p v-if="returnableToDelete">
                            <strong>
                                {{ returnableToDelete.client_name }}
                            </strong>
                            -
                            <strong>
                                {{ returnableToDelete.product_name }}
                            </strong>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button
                            @click="closeDeleteConfirm"
                            class="btn-secondary"
                        >
                            <i class="fas fa-times"></i>
                            Annuler
                        </button>
                        <button @click="deleteReturnable" class="btn-danger">
                            <i class="fas fa-trash"></i>
                            Supprimer
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal: Historique Retours -->
            <div
                v-if="showHistoryModal"
                class="modal-overlay"
                @click.self="closeHistoryModal"
            >
                <div class="modal-container modal-large">
                    <div class="modal-header">
                        <h5>
                            <i class="fas fa-history"></i>
                            Historique des Retours - Facture #{{
                                selectedSaleId
                            }}
                        </h5>
                        <button @click="closeHistoryModal" class="modal-close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="history-table">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Quantité Retournée</th>
                                    <th>Date de Retour</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="item in returnHistory"
                                    :key="item.id"
                                >
                                    <td>
                                        {{ getProductName(item.product_id) }}
                                    </td>
                                    <td style="text-align: center">
                                        {{ item.quantity_returned }}
                                    </td>
                                    <td>{{ formatDate(item.return_date) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button
                            @click="closeHistoryModal"
                            class="btn-secondary"
                        >
                            <i class="fas fa-times"></i>
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<script>
    export default {
        name: 'ReturnableProductsComponent',

        data() {
            return {
                returnables: [],
                showHistoryModal: false,
                returnHistory: [],
                selectedSaleId: null,
                showReturnModal: false,
                showDeleteConfirmation: false,
                returnableToDelete: null,
                returnDate: new Date().toISOString().split('T')[0],
                returnItem: {
                    id: null,
                    client_name: '',
                    product_name: '',
                    quantity_purchased: 0,
                    quantity_returned: 0,
                },
                returnQuantity: 0,
                searchQuery: '',
                filterClient: '',
                filterStatus: '',
                sortOption: 'Date (récent)',
                currentPage: 1,
                itemsPerPage: 10,
                // Invoice modal data
                showCreateInvoiceModal: false,
                invoiceCustomerSearch: '',
                invoiceSelectedCustomer: null,
                invoiceSelectedProducts: [],
                showInvoiceCustomerDropdown: false,
            };
        },

        mounted() {
            this.fetchReturnables();
            document.addEventListener('click', this.handleClickOutside);
        },

        beforeUnmount() {
            document.removeEventListener('click', this.handleClickOutside);
        },

        methods: {
            async fetchReturnables() {
                try {
                    console.log(
                        '[v0] Fetching returnables - Route: /returnableProductsList'
                    );
                    const response = await axios.get(
                        `${window.location.origin}/returnableProductsList`
                    );
                    console.log(
                        '[v0] Returnables fetched successfully:',
                        response.data
                    );
                    this.returnables = response.data;
                } catch (error) {
                    console.error('[v0] Error fetching returnables:', error);
                }
            },

            openReturnModal(item) {
                console.log('[v0] Opening return modal for item:', item);
                this.returnItem = { ...item };
                this.returnQuantity = '';
                this.showReturnModal = true;
            },

            closeReturnModal() {
                this.showReturnModal = false;
                this.returnItem = {
                    id: null,
                    client_name: '',
                    product_name: '',
                    quantity_purchased: 0,
                    quantity_returned: 0,
                };
                this.returnQuantity = 0;
            },

            async submitReturn() {
                const maxReturn =
                    this.returnItem.quantity_purchased -
                    this.returnItem.quantity_returned;

                if (!this.returnQuantity || this.returnQuantity <= 0) {
                    alert('Veuillez entrer une quantité valide');
                    return;
                }

                if (this.returnQuantity > maxReturn) {
                    alert(
                        `Vous ne pouvez pas retourner plus de ${maxReturn} emballages`
                    );
                    return;
                }

                if (!this.returnDate) {
                    alert('Veuillez sélectionner une date de retour');
                    return;
                }

                try {
                    const data = {
                        quantity_returned: this.returnQuantity,
                        date: this.returnDate,
                    };
                    console.log(
                        `[v0] Submitting return - Route: /returnableProducts/${this.returnItem.id}/return - Data:`,
                        data
                    );

                    const response = await axios.post(
                        `${window.location.origin}/returnableProducts/${this.returnItem.id}/return`,
                        data
                    );

                    console.log(
                        '[v0] Return submitted successfully:',
                        response.data
                    );

                    if (response.data.success) {
                        await this.fetchReturnables();
                        this.closeReturnModal();
                        alert('Retour enregistré avec succès');
                    }
                } catch (error) {
                    console.error('[v0] Error submitting return:', error);
                    alert("Erreur lors de l'enregistrement du retour");
                }
            },

            openDeleteConfirm(item) {
                console.log('[v0] Opening delete confirmation for item:', item);
                this.returnableToDelete = item;
                this.showDeleteConfirmation = true;
            },

            closeDeleteConfirm() {
                this.showDeleteConfirmation = false;
                this.returnableToDelete = null;
            },

            async deleteReturnable() {
                if (!this.returnableToDelete) return;

                try {
                    console.log(
                        `[v0] Deleting returnable - Route: /returnableProducts/${this.returnableToDelete.id}/delete`
                    );

                    const response = await axios.post(
                        `${window.location.origin}/returnableProducts/${this.returnableToDelete.id}/delete`
                    );

                    console.log('[v0] Delete successful:', response.data);

                    if (response.data.success) {
                        await this.fetchReturnables();
                        this.closeDeleteConfirm();
                        alert('Enregistrement supprimé avec succès');
                    }
                } catch (error) {
                    console.error('[v0] Error deleting returnable:', error);
                    alert('Erreur lors de la suppression');
                }
            },

            formatDate(dateString) {
                return new Date(dateString).toLocaleDateString('fr-FR');
            },

            getStatusText(item) {
                const remaining =
                    item.quantity_purchased - item.quantity_returned;
                if (remaining === 0) return 'Tous retournés';
                if (item.quantity_returned > 0) return 'Partiellement retourné';
                return 'Non retourné';
            },

            getStatusBadgeStyle(item) {
                const remaining =
                    item.quantity_purchased - item.quantity_returned;
                let colors;

                if (remaining === 0) {
                    colors = {
                        bg: '#e8f5e9',
                        color: '#388e3c',
                        border: '#a5d6a7',
                    };
                } else if (item.quantity_returned > 0) {
                    colors = {
                        bg: '#fff3e0',
                        color: '#f57c00',
                        border: '#ffcc80',
                    };
                } else {
                    colors = {
                        bg: '#ffebee',
                        color: '#c62828',
                        border: '#ef9a9a',
                    };
                }

                return {
                    backgroundColor: colors.bg,
                    color: colors.color,
                    border: `1px solid ${colors.border}`,
                    padding: '0.4rem 0.8rem',
                    borderRadius: '20px',
                    fontSize: '0.85rem',
                    fontWeight: '600',
                    display: 'inline-block',
                };
            },

            getRowClass(item) {
                const remaining =
                    item.quantity_purchased - item.quantity_returned;
                if (remaining === 0) return 'row-complete';
                if (item.quantity_returned > 0) return 'row-partial';
                return 'row-pending';
            },

            async viewHistory(saleId) {
                console.log('[v0] Viewing return history for sale:', saleId);
                this.selectedSaleId = saleId;
                this.showHistoryModal = true;

                try {
                    console.log(
                        '[v0] Fetching return history - Route: /stocksReturnableProductsList'
                    );
                    const response = await axios.get(
                        `${window.location.origin}/stocksReturnableProductsList`
                    );
                    console.log('[v0] Return history fetched:', response.data);

                    this.returnHistory = response.data.filter(
                        (item) => item.sale_id === saleId
                    );
                } catch (error) {
                    console.error('[v0] Error fetching return history:', error);
                }
            },

            closeHistoryModal() {
                this.showHistoryModal = false;
                this.returnHistory = [];
                this.selectedSaleId = null;
            },

            getProductName(productId) {
                const item = this.returnables.find(
                    (r) => r.product_id === productId
                );
                return item ? item.product_name : 'Produit inconnu';
            },

            // Invoice creation methods
            openCreateInvoiceModal() {
                console.log('[v0] Opening create invoice modal');
                this.showCreateInvoiceModal = true;
                this.invoiceCustomerSearch = '';
                this.invoiceSelectedCustomer = null;
                this.invoiceSelectedProducts = [];
            },

            closeCreateInvoiceModal() {
                this.showCreateInvoiceModal = false;
                this.invoiceCustomerSearch = '';
                this.invoiceSelectedCustomer = null;
                this.invoiceSelectedProducts = [];
            },

            filterInvoiceCustomers() {
                this.showInvoiceCustomerDropdown = true;
            },

            selectInvoiceCustomer(client) {
                console.log('[v0] Selected customer for invoice:', client);
                this.invoiceSelectedCustomer = client;
                this.invoiceCustomerSearch = client;
                this.showInvoiceCustomerDropdown = false;
                this.invoiceSelectedProducts = [];
            },

            getCustomerReturnables(clientName) {
                return this.filteredReturnables.filter(
                    (item) => item.client_name === clientName
                );
            },

            generateInvoice() {
                if (
                    !this.invoiceSelectedCustomer ||
                    this.invoiceSelectedProducts.length === 0
                ) {
                    alert(
                        'Veuillez sélectionner un client et au moins un produit'
                    );
                    return;
                }

                console.log(
                    '[v0] Generating invoice - Customer:',
                    this.invoiceSelectedCustomer,
                    'Products:',
                    this.invoiceSelectedProducts
                );

                const items = this.filteredReturnables.filter(
                    (item) =>
                        item.client_name === this.invoiceSelectedCustomer &&
                        this.invoiceSelectedProducts.includes(item.id)
                );

                if (items.length === 0) return;

                this.printInvoice(items);
                this.closeCreateInvoiceModal();
            },

            printInvoice(items) {
                const client = items[0].client_name;
                let totalPurchased = 0;
                let totalReturned = 0;
                let totalRemaining = 0;

                const tableRows = items
                    .map((item, idx) => {
                        const remaining =
                            item.quantity_purchased - item.quantity_returned;
                        totalPurchased += item.quantity_purchased;
                        totalReturned += item.quantity_returned;
                        totalRemaining += remaining;

                        return `
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd;">${
                                idx + 1
                            }</td>
                            <td style="padding: 10px; border: 1px solid #ddd;">${
                                item.product_name
                            }</td>
                            <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">${
                                item.quantity_purchased
                            }</td>
                            <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">${
                                item.quantity_returned
                            }</td>
                            <td style="padding: 10px; border: 1px solid #ddd; text-align: center; font-weight: bold;">${remaining}</td>
                            <td style="padding: 10px; border: 1px solid #ddd;">${this.formatDate(
                                item.created_at
                            )}</td>
                        </tr>
                    `;
                    })
                    .join('');

                const htmlContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Facture Emballages Consignés</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            margin: 20px;
                            color: #333;
                        }
                        .company-header {
                            text-align: center;
                            margin-bottom: 30px;
                            border-bottom: 3px solid #667eea;
                            padding-bottom: 20px;
                        }
                        .company-header h1 {
                            color: #667eea;
                            margin: 0;
                            font-size: 2rem;
                        }
                        .company-header p {
                            margin: 5px 0;
                            color: #666;
                        }
                        h2 {
                            text-align: center;
                            color: #764ba2;
                            margin-bottom: 30px;
                        }
                        .client-info {
                            background: #f8f9fa;
                            padding: 15px;
                            border-radius: 8px;
                            margin-bottom: 20px;
                            border-left: 4px solid #667eea;
                        }
                        .client-info p {
                            margin: 5px 0;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 20px;
                            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                        }
                        thead {
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            color: white;
                        }
                        th {
                            padding: 12px;
                            text-align: left;
                            border: 1px solid #667eea;
                        }
                        tfoot {
                            background: #f8f9fa;
                            font-weight: bold;
                        }
                        .observation {
                            margin-top: 30px;
                            padding: 15px;
                            background: #fff3cd;
                            border-left: 4px solid #ffc107;
                            border-radius: 4px;
                        }
                        .observation p {
                            margin: 0;
                            color: #856404;
                        }
                        .footer {
                            margin-top: 40px;
                            text-align: center;
                            color: #666;
                            font-size: 0.9rem;
                        }
                        @media print {
                            body { margin: 10mm; }
                        }
                    </style>
                </head>
                <body>
                    <div class="company-header">
                        <h1>SAGER</h1>
                        <p>Votre partenaire de confiance pour tous vos besoins en boissons et gaz domestique
                        <br> Distribution professionnelle • Vente en gros et détail</p>
                        <p><strong>Téléphone:</strong> +229 0196466625</p>
                        <p><strong>IFU:</strong> 0202586942320</p>
                    </div>

                    <h2>FACTURE EMBALLAGES CONSIGNÉS</h2>

                    <div class="client-info">
                        <p><strong>Client:</strong> ${client}</p>
                        <p><strong>Date d'impression:</strong> ${new Date().toLocaleDateString(
                            'fr-FR'
                        )}</p>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Produit</th>
                                <th style="text-align: center;">Qté Achetée</th>
                                <th style="text-align: center;">Qté Retournée</th>
                                <th style="text-align: center;">Qté Restante</th>
                                <th>Date d'achat</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${tableRows}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" style="padding: 12px; border: 1px solid #ddd; text-align: right;">TOTAL:</td>
                                <td style="padding: 12px; border: 1px solid #ddd; text-align: center;">${totalPurchased}</td>
                                <td style="padding: 12px; border: 1px solid #ddd; text-align: center;">${totalReturned}</td>
                                <td style="padding: 12px; border: 1px solid #ddd; text-align: center; color: ${
                                    totalRemaining > 0 ? '#c62828' : '#388e3c'
                                };">${totalRemaining}</td>
                                <td style="padding: 12px; border: 1px solid #ddd;"></td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="observation">
                        <p><strong>Observation:</strong> Les emballages consignés restent la propriété de SAGER MARKET jusqu'à leur retour complet.</p>
                    </div>

                    <div class="footer">
                        <p>Merci de votre confiance</p>
                        <p>Rapport généré avec l'application SagerMarket</p>
                    </div>
                </body>
                </html>
            `;

                console.log('[v0] Printing invoice for client:', client);
                const printWindow = window.open('', '', 'width=800,height=600');
                printWindow.document.open();
                printWindow.document.write(htmlContent);
                printWindow.document.close();
                printWindow.focus();
                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 250);
            },

            printAllInvoices() {
                console.log('[v0] Printing all invoices');
                const groups = this.groupedBySaleId;

                let invoicesHTML = '';

                groups.forEach((group, index) => {
                    const tableRows = group.items
                        .map((item, idx) => {
                            const remaining =
                                item.quantity_purchased -
                                item.quantity_returned;
                            return `
                            <tr>
                                <td style="padding: 10px; border: 1px solid #ddd;">${
                                    idx + 1
                                }</td>
                                <td style="padding: 10px; border: 1px solid #ddd;">${
                                    item.product_name
                                }</td>
                                <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">${
                                    item.quantity_purchased
                                }</td>
                                <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">${
                                    item.quantity_returned
                                }</td>
                                <td style="padding: 10px; border: 1px solid #ddd; text-align: center; font-weight: bold;">${remaining}</td>
                                <td style="padding: 10px; border: 1px solid #ddd;">${this.formatDate(
                                    item.created_at
                                )}</td>
                            </tr>
                        `;
                        })
                        .join('');

                    invoicesHTML += `
                    <div class="invoice-page" style="${
                        index > 0 ? 'page-break-before: always;' : ''
                    }">
                        <div class="company-header" style="text-align: center; margin-bottom: 30px; border-bottom: 3px solid #667eea; padding-bottom: 20px;">
                            <h1 style="color: #667eea; margin: 0; font-size: 2rem;">SAGER</h1>
                            <p>Votre partenaire de confiance pour tous vos besoins en boissons et gaz domestique
                            <br> Distribution professionnelle • Vente en gros et détail</p>
                            <p style="margin: 5px 0; color: #666;"><strong>Téléphone:</strong> +229 0196466625</p>
                            <p style="margin: 5px 0; color: #666;"><strong>IFU:</strong> 0202586942320</p>
                        </div>

                        <h2 style="text-align: center; color: #764ba2; margin-bottom: 30px;">FACTURE EMBALLAGES CONSIGNÉS</h2>

                        <div class="client-info" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #667eea;">
                            <p style="margin: 5px 0;"><strong>Facture N°:</strong> ${
                                group.sale_id
                            }</p>
                            <p style="margin: 5px 0;"><strong>Client:</strong> ${
                                group.items[0].client_name
                            }</p>
                            <p style="margin: 5px 0;"><strong>Date d'impression:</strong> ${new Date().toLocaleDateString(
                                'fr-FR'
                            )}</p>
                        </div>

                        <table style="width: 100%; border-collapse: collapse; margin-top: 20px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
                            <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                <tr>
                                    <th style="padding: 12px; text-align: left; border: 1px solid #667eea;">N°</th>
                                    <th style="padding: 12px; text-align: left; border: 1px solid #667eea;">Produit</th>
                                    <th style="padding: 12px; text-align: center; border: 1px solid #667eea;">Qté Achetée</th>
                                    <th style="padding: 12px; text-align: center; border: 1px solid #667eea;">Qté Retournée</th>
                                    <th style="padding: 12px; text-align: center; border: 1px solid #667eea;">Qté Restante</th>
                                    <th style="padding: 12px; text-align: left; border: 1px solid #667eea;">Date d'achat</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows}
                            </tbody>
                            <tfoot style="background: #f8f9fa; font-weight: bold;">
                                <tr>
                                    <td colspan="2" style="padding: 12px; border: 1px solid #ddd; text-align: right;">TOTAL:</td>
                                    <td style="padding: 12px; border: 1px solid #ddd; text-align: center;">${
                                        group.totalPurchased
                                    }</td>
                                    <td style="padding: 12px; border: 1px solid #ddd; text-align: center;">${
                                        group.totalReturned
                                    }</td>
                                    <td style="padding: 12px; border: 1px solid #ddd; text-align: center; color: ${
                                        group.totalRemaining > 0
                                            ? '#c62828'
                                            : '#388e3c'
                                    };">${group.totalRemaining}</td>
                                    <td style="padding: 12px; border: 1px solid #ddd;"></td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="observation" style="margin-top: 30px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
                            <p style="margin: 0; color: #856404;"><strong>Observation:</strong> Les emballages consignés restent la propriété de SAGER MARKET jusqu'à leur retour complet.</p>
                        </div>

                        <div class="footer" style="margin-top: 40px; text-align: center; color: #666; font-size: 0.9rem;">
                            <p>Merci de votre confiance</p>
                            <p>Rapport généré avec l'application SagerMarket</p>
                        </div>
                    </div>
                `;
                });

                const htmlContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Factures Emballages Consignés</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            margin: 20px;
                            color: #333;
                        }
                        @media print {
                            .invoice-page {
                                page-break-after: always;
                            }
                            body { margin: 10mm; }
                        }
                    </style>
                </head>
                <body>
                    ${invoicesHTML}
                </body>
                </html>
            `;

                const printWindow = window.open('', '', 'width=800,height=600');
                printWindow.document.open();
                printWindow.document.write(htmlContent);
                printWindow.document.close();
                printWindow.focus();
                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 250);
            },

            nextPage() {
                if (this.currentPage < this.totalPages) {
                    this.currentPage++;
                }
            },

            previousPage() {
                if (this.currentPage > 1) {
                    this.currentPage--;
                }
            },

            handleClickOutside(event) {
                if (
                    event.target.closest('.customer-search-container') === null
                ) {
                    this.showInvoiceCustomerDropdown = false;
                }
            },
        },

        computed: {
            filteredReturnables() {
                let filtered = this.returnables;

                // Search filter
                if (this.searchQuery) {
                    const query = this.searchQuery.toLowerCase();
                    filtered = filtered.filter(
                        (item) =>
                            (item.client_name &&
                                item.client_name
                                    .toLowerCase()
                                    .includes(query)) ||
                            (item.product_name &&
                                item.product_name.toLowerCase().includes(query))
                    );
                }

                // Client filter
                if (this.filterClient) {
                    filtered = filtered.filter(
                        (item) => item.client_name === this.filterClient
                    );
                }

                // Status filter
                if (this.filterStatus) {
                    filtered = filtered.filter((item) => {
                        const remaining =
                            item.quantity_purchased - item.quantity_returned;
                        if (this.filterStatus === 'pending')
                            return remaining === item.quantity_purchased;
                        if (this.filterStatus === 'partial')
                            return (
                                remaining > 0 &&
                                remaining < item.quantity_purchased
                            );
                        if (this.filterStatus === 'complete')
                            return remaining === 0;
                        return true;
                    });
                }

                // Sorting
                switch (this.sortOption) {
                    case 'Date (récent)':
                        filtered.sort(
                            (a, b) =>
                                new Date(b.created_at) - new Date(a.created_at)
                        );
                        break;
                    case 'Date (ancien)':
                        filtered.sort(
                            (a, b) =>
                                new Date(a.created_at) - new Date(b.created_at)
                        );
                        break;
                    case 'Client (A-Z)':
                        filtered.sort((a, b) =>
                            (a.client_name || '').localeCompare(
                                b.client_name || ''
                            )
                        );
                        break;
                    case 'Client (Z-A)':
                        filtered.sort((a, b) =>
                            (b.client_name || '').localeCompare(
                                a.client_name || ''
                            )
                        );
                        break;
                    case 'Quantité restante (croissant)':
                        filtered.sort(
                            (a, b) =>
                                a.quantity_purchased -
                                a.quantity_returned -
                                (b.quantity_purchased - b.quantity_returned)
                        );
                        break;
                    case 'Quantité restante (décroissant)':
                        filtered.sort(
                            (a, b) =>
                                b.quantity_purchased -
                                b.quantity_returned -
                                (a.quantity_purchased - a.quantity_returned)
                        );
                        break;
                }

                return filtered;
            },

            totalPurchased() {
                return this.filteredReturnables.reduce(
                    (sum, item) => sum + item.quantity_purchased,
                    0
                );
            },

            totalReturned() {
                return this.filteredReturnables.reduce(
                    (sum, item) => sum + item.quantity_returned,
                    0
                );
            },

            totalPending() {
                return this.filteredReturnables.reduce(
                    (sum, item) =>
                        sum +
                        (item.quantity_purchased - item.quantity_returned),
                    0
                );
            },

            uniqueClients() {
                return [
                    ...new Set(
                        this.returnables.map((item) => item.client_name)
                    ),
                ].sort();
            },

            filteredInvoiceCustomers() {
                if (!this.invoiceCustomerSearch) return this.uniqueClients;
                const query = this.invoiceCustomerSearch.toLowerCase();
                return this.uniqueClients.filter((client) =>
                    client.toLowerCase().includes(query)
                );
            },

            groupedBySaleId() {
                const groups = {};

                this.filteredReturnables.forEach((item) => {
                    if (!groups[item.sale_id]) {
                        groups[item.sale_id] = {
                            sale_id: item.sale_id,
                            items: [],
                            totalPurchased: 0,
                            totalReturned: 0,
                            totalRemaining: 0,
                        };
                    }

                    groups[item.sale_id].items.push(item);
                    groups[item.sale_id].totalPurchased +=
                        item.quantity_purchased;
                    groups[item.sale_id].totalReturned +=
                        item.quantity_returned;
                    groups[item.sale_id].totalRemaining +=
                        item.quantity_purchased - item.quantity_returned;
                });

                return Object.values(groups);
            },

            totalPages() {
                return Math.ceil(
                    this.filteredReturnables.length / this.itemsPerPage
                );
            },

            paginatedItems() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                return this.filteredReturnables.slice(
                    start,
                    start + this.itemsPerPage
                );
            },
        },

        watch: {
            filteredReturnables() {
                this.currentPage = 1;
            },
        },
    };
</script>

<style scoped>
    .returnables-content {
        padding: 2rem;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
    }

    .header-section {
        margin-bottom: 2rem;
    }

    .section-header {
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .section-header h2 {
        margin: 0;
        color: #333;
        font-size: 1.8rem;
    }

    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: bold;
        color: #667eea;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        color: #666;
        font-size: 0.95rem;
    }

    .controls-section {
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .search-box {
        position: relative;
        margin-bottom: 1rem;
    }

    .search-input {
        width: 100%;
        padding: 0.75rem 2.5rem 0.75rem 1rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
    }

    .search-box i {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
    }

    .filters-group {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .select-filter {
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
        background: white;
        cursor: pointer;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
    }

    .btn-primary,
    .btn-secondary,
    .btn-danger {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background: #667eea;
        color: white;
    }

    .btn-primary:hover {
        background: #5568d3;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-primary:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background: #c82333;
    }

    .table-container {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .products-table {
        width: 100%;
        border-collapse: collapse;
    }

    .products-table thead {
        background: #667eea;
        color: white;
    }

    .products-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
    }

    .products-table td {
        padding: 1rem;
        border-bottom: 1px solid #eee;
    }

    .products-table tbody tr {
        transition: background 0.2s ease;
    }

    .products-table tbody tr:hover {
        background: #f8f9fa;
    }

    .row-complete {
        background: #e8f5e9;
    }

    .row-partial {
        background: #fff3e0;
    }

    .row-pending {
        background: #ffebee;
    }

    .actions-cell {
        display: flex;
        gap: 0.5rem;
    }

    .btn-small {
        padding: 0.5rem;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .btn-action {
        background: #667eea;
        color: white;
    }

    .btn-action:hover {
        background: #5568d3;
    }

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background: #c82333;
    }

    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
        border-top: 1px solid #eee;
    }

    .btn-pagination {
        padding: 0.5rem 1rem;
        border: 1px solid #667eea;
        background: white;
        color: #667eea;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-pagination:hover:not(:disabled) {
        background: #667eea;
        color: white;
    }

    .btn-pagination:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .pagination-info {
        color: #666;
        font-weight: 600;
    }

    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .modal-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        max-width: 600px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-large {
        max-width: 900px;
    }

    .modal-confirm {
        max-width: 400px;
    }

    .modal-header {
        background: #667eea;
        color: white;
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #eee;
    }

    .modal-header h5 {
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .modal-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .modal-close:hover {
        transform: scale(1.2);
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
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
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
        box-sizing: border-box;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .customer-search-container {
        position: relative;
    }

    .customer-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-top: none;
        border-radius: 0 0 8px 8px;
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .customer-dropdown-item {
        padding: 0.75rem 1rem;
        cursor: pointer;
        transition: background 0.2s ease;
        border-bottom: 1px solid #eee;
    }

    .customer-dropdown-item:hover {
        background: #f8f9fa;
    }

    .customer-dropdown-item:last-child {
        border-bottom: none;
    }

    .products-selection {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .product-checkbox {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .product-checkbox input[type='checkbox'] {
        cursor: pointer;
        width: 18px;
        height: 18px;
    }

    .product-checkbox label {
        margin: 0;
        font-weight: 400;
        cursor: pointer;
    }

    .history-table {
        width: 100%;
        border-collapse: collapse;
    }

    .history-table thead {
        background: #f8f9fa;
    }

    .history-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        border-bottom: 2px solid #ddd;
    }

    .history-table td {
        padding: 1rem;
        border-bottom: 1px solid #eee;
    }

    @media (max-width: 768px) {
        .stats-container {
            grid-template-columns: 1fr;
        }

        .filters-group {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column;
        }

        .products-table {
            font-size: 0.9rem;
        }

        .products-table th,
        .products-table td {
            padding: 0.75rem 0.5rem;
        }

        .modal-container {
            width: 95%;
            max-height: 95vh;
        }
    }
</style>
