<template>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <main class="main-content" id="app">
        <div class="sales-content">
            <!-- Sales Form -->
            <div class="sales-form">
                <h3 style="margin-bottom: 1.5rem; color: #333">
                    Informations de la vente
                </h3>

                <form @submit.prevent="validateOrder">
                    <!-- Customer Information -->
                    <div class="customer-info">
                        <div class="form-group">
                            <label>Nom du client</label>
                            <input
                                type="text"
                                class="form-control"
                                id="customerName"
                                v-model="customer_name"
                                placeholder="Nom complet du client"
                                style="width: 200px"
                                required
                            />
                        </div>
                    </div>

                    <!-- Product Lines -->
                    <div class="product-lines">
                        <h4 style="margin-bottom: 1rem; color: #333">
                            Produits à vendre
                        </h4>
                        <div id="productLinesContainer">
                            <div
                                class="product-line"
                                v-for="(line, index) in productLines"
                                :key="index"
                            >
                                <!-- Ajout de la recherche et sélection de produit -->
                                <div class="form-group">
                                    <label>Produit</label>
                                    <div
                                        style="
                                            display: flex;
                                            flex-direction: column;
                                            gap: 0.5rem;
                                        "
                                    >
                                        <!-- Select de produit -->
                                        <select
                                            v-model="line.productId"
                                            @change="onProductChange(index)"
                                            class="form-control product-select"
                                            style="width: 170px"
                                        >
                                            <option disabled value="">
                                                Sélectionner un produit
                                            </option>
                                            <option
                                                v-for="product in filteredProducts(
                                                    index
                                                )"
                                                :key="product.id"
                                                :value="product.id"
                                            >
                                                {{ product.name }}
                                            </option>
                                        </select>

                                        <!-- Affichage de l'indicateur consignable, quantité restante et prix consignation -->
                                        <div
                                            v-if="line.product"
                                            style="
                                                font-size: 0.85rem;
                                                color: #666;
                                            "
                                        >
                                            <span
                                                v-if="
                                                    line.product.is_depositable
                                                "
                                                style="
                                                    background: #28a745;
                                                    color: white;
                                                    padding: 2px 6px;
                                                    border-radius: 3px;
                                                    margin-right: 8px;
                                                "
                                            >
                                                Consignable
                                            </span>
                                            <span
                                                style="
                                                    color: #007bff;
                                                    font-weight: 500;
                                                    margin-right: 10px;
                                                "
                                            >
                                                Stock:
                                                {{ line.product.quantity }}
                                                disponible(s)
                                            </span>

                                            <!-- Affichage des prix de consignation et rechargement avec filling_price -->
                                            <div
                                                v-if="
                                                    line.product.is_depositable
                                                "
                                                style="
                                                    margin-top: 0.25rem;
                                                    display: flex;
                                                    gap: 1rem;
                                                    flex-wrap: wrap;
                                                "
                                            >
                                                <span
                                                    style="
                                                        color: #888;
                                                        font-style: italic;
                                                    "
                                                >
                                                    Consignation :
                                                    {{
                                                        formatAmount(
                                                            line.product
                                                                .deposit_price
                                                        )
                                                    }}
                                                    FCFA
                                                </span>
                                                <span
                                                    style="
                                                        color: #888;
                                                        font-style: italic;
                                                    "
                                                >
                                                    Recharge :
                                                    {{
                                                        formatAmount(
                                                            line.product
                                                                .filling_price
                                                        )
                                                    }}
                                                    FCFA
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sélecteur type de prix -->
                                <div class="form-group" v-if="line.product">
                                    <label>Type de prix</label>
                                    <select
                                        class="form-control price-type"
                                        v-model="line.selectedPrice"
                                        @change="updateUnitPrice(index)"
                                        style="width: 170px"
                                    >
                                        <option disabled value="">
                                            Sélectionner le type
                                        </option>

                                        <!-- Options pour produits consignables avec filling_price -->
                                        <template
                                            v-if="line.product.is_depositable"
                                        >
                                            <option
                                                :value="
                                                    JSON.stringify({
                                                        price: line.product
                                                            .deposit_price,
                                                        type: 'deposit',
                                                    })
                                                "
                                            >
                                                Consignation
                                                <br />
                                                {{
                                                    formatAmount(
                                                        line.product
                                                            .deposit_price
                                                    )
                                                }}
                                                FCFA
                                            </option>
                                            <option
                                                :value="
                                                    JSON.stringify({
                                                        price: line.product
                                                            .filling_price,
                                                        type: 'refill',
                                                    })
                                                "
                                            >
                                                Recharge
                                                <br />
                                                {{
                                                    formatAmount(
                                                        line.product
                                                            .filling_price
                                                    )
                                                }}
                                                FCFA
                                            </option>
                                            <option
                                                :value="
                                                    JSON.stringify({
                                                        price:
                                                            Number(
                                                                line.product
                                                                    .deposit_price
                                                            ) +
                                                            Number(
                                                                line.product
                                                                    .filling_price
                                                            ),
                                                        type: 'both',
                                                    })
                                                "
                                            >
                                                Consignation + Recharge
                                                <br />
                                                {{
                                                    formatAmount(
                                                        Number(
                                                            line.product
                                                                .deposit_price
                                                        ) +
                                                            Number(
                                                                line.product
                                                                    .filling_price
                                                            )
                                                    )
                                                }}
                                                FCFA
                                            </option>
                                        </template>

                                        <!-- Options pour produits non-consignables -->
                                        <template v-else>
                                            <option
                                                :value="
                                                    JSON.stringify({
                                                        price: line.product
                                                            .price_detail,
                                                        type: 'detail',
                                                    })
                                                "
                                            >
                                                Détail
                                                <br />
                                                {{
                                                    formatAmount(
                                                        line.product
                                                            .price_detail
                                                    )
                                                }}
                                                FCFA
                                            </option>
                                            <option
                                                :value="
                                                    JSON.stringify({
                                                        price: line.product
                                                            .price_semi_bulk,
                                                        type: 'semi_bulk',
                                                    })
                                                "
                                            >
                                                Semi gros
                                                <br />
                                                {{
                                                    formatAmount(
                                                        line.product
                                                            .price_semi_bulk
                                                    )
                                                }}
                                                FCFA
                                            </option>
                                            <option
                                                :value="
                                                    JSON.stringify({
                                                        price: line.product
                                                            .price_bulk,
                                                        type: 'bulk',
                                                    })
                                                "
                                            >
                                                Gros
                                                <br />
                                                {{
                                                    formatAmount(
                                                        line.product.price_bulk
                                                    )
                                                }}
                                                FCFA
                                            </option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Quantité décimale -->
                                <div class="form-group">
                                    <label>Quantité</label>
                                    <input
                                        type="number"
                                        class="form-control quantity-input"
                                        min="0.01"
                                        step="0.01"
                                        :max="line.product?.quantity || 0"
                                        v-model.number="line.quantity"
                                        style="width: 100px"
                                        @input="onQuantityChange(index)"
                                    />
                                </div>

                                <!-- Prix unitaire -->
                                <div class="form-group">
                                    <label>Prix unitaire</label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        style="width: 100px"
                                        step="0.01"
                                        v-model="line.unitPrice"
                                        readonly
                                    />
                                </div>

                                <!-- Supprimer ligne -->
                                <div class="form-group">
                                    <label style="opacity: 0">Action</label>
                                    <button
                                        type="button"
                                        class="btn-remove"
                                        @click="removeProductLine(index)"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Bouton ajouter ligne -->
                        <button
                            type="button"
                            class="btn-add-line"
                            @click="addProductLine"
                            :disabled="productLines.length >= products.length"
                        >
                            <i class="fas fa-plus"></i>
                            Ajouter une ligne
                        </button>
                    </div>

                    <!-- Cart Section -->
                    <div class="cart-section">
                        <div class="cart-summary">
                            <div class="cart-total" id="cartTotal">
                                Total: {{ formatAmount(total) }} FCFA
                            </div>
                        </div>
                        <!-- Remplacement du bouton "Finaliser" par "Valider" -->
                        <div style="text-align: center; margin-top: 1.5rem">
                            <button class="btn-primary" type="submit">
                                <i class="fas fa-check"></i>
                                Valider
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Sales History -->
            <div class="sales-history">
                <div class="history-header">
                    <h3>Historique de mes ventes ( {{ sales.length }} )</h3>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>N° Facture</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="sale in paginatedSales" :key="sale.id">
                            <td data-label="N° Facture">
                                <strong>#INV-{{ sale.id }}</strong>
                            </td>
                            <td data-label="Client">{{ sale.buyer_name }}</td>
                            <td data-label="Date">
                                {{ formatDateTime(sale.created_at) }}
                            </td>
                            <td data-label="Montant">
                                <strong>
                                    {{ formatAmount(sale.total) }} FCFA
                                </strong>
                            </td>

                            <td data-label="Statut">
                                <strong>
                                    {{
                                        sale.status === 'done'
                                            ? 'Terminée'
                                            : sale.status === 'cancelled'
                                            ? 'Annulée'
                                            : sale.status
                                    }}
                                </strong>
                            </td>

                            <td data-label="Actions">
                                <button
                                    class="invoice-btn"
                                    @click="viewInvoice(sale)"
                                >
                                    <i
                                        class="fas fa-eye"
                                        style="margin-right: 4px"
                                    ></i>
                                    Voir
                                </button>
                                <button
                                    class="print-btn"
                                    @click="printInvoice(sale.id)"
                                >
                                    <i
                                        class="fas fa-print"
                                        style="margin-right: 4px"
                                    ></i>
                                    Imprimer
                                </button>
                                <button
                                    v-if="sale.status === 'done'"
                                    class="cancel-btn"
                                    @click="cancelInvoice(sale.id)"
                                    style="
                                        background: #dc3545;
                                        color: white;
                                        border: none;
                                        padding: 0.5rem 1rem;
                                        border-radius: 5px;
                                        cursor: pointer;
                                        margin: 5px;
                                    "
                                >
                                    <i
                                        class="fas fa-times"
                                        style="margin-right: 4px"
                                    ></i>
                                    Annuler
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination améliorée avec un meilleur style -->
                <div class="pagination-container">
                    <button
                        :disabled="currentPage === 1"
                        @click="currentPage--"
                        class="pagination-btn pagination-nav"
                    >
                        <i class="fas fa-chevron-left"></i>
                        Précédent
                    </button>

                    <div class="pagination-numbers">
                        <button
                            v-for="page in visiblePages"
                            :key="page"
                            @click="currentPage = page"
                            :class="[
                                'pagination-btn',
                                'pagination-number',
                                { active: currentPage === page },
                            ]"
                        >
                            {{ page }}
                        </button>
                    </div>

                    <button
                        :disabled="currentPage === totalPages"
                        @click="currentPage++"
                        class="pagination-btn pagination-nav"
                    >
                        Suivant
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Nouveau modal de récapitulatif de commande -->
        <div
            v-if="showSummaryModal"
            class="modal-overlay"
            @click.self="closeSummaryModal"
        >
            <div class="modal-container summary-modal">
                <div class="modal-header">
                    <h5>
                        <i class="fas fa-shopping-cart"></i>
                        Récapitulatif de la commande
                    </h5>
                    <button @click="closeSummaryModal" class="modal-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="summary-section">
                        <p>
                            <strong>Client :</strong>
                            {{ customer_name }}
                        </p>
                        <p>
                            <strong>Vendeur :</strong>
                            {{ seller_name }}
                        </p>
                    </div>

                    <hr
                        style="
                            margin: 1rem 0;
                            border: none;
                            border-top: 1px solid #e0e0e0;
                        "
                    />

                    <h6 style="margin-bottom: 1rem; color: #333">
                        Produits commandés :
                    </h6>
                    <div class="products-summary">
                        <div
                            v-for="(line, index) in productLines"
                            :key="index"
                            class="product-summary-item"
                        >
                            <div class="product-info">
                                <span class="product-name">
                                    {{ line.product.name }}
                                </span>
                                <span
                                    v-if="line.product.is_depositable"
                                    class="badge-depositable"
                                >
                                    Consignable
                                </span>
                            </div>

                            <!-- Affichage du type de transaction pour produits consignables -->
                            <div
                                v-if="
                                    line.product.is_depositable &&
                                    line.priceType
                                "
                                style="
                                    font-size: 0.85rem;
                                    color: #666;
                                    margin-top: 0.25rem;
                                    font-style: italic;
                                "
                            >
                                <span v-if="line.priceType === 'deposit'">
                                    (Consignation uniquement)
                                </span>
                                <span v-else-if="line.priceType === 'refill'">
                                    (Recharge uniquement)
                                </span>
                                <span v-else-if="line.priceType === 'both'">
                                    (Consignation + Recharge)
                                </span>
                            </div>

                            <div class="product-details">
                                <span>
                                    {{ line.quantity }} ×
                                    {{ formatAmount(line.unitPrice) }} FCFA
                                </span>
                                <span class="product-subtotal">
                                    =
                                    {{
                                        formatAmount(
                                            line.quantity * line.unitPrice
                                        )
                                    }}
                                    FCFA
                                </span>
                            </div>
                        </div>
                    </div>

                    <hr
                        style="
                            margin: 1rem 0;
                            border: none;
                            border-top: 2px solid #333;
                        "
                    />

                    <div class="total-summary">
                        <span style="font-size: 1.2rem; font-weight: bold">
                            Total :
                        </span>
                        <span
                            style="
                                font-size: 1.3rem;
                                font-weight: bold;
                                color: #28a745;
                            "
                        >
                            {{ formatAmount(total) }} FCFA
                        </span>
                    </div>

                    <!-- Ajout du sélecteur de mode de paiement -->
                    <div class="payment-method-section">
                        <label
                            style="
                                font-weight: 600;
                                margin-bottom: 0.5rem;
                                display: block;
                            "
                        >
                            Mode de paiement :
                        </label>
                        <select
                            v-model="payment_method"
                            class="form-control payment-select"
                        >
                            <option value="Especes">Espèces</option>
                            <option value="Credit">Crédit</option>
                            <option value="Mobile money">Mobile money</option>
                        </select>
                    </div>
                </div>

                <!-- Boutons pour continuer/modifier ou finaliser -->
                <div class="modal-footer summary-footer">
                    <button @click="closeSummaryModal" class="btn-secondary">
                        <i class="fas fa-edit"></i>
                        Continuer / Modifier
                    </button>
                    <button @click="submitForm" class="btn-primary">
                        <i class="fas fa-check-circle"></i>
                        Finaliser la vente
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Détails de la vente -->
        <div v-if="showSaleModal" class="modal-overlay">
            <div class="modal-container">
                <div class="modal-header">
                    <h5>Détails de la vente</h5>
                    <button @click="closeSaleModal" class="modal-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        <strong>Client :</strong>
                        {{ selectedSale.buyer_name }}
                    </p>
                    <p>
                        <strong>Vendeur :</strong>
                        {{ selectedSale.seller_name }}
                    </p>
                    <p>
                        <strong>Date :</strong>
                        {{ formatDateTime(selectedSale.created_at) }}
                    </p>
                    <p>
                        <strong>Total :</strong>
                        {{ formatAmount(selectedSale.total) }} FCFA
                    </p>
                    <hr />
                    <h6>Produits achetés :</h6>
                    <ul>
                        <li
                            v-for="(product, i) in selectedSale.products"
                            :key="i"
                        >
                            {{ product.name }} - {{ product.pivot.quantity }} ×
                            {{ formatAmount(Number(product.pivot.price)) }} =
                            {{
                                formatAmount(
                                    Number(product.pivot.price) *
                                        product.pivot.quantity
                                )
                            }}
                            FCFA
                        </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button
                        @click="printInvoice(selectedSale.id)"
                        class="btn-download"
                    >
                        <i class="fas fa-print"></i>
                        Imprimer Facture
                    </button>
                </div>
            </div>
        </div>
    </main>
</template>

<script>
    export default {
        name: 'SaleComponent',

        data() {
            return {
                products: [],
                seller_name: window.sellerName || '',
                customer_name: '',
                customer_phone: '5222',
                productLines: [],
                total: 0,
                sales: [],
                salesPerPage: 5,
                currentPage: 1,
                showSaleModal: false,
                selectedSale: null,
                showSummaryModal: false,
                payment_method: 'Especes',
            };
        },

        mounted() {
            this.fetchSalesData();
            this.addProductLine();
        },
        computed: {
            paginatedSales() {
                const start = (this.currentPage - 1) * this.salesPerPage;
                return this.sales.slice(start, start + this.salesPerPage);
            },
            totalPages() {
                return Math.ceil(this.sales.length / this.salesPerPage);
            },
            visiblePages() {
                const pages = [];
                const total = this.totalPages;
                const current = this.currentPage;

                if (total <= 7) {
                    for (let i = 1; i <= total; i++) {
                        pages.push(i);
                    }
                } else {
                    if (current <= 4) {
                        for (let i = 1; i <= 5; i++) pages.push(i);
                        pages.push('...');
                        pages.push(total);
                    } else if (current >= total - 3) {
                        pages.push(1);
                        pages.push('...');
                        for (let i = total - 4; i <= total; i++) pages.push(i);
                    } else {
                        pages.push(1);
                        pages.push('...');
                        for (let i = current - 1; i <= current + 1; i++)
                            pages.push(i);
                        pages.push('...');
                        pages.push(total);
                    }
                }

                return pages;
            },
        },

        methods: {
            fetchSalesData() {
                axios
                    .get('/productsList')
                    .then((response) => {
                        this.products = response.data;
                    })
                    .catch((error) => {
                        console.error('Erreur produits :', error);
                    });

                axios
                    .get('/sellerSalesList?seller_name=' + this.seller_name)
                    .then((response) => {
                        this.sales = response.data;
                    })
                    .catch((error) => {
                        console.error('Erreur ventes :', error);
                    });
            },

            addProductLine() {
                this.productLines.push({
                    productId: '',
                    product: null,
                    selectedPrice: '',
                    unitPrice: 0,
                    quantity: 1,
                    searchQuery: '',
                    priceType: '', // Ajout du tracking du type de prix
                });
            },

            removeProductLine(index) {
                this.productLines.splice(index, 1);
                this.updateTotal();
            },

            onSearchChange(index) {
                // La recherche filtre automatiquement via filteredProducts
            },

            filteredProducts(currentIndex) {
                const line = this.productLines[currentIndex];
                const searchQuery = (line.searchQuery || '').toLowerCase();

                const selectedIds = this.productLines
                    .map((l, i) => (i !== currentIndex ? l.productId : null))
                    .filter((id) => id);

                let available = this.products.filter(
                    (p) => !selectedIds.includes(p.id)
                );

                if (searchQuery) {
                    available = available.filter((p) =>
                        p.name.toLowerCase().includes(searchQuery)
                    );
                }

                return available;
            },

            onProductChange(index) {
                const productId = this.productLines[index].productId;
                if (!productId) return;

                axios
                    .get(`/product/${productId}`)
                    .then((response) => {
                        this.productLines[index].product = response.data;
                        this.productLines[index].selectedPrice = '';
                        this.productLines[index].unitPrice = 0;
                        this.productLines[index].quantity = 1;
                        this.productLines[index].priceType = ''; // Reset du type de prix
                        this.updateTotal();
                    })
                    .catch((error) => {
                        console.error(
                            'Erreur lors de la récupération du produit :',
                            error
                        );
                    });
            },

            updateUnitPrice(index) {
                const line = this.productLines[index];
                try {
                    const priceData = JSON.parse(line.selectedPrice);
                    line.unitPrice = parseFloat(priceData.price) || 0;
                    line.priceType = priceData.type || '';
                } catch (e) {
                    // Fallback pour compatibilité avec l'ancien format
                    line.unitPrice = parseFloat(line.selectedPrice) || 0;
                    line.priceType = '';
                }
                this.updateTotal();
            },

            onQuantityChange(index) {
                const line = this.productLines[index];
                const available = line.product?.quantity || 0;

                if (line.quantity > available) {
                    alert(`Stock insuffisant, disponible : ${available}`);
                    line.quantity = available;
                }

                this.updateTotal();
            },

            availableProducts(currentIndex) {
                const selectedIds = this.productLines
                    .map((line, i) =>
                        i !== currentIndex ? line.productId : null
                    )
                    .filter((id) => id);
                return this.products.filter((p) => !selectedIds.includes(p.id));
            },

            updateTotal() {
                this.total = this.productLines.reduce((sum, line) => {
                    return sum + line.unitPrice * line.quantity;
                }, 0);
            },

            formatAmount(value) {
                return Number(value).toLocaleString('fr-FR');
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

            printInvoice(sale_id) {
                window.location.href = `/newInvoice/${sale_id}`;
            },

            validateOrder() {
                if (!this.customer_name || this.customer_name.trim() === '') {
                    alert('Veuillez entrer le nom du client.');
                    return;
                }

                if (this.productLines.length === 0) {
                    alert('Veuillez ajouter au moins un produit.');
                    return;
                }

                for (let i = 0; i < this.productLines.length; i++) {
                    const line = this.productLines[i];
                    if (!line.productId || !line.unitPrice || !line.quantity) {
                        alert('Veuillez compléter toutes les lignes produit.');
                        return;
                    }
                }

                // Afficher le modal de récapitulatif
                this.showSummaryModal = true;
            },

            closeSummaryModal() {
                this.showSummaryModal = false;
            },

            submitForm() {
                if (this.isSubmitting) return;

                this.isSubmitting = true;

                const saleData = {
                    seller_name: this.seller_name,
                    buyer_name: this.customer_name,
                    buyer_phone: this.customer_phone,
                    payment_method: this.payment_method,
                    products: this.productLines.map((line) => ({
                        product_id: line.productId,
                        quantity: line.quantity,
                        price: line.unitPrice,
                        price_type: line.selectedPrice
                            ? JSON.parse(line.selectedPrice).type
                            : null,
                    })),
                };

                axios
                    .post('/sale', saleData)
                    .then((response) => {
                        alert('Vente enregistrée avec succès !');
                        window.location.reload();
                    })
                    .catch((error) => {
                        console.error(error);
                        let message = 'Une erreur est survenue.';

                        if (error.response && error.response.data) {
                            const data = error.response.data;
                            message = data.error || data.message || message;
                        }

                        alert(error.response.data.message);

                        this.isSubmitting = false;
                    });
            },

            cancelInvoice(id) {
                if (!confirm('Voulez-vous vraiment annuler cette facture ?'))
                    return;

                axios
                    .post(`/invoices/${id}/cancel`)
                    .then(() => {
                        alert('Facture annulée avec succès.');
                        this.fetchSalesData();
                    })
                    .catch((error) => {
                        alert("Erreur lors de l'annulation de la facture.");
                        console.error(error);
                    });
            },

            viewInvoice(sale) {
                this.selectedSale = sale;
                this.showSaleModal = true;
            },
            closeSaleModal() {
                this.showSaleModal = false;
                this.selectedSale = null;
            },
        },
    };
</script>

<style>
    .pagination-container {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    .pagination-numbers {
        display: flex;
        gap: 0.25rem;
    }

    .pagination-btn {
        padding: 0.5rem 0.75rem;
        border: 1px solid #ddd;
        background: #fff;
        cursor: pointer;
        border-radius: 6px;
        transition: all 0.2s ease;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .pagination-btn:hover:not(:disabled) {
        background-color: #f0f0f0;
        border-color: #999;
    }

    .pagination-btn.pagination-nav {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: #007bff;
        color: white;
        border-color: #007bff;
    }

    .pagination-btn.pagination-nav:hover:not(:disabled) {
        background: #0056b3;
        border-color: #0056b3;
    }

    .pagination-btn.pagination-number.active {
        background-color: #007bff;
        color: #fff;
        font-weight: bold;
        border-color: #007bff;
    }

    .pagination-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
        background: #f5f5f5;
    }

    /* Styles pour le modal de récapitulatif */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 999;
        padding: 1rem;
    }

    .modal-container {
        background: #fff;
        border-radius: 12px;
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }

    .summary-modal {
        max-width: 700px;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 2px solid #f0f0f0;
    }

    .modal-header h5 {
        margin: 0;
        font-size: 1.3rem;
        color: #333;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #666;
        transition: color 0.2s;
    }

    .modal-close:hover {
        color: #000;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .summary-section {
        margin-bottom: 1rem;
    }

    .summary-section p {
        margin: 0.5rem 0;
        font-size: 1rem;
    }

    .products-summary {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .product-summary-item {
        padding: 1rem;
        background: #f9f9f9;
        border-radius: 8px;
        border-left: 4px solid #007bff;
    }

    .product-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .product-name {
        font-weight: 600;
        color: #333;
        font-size: 1.05rem;
    }

    .badge-depositable {
        background: #28a745;
        color: white;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .product-details {
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #666;
        font-size: 0.95rem;
    }

    .product-subtotal {
        font-weight: 600;
        color: #333;
    }

    .total-summary {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        background: #f0f8ff;
        border-radius: 8px;
        margin-top: 1rem;
    }

    .payment-method-section {
        margin-top: 1.5rem;
        padding: 1rem;
        background: #fff9e6;
        border-radius: 8px;
        border: 1px solid #ffd700;
    }

    .payment-select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 1rem;
        cursor: pointer;
    }

    .modal-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-top: 2px solid #f0f0f0;
        gap: 1rem;
    }

    .summary-footer {
        flex-wrap: wrap;
    }

    .btn-secondary {
        padding: 0.75rem 1.5rem;
        background: #6c757d;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: background 0.2s;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    .btn-primary {
        padding: 0.75rem 1.5rem;
        background: #28a745;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: background 0.2s;
    }

    .btn-primary:hover {
        background: #218838;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .modal-container {
            width: 95%;
            max-height: 95vh;
        }

        .modal-header h5 {
            font-size: 1.1rem;
        }

        .modal-footer {
            flex-direction: column;
        }

        .btn-secondary,
        .btn-primary {
            width: 100%;
            justify-content: center;
        }

        .pagination-container {
            gap: 0.25rem;
        }

        .pagination-btn {
            padding: 0.4rem 0.6rem;
            font-size: 0.85rem;
        }
    }

    /* Ajout de styles pour aligner tous les inputs et selects sur la même ligne */
    .product-line {
        display: flex;
        align-items: flex-end;
        gap: 1rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #333;
        font-size: 0.9rem;
    }

    .form-control {
        padding: 0.5rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.95rem;
    }

    .btn-remove {
        padding: 0.5rem 0.75rem;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.2s;
        height: 38px;
    }

    .btn-remove:hover {
        background: #c82333;
    }
</style>
