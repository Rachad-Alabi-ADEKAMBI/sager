<template>
    <div>
        <div class="users-content" v-if="showUsers">
            <div class="users-header">
                <h2>Équipe SAGER ( {{ sellers.length }} )</h2>
                <button class="btn-primary" @click="openAddUserModal()">
                    <i class="fas fa-user-plus"></i>
                    Ajouter un vendeur
                </button>
            </div>

            <div class="users-grid">
                <div
                    class="user-card"
                    v-for="seller in sellers"
                    :key="seller.id"
                >
                    <div
                        class="user-status"
                        :class="{
                            'status-active': seller.status === 'active',
                            'status-banned': seller.status === 'banned',
                        }"
                    >
                        {{ seller.status === 'active' ? 'Actif' : 'Banni' }}
                    </div>
                    <div class="user-card-header">
                        <div class="user-card-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="user-card-info">
                            <h3>{{ seller.name }}</h3>
                        </div>
                    </div>
                    <div class="user-details">
                        <div class="user-detail-item">
                            <span class="user-detail-label">Email:</span>
                            <span class="user-detail-value">
                                {{ seller.email }}
                            </span>
                        </div>
                        <div class="user-detail-item"></div>
                    </div>
                    <div class="user-actions">
                        <button
                            class="btn-sm btn-view"
                            @click="openSalesModal(seller)"
                        >
                            <i class="fas fa-eye"></i>
                            Voir activité
                        </button>
                        <button
                            class="btn-sm btn-ban"
                            @click="openBanUserModal(seller)"
                        >
                            <i class="fas fa-ban"></i>
                            Bannir
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="" v-if="addUserModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Ajouter un nouveau vendeur</h3>
                    <span class="close" @click="closeAddUserModal()">
                        &times;
                    </span>
                </div>
                <form @submit.prevent="submitAddUserForm">
                    <div class="form-group">
                        <label for="name">Nom complet</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control"
                            v-model="newUser.name"
                            required
                        />
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            v-model="newUser.email"
                            required
                        />
                    </div>
                    <div class="form-group">
                        <label for="password">Mot de passe temporaire</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            v-model="newUser.password"
                            required
                        />
                    </div>
                    <div style="display: flex; gap: 1rem; margin-top: 2rem">
                        <button
                            type="submit"
                            class="btn-primary"
                            style="flex: 1"
                        >
                            <i class="fas fa-user-plus"></i>
                            Ajouter l'utilisateur
                        </button>
                        <button
                            type="button"
                            @click="closeAddUserModal()"
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

        <div class="" v-if="salesModal && selectedSeller">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Liste des ventes de {{ selectedSeller.name }}</h3>
                    <span class="close" @click="closeSalesModal()">
                        &times;
                    </span>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Montant</th>
                            <th>Client</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-if="
                                selectedSeller.sales &&
                                selectedSeller.sales.length > 0
                            "
                            v-for="sale in selectedSeller.sales"
                            :key="sale.id"
                        >
                            <td data-label="Date">
                                {{ formatDateTime(sale.created_at) }}
                            </td>
                            <td data-label="Montant">
                                {{ formatAmount(sale.total) }} F CFA
                            </td>
                            <td data-label="Client">{{ sale.buyer_name }}</td>
                        </tr>
                        <tr v-else>
                            <td colspan="3">
                                Aucune vente enregistrée pour ce vendeur.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="" v-if="banUserModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Bannir le vendeur {{ selectedSeller.name }}</h3>
                    <span class="close" @click="closeBanUserModal()">
                        &times;
                    </span>
                </div>
                <p>
                    Veuillez confirmer le bannissement de cet utilisateur et
                    fournir un motif.
                </p>
                <form @submit.prevent="submitBanUserForm">
                    <div class="form-group">
                        <label for="banReason">Motif du bannissement</label>
                        <textarea
                            id="banReason"
                            v-model="banReason"
                            class="form-control"
                            placeholder="Entrez le motif du bannissement ici. ecrivez au moins 10 caractères."
                            rows="4"
                            required
                        ></textarea>
                    </div>
                    <div style="display: flex; gap: 1rem; margin-top: 2rem">
                        <button
                            type="submit"
                            class="btn-sm btn-ban"
                            style="flex: 1"
                        >
                            Confirmer le bannissement
                        </button>
                        <button
                            type="button"
                            @click="closeBanUserModal()"
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
    </div>
</template>

<script>
    export default {
        name: 'SellersComponent',

        data() {
            return {
                sellers: [],
                showUsers: true,
                addUserModal: false,
                salesModal: false,
                banUserModal: false,
                selectedSeller: null, // Vendeur sélectionné pour voir ses ventes
                banReason: '',
                newUser: {
                    name: '',
                    email: '',
                    password: '',
                    role: 'seller',
                },
            };
        },

        mounted() {
            this.fetchSellers();
        },

        methods: {
            fetchSellers() {
                this.showUsers = true;
                this.addUserModal = false;
                this.salesModal = false;

                axios
                    .get('/sellersList')
                    .then((response) => {
                        this.sellers = response.data;
                    })
                    .catch((error) => {
                        console.error(
                            'Erreur lors de la récupération des données :',
                            error
                        );
                    });
            },

            // Modales de gestion
            openAddUserModal() {
                this.showUsers = false;
                this.addUserModal = true;
                this.salesModal = false;
                this.banUserModal = false;
                this.selectedSeller = null;
            },
            closeAddUserModal() {
                this.showUsers = true;
                this.addUserModal = false;
                this.newUser = {
                    name: '',
                    email: '',
                    password: '',
                    role: 'seller',
                };
            },
            openSalesModal(seller) {
                this.showUsers = false;
                this.salesModal = true;
                this.selectedSeller = seller;
                this.addUserModal = false;
                this.banUserModal = false;

                axios
                    .get(`/sellers/${seller.id}/sales`)
                    .then((response) => {
                        this.selectedSeller.sales = response.data;
                    })
                    .catch((error) => {
                        console.error(
                            'Erreur lors de la récupération des ventes :',
                            error
                        );
                        this.selectedSeller.sales = [];
                    });
            },
            closeSalesModal() {
                this.salesModal = false;
                this.selectedSeller = null;
                this.showUsers = true;
            },
            openBanUserModal(seller) {
                this.showUsers = false;
                this.banUserModal = true;
                this.selectedSeller = seller; // On stocke l'objet vendeur complet
                this.salesModal = false;
                this.addUserModal = false;
                this.banReason = ''; // Réinitialise le motif
            },
            closeBanUserModal() {
                this.showUsers = true;
                this.banUserModal = false;
                this.selectedSeller = null; // Réinitialise le vendeur sélectionné
                this.banReason = '';
            },

            // Logique du formulaire
            submitAddUserForm() {
                axios
                    .post('/users', this.newUser)
                    .then((response) => {
                        console.log('User added successfully:', response.data);
                        this.fetchSellers();
                        this.closeAddUserModal();
                        alert('Vendeur ajouté avec succès!');
                    })
                    .catch((error) => {
                        console.error(
                            'Error adding user:',
                            error.response ? error.response.data : error.message
                        );
                        alert(
                            "Erreur lors de l'ajout du vendeur. Veuillez vérifier les informations."
                        );
                    });
            },
            submitBanUserForm() {
                if (!this.banReason.trim()) {
                    alert('Le motif du bannissement est obligatoire.');
                    return;
                }

                axios
                    .post(`/sellers/${this.selectedSeller.id}/ban`, {
                        reason: this.banReason,
                    })
                    .then(() => {
                        alert('Le vendeur a été banni avec succès.');
                        this.closeBanUserModal();
                        this.fetchSellers();
                    })
                    .catch((error) => {
                        console.error(
                            'Erreur lors du bannissement de :' +
                                this.selectedSeller.name,
                            error.response ? error.response.data : error.message
                        );
                        alert(
                            'Erreur lors du bannissement. Veuillez renseigner un motif de minimum 10 caractères'
                        );
                    });
            },

            // Utilitaires de formatage
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
        },
    };
</script>

<style scoped>
    /* Styles pour la modale et les tableaux */
    .modal-content {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 2rem;
        max-width: 600px;
        margin: auto;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .close {
        font-size: 1.5rem;
        cursor: pointer;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .table th,
    .table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #e0e0e0;
    }

    .table thead th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    .table tbody tr:hover {
        background-color: #f1f1f1;
        transition: background-color 0.2s ease;
    }

    /* Responsive styles for tables */
    @media (max-width: 768px) {
        .table thead {
            display: none;
        }

        .table,
        .table tbody,
        .table tr,
        .table td {
            display: block;
            width: 100%;
        }

        .table tr {
            margin-bottom: 1rem;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .table td {
            text-align: right;
            padding-left: 50%;
            position: relative;
        }

        .table td::before {
            content: attr(data-label);
            position: absolute;
            left: 0;
            width: 50%;
            padding-left: 1rem;
            font-weight: 600;
            text-align: left;
            white-space: nowrap;
        }
    }
</style>
