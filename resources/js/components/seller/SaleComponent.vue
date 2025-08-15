<template>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <main class="main-content" id="app">
        <div class="sales-content">
            <!-- Sales Form -->
            <div class="sales-form">
                <h3 style="margin-bottom: 1.5rem; color: #333">
                    Informations de la vente
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
                            <div
                                class="product-line"
                                v-for="(line, index) in productLines"
                                :key="index"
                            >
                                <!-- Sélecteur produit -->
                                <div class="form-group">
                                    <label>Produit</label>
                                    <select
                                        v-model="line.productId"
                                        @change="onProductChange(index)"
                                        class="form-control product-select"
                                        style="width: 150px"
                                    >
                                        <option disabled value="">
                                            Sélectionner un produit
                                        </option>
                                        <option
                                            v-for="product in availableProducts(
                                                index
                                            )"
                                            :key="product.id"
                                            :value="product.id"
                                        >
                                            {{ product.name }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Sélecteur type de prix -->
                                <div class="form-group" v-if="line.product">
                                    <label>Type de prix</label>
                                    <select
                                        class="form-control price-type"
                                        v-model="line.selectedPrice"
                                        @change="updateUnitPrice(index)"
                                    >
                                        <option disabled value="">
                                            Sélectionner le type
                                        </option>
                                        <option
                                            :value="line.product.price_detail"
                                        >
                                            Détail
                                            {{ line.product.price_detail }}
                                        </option>
                                        <option
                                            :value="
                                                line.product.price_semi_bulk
                                            "
                                        >
                                            Semi gros
                                            {{ line.product.price_semi_bulk }}
                                        </option>
                                        <option
                                            :value="line.product.price_bulk"
                                        >
                                            Gros {{ line.product.price_bulk }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Quantité -->
                                <div class="form-group">
                                    <label>Quantité</label>
                                    <input
                                        type="number"
                                        class="form-control quantity-input"
                                        min="1"
                                        :max="line.product?.quantity || 1"
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
                        <div style="text-align: center; margin-top: 1.5rem">
                            <button class="btn-primary" type="submit">
                                <i class="fas fa-check"></i>
                                Finaliser la vente
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
                                    <i class="fas fa-eye"></i>
                                    Voir
                                </button>
                                <button
                                    class="print-btn"
                                    @click="printInvoice(sale.id)"
                                >
                                    <i class="fas fa-print"></i>
                                    Imprimer
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
            };
        },

        mounted() {
            this.fetchSalesData();
            this.addProductLine(); // initialiser avec une ligne
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
                });
            },

            removeProductLine(index) {
                this.productLines.splice(index, 1);
                this.updateTotal();
            },

            onProductChange(index) {
                const productId = this.productLines[index].productId;
                if (!productId) return;

                axios
                    .get(`/product/${productId}`)
                    .then((response) => {
                        // Affectations directes
                        this.productLines[index].product = response.data;
                        this.productLines[index].selectedPrice = '';
                        this.productLines[index].unitPrice = 0;
                        this.productLines[index].quantity = 1;
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
                line.unitPrice = parseFloat(line.selectedPrice) || 0;
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

            submitForm() {
                if (this.isSubmitting) return;
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

                const saleData = {
                    seller_name: this.seller_name,
                    buyer_name: this.customer_name,
                    buyer_phone: this.customer_phone,
                    products: this.productLines.map((line) => ({
                        product_id: line.productId,
                        quantity: line.quantity,
                        price: line.unitPrice,
                    })),
                };

                axios
                    .post('/sale', saleData)
                    .then((response) => {
                        alert('Vente enregistrée avec succès !');
                        window.location.reload();
                    })
                    .catch((error) => {
                        console.error(
                            "Erreur lors de l'enregistrement de la vente :",
                            error
                        );
                        let message = 'Une erreur est survenue.';

                        if (error.response && error.response.data) {
                            const data = error.response.data;
                            message = data.error || data.message || message;
                        }

                        alert(message);

                        this.isSubmitting = false;
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
</style>
