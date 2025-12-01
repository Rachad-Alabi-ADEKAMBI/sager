<template>
    <div class="claims-content">
        <div class="claims-header">
            <h2>Gestion des Créances</h2>
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

        <!-- Vue groupée par client -->
        <div class="showClaims" v-if="showClaims && groupByClient">
            <div
                class="table-container"
                v-for="client in groupedClaims"
                :key="client.client_id"
            >
                <div class="table-header">
                    <h3>
                        <i class="fas fa-user"></i>
                        {{ client.client_name }} - {{ client.client_phone }}
                    </h3>
                    <div style="display: flex; gap: 1rem; align-items: center">
                        <strong style="color: #fff3cd; font-size: 1.1rem">
                            Total: {{ formatAmount(client.total_debt) }} FCFA
                        </strong>
                        <strong style="color: #d4edda; font-size: 1.1rem">
                            Payé: {{ formatAmount(client.total_paid) }} FCFA
                        </strong>
                        <strong style="color: #f8d7da; font-size: 1.1rem">
                            Reste: {{ formatAmount(client.remaining) }} FCFA
                        </strong>
                    </div>
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

            <div v-if="groupedClaims.length === 0" class="no-data">
                <strong>Aucune créance disponible</strong>
            </div>
        </div>

        <!-- Vue liste complète -->
        <div class="showClaims" v-if="showClaims && !groupByClient">
            <div class="table-container">
                <div class="table-header">
                    <h3>Liste complète des créances</h3>
                    <strong>Total: {{ filteredClaims.length }}</strong>
                </div>

                <table class="table" v-if="filteredClaims.length > 0">
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
                        <tr v-for="claim in filteredClaims" :key="claim.id">
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
                    v-if="filteredClaims.length === 0"
                    class="mx-auto text-center"
                >
                    Aucune créance disponible
                </strong>
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
                    @click="closeAddClientModal"
                    style="cursor: pointer; font-size: 1.5rem"
                >
                    &times;
                </span>
            </div>

            <form @submit.prevent="addClientForm">
                <div class="form-group" style="margin-bottom: 1rem">
                    <label>Nom du client</label>
                    <input
                        v-model="newClient.name"
                        type="text"
                        class="form-control"
                        required
                    />
                </div>

                <div class="form-group" style="margin-bottom: 1rem">
                    <label>Téléphone</label>
                    <input
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
                        margin-top: 2rem;
                        flex-wrap: wrap;
                    "
                >
                    <button
                        type="submit"
                        class="btn-primary"
                        style="flex: 1 1 45%; min-width: 120px"
                    >
                        <i class="fas fa-save"></i>
                        Enregistrer
                    </button>
                    <button
                        type="button"
                        @click="closeAddClientModal()"
                        style="
                            flex: 1 1 45%;
                            min-width: 120px;
                            background: #6c757d;
                            color: white;
                            border: none;
                            padding: 0.75rem;
                            border-radius: 10px;
                            cursor: pointer;
                        "
                    >
                        Annuler
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
                <h3>Ajouter une nouvelle créance</h3>
                <span
                    class="close"
                    @click="closeAddClaimModal"
                    style="cursor: pointer; font-size: 1.5rem"
                >
                    &times;
                </span>
            </div>

            <form @submit.prevent="addClaimForm">
                <div class="form-group" style="margin-bottom: 1rem">
                    <label>Client</label>
                    <select
                        v-model="newClaim.client_id"
                        class="form-control"
                        required
                    >
                        <option value="">Sélectionner un client</option>
                        <option
                            v-for="client in clients"
                            :key="client.id"
                            :value="client.id"
                        >
                            {{ client.name }} - {{ client.phone }}
                        </option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 1rem">
                    <label>Montant de la dette</label>
                    <input
                        v-model="newClaim.debt_amount"
                        type="number"
                        class="form-control"
                        step="0.01"
                        min="0"
                        required
                    />
                </div>

                <div class="form-group" style="margin-bottom: 1rem">
                    <label>Commentaire</label>
                    <textarea
                        v-model="newClaim.comment"
                        class="form-control"
                        rows="3"
                        placeholder="Ex: Facture N°123..."
                    ></textarea>
                </div>

                <div
                    style="
                        display: flex;
                        gap: 1rem;
                        margin-top: 2rem;
                        flex-wrap: wrap;
                    "
                >
                    <button
                        type="submit"
                        class="btn-primary"
                        style="flex: 1 1 45%; min-width: 120px"
                    >
                        <i class="fas fa-save"></i>
                        Enregistrer
                    </button>
                    <button
                        type="button"
                        @click="closeAddClaimModal()"
                        style="
                            flex: 1 1 45%;
                            min-width: 120px;
                            background: #6c757d;
                            color: white;
                            border: none;
                            padding: 0.75rem;
                            border-radius: 10px;
                            cursor: pointer;
                        "
                    >
                        Annuler
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
            <div class="modal-header">
                <h3>
                    Ajouter un paiement pour
                    <strong>{{ selectedClaim.client_name }}</strong>
                </h3>
                <span
                    class="close"
                    @click="closeAddPaymentModal"
                    style="cursor: pointer; font-size: 1.5rem"
                >
                    &times;
                </span>
            </div>

            <div
                style="
                    background: #f8f9fa;
                    padding: 1rem;
                    border-radius: 8px;
                    margin: 1rem 0;
                "
            >
                <p>
                    <strong>Montant dû:</strong>
                    {{ formatAmount(selectedClaim.debt_amount) }} FCFA
                </p>
                <p>
                    <strong>Déjà payé:</strong>
                    {{ formatAmount(getClaimPaidAmount(selectedClaim.id)) }}
                    FCFA
                </p>
                <p>
                    <strong>Reste à payer:</strong>
                    <span style="color: #dc3545; font-weight: bold">
                        {{
                            formatAmount(
                                selectedClaim.debt_amount -
                                    getClaimPaidAmount(selectedClaim.id)
                            )
                        }}
                        FCFA
                    </span>
                </p>
            </div>

            <form @submit.prevent="addPaymentForm">
                <div class="form-group" style="margin-bottom: 1rem">
                    <label>Montant du paiement</label>
                    <input
                        v-model="newPayment.amount"
                        type="number"
                        class="form-control"
                        step="0.01"
                        min="0"
                        required
                    />
                </div>

                <div class="form-group" style="margin-bottom: 1rem">
                    <label>Méthode de paiement</label>
                    <select
                        v-model="newPayment.payment_method"
                        class="form-control"
                        required
                    >
                        <option value="espèces">Espèces</option>
                        <option value="carte bancaire">Carte bancaire</option>
                        <option value="virement">Virement</option>
                        <option value="mobile money">Mobile Money</option>
                        <option value="chèque">Chèque</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 1rem">
                    <label>Commentaire (optionnel)</label>
                    <textarea
                        v-model="newPayment.comment"
                        class="form-control"
                        rows="2"
                        placeholder="Ex: Acompte sur facture..."
                    ></textarea>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem">
                    <button type="submit" class="btn-primary" style="flex: 1">
                        <i class="fas fa-save"></i>
                        Enregistrer le paiement
                    </button>
                    <button
                        type="button"
                        @click="closeAddPaymentModal()"
                        style="
                            flex: 1;
                            background: #6c757d;
                            color: white;
                            border: none;
                            padding: 0.75rem;
                            border-radius: 10px;
                            cursor: pointer;
                        "
                    >
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Supprimer Créance -->
    <div
        v-if="showDeleteClaimModal"
        class="modal-delete-claim"
        @click.self="closeDeleteClaimModal"
    >
        <div class="modal-content" style="padding: 2rem; max-width: 400px">
            <div class="modal-header">
                <h3>Confirmer la suppression</h3>
                <span
                    class="close"
                    @click="closeDeleteClaimModal"
                    style="cursor: pointer"
                >
                    &times;
                </span>
            </div>
            <div class="modal-body">
                <p>
                    Voulez-vous vraiment supprimer cette créance de
                    <strong>{{ currentClaim.client_name }}</strong>
                    d'un montant de
                    <strong>
                        {{ formatAmount(currentClaim.debt_amount) }} FCFA
                    </strong>
                    ?
                </p>
            </div>
            <div
                style="
                    margin-top: 1rem;
                    display: flex;
                    gap: 1rem;
                    justify-content: flex-end;
                "
            >
                <button
                    @click="deleteClaim"
                    style="
                        background: #dc3545;
                        color: #fff;
                        border: none;
                        padding: 0.5rem 1rem;
                        border-radius: 5px;
                        cursor: pointer;
                    "
                >
                    Supprimer
                </button>
                <button
                    @click="closeDeleteClaimModal"
                    style="
                        background: #6c757d;
                        color: #fff;
                        border: none;
                        padding: 0.5rem 1rem;
                        border-radius: 5px;
                        cursor: pointer;
                    "
                >
                    Annuler
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
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        h1 { text-align: center; color: #333; margin-bottom: 10px; }
                        .info { text-align: center; margin-bottom: 30px; color: #666; }
                        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                        th { background-color: #667eea; color: white; padding: 12px; text-align: left; border: 1px solid #ddd; }
                        .total-row { background-color: #f0f4ff; font-weight: bold; }
                        @media print {
                            button { display: none; }
                        }
                    </style>
                </head>
                <body>
                    <h1>Liste des Créances</h1>
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

                    <button onclick="window.print()" style="padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">
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

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px 10px 0 0;
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

    /* Added styling for new client button */
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

    .btn-sm {
        padding: 0.5rem 0.75rem;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }

    .btn-edit {
        background: #007bff;
        color: white;
    }

    .btn-edit:hover {
        background: #0056b3;
    }

    .btn-delete {
        background: #dc3545;
        color: white;
    }

    .btn-delete:hover {
        background: #c82333;
    }

    /* Inputs */
    .search-input,
    .filter-select {
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 0.95rem;
        min-width: 200px;
        transition: border-color 0.3s ease;
    }

    .search-input:focus,
    .filter-select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    /* Table */
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

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    /* Modals */
    .modal-add-claim,
    .modal-add-payment,
    .modal-delete-claim,
    .modal-add-client {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .modal-content {
        background: white;
        border-radius: 10px;
        padding: 2rem;
        max-width: 600px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .modal-header h3 {
        margin: 0;
        color: #333;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #495057;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: border-color 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .no-data {
        text-align: center;
        padding: 3rem;
        color: #6c757d;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .header-actions {
            flex-direction: column;
            width: 100%;
        }

        .search-input,
        .filter-select {
            width: 100%;
        }

        .table {
            display: block;
            overflow-x: auto;
        }

        .action-buttons {
            flex-direction: column;
        }
    }
</style>
