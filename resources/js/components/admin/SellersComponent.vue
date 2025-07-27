<template>
    <div class="users-content">
        <div class="users-header">
            <h2>Équipe SAGER ( {{}} )</h2>
            <button class="btn-primary" @click="openAddUserModal">
                <i class="fas fa-user-plus"></i>
                Ajouter un vendeur
            </button>
        </div>
        <!-- Users Grid -->

      
        <div class="users-grid">
            <div class="user-card" v-for="seller in sellers" :key="seller.id">
                <div class="user-status status-active">Actif</div>
                <div class="user-card-header">
                    <div class="user-card-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-card-info">
                        <h3>{{ seller.name }}</h3>
                        <p>{{ seller.role || 'Rôle non défini' }}</p>
                    </div>
                </div>
                <div class="user-details">
                    <div class="user-detail-item">
                        <span class="user-detail-label">Email:</span>
                        <span class="user-detail-value">
                            {{ seller.email }}
                        </span>
                    </div>
                    <div class="user-detail-item">
                        <span class="user-detail-label">
                            Dernière connexion:
                        </span>
                        <span class="user-detail-value">
                            {{ seller.last_login || 'Non disponible' }}
                        </span>
                    </div>
                    <div class="user-detail-item">
                        <span class="user-detail-label">Opérations:</span>
                        <span class="user-detail-value">
                            {{ seller.operations_count || 0 }} cette semaine
                        </span>
                    </div>
                </div>
                <div class="user-actions">
                    <button
                        class="btn-sm btn-view"
                        @click="displaySales(seller.id)"
                    >
                        <i class="fas fa-eye"></i>
                        Voir activité
                    </button>
                    <button
                        class="btn-sm btn-ban"
                        @click="banSeller(seller.id) "
                    >
                        <i class="fas fa-ban"></i>
                        Bannir
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Ajouter Utilisateur -->
        <div id="addUserModal" class="modal" v-show="addUserModal" >
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Ajouter un nouveau vendeur</h3>
                    <span class="close" @click="closeAddUserModal">
                        &times;
                    </span>
                </div>
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name">Nom complet</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control"
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
                            required
                        />
                    </div>
                    <input type="hidden" name="role" value="seller" />
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
                            @click="closeModal()"
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
                message: 'Bonjour depuis Vue !',
                sellers: [],
                addUserModal: false,
            };
        },

        mounted() {
            this.fetchSellers();
        },

        methods: {
            fetchSellers() {
                axios
                    .get('/sellersList')
                    .then((response) => {
                        console.log(response.data);
                        this.sellers = response.data;
                    })
                    .catch((error) => {
                        console.error(
                            'Erreur lors de la récupération des données :',
                            error
                        );
                    });
            },
            openAddUserModal() {
                this.addUserModal = true;
            },
            closeAddUserModal() {
                this.addUserModal = false;
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
            displaySales(seller_id){
                alert('sales');
            }
        },
    };
</script>

<style>
   .modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex; /* C’est ça qui rend le modal visible */
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background-color: #fff;
    padding: 2rem;
    border-radius: 10px;
    max-width: 600px;
    width: 100%;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    position: relative;
}

</style>
