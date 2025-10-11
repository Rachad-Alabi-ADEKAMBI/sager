<template>
    <div class="stock-content">
        <div class="stock-header">
            <h2>Inventaire des Produits</h2>
            <div
                style="
                    display: flex;
                    gap: 10px;
                    margin-bottom: 20px;
                    flex-wrap: wrap;
                    align-items: center;
                "
            >
                <button class="btn btn-primary" @click="openAddProductModal()">
                    <i class="fas fa-plus"></i>
                    Ajouter un produit
                </button>

                <button
                    @click="printList"
                    class="btn-primary"
                    style="background-color: #17a2b8; padding: 0.75rem 1.5rem"
                >
                    <i class="fas fa-print"></i>
                    Imprimer la liste
                </button>

                <input
                    type="text"
                    style="
                        padding: 0.75rem;
                        border: 1px solid #ddd;
                        border-radius: 5px;
                        min-width: 200px;
                    "
                    placeholder="Rechercher un produit..."
                    v-model="searchQuery"
                />

                <select
                    style="
                        padding: 0.75rem;
                        border: 1px solid #ddd;
                        border-radius: 5px;
                        min-width: 200px;
                    "
                    v-model="statusFilter"
                >
                    <option>Tous les statuts</option>
                    <option>En stock (plus de 5)</option>
                    <option>Stock faible (entre 1 et 5)</option>
                    <option>Rupture de stock (stock a zero)</option>
                </select>

                <select
                    style="
                        padding: 0.75rem;
                        border: 1px solid #ddd;
                        border-radius: 5px;
                        min-width: 180px;
                    "
                    v-model="sortOption"
                >
                    <option>Nom (A-Z)</option>
                    <option>Nom (Z-A)</option>
                    <option>Prix (croissant)</option>
                    <option>Prix (décroissant)</option>
                    <option>Quantité</option>
                </select>
            </div>
        </div>

        <div class="showProducts" v-if="showProducts">
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

                                <template v-if="product.is_depositable == 1">
                                    <br />
                                    Consignation:
                                    <strong>
                                        {{
                                            formatAmount(product.deposit_price)
                                        }}
                                        FCFA
                                    </strong>
                                </template>
                            </td>
                            <td data-label="Quantité">
                                {{ product.quantity }}
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
                                            product.quantity >= 5
                                                ? '#d4edda'
                                                : product.quantity > 0
                                                ? '#fff3cd'
                                                : '#f8d7da',
                                        color:
                                            product.quantity >= 5
                                                ? '#155724'
                                                : product.quantity > 0
                                                ? '#856404'
                                                : '#721c24',
                                        border:
                                            product.quantity >= 5
                                                ? '1px solid #c3e6cb'
                                                : product.quantity > 0
                                                ? '1px solid #ffeaa7'
                                                : '1px solid #f5c6cb',
                                    }"
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
                                        @click="openEditPricesModal(product)"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button
                                        class="btn-sm btn-delete"
                                        title="Supprimer"
                                        @click="openDeleteProductModal(product)"
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(stock, index) in stocks" :key="stock.id">
                            <td data-label="Date">
                                {{ formatDateTime(stock.created_at) }}
                            </td>
                            <td data-label="Stock initial">
                                <strong>{{ stock.initial_stock }}</strong>
                            </td>
                            <td data-label="Libellé">{{ stock.label }}</td>
                            <td data-label="Quantité">{{ stock.quantity }}</td>
                            <td data-label="Stock final">
                                <strong>{{ stock.final_stock }}</strong>
                            </td>
                            <td data-label="Actions">
                                <button
                                    v-if="index === firstRelevantIndex"
                                    class="btn-sm"
                                    style="
                                        background-color: #6f42c1;
                                        color: white;
                                    "
                                    title="Annuler l'opération"
                                    @click="
                                        openRevertAddStockModal(
                                            stock.product_id
                                        )
                                    "
                                >
                                    <i class="fas fa-undo"></i>
                                </button>
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
    <div
        v-if="showAddProductModal"
        class="modal-add-product"
        @click.self="closeAddProductModal()"
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
                <h3>Ajouter un nouveau produit</h3>
                <span
                    class="close"
                    @click="closeAddProductModal"
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
                            step="0.01"
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

                    <!-- Consignation -->
                    <div
                        class="form-group"
                        style="
                            margin-bottom: 1rem;
                            display: flex;
                            gap: 1rem;
                            align-items: flex-end;
                        "
                    >
                        <div style="flex: 1">
                            <label>Produit consignable ?</label>
                            <select
                                v-model="is_depositable"
                                class="form-control"
                            >
                                <option :value="0">Non</option>
                                <option :value="1">Oui</option>
                            </select>
                        </div>

                        <div style="flex: 1" v-if="is_depositable">
                            <label>Prix de consignation</label>
                            <input
                                v-model="deposit_price"
                                type="number"
                                class="form-control"
                                step="0.01"
                                min="0"
                                required
                                placeholder="Prix consigne"
                            />
                        </div>
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
                        @click="closeAddProductModal()"
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
    <div v-if="showAddStockModal" class="modal-add-stock">
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
                        min="0.01"
                        step="0.01"
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

    <!--modal revert add stock-->
    <div v-if="showRevertAddStockModal" class="modal-revert-stock">
        <div class="modal-content">
            <h3>
                Annuler cette opération d'ajout de stock pour
                <strong>{{ selectedProduct.name }}</strong>
                ?
                <span
                    class="close"
                    @click="closeRevertAddStockModal"
                    style="cursor: pointer; float: right; font-size: 1.5rem"
                >
                    &times;
                </span>
            </h3>

            <form @submit.prevent="submitRevertAddStockForm">
                <div
                    class="form-actions"
                    style="margin-top: 1rem; display: flex; gap: 1rem"
                >
                    <button
                        type="submit"
                        class="btn btn-primary"
                        style="background: red"
                    >
                        Oui
                    </button>
                    <button
                        type="button"
                        class="btn btn-secondary"
                        @click="closeRevertAddStockModal"
                    >
                        Non
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Modifier les Prix -->
    <div
        v-if="showEditPricesModal"
        class="modal-edit-prices"
        @click.self="closeEditPricesModal"
    >
        <div class="modal-content" style="padding: 2rem; max-width: 400px">
            <div class="modal-header">
                <h3>Modifier les prix</h3>
                <span
                    class="close"
                    @click="closeEditPricesModal"
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
                        :placeholder="formatAmount(currentProduct.price_detail)"
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

                <!-- ✅ Ajout conditionnel du prix de consignation -->
                <div
                    class="form-group"
                    v-if="currentProduct.is_depositable == 1"
                >
                    <label>Prix de consignation</label>
                    <input
                        type="number"
                        class="form-control"
                        v-model.number="editedDepositPrice"
                        :placeholder="currentProduct.deposit_price"
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
                    @click="closeEditPricesModal"
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

    <!-- Modal Supprimer Produit -->
    <div
        v-if="showDeleteProductModal"
        class="modal-delete-product"
        @click.self="closeDeleteProductModal"
    >
        <div class="modal-content" style="padding: 2rem; max-width: 400px">
            <div class="modal-header">
                <h3>Confirmer la suppression</h3>
                <span
                    class="close"
                    @click="closeDeleteProductModal"
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
                    @click="closeDeleteProductModal"
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
                showAddProductModal: false,
                showProducts: true,
                showStock: false,
                stocks: [],
                salesDetails: [],
                transactions: [],
                showAccounting: false,
                name: '',
                purchase_price: '',
                quantity: '',
                price_detail: '',
                price_semi_bulk: '',
                price_bulk: '',
                searchQuery: '',
                is_depositable: 0, // Nouveau
                deposit_price: '',
                statusFilter: 'Tous les statuts',
                sortOption: 'Nom (A-Z)',
                showEditPricesModal: false,
                currentProduct: null,
                editedPriceDetail: null,
                editedPriceSemiBulk: null,
                editedPriceBulk: null,
                editedDepositPrice: null,
                showDeleteProductModal: false,
                showAddStockModal: false,
                showTransactions: false,
                selectedProduct: {},
                stockQuantity: 1,
                selectedProductId: '',
                showRevertAddStockModal: false,
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
                this.showAccounting = false;
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

            printList() {
                const printWindow = window.open('', '_blank');

                const totalValue = this.filteredProducts.reduce(
                    (sum, product) => {
                        return (
                            sum +
                            parseFloat(product.price_detail) *
                                parseFloat(product.quantity)
                        );
                    },
                    0
                );

                let tableRows = '';
                this.filteredProducts.forEach((product, index) => {
                    const statusText =
                        product.quantity >= 5
                            ? 'En stock'
                            : product.quantity > 0
                            ? 'Stock faible'
                            : 'Rupture de stock';
                    const statusColor =
                        product.quantity >= 5
                            ? '#d4edda'
                            : product.quantity > 0
                            ? '#fff3cd'
                            : '#f8d7da';

                    tableRows += `
                        <tr>
                            <td style="padding: 12px; border: 1px solid #ddd;">${
                                index + 1
                            }</td>
                            <td style="padding: 12px; border: 1px solid #ddd;"><strong>${
                                product.name
                            }</strong></td>
                            <td style="padding: 12px; border: 1px solid #ddd; text-align: right;">${this.formatAmount(
                                product.price_detail
                            )} FCFA</td>
                            <td style="padding: 12px; border: 1px solid #ddd; text-align: center;">${
                                product.quantity
                            }</td>
                            <td style="padding: 12px; border: 1px solid #ddd; text-align: center;">
                                <span style="padding: 4px 12px; border-radius: 12px; background: ${statusColor}; font-size: 0.85rem; font-weight: 600;">
                                    ${statusText}
                                </span>
                            </td>
                            <td style="padding: 12px; border: 1px solid #ddd; text-align: right;">${this.formatAmount(
                                product.price_detail * product.quantity
                            )} FCFA</td>
                        </tr>
                    `;
                });

                const htmlContent = `
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Inventaire des Produits</title>
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
                        <h1>Inventaire des Produits</h1>
                        <div class="info">
                            <p><strong>Date d'impression:</strong> ${new Date().toLocaleString(
                                'fr-FR'
                            )}</p>
                            <p><strong>Nombre total de produits:</strong> ${
                                this.filteredProducts.length
                            }</p>
                            <p><strong>Filtre de statut:</strong> ${
                                this.statusFilter
                            }</p>
                        </div>

                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nom du produit</th>
                                    <th style="text-align: right;">Prix détail</th>
                                    <th style="text-align: center;">Quantité</th>
                                    <th style="text-align: center;">Statut</th>
                                    <th style="text-align: right;">Valeur totale</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows}
                                <tr class="total-row">
                                    <td colspan="5" style="padding: 12px; border: 1px solid #ddd; text-align: right;">VALEUR TOTALE DE L'INVENTAIRE:</td>
                                    <td style="padding: 12px; border: 1px solid #ddd; text-align: right;">${this.formatAmount(
                                        totalValue
                                    )} FCFA</td>
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

            openAddProductModal() {
                this.showAddProductModal = true;
            },
            closeAddProductModal() {
                this.showAddProductModal = false;
            },
            submitForm() {
                // Préparer les données dans le format attendu
                const productData = {
                    name: this.name,
                    purchase_price: this.purchase_price,
                    buyer_phone: this.buyer_phone,
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
                        this.closeEditPricesModal();
                    })
                    .catch((error) => {
                        console.error(
                            'Erreur mise à jour:',
                            error.response.data
                        );
                    });
            },
            openEditPricesModal(product) {
                this.currentProduct = product;
                this.editedPriceDetail = product.price_detail;
                this.editedPriceSemiBulk = product.price_semi_bulk;
                this.editedPriceBulk = product.price_bulk;
                this.showEditPricesModal = true;
                this.editedDepositPrice = product.deposit_price;
            },
            closeEditPricesModal() {
                this.showEditPricesModal = false;
                this.currentProduct = null;
                this.editedPriceDetail = null;
                this.editedPriceSemiBulk = null;
                this.editedPriceBulk = null;
                this.editedDepositPrice = null;
            },
            savePrice() {
                if (this.currentProduct) {
                    const productId = this.currentProduct.id;

                    // ✅ Prépare le payload dynamiquement
                    const payload = {
                        price_detail: this.editedPriceDetail,
                        price_semi_bulk: this.editedPriceSemiBulk,
                        price_bulk: this.editedPriceBulk,
                    };

                    if (this.currentProduct.is_depositable == 1) {
                        payload.deposit_price = this.editedDepositPrice;
                    }

                    axios
                        .post(`/products/${productId}/update-prices`, payload)
                        .then((response) => {
                            alert('Prix mis à jour avec succès.');

                            // ✅ Met à jour localement l’objet courant
                            this.currentProduct.price_detail =
                                this.editedPriceDetail;
                            this.currentProduct.price_semi_bulk =
                                this.editedPriceSemiBulk;
                            this.currentProduct.price_bulk =
                                this.editedPriceBulk;

                            if (this.currentProduct.is_depositable == 1) {
                                this.currentProduct.deposit_price =
                                    this.editedDepositPrice;
                            }

                            this.closeEditPricesModal();
                        })
                        .catch((error) => {
                            console.error(
                                'Erreur lors de la mise à jour des prix :',
                                error
                            );
                            alert('Erreur lors de la mise à jour des prix.');
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

            openDeleteProductModal(product) {
                this.currentProduct = product;
                this.showDeleteProductModal = true;
            },
            closeDeleteProductModal() {
                this.showDeleteProductModal = false;
                this.currentProduct = null;
            },
            deleteProduct() {
                axios
                    .post(`/products/${this.currentProduct.id}/delete`)
                    .then(() => {
                        // Actualise la liste, ferme le modal
                        alert(
                            `Le produit ${this.currentProduct.name} a été supprimé avec succès.`
                        );
                        this.fetchProducts(); //relaod plutot la page window.location.reload()
                        this.closeDeleteProductModal();
                    })
                    .catch((error) => {
                        if (
                            error.response &&
                            error.response.data &&
                            error.response.data.message
                        ) {
                            alert(error.response.data.message);
                        } else {
                            alert('Erreur lors de la suppression.');
                        }
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

                // Nouveaux champs consignation
                formData.append('is_depositable', this.is_depositable ? 1 : 0);
                if (this.is_depositable) {
                    formData.append('deposit_price', this.deposit_price);
                }

                // 🔍 Affichage du contenu envoyé
                console.log('Contenu du formData :');
                for (let [key, value] of formData.entries()) {
                    console.log(`${key}:`, value);
                }

                axios
                    .post('/products', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        },
                    })
                    .then(() => {
                        alert('Produit ajouté avec succès.');
                        this.fetchProducts(); // Rafraîchir la liste
                        this.closeAddProductModal(); // Fermer le modal après ajout

                        // Réinitialiser le formulaire
                        this.name = '';
                        this.purchase_price = '';
                        this.quantity = '';
                        this.price_detail = '';
                        this.price_semi_bulk = '';
                        this.price_bulk = '';
                        this.is_depositable = 0;
                        this.deposit_price = '';
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
                this.stockQuantity = 0;
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
            openRevertAddStockModal(product_id) {
                this.selectedProductId = product_id;
                this.showRevertAddStockModal = true;
                console.log('Id produit à annuler :', product_id);
                axios
                    .get(`/product/${this.selectedProductId}`, {})
                    .then((response) => {
                        this.selectedProduct = response.data;
                        console.log(
                            'Produit sélectionné:',
                            this.selectedProduct
                        );
                    })
                    .catch((error) => {
                        console.error(
                            'Erreur suppression:',
                            error.response.data
                        );
                    });
            },
            closeRevertAddStockModal() {
                this.showRevertAddStockModal = false;
                this.selectedProduct = {};
            },
            submitRevertAddStockForm() {
                axios
                    .post('/revertAddStock', {
                        product_id: this.selectedProductId,
                    })
                    .then((response) => {
                        alert(
                            "Opération d'annulation réussie !",
                            response.data
                        );

                        // 1. Mettez à jour le tableau du stock pour refléter le changement
                        this.displayStock(this.selectedProductId);

                        // 2. Fermez la modale d'annulation
                        this.closeRevertAddStockModal();

                        // 3. (Optionnel) Affichez une notification de succès à l'utilisateur
                        // Par exemple : alert('Opération annulée avec succès !');
                    })
                    .catch((error) => {
                        console.error(
                            "Erreur lors de l'annulation de l'opération:",
                            error
                        );

                        // 1. (Optionnel) Affichez un message d'erreur à l'utilisateur
                        // Par exemple : alert('Erreur : ' + error.response.data.message);

                        // 2. Fermez la modale même en cas d'erreur
                        this.closeRevertAddStockModal();
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
            firstRelevantIndex() {
                // Trouve l'index du premier élément qui commence par le texte souhaité.
                // Si aucun élément n'est trouvé, il retourne -1.
                return this.stocks.findIndex((stock) =>
                    stock.label.startsWith('Mise à jour du stock de ')
                );
            },
        },
    };
</script>

<style>
    /* Added more specific class names for each modal type */
    .modal-add-product,
    .modal-edit-prices,
    .modal-delete-product {
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
    .modal-add-stock,
    .modal-revert-stock {
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

<style>
    .action-buttons {
        max-width: 200px;
        display: flex;
        flex-wrap: wrap; /* Permet le retour à la ligne */
        gap: 5px;
    }

    .action-buttons button {
        flex: 0 0 auto; /* Empêche l'étirement des boutons */
    }
</style>
