<template>
    <div class="claims-content">
        <div class="claims-header">
            <h2>Gestion des Créances</h2>

            <!-- Improved responsive statistics section -->
            <div class="statistics-section">
                <div class="stat-card">
                    <div class="stat-label">Total des Créances</div>
                    <div class="stat-value">
                        {{ formatAmount(totalDebtAmount) }} FCFA
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Payé</div>
                    <div class="stat-value" style="color: #28a745">
                        {{ formatAmount(totalPaidAmount) }} FCFA
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Restant</div>
                    <div class="stat-value" style="color: #dc3545">
                        {{ formatAmount(totalRemainingAmount) }} FCFA
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Nombre de Créances</div>
                    <div class="stat-value">{{ filteredClaims.length }}</div>
                </div>
            </div>

            <div class="header-actions">
                <button class="btn btn-primary" @click="openAddClaimModal()">
                    <i class="fas fa-plus"></i>
                    Ajouter une créance
                </button>

                <button class="btn btn-success" @click="openAddClientModal()">
                    <i class="fas fa-user-plus"></i>
                    Nouveau client
                </button>

                <button @click="printList" class="btn btn-print">
                    <i class="fas fa-print"></i>
                    Imprimer la liste
                </button>

                <input
                    type="text"
                    class="search-input"
                    placeholder="Rechercher un client..."
                    v-model="searchQuery"
                />

                <select class="filter-select" v-model="groupByClient">
                    <option :value="true">Grouper par client</option>
                    <option :value="false">Liste complète</option>
                </select>

                <select class="filter-select" v-model="sortOption">
                    <option>Client (A-Z)</option>
                    <option>Client (Z-A)</option>
                    <option>Montant (croissant)</option>
                    <option>Montant (décroissant)</option>
                    <option>Date (récent)</option>
                    <option>Date (ancien)</option>
                </select>
            </div>
        </div>

        <!-- Improved responsive table with white text on header and print button for each client -->
        <div class="showClaims" v-if="showClaims && groupByClient">
            <div
                class="table-container"
                v-for="client in paginatedGroupedClaims"
                :key="client.client_id"
            >
                <div class="table-header">
                    <div class="header-info">
                        <h3>
                            <i class="fas fa-user"></i>
                            {{ client.client_name }} - {{ client.client_phone }}
                        </h3>
                        <div class="stats-row">
                            <strong class="stat-item">
                                Total:
                                {{ formatAmount(client.total_debt) }} FCFA
                            </strong>
                            <strong class="stat-item stat-paid">
                                Payé: {{ formatAmount(client.total_paid) }} FCFA
                            </strong>
                            <strong class="stat-item stat-remaining">
                                Reste: {{ formatAmount(client.remaining) }} FCFA
                            </strong>
                        </div>
                    </div>
                    <button
                        @click="printClientHistory(client)"
                        class="btn-print-client"
                        title="Imprimer l'historique du client"
                    >
                        <i class="fas fa-print"></i>
                        Imprimer
                    </button>
                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Commentaire</th>
                            <th>Montant dû</th>
                            <th>Montant payé</th>
                            <th>Reste à payer</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="claim in client.claims" :key="claim.id">
                            <td data-label="Date">
                                {{ formatDateTime(claim.created_at) }}
                            </td>
                            <td data-label="Commentaire">
                                {{ claim.comment }}
                            </td>
                            <td data-label="Montant dû">
                                <strong>
                                    {{ formatAmount(claim.debt_amount) }} FCFA
                                </strong>
                            </td>
                            <td data-label="Montant payé">
                                {{ formatAmount(getClaimPaidAmount(claim.id)) }}
                                FCFA
                            </td>
                            <td data-label="Reste à payer">
                                <strong style="color: #dc3545">
                                    {{
                                        formatAmount(
                                            claim.debt_amount -
                                                getClaimPaidAmount(claim.id)
                                        )
                                    }}
                                    FCFA
                                </strong>
                            </td>
                            <td data-label="Statut">
                                <span
                                    :style="{
                                        padding: '0.4rem 0.8rem',
                                        borderRadius: '20px',
                                        fontSize: '0.85rem',
                                        fontWeight: '600',
                                        display: 'inline-block',
                                        backgroundColor:
                                            getClaimPaidAmount(claim.id) >=
                                            claim.debt_amount
                                                ? '#d4edda'
                                                : getClaimPaidAmount(claim.id) >
                                                  0
                                                ? '#fff3cd'
                                                : '#f8d7da',
                                        color:
                                            getClaimPaidAmount(claim.id) >=
                                            claim.debt_amount
                                                ? '#155724'
                                                : getClaimPaidAmount(claim.id) >
                                                  0
                                                ? '#856404'
                                                : '#721c24',
                                        border:
                                            getClaimPaidAmount(claim.id) >=
                                            claim.debt_amount
                                                ? '1px solid #c3e6cb'
                                                : getClaimPaidAmount(claim.id) >
                                                  0
                                                ? '1px solid #ffeaa7'
                                                : '1px solid #f5c6cb',
                                    }"
                                >
                                    {{
                                        getClaimPaidAmount(claim.id) >=
                                        claim.debt_amount
                                            ? 'Soldé'
                                            : getClaimPaidAmount(claim.id) > 0
                                            ? 'Partiel'
                                            : 'Impayé'
                                    }}
                                </span>
                            </td>
                            <td data-label="Actions">
                                <div class="action-buttons">
                                    <button
                                        class="btn-sm"
                                        style="
                                            background-color: #28a745;
                                            color: white;
                                        "
                                        title="Ajouter un paiement"
                                        @click="openAddPaymentModal(claim)"
                                    >
                                        <i class="fas fa-money-bill-wave"></i>
                                    </button>
                                    <button
                                        class="btn-sm btn-edit"
                                        title="Historique des paiements"
                                        @click="viewPaymentHistory(claim)"
                                    >
                                        <i class="fas fa-history"></i>
                                    </button>
                                    <button
                                        class="btn-sm btn-delete"
                                        title="Supprimer"
                                        @click="openDeleteClaimModal(claim)"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="paginatedGroupedClaims.length === 0" class="no-data">
                <strong>Aucune créance disponible</strong>
            </div>

            <div v-if="groupedClaims.length > 0" class="pagination">
                <button
                    @click="previousPage"
                    :disabled="currentPage === 1"
                    class="btn-pagination"
                >
                    <i class="fas fa-chevron-left"></i>
                    Précédent
                </button>
                <span class="pagination-info">
                    Page {{ currentPage }} sur {{ totalPages }}
                </span>
                <button
                    @click="nextPage"
                    :disabled="currentPage === totalPages"
                    class="btn-pagination"
                >
                    Suivant
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>

        <!-- Vue liste complète -->
        <div class="showClaims" v-if="showClaims && !groupByClient">
            <div class="table-container">
                <div class="table-header">
                    <h3>Liste complète des créances</h3>
                    <strong>Total: {{ filteredClaims.length }}</strong>
                </div>

                <table class="table" v-if="paginatedClaims.length > 0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Téléphone</th>
                            <th>Commentaire</th>
                            <th>Montant dû</th>
                            <th>Montant payé</th>
                            <th>Reste</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="claim in paginatedClaims" :key="claim.id">
                            <td data-label="Date">
                                {{ formatDateTime(claim.created_at) }}
                            </td>
                            <td data-label="Client">
                                <strong>{{ claim.client_name }}</strong>
                            </td>
                            <td data-label="Téléphone">
                                {{ claim.client_phone }}
                            </td>
                            <td data-label="Commentaire">
                                {{ claim.comment }}
                            </td>
                            <td data-label="Montant dû">
                                <strong>
                                    {{ formatAmount(claim.debt_amount) }} FCFA
                                </strong>
                            </td>
                            <td data-label="Montant payé">
                                {{ formatAmount(getClaimPaidAmount(claim.id)) }}
                                FCFA
                            </td>
                            <td data-label="Reste">
                                <strong style="color: #dc3545">
                                    {{
                                        formatAmount(
                                            claim.debt_amount -
                                                getClaimPaidAmount(claim.id)
                                        )
                                    }}
                                    FCFA
                                </strong>
                            </td>
                            <td data-label="Statut">
                                <span
                                    :style="{
                                        padding: '0.4rem 0.8rem',
                                        borderRadius: '20px',
                                        fontSize: '0.85rem',
                                        fontWeight: '600',
                                        display: 'inline-block',
                                        backgroundColor:
                                            getClaimPaidAmount(claim.id) >=
                                            claim.debt_amount
                                                ? '#d4edda'
                                                : getClaimPaidAmount(claim.id) >
                                                  0
                                                ? '#fff3cd'
                                                : '#f8d7da',
                                        color:
                                            getClaimPaidAmount(claim.id) >=
                                            claim.debt_amount
                                                ? '#155724'
                                                : getClaimPaidAmount(claim.id) >
                                                  0
                                                ? '#856404'
                                                : '#721c24',
                                        border:
                                            getClaimPaidAmount(claim.id) >=
                                            claim.debt_amount
                                                ? '1px solid #c3e6cb'
                                                : getClaimPaidAmount(claim.id) >
                                                  0
                                                ? '1px solid #ffeaa7'
                                                : '1px solid #f5c6cb',
                                    }"
                                >
                                    {{
                                        getClaimPaidAmount(claim.id) >=
                                        claim.debt_amount
                                            ? 'Soldé'
                                            : getClaimPaidAmount(claim.id) > 0
                                            ? 'Partiel'
                                            : 'Impayé'
                                    }}
                                </span>
                            </td>
                            <td data-label="Actions">
                                <div class="action-buttons">
                                    <button
                                        class="btn-sm"
                                        style="
                                            background-color: #28a745;
                                            color: white;
                                        "
                                        title="Ajouter un paiement"
                                        @click="openAddPaymentModal(claim)"
                                    >
                                        <i class="fas fa-money-bill-wave"></i>
                                    </button>
                                    <button
                                        class="btn-sm btn-edit"
                                        title="Historique des paiements"
                                        @click="viewPaymentHistory(claim)"
                                    >
                                        <i class="fas fa-history"></i>
                                    </button>
                                    <button
                                        class="btn-sm btn-delete"
                                        title="Supprimer"
                                        @click="openDeleteClaimModal(claim)"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <strong
                    v-if="paginatedClaims.length === 0"
                    class="mx-auto text-center"
                >
                    Aucune créance disponible
                </strong>
            </div>

            <!-- Added pagination controls for list view -->
            <div v-if="filteredClaims.length > 0" class="pagination">
                <button
                    @click="previousPage"
                    :disabled="currentPage === 1"
                    class="btn-pagination"
                >
                    <i class="fas fa-chevron-left"></i>
                    Précédent
                </button>
                <span class="pagination-info">
                    Page {{ currentPage }} sur {{ totalPages }}
                </span>
                <button
                    @click="nextPage"
                    :disabled="currentPage === totalPages"
                    class="btn-pagination"
                >
                    Suivant
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>

        <!-- Historique des paiements -->
        <div class="showPaymentHistory" v-if="showPaymentHistory">
            <div class="table-container">
                <div class="table-header">
                    <h3 style="display: flex; align-items: center; gap: 0.5rem">
                        <span
                            @click="closePaymentHistory()"
                            style="
                                display: flex;
                                align-items: center;
                                cursor: pointer;
                                color: #007bff;
                                font-weight: bold;
                            "
                        >
                            <i
                                class="fas fa-arrow-left"
                                style="color: white"
                            ></i>
                        </span>
                        | Historique des paiements -
                        <strong>{{ selectedClaim.client_name }}</strong>
                    </h3>
                    <strong>
                        Total payé:
                        {{
                            formatAmount(getTotalPaidForClaim(selectedClaim.id))
                        }}
                        FCFA
                    </strong>
                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Montant</th>
                            <th>Méthode</th>
                            <th>Commentaire</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="payment in getPaymentsForClaim(
                                selectedClaim.id
                            )"
                            :key="payment.id"
                        >
                            <td data-label="Date">
                                {{ formatDateTime(payment.created_at) }}
                            </td>
                            <td data-label="Montant">
                                <strong>
                                    {{ formatAmount(payment.amount) }} FCFA
                                </strong>
                            </td>
                            <td data-label="Méthode">
                                {{ payment.payment_method }}
                            </td>
                            <td data-label="Commentaire">
                                {{ payment.comment }}
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div
                    v-if="getPaymentsForClaim(selectedClaim.id).length === 0"
                    class="no-data"
                >
                    <strong>Aucun paiement enregistré</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ajouter Client -->
    <div
        v-if="showAddClientModal"
        class="modal-add-client"
        @click.self="closeAddClientModal()"
    >
        <div class="modal-content" style="padding: 2rem">
            <div
                class="modal-header"
                style="
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                "
            >
                <h3>Ajouter un nouveau client</h3>
                <span
                    class="close"
                    @click="closeAddClientModal()"
                    style="cursor: pointer; font-size: 1.5rem; color: #999"
                >
                    &times;
                </span>
            </div>

            <form @submit.prevent="addClientForm" class="form">
                <div class="form-group">
                    <label for="clientName">Nom du client</label>
                    <input
                        id="clientName"
                        v-model="newClient.name"
                        type="text"
                        class="form-control"
                        required
                    />
                </div>

                <div class="form-group">
                    <label for="clientPhone">Téléphone</label>
                    <input
                        id="clientPhone"
                        v-model="newClient.phone"
                        type="text"
                        class="form-control"
                        required
                    />
                </div>

                <div
                    style="
                        display: flex;
                        gap: 1rem;
                        justify-content: flex-end;
                        margin-top: 2rem;
                    "
                >
                    <button
                        type="button"
                        class="btn"
                        @click="closeAddClientModal()"
                        style="background: #6c757d; color: white"
                    >
                        Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Ajouter Créance -->
    <div
        v-if="showAddClaimModal"
        class="modal-add-claim"
        @click.self="closeAddClaimModal()"
    >
        <div class="modal-content" style="padding: 2rem">
            <div
                class="modal-header"
                style="
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                "
            >
                <h3>Ajouter une créance</h3>
                <span
                    class="close"
                    @click="closeAddClaimModal()"
                    style="cursor: pointer; font-size: 1.5rem; color: #999"
                >
                    &times;
                </span>
            </div>

            <form @submit.prevent="addClaimForm" class="form">
                <div class="form-group">
                    <label for="clientSelect">Client</label>
                    <select
                        id="clientSelect"
                        v-model="newClaim.client_id"
                        class="form-control"
                        required
                    >
                        <option value="">-- Sélectionner un client --</option>
                        <option
                            v-for="client in clients"
                            :key="client.id"
                            :value="client.id"
                        >
                            {{ client.name }} - {{ client.phone }}
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="debtAmount">Montant de la créance</label>
                    <input
                        id="debtAmount"
                        v-model="newClaim.debt_amount"
                        type="number"
                        class="form-control"
                        required
                    />
                </div>

                <div class="form-group">
                    <label for="claimComment">Commentaire</label>
                    <textarea
                        id="claimComment"
                        v-model="newClaim.comment"
                        class="form-control"
                        rows="3"
                    ></textarea>
                </div>

                <div
                    style="
                        display: flex;
                        gap: 1rem;
                        justify-content: flex-end;
                        margin-top: 2rem;
                    "
                >
                    <button
                        type="button"
                        class="btn"
                        @click="closeAddClaimModal()"
                        style="background: #6c757d; color: white"
                    >
                        Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Ajouter Paiement -->
    <div
        v-if="showAddPaymentModal"
        class="modal-add-payment"
        @click.self="closeAddPaymentModal()"
    >
        <div class="modal-content" style="padding: 2rem">
            <div
                class="modal-header"
                style="
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                "
            >
                <h3>Ajouter un paiement</h3>
                <span
                    class="close"
                    @click="closeAddPaymentModal()"
                    style="cursor: pointer; font-size: 1.5rem; color: #999"
                >
                    &times;
                </span>
            </div>

            <form @submit.prevent="addPaymentForm" class="form">
                <div style="margin-bottom: 1.5rem">
                    <strong>Créance:</strong>
                    {{ selectedClaim.client_name }} -
                    {{ formatAmount(selectedClaim.debt_amount) }} FCFA
                </div>

                <div class="form-group">
                    <label for="paymentAmount">Montant du paiement</label>
                    <input
                        id="paymentAmount"
                        v-model="newPayment.amount"
                        type="number"
                        class="form-control"
                        required
                    />
                </div>

                <div class="form-group">
                    <label for="paymentMethod">Méthode de paiement</label>
                    <select
                        id="paymentMethod"
                        v-model="newPayment.payment_method"
                        class="form-control"
                    >
                        <option value="espèces">Espèces</option>
                        <option value="chèque">Chèque</option>
                        <option value="virement">Virement</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="paymentComment">Commentaire</label>
                    <textarea
                        id="paymentComment"
                        v-model="newPayment.comment"
                        class="form-control"
                        rows="3"
                    ></textarea>
                </div>

                <div
                    style="
                        display: flex;
                        gap: 1rem;
                        justify-content: flex-end;
                        margin-top: 2rem;
                    "
                >
                    <button
                        type="button"
                        class="btn"
                        @click="closeAddPaymentModal()"
                        style="background: #6c757d; color: white"
                    >
                        Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Ajouter le paiement
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Supprimer Créance -->
    <div
        v-if="showDeleteClaimModal"
        class="modal-delete-claim"
        @click.self="closeDeleteClaimModal()"
    >
        <div class="modal-content" style="padding: 2rem">
            <div
                class="modal-header"
                style="
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                "
            >
                <h3>Confirmation de suppression</h3>
                <span
                    class="close"
                    @click="closeDeleteClaimModal()"
                    style="cursor: pointer; font-size: 1.5rem; color: #999"
                >
                    &times;
                </span>
            </div>

            <p style="margin-bottom: 1.5rem">
                Êtes-vous sûr de vouloir supprimer cette créance de
                <strong>{{ currentClaim.client_name }}</strong>
                pour un montant de
                <strong>
                    {{ formatAmount(currentClaim.debt_amount) }} FCFA
                </strong>
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
                    @click="closeDeleteClaimModal()"
                    style="background: #6c757d; color: white"
                >
                    Annuler
                </button>
                <button
                    class="btn"
                    @click="deleteClaim"
                    style="background: #dc3545; color: white"
                >
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'ClaimsComponent',

        data() {
            return {
                claims: [],
                clients: [],
                payments: [],
                showClaims: true,
                showPaymentHistory: false,
                showAddClaimModal: false,
                showAddPaymentModal: false,
                showDeleteClaimModal: false,
                showAddClientModal: false,

                searchQuery: '',
                groupByClient: true,
                sortOption: 'Client (A-Z)',

                selectedClaim: {},
                currentClaim: null,

                currentPage: 1,
                itemsPerPage: 10,

                newClient: {
                    name: '',
                    phone: '',
                },

                newClaim: {
                    client_id: '',
                    debt_amount: '',
                    comment: '',
                },

                newPayment: {
                    amount: '',
                    payment_method: 'espèces',
                    comment: '',
                },
            };
        },

        mounted() {
            this.fetchAllData();
        },

        methods: {
            async fetchAllData() {
                console.log('[v0] === DÉBUT CHARGEMENT DES DONNÉES ===');
                console.log('[v0] URL de base:', window.location.origin);

                try {
                    const API_BASE = window.location.origin;

                    const config = {
                        headers: {
                            'Cache-Control': 'no-cache',
                            Pragma: 'no-cache',
                        },
                        params: {
                            _t: new Date().getTime(), // Timestamp pour forcer le rechargement
                        },
                    };

                    console.log('[v0] Appel des APIs...');
                    const [claimsRes, clientsRes, paymentsRes] =
                        await Promise.all([
                            axios.get(`${API_BASE}/claimslist`, config),
                            axios.get(`${API_BASE}/clientslist`, config),
                            axios.get(`${API_BASE}/claims/payments`, config),
                        ]);

                    console.log('[v0] Réponses reçues:', {
                        claimsStatus: claimsRes.status,
                        clientsStatus: clientsRes.status,
                        paymentsStatus: paymentsRes.status,
                    });

                    this.claims = claimsRes.data.map((claim) => ({
                        ...claim,
                        id: Number(claim.id),
                        client_id: Number(claim.client_id),
                        debt_amount: parseFloat(claim.debt_amount),
                    }));

                    this.clients = clientsRes.data.map((client) => ({
                        ...client,
                        id: Number(client.id),
                    }));

                    this.payments = paymentsRes.data.map((payment) => ({
                        ...payment,
                        id: Number(payment.id),
                        claim_id: Number(payment.claim_id),
                        amount: parseFloat(payment.amount),
                    }));

                    console.log('[v0] Données normalisées:', {
                        claims: this.claims.length,
                        clients: this.clients.length,
                        payments: this.payments.length,
                    });

                    console.log('[v0] Exemple de paiement:', this.payments[0]);
                    console.log('[v0] Exemple de créance:', this.claims[0]);

                    this.$nextTick(() => {
                        this.$forceUpdate();
                        console.log('[v0] Vue forcé à se mettre à jour');
                    });

                    console.log('[v0] === FIN CHARGEMENT DES DONNÉES ===');
                } catch (error) {
                    console.error('[v0] === ERREUR LORS DU CHARGEMENT ===');
                    console.error('[v0] Message:', error.message);
                    console.error('[v0] Réponse:', error.response?.data);
                    console.error('[v0] Status:', error.response?.status);
                    console.error('[v0] URL:', error.config?.url);
                    alert(
                        'Erreur lors du chargement des données: ' +
                            (error.response?.data?.message || error.message)
                    );
                }
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

            getClaimPaidAmount(claimId) {
                const normalizedClaimId = Number(claimId);

                const claimPayments = this.payments.filter((p) => {
                    const paymentClaimId = Number(p.claim_id);
                    return paymentClaimId === normalizedClaimId;
                });

                const total = claimPayments.reduce(
                    (sum, p) => sum + parseFloat(p.amount),
                    0
                );

                // Log uniquement si des paiements sont trouvés (pour éviter trop de logs)
                if (claimPayments.length > 0) {
                    console.log(
                        `[v0] Paiements pour créance ${normalizedClaimId}:`,
                        {
                            nombrePaiements: claimPayments.length,
                            total: total,
                            paiements: claimPayments,
                        }
                    );
                }

                return total;
            },

            getTotalPaidForClaim(claimId) {
                return this.getClaimPaidAmount(claimId);
            },

            getPaymentsForClaim(claimId) {
                const normalizedClaimId = Number(claimId);
                const payments = this.payments.filter(
                    (p) => Number(p.claim_id) === normalizedClaimId
                );

                console.log(
                    `[v0] Récupération paiements pour créance ${normalizedClaimId}:`,
                    payments.length
                );

                return payments;
            },

            previousPage() {
                if (this.currentPage > 1) {
                    this.currentPage--;
                }
            },

            nextPage() {
                if (this.currentPage < this.totalPages) {
                    this.currentPage++;
                }
            },

            openAddClaimModal() {
                this.showAddClaimModal = true;
            },

            closeAddClaimModal() {
                this.showAddClaimModal = false;
                this.newClaim = {
                    client_id: '',
                    debt_amount: '',
                    comment: '',
                };
            },

            openAddClientModal() {
                this.showAddClientModal = true;
            },

            closeAddClientModal() {
                this.showAddClientModal = false;
                this.newClient = {
                    name: '',
                    phone: '',
                };
            },

            // Amélioration de addClientForm avec logs et rechargement optimisé
            async addClientForm() {
                console.log("[v0] === AJOUT D'UN CLIENT ===");
                try {
                    const API_BASE = window.location.origin;
                    const clientData = {
                        name: this.newClient.name,
                        phone: this.newClient.phone,
                    };
                    console.log('[v0] Envoi des données:', clientData);
                    const response = await axios.post(
                        `${API_BASE}/clients`,
                        clientData
                    );
                    console.log('[v0] Réponse:', response.data);

                    alert('Client ajouté avec succès.');
                    this.closeAddClientModal();

                    await new Promise((resolve) => setTimeout(resolve, 300));
                    await this.fetchAllData();
                } catch (error) {
                    console.error('[v0] Erreur ajout client:', error);
                    alert(
                        "Erreur lors de l'ajout du client: " +
                            (error.response?.data?.message || error.message)
                    );
                }
            },

            async addClaimForm() {
                console.log("[v0] === AJOUT D'UNE CRÉANCE ===");
                try {
                    const API_BASE = window.location.origin;
                    const claimData = {
                        client_id: Number(this.newClaim.client_id),
                        amount: parseFloat(this.newClaim.debt_amount),
                        comment: this.newClaim.comment,
                    };

                    console.log('[v0] Envoi des données:', claimData);
                    const response = await axios.post(
                        `${API_BASE}/claims/add`,
                        claimData
                    );
                    console.log('[v0] Réponse:', response.data);

                    alert('Créance ajoutée avec succès.');
                    this.closeAddClaimModal();

                    await new Promise((resolve) => setTimeout(resolve, 300));
                    await this.fetchAllData();
                } catch (error) {
                    console.error('[v0] Erreur ajout créance:', error);
                    alert(
                        "Erreur lors de l'ajout de la créance: " +
                            (error.response?.data?.message || error.message)
                    );
                }
            },

            openAddPaymentModal(claim) {
                this.selectedClaim = claim;
                this.showAddPaymentModal = true;
            },

            closeAddPaymentModal() {
                this.showAddPaymentModal = false;
                this.selectedClaim = {};
                this.newPayment = {
                    amount: '',
                    payment_method: 'espèces',
                    comment: '',
                };
            },

            async addPaymentForm() {
                console.log("[v0] === AJOUT D'UN PAIEMENT ===");
                console.log('[v0] Créance sélectionnée:', this.selectedClaim);
                console.log('[v0] Données du paiement:', this.newPayment);

                try {
                    const API_BASE = window.location.origin;
                    const paymentData = {
                        claim_id: Number(this.selectedClaim.id),
                        amount: parseFloat(this.newPayment.amount),
                        payment_method: this.newPayment.payment_method,
                        comment: this.newPayment.comment,
                    };

                    console.log('[v0] Envoi des données:', paymentData);

                    const response = await axios.post(
                        `${API_BASE}/claims/pay`,
                        paymentData
                    );

                    console.log('[v0] Réponse du serveur:', response.data);
                    console.log('[v0] Status:', response.status);

                    alert('Paiement enregistré avec succès.');

                    this.closeAddPaymentModal();

                    await new Promise((resolve) => setTimeout(resolve, 300));

                    console.log(
                        '[v0] Rechargement des données après ajout du paiement...'
                    );
                    await this.fetchAllData();

                    console.log(
                        '[v0] Paiement ajouté et données rechargées avec succès'
                    );
                } catch (error) {
                    console.error('[v0] === ERREUR AJOUT PAIEMENT ===');
                    console.error('[v0] Message:', error.message);
                    console.error('[v0] Réponse:', error.response?.data);
                    console.error('[v0] Status:', error.response?.status);
                    alert(
                        "Erreur lors de l'ajout du paiement: " +
                            (error.response?.data?.message || error.message)
                    );
                }
            },

            viewPaymentHistory(claim) {
                this.selectedClaim = claim;
                this.showPaymentHistory = true;
                this.showClaims = false;
            },

            closePaymentHistory() {
                this.showPaymentHistory = false;
                this.showClaims = true;
                this.selectedClaim = {};
            },

            openDeleteClaimModal(claim) {
                this.currentClaim = claim;
                this.showDeleteClaimModal = true;
            },

            closeDeleteClaimModal() {
                this.showDeleteClaimModal = false;
                this.currentClaim = null;
            },

            async deleteClaim() {
                console.log("[v0] === SUPPRESSION D'UNE CRÉANCE ===");
                try {
                    const API_BASE = window.location.origin;
                    console.log(
                        '[v0] Suppression de la créance:',
                        this.currentClaim.id
                    );

                    await axios.post(
                        `${API_BASE}/claims/${this.currentClaim.id}/delete`
                    );

                    alert('Créance supprimée avec succès.');
                    this.closeDeleteClaimModal();

                    await new Promise((resolve) => setTimeout(resolve, 300));
                    await this.fetchAllData();
                } catch (error) {
                    console.error('[v0] Erreur suppression:', error);
                    alert(
                        'Erreur lors de la suppression: ' +
                            (error.response?.data?.message || error.message)
                    );
                }
            },

            printList() {
                const printWindow = window.open('', '_blank');

                const totalDebt = this.filteredClaims.reduce(
                    (sum, claim) => sum + parseFloat(claim.debt_amount),
                    0
                );
                const totalPaid = this.filteredClaims.reduce(
                    (sum, claim) => sum + this.getClaimPaidAmount(claim.id),
                    0
                );
                const totalRemaining = totalDebt - totalPaid;

                let tableRows = '';

                if (this.groupByClient) {
                    this.groupedClaims.forEach((client) => {
                        tableRows += `
                        <tr style="background-color: #667eea; color: white;">
                            <td colspan="6" style="padding: 12px; border: 1px solid #ddd; font-weight: bold;">
                                ${client.client_name} - ${client.client_phone}
                            </td>
                        </tr>
                    `;

                        client.claims.forEach((claim) => {
                            const paidAmount = this.getClaimPaidAmount(
                                claim.id
                            );
                            const remaining = claim.debt_amount - paidAmount;
                            const status =
                                paidAmount >= claim.debt_amount
                                    ? 'Soldé'
                                    : paidAmount > 0
                                    ? 'Partiel'
                                    : 'Impayé';

                            tableRows += `
                            <tr>
                                <td style="padding: 12px; border: 1px solid #ddd;">${this.formatDateTime(
                                    claim.created_at
                                )}</td>
                                <td style="padding: 12px; border: 1px solid #ddd;">${
                                    claim.comment
                                }</td>
                                <td style="padding: 12px; border: 1px solid #ddd; text-align: right;"><strong>${this.formatAmount(
                                    claim.debt_amount
                                )} FCFA</strong></td>
                                <td style="padding: 12px; border: 1px solid #ddd; text-align: right;">${this.formatAmount(
                                    paidAmount
                                )} FCFA</td>
                                <td style="padding: 12px; border: 1px solid #ddd; text-align: right;"><strong>${this.formatAmount(
                                    remaining
                                )} FCFA</strong></td>
                                <td style="padding: 12px; border: 1px solid #ddd; text-align: center;">${status}</td>
                            </tr>
                        `;
                        });
                    });
                } else {
                    this.filteredClaims.forEach((claim, index) => {
                        const paidAmount = this.getClaimPaidAmount(claim.id);
                        const remaining = claim.debt_amount - paidAmount;
                        const status =
                            paidAmount >= claim.debt_amount
                                ? 'Soldé'
                                : paidAmount > 0
                                ? 'Partiel'
                                : 'Impayé';

                        tableRows += `
                        <tr>
                            <td style="padding: 12px; border: 1px solid #ddd;">${
                                index + 1
                            }</td>
                            <td style="padding: 12px; border: 1px solid #ddd;">${this.formatDateTime(
                                claim.created_at
                            )}</td>
                            <td style="padding: 12px; border: 1px solid #ddd;"><strong>${
                                claim.client_name
                            }</strong></td>
                            <td style="padding: 12px; border: 1px solid #ddd;">${
                                claim.comment
                            }</td>
                            <td style="padding: 12px; border: 1px solid #ddd; text-align: right;"><strong>${this.formatAmount(
                                claim.debt_amount
                            )} FCFA</strong></td>
                            <td style="padding: 12px; border: 1px solid #ddd; text-align: right;">${this.formatAmount(
                                paidAmount
                            )} FCFA</td>
                            <td style="padding: 12px; border: 1px solid #ddd; text-align: right;"><strong>${this.formatAmount(
                                remaining
                            )} FCFA</strong></td>
                            <td style="padding: 12px; border: 1px solid #ddd; text-align: center;">${status}</td>
                        </tr>
                    `;
                    });
                }

                const htmlContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Liste des Créances</title>
                      <style>
                            body { 
                                font-family: Arial, sans-serif; 
                                padding: 20px;
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
                                margin: 8px 0;
                            }
                            .stats-summary {
                                display: grid;
                                grid-template-columns: repeat(3, 1fr);
                                gap: 15px;
                                margin-bottom: 20px;
                            }
                            .stat-box {
                                background: #f8f9fa;
                                padding: 15px;
                                border-radius: 8px;
                                text-align: center;
                                border-left: 4px solid #667eea;
                            }
                            .stat-box.paid {
                                border-left-color: #28a745;
                            }
                            .stat-box.remaining {
                                border-left-color: #dc3545;
                            }
                            .stat-label {
                                font-size: 0.9rem;
                                color: #666;
                                margin-bottom: 5px;
                            }
                            .stat-value {
                                font-size: 1.3rem;
                                font-weight: 700;
                                color: #667eea;
                            }
                            .stat-box.paid .stat-value {
                                color: #28a745;
                            }
                            .stat-box.remaining .stat-value {
                                color: #dc3545;
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
                            th, td { 
                                border: 1px solid #ddd; 
                                padding: 12px; 
                                text-align: left; 
                            }
                            th {
                                border: 1px solid #667eea;
                            }
                            tbody tr:nth-child(even) {
                                background-color: #f8f9fa;
                            }
                            .total-row {
                                background: #e9ecef !important;
                                font-weight: bold;
                            }
                            .footer {
                                margin-top: 40px;
                                text-align: center;
                                color: #666;
                                font-size: 0.9rem;
                            }
                            @media print {
                                button { display: none; }
                                body { margin: 10mm; }
                            }
                        </style>
                          <style>
                            body {
                                font-family: Arial, sans-serif;
                                margin: 20px;
                                color: #333;
                            }
                            h1 {
                                color: #667eea;
                                text-align: center;
                                margin-bottom: 10px;
                            }
                            h2 {
                                text-align: center;
                                margin-bottom: 20px;
                                color: #333;
                            }
                            .company-info {
                                text-align: center;
                                margin-bottom: 30px;
                            }
                            .company-info p {
                                margin: 5px 0;
                                color: #666;
                            }
                            .info {
                                background: #f8f9fa;
                                padding: 15px;
                                border-radius: 8px;
                                margin-bottom: 20px;
                                border-left: 4px solid #667eea;
                            }
                            .info p {
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
                            th:last-child {
                                text-align: right;
                            }
                            tbody tr:nth-child(even) {
                                background: #f8f9fa;
                            }
                            tbody tr:hover {
                                background: #e9ecef;
                            }
                            .total-row {
                                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
                                color: white;
                                font-weight: bold;
                            }
                            button {
                                margin-top: 20px;
                                padding: 10px 20px;
                                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                color: white;
                                border: none;
                                border-radius: 8px;
                                cursor: pointer;
                                font-size: 16px;
                                box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
                            }
                            button:hover {
                                transform: translateY(-2px);
                                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
                            }
                            @media print {
                                button { display: none; }
                            }
                        </style>
                </head>
                <body>  
                       <h1>SAGER</h1>
                          <p style="margin: auto; text-align: center">Votre partenaire de confiance pour tous vos besoins en boissons et gaz domestique<br>
                                    Distribution professionnelle • Vente en gros et détail</p>
                                
                        <div class="company-info">
                            <p style="margin: auto; text-align: center"><strong>Téléphone:</strong> +229 0196466625</p>
                            <p style="margin: auto; text-align: center"><strong>IFU:</strong> 0202586942320</p>
                        </div>

                    <div class="info">
                        <p><strong>Date d'impression:</strong> ${new Date().toLocaleString(
                            'fr-FR'
                        )}</p>
                        <p><strong>Nombre total de créances:</strong> ${
                            this.filteredClaims.length
                        }</p>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                ${this.groupByClient ? '' : '<th>#</th>'}
                                <th>Date</th>
                                ${this.groupByClient ? '' : '<th>Client</th>'}
                                <th>Commentaire</th>
                                <th style="text-align: right;">Montant dû</th>
                                <th style="text-align: right;">Montant payé</th>
                                <th style="text-align: right;">Reste</th>
                                <th style="text-align: center;">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${tableRows}
                            <tr class="total-row">
                                <td colspan="${
                                    this.groupByClient ? '2' : '3'
                                }" style="padding: 12px; border: 1px solid #ddd; text-align: right;">TOTAUX:</td>
                                <td style="padding: 12px; border: 1px solid #ddd; text-align: right;">${this.formatAmount(
                                    totalDebt
                                )} FCFA</td>
                                <td style="padding: 12px; border: 1px solid #ddd; text-align: right;">${this.formatAmount(
                                    totalPaid
                                )} FCFA</td>
                                <td style="padding: 12px; border: 1px solid #ddd; text-align: right;">${this.formatAmount(
                                    totalRemaining
                                )} FCFA</td>
                                <td style="padding: 12px; border: 1px solid #ddd;"></td>
                            </tr>
                        </tbody>
                    </table>
                     <div class="footer" style="margin-auto; text-align: center">
                                    <p>Merci de votre confiance</p>
                                    <p>Rapport généré avec l'application SagerMarket</p>
                                </div>


                    <button onclick="window.print()" style="padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">
                        Imprimer
                    </button>
                </body>
                </html>
            `;

                printWindow.document.write(htmlContent);
                printWindow.document.close();
            },

            printClientHistory(client) {
                const printWindow = window.open('', '_blank');

                const totalDebt = client.total_debt;
                const totalPaid = client.total_paid;
                const totalRemaining = client.remaining;

                const tableRows = client.claims
                    .map(
                        (claim, index) => `
                    <tr>
                        <td style="padding: 12px; border: 1px solid #ddd;">${
                            index + 1
                        }</td>
                        <td style="padding: 12px; border: 1px solid #ddd;">${this.formatDateTime(
                            claim.created_at
                        )}</td>
                        <td style="padding: 12px; border: 1px solid #ddd;">${
                            claim.comment || '-'
                        }</td>
                        <td style="padding: 12px; border: 1px solid #ddd; text-align: right;">${this.formatAmount(
                            claim.debt_amount
                        )} FCFA</td>
                        <td style="padding: 12px; border: 1px solid #ddd; text-align: right;">${this.formatAmount(
                            this.getClaimPaidAmount(claim.id)
                        )} FCFA</td>
                        <td style="padding: 12px; border: 1px solid #ddd; text-align: right;">${this.formatAmount(
                            claim.debt_amount -
                                this.getClaimPaidAmount(claim.id)
                        )} FCFA</td>
                        <td style="padding: 12px; border: 1px solid #ddd; text-align: center;">
                            ${
                                this.getClaimPaidAmount(claim.id) >=
                                claim.debt_amount
                                    ? '<span style="color: #155724; font-weight: 600;">Soldé</span>'
                                    : this.getClaimPaidAmount(claim.id) > 0
                                    ? '<span style="color: #856404; font-weight: 600;">Partiel</span>'
                                    : '<span style="color: #721c24; font-weight: 600;">Impayé</span>'
                            }
                        </td>
                    </tr>
                `
                    )
                    .join('');

                const htmlContent = `
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Historique des Créances - ${
                            client.client_name
                        }</title>
                        <meta charset="UTF-8">
                        <style>
                            body { 
                                font-family: Arial, sans-serif; 
                                padding: 20px;
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
                                margin: 8px 0;
                            }
                            .stats-summary {
                                display: grid;
                                grid-template-columns: repeat(3, 1fr);
                                gap: 15px;
                                margin-bottom: 20px;
                            }
                            .stat-box {
                                background: #f8f9fa;
                                padding: 15px;
                                border-radius: 8px;
                                text-align: center;
                                border-left: 4px solid #667eea;
                            }
                            .stat-box.paid {
                                border-left-color: #28a745;
                            }
                            .stat-box.remaining {
                                border-left-color: #dc3545;
                            }
                            .stat-label {
                                font-size: 0.9rem;
                                color: #666;
                                margin-bottom: 5px;
                            }
                            .stat-value {
                                font-size: 1.3rem;
                                font-weight: 700;
                                color: #667eea;
                            }
                            .stat-box.paid .stat-value {
                                color: #28a745;
                            }
                            .stat-box.remaining .stat-value {
                                color: #dc3545;
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
                            th, td { 
                                border: 1px solid #ddd; 
                                padding: 12px; 
                                text-align: left; 
                            }
                            th {
                                border: 1px solid #667eea;
                            }
                            tbody tr:nth-child(even) {
                                background-color: #f8f9fa;
                            }
                            .total-row {
                                background: #e9ecef !important;
                                font-weight: bold;
                            }
                            .footer {
                                margin-top: 40px;
                                text-align: center;
                                color: #666;
                                font-size: 0.9rem;
                            }
                            @media print {
                                button { display: none; }
                                body { margin: 10mm; }
                            }
                        </style>
                          <style>
                            body {
                                font-family: Arial, sans-serif;
                                margin: 20px;
                                color: #333;
                            }
                            h1 {
                                color: #667eea;
                                text-align: center;
                                margin-bottom: 10px;
                            }
                            h2 {
                                text-align: center;
                                margin-bottom: 20px;
                                color: #333;
                            }
                            .company-info {
                                text-align: center;
                                margin-bottom: 30px;
                            }
                            .company-info p {
                                margin: 5px 0;
                                color: #666;
                            }
                            .info {
                                background: #f8f9fa;
                                padding: 15px;
                                border-radius: 8px;
                                margin-bottom: 20px;
                                border-left: 4px solid #667eea;
                            }
                            .info p {
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
                            th:last-child {
                                text-align: right;
                            }
                            tbody tr:nth-child(even) {
                                background: #f8f9fa;
                            }
                            tbody tr:hover {
                                background: #e9ecef;
                            }
                            .total-row {
                                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
                                color: white;
                                font-weight: bold;
                            }
                            button {
                                margin-top: 20px;
                                padding: 10px 20px;
                                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                color: white;
                                border: none;
                                border-radius: 8px;
                                cursor: pointer;
                                font-size: 16px;
                                box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
                            }
                            button:hover {
                                transform: translateY(-2px);
                                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
                            }
                            @media print {
                                button { display: none; }
                            }
                        </style>
                    </head>
                    <body>
                       <h1>SAGER</h1>
                          <p style="margin: auto; text-align: center">Votre partenaire de confiance pour tous vos besoins en boissons et gaz domestique<br>
                                    Distribution professionnelle • Vente en gros et détail</p>
                                
                        <div class="company-info">
                            <p><strong>Téléphone:</strong> +229 0196466625</p>
                            <p><strong>IFU:</strong> 0202586942320</p>
                        </div>

                        <h2>HISTORIQUE DES CRÉANCES</h2>

                        <div class="client-info">
                            <p><strong>Client:</strong> ${
                                client.client_name
                            }</p>
                            <p><strong>Téléphone:</strong> ${
                                client.client_phone
                            }</p>
                            <p><strong>Date d'impression:</strong> ${new Date().toLocaleString(
                                'fr-FR'
                            )}</p>
                            <p><strong>Nombre de créances:</strong> ${
                                client.claims.length
                            }</p>
                        </div>

                        <div class="stats-summary">
                            <div class="stat-box">
                                <div class="stat-label">Total des Créances</div>
                                <div class="stat-value">${this.formatAmount(
                                    totalDebt
                                )} FCFA</div>
                            </div>
                            <div class="stat-box paid">
                                <div class="stat-label">Total Payé</div>
                                <div class="stat-value">${this.formatAmount(
                                    totalPaid
                                )} FCFA</div>
                            </div>
                            <div class="stat-box remaining">
                                <div class="stat-label">Total Restant</div>
                                <div class="stat-value">${this.formatAmount(
                                    totalRemaining
                                )} FCFA</div>
                            </div>
                        </div>

                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Commentaire</th>
                                    <th style="text-align: right;">Montant dû</th>
                                    <th style="text-align: right;">Montant payé</th>
                                    <th style="text-align: right;">Reste</th>
                                    <th style="text-align: center;">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows}
                                <tr class="total-row">
                                    <td colspan="3" style="padding: 12px; border: 1px solid #ddd; text-align: right;">TOTAUX:</td>
                                    <td style="padding: 12px; border: 1px solid #ddd; text-align: right;">${this.formatAmount(
                                        totalDebt
                                    )} FCFA</td>
                                    <td style="padding: 12px; border: 1px solid #ddd; text-align: right;">${this.formatAmount(
                                        totalPaid
                                    )} FCFA</td>
                                    <td style="padding: 12px; border: 1px solid #ddd; text-align: right;">${this.formatAmount(
                                        totalRemaining
                                    )} FCFA</td>
                                    <td style="padding: 12px; border: 1px solid #ddd;"></td>
                                </tr>
                            </tbody>
                        </table>

                         <div class="footer">
                        <p>Merci de votre confiance</p>
                        <p>Rapport généré avec l'application SagerMarket</p>
                    </div>

                        <button onclick="window.print()" style="margin-top: 20px; padding: 10px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: 600;">
                            Imprimer
                        </button>
                    </body>
                    </html>
                `;

                printWindow.document.write(htmlContent);
                printWindow.document.close();
            },
        },

        computed: {
            totalDebtAmount() {
                return this.filteredClaims.reduce(
                    (sum, claim) => sum + parseFloat(claim.debt_amount),
                    0
                );
            },

            totalPaidAmount() {
                return this.filteredClaims.reduce(
                    (sum, claim) => sum + this.getClaimPaidAmount(claim.id),
                    0
                );
            },

            totalRemainingAmount() {
                return this.totalDebtAmount - this.totalPaidAmount;
            },

            totalPages() {
                if (this.groupByClient) {
                    return (
                        Math.ceil(
                            this.groupedClaims.length / this.itemsPerPage
                        ) || 1
                    );
                } else {
                    return (
                        Math.ceil(
                            this.filteredClaims.length / this.itemsPerPage
                        ) || 1
                    );
                }
            },

            paginatedClaims() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                const end = start + this.itemsPerPage;
                return this.filteredClaims.slice(start, end);
            },

            paginatedGroupedClaims() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                const end = start + this.itemsPerPage;
                return this.groupedClaims.slice(start, end);
            },

            filteredClaims() {
                let filtered = this.claims;

                if (this.searchQuery) {
                    const query = this.searchQuery.toLowerCase();
                    filtered = filtered.filter(
                        (c) =>
                            c.client_name.toLowerCase().includes(query) ||
                            c.client_phone.includes(query) ||
                            c.comment.toLowerCase().includes(query)
                    );
                }

                switch (this.sortOption) {
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
                    case 'Montant (croissant)':
                        filtered.sort(
                            (a, b) =>
                                parseFloat(a.debt_amount) -
                                parseFloat(b.debt_amount)
                        );
                        break;
                    case 'Montant (décroissant)':
                        filtered.sort(
                            (a, b) =>
                                parseFloat(b.debt_amount) -
                                parseFloat(a.debt_amount)
                        );
                        break;
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
                }

                this.currentPage = 1;
                return filtered;
            },

            groupedClaims() {
                const grouped = {};

                this.filteredClaims.forEach((claim) => {
                    const clientId = Number(claim.client_id);

                    if (!grouped[clientId]) {
                        grouped[clientId] = {
                            client_id: clientId,
                            client_name: claim.client_name,
                            client_phone: claim.client_phone,
                            claims: [],
                            total_debt: 0,
                            total_paid: 0,
                            remaining: 0,
                        };
                    }

                    grouped[clientId].claims.push(claim);
                    grouped[clientId].total_debt += parseFloat(
                        claim.debt_amount
                    );
                    grouped[clientId].total_paid += this.getClaimPaidAmount(
                        claim.id
                    );
                });

                Object.values(grouped).forEach((client) => {
                    client.remaining = client.total_debt - client.total_paid;
                });

                return Object.values(grouped);
            },
        },
    };
</script>

<style scoped>
    /* Header */
    .claims-header {
        margin-bottom: 2rem;
    }

    /* Improved responsive statistics section */
    .statistics-section {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 10px;
        padding: 1.25rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-left: 4px solid #667eea;
    }

    .stat-label {
        color: #6c757d;
        font-size: 0.85rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        color: #667eea;
        font-size: 1.5rem;
        font-weight: 700;
        word-break: break-word;
    }

    /* Improved table header for mobile with white text */
    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px 10px 0 0;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .table-header h3 {
        color: white;
        margin: 0;
        font-size: 1.2rem;
    }

    .header-info {
        flex: 1;
        min-width: 250px;
    }

    .stats-row {
        display: flex;
        gap: 1rem;
        margin-top: 0.5rem;
        flex-wrap: wrap;
    }

    .stat-item {
        color: white;
        font-size: 0.95rem;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        background: rgba(255, 255, 255, 0.1);
    }

    .stat-paid {
        background: rgba(40, 167, 69, 0.3);
    }

    .stat-remaining {
        background: rgba(220, 53, 69, 0.3);
    }

    /* Added print button for client */
    .btn-print-client {
        padding: 0.6rem 1.2rem;
        background: white;
        color: #667eea;
        border: 2px solid white;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.9rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .btn-print-client:hover {
        background: rgba(255, 255, 255, 0.9);
        transform: translateY(-2px);
    }

    .claims-header h2 {
        color: #333;
        margin-bottom: 1.5rem;
        font-size: 1.8rem;
        font-weight: 600;
    }

    .header-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }

    /* Buttons */
    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.95rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
    }

    .btn-print {
        background: #17a2b8;
        color: white;
    }

    .btn-print:hover {
        background: #138496;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(23, 162, 184, 0.4);
    }

    .btn-pagination {
        padding: 0.75rem 1rem;
        border: 1px solid #667eea;
        background: white;
        color: #667eea;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-pagination:hover:not(:disabled) {
        background: #667eea;
        color: white;
    }

    .btn-pagination:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Form inputs */
    .search-input,
    .filter-select {
        padding: 0.75rem;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: border-color 0.3s ease;
    }

    .search-input {
        flex: 1;
        min-width: 200px;
    }

    .search-input:focus,
    .filter-select:focus {
        outline: none;
        border-color: #667eea;
    }

    /* Tables */
    .table-container {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
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
    }

    .table td {
        padding: 1rem;
        border-bottom: 1px solid #f0f0f0;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    /* Action buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .btn-sm {
        padding: 0.5rem 0.75rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .btn-edit {
        background-color: #667eea;
        color: white;
    }

    .btn-edit:hover {
        background-color: #5568d3;
        transform: translateY(-2px);
    }

    .btn-delete {
        background-color: #dc3545;
        color: white;
    }

    .btn-delete:hover {
        background-color: #c82333;
        transform: translateY(-2px);
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        margin-top: 2rem;
    }

    .pagination-info {
        font-weight: 500;
        color: #333;
    }

    /* No data message */
    .no-data {
        text-align: center;
        padding: 3rem;
        color: #6c757d;
    }

    /* Improved responsive styles for mobile */
    @media (max-width: 768px) {
        .statistics-section {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }

        .stat-card {
            padding: 1rem;
        }

        .stat-value {
            font-size: 1.2rem;
        }

        .header-actions {
            flex-direction: column;
            width: 100%;
        }

        .header-actions .btn,
        .search-input,
        .filter-select {
            width: 100%;
        }

        .table-header {
            flex-direction: column;
            align-items: flex-start;
            padding: 1rem;
        }

        .header-info {
            width: 100%;
            min-width: auto;
        }

        .table-header h3 {
            font-size: 1rem;
            word-break: break-word;
        }

        .stats-row {
            flex-direction: column;
            gap: 0.5rem;
            width: 100%;
        }

        .stat-item {
            font-size: 0.85rem;
            width: 100%;
        }

        .btn-print-client {
            width: 100%;
            justify-content: center;
        }

        .table {
            display: block;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table thead {
            display: none;
        }

        .table tbody,
        .table tr,
        .table td {
            display: block;
            width: 100%;
        }

        .table tr {
            margin-bottom: 1rem;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
        }

        .table td {
            text-align: right;
            padding: 0.75rem;
            position: relative;
            padding-left: 50%;
        }

        .table td::before {
            content: attr(data-label);
            position: absolute;
            left: 0;
            width: 45%;
            padding-left: 0.75rem;
            font-weight: 600;
            text-align: left;
            color: #667eea;
        }

        .action-buttons {
            justify-content: flex-end;
            gap: 0.5rem;
        }

        .pagination {
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .btn-pagination {
            flex: 1;
            min-width: 100px;
            justify-content: center;
        }

        .pagination-info {
            width: 100%;
            text-align: center;
        }
    }

    @media (max-width: 480px) {
        .statistics-section {
            grid-template-columns: 1fr;
        }

        .stat-value {
            font-size: 1.4rem;
        }

        .claims-header h2 {
            font-size: 1.5rem;
        }
    }
</style>
