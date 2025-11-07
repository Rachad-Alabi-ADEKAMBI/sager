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
                    <!-- Customer Information with Search -->
                    <div class="customer-info">
                        <div class="form-group">
                            <label>Nom du client</label>
                            <!-- Ajout d'un système de recherche de clients avec autocomplétion -->
                            <div class="customer-search-container">
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="customerSearchQuery"
                                    @input="onCustomerSearch"
                                    @focus="showCustomerDropdown = true"
                                    placeholder="Rechercher ou créer un client"
                                    style="width: 300px"
                                    required
                                />

                                <!-- Dropdown des clients filtrés -->
                                <div
                                    v-if="
                                        showCustomerDropdown &&
                                        filteredCustomers.length > 0
                                    "
                                    class="customer-dropdown"
                                >
                                    <div
                                        v-for="customer in filteredCustomers"
                                        :key="customer.id"
                                        @click="selectCustomer(customer)"
                                        class="customer-dropdown-item"
                                    >
                                        <div class="customer-item-name">
                                            {{ customer.name }}
                                        </div>
                                        <div class="customer-item-phone">
                                            {{ customer.phone }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Bouton pour créer un nouveau client -->
                                <button
                                    v-if="
                                        customerSearchQuery &&
                                        !customerSelected &&
                                        filteredCustomers.length === 0
                                    "
                                    type="button"
                                    @click="openCreateCustomerModal"
                                    class="btn-create-customer"
                                >
                                    <i class="fas fa-plus"></i>
                                    Créer "{{ customerSearchQuery }}"
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Product Lines -->
                    <div class="product-lines">
                        <h4 style="margin-bottom: 1rem; color: #333">
                            Produits à vendre
                        </h4>
                        <div id="productLinesContainer">
                            <!-- Réorganisation pour aligner tous les inputs sur la même ligne -->
                            <div
                                class="product-line"
                                v-for="(line, index) in productLines"
                                :key="index"
                            >
                                <!-- Remplacement du select simple par un combo input/select avec recherche -->
                                <div class="form-group product-group">
                                    <label>Produit</label>
                                    <div class="product-search-container">
                                        <input
                                            type="text"
                                            class="form-control product-search-input"
                                            v-model="line.searchQuery"
                                            @input="onProductSearch(index)"
                                            @focus="line.showDropdown = true"
                                            placeholder="Rechercher un produit..."
                                        />

                                        <!-- Dropdown des produits filtrés -->
                                        <div
                                            v-if="
                                                line.showDropdown &&
                                                getFilteredProductsForLine(
                                                    index
                                                ).length > 0
                                            "
                                            class="product-dropdown"
                                        >
                                            <div
                                                v-for="product in getFilteredProductsForLine(
                                                    index
                                                )"
                                                :key="product.id"
                                                @click="
                                                    selectProduct(
                                                        index,
                                                        product
                                                    )
                                                "
                                                class="product-dropdown-item"
                                            >
                                                <div class="product-item-name">
                                                    {{ product.name }}
                                                    <span
                                                        v-if="
                                                            isProductDepositable(
                                                                product
                                                            )
                                                        "
                                                        class="badge-inline"
                                                    >
                                                        Consignable
                                                    </span>
                                                </div>
                                                <div class="product-item-stock">
                                                    Stock:
                                                    {{ product.quantity }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Info produit en dessous -->
                                    <div
                                        v-if="line.product"
                                        class="product-info-text"
                                    >
                                        <!-- Vérification stricte avec la méthode helper -->
                                        <span
                                            v-if="
                                                isProductDepositable(
                                                    line.product
                                                )
                                            "
                                            class="badge-small"
                                        >
                                            Consignable
                                        </span>
                                        <span class="stock-info">
                                            Stock: {{ line.product.quantity }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Vérification stricte pour afficher les bons types de prix -->
                                <div
                                    class="form-group price-type-group"
                                    v-if="line.product"
                                >
                                    <label>Type de prix</label>
                                    <select
                                        class="form-control price-type"
                                        v-model="line.selectedPrice"
                                        @change="updateUnitPrice(index)"
                                    >
                                        <option disabled value="">
                                            Sélectionner le type
                                        </option>

                                        <!-- Options pour produits consignables avec vérification stricte -->
                                        <template
                                            v-if="
                                                isProductDepositable(
                                                    line.product
                                                )
                                            "
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
                                                Consignation ({{
                                                    formatAmount(
                                                        line.product
                                                            .deposit_price
                                                    )
                                                }}
                                                FCFA)
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
                                                Recharge ({{
                                                    formatAmount(
                                                        line.product
                                                            .filling_price
                                                    )
                                                }}
                                                FCFA)
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
                                                Consig. + Recharge ({{
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
                                                FCFA)
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
                                                Détail ({{
                                                    formatAmount(
                                                        line.product
                                                            .price_detail
                                                    )
                                                }}
                                                FCFA)
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
                                                Semi gros ({{
                                                    formatAmount(
                                                        line.product
                                                            .price_semi_bulk
                                                    )
                                                }}
                                                FCFA)
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
                                                Gros ({{
                                                    formatAmount(
                                                        line.product.price_bulk
                                                    )
                                                }}
                                                FCFA)
                                            </option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Quantité -->
                                <div class="form-group quantity-group">
                                    <label>Quantité</label>
                                    <input
                                        type="number"
                                        class="form-control quantity-input"
                                        min="0.01"
                                        step="0.01"
                                        :max="line.product?.quantity || 0"
                                        v-model.number="line.quantity"
                                        @input="onQuantityChange(index)"
                                    />
                                </div>

                                <!-- Prix unitaire -->
                                <div class="form-group price-group">
                                    <label>Prix unitaire</label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        step="0.01"
                                        v-model="line.unitPrice"
                                        readonly
                                    />
                                </div>

                                <!-- Bouton supprimer -->
                                <div class="form-group action-group">
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

        <!-- Nouveau modal pour créer un client -->
        <div
            v-if="showCreateCustomerModal"
            class="modal-overlay"
            @click.self="closeCreateCustomerModal"
        >
            <div class="modal-container create-customer-modal">
                <div class="modal-header">
                    <h5>
                        <i class="fas fa-user-plus"></i>
                        Créer un nouveau client
                    </h5>
                    <button
                        @click="closeCreateCustomerModal"
                        class="modal-close"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nom du client *</label>
                        <input
                            type="text"
                            class="form-control"
                            v-model="newCustomer.name"
                            placeholder="Nom complet"
                            required
                        />
                    </div>
                    <div class="form-group">
                        <label>Téléphone *</label>
                        <input
                            type="tel"
                            class="form-control"
                            v-model="newCustomer.phone"
                            placeholder="Numéro de téléphone"
                            required
                        />
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        @click="closeCreateCustomerModal"
                        class="btn-secondary"
                    >
                        <i class="fas fa-times"></i>
                        Annuler
                    </button>
                    <button @click="createCustomer" class="btn-primary">
                        <i class="fas fa-check"></i>
                        Créer le client
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
                                <!-- Vérification stricte pour le badge -->
                                <span
                                    v-if="isProductDepositable(line.product)"
                                    class="badge-depositable"
                                >
                                    Consignable
                                </span>
                            </div>

                            <!-- Affichage du type de transaction pour produits consignables -->
                            <div
                                v-if="
                                    isProductDepositable(line.product) &&
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
                            <option value="cash" selected>
                                Espèces (Cash)
                            </option>
                            <option value="credit">Crédit</option>
                            <option value="mobile_money">Mobile Money</option>
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
                payment_method: 'cash',
                customers: [],
                customerSearchQuery: '',
                showCustomerDropdown: false,
                customerSelected: false,
                selectedCustomerId: null,
                showCreateCustomerModal: false,
                newCustomer: {
                    name: '',
                    phone: '',
                },
                isSubmitting: false, // Added for preventing double submission
            };
        },

        mounted() {
            this.fetchSalesData();
            this.fetchCustomers();
            this.addProductLine();
            document.addEventListener('click', this.handleClickOutside);
        },

        beforeUnmount() {
            document.removeEventListener('click', this.handleClickOutside);
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
            filteredCustomers() {
                if (!this.customerSearchQuery)
                    return this.customers.slice(0, 5);

                const query = this.customerSearchQuery.toLowerCase();
                return this.customers
                    .filter(
                        (customer) =>
                            customer.name.toLowerCase().includes(query) ||
                            customer.phone.includes(query)
                    )
                    .slice(0, 5);
            },
        },

        methods: {
            isProductDepositable(product) {
                if (!product) {
                    // console.log('[v0] Product is null or undefined');
                    return false;
                }

                // Vérification stricte qui gère les cas où is_depositable peut être:
                // - un booléen (true/false)
                // - un nombre (1/0)
                // - une chaîne ("1"/"0", "true"/"false")
                const depositable = product.is_depositable;
                const result =
                    depositable === 1 ||
                    depositable === true ||
                    depositable === '1' ||
                    depositable === 'true';

                // console.log('[v0] Product:', product.name, 'is_depositable value:', depositable, 'type:', typeof depositable, 'result:', result);
                return result;
            },

            fetchCustomers() {
                // console.log('[v0] Fetching customers...');
                axios
                    .get('/clientslist')
                    .then((response) => {
                        this.customers = response.data;
                        // console.log('[v0] Customers loaded:', this.customers.length);
                    })
                    .catch((error) => {
                        console.error('[v0] Error fetching customers:', error);
                    });
            },

            onCustomerSearch() {
                this.showCustomerDropdown = true;
                this.customerSelected = false;
            },

            selectCustomer(customer) {
                this.customerSearchQuery = customer.name;
                this.customer_name = customer.name;
                this.customer_phone = customer.phone;
                this.selectedCustomerId = customer.id;
                this.customerSelected = true;
                this.showCustomerDropdown = false;
            },

            handleClickOutside(event) {
                const container = event.target.closest(
                    '.customer-search-container, .product-search-container'
                );
                if (!container) {
                    this.showCustomerDropdown = false;
                    // Fermer tous les dropdowns de produits
                    this.productLines.forEach((line) => {
                        line.showDropdown = false;
                    });
                }
            },

            openCreateCustomerModal() {
                this.newCustomer.name = this.customerSearchQuery;
                this.newCustomer.phone = '';
                this.showCreateCustomerModal = true;
            },

            closeCreateCustomerModal() {
                this.showCreateCustomerModal = false;
                this.newCustomer = { name: '', phone: '' };
            },

            createCustomer() {
                if (!this.newCustomer.name || !this.newCustomer.phone) {
                    alert('Veuillez remplir tous les champs obligatoires.');
                    return;
                }

                // console.log('[v0] Creating customer:', this.newCustomer);
                axios
                    .post('/clients', {
                        name: this.newCustomer.name,
                        phone: this.newCustomer.phone,
                    })
                    .then((response) => {
                        alert('Client créé avec succès !');
                        const client = response.data.client;
                        this.customers.push(client);
                        this.selectCustomer(client);
                        this.closeCreateCustomerModal();
                        // console.log('[v0] Customer created successfully:', client);
                    })
                    .catch((error) => {
                        console.error('[v0] Error creating customer:', error);
                        alert(
                            'Erreur lors de la création du client. Veuillez réessayer.'
                        );
                    });
            },

            fetchSalesData() {
                // console.log('[v0] Fetching products and sales...');
                axios
                    .get('/productsList')
                    .then((response) => {
                        this.products = response.data.sort((a, b) => {
                            return a.name.localeCompare(b.name);
                        });
                        // console.log('[v0] Products loaded:', this.products.length);

                        // Log des produits consignables pour débogage
                        // this.products.forEach(p => {
                        //     console.log('[v0] Product:', p.name, 'is_depositable:', p.is_depositable, 'type:', typeof p.is_depositable);
                        // });
                    })
                    .catch((error) => {
                        console.error('[v0] Error fetching products:', error);
                    });

                axios
                    .get('/sellerSalesList?seller_name=' + this.seller_name)
                    .then((response) => {
                        this.sales = response.data;
                        // console.log('[v0] Sales loaded:', this.sales.length);
                    })
                    .catch((error) => {
                        console.error('[v0] Error fetching sales:', error);
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
                    priceType: '',
                    showDropdown: false, // Ajout du flag pour le dropdown
                });
            },

            removeProductLine(index) {
                this.productLines.splice(index, 1);
                this.updateTotal();
            },

            getFilteredProductsForLine(currentIndex) {
                const line = this.productLines[currentIndex];

                // Produits déjà sélectionnés dans d'autres lignes
                const selectedIds = this.productLines
                    .map((l, i) => (i !== currentIndex ? l.productId : null))
                    .filter((id) => id);

                // Filtrer par recherche et disponibilité
                let filtered = this.products.filter(
                    (p) => !selectedIds.includes(p.id)
                );

                if (line.searchQuery && line.searchQuery.trim() !== '') {
                    const query = line.searchQuery.toLowerCase();
                    filtered = filtered.filter((p) =>
                        p.name.toLowerCase().includes(query)
                    );
                }

                return filtered.slice(0, 10); // Limiter à 10 résultats
            },

            onProductSearch(index) {
                this.productLines[index].showDropdown = true;
            },

            selectProduct(index, product) {
                // console.log('[v0] Selecting product:', product.name, 'for line', index);
                this.productLines[index].productId = product.id;
                this.productLines[index].searchQuery = product.name;
                this.productLines[index].showDropdown = false;

                // Charger les détails du produit
                axios
                    .get(`/product/${product.id}`)
                    .then((response) => {
                        this.productLines[index].product = response.data;
                        this.productLines[index].selectedPrice = '';
                        this.productLines[index].unitPrice = 0;
                        this.productLines[index].quantity = 1;
                        this.productLines[index].priceType = '';

                        // console.log('[v0] Product details loaded:', response.data);
                        // console.log('[v0] Is depositable:', this.isProductDepositable(response.data));

                        this.updateTotal();
                    })
                    .catch((error) => {
                        console.error(
                            '[v0] Error loading product details:',
                            error
                        );
                    });
            },

            filteredProducts(currentIndex) {
                const selectedIds = this.productLines
                    .map((l, i) => (i !== currentIndex ? l.productId : null))
                    .filter((id) => id);

                return this.products.filter((p) => !selectedIds.includes(p.id));
            },

            onProductChange(index) {
                const productId = this.productLines[index].productId;
                if (!productId) return;

                // console.log('[v0] Product changed for line', index, 'productId:', productId);
                axios
                    .get(`/product/${productId}`)
                    .then((response) => {
                        this.productLines[index].product = response.data;
                        this.productLines[index].selectedPrice = '';
                        this.productLines[index].unitPrice = 0;
                        this.productLines[index].quantity = 1;
                        this.productLines[index].priceType = '';

                        // console.log('[v0] Product loaded:', response.data);
                        // console.log('[v0] Is depositable:', this.isProductDepositable(response.data));

                        this.updateTotal();
                    })
                    .catch((error) => {
                        console.error('[v0] Error loading product:', error);
                    });
            },

            updateUnitPrice(index) {
                const line = this.productLines[index];
                // console.log('[v0] Updating unit price for line', index, 'selectedPrice:', line.selectedPrice);

                try {
                    const priceData = JSON.parse(line.selectedPrice);
                    line.unitPrice = parseFloat(priceData.price) || 0;
                    line.priceType = priceData.type || '';

                    // console.log('[v0] Price updated - unitPrice:', line.unitPrice, 'priceType:', line.priceType);
                } catch (e) {
                    console.error('[v0] Error parsing price data:', e);
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

                // console.log('[v0] Quantity changed for line', index, 'new quantity:', line.quantity);
                this.updateTotal();
            },

            updateTotal() {
                this.total = this.productLines.reduce((sum, line) => {
                    return sum + line.unitPrice * line.quantity;
                }, 0);
                // console.log('[v0] Total updated:', this.total);
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
                // console.log('[v0] Validating order...');

                if (
                    !this.customerSearchQuery ||
                    this.customerSearchQuery.trim() === ''
                ) {
                    alert('Veuillez sélectionner ou créer un client.');
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

                this.customer_name = this.customerSearchQuery;
                // console.log('[v0] Order validated, showing summary modal');
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

                // console.log('[v0] Submitting sale:', saleData);
                axios
                    .post('/sale', saleData)
                    .then((response) => {
                        // console.log('[v0] Sale submitted successfully:', response.data);
                        alert('Vente enregistrée avec succès !');
                        window.location.reload();
                    })
                    .catch((error) => {
                        console.error('[v0] Error submitting sale:', error);
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

                // console.log('[v0] Cancelling invoice:', id);
                axios
                    .post(`/invoices/${id}/cancel`)
                    .then(() => {
                        alert('Facture annulée avec succès.');
                        this.fetchSalesData();
                    })
                    .catch((error) => {
                        alert("Erreur lors de l'annulation de la facture.");
                        console.error('[v0] Error cancelling invoice:', error);
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
    /* Nouveaux styles pour le système de recherche de produits */
    .product-search-container {
        position: relative;
        width: 100%;
    }

    .product-search-input {
        width: 100%;
    }

    .product-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        max-height: 300px;
        overflow-y: auto;
        z-index: 100;
        margin-top: 0.25rem;
    }

    .product-dropdown-item {
        padding: 0.75rem 1rem;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s;
    }

    .product-dropdown-item:hover {
        background: #f8f9fa;
    }

    .product-dropdown-item:last-child {
        border-bottom: none;
    }

    .product-item-name {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .product-item-stock {
        font-size: 0.85rem;
        color: #007bff;
    }

    .badge-inline {
        background: #28a745;
        color: white;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 0.7rem;
        font-weight: 500;
    }

    /* Styles pour la recherche de clients */
    .customer-search-container {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .customer-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        max-height: 250px;
        overflow-y: auto;
        z-index: 100;
        margin-top: 0.25rem;
    }

    .customer-dropdown-item {
        padding: 0.75rem 1rem;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s;
    }

    .customer-dropdown-item:hover {
        background: #f8f9fa;
    }

    .customer-dropdown-item:last-child {
        border-bottom: none;
    }

    .customer-item-name {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.25rem;
    }

    .customer-item-phone {
        font-size: 0.85rem;
        color: #666;
    }

    .btn-create-customer {
        padding: 0.75rem 1rem;
        background: #28a745;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.95rem;
        transition: background 0.2s;
        width: fit-content;
    }

    .btn-create-customer:hover {
        background: #218838;
    }

    .create-customer-modal .form-group {
        margin-bottom: 1rem;
    }

    .create-customer-modal .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #333;
    }

    .create-customer-modal .form-control {
        width: 100%;
    }

    /* Amélioration de l'alignement des lignes de produits pour desktop/tablette */
    .product-line {
        display: flex;
        align-items: flex-end;
        gap: 1rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    /* Largeurs spécifiques pour chaque colonne */
    .product-group {
        flex: 1;
        min-width: 200px;
    }

    .price-type-group {
        flex: 1;
        min-width: 180px;
    }

    .quantity-group {
        width: 120px;
        flex-shrink: 0;
    }

    .price-group {
        width: 130px;
        flex-shrink: 0;
    }

    .action-group {
        width: 50px;
        flex-shrink: 0;
    }

    .product-info-text {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        margin-top: 0.25rem;
        font-size: 0.8rem;
    }

    .badge-small {
        background: #28a745;
        color: white;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 0.7rem;
    }

    .stock-info {
        color: #007bff;
        font-weight: 500;
    }

    /* Responsive: empiler sur mobile */
    @media (max-width: 768px) {
        .product-line {
            flex-direction: column;
            align-items: stretch;
        }

        .product-group,
        .price-type-group,
        .quantity-group,
        .price-group,
        .action-group {
            width: 100%;
            min-width: unset;
        }

        .customer-search-container {
            width: 100%;
        }

        .customer-search-container input {
            width: 100% !important;
        }
    }

    /* Styles existants */
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

    .btn-add-line {
        margin-top: 1rem;
        padding: 0.75rem 1.5rem;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: background 0.2s;
    }

    .btn-add-line:hover:not(:disabled) {
        background: #0056b3;
    }

    .btn-add-line:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
