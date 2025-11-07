<template>
    <div class="clients-content">
        <!-- Added loading and error states -->
        <div v-if="isLoading" class="loading-overlay">
            <div class="spinner"></div>
            <p>Chargement des données...</p>
        </div>

        <div v-if="errorMessage" class="error-banner">
            <i class="fas fa-exclamation-triangle"></i>
            {{ errorMessage }}
            <button @click="retryFetch" class="btn-retry">Réessayer</button>
        </div>

        <div class="clients-header">
            <h2>Gestion des Clients et Créances</h2>
            <div class="header-actions">
                <button class="btn btn-primary" @click="openAddClientModal()">
                    <i class="fas fa-user-plus"></i>
                    Ajouter un client
                </button>

                <!-- Added global print button with #17a2b8 color -->
                <button class="btn btn-print" @click="printAllClients()">
                    <i class="fas fa-print"></i>
                    Imprimer tout
                </button>

                <input
                    type="text"
                    class="search-input"
                    placeholder="Rechercher un client..."
                    v-model="searchQuery"
                />

                <select class="filter-select" v-model="statusFilter">
                    <option>Tous les statuts</option>
                    <option>Avec créances</option>
                    <option>Sans créances</option>
                </select>
            </div>
        </div>

        <!-- Liste des clients -->
        <div class="showClients" v-if="showClients">
            <div class="table-container">
                <div class="table-header">
                    <h3>Liste des Clients</h3>
                    <strong>Total: {{ filteredClients.length }}</strong>
                </div>

                <table class="table" v-if="paginatedClients.length > 0">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Téléphone</th>
                            <th>Créances</th>
                            <th>Total dû</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Using paginatedClients instead of filteredClients -->
                        <tr v-for="client in paginatedClients" :key="client.id">
                            <td data-label="Nom">
                                <strong>{{ client.name }}</strong>
                            </td>
                            <td data-label="Téléphone">{{ client.phone }}</td>
                            <td data-label="Créances">
                                {{ getClientClaimsCount(client.id) }}
                            </td>
                            <td data-label="Total dû">
                                <strong>
                                    {{
                                        formatAmount(
                                            getClientTotalDebt(client.id)
                                        )
                                    }}
                                    FCFA
                                </strong>
                            </td>
                            <td data-label="Statut">
                                <span
                                    :class="[
                                        'status-badge',
                                        getClientClaimsCount(client.id) > 0
                                            ? 'status-debt'
                                            : 'status-clear',
                                    ]"
                                >
                                    {{
                                        getClientClaimsCount(client.id) > 0
                                            ? 'Créances en cours'
                                            : 'Aucune créance'
                                    }}
                                </span>
                            </td>
                            <td data-label="Actions">
                                <div class="action-buttons">
                                    <!-- Added print button per client -->
                                    <button
                                        class="btn-sm btn-print"
                                        title="Imprimer"
                                        @click="printClient(client)"
                                    >
                                        <i class="fas fa-print"></i>
                                    </button>

                                    <button
                                        class="btn-sm btn-info"
                                        title="Voir les créances"
                                        @click="viewClientClaims(client)"
                                    >
                                        <i
                                            class="fas fa-file-invoice-dollar"
                                        ></i>
                                    </button>

                                    <button
                                        class="btn-sm btn-success"
                                        title="Ajouter une créance"
                                        @click="openAddClaimModal(client)"
                                    >
                                        <i class="fas fa-plus"></i>
                                    </button>

                                    <button
                                        class="btn-sm btn-delete"
                                        title="Supprimer le client"
                                        @click="openDeleteClientModal(client)"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Updated no data message and check paginatedClients -->
                <strong v-if="paginatedClients.length == 0" class="no-data">
                    Aucun client trouvé
                </strong>

                <!-- Added pagination controls -->
                <div class="pagination" v-if="totalPages > 1">
                    <!-- Updated pagination button classes and simplified logic -->
                    <button
                        @click="currentPage--"
                        :disabled="currentPage === 1"
                        class="btn-pagination"
                    >
                        Précédent
                    </button>
                    <!-- Updated page info span -->
                    <span class="page-info">
                        Page {{ currentPage }} sur {{ totalPages }}
                    </span>
                    <button
                        @click="currentPage++"
                        :disabled="currentPage === totalPages"
                        class="btn-pagination"
                    >
                        Suivant
                    </button>
                </div>
            </div>
        </div>

        <!-- Vue détaillée des créances d'un client -->
        <div class="showClaims" v-if="showClaims">
            <div class="table-container">
                <div
                    class="table-header"
                    style="
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        flex-wrap: wrap;
                        gap: 0.5rem;
                    "
                >
                    <h3
                        style="
                            display: flex;
                            align-items: center;
                            gap: 0.5rem;
                            flex: 1 1 100%;
                        "
                    >
                        <span @click="backToClients()" class="back-button">
                            <i
                                class="fas fa-arrow-left"
                                style="color: white"
                            ></i>
                            <span style="color: white">Retour</span>
                        </span>
                        | Créances de
                        <strong>{{ selectedClient.name }}</strong>
                    </h3>
                    <strong style="flex: 1 1 100%; text-align: right">
                        Total: {{ clientClaims.length }}
                    </strong>
                </div>

                <table class="table" v-if="clientClaims.length > 0">
                    <thead>
                        <tr>
                            <th>Montant</th>
                            <th>Commentaire</th>
                            <th>Total payé</th>
                            <th>Reste à payer</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="claim in clientClaims" :key="claim.id">
                            <td data-label="Montant">
                                <strong>
                                    {{ formatAmount(claim.amount) }} FCFA
                                </strong>
                            </td>
                            <td data-label="Commentaire">
                                {{ claim.comment }}
                            </td>
                            <td data-label="Total payé">
                                {{ formatAmount(getClaimTotalPaid(claim.id)) }}
                                FCFA
                            </td>
                            <td data-label="Reste à payer">
                                <strong>
                                    {{
                                        formatAmount(
                                            claim.amount -
                                                getClaimTotalPaid(claim.id)
                                        )
                                    }}
                                    FCFA
                                </strong>
                            </td>
                            <td data-label="Statut">
                                <span
                                    :class="[
                                        'status-badge',
                                        getClaimTotalPaid(claim.id) >=
                                        claim.amount
                                            ? 'status-paid'
                                            : 'status-pending',
                                    ]"
                                >
                                    {{
                                        getClaimTotalPaid(claim.id) >=
                                        claim.amount
                                            ? 'Payé'
                                            : 'En cours'
                                    }}
                                </span>
                            </td>
                            <td data-label="Date">
                                {{ formatDateTime(claim.created_at) }}
                            </td>
                            <td data-label="Actions">
                                <div class="action-buttons">
                                    <!-- Added print button per claim -->
                                    <button
                                        class="btn-sm btn-print"
                                        title="Imprimer"
                                        @click="printClaim(claim)"
                                    >
                                        <i class="fas fa-print"></i>
                                    </button>

                                    <button
                                        class="btn-sm btn-warning"
                                        title="Voir les paiements"
                                        @click="viewClaimPayments(claim)"
                                    >
                                        <i class="fas fa-money-bill-wave"></i>
                                    </button>

                                    <button
                                        class="btn-sm btn-success"
                                        title="Ajouter un paiement"
                                        @click="openAddPaymentModal(claim)"
                                        v-if="
                                            getClaimTotalPaid(claim.id) <
                                            claim.amount
                                        "
                                    >
                                        <i class="fas fa-plus"></i>
                                    </button>

                                    <button
                                        class="btn-sm btn-delete"
                                        title="Supprimer la créance"
                                        @click="openDeleteClaimModal(claim)"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Updated no data message -->
                <strong v-if="clientClaims.length == 0" class="no-data">
                    Aucune créance enregistrée
                </strong>
            </div>
        </div>

        <!-- Vue des paiements d'une créance -->
        <div class="showPayments" v-if="showPayments">
            <div class="table-container">
                <div class="table-header">
                    <h3 style="display: flex; align-items: center; gap: 0.5rem">
                        <span @click="backToClaims()" class="back-button">
                            <i
                                class="fas fa-arrow-left"
                                style="color: white"
                            ></i>
                            <span style="color: white">Retour</span>
                        </span>
                        | Paiements pour la créance de
                        <strong>
                            {{ formatAmount(selectedClaim.amount) }} FCFA
                        </strong>
                    </h3>
                    <strong>Total: {{ claimPayments.length }}</strong>
                </div>

                <table class="table" v-if="claimPayments.length > 0">
                    <thead>
                        <tr>
                            <th>Montant</th>
                            <th>Méthode</th>
                            <th>Commentaire</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="payment in claimPayments" :key="payment.id">
                            <td data-label="Montant">
                                <strong>
                                    {{ formatAmount(payment.amount) }} FCFA
                                </strong>
                            </td>
                            <td data-label="Méthode">
                                <span class="payment-method-badge">
                                    {{
                                        payment.payment_method || 'Non spécifié'
                                    }}
                                </span>
                            </td>
                            <td data-label="Commentaire">
                                {{ payment.comment }}
                            </td>
                            <td data-label="Date">
                                {{ formatDateTime(payment.created_at) }}
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Updated no data message -->
                <strong v-if="claimPayments.length == 0" class="no-data">
                    Aucun paiement enregistré
                </strong>
            </div>
        </div>
    </div>

    <!-- Modal Ajouter Client -->
    <div
        v-if="showAddClientModal"
        class="modal-overlay"
        @click.self="closeAddClientModal()"
    >
        <div class="modal-content">
            <div class="modal-header">
                <h3>Ajouter un nouveau client</h3>
                <span class="close" @click="closeAddClientModal()">
                    &times;
                </span>
            </div>

            <form @submit.prevent="addClient">
                <div class="form-group">
                    <label>Nom du client</label>
                    <input
                        v-model="newClient.name"
                        type="text"
                        class="form-control"
                        required
                        placeholder="Entrez le nom"
                    />
                </div>

                <div class="form-group">
                    <label>Téléphone</label>
                    <input
                        v-model="newClient.phone"
                        type="tel"
                        class="form-control"
                        required
                        placeholder="Entrez le numéro de téléphone"
                    />
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i>
                        Enregistrer
                    </button>
                    <button
                        type="button"
                        @click="closeAddClientModal()"
                        class="btn-secondary"
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
        class="modal-overlay"
        @click.self="closeAddClaimModal()"
    >
        <div class="modal-content">
            <div class="modal-header">
                <h3>
                    Ajouter une créance pour
                    <strong>{{ selectedClient.name }}</strong>
                </h3>
                <span class="close" @click="closeAddClaimModal()">&times;</span>
            </div>

            <form @submit.prevent="addClaim">
                <div class="form-group">
                    <label>Montant de la créance</label>
                    <input
                        v-model.number="newClaim.amount"
                        type="number"
                        class="form-control"
                        step="0.01"
                        min="0"
                        required
                        placeholder="Entrez le montant"
                    />
                </div>

                <div class="form-group">
                    <label>Commentaire</label>
                    <textarea
                        v-model="newClaim.comment"
                        class="form-control"
                        rows="3"
                        placeholder="Ajoutez un commentaire (optionnel)"
                    ></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i>
                        Enregistrer
                    </button>
                    <button
                        type="button"
                        @click="closeAddClaimModal()"
                        class="btn-secondary"
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
        class="modal-overlay"
        @click.self="closeAddPaymentModal()"
    >
        <div class="modal-content">
            <div class="modal-header">
                <h3>Enregistrer un paiement</h3>
                <span class="close" @click="closeAddPaymentModal()">
                    &times;
                </span>
            </div>

            <form @submit.prevent="addPayment">
                <div class="form-group">
                    <label>Montant du paiement</label>
                    <input
                        v-model.number="newPayment.amount"
                        type="number"
                        class="form-control"
                        step="0.01"
                        min="0"
                        :max="
                            selectedClaim.amount -
                            getClaimTotalPaid(selectedClaim.id)
                        "
                        required
                        placeholder="Entrez le montant"
                    />
                    <small class="form-hint">
                        Reste à payer:
                        {{
                            formatAmount(
                                selectedClaim.amount -
                                    getClaimTotalPaid(selectedClaim.id)
                            )
                        }}
                        FCFA
                    </small>
                </div>

                <div class="form-group">
                    <label>Méthode de paiement</label>
                    <!-- Set default value to "espèces" -->
                    <select
                        v-model="newPayment.payment_method"
                        class="form-control"
                        required
                    >
                        <option value="espèces">Espèces</option>
                        <option value="mobile money">Mobile Money</option>
                        <option value="transfert bancaire">
                            Transfert Bancaire
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Commentaire</label>
                    <textarea
                        v-model="newPayment.comment"
                        class="form-control"
                        rows="3"
                        placeholder="Ajoutez un commentaire (optionnel)"
                    ></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i>
                        Enregistrer
                    </button>
                    <button
                        type="button"
                        @click="closeAddPaymentModal()"
                        class="btn-secondary"
                    >
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Supprimer Client -->
    <div
        v-if="showDeleteClientModal"
        class="modal-overlay"
        @click.self="closeDeleteClientModal()"
    >
        <div class="modal-content modal-confirm">
            <div class="modal-header">
                <h3>Confirmer la suppression</h3>
                <span class="close" @click="closeDeleteClientModal()">
                    &times;
                </span>
            </div>
            <div class="modal-body">
                <p>
                    Voulez-vous vraiment supprimer le client
                    <strong>{{ selectedClient.name }}</strong>
                    ?
                </p>
                <p class="warning-text">
                    Cette action supprimera également toutes les créances
                    associées.
                </p>
            </div>
            <div class="form-actions">
                <button
                    @click="deleteClient"
                    class="btn btn-danger text-center"
                >
                    <i class="fas fa-trash"></i>
                    Supprimer
                </button>
                <button @click="closeDeleteClientModal()" class="btn-secondary">
                    Annuler
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Supprimer Créance -->
    <div
        v-if="showDeleteClaimModal"
        class="modal-overlay"
        @click.self="closeDeleteClaimModal()"
    >
        <div class="modal-content modal-confirm">
            <div class="modal-header">
                <h3>Confirmer la suppression</h3>
                <span class="close" @click="closeDeleteClaimModal()">
                    &times;
                </span>
            </div>
            <div class="modal-body">
                <p>
                    Voulez-vous vraiment supprimer cette créance de
                    <strong>
                        {{ formatAmount(selectedClaim.amount) }} FCFA
                    </strong>
                    ?
                </p>
            </div>
            <div class="form-actions">
                <button @click="deleteClaim" class="btn btn-danger">
                    <i class="fas fa-trash"></i>
                    Supprimer
                </button>
                <button @click="closeDeleteClaimModal()" class="btn-secondary">
                    Annuler
                </button>
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
                claims: [],
                payments: [],

                isLoading: false,
                errorMessage: '',

                // Vue states
                showClients: true,
                showClaims: false,
                showPayments: false,

                // Modal states
                showAddClientModal: false,
                showAddClaimModal: false,
                showAddPaymentModal: false,
                showDeleteClientModal: false,
                showDeleteClaimModal: false,

                // Selected items
                selectedClient: {},
                selectedClaim: {},

                // Filters
                searchQuery: '',
                statusFilter: 'Tous les statuts',

                // Added pagination variables
                currentPage: 1,
                itemsPerPage: 10,

                // Form data
                newClient: {
                    name: '',
                    phone: '',
                },
                newClaim: {
                    amount: '',
                    comment: '',
                },
                // Set default payment method to "espèces"
                newPayment: {
                    amount: '',
                    payment_method: 'espèces',
                    comment: '',
                },
            };
        },

        mounted() {
            console.log('[v0] Component mounted, fetching data...');
            this.fetchAllData();
        },

        computed: {
            filteredClients() {
                let filtered = this.clients;

                // Filter by search query
                if (this.searchQuery) {
                    const query = this.searchQuery.toLowerCase();
                    filtered = filtered.filter(
                        (c) =>
                            c.name.toLowerCase().includes(query) ||
                            c.phone.includes(query)
                    );
                }

                // Filter by status
                if (this.statusFilter === 'Avec créances') {
                    filtered = filtered.filter(
                        (c) => this.getClientClaimsCount(c.id) > 0
                    );
                } else if (this.statusFilter === 'Sans créances') {
                    filtered = filtered.filter(
                        (c) => this.getClientClaimsCount(c.id) === 0
                    );
                }

                return filtered;
            },

            // Added pagination computed properties
            paginatedClients() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                const end = start + this.itemsPerPage;
                return this.filteredClients.slice(start, end);
            },

            totalPages() {
                return Math.ceil(
                    this.filteredClients.length / this.itemsPerPage
                );
            },

            clientClaims() {
                console.log('[v0] clientClaims computed appelé');
                console.log('[v0] selectedClient:', this.selectedClient);
                console.log(
                    '[v0] Total claims disponibles:',
                    this.claims.length
                );

                if (!this.selectedClient || !this.selectedClient.id) {
                    console.log('[v0] Pas de client sélectionné');
                    return [];
                }

                const filtered = this.claims.filter(
                    (claim) => claim.client_id === this.selectedClient.id
                );

                console.log(
                    '[v0] Créances filtrées pour client',
                    this.selectedClient.id,
                    ':',
                    filtered.length
                );
                console.log('[v0] Créances:', filtered);

                return filtered;
            },

            claimPayments() {
                return this.payments.filter(
                    (payment) => payment.claim_id === this.selectedClaim.id
                );
            },
        },

        methods: {
            async fetchAllData() {
                this.isLoading = true;
                this.errorMessage = '';

                console.log('[v0] ========================================');
                console.log('[v0] Début du chargement des données...');
                console.log('[v0] URL de base:', window.location.origin);
                console.log('[v0] Environment:', process.env.NODE_ENV);

                try {
                    console.log('[v0] Appel API: /clientslist');
                    const clientsRes = await axios.get('/clientslist');
                    console.log(
                        '[v0] ✓ Clients reçus:',
                        clientsRes.data.length,
                        'clients'
                    );

                    console.log('[v0] Appel API: /claimslist');
                    const claimsRes = await axios.get('/claimslist');
                    console.log(
                        '[v0] ✓ Créances reçues:',
                        claimsRes.data.length,
                        'créances'
                    );
                    console.log(
                        '[v0] Structure de la première créance:',
                        claimsRes.data[0]
                    );

                    console.log('[v0] Appel API: /claims/payments');
                    const paymentsRes = await axios.get('/claims/payments');
                    console.log(
                        '[v0] ✓ Paiements reçus:',
                        paymentsRes.data.length,
                        'paiements'
                    );

                    if (!Array.isArray(clientsRes.data)) {
                        throw new Error(
                            'Les données clients ne sont pas au format attendu (pas un tableau)'
                        );
                    }
                    if (!Array.isArray(claimsRes.data)) {
                        throw new Error(
                            'Les données créances ne sont pas au format attendu (pas un tableau)'
                        );
                    }
                    if (!Array.isArray(paymentsRes.data)) {
                        throw new Error(
                            'Les données paiements ne sont pas au format attendu (pas un tableau)'
                        );
                    }

                    const normalizedClaims = this.normalizeClaims(
                        claimsRes.data
                    );
                    console.log(
                        '[v0] Créances normalisées:',
                        normalizedClaims.length
                    );
                    if (normalizedClaims.length > 0) {
                        console.log(
                            '[v0] Première créance normalisée:',
                            normalizedClaims[0]
                        );
                    }

                    this.clients = clientsRes.data;
                    this.claims = normalizedClaims;
                    this.payments = paymentsRes.data;

                    console.log('[v0] ✓ Données assignées avec succès');
                    console.log(
                        '[v0] this.clients.length:',
                        this.clients.length
                    );
                    console.log('[v0] this.claims.length:', this.claims.length);
                    console.log(
                        '[v0] this.payments.length:',
                        this.payments.length
                    );

                    await this.$nextTick();
                    console.log('[v0] ✓ DOM mis à jour (après $nextTick)');

                    console.log('[v0] Vérification finale des données:');
                    console.log(
                        '[v0] - Nombre de clients:',
                        this.clients.length
                    );
                    console.log(
                        '[v0] - Nombre de créances:',
                        this.claims.length
                    );
                    console.log(
                        '[v0] - Nombre de paiements:',
                        this.payments.length
                    );
                } catch (error) {
                    console.error('[v0] ❌ ERREUR lors du chargement:', error);

                    if (error.response) {
                        this.errorMessage = `Erreur ${error.response.status}: ${error.response.statusText}`;
                        console.error(
                            "[v0] Détails de l'erreur:",
                            error.response.data
                        );
                    } else if (error.request) {
                        this.errorMessage =
                            'Impossible de contacter le serveur. Vérifiez votre connexion.';
                        console.error('[v0] Pas de réponse du serveur');
                    } else {
                        this.errorMessage =
                            error.message || 'Une erreur est survenue';
                    }

                    console.log(
                        '[v0] ========================================'
                    );
                } finally {
                    this.isLoading = false;
                }
            },

            normalizeClaims(claims) {
                console.log('[v0] ========================================');
                console.log('[v0] Normalisation des créances...');

                if (!Array.isArray(claims)) {
                    console.error(
                        "[v0] ❌ Claims n'est pas un tableau:",
                        claims
                    );
                    return [];
                }

                console.log(
                    '[v0] Nombre de créances à normaliser:',
                    claims.length
                );

                const normalized = claims.map((claim, index) => {
                    // Vérifier si la créance a debt_amount ou amount
                    const hasDebtAmount = claim.hasOwnProperty('debt_amount');
                    const hasAmount = claim.hasOwnProperty('amount');

                    console.log(
                        `[v0] Créance ${index + 1}/${claims.length} (ID: ${
                            claim.id
                        }):`,
                        {
                            hasDebtAmount,
                            hasAmount,
                            debt_amount: claim.debt_amount,
                            amount: claim.amount,
                            client_id: claim.client_id,
                        }
                    );

                    const normalizedClaim = {
                        id: claim.id,
                        client_id: claim.client_id,
                        client_name: claim.client_name || '',
                        client_phone: claim.client_phone || '',
                        comment: claim.comment || '',
                        created_at: claim.created_at,
                        updated_at: claim.updated_at,
                        // Utiliser debt_amount si disponible, sinon amount, sinon 0
                        amount: hasDebtAmount
                            ? parseFloat(claim.debt_amount) || 0
                            : parseFloat(claim.amount) || 0,
                    };

                    console.log(`[v0] ✓ Créance ${claim.id} normalisée:`, {
                        id: normalizedClaim.id,
                        client_id: normalizedClaim.client_id,
                        amount: normalizedClaim.amount,
                    });

                    return normalizedClaim;
                });

                console.log(
                    '[v0] ✓ Normalisation terminée:',
                    normalized.length,
                    'créances'
                );
                console.log('[v0] ========================================');

                return normalized;
            },

            retryFetch() {
                console.log('[v0] Retry fetch demandé');
                this.fetchAllData();
            },

            // Updated pagination methods with correct names and logic
            nextPage() {
                if (this.currentPage < this.totalPages) {
                    this.currentPage++;
                }
            },

            prevPage() {
                if (this.currentPage > 1) {
                    this.currentPage--;
                }
            },

            // Added print methods inspired by ProductsComponent
            printAllClients() {
                const printWindow = window.open('', '_blank');
                const currentDate = new Date().toLocaleDateString('fr-FR', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                });

                let totalDebt = 0;
                let totalClients = this.filteredClients.length;
                let clientsWithDebt = 0;

                let tableRows = '';
                this.filteredClients.forEach((client) => {
                    const claimsCount = this.getClientClaimsCount(client.id);
                    const debt = this.getClientTotalDebt(client.id);
                    totalDebt += debt;
                    if (claimsCount > 0) clientsWithDebt++;

                    tableRows += `
                        <tr>
                            <td style="padding: 12px; border: 1px solid #ddd;">${
                                client.name
                            }</td>
                            <td style="padding: 12px; border: 1px solid #ddd;">${
                                client.phone
                            }</td>
                            <td style="padding: 12px; border: 1px solid #ddd; text-align: center;">${claimsCount}</td>
                            <td style="padding: 12px; border: 1px solid #ddd; text-align: right; font-weight: bold;">${this.formatAmount(
                                debt
                            )} FCFA</td>
                            <td style="padding: 12px; border: 1px solid #ddd; text-align: center;">
                                <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; ${
                                    claimsCount > 0
                                        ? 'background: #fff3cd; color: #856404;'
                                        : 'background: #d4edda; color: #155724;'
                                }">
                                    ${
                                        claimsCount > 0
                                            ? 'Créances en cours'
                                            : 'Aucune créance'
                                    }
                                </span>
                            </td>
                        </tr>
                    `;
                });

                const htmlContent = `
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Liste des Clients - ${currentDate}</title>
                        <meta charset="UTF-8">
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                padding: 20px;
                                color: #333;
                            }
                            .print-header {
                                text-align: center;
                                margin-bottom: 30px;
                                border-bottom: 3px solid #17a2b8;
                                padding-bottom: 20px;
                            }
                            .print-header h1 {
                                margin: 0;
                                color: #17a2b8;
                                font-size: 28px;
                            }
                            .print-header p {
                                margin: 5px 0;
                                color: #666;
                            }
                            .info-section {
                                display: grid;
                                grid-template-columns: 1fr 1fr;
                                gap: 15px;
                                margin-bottom: 30px;
                                padding: 20px;
                                background: #f8f9fa;
                                border-radius: 8px;
                            }
                            .info-item {
                                display: flex;
                                justify-content: space-between;
                                padding: 8px 0;
                            }
                            .info-label {
                                font-weight: 600;
                                color: #495057;
                            }
                            .info-value {
                                color: #212529;
                            }
                            table {
                                width: 100%;
                                border-collapse: collapse;
                                margin-bottom: 30px;
                            }
                            th {
                                background: #17a2b8;
                                color: white;
                                padding: 12px;
                                text-align: left;
                                border: 1px solid #138496;
                            }
                            tr:nth-child(even) {
                                background: #f8f9fa;
                            }
                            .print-button {
                                background: #17a2b8;
                                color: white;
                                border: none;
                                padding: 12px 24px;
                                border-radius: 6px;
                                cursor: pointer;
                                font-size: 16px;
                                display: block;
                                margin: 20px auto;
                            }
                            .print-button:hover {
                                background: #138496;
                            }
                            @media print {
                                .print-button {
                                    display: none;
                                }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="print-header">
                            <h1>Liste des Clients et Créances</h1>
                            <p>Date d'impression: ${currentDate}</p>
                        </div>

                        <div class="info-section">
                            <div class="info-item">
                                <span class="info-label">Total clients:</span>
                                <span class="info-value">${totalClients}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Clients avec créances:</span>
                                <span class="info-value">${clientsWithDebt}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Total dû:</span>
                                <span class="info-value" style="font-weight: bold; color: #dc3545;">${this.formatAmount(
                                    totalDebt
                                )} FCFA</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Clients sans créances:</span>
                                <span class="info-value">${
                                    totalClients - clientsWithDebt
                                }</span>
                            </div>
                        </div>

                        <table>
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Téléphone</th>
                                    <th style="text-align: center;">Créances</th>
                                    <th style="text-align: right;">Total dû</th>
                                    <th style="text-align: center;">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows}
                            </tbody>
                        </table>

                        <button class="print-button" onclick="window.print()">
                            Imprimer
                        </button>
                    </body>
                    </html>
                `;

                printWindow.document.write(htmlContent);
                printWindow.document.close();
            },

            printClient(client) {
                const printWindow = window.open('', '_blank');
                const currentDate = new Date().toLocaleDateString('fr-FR', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                });

                const clientClaims = this.claims.filter(
                    (claim) => claim.client_id === client.id
                );
                const totalDebt = this.getClientTotalDebt(client.id);

                let claimsRows = '';
                clientClaims.forEach((claim) => {
                    const totalPaid = this.getClaimTotalPaid(claim.id);
                    const remaining = claim.amount - totalPaid;
                    const isPaid = totalPaid >= claim.amount;

                    claimsRows += `
                        <tr>
                            <td style="padding: 12px; border: 1px solid #ddd; font-weight: bold;">${this.formatAmount(
                                claim.amount
                            )} FCFA</td>
                            <td style="padding: 12px; border: 1px solid #ddd;">${
                                claim.comment || 'N/A'
                            }</td>
                            <td style="padding: 12px; border: 1px solid #ddd; text-align: right;">${this.formatAmount(
                                totalPaid
                            )} FCFA</td>
                            <td style="padding: 12px; border: 1px solid #ddd; text-align: right; font-weight: bold;">${this.formatAmount(
                                remaining
                            )} FCFA</td>
                            <td style="padding: 12px; border: 1px solid #ddd; text-align: center;">
                                <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; ${
                                    isPaid
                                        ? 'background: #d4edda; color: #155724;'
                                        : 'background: #fff3cd; color: #856404;'
                                }">
                                    ${isPaid ? 'Payé' : 'En cours'}
                                </span>
                            </td>
                            <td style="padding: 12px; border: 1px solid #ddd;">${this.formatDateTime(
                                claim.created_at
                            )}</td>
                        </tr>
                    `;
                });

                const htmlContent = `
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Détails Client - ${client.name}</title>
                        <meta charset="UTF-8">
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                padding: 20px;
                                color: #333;
                            }
                            .print-header {
                                text-align: center;
                                margin-bottom: 30px;
                                border-bottom: 3px solid #17a2b8;
                                padding-bottom: 20px;
                            }
                            .print-header h1 {
                                margin: 0;
                                color: #17a2b8;
                                font-size: 28px;
                            }
                            .print-header p {
                                margin: 5px 0;
                                color: #666;
                            }
                            .info-section {
                                display: grid;
                                grid-template-columns: 1fr 1fr;
                                gap: 15px;
                                margin-bottom: 30px;
                                padding: 20px;
                                background: #f8f9fa;
                                border-radius: 8px;
                            }
                            .info-item {
                                display: flex;
                                justify-content: space-between;
                                padding: 8px 0;
                            }
                            .info-label {
                                font-weight: 600;
                                color: #495057;
                            }
                            .info-value {
                                color: #212529;
                            }
                            .section-title {
                                font-size: 20px;
                                font-weight: 600;
                                color: #17a2b8;
                                margin: 30px 0 15px 0;
                                padding-bottom: 10px;
                                border-bottom: 2px solid #17a2b8;
                            }
                            table {
                                width: 100%;
                                border-collapse: collapse;
                                margin-bottom: 30px;
                            }
                            th {
                                background: #17a2b8;
                                color: white;
                                padding: 12px;
                                text-align: left;
                                border: 1px solid #138496;
                            }
                            tr:nth-child(even) {
                                background: #f8f9fa;
                            }
                            .print-button {
                                background: #17a2b8;
                                color: white;
                                border: none;
                                padding: 12px 24px;
                                border-radius: 6px;
                                cursor: pointer;
                                font-size: 16px;
                                display: block;
                                margin: 20px auto;
                            }
                            .print-button:hover {
                                background: #138496;
                            }
                            @media print {
                                .print-button {
                                    display: none;
                                }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="print-header">
                            <h1>Détails du Client</h1>
                            <p>Date d'impression: ${currentDate}</p>
                        </div>

                        <div class="info-section">
                            <div class="info-item">
                                <span class="info-label">Nom:</span>
                                <span class="info-value">${client.name}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Téléphone:</span>
                                <span class="info-value">${client.phone}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Nombre de créances:</span>
                                <span class="info-value">${
                                    clientClaims.length
                                }</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Total dû:</span>
                                <span class="info-value" style="font-weight: bold; color: #dc3545;">${this.formatAmount(
                                    totalDebt
                                )} FCFA</span>
                            </div>
                        </div>

                        <h2 class="section-title">Créances</h2>

                        ${
                            clientClaims.length > 0
                                ? `
                        <table>
                            <thead>
                                <tr>
                                    <th>Montant</th>
                                    <th>Commentaire</th>
                                    <th style="text-align: right;">Total payé</th>
                                    <th style="text-align: right;">Reste à payer</th>
                                    <th style="text-align: center;">Statut</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${claimsRows}
                            </tbody>
                        </table>
                        `
                                : '<p style="text-align: center; color: #6c757d; padding: 20px;">Aucune créance pour ce client</p>'
                        }

                        <button class="print-button" onclick="window.print()">
                            Imprimer
                        </button>
                    </body>
                    </html>
                `;

                printWindow.document.write(htmlContent);
                printWindow.document.close();
            },

            printClaim(claim) {
                const printWindow = window.open('', '_blank');
                const currentDate = new Date().toLocaleDateString('fr-FR', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                });

                const claimPayments = this.payments.filter(
                    (payment) => payment.claim_id === claim.id
                );
                const totalPaid = this.getClaimTotalPaid(claim.id);
                const remaining = claim.amount - totalPaid;

                let paymentsRows = '';
                claimPayments.forEach((payment) => {
                    paymentsRows += `
                        <tr>
                            <td style="padding: 12px; border: 1px solid #ddd; font-weight: bold;">${this.formatAmount(
                                payment.amount
                            )} FCFA</td>
                            <td style="padding: 12px; border: 1px solid #ddd;">
                                <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; background: #e7f3ff; color: #0056b3;">
                                    ${payment.payment_method || 'Non spécifié'}
                                </span>
                            </td>
                            <td style="padding: 12px; border: 1px solid #ddd;">${
                                payment.comment || 'N/A'
                            }</td>
                            <td style="padding: 12px; border: 1px solid #ddd;">${this.formatDateTime(
                                payment.created_at
                            )}</td>
                        </tr>
                    `;
                });

                const htmlContent = `
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Détails Créance - ${this.formatAmount(
                            claim.amount
                        )} FCFA</title>
                        <meta charset="UTF-8">
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                padding: 20px;
                                color: #333;
                            }
                            .print-header {
                                text-align: center;
                                margin-bottom: 30px;
                                border-bottom: 3px solid #17a2b8;
                                padding-bottom: 20px;
                            }
                            .print-header h1 {
                                margin: 0;
                                color: #17a2b8;
                                font-size: 28px;
                            }
                            .print-header p {
                                margin: 5px 0;
                                color: #666;
                            }
                            .info-section {
                                display: grid;
                                grid-template-columns: 1fr 1fr;
                                gap: 15px;
                                margin-bottom: 30px;
                                padding: 20px;
                                background: #f8f9fa;
                                border-radius: 8px;
                            }
                            .info-item {
                                display: flex;
                                justify-content: space-between;
                                padding: 8px 0;
                            }
                            .info-label {
                                font-weight: 600;
                                color: #495057;
                            }
                            .info-value {
                                color: #212529;
                            }
                            .section-title {
                                font-size: 20px;
                                font-weight: 600;
                                color: #17a2b8;
                                margin: 30px 0 15px 0;
                                padding-bottom: 10px;
                                border-bottom: 2px solid #17a2b8;
                            }
                            table {
                                width: 100%;
                                border-collapse: collapse;
                                margin-bottom: 30px;
                            }
                            th {
                                background: #17a2b8;
                                color: white;
                                padding: 12px;
                                text-align: left;
                                border: 1px solid #138496;
                            }
                            tr:nth-child(even) {
                                background: #f8f9fa;
                            }
                            .print-button {
                                background: #17a2b8;
                                color: white;
                                border: none;
                                padding: 12px 24px;
                                border-radius: 6px;
                                cursor: pointer;
                                font-size: 16px;
                                display: block;
                                margin: 20px auto;
                            }
                            .print-button:hover {
                                background: #138496;
                            }
                            @media print {
                                .print-button {
                                    display: none;
                                }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="print-header">
                            <h1>Détails de la Créance</h1>
                            <p>Date d'impression: ${currentDate}</p>
                        </div>

                        <div class="info-section">
                            <div class="info-item">
                                <span class="info-label">Client:</span>
                                <span class="info-value">${
                                    this.selectedClient.name
                                }</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Montant initial:</span>
                                <span class="info-value" style="font-weight: bold;">${this.formatAmount(
                                    claim.amount
                                )} FCFA</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Total payé:</span>
                                <span class="info-value" style="color: #28a745; font-weight: bold;">${this.formatAmount(
                                    totalPaid
                                )} FCFA</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Reste à payer:</span>
                                <span class="info-value" style="color: #dc3545; font-weight: bold;">${this.formatAmount(
                                    remaining
                                )} FCFA</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Commentaire:</span>
                                <span class="info-value">${
                                    claim.comment || 'N/A'
                                }</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Date de création:</span>
                                <span class="info-value">${this.formatDateTime(
                                    claim.created_at
                                )}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Nombre de paiements:</span>
                                <span class="info-value">${
                                    claimPayments.length
                                }</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Statut:</span>
                                <span class="info-value">
                                    <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; ${
                                        totalPaid >= claim.amount
                                            ? 'background: #d4edda; color: #155724;'
                                            : 'background: #fff3cd; color: #856404;'
                                    }">
                                        ${
                                            totalPaid >= claim.amount
                                                ? 'Payé'
                                                : 'En cours'
                                        }
                                    </span>
                                </span>
                            </div>
                        </div>

                        <h2 class="section-title">Historique des Paiements</h2>

                        ${
                            claimPayments.length > 0
                                ? `
                        <table>
                            <thead>
                                <tr>
                                    <th>Montant</th>
                                    <th>Méthode</th>
                                    <th>Commentaire</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${paymentsRows}
                            </tbody>
                        </table>
                        `
                                : '<p style="text-align: center; color: #6c757d; padding: 20px;">Aucun paiement enregistré</p>'
                        }

                        <button class="print-button" onclick="window.print()">
                            Imprimer
                        </button>
                    </body>
                    </html>
                `;

                printWindow.document.write(htmlContent);
                printWindow.document.close();
            },

            viewClientClaims(client) {
                console.log('[v0] ========================================');
                console.log('[v0] viewClientClaims appelé pour:', client);
                console.log('[v0] Client ID:', client.id);
                console.log(
                    '[v0] Total créances disponibles:',
                    this.claims.length
                );

                this.selectedClient = client;
                this.showClients = false;
                this.showClaims = true;

                console.log('[v0] État après changement:');
                console.log('[v0] - selectedClient:', this.selectedClient);
                console.log('[v0] - showClients:', this.showClients);
                console.log('[v0] - showClaims:', this.showClaims);

                this.$nextTick(() => {
                    console.log('[v0] Après $nextTick:');
                    console.log(
                        '[v0] - clientClaims computed:',
                        this.clientClaims
                    );
                    console.log(
                        '[v0] - clientClaims.length:',
                        this.clientClaims.length
                    );

                    if (this.clientClaims.length === 0) {
                        console.warn(
                            '[v0] ⚠️ ATTENTION: Aucune créance trouvée pour ce client!'
                        );
                        console.log(
                            '[v0] Vérification des créances disponibles:'
                        );
                        this.claims.forEach((claim) => {
                            console.log(
                                `[v0] - Créance ID ${claim.id}: client_id=${claim.client_id}, amount=${claim.amount}`
                            );
                        });
                    } else {
                        console.log(
                            '[v0] ✓ Créances trouvées:',
                            this.clientClaims.length
                        );
                        this.clientClaims.forEach((claim, index) => {
                            console.log(`[v0] Créance ${index + 1}:`, {
                                id: claim.id,
                                amount: claim.amount,
                                comment: claim.comment,
                            });
                        });
                    }

                    console.log(
                        '[v0] ========================================'
                    );
                });
            },

            viewClaimPayments(claim) {
                console.log('[v0] ========================================');
                console.log('[v0] viewClaimPayments appelé pour:', claim);
                console.log('[v0] Claim ID:', claim.id);
                console.log(
                    '[v0] Total paiements disponibles:',
                    this.payments.length
                );

                this.selectedClaim = claim;
                this.showClients = false;
                this.showClaims = false;
                this.showPayments = true;

                console.log('[v0] État après changement:');
                console.log('[v0] - selectedClaim:', this.selectedClaim);
                console.log('[v0] - showPayments:', this.showPayments);

                // Attendre le prochain tick et vérifier les paiements
                this.$nextTick(() => {
                    console.log('[v0] Après $nextTick:');
                    console.log(
                        '[v0] - claimPayments computed:',
                        this.claimPayments
                    );
                    console.log(
                        '[v0] - claimPayments.length:',
                        this.claimPayments.length
                    );

                    if (this.claimPayments.length === 0) {
                        console.warn(
                            '[v0] ⚠️ ATTENTION: Aucun paiement trouvé pour cette créance!'
                        );
                    } else {
                        console.log(
                            '[v0] ✓ Paiements trouvés:',
                            this.claimPayments.length
                        );
                    }

                    console.log(
                        '[v0] ========================================'
                    );
                });
            },

            backToClients() {
                this.showClients = true;
                this.showClaims = false;
                this.showPayments = false;
                this.selectedClient = {};
                // Reset to first page when returning to clients list
                this.currentPage = 1;
            },

            backToClaims() {
                this.showClients = false;
                this.showClaims = true;
                this.showPayments = false;
                this.selectedClaim = {};
            },

            // Helper methods
            formatAmount(value) {
                if (value === null || value === undefined || isNaN(value)) {
                    return '0';
                }
                return Number(value).toLocaleString('fr-FR');
            },

            formatDateTime(datetime) {
                if (!datetime) return 'N/A';
                try {
                    const date = new Date(datetime);
                    if (isNaN(date.getTime())) {
                        return 'Date invalide';
                    }
                    const options = {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                    };
                    return date.toLocaleString('fr-FR', options);
                } catch (error) {
                    console.error('[v0] Erreur de formatage de date:', error);
                    return 'N/A';
                }
            },

            getClientClaimsCount(clientId) {
                if (!Array.isArray(this.claims)) {
                    console.warn(
                        '[v0] ⚠️ Claims is not an array in getClientClaimsCount'
                    );
                    return 0;
                }
                const count = this.claims.filter(
                    (claim) => claim.client_id === clientId
                ).length;

                // Log uniquement si count > 0 pour éviter trop de logs
                if (count > 0) {
                    console.log(
                        `[v0] Client ${clientId} a ${count} créance(s)`
                    );
                }

                return count;
            },

            getClientTotalDebt(clientId) {
                if (!Array.isArray(this.claims)) {
                    console.warn(
                        '[v0] ⚠️ Claims is not an array in getClientTotalDebt'
                    );
                    return 0;
                }
                const clientClaims = this.claims.filter(
                    (claim) => claim.client_id === clientId
                );

                return clientClaims.reduce((total, claim) => {
                    const paid = this.getClaimTotalPaid(claim.id);
                    const amount = parseFloat(claim.amount) || 0;
                    const remaining = amount - paid;

                    return total + remaining;
                }, 0);
            },

            getClaimTotalPaid(claimId) {
                if (!Array.isArray(this.payments)) {
                    console.warn(
                        '[v0] ⚠️ Payments is not an array in getClaimTotalPaid'
                    );
                    return 0;
                }

                const claimPayments = this.payments.filter(
                    (payment) => payment.claim_id === claimId
                );

                return claimPayments.reduce((total, payment) => {
                    return total + (parseFloat(payment.amount) || 0);
                }, 0);
            },

            // Modal methods - Client
            openAddClientModal() {
                this.showAddClientModal = true;
            },

            closeAddClientModal() {
                this.showAddClientModal = false;
                this.newClient = { name: '', phone: '' };
            },

            async addClient() {
                if (!this.newClient.name || !this.newClient.phone) {
                    alert('Veuillez remplir tous les champs');
                    return;
                }

                this.isLoading = true;
                console.log('[v0] Ajout du client:', this.newClient);

                try {
                    const response = await axios.post(
                        '/clients',
                        this.newClient
                    );
                    console.log('[v0] Client ajouté:', response.data);

                    alert('Client ajouté avec succès');
                    this.closeAddClientModal();
                    await this.fetchAllData();
                } catch (error) {
                    console.error(
                        "[v0] Erreur lors de l'ajout du client:",
                        error
                    );
                    console.error('[v0] Détails:', error.response?.data);

                    const errorMsg =
                        error.response?.data?.message ||
                        "Erreur lors de l'ajout du client";
                    alert(errorMsg);
                } finally {
                    this.isLoading = false;
                }
            },

            openDeleteClientModal(client) {
                this.selectedClient = client;
                this.showDeleteClientModal = true;
            },

            closeDeleteClientModal() {
                this.showDeleteClientModal = false;
                this.selectedClient = {};
            },

            async deleteClient() {
                this.isLoading = true;
                console.log(
                    '[v0] Suppression du client:',
                    this.selectedClient.id
                );

                try {
                    await axios.post(
                        `/clients/${this.selectedClient.id}/delete`
                    );
                    console.log('[v0] Client supprimé');

                    alert('Client supprimé avec succès');
                    this.closeDeleteClientModal();
                    await this.fetchAllData();
                } catch (error) {
                    console.error('[v0] Erreur lors de la suppression:', error);
                    console.error('[v0] Détails:', error.response?.data);

                    const errorMsg =
                        error.response?.data?.message ||
                        'Erreur lors de la suppression du client';
                    alert(errorMsg);
                } finally {
                    this.isLoading = false;
                }
            },

            // Modal methods - Claim
            openAddClaimModal(client) {
                this.selectedClient = client;
                this.showAddClaimModal = true;
            },

            closeAddClaimModal() {
                this.showAddClaimModal = false;
                this.newClaim = { amount: '', comment: '' };
            },

            async addClaim() {
                if (
                    !this.newClaim.amount ||
                    parseFloat(this.newClaim.amount) <= 0
                ) {
                    alert('Veuillez entrer un montant valide');
                    return;
                }

                this.isLoading = true;
                const claimData = {
                    client_id: this.selectedClient.id,
                    amount: this.newClaim.amount,
                    comment: this.newClaim.comment,
                };

                console.log('[v0] Ajout de la créance:', claimData);

                try {
                    const response = await axios.post('/claims/add', claimData);
                    console.log('[v0] Créance ajoutée:', response.data);

                    if (response.data.success) {
                        alert(
                            response.data.message ||
                                'Créance ajoutée avec succès'
                        );
                        this.closeAddClaimModal();
                        await this.fetchAllData();
                    } else {
                        alert(
                            response.data.message ||
                                "Erreur lors de l'ajout de la créance"
                        );
                    }
                } catch (error) {
                    console.error(
                        "[v0] Erreur lors de l'ajout de la créance:",
                        error
                    );
                    console.error('[v0] Détails:', error.response?.data);

                    const errorMsg =
                        error.response?.data?.message ||
                        "Erreur lors de l'ajout de la créance";
                    alert(errorMsg);
                } finally {
                    this.isLoading = false;
                }
            },

            openDeleteClaimModal(claim) {
                this.selectedClaim = claim;
                this.showDeleteClaimModal = true;
            },

            closeDeleteClaimModal() {
                this.showDeleteClaimModal = false;
                this.selectedClaim = {};
            },

            async deleteClaim() {
                this.isLoading = true;
                console.log(
                    '[v0] Suppression de la créance:',
                    this.selectedClaim.id
                );

                try {
                    await axios.post(`/claims/${this.selectedClaim.id}/delete`);
                    console.log('[v0] Créance supprimée');

                    alert('Créance supprimée avec succès');
                    this.closeDeleteClaimModal();
                    await this.fetchAllData();
                } catch (error) {
                    console.error('[v0] Erreur lors de la suppression:', error);
                    console.error('[v0] Détails:', error.response?.data);

                    const errorMsg =
                        error.response?.data?.message ||
                        'Erreur lors de la suppression de la créance';
                    alert(errorMsg);
                } finally {
                    this.isLoading = false;
                }
            },

            // Modal methods - Payment
            openAddPaymentModal(claim) {
                this.selectedClaim = claim;
                this.showAddPaymentModal = true;
            },

            closeAddPaymentModal() {
                this.showAddPaymentModal = false;
                // Reset to default payment method "espèces"
                this.newPayment = {
                    amount: '',
                    payment_method: 'espèces',
                    comment: '',
                };
            },

            async addPayment() {
                if (
                    !this.newPayment.amount ||
                    parseFloat(this.newPayment.amount) <= 0
                ) {
                    alert('Veuillez entrer un montant valide');
                    return;
                }

                this.isLoading = true;
                const paymentData = {
                    claim_id: this.selectedClaim.id,
                    amount: this.newPayment.amount,
                    payment_method: this.newPayment.payment_method,
                    comment: this.newPayment.comment,
                };

                console.log('[v0] Ajout du paiement:', paymentData);

                try {
                    const response = await axios.post(
                        '/claims/pay',
                        paymentData
                    );
                    console.log('[v0] Paiement ajouté:', response.data);

                    alert('Paiement enregistré avec succès');
                    this.closeAddPaymentModal();
                    await this.fetchAllData();
                } catch (error) {
                    console.error(
                        "[v0] Erreur lors de l'ajout du paiement:",
                        error
                    );
                    console.error('[v0] Détails:', error.response?.data);

                    const errorMsg =
                        error.response?.data?.message ||
                        "Erreur lors de l'ajout du paiement";
                    alert(errorMsg);
                } finally {
                    this.isLoading = false;
                }
            },
        },
    };
</script>

<style scoped>
    /* Main container */
    .clients-content {
        padding: 2rem;
        background: #f8f9fa;
        min-height: 100vh;
    }

    /* Header */
    .clients-header {
        margin-bottom: 2rem;
    }

    .clients-header h2 {
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

    /* Added print button styling with #17a2b8 color */
    .btn-print {
        background: #17a2b8;
        color: white;
    }

    .btn-print:hover {
        background: #138496;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(23, 162, 184, 0.4);
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

    .btn-sm {
        padding: 0.5rem 0.75rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }

    .btn-info {
        background: #17a2b8;
        color: white;
    }

    .btn-info:hover {
        background: #138496;
    }

    .btn-success {
        background: #28a745;
        color: white;
    }

    .btn-success:hover {
        background: #218838;
    }

    .btn-warning {
        background: #ffc107;
        color: #212529;
    }

    .btn-warning:hover {
        background: #e0a800;
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
    }

    .search-input:focus,
    .filter-select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    /* Table container */
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

    /* Table */
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

    /* Added pagination styling */
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
        border-top: 1px solid #dee2e6;
    }

    .pagination-btn {
        padding: 0.5rem 1rem;
        border: 1px solid #dee2e6;
        background: white;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }

    .pagination-btn:hover:not(:disabled) {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Updated page info span class name */
    .page-info {
        font-weight: 500;
        color: #495057;
    }

    /* Renamed btn-pagination to be more generic */
    .btn-pagination {
        padding: 0.5rem 1rem;
        border: 1px solid #dee2e6;
        background: white;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }

    .btn-pagination:hover:not(:disabled) {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .btn-pagination:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Status badges */
    .status-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
    }

    .status-debt {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .status-clear {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .status-paid {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .payment-method-badge {
        padding: 0.3rem 0.6rem;
        border-radius: 12px;
        font-size: 0.8rem;
        background: #e7f3ff;
        color: #0056b3;
        font-weight: 500;
    }

    /* Action buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    /* Back button */
    .back-button {
        display: flex;
        align-items: center;
        cursor: pointer;
        color: #667eea;
        font-weight: bold;
        transition: all 0.2s ease;
    }

    .back-button:hover {
        color: #764ba2;
    }

    /* No data message */
    .no-data {
        display: block;
        text-align: center;
        padding: 3rem;
        color: #6c757d;
        font-size: 1.1rem;
    }

    /* Modal overlay */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    /* Modal content */
    .modal-content {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-confirm {
        max-width: 400px;
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
        font-size: 1.3rem;
    }

    .close {
        cursor: pointer;
        font-size: 1.5rem;
        color: #6c757d;
        transition: color 0.2s ease;
    }

    .close:hover {
        color: #333;
    }

    .modal-body {
        margin-bottom: 1.5rem;
    }

    .modal-body p {
        margin: 0.5rem 0;
        color: #495057;
    }

    .warning-text {
        color: #dc3545;
        font-weight: 500;
        font-size: 0.9rem;
    }

    /* Form elements */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: #495057;
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: border-color 0.2s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }

    .form-hint {
        display: block;
        margin-top: 0.5rem;
        color: #6c757d;
        font-size: 0.85rem;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .form-actions button {
        flex: 1;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .clients-content {
            padding: 1rem;
        }

        .header-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .search-input,
        .filter-select {
            width: 100%;
        }

        .table {
            display: block;
            overflow-x: auto;
        }

        .table thead {
            display: none;
        }

        .table tbody tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }

        .table td {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem;
            border-bottom: 1px solid #f1f1f1;
        }

        .table td:before {
            content: attr(data-label);
            font-weight: 600;
            color: #495057;
        }

        .action-buttons {
            justify-content: flex-end;
        }

        .modal-content {
            width: 95%;
            padding: 1.5rem;
        }

        .form-actions {
            flex-direction: column;
        }

        .pagination {
            flex-direction: column;
            gap: 0.5rem;
        }

        .pagination-btn {
            width: 100%;
            justify-content: center;
        }
    }

    /* Added loading overlay styles */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        color: white;
    }

    .spinner {
        border: 4px solid rgba(255, 255, 255, 0.3);
        border-top: 4px solid white;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
        margin-bottom: 1rem;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    /* Added error banner styles */
    .error-banner {
        background: #f44336;
        color: white;
        padding: 1rem;
        margin-bottom: 1rem;
        border-radius: 4px;
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .error-banner i {
        font-size: 1.5rem;
    }

    .btn-retry {
        background: white;
        color: #f44336;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        margin-left: auto;
    }

    .btn-retry:hover {
        background: #f5f5f5;
    }
</style>
