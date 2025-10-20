<template>
    <div>
        <div class="sales-content">
            <!-- Main clients list view -->
            <div class="sales-history" v-if="!showClientDetails">
                <div class="history-header">
                    <h3>Gestion des Clients</h3>
                    <div class="header-actions">
                        <button @click="printClientsList" class="btn-print-all">
                            <i class="fas fa-print"></i>
                            Imprimer la Liste
                        </button>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="filters-container">
                    <input
                        type="text"
                        v-model="searchQuery"
                        class="search-input"
                        placeholder="Rechercher un client..."
                    />
                    <select v-model="sortOption" class="filter-select">
                        <option value="name_asc">Nom (A-Z)</option>
                        <option value="name_desc">Nom (Z-A)</option>
                        <option value="purchases_desc">Plus d'achats</option>
                        <option value="debt_desc">Plus de créances</option>
                        <option value="recent">Achat récent</option>
                    </select>
                </div>

                <!-- Clients Table -->
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Téléphone</th>
                                <th>Total Achats</th>
                                <th>Créance</th>
                                <th>Dernier Achat</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="client in paginatedClients"
                                :key="client.id"
                            >
                                <td data-label="Client">
                                    <strong>{{ client.name }}</strong>
                                </td>
                                <td data-label="Téléphone">
                                    {{ client.phone || 'N/A' }}
                                </td>
                                <td data-label="Total Achats">
                                    <strong style="color: #28a745">
                                        {{
                                            formatAmount(client.total_purchases)
                                        }}
                                        FCFA
                                    </strong>
                                </td>
                                <td data-label="Créance">
                                    <span
                                        :style="{
                                            color:
                                                client.total_debt > 0
                                                    ? '#dc3545'
                                                    : '#28a745',
                                            fontWeight: 'bold',
                                        }"
                                    >
                                        {{ formatAmount(client.total_debt) }}
                                        FCFA
                                    </span>
                                </td>
                                <td data-label="Dernier Achat">
                                    {{
                                        client.last_purchase_date
                                            ? formatDateTime(
                                                  client.last_purchase_date
                                              )
                                            : 'Aucun'
                                    }}
                                </td>
                                <td data-label="Actions">
                                    <div class="action-buttons">
                                        <button
                                            @click="
                                                viewClientDetails(client.id)
                                            "
                                            class="action-btn view-btn"
                                            title="Voir détails"
                                        >
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button
                                            v-if="client.phone"
                                            @click="callClient(client.phone)"
                                            class="action-btn call-btn"
                                            title="Appeler"
                                        >
                                            <i class="fas fa-phone"></i>
                                        </button>
                                        <button
                                            v-if="client.phone"
                                            @click="
                                                whatsappClient(client.phone)
                                            "
                                            class="action-btn whatsapp-btn"
                                            title="WhatsApp"
                                        >
                                            <i class="fab fa-whatsapp"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Empty State -->
                    <div v-if="clients.length === 0" class="empty-state">
                        <i class="fas fa-users"></i>
                        <p>Aucun client trouvé</p>
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

            <!-- Client Details View -->
            <div class="showStock" v-if="showClientDetails">
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
                                @click="closeClientDetails()"
                                style="
                                    display: flex;
                                    align-items: center;
                                    cursor: pointer;
                                    color: #007bff;
                                    font-weight: bold;
                                "
                            >
                                <i class="fas fa-arrow-left"></i>
                                <span style="margin-left: 5px">Retour</span>
                            </span>
                            | Détails de
                            <strong>{{ selectedClient.name }}</strong>
                        </h3>
                        <div
                            style="
                                display: flex;
                                gap: 10px;
                                align-items: center;
                            "
                        >
                            <button
                                @click="printClientReport"
                                class="btn-print-history"
                            >
                                <i class="fas fa-print"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Client Summary Card -->
                    <div class="client-summary-card">
                        <div class="summary-item">
                            <span class="summary-label">Téléphone:</span>
                            <span class="summary-value">
                                {{ selectedClient.phone || 'N/A' }}
                                <button
                                    v-if="selectedClient.phone"
                                    @click="callClient(selectedClient.phone)"
                                    class="inline-action-btn"
                                    title="Appeler"
                                >
                                    <i class="fas fa-phone"></i>
                                </button>
                                <button
                                    v-if="selectedClient.phone"
                                    @click="
                                        whatsappClient(selectedClient.phone)
                                    "
                                    class="inline-action-btn whatsapp"
                                    title="WhatsApp"
                                >
                                    <i class="fab fa-whatsapp"></i>
                                </button>
                            </span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Total Achats:</span>
                            <span class="summary-value" style="color: #28a745">
                                {{
                                    formatAmount(selectedClient.total_purchases)
                                }}
                                FCFA
                            </span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Créance Totale:</span>
                            <span
                                class="summary-value"
                                :style="{
                                    color:
                                        selectedClient.total_debt > 0
                                            ? '#dc3545'
                                            : '#28a745',
                                }"
                            >
                                {{ formatAmount(selectedClient.total_debt) }}
                                FCFA
                            </span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Total Payé:</span>
                            <span class="summary-value" style="color: #667eea">
                                {{ formatAmount(selectedClient.total_paid) }}
                                FCFA
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="detail-actions">
                        <button @click="openAddDebtModal" class="btn-add-debt">
                            <i class="fas fa-plus"></i>
                            Ajouter Créance
                        </button>
                        <button
                            @click="openAddPaymentModal"
                            class="btn-add-payment"
                        >
                            <i class="fas fa-money-bill"></i>
                            Enregistrer Paiement
                        </button>
                    </div>

                    <!-- Tabs for History -->
                    <div class="tabs-container">
                        <button
                            @click="activeTab = 'purchases'"
                            :class="[
                                'tab-btn',
                                { active: activeTab === 'purchases' },
                            ]"
                        >
                            <i class="fas fa-shopping-cart"></i>
                            Historique d'Achats
                        </button>
                        <button
                            @click="activeTab = 'payments'"
                            :class="[
                                'tab-btn',
                                { active: activeTab === 'payments' },
                            ]"
                        >
                            <i class="fas fa-receipt"></i>
                            Historique de Paiements
                        </button>
                        <button
                            @click="activeTab = 'debts'"
                            :class="[
                                'tab-btn',
                                { active: activeTab === 'debts' },
                            ]"
                        >
                            <i class="fas fa-file-invoice-dollar"></i>
                            Créances
                        </button>
                    </div>

                    <!-- Purchase History Tab -->
                    <div v-if="activeTab === 'purchases'" class="tab-content">
                        <h4>Historique des Achats</h4>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>N° Facture</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="purchase in purchaseHistory"
                                    :key="purchase.id"
                                >
                                    <td data-label="Date">
                                        {{
                                            formatDateTime(purchase.created_at)
                                        }}
                                    </td>
                                    <td data-label="N° Facture">
                                        <strong>N° {{ purchase.id }}</strong>
                                    </td>
                                    <td data-label="Montant">
                                        <strong style="color: #28a745">
                                            {{ formatAmount(purchase.total) }}
                                            FCFA
                                        </strong>
                                    </td>
                                    <td data-label="Statut">
                                        <span
                                            :class="
                                                getStatusClass(purchase.status)
                                            "
                                            class="status-badge"
                                        >
                                            {{
                                                purchase.status === 'done'
                                                    ? 'Terminée'
                                                    : 'Annulée'
                                            }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div
                            v-if="purchaseHistory.length === 0"
                            class="empty-state"
                        >
                            <i class="fas fa-shopping-cart"></i>
                            <p>Aucun achat trouvé</p>
                        </div>
                    </div>

                    <!-- Payment History Tab -->
                    <div v-if="activeTab === 'payments'" class="tab-content">
                        <h4>Historique des Paiements</h4>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Montant</th>
                                    <th>Méthode</th>
                                    <th>Commentaire</th>
                                    <th>Justificatif</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="payment in paymentHistory"
                                    :key="payment.id"
                                >
                                    <td data-label="Date">
                                        {{ formatDateTime(payment.created_at) }}
                                    </td>
                                    <td data-label="Montant">
                                        <strong style="color: #28a745">
                                            {{ formatAmount(payment.amount) }}
                                            FCFA
                                        </strong>
                                    </td>
                                    <td data-label="Méthode">
                                        {{ payment.payment_method || 'N/A' }}
                                    </td>
                                    <td data-label="Commentaire">
                                        {{ payment.comment || '-' }}
                                    </td>
                                    <td data-label="Justificatif">
                                        <button
                                            v-if="payment.receipt_url"
                                            @click="
                                                viewReceipt(payment.receipt_url)
                                            "
                                            class="view-receipt-btn"
                                        >
                                            <i class="fas fa-file-image"></i>
                                            Voir
                                        </button>
                                        <span v-else>-</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div
                            v-if="paymentHistory.length === 0"
                            class="empty-state"
                        >
                            <i class="fas fa-receipt"></i>
                            <p>Aucun paiement trouvé</p>
                        </div>
                    </div>

                    <!-- Debts Tab -->
                    <div v-if="activeTab === 'debts'" class="tab-content">
                        <h4>Créances</h4>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Montant</th>
                                    <th>Commentaire</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="debt in debtHistory" :key="debt.id">
                                    <td data-label="Date">
                                        {{ formatDateTime(debt.created_at) }}
                                    </td>
                                    <td data-label="Montant">
                                        <strong style="color: #dc3545">
                                            {{ formatAmount(debt.amount) }} FCFA
                                        </strong>
                                    </td>
                                    <td data-label="Commentaire">
                                        {{ debt.comment || '-' }}
                                    </td>
                                    <td data-label="Statut">
                                        <span
                                            :class="
                                                debt.is_paid
                                                    ? 'status-paid'
                                                    : 'status-unpaid'
                                            "
                                            class="status-badge"
                                        >
                                            {{
                                                debt.is_paid
                                                    ? 'Payée'
                                                    : 'Impayée'
                                            }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div
                            v-if="debtHistory.length === 0"
                            class="empty-state"
                        >
                            <i class="fas fa-file-invoice-dollar"></i>
                            <p>Aucune créance trouvée</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Debt Modal -->
        <div
            v-if="showAddDebtModal"
            class="modal-overlay"
            @click.self="closeAddDebtModal"
        >
            <div class="modal-container">
                <div class="modal-header">
                    <h5>Ajouter une Créance</h5>
                    <button @click="closeAddDebtModal" class="modal-close">
                        &times;
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Montant (FCFA)</label>
                        <input
                            v-model.number="newDebt.amount"
                            type="number"
                            min="0"
                            step="0.01"
                            class="form-control"
                            placeholder="Entrer le montant"
                            required
                        />
                    </div>
                    <div class="form-group">
                        <label>Commentaire</label>
                        <textarea
                            v-model="newDebt.comment"
                            class="form-control"
                            rows="3"
                            placeholder="Raison de la créance..."
                        ></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button @click="closeAddDebtModal" class="btn-cancel">
                        Annuler
                    </button>
                    <button @click="addDebt" class="btn-submit">Ajouter</button>
                </div>
            </div>
        </div>

        <!-- Add Payment Modal -->
        <div
            v-if="showAddPaymentModal"
            class="modal-overlay"
            @click.self="closeAddPaymentModal"
        >
            <div class="modal-container">
                <div class="modal-header">
                    <h5>Enregistrer un Paiement</h5>
                    <button @click="closeAddPaymentModal" class="modal-close">
                        &times;
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Montant Payé (FCFA)</label>
                        <input
                            v-model.number="newPayment.amount"
                            type="number"
                            min="0"
                            step="0.01"
                            class="form-control"
                            placeholder="Entrer le montant payé"
                            required
                        />
                    </div>
                    <div class="form-group">
                        <label>Méthode de Paiement</label>
                        <select
                            v-model="newPayment.payment_method"
                            class="form-control"
                        >
                            <option value="">Sélectionner...</option>
                            <option value="Espèces">Espèces</option>
                            <option value="Mobile Money">Mobile Money</option>
                            <option value="Virement">Virement</option>
                            <option value="Chèque">Chèque</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Justificatif de Paiement</label>
                        <input
                            type="file"
                            @change="handleReceiptUpload"
                            class="form-control"
                            accept="image/*,application/pdf"
                        />
                    </div>
                    <div class="form-group">
                        <label>Commentaire</label>
                        <textarea
                            v-model="newPayment.comment"
                            class="form-control"
                            rows="3"
                            placeholder="Commentaire optionnel..."
                        ></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button @click="closeAddPaymentModal" class="btn-cancel">
                        Annuler
                    </button>
                    <button @click="addPayment" class="btn-submit">
                        Enregistrer
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'ClientsComponent',

        data() {
            return {
                clients: [],
                selectedClient: null,
                showClientDetails: false,
                purchaseHistory: [],
                paymentHistory: [],
                debtHistory: [],
                activeTab: 'purchases',

                // Modals
                showAddDebtModal: false,
                showAddPaymentModal: false,

                // Forms
                newDebt: {
                    amount: 0,
                    comment: '',
                },
                newPayment: {
                    amount: 0,
                    payment_method: '',
                    receipt: null,
                    comment: '',
                },

                // Filters and Search
                searchQuery: '',
                sortOption: 'name_asc',

                // Pagination
                currentPage: 1,
                perPage: 10,
            };
        },

        mounted() {
            this.fetchClientsData();
        },

        methods: {
            fetchClientsData() {
                // Fix: Declare axios as a global variable or import it.
                // For this example, assuming axios is globally available via a CDN.
                axios
                    .get('/clientsList')
                    .then((response) => {
                        if (Array.isArray(response.data)) {
                            this.clients = response.data;
                        } else if (
                            response.data.success &&
                            Array.isArray(response.data.data)
                        ) {
                            this.clients = response.data.data;
                        } else {
                            this.clients = [];
                        }
                    })
                    .catch((error) => {
                        console.error('Error fetching clients:', error);
                        this.clients = [];
                        alert('Erreur lors de la récupération des clients');
                    });
            },

            viewClientDetails(clientId) {
                axios
                    .get(`/clients/${clientId}/details`)
                    .then((response) => {
                        this.selectedClient = response.data.client;
                        this.purchaseHistory =
                            response.data.purchase_history || [];
                        this.paymentHistory =
                            response.data.payment_history || [];
                        this.debtHistory = response.data.debt_history || [];
                        this.showClientDetails = true;
                        this.activeTab = 'purchases';
                    })
                    .catch((error) => {
                        console.error('Error fetching client details:', error);
                        alert(
                            'Erreur lors de la récupération des détails du client'
                        );
                    });
            },

            closeClientDetails() {
                this.showClientDetails = false;
                this.selectedClient = null;
                this.purchaseHistory = [];
                this.paymentHistory = [];
                this.debtHistory = [];
            },

            openAddDebtModal() {
                this.showAddDebtModal = true;
            },

            closeAddDebtModal() {
                this.showAddDebtModal = false;
                this.newDebt = {
                    amount: 0,
                    comment: '',
                };
            },

            addDebt() {
                if (this.newDebt.amount <= 0) {
                    alert('Le montant doit être supérieur à 0');
                    return;
                }

                axios
                    .post(`/clients/${this.selectedClient.id}/debt`, {
                        amount: this.newDebt.amount,
                        comment: this.newDebt.comment,
                    })
                    .then((response) => {
                        alert('Créance ajoutée avec succès');
                        this.closeAddDebtModal();
                        this.viewClientDetails(this.selectedClient.id);
                        this.fetchClientsData();
                    })
                    .catch((error) => {
                        console.error('Error adding debt:', error);
                        alert("Erreur lors de l'ajout de la créance");
                    });
            },

            openAddPaymentModal() {
                this.showAddPaymentModal = true;
            },

            closeAddPaymentModal() {
                this.showAddPaymentModal = false;
                this.newPayment = {
                    amount: 0,
                    payment_method: '',
                    receipt: null,
                    comment: '',
                };
            },

            handleReceiptUpload(event) {
                this.newPayment.receipt = event.target.files[0];
            },

            addPayment() {
                if (this.newPayment.amount <= 0) {
                    alert('Le montant doit être supérieur à 0');
                    return;
                }

                const formData = new FormData();
                formData.append('amount', this.newPayment.amount);
                formData.append(
                    'payment_method',
                    this.newPayment.payment_method
                );
                formData.append('comment', this.newPayment.comment);
                if (this.newPayment.receipt) {
                    formData.append('receipt', this.newPayment.receipt);
                }

                axios
                    .post(
                        `/clients/${this.selectedClient.id}/payment`,
                        formData,
                        {
                            headers: {
                                'Content-Type': 'multipart/form-data',
                            },
                        }
                    )
                    .then((response) => {
                        alert('Paiement enregistré avec succès');
                        this.closeAddPaymentModal();
                        this.viewClientDetails(this.selectedClient.id);
                        this.fetchClientsData();
                    })
                    .catch((error) => {
                        console.error('Error adding payment:', error);
                        alert("Erreur lors de l'enregistrement du paiement");
                    });
            },

            callClient(phone) {
                window.location.href = `tel:${phone}`;
            },

            whatsappClient(phone) {
                const cleanPhone = phone.replace(/\D/g, '');
                window.open(`https://wa.me/${cleanPhone}`, '_blank');
            },

            viewReceipt(url) {
                window.open(url, '_blank');
            },

            printClientsList() {
                const printWindow = window.open('', '_blank');
                const printContent = this.generateClientsListPrint();

                printWindow.document.write(printContent);
                printWindow.document.close();
                printWindow.focus();

                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 250);
            },

            generateClientsListPrint() {
                let html = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Liste des Clients</title>
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
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 20px;
                        }
                        th {
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            color: white;
                            padding: 15px;
                            text-align: left;
                            font-weight: 600;
                        }
                        td {
                            padding: 12px 15px;
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
                        <h1>Liste des Clients</h1>
                    </div>
                    <div class="print-info">
                        <p><strong>Date d'impression:</strong> ${new Date().toLocaleString(
                            'fr-FR'
                        )}</p>
                        <p><strong>Nombre de clients:</strong> ${
                            this.filteredClients.length
                        }</p>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Téléphone</th>
                                <th>Total Achats</th>
                                <th>Créance</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

                this.filteredClients.forEach((client) => {
                    html += `
                    <tr>
                        <td><strong>${client.name}</strong></td>
                        <td>${client.phone || 'N/A'}</td>
                        <td><strong style="color: #28a745;">${this.formatAmount(
                            client.total_purchases
                        )} FCFA</strong></td>
                        <td><strong style="color: ${
                            client.total_debt > 0 ? '#dc3545' : '#28a745'
                        };">${this.formatAmount(
                        client.total_debt
                    )} FCFA</strong></td>
                    </tr>
                `;
                });

                html += `
                        </tbody>
                    </table>
                    <div class="footer">
                        <p>Document généré automatiquement - Système de Gestion des Clients</p>
                    </div>
                </body>
                </html>
            `;

                return html;
            },

            printClientReport() {
                const printWindow = window.open('', '_blank');
                const printContent = this.generateClientReportPrint();

                printWindow.document.write(printContent);
                printWindow.document.close();
                printWindow.focus();

                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 250);
            },

            generateClientReportPrint() {
                let html = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Rapport Client - ${this.selectedClient.name}</title>
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
                        .client-info {
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            color: white;
                            padding: 20px;
                            border-radius: 10px;
                            margin-bottom: 30px;
                        }
                        .client-info h2 {
                            margin-bottom: 15px;
                        }
                        .info-grid {
                            display: grid;
                            grid-template-columns: repeat(2, 1fr);
                            gap: 15px;
                        }
                        .info-item {
                            padding: 10px;
                            background: rgba(255, 255, 255, 0.1);
                            border-radius: 5px;
                        }
                        .section-title {
                            color: #667eea;
                            font-size: 1.5rem;
                            margin: 30px 0 15px 0;
                            padding-bottom: 10px;
                            border-bottom: 2px solid #667eea;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-bottom: 30px;
                        }
                        th {
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            color: white;
                            padding: 12px;
                            text-align: left;
                            font-weight: 600;
                        }
                        td {
                            padding: 10px 12px;
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
                        <h1>Rapport Client</h1>
                    </div>
                    <div class="client-info">
                        <h2>${this.selectedClient.name}</h2>
                        <div class="info-grid">
                            <div class="info-item">
                                <strong>Téléphone:</strong> ${
                                    this.selectedClient.phone || 'N/A'
                                }
                            </div>
                            <div class="info-item">
                                <strong>Total Achats:</strong> ${this.formatAmount(
                                    this.selectedClient.total_purchases
                                )} FCFA
                            </div>
                            <div class="info-item">
                                <strong>Créance Totale:</strong> ${this.formatAmount(
                                    this.selectedClient.total_debt
                                )} FCFA
                            </div>
                            <div class="info-item">
                                <strong>Total Payé:</strong> ${this.formatAmount(
                                    this.selectedClient.total_paid
                                )} FCFA
                            </div>
                        </div>
                    </div>

                    <h3 class="section-title">Historique des Achats</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>N° Facture</th>
                                <th>Montant</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

                this.purchaseHistory.forEach((purchase) => {
                    html += `
                    <tr>
                        <td>${this.formatDateTime(purchase.created_at)}</td>
                        <td>N° ${purchase.id}</td>
                        <td><strong>${this.formatAmount(
                            purchase.total
                        )} FCFA</strong></td>
                        <td>${
                            purchase.status === 'done' ? 'Terminée' : 'Annulée'
                        }</td>
                    </tr>
                `;
                });

                html += `
                        </tbody>
                    </table>

                    <h3 class="section-title">Historique des Paiements</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Montant</th>
                                <th>Méthode</th>
                                <th>Commentaire</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

                this.paymentHistory.forEach((payment) => {
                    html += `
                    <tr>
                        <td>${this.formatDateTime(payment.created_at)}</td>
                        <td><strong>${this.formatAmount(
                            payment.amount
                        )} FCFA</strong></td>
                        <td>${payment.payment_method || 'N/A'}</td>
                        <td>${payment.comment || '-'}</td>
                    </tr>
                `;
                });

                html += `
                        </tbody>
                    </table>

                    <div class="footer">
                        <p><strong>Date d'impression:</strong> ${new Date().toLocaleString(
                            'fr-FR'
                        )}</p>
                        <p>Document généré automatiquement - Système de Gestion des Clients</p>
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

            formatAmount(value) {
                return Number(value).toLocaleString('fr-FR', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2,
                });
            },

            getStatusClass(status) {
                if (status === 'done') {
                    return 'status-completed';
                } else if (status === 'cancelled') {
                    return 'status-cancelled';
                }
                return '';
            },

            goToPage(page) {
                if (page >= 1 && page <= this.totalPages) {
                    this.currentPage = page;
                }
            },
        },

        computed: {
            filteredClients() {
                let filtered = [...this.clients];

                // Search filter
                if (this.searchQuery) {
                    const query = this.searchQuery.toLowerCase();
                    filtered = filtered.filter(
                        (client) =>
                            client.name.toLowerCase().includes(query) ||
                            (client.phone &&
                                client.phone.toLowerCase().includes(query))
                    );
                }

                // Sort
                switch (this.sortOption) {
                    case 'name_asc':
                        filtered.sort((a, b) => a.name.localeCompare(b.name));
                        break;
                    case 'name_desc':
                        filtered.sort((a, b) => b.name.localeCompare(a.name));
                        break;
                    case 'purchases_desc':
                        filtered.sort(
                            (a, b) => b.total_purchases - a.total_purchases
                        );
                        break;
                    case 'debt_desc':
                        filtered.sort((a, b) => b.total_debt - a.total_debt);
                        break;
                    case 'recent':
                        filtered.sort(
                            (a, b) =>
                                new Date(b.last_purchase_date) -
                                new Date(a.last_purchase_date)
                        );
                        break;
                }

                return filtered;
            },

            totalPages() {
                return Math.ceil(this.filteredClients.length / this.perPage);
            },

            paginatedClients() {
                const start = (this.currentPage - 1) * this.perPage;
                const end = start + this.perPage;
                return this.filteredClients.slice(start, end);
            },
        },
    };
</script>

<style scoped>
    .sales-content {
        padding: 20px;
    }

    .sales-history {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }

    .history-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .history-header h3 {
        margin: 0;
        color: #333;
        font-size: 1.5rem;
    }

    .header-actions {
        display: flex;
        gap: 10px;
    }

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
        background: #17a2b8;
        color: white;
    }

    .btn-print-all:hover {
        background: #138496;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(23, 162, 184, 0.3);
    }

    .filters-container {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .search-input {
        flex: 1;
        min-width: 250px;
        padding: 10px 15px;
        border: 2px solid #e1e5e9;
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.3s ease;
    }

    .search-input:focus {
        outline: none;
        border-color: #667eea;
    }

    .filter-select {
        padding: 10px 15px;
        border: 2px solid #e1e5e9;
        border-radius: 8px;
        font-size: 1rem;
        cursor: pointer;
        transition: border-color 0.3s ease;
    }

    .filter-select:focus {
        outline: none;
        border-color: #667eea;
    }

    .table-container {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .table th,
    .table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }

    .table th {
        background-color: #f8f9fa;
        color: #333;
        font-weight: 600;
        position: sticky;
        top: 0;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .action-buttons {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }

    .action-btn {
        padding: 8px 12px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        color: white;
    }

    .view-btn {
        background: #667eea;
    }

    .view-btn:hover {
        background: #5568d3;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
    }

    .call-btn {
        background: #28a745;
    }

    .call-btn:hover {
        background: #218838;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
    }

    .whatsapp-btn {
        background: #25d366;
    }

    .whatsapp-btn:hover {
        background: #1ebe57;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(37, 211, 102, 0.3);
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

    /* Client Details View */
    .showStock {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e9ecef;
        flex-wrap: wrap;
        gap: 10px;
    }

    .table-header h3 {
        margin: 0;
        color: #333;
        font-size: 1.3rem;
    }

    .btn-print-history {
        padding: 8px 16px;
        background: #17a2b8;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-print-history:hover {
        background: #138496;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(23, 162, 184, 0.3);
    }

    .client-summary-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }

    .summary-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .summary-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .summary-value {
        font-size: 1.2rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .inline-action-btn {
        padding: 4px 8px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        transition: all 0.3s ease;
    }

    .inline-action-btn:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .inline-action-btn.whatsapp {
        background: rgba(37, 211, 102, 0.3);
    }

    .inline-action-btn.whatsapp:hover {
        background: rgba(37, 211, 102, 0.5);
    }

    .detail-actions {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .btn-add-debt,
    .btn-add-payment {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: white;
    }

    .btn-add-debt {
        background: #dc3545;
    }

    .btn-add-debt:hover {
        background: #c82333;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }

    .btn-add-payment {
        background: #28a745;
    }

    .btn-add-payment:hover {
        background: #218838;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    }

    .tabs-container {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        border-bottom: 2px solid #e9ecef;
        flex-wrap: wrap;
    }

    .tab-btn {
        padding: 10px 20px;
        border: none;
        background: transparent;
        cursor: pointer;
        font-weight: 600;
        color: #666;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .tab-btn:hover {
        color: #667eea;
    }

    .tab-btn.active {
        color: #667eea;
        border-bottom-color: #667eea;
    }

    .tab-content {
        margin-top: 20px;
    }

    .tab-content h4 {
        color: #333;
        margin-bottom: 15px;
        font-size: 1.2rem;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
    }

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

    .status-paid {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .status-unpaid {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .view-receipt-btn {
        padding: 6px 12px;
        background: #667eea;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .view-receipt-btn:hover {
        background: #5568d3;
        transform: translateY(-1px);
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
        .history-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .header-actions {
            width: 100%;
        }

        .btn-print-all {
            width: 100%;
            justify-content: center;
        }

        .filters-container {
            flex-direction: column;
        }

        .search-input,
        .filter-select {
            width: 100%;
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

        .client-summary-card {
            grid-template-columns: 1fr;
        }

        .tabs-container {
            flex-direction: column;
        }

        .tab-btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
