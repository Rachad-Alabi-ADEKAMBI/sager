<template>
    <main class="main-content" id="app">
        <header class="header">
            <div style="display: flex; align-items: center; gap: 1rem">
                <button class="menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>Nouvelle vente</h1>
            </div>
            <div class="user-info">
                <span>{{ seller_name }}</span>
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
            </div>
        </header>

        <div class="sales-content">
            <div class="sales-header"></div>

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
                            <div class="product-line first-line">
                                <div class="form-group">
                                    <label>Produit</label>
                                    <select
                                        v-model="selectedProductId"
                                        @change="onProductChange"
                                        class="form-control product-select"
                                    >
                                        <option value="">
                                            Sélectionner un produit
                                        </option>
                                        <option
                                            v-for="product in products"
                                            :key="product.id"
                                            :value="product.id"
                                        >
                                            {{ product.name }}
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group" v-if="product">
                                    <label>Type de prix</label>
                                    <select
                                        class="form-control price-type"
                                        v-model="selectedPrice"
                                        @change="updateUnitPrice"
                                    >
                                        <option value="">
                                            Sélectionner le type
                                        </option>
                                        <option :value="product.price_detail">
                                            Détail {{ product.price_detail }}
                                        </option>
                                        <option
                                            :value="product.price_semi_bulk"
                                        >
                                            Semi gros
                                            {{ product.price_semi_bulk }}
                                        </option>
                                        <option :value="product.price_bulk">
                                            Gros {{ product.price_bulk }}
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Quantité</label>
                                    <input
                                        type="number"
                                        class="form-control quantity-input"
                                        min="1"
                                        :max="product.quantity"
                                        v-model.number="quantity"
                                        style="width: 100px"
                                        @input="onQuantityChange"
                                    />
                                </div>

                                <div class="form-group">
                                    <label>Prix unitaire</label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        style="width: 100px"
                                        step="0.01"
                                        v-model="unitPrice"
                                        readonly
                                    />
                                </div>
                                <div class="form-group">
                                    <label style="opacity: 0">Action</label>
                                    <button
                                        type="button"
                                        class="btn-remove"
                                        onclick="removeProductLine(this)"
                                        style="
                                            opacity: 0.3;
                                            cursor: not-allowed;
                                        "
                                        disabled
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button
                            type="button"
                            class="btn-add-line"
                            onclick="alert('Fonctionnalité en cours de développement.')"
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
        </div>
    </main>
</template>

<script>
    export default {
        name: 'SaleComponent',

        data() {
            return {
                message: 'Bonjour depuis Vue !',
                products: [],
                seller_name: window.sellerName || '',
                selectedProductId: '',
                product: [],
                unitPrice: '',
                quantity: '',
                total: '',
                customer_name: '',
                customer_phone: '5222',
                sales: [],
                selectedPrice: '',
            };
        },
        mounted() {
            this.fetchSalesData();
        },
        methods: {
            fetchSalesData() {
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

                axios
                    .get('/userSales')
                    .then((response) => {
                        console.log(response.data);
                        this.sales = response.data;
                    })
                    .catch((error) => {
                        console.error(
                            'Erreur lors de la récupération des données :',
                            error
                        );
                    });
            },
            onProductChange() {
                axios
                    .get(`/product/${this.selectedProductId}`)
                    .then((response) => {
                        this.product = response.data;
                        console.log('Produit sélectionné:', this.product);
                        // Mettre à jour le prix unitaire dans la ligne de produit
                    })
                    .catch((error) => {
                        console.error(
                            'Erreur lors de la récupération du produit :',
                            error
                        );
                    });
            },
            updateUnitPrice() {
                this.unitPrice = parseFloat(this.selectedPrice) || 0;
            },
            onQuantityChange(event) {
                this.quantity = parseFloat(event.target.value) || 0;

                const selectedProduct = this.products.find(
                    (p) => p.id === this.selectedProductId
                );
                const availableStock = selectedProduct
                    ? selectedProduct.quantity
                    : 0;

                if (this.quantity > availableStock) {
                    alert(
                        'Stock insuffisant pour ce produit, stock disponible : ' +
                            availableStock
                    );
                    this.quantity = availableStock;
                }

                this.total = this.unitPrice * this.quantity;
            },

            submitForm() {
                if (!this.customer_name || this.customer_name.trim() === '') {
                    alert('Veuillez entrer le nom du client.');
                    return;
                }
                if (!this.quantity || this.quantity <= 0) {
                    alert('Veuillez entrer une quantité valide.');
                    return;
                }

                if (!this.selectedProductId) {
                    alert('Veuillez sélectionner un produit.');
                    return;
                }

                if (!this.unitPrice || this.unitPrice <= 0) {
                    alert('Veuillez entrer un prix unitaire valide.');
                    return;
                }

                // Préparer les données dans le format attendu
                const saleData = {
                    seller_name: this.seller_name,
                    buyer_name: this.customer_name,
                    buyer_phone: this.customer_phone,
                    products: [
                        {
                            product_id: this.selectedProductId,
                            quantity: this.quantity,
                            price: this.unitPrice,
                        },
                    ],
                };

                console.log('Données à envoyer:', saleData);

                axios
                    .post('/sale', saleData)
                    .then((response) => {
                        console.log(
                            'Vente enregistrée avec succès:',
                            response.data
                        );
                        alert('Vente enregistrée avec succès !');
                        window.location.reload();

                        // Redirection vers la page /newInvoice avec l'ID de la vente
                        // window.location.href =
                        //  '/newInvoice?sale_id=' + response.data.sale_id;
                    })
                    .catch((error) => {
                        console.error(
                            "Erreur lors de l'enregistrement de la vente :",
                            error
                        );

                        let message =
                            "Une erreur est survenue lors de l'enregistrement de la vente.";

                        if (error.response && error.response.data) {
                            const data = error.response.data;

                            if (data.error) {
                                message = data.error;
                            } else if (data.message) {
                                message = data.message;
                            }
                        }

                        alert(message);
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
            viewInvoice(sale) {
                // Code pour voir la facture
                console.log('Voir facture:', sale.id);
            },
            printInvoice(sale) {
                // Code pour imprimer la facture
                console.log('Imprimer facture:', sale.id);
            },
        },
    };
</script>
