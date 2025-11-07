<template>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <main class="main-content" id="app">
        <div class="sales-content">
            <!-- Sales Form -->
            <div class="sales-form">
                <h3 style="margin-bottom: 1.5rem; color: #333">
                    Informations de la facture proforma
                </h3>

                <form @submit.prevent="submitForm">
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
                                style="width: 250px"
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
                            <!-- Réorganisation des lignes de produits pour supporter les produits consignables -->
                            <div
                                class="product-line"
                                v-for="(line, index) in productLines"
                                :key="index"
                            >
                                <!-- Remplacement du select simple par un combo recherche/select -->
                                <div class="form-group product-group">
                                    <label>Produit</label>
                                    <div class="product-search-container">
                                        <input
                                            type="text"
                                            class="form-control product-search-input"
                                            v-model="line.searchQuery"
                                            @input="onProductSearch(index)"
                                            @focus="line.showDropdown = true"
                                            placeholder="Rechercher ou sélectionner un produit..."
                                        />
                                        <div
                                            v-if="
                                                line.showDropdown &&
                                                filteredProducts(index).length >
                                                    0
                                            "
                                            class="product-dropdown"
                                        >
                                            <div
                                                v-for="product in filteredProducts(
                                                    index
                                                )"
                                                :key="product.id"
                                                class="product-dropdown-item"
                                                @click="
                                                    selectProduct(
                                                        index,
                                                        product
                                                    )
                                                "
                                            >
                                                {{ product.name }}
                                                <span
                                                    v-if="
                                                        isProductDepositable(
                                                            product
                                                        )
                                                    "
                                                    class="badge-small-inline"
                                                >
                                                    Consignable
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Info produit avec badge consignable -->
                                    <div
                                        v-if="line.product"
                                        class="product-info-text"
                                    >
                                        <!-- Utilisation de la méthode isProductDepositable pour vérification stricte -->
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
                                    </div>
                                </div>

                                <!-- Sélecteur type de prix avec support des produits consignables -->
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

                                        <!-- Vérification stricte avec isProductDepositable -->
                                        <!-- Options pour produits consignables -->
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

                                <!-- Supprimer ligne -->
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
                                Finaliser la facture proforma
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Sales History -->
            <div class="sales-history">
                <div class="history-header">
                    <h3>
                        Historique de mes factures proforma (
                        {{ sales.length }} )
                    </h3>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>N° Facture</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Montant</th>
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

                <div
                    class="pagination"
                    style="margin-top: 1rem; text-align: center"
                >
                    <button
                        :disabled="currentPage === 1"
                        @click="currentPage--"
                        class="pagination-btn"
                    >
                        ← Précédent
                    </button>

                    <button
                        v-for="page in totalPages"
                        :key="page"
                        @click="currentPage = page"
                        :class="[
                            'pagination-btn',
                            { active: currentPage === page },
                        ]"
                    >
                        {{ page }}
                    </button>

                    <button
                        :disabled="currentPage === totalPages"
                        @click="currentPage++"
                        class="pagination-btn"
                    >
                        Suivant →
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Détails de la vente -->
        <div v-if="showSaleModal" class="modal-overlay">
            <div class="modal-container">
                <div class="modal-header">
                    <h3>Détails de la facture Proforma</h3>
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
                    <!-- Ajout du moyen de paiement dans les détails -->
                    <p v-if="selectedSale.payment_method">
                        <strong>Moyen de paiement :</strong>
                        {{ formatPaymentMethod(selectedSale.payment_method) }}
                    </p>
                    <hr />
                    <h6>Produits achetés :</h6>
                    <ul>
                        <li
                            v-for="(product, i) in selectedSale.products"
                            :key="i"
                        >
                            {{ product.product?.name ?? 'Produit supprimé' }}
                            <!-- Affichage du type de transaction pour produits consignables -->
                            <span
                                v-if="product.price_type"
                                style="
                                    font-style: italic;
                                    color: #666;
                                    font-size: 0.9em;
                                "
                            >
                                ({{ formatPriceType(product.price_type) }})
                            </span>
                            - {{ product.quantity }} ×
                            {{ formatAmount(Number(product.price)) }} =
                            {{
                                formatAmount(
                                    Number(product.price) * product.quantity
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
        name: 'ProformaComponent',

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
                payment_method: 'cash',
            };
        },

        mounted() {
            console.log('[v0] ProformaComponent mounted - Initialisation');
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
        },

        methods: {
            isProductDepositable(product) {
                if (!product) {
                    console.log(
                        '[v0] isProductDepositable: produit null/undefined'
                    );
                    return false;
                }

                const depositable = product.is_depositable;

                // Vérification stricte : accepte true, 1, "1"
                const result =
                    depositable === true ||
                    depositable === 1 ||
                    depositable === '1';

                console.log('[v0] isProductDepositable:', {
                    productName: product.name,
                    is_depositable: depositable,
                    type: typeof depositable,
                    result: result,
                });

                return result;
            },

            filteredProducts(currentIndex) {
                const line = this.productLines[currentIndex];
                const searchQuery = (line.searchQuery || '')
                    .toLowerCase()
                    .trim();

                // Produits déjà sélectionnés dans d'autres lignes
                const selectedIds = this.productLines
                    .map((l, i) => (i !== currentIndex ? l.productId : null))
                    .filter((id) => id);

                // Filtrer par recherche et disponibilité
                let filtered = this.products.filter((p) => {
                    if (selectedIds.includes(p.id)) return false;
                    if (!searchQuery) return true;
                    return p.name.toLowerCase().includes(searchQuery);
                });

                console.log('[v0] filteredProducts:', {
                    searchQuery,
                    totalProducts: this.products.length,
                    filteredCount: filtered.length,
                });

                return filtered;
            },

            selectProduct(index, product) {
                console.log('[v0] selectProduct:', {
                    index,
                    productId: product.id,
                    productName: product.name,
                    is_depositable: product.is_depositable,
                });

                this.productLines[index].productId = product.id;
                this.productLines[index].searchQuery = product.name;
                this.productLines[index].showDropdown = false;
                this.onProductChange(index);
            },

            onProductSearch(index) {
                const line = this.productLines[index];
                line.showDropdown = true;

                // Si la recherche est vide, réinitialiser la sélection
                if (!line.searchQuery || line.searchQuery.trim() === '') {
                    line.productId = '';
                    line.product = null;
                    line.selectedPrice = '';
                    line.unitPrice = 0;
                    line.priceType = '';
                    this.updateTotal();
                }
            },

            fetchSalesData() {
                console.log(
                    '[v0] fetchSalesData: Début du chargement des données'
                );

                // Chargement des produits
                axios
                    .get('/productsList')
                    .then((response) => {
                        console.log('[v0] Produits chargés:', {
                            count: response.data.length,
                            sample: response.data.slice(0, 2).map((p) => ({
                                id: p.id,
                                name: p.name,
                                is_depositable: p.is_depositable,
                                type: typeof p.is_depositable,
                            })),
                        });

                        this.products = response.data.sort((a, b) => {
                            return a.name.localeCompare(b.name);
                        });
                    })
                    .catch((error) => {
                        console.error(
                            '[v0] Erreur lors du chargement des produits:',
                            {
                                message: error.message,
                                response: error.response?.data,
                                status: error.response?.status,
                            }
                        );
                        alert(
                            'Erreur lors du chargement des produits. Veuillez rafraîchir la page.'
                        );
                    });

                // Chargement des ventes
                axios
                    .get('/proformaApiBySellerList')
                    .then((response) => {
                        console.log('[v0] Factures proforma chargées:', {
                            count: response.data.length,
                        });
                        this.sales = response.data;
                    })
                    .catch((error) => {
                        console.error(
                            '[v0] Erreur lors du chargement des factures:',
                            {
                                message: error.message,
                                response: error.response?.data,
                                status: error.response?.status,
                            }
                        );
                        alert(
                            'Erreur lors du chargement des factures. Veuillez rafraîchir la page.'
                        );
                    });
            },

            addProductLine() {
                console.log("[v0] addProductLine: Ajout d'une nouvelle ligne");
                this.productLines.push({
                    productId: '',
                    product: null,
                    selectedPrice: '',
                    unitPrice: 0,
                    quantity: 1,
                    priceType: '',
                    searchQuery: '', // Ajout du champ de recherche
                    showDropdown: false, // Ajout du contrôle du dropdown
                });
            },

            removeProductLine(index) {
                console.log('[v0] removeProductLine:', { index });
                this.productLines.splice(index, 1);
                this.updateTotal();
            },

            onProductChange(index) {
                const productId = this.productLines[index].productId;
                if (!productId) return;

                console.log('[v0] onProductChange:', { index, productId });

                axios
                    .get(`/product/${productId}`)
                    .then((response) => {
                        console.log('[v0] Détails du produit chargés:', {
                            productId,
                            name: response.data.name,
                            is_depositable: response.data.is_depositable,
                            type: typeof response.data.is_depositable,
                            deposit_price: response.data.deposit_price,
                            filling_price: response.data.filling_price,
                            price_detail: response.data.price_detail,
                        });

                        this.productLines[index].product = response.data;
                        this.productLines[index].selectedPrice = '';
                        this.productLines[index].unitPrice = 0;
                        this.productLines[index].quantity = 1;
                        this.productLines[index].priceType = '';
                        this.updateTotal();
                    })
                    .catch((error) => {
                        console.error(
                            '[v0] Erreur lors de la récupération du produit:',
                            {
                                productId,
                                message: error.message,
                                response: error.response?.data,
                                status: error.response?.status,
                            }
                        );
                        alert(
                            'Erreur lors du chargement du produit. Veuillez réessayer.'
                        );
                    });
            },

            updateUnitPrice(index) {
                const line = this.productLines[index];
                console.log('[v0] updateUnitPrice:', {
                    index,
                    selectedPrice: line.selectedPrice,
                });

                try {
                    const priceData = JSON.parse(line.selectedPrice);
                    line.unitPrice = parseFloat(priceData.price) || 0;
                    line.priceType = priceData.type || '';

                    console.log('[v0] Prix mis à jour:', {
                        unitPrice: line.unitPrice,
                        priceType: line.priceType,
                    });
                } catch (e) {
                    console.error('[v0] Erreur lors du parsing du prix:', e);
                    line.unitPrice = parseFloat(line.selectedPrice) || 0;
                    line.priceType = '';
                }
                this.updateTotal();
            },

            onQuantityChange(index) {
                console.log('[v0] onQuantityChange:', {
                    index,
                    quantity: this.productLines[index].quantity,
                });
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

                console.log('[v0] Total mis à jour:', {
                    total: this.total,
                    linesCount: this.productLines.length,
                });
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

            formatPaymentMethod(method) {
                const methods = {
                    cash: 'Espèces (Cash)',
                    credit: 'Crédit',
                    mobile_money: 'Mobile Money',
                };
                return methods[method] || method;
            },

            formatPriceType(type) {
                const types = {
                    deposit: 'Consignation',
                    refill: 'Recharge',
                    both: 'Consignation + Recharge',
                    detail: 'Détail',
                    semi_bulk: 'Semi gros',
                    bulk: 'Gros',
                };
                return types[type] || type;
            },

            printInvoice(proforma_id) {
                console.log('[v0] printInvoice:', { proforma_id });
                window.location.href = `/newProforma/${proforma_id}`;
            },

            submitForm() {
                if (this.isSubmitting) {
                    console.log('[v0] submitForm: Soumission déjà en cours');
                    return;
                }

                console.log('[v0] submitForm: Validation du formulaire');

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

                this.isSubmitting = true;

                const proformaData = {
                    seller_name: this.seller_name,
                    buyer_name: this.customer_name,
                    buyer_phone: this.customer_phone,
                    payment_method: this.payment_method,
                    products: this.productLines.map((line) => ({
                        product_id: line.productId,
                        quantity: line.quantity,
                        price: line.unitPrice,
                        price_type: line.priceType,
                    })),
                };

                console.log('[v0] Envoi de la facture proforma:', proformaData);

                axios
                    .post('/proforma', proformaData)
                    .then((response) => {
                        console.log(
                            '[v0] Facture proforma enregistrée avec succès:',
                            response.data
                        );
                        alert('Facture proforma enregistrée avec succès !');
                        window.location.reload();
                    })
                    .catch((error) => {
                        console.error("[v0] Erreur lors de l'enregistrement:", {
                            message: error.message,
                            response: error.response?.data,
                            status: error.response?.status,
                        });

                        let message = 'Une erreur est survenue.';

                        if (error.response && error.response.data) {
                            const data = error.response.data;
                            message = data.error || data.message || message;
                        }

                        alert(message);
                        this.isSubmitting = false;
                    });
            },

            cancelInvoice(id) {
                if (!confirm('Voulez-vous vraiment annuler cette facture ?'))
                    return;

                console.log('[v0] cancelInvoice:', { id });

                axios
                    .post(`/invoices/${id}/cancel`)
                    .then(() => {
                        console.log('[v0] Facture annulée avec succès');
                        alert('Facture annulée avec succès.');
                        this.fetchSalesData();
                    })
                    .catch((error) => {
                        console.error("[v0] Erreur lors de l'annulation:", {
                            message: error.message,
                            response: error.response?.data,
                            status: error.response?.status,
                        });
                        alert("Erreur lors de l'annulation de la facture.");
                    });
            },

            viewInvoice(sale) {
                console.log('[v0] viewInvoice:', { saleId: sale.id });
                this.selectedSale = sale;
                this.showSaleModal = true;
            },

            closeSaleModal() {
                console.log('[v0] closeSaleModal');
                this.showSaleModal = false;
                this.selectedSale = null;
            },
        },
    };
</script>

<style>
    /* Ajout des styles pour les produits consignables et l'amélioration de la mise en page */
    .product-line {
        display: flex;
        align-items: flex-end;
        gap: 1rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

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

    .payment-section {
        margin-top: 2rem;
        padding: 1.5rem;
        background: #f9f9f9;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
    }

    .payment-select {
        padding: 0.5rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.95rem;
        cursor: pointer;
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
    }

    .pagination-btn {
        margin: 0 4px;
        padding: 6px 12px;
        border: 1px solid #ddd;
        background: #f9f9f9;
        cursor: pointer;
        border-radius: 4px;
    }

    .pagination-btn.active {
        background-color: #007bff;
        color: #fff;
        font-weight: bold;
    }

    .pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 999;
    }

    .modal-container {
        background: #fff;
        border-radius: 10px;
        width: 90%;
        max-width: 600px;
        padding: 1.5rem;
    }

    .modal-header,
    .modal-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.2rem;
        cursor: pointer;
    }

    /* Ajout des styles pour le système de recherche/select combiné */
    .product-search-container {
        position: relative;
        width: 100%;
    }

    .product-search-input {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.95rem;
    }

    .product-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        max-height: 250px;
        overflow-y: auto;
        background: white;
        border: 1px solid #ddd;
        border-top: none;
        border-radius: 0 0 6px 6px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        margin-top: -1px;
    }

    .product-dropdown-item {
        padding: 0.75rem;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background 0.2s;
    }

    .product-dropdown-item:hover {
        background: #f8f9fa;
    }

    .product-dropdown-item:last-child {
        border-bottom: none;
    }

    .badge-small-inline {
        background: #28a745;
        color: white;
        padding: 2px 8px;
        border-radius: 3px;
        font-size: 0.7rem;
        margin-left: 8px;
    }
</style>
