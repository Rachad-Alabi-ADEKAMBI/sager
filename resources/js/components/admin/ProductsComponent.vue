<template>
    <div class="stock-content">
        <div class="stock-header">
            <h2>Inventaire des Produits</h2>
            <div style="display: flex; gap: 10px; margin-bottom: 20px">
                <button class="btn btn-primary" @click="openModal()">
                    <i class="fas fa-plus"></i>
                    Ajouter un produit
                </button>

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

        <div class="showProducts" v-if="showProducts">
            <!-- Filtres -->
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
                    <div class="form-group">
                        <label>Statut</label>

                        <select class="form-control" v-model="statusFilter">
                            <option>Tous les statuts</option>
                            <option>En stock (plus de 5)</option>
                            <option>Stock faible (entre 1 et 5)</option>
                            <option>Rupture de stock (stock a zero)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Trier par</label>
                        <select class="form-control" v-model="sortOption">
                            <option>Nom (A-Z)</option>
                            <option>Nom (Z-A)</option>
                            <option>Prix (croissant)</option>
                            <option>Prix (décroissant)</option>
                            <option>Quantité</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tableau -->
            <div class="table-container">
                <div class="table-header">
                    <h3>Liste des Produits</h3>
                    <strong>Total: {{ filteredProducts.length }}</strong>
                </div>

                <table class="table" v-if="filteredProducts.length > 0">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Prix</th>
                            <th>Quantité</th>
                            <th>Statut</th>
                            <th>Dernière MAJ</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="product in filteredProducts"
                            :key="product.id"
                        >
                            <td data-label="Nom">
                                <strong>{{ product.name }}</strong>
                            </td>
                            <td data-label="Prix">
                                Détail:
                                <strong>
                                    {{ formatAmount(product.price_detail) }}
                                    FCFA
                                </strong>
                                <br />
                                Semi-gros:
                                <strong>
                                    {{ formatAmount(product.price_semi_bulk) }}
                                    FCFA
                                </strong>
                                <br />
                                Gros:
                                <strong>
                                    {{ formatAmount(product.price_bulk) }} FCFA
                                </strong>
                            </td>
                            <td data-label="Quantité">
                                {{ product.quantity }}
                            </td>
                            <td data-label="Statut">
                                <span
                                    :class="[
                                        'status-badge',
                                        product.quantity >= 5
                                            ? 'status-in-stock'
                                            : product.quantity > 0
                                            ? 'status-low-stock'
                                            : 'status-out-stock',
                                    ]"
                                >
                                    {{
                                        product.quantity >= 5
                                            ? 'En stock'
                                            : product.quantity > 0
                                            ? 'Stock faible'
                                            : 'Rupture de stock'
                                    }}
                                </span>
                            </td>
                            <td data-label="Dernière MAJ">
                                {{ formatDateTime(product.updated_at) }}
                            </td>
                            <td data-label="Actions">
                                <div class="action-buttons">
                                    <button
                                        class="btn-sm btn-edit btn-success"
                                        style="
                                            background-color: #28a745;
                                            color: white;
                                            border: none;
                                        "
                                        title="Historique"
                                        @click="displayStock(product.id)"
                                    >
                                        <i class="fas fa-clock"></i>
                                    </button>

                                    <button
                                        class="btn-sm btn-edit"
                                        title="Comptabilité"
                                        @click="displayAccounting(product)"
                                        style="
                                            background-color: #0056b3;
                                            color: white;
                                            border: none;
                                        "
                                    >
                                        <i class="fas fa-money-bill"></i>
                                    </button>

                                    <button
                                        class="btn-sm"
                                        style="
                                            background-color: #6f42c1;
                                            color: white;
                                        "
                                        title="Ajouter du stock"
                                        @click="openAddStockModal(product)"
                                    >
                                        <i class="fas fa-plus"></i>
                                    </button>

                                    <button
                                        class="btn-sm btn-edit"
                                        title="Modifier"
                                        @click="openEditModal(product)"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button
                                        class="btn-sm btn-delete"
                                        title="Supprimer"
                                        @click="openDeleteModal(product)"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <strong
                    v-if="filteredProducts.length == 0"
                    class="mx-auto text-center"
                >
                    Aucun produit disponible
                </strong>
            </div>
        </div>

        <!--stocks-->
        <div class="showStock" v-if="showStock">
            <div class="table-container">
                <div class="table-header">
                    <h3 style="display: flex; align-items: center; gap: 0.5rem">
                        <span
                            @click="fetchProducts()"
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
                        | Liste des opérations sur
                        <strong>{{ stocks[0]?.product_name }}</strong>
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
                            <td data-label="Date">
                                {{ formatDateTime(stock.date) }}
                            </td>
                            <td data-label="Stock initial">
                                <strong>{{ stock.initial_stock }}</strong>
                            </td>
                            <td data-label="Libellé">{{ stock.label }}</td>
                            <td data-label="Quantité">{{ stock.quantity }}</td>
                            <td data-label="Stock final">
                                <strong>{{ stock.final_stock }}</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Volet Comptabilité -->
        <div class="showAccounting" v-if="showAccounting">
            <div class="table-container">
                <div class="table-header">
                    <h3 style="display: flex; align-items: center; gap: 0.5rem">
                        <span
                            @click="fetchProducts()"
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
                        | Rentabilité de
                        <strong>{{ selectedProduct.name }}</strong>
                    </h3>
                    <button @click="showAccounting = false" class="btn-close">
                        Fermer
                    </button>
                </div>

                <!-- Tableau résumé comptabilité -->
                <table class="table">
                    <thead>
                        <tr>
                            <th data-label="PA Unitaire">PA Unitaire</th>
                            <th data-label="Qté vendue">Qté vendue</th>
                            <th data-label="Total ventes">Total ventes</th>
                            <th data-label="Coût total">Coût total</th>
                            <th data-label="Bénéfice">Bénéfice</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-label="PA Unitaire">
                                {{
                                    formatAmount(selectedProduct.purchase_price)
                                }}
                                FCFA
                            </td>
                            <td data-label="Qté vendue">
                                {{
                                    formatAmount(
                                        accountingData.total_quantity_sold
                                    )
                                }}
                            </td>
                            <td data-label="Total ventes">
                                {{ formatAmount(accountingData.total_revenue) }}
                                FCFA
                            </td>
                            <td data-label="Coût total">
                                {{ formatAmount(accountingData.total_cost) }}
                                FCFA
                            </td>
                            <td data-label="Bénéfice">
                                <strong>
                                    {{ formatAmount(accountingData.profit) }}
                                    FCFA
                                </strong>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Tableau détaillé des opérations -->
                <h4 style="margin-top: 2rem; padding: 5px">
                    Tableau détaillé des ventes de
                    {{ selectedProduct.name }}
                </h4>
                <br />
                <table class="table">
                    <thead>
                        <tr>
                            <th data-label="Date">Date</th>
                            <th data-label="PA Unitaire">PA Unitaire</th>
                            <th data-label="Qté vendue">Qté vendue</th>
                            <th data-label="Prix unitaire vente">
                                Prix unitaire vente
                            </th>
                            <th data-label="Total vente">Total vente</th>
                            <th data-label="Bénéfice">Bénéfice</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(op, index) in salesDetails" :key="index">
                            <td data-label="Date">
                                {{ formatDateTime(op.created_at) }}
                            </td>
                            <td data-label="PA Unitaire">
                                {{
                                    formatAmount(selectedProduct.purchase_price)
                                }}
                                FCFA
                            </td>
                            <td data-label="Qté vendue">{{ op.quantity }}</td>
                            <td data-label="Prix unitaire vente">
                                {{ formatAmount(op.price) }} FCFA
                            </td>
                            <td data-label="Total vente">
                                {{ formatAmount(op.total_sale) }} FCFA
                            </td>
                            <td data-label="Bénéfice">
                                {{ formatAmount(op.profit) }} FCFA
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Ajouter Produit -->
    <div v-if="showModal" class="modal" @click.self="closeModal()">
        <div class="modal-content" style="padding: 2rem">
            <div
                class="modal-header"
                style="
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                "
            >
                <h3>Ajouter un nouveau produit</h3>
                <span
                    class="close"
                    @click="closeModal"
                    style="cursor: pointer; font-size: 1.5rem"
                >
                    &times;
                </span>
            </div>

            <form
                enctype="multipart/form-data"
                method="POST"
                @submit.prevent="addProductForm"
            >
                <div class="form-group" style="margin-bottom: 1rem">
                    <label>Nom du produit</label>
                    <input
                        v-model="name"
                        type="text"
                        class="form-control"
                        required
                    />
                </div>

                <div
                    class="row"
                    style="
                        display: flex;
                        gap: 1rem;
                        flex-wrap: wrap;
                        margin-bottom: 1rem;
                    "
                >
                    <div class="form-group" style="flex: 1 1 45%">
                        <label>Prix d'achat</label>
                        <input
                            v-model="purchase_price"
                            type="number"
                            class="form-control"
                            step="0.01"
                            min="0"
                            required
                        />
                    </div>

                    <div class="form-group" style="flex: 1 1 45%">
                        <label>Quantité</label>
                        <input
                            v-model="quantity"
                            type="number"
                            class="form-control"
                            min="0"
                            required
                        />
                    </div>
                </div>

                <div
                    class="row"
                    style="
                        display: flex;
                        gap: 1rem;
                        flex-wrap: wrap;
                        margin-bottom: 1rem;
                    "
                >
                    <div class="form-group" style="flex: 1 1 30%">
                        <label>Prix détail</label>
                        <input
                            v-model="price_detail"
                            type="number"
                            class="form-control"
                            step="0.01"
                            min="0"
                            required
                        />
                    </div>

                    <div class="form-group" style="flex: 1 1 30%">
                        <label>Prix semi gros</label>
                        <input
                            v-model="price_semi_bulk"
                            type="number"
                            class="form-control"
                            step="0.01"
                            min="0"
                            required
                        />
                    </div>

                    <div class="form-group" style="flex: 1 1 30%">
                        <label>Prix gros</label>
                        <input
                            v-model="price_bulk"
                            type="number"
                            class="form-control"
                            step="0.01"
                            min="0"
                            required
                        />
                    </div>
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
                        @click="closeModal()"
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

    <!--modal add stock-->
    <div v-if="showAddStockModal" class="modal-overlay">
        <div class="modal-content">
            <h3>
                Ajouter du stock pour
                <strong>{{ selectedProduct.name }}</strong>
            </h3>

            <form @submit.prevent="submitAddStockForm">
                <div class="form-group">
                    <label>Quantité à ajouter</label>
                    <input
                        v-model.number="stockQuantity"
                        type="number"
                        min="1"
                        class="form-control"
                        required
                    />
                </div>

                <div
                    class="form-actions"
                    style="margin-top: 1rem; display: flex; gap: 1rem"
                >
                    <button type="submit" class="btn btn-primary">
                        Ajouter
                    </button>
                    <button
                        type="button"
                        class="btn btn-secondary"
                        @click="closeAddStockModal"
                    >
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div v-if="showEditModal" class="modal" @click.self="closeEditModal">
        <div class="modal-content" style="padding: 2rem; max-width: 400px">
            <div class="modal-header">
                <h3>Modifier les prix</h3>
                <span
                    class="close"
                    @click="closeEditModal"
                    style="cursor: pointer"
                >
                    &times;
                </span>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Prix détail</label>
                    <input
                        type="number"
                        class="form-control"
                        v-model.number="editedPriceDetail"
                        :placeholder="currentProduct.price_detail"
                        min="0"
                        step="0.01"
                    />
                </div>
                <div class="form-group">
                    <label>Prix semi gros</label>
                    <input
                        type="number"
                        class="form-control"
                        v-model.number="editedPriceSemiBulk"
                        :placeholder="currentProduct.price_semi_bulk"
                        min="0"
                        step="0.01"
                    />
                </div>
                <div class="form-group">
                    <label>Prix gros</label>
                    <input
                        type="number"
                        class="form-control"
                        v-model.number="editedPriceBulk"
                        :placeholder="currentProduct.price_bulk"
                        min="0"
                        step="0.01"
                    />
                </div>
            </div>
            <div
                style="
                    margin-top: 1rem;
                    display: flex;
                    gap: 1rem;
                    justify-content: flex-end;
                "
            >
                <button class="btn-primary" @click="savePrice">
                    Enregistrer
                </button>
                <button
                    @click="closeEditModal"
                    style="
                        background: #6c757d;
                        color: #fff;
                        border: none;
                        padding: 0.5rem 1rem;
                        border-radius: 5px;
                    "
                >
                    Annuler
                </button>
            </div>
        </div>
    </div>

    <div v-if="showDeleteModal" class="modal" @click.self="closeDeleteModal">
        <div class="modal-content" style="padding: 2rem; max-width: 400px">
            <div class="modal-header">
                <h3>Confirmer la suppression</h3>
                <span
                    class="close"
                    @click="closeDeleteModal"
                    style="cursor: pointer"
                >
                    &times;
                </span>
            </div>
            <div class="modal-body">
                <p>
                    Voulez-vous vraiment supprimer le produit
                    <strong>{{ currentProduct.name }}</strong>
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
                    @click="deleteProduct"
                    style="
                        background: #dc3545;
                        color: #fff;
                        border: none;
                        padding: 0.5rem 1rem;
                        border-radius: 5px;
                    "
                >
                    Supprimer
                </button>
                <button
                    @click="closeDeleteModal"
                    style="
                        background: #6c757d;
                        color: #fff;
                        border: none;
                        padding: 0.5rem 1rem;
                        border-radius: 5px;
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
        name: 'ProductsComponent',

        data() {
            return {
                message: 'Bonjour depuis Vue !',
                products: [], // ajout pour stocker les produits,
                showModal: null,
                showProducts: true,
                showStock: false,
                stocks: [],
                salesDetails: [],
                transactions: [
                    {
                        id: 1,
                        date: '2023-10-01',
                        solde_initial: 1000,
                        libelle: 'Achat de marchandises',
                        montant: -200,
                        solde_final: 800,
                    },
                    {
                        id: 2,
                        date: '2023-10-05',
                        solde_initial: 800,
                        libelle: 'Vente de marchandises',
                        montant: 500,
                        solde_final: 1300,
                    },
                ],
                showAccounting: false,
                name: '',
                purchase_price: '',
                quantity: '',
                price_detail: '',
                price_semi_bulk: '',
                price_bulk: '',
                searchQuery: '',
                statusFilter: 'Tous les statuts',
                sortOption: 'Nom (A-Z)',
                showEditModal: false,
                currentProduct: null,
                editedPriceDetail: null,
                editedPriceSemiBulk: null,
                editedPriceBulk: null,
                showDeleteModal: false,
                showAddStockModal: false,
                selectedProduct: {},
                stockQuantity: 1,
                accountingData: {
                    total_quantity_sold: 0,
                    total_revenue: 0,
                    total_cost: 0,
                    profit: 0,
                },
            };
        },

        mounted() {
            this.fetchProducts();
        },

        methods: {
            fetchProducts() {
                this.showProducts = true;
                this.showStock = false;
                this.showTAccounting = false;
                axios
                    .get('/productsList')
                    .then((response) => {
                        console.log(response.data);
                        this.products = response.data;
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

            openModal() {
                this.showModal = true;
            },
            closeModal() {
                this.showModal = false;
            },
            submitForm() {
                // Préparer les données dans le format attendu
                const productData = {
                    name: this.name,
                    purchase_price: this.purchase_price,
                    buyer_phone: this.purchase_price,
                    quantity: this.quantity,
                    price_detail: this.price_detail,
                    price_semi_bulk: this.price_semi_bulk,
                    price_bulk: this.price_bulk,
                };

                console.log('Données à envoyer:', productData);

                axios
                    .put(`/products/${this.currentProduct.id}`, {
                        price_detail: this.editedPriceDetail,
                        price_semi_bulk: this.editedPriceSemiBulk,
                        price_bulk: this.editedPriceBulk,
                    })
                    .then((response) => {
                        console.log('Prix mis à jour', response.data);
                        this.closeEditModal();
                    })
                    .catch((error) => {
                        console.error(
                            'Erreur mise à jour:',
                            error.response.data
                        );
                    });
            },
            openEditModal(product) {
                this.currentProduct = product;
                this.editedPriceDetail = product.price_detail;
                this.editedPriceSemiBulk = product.price_semi_bulk;
                this.editedPriceBulk = product.price_bulk;
                this.showEditModal = true;
            },
            closeEditModal() {
                this.showEditModal = false;
                this.currentProduct = null;
                this.editedPriceDetail = null;
                this.editedPriceSemiBulk = null;
                this.editedPriceBulk = null;
            },
            savePrice() {
                if (this.currentProduct) {
                    const productId = this.currentProduct.id;
                    const payload = {
                        price_detail: this.editedPriceDetail,
                        price_semi_bulk: this.editedPriceSemiBulk,
                        price_bulk: this.editedPriceBulk,
                    };

                    axios
                        .post(`/products/${productId}`, payload)
                        .then((response) => {
                            // Mise à jour locale des données si besoin
                            this.currentProduct.price_detail =
                                this.editedPriceDetail;
                            this.currentProduct.price_semi_bulk =
                                this.editedPriceSemiBulk;
                            this.currentProduct.price_bulk =
                                this.editedPriceBulk;

                            // affiche un message en alert que la modification a marche

                            this.closeEditModal();
                        })
                        .catch((error) => {
                            console.error(
                                'Erreur lors de la mise à jour des prix :',
                                error
                            );
                            // Gestion d'erreur (alerte, message, etc.)
                        });
                }
            },
            displayStock(product_id) {
                axios
                    .get(`/stock/${product_id}`)
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

                this.showStock = true;
                this.showProducts = false;
                this.showTransactions = false;
            },
            displayAccounting(product) {
                this.selectedProduct = product;
                this.showTransactions = true;
                this.showProducts = false;
                this.showStock = false;

                axios
                    .get(`/accounting/${product.id}`)
                    .then((response) => {
                        this.accountingData = response.data;
                        this.salesDetails = response.data.sales_details || [];
                        this.showAccounting = true;
                    })
                    .catch((error) => {
                        console.error(
                            'Erreur lors de la récupération des données comptables :',
                            error
                        );
                    });
            },

            openDeleteModal(product) {
                this.currentProduct = product;
                this.showDeleteModal = true;
            },
            closeDeleteModal() {
                this.showDeleteModal = false;
                this.currentProduct = null;
            },
            deleteProduct() {
                axios
                    .delete(`/products/${this.currentProduct.id}`)
                    .then(() => {
                        // Actualise la liste, ferme le modal
                        alert(
                            `Le produit ${this.currentProduct.name} a été supprimé avec succès.`
                        );
                        this.fetchProducts();
                        this.closeDeleteModal();
                    })
                    .catch((error) => {
                        alert('Erreur lors de la suppression');
                        console.error(error);
                    });
            },
            addProductForm() {
                const formData = new FormData();
                formData.append('name', this.name);
                formData.append('purchase_price', this.purchase_price);
                formData.append('quantity', this.quantity);
                formData.append('price_detail', this.price_detail);
                formData.append('price_semi_bulk', this.price_semi_bulk);
                formData.append('price_bulk', this.price_bulk);

                axios
                    .post('/products', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        },
                    })
                    .then(() => {
                        alert('Produit ajouté avec succès.');
                        this.fetchProducts(); // si tu veux rafraîchir la liste
                        this.closeModal(); // si tu veux fermer le modal après ajout
                    })
                    .catch((error) => {
                        alert("Erreur lors de l'ajout du produit.");
                        console.error(error);
                    });
            },
            openAddStockModal(product) {
                this.selectedProduct = product;
                this.selectedProductId = product.id;
                this.stockQuantity = 1;
                this.showAddStockModal = true;
            },

            closeAddStockModal() {
                this.showAddStockModal = false;
                this.selectedProduct = {};
                this.stockQuantity = 1;
            },

            submitAddStockForm() {
                axios
                    .post(`/products/${this.selectedProductId}/stock`, {
                        product_id: this.selectedProduct.id,
                        quantity: this.stockQuantity,
                    })
                    .then(() => {
                        alert('Stock ajouté avec succès.');
                        this.closeAddStockModal();
                        this.fetchProducts(); // ou autre action de mise à jour
                    })
                    .catch((error) => {
                        console.error(
                            'Erreur lors de l’ajout du stock :',
                            error
                        );
                        alert('Erreur lors de l’ajout du stock.');
                    });
            },
        },
        computed: {
            filteredProducts() {
                let filtered = this.products;

                // Filtrer par recherche (nom produit)
                if (this.searchQuery) {
                    const query = this.searchQuery.toLowerCase();
                    filtered = filtered.filter((p) =>
                        p.name.toLowerCase().includes(query)
                    );
                }

                // Filtrer par statut
                if (this.statusFilter !== 'Tous les statuts') {
                    filtered = filtered.filter((p) => {
                        if (this.statusFilter === 'En stock (plus de 5)')
                            return p.quantity >= 5;
                        if (this.statusFilter === 'Stock faible (entre 1 et 5)')
                            return p.quantity > 0 && p.quantity < 5;
                        if (
                            this.statusFilter ===
                            'Rupture de stock (stock a zero)'
                        )
                            return p.quantity === 0;
                        return true;
                    });
                }

                // Trier selon l'option choisie
                switch (this.sortOption) {
                    case 'Nom (A-Z)':
                        filtered.sort((a, b) => a.name.localeCompare(b.name));
                        break;
                    case 'Nom (Z-A)':
                        filtered.sort((a, b) => b.name.localeCompare(a.name));
                        break;
                    case 'Prix (croissant)':
                        filtered.sort(
                            (a, b) => a.price_detail - b.price_detail
                        );
                        break;
                    case 'Prix (décroissant)':
                        filtered.sort(
                            (a, b) => b.price_detail - a.price_detail
                        );
                        break;
                    case 'Quantité':
                        filtered.sort((a, b) => b.quantity - a.quantity);
                        break;
                }

                return filtered;
            },
        },
    };
</script>

<style>
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5); /* fond semi-transparent */
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    .modal-content {
        background: white;
        border-radius: 10px;
        padding: 2rem;
        max-width: 700px;
        width: 100%;
    }
</style>

<style>
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: white;
        padding: 2rem;
        border-radius: 10px;
        width: 90%;
        max-width: 400px;
    }
</style>
