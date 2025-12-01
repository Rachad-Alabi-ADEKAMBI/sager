<template>
    <div class="returnables-content">
        <!-- Statistics section -->
        <div class="statistics-section">
            <div class="stat-card stat-card-1">
                <div class="stat-icon"><i class="fas fa-recycle"></i></div>
                <div class="stat-info">
                    <div class="stat-label">Total emballages vendus</div>
                    <div class="stat-value">{{ totalPurchased }}</div>
                </div>
            </div>
            <div class="stat-card stat-card-2">
                <div class="stat-icon"><i class="fas fa-undo-alt"></i></div>
                <div class="stat-info">
                    <div class="stat-label">Total emballages retournés</div>
                    <div class="stat-value">{{ totalReturned }}</div>
                </div>
            </div>
            <div class="stat-card stat-card-3">
                <div class="stat-icon"><i class="fas fa-boxes"></i></div>
                <div class="stat-info">
                    <div class="stat-label">Emballages non retournés</div>
                    <div class="stat-value">{{ totalPending }}</div>
                </div>
            </div>
        </div>

        <!-- Header Actions -->
        <div class="returnables-header">
            <h2>Gestion des Emballages Consignés</h2>
            <div class="header-actions">
                <button @click="printInvoice" class="btn btn-print">
                    <i class="fas fa-print"></i>
                    Imprimer Facture
                </button>
                <input
                    type="text"
                    class="search-input"
                    placeholder="Rechercher par client ou produit..."
                    v-model="searchQuery"
                />
            </div>
        </div>

        <!-- Filters Section -->
        <div class="filters-section">
            <select class="filter-select" v-model="filterClient">
                <option value="">Tous les clients</option>
                <option
                    v-for="client in uniqueClients"
                    :key="client"
                    :value="client"
                >
                    {{ client }}
                </option>
            </select>

            <select class="filter-select" v-model="filterStatus">
                <option value="">Tous les statuts</option>
                <option value="pending">Non retournés</option>
                <option value="partial">Partiellement retournés</option>
                <option value="complete">Tous retournés</option>
            </select>

            <select class="filter-select" v-model="dateFilterMode">
                <option value="">Toutes les dates</option>
                <option value="specific">Date précise</option>
                <option value="range">Entre deux dates</option>
            </select>

            <input
                v-if="dateFilterMode === 'specific'"
                type="date"
                class="filter-select"
                v-model="filterDate"
            />

            <div v-if="dateFilterMode === 'range'" class="date-range">
                <input
                    type="date"
                    class="filter-select"
                    v-model="filterDateStart"
                />
                <span>à</span>
                <input
                    type="date"
                    class="filter-select"
                    v-model="filterDateEnd"
                />
            </div>

            <select class="filter-select" v-model="sortOption">
                <option>Date (récent)</option>
                <option>Date (ancien)</option>
                <option>Client (A-Z)</option>
                <option>Client (Z-A)</option>
                <option>Quantité restante (croissant)</option>
                <option>Quantité restante (décroissant)</option>
            </select>
        </div>

        <!-- Returnables Table -->
        <div class="table-container" v-if="filteredReturnables.length > 0">
            <table class="table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Produit</th>
                        <th>Qté Achetée</th>
                        <th>Qté Retournée</th>
                        <th>Qté Restante</th>
                        <th>Statut</th>
                        <th>Date d'achat</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in paginatedReturnables" :key="item.id">
                        <td>{{ item.client_name }}</td>
                        <td>{{ item.product_name }}</td>
                        <td class="text-center">
                            {{ item.quantity_purchased }}
                        </td>
                        <td class="text-center">
                            {{ item.quantity_returned }}
                        </td>
                        <td class="text-center">
                            <strong>
                                {{
                                    item.quantity_purchased -
                                    item.quantity_returned
                                }}
                            </strong>
                        </td>
                        <td>
                            <span :style="getStatusBadgeStyle(item)">
                                {{ getStatusText(item) }}
                            </span>
                        </td>
                        <td>{{ formatDate(item.created_at) }}</td>
                        <td>
                            <div class="action-buttons">
                                <button
                                    v-if="
                                        item.quantity_purchased -
                                            item.quantity_returned >
                                        0
                                    "
                                    class="btn-sm btn-return"
                                    title="Retourner des emballages"
                                    @click="openReturnModal(item)"
                                >
                                    <i class="fas fa-undo-alt"></i>
                                </button>
                                <button
                                    class="btn-sm btn-delete"
                                    title="Supprimer"
                                    @click="confirmDeleteReturnable(item)"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                <button
                    @click="currentPage--"
                    :disabled="currentPage === 1"
                    class="pagination-btn"
                >
                    Précédent
                </button>
                <span class="pagination-info">
                    Page {{ currentPage }} sur {{ totalPages }}
                </span>
                <button
                    @click="currentPage++"
                    :disabled="currentPage === totalPages"
                    class="pagination-btn"
                >
                    Suivant
                </button>
            </div>
        </div>

        <div v-else class="empty-state">
            <i class="fas fa-box-open"></i>
            <p>Aucun emballage consigné trouvé</p>
        </div>

        <!-- Modal Retourner des Emballages -->
        <div
            v-if="showReturnModal"
            class="modal-overlay"
            @click.self="closeReturnModal()"
        >
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Retourner des emballages</h3>
                    <span class="close" @click="closeReturnModal()">
                        &times;
                    </span>
                </div>
                <div class="modal-body">
                    <p>
                        <strong>Client:</strong>
                        {{ returnItem.client_name }}
                    </p>
                    <p>
                        <strong>Produit:</strong>
                        {{ returnItem.product_name }}
                    </p>
                    <p>
                        <strong>Quantité disponible à retourner:</strong>
                        {{
                            returnItem.quantity_purchased -
                            returnItem.quantity_returned
                        }}
                    </p>

                    <div class="form-group">
                        <label for="returnQuantity">
                            Quantité à retourner
                            <span class="required">*</span>
                        </label>
                        <input
                            type="number"
                            id="returnQuantity"
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
                </div>
                <div class="modal-footer">
                    <button
                        class="btn btn-secondary"
                        @click="closeReturnModal()"
                    >
                        Annuler
                    </button>
                    <button class="btn btn-primary" @click="submitReturn()">
                        Enregistrer le retour
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Confirmation Suppression -->
        <div
            v-if="showDeleteConfirmation"
            class="modal-overlay"
            @click.self="showDeleteConfirmation = false"
        >
            <div class="modal-content" style="padding: 2rem">
                <div class="modal-header">
                    <h3>Confirmation de suppression</h3>
                    <span
                        class="close"
                        @click="showDeleteConfirmation = false"
                        style="cursor: pointer; font-size: 1.5rem; color: #999"
                    >
                        &times;
                    </span>
                </div>

                <p style="margin-bottom: 1.5rem">
                    Êtes-vous sûr de vouloir supprimer cet enregistrement pour
                    <strong>{{ returnableToDelete.client_name }}</strong>
                    -
                    <strong>{{ returnableToDelete.product_name }}</strong>
                    ?
                </p>

                <div
                    style="
                        display: flex;
                        gap: 1rem;
                        justify-content: flex-end;
                        margin-top: 2rem;
                    "
                >
                    <button
                        class="btn"
                        @click="showDeleteConfirmation = false"
                        style="background: #6c757d; color: white"
                    >
                        Annuler
                    </button>
                    <button
                        class="btn"
                        @click="deleteReturnable"
                        style="background: #dc3545; color: white"
                    >
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'ReturnableProductsComponent',
        data() {
            return {
                returnables: [],
                showReturnModal: false,
                showDeleteConfirmation: false,
                returnableToDelete: null,
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
                dateFilterMode: '',
                filterDate: '',
                filterDateStart: '',
                filterDateEnd: '',
                sortOption: 'Date (récent)',
                currentPage: 1,
                itemsPerPage: 10,
            };
        },
        mounted() {
            this.fetchReturnables();
        },
        methods: {
            async fetchReturnables() {
                try {
                    const response = await axios.get(
                        `${window.location.origin}/returnableProductsList`
                    );
                    this.returnables = response.data;
                } catch (error) {
                    console.error(
                        'Erreur lors de la récupération des emballages:',
                        error
                    );
                }
            },

            openReturnModal(item) {
                this.returnItem = { ...item };
                this.returnQuantity = 0;
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

                try {
                    const response = await axios.post(
                        `${window.location.origin}/returnableProducts/${this.returnItem.id}/return`,
                        {
                            quantity_returned: this.returnQuantity,
                        }
                    );

                    if (response.data.success) {
                        await this.fetchReturnables();
                        this.closeReturnModal();
                        alert('Retour enregistré avec succès');
                    }
                } catch (error) {
                    console.error(
                        "Erreur lors de l'enregistrement du retour:",
                        error
                    );
                    alert("Erreur lors de l'enregistrement du retour");
                }
            },

            confirmDeleteReturnable(item) {
                this.returnableToDelete = item;
                this.showDeleteConfirmation = true;
            },

            async deleteReturnable() {
                if (!this.returnableToDelete) return;

                try {
                    const response = await axios.delete(
                        `${window.location.origin}/returnableProducts/${this.returnableToDelete.id}`
                    );
                    if (response.data.success) {
                        await this.fetchReturnables();
                        this.showDeleteConfirmation = false;
                        this.returnableToDelete = null;
                        alert('Enregistrement supprimé avec succès');
                    }
                } catch (error) {
                    console.error('Erreur lors de la suppression:', error);
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

            printInvoice() {
                const clientData = {};

                // Group by client
                this.filteredReturnables.forEach((item) => {
                    if (!clientData[item.client_name]) {
                        clientData[item.client_name] = [];
                    }
                    clientData[item.client_name].push(item);
                });

                // Generate invoice HTML for each client
                let invoicesHTML = '';

                Object.keys(clientData).forEach((clientName, index) => {
                    const items = clientData[clientName];
                    let totalPurchased = 0;
                    let totalReturned = 0;
                    let totalPending = 0;

                    const tableRows = items
                        .map((item, idx) => {
                            const remaining =
                                item.quantity_purchased -
                                item.quantity_returned;
                            totalPurchased += item.quantity_purchased;
                            totalReturned += item.quantity_returned;
                            totalPending += remaining;

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
                                <h1 style="color: #667eea; margin: 0; font-size: 2rem;">SAGER MARKET</h1>
                                <p style="margin: 5px 0; color: #666;">Téléphone: +229 61 11 00 66</p>
                                <p style="margin: 5px 0; color: #666;">IFU: 1202411712985</p>
                            </div>
                            
                            <h2 style="text-align: center; color: #764ba2; margin-bottom: 30px;">FACTURE EMBALLAGES CONSIGNÉS</h2>
                            
                            <div class="client-info" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #667eea;">
                                <p style="margin: 5px 0;"><strong>Client:</strong> ${clientName}</p>
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
                                        <td style="padding: 12px; border: 1px solid #ddd; text-align: center;">${totalPurchased}</td>
                                        <td style="padding: 12px; border: 1px solid #ddd; text-align: center;">${totalReturned}</td>
                                        <td style="padding: 12px; border: 1px solid #ddd; text-align: center; color: ${
                                            totalPending > 0
                                                ? '#c62828'
                                                : '#388e3c'
                                        };">${totalPending}</td>
                                        <td style="padding: 12px; border: 1px solid #ddd;"></td>
                                    </tr>
                                </tfoot>
                            </table>
                            
                            <div class="observation" style="margin-top: 30px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
                                <p style="margin: 0; color: #856404;"><strong>Observation:</strong> Les emballages consignés restent la propriété de SAGER MARKET jusqu'à leur retour complet.</p>
                            </div>
                            
                            <div class="footer" style="margin-top: 40px; text-align: center; color: #666; font-size: 0.9rem;">
                                <p>Merci de votre confiance</p>
                            </div>
                        </div>
                    `;
                });

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
                            @media print {
                                .invoice-page {
                                    page-break-after: always;
                                }
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
        },
        computed: {
            filteredReturnables() {
                let filtered = this.returnables;

                // Search filter
                if (this.searchQuery) {
                    const query = this.searchQuery.toLowerCase();
                    filtered = filtered.filter(
                        (item) =>
                            item.client_name.toLowerCase().includes(query) ||
                            item.product_name.toLowerCase().includes(query)
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

                // Date filter
                if (this.dateFilterMode === 'specific' && this.filterDate) {
                    filtered = filtered.filter(
                        (item) =>
                            item.created_at.split(' ')[0] === this.filterDate
                    );
                }

                if (
                    this.dateFilterMode === 'range' &&
                    this.filterDateStart &&
                    this.filterDateEnd
                ) {
                    const start = new Date(this.filterDateStart);
                    const end = new Date(this.filterDateEnd);
                    filtered = filtered.filter((item) => {
                        const date = new Date(item.created_at);
                        return date >= start && date <= end;
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
                            a.client_name.localeCompare(b.client_name)
                        );
                        break;
                    case 'Client (Z-A)':
                        filtered.sort((a, b) =>
                            b.client_name.localeCompare(a.client_name)
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
                ];
            },

            totalPages() {
                return Math.ceil(
                    this.filteredReturnables.length / this.itemsPerPage
                );
            },

            paginatedReturnables() {
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

    .statistics-section {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        display: flex;
        align-items: center;
        gap: 1.5rem;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .stat-card-1 {
        border-left: 4px solid #667eea;
    }

    .stat-card-2 {
        border-left: 4px solid #764ba2;
    }

    .stat-card-3 {
        border-left: 4px solid #f57c00;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .stat-info {
        flex: 1;
    }

    .stat-label {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: bold;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .returnables-header {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        margin-bottom: 1.5rem;
    }

    .returnables-header h2 {
        margin: 0 0 1rem 0;
        color: #333;
        font-size: 1.5rem;
    }

    .header-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        align-items: center;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-print {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-print:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    .search-input {
        flex: 1;
        min-width: 250px;
        padding: 0.75rem 1rem;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .search-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .filters-section {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        margin-bottom: 1.5rem;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        align-items: center;
    }

    .filter-select {
        padding: 0.75rem 1rem;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 1rem;
        background: white;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 180px;
    }

    .filter-select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .date-range {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .table-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table td {
        padding: 1rem;
        border-bottom: 1px solid #f0f0f0;
    }

    .table tbody tr {
        transition: background-color 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .text-center {
        text-align: center;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
    }

    .btn-sm {
        padding: 0.5rem 0.75rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .btn-return {
        background-color: #28a745;
        color: white;
    }

    .btn-return:hover {
        background-color: #218838;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
    }

    .btn-delete {
        background-color: #dc3545;
        color: white;
    }

    .btn-delete:hover {
        background-color: #c82333;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
    }

    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
        background: white;
    }

    .pagination-btn {
        padding: 0.5rem 1rem;
        border: 2px solid #667eea;
        background: white;
        color: #667eea;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .pagination-btn:hover:not(:disabled) {
        background: #667eea;
        color: white;
        transform: translateY(-2px);
    }

    .pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .pagination-info {
        font-weight: 500;
        color: #666;
    }

    .empty-state {
        background: white;
        padding: 4rem 2rem;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .empty-state i {
        font-size: 4rem;
        color: #ccc;
        margin-bottom: 1rem;
    }

    .empty-state p {
        color: #999;
        font-size: 1.2rem;
    }

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
        z-index: 1000;
        backdrop-filter: blur(4px);
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 2px solid #f0f0f0;
    }

    .modal-header h3 {
        margin: 0;
        color: #333;
        font-size: 1.3rem;
    }

    .close {
        cursor: pointer;
        font-size: 1.5rem;
        color: #999;
        transition: color 0.3s ease;
    }

    .close:hover {
        color: #333;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-body p {
        margin-bottom: 1rem;
        color: #666;
    }

    .form-group {
        margin-top: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: #333;
        font-weight: 500;
    }

    .required {
        color: #dc3545;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        padding: 1.5rem;
        border-top: 2px solid #f0f0f0;
    }

    @media print {
        .returnables-content {
            background: white;
            padding: 0;
        }

        .statistics-section,
        .returnables-header,
        .filters-section,
        .action-buttons,
        .pagination {
            display: none !important;
        }

        .table-container {
            box-shadow: none;
        }
    }
</style>
