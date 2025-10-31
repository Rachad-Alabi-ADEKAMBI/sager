<template>
    <header class="header">
        <div style="display: flex; align-items: center; gap: 1rem">
            <button class="menu-toggle" @click="toggleSidebar">
                <i class="fas fa-bars"></i>
            </button>
            <h1>Historique du stock</h1>
        </div>
        <div class="user-info">
            <span>Administrateur</span>
            <div class="user-avatar">
                <i class="fas fa-user"></i>
            </div>
        </div>
    </header>

    <div class="stock-content">
        <div class="stock-header">
            <h2>Historique du stock</h2>
            <div style="display: flex; gap: 10px; margin-bottom: 20px">
                <button
                    class="btn btn-success"
                    @click="
                        alert(
                            'Fonctionnalité d\'impression en cours de développement.'
                        )
                    "
                >
                    <i class="fas fa-print"></i>
                    Imprimer
                </button>
            </div>
        </div>

        <!-- Filtres -->
        <div class="showProducts">
            <div class="filters">
                <div class="filters-grid">
                    <div class="form-group">
                        <label>Rechercher</label>
                        <input
                            type="text"
                            class="form-control"
                            placeholder="Nom du produit..."
                            v-model="searchQuery"
                        />
                    </div>
                </div>
            </div>

            <!-- Tableau -->
            <div class="table-container">
                <div class="table-header">
                    <h3>Toutes les opérations du stock</h3>
                    <strong>Total: {{ filteredProducts.length }}</strong>
                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Produit</th>
                            <th>Stock initial</th>
                            <th>Libellé</th>
                            <th>Quantité</th>
                            <th>Stock final</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="stock in filteredProducts" :key="stock.id">
                            <td data-label="Date">{{ stock.date }}</td>
                            <td data-label="Produit">
                                <strong>{{ stock.product_name }}</strong>
                            </td>
                            <td data-label="Stock initial">
                                <strong>{{ stock.stock_initial }}</strong>
                            </td>
                            <td data-label="Libellé">{{ stock.libelle }}</td>
                            <td data-label="Quantité">{{ stock.quantite }}</td>
                            <td data-label="Stock final">
                                <strong>{{ stock.stock_final }}</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Affichage spécifique pour "gaz" -->
        <div class="showStock">
            <div class="table-container">
                <div class="table-header">
                    <h3 style="display: flex; align-items: center; gap: 0.5rem">
                        <i
                            class="fas fa-arrow-left"
                            style="
                                cursor: pointer;
                                color: white;
                                font-weight: bold;
                            "
                        ></i>
                        |
                        <strong>gaz</strong>
                    </h3>
                    <strong>Total: {{ stocks.length }}</strong>
                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Stock initial</th>
                            <th>Libellé</th>
                            <th>Quantité</th>
                            <th>Stock final</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="stock in stocks" :key="stock.id">
                            <td data-label="Date">{{ stock.date }}</td>
                            <td data-label="Stock initial">
                                <strong>{{ stock.stock_initial }}</strong>
                            </td>
                            <td data-label="Libellé">{{ stock.libelle }}</td>
                            <td data-label="Quantité">{{ stock.quantite }}</td>
                            <td data-label="Stock final">
                                <strong>{{ stock.stock_final }}</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'StocksComponent',

        data() {
            return {
                message: 'Bonjour depuis Vue !',
                products: [], // ajout pour stocker les produits,
                showProducts: true,
                stocks: [
                    {
                        id: 1,
                        date: '2023-10-01',
                        stock_initial: 100,
                        libelle: 'Réception de marchandises',
                        quantite: 50,
                        stock_final: 150,
                    },
                    {
                        id: 2,
                        date: '2023-10-05',
                        stock_initial: 150,
                        libelle: 'Vente de marchandises',
                        quantite: -30,
                        stock_final: 120,
                    },
                ],
            };
        },

        mounted() {
            this.fetchStocks();
        },

        methods: {
            fetchStocks() {
                axios
                    .get('/stocksList')
                    .then((response) => {
                        console.log(response.data);
                        this.stocks = response.data;
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
            displayStock(product) {
                this.showStock = true;
                this.showProducts = false;
            },
        },
    };
</script>
