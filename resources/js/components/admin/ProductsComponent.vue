<template>
    <div class="stock-content">
        <div class="products-header">
            <h2>Inventaire des Produits</h2>
            <div class="header-actions">
                <button class="btn btn-primary" @click="openAddProductModal()">
                    <i class="fas fa-plus"></i>
                    Ajouter un produit
                </button>

                <button @click="openPrintOptionsModal" class="btn btn-print">
                    <i class="fas fa-print"></i>
                    Imprimer la liste
                </button>

                <input
                    type="text"
                    class="search-input"
                    placeholder="Rechercher un produit..."
                    v-model="searchQuery"
                />

                <select class="filter-select" v-model="statusFilter">
                    <option>Tous les statuts</option>
                    <option>En stock (plus de 5)</option>
                    <option>Stock faible (entre 1 et 5)</option>
                    <option>Rupture de stock (stock a zero)</option>
                </select>

                <select class="filter-select" v-model="sortOption">
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
                            v-for="product in paginatedProducts"
                            :key="product.id"
                        >
                            <td data-label="Nom">
                                <strong>{{ product.name }}</strong>
                            </td>
                            <td data-label="Prix">
                                <template v-if="product.is_depositable == 0">
                                    Détail :
                                    <strong>
                                        {{ formatAmount(product.price_detail) }}
                                        FCFA
                                    </strong>
                                    <br />
                                    Semi-gros :
                                    <strong>
                                        {{
                                            formatAmount(
                                                product.price_semi_bulk
                                            )
                                        }}
                                        FCFA
                                    </strong>
                                    <br />
                                    Gros :
                                    <strong>
                                        {{ formatAmount(product.price_bulk) }}
                                        FCFA
                                    </strong>
                                </template>

                                <template v-else>
                                    Consignation :
                                    <strong>
                                        {{
                                            formatAmount(product.deposit_price)
                                        }}
                                        FCFA
                                    </strong>
                                    <br />
                                    Rechargement :
                                    <strong>
                                        {{
                                            formatAmount(product.filling_price)
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

                                    <!-- Nouveau bouton pour imprimer l'historique -->
                                    <button
                                        class="btn-sm"
                                        style="
                                            background-color: #17a2b8;
                                            color: white;
                                            border: none;
                                        "
                                        title="Imprimer l'historique"
                                        @click="printProductHistory(product.id)"
                                    >
                                        <i class="fas fa-print"></i>
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
                                        class="btn-sm"
                                        style="
                                            background-color: #8b3840ff;
                                            color: white;
                                        "
                                        title="Retirer du stock"
                                        @click="openRemoveStockModal(product)"
                                    >
                                        <i class="fas fa-minus"></i>
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

                <!-- Ajout de la pagination en bas du tableau -->
                <div
                    v-if="filteredProducts.length > 0"
                    class="pagination-container"
                >
                    <button
                        @click="currentPage--"
                        :disabled="currentPage === 1"
                        class="pagination-btn"
                    >
                        <i class="fas fa-chevron-left"></i>
                        Précédent
                    </button>
                    <span class="pagination-info">
                        Page {{ currentPage }} / {{ totalPages }}
                    </span>
                    <button
                        @click="currentPage++"
                        :disabled="currentPage === totalPages"
                        class="pagination-btn"
                    >
                        Suivant
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
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
                            <i
                                class="fas fa-arrow-left"
                                style="color: white"
                            ></i>
                        </span>
                        |
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
                            <i
                                class="fas fa-arrow-left"
                                style="color: white"
                            ></i>
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

        <!-- Modal pour les options d'impression -->
        <div
            v-if="showPrintOptionsModal"
            class="modal-add-product"
            @click.self="showPrintOptionsModal = false"
        >
            <div class="modal-content" style="padding: 2rem; max-width: 500px">
                <div
                    class="modal-header"
                    style="
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: 1.5rem;
                    "
                >
                    <h3>Options d'impression</h3>
                    <button
                        @click="showPrintOptionsModal = false"
                        class="btn-close"
                        style="
                            background: none;
                            border: none;
                            font-size: 1.5rem;
                            cursor: pointer;
                        "
                    >
                        ×
                    </button>
                </div>

                <div style="margin-bottom: 1.5rem">
                    <label
                        style="
                            display: flex;
                            align-items: center;
                            gap: 0.75rem;
                            margin-bottom: 1rem;
                            cursor: pointer;
                        "
                    >
                        <input
                            type="checkbox"
                            v-model="printOptions.showPriceDetail"
                        />
                        <span>Prix détail</span>
                    </label>
                    <label
                        style="
                            display: flex;
                            align-items: center;
                            gap: 0.75rem;
                            margin-bottom: 1rem;
                            cursor: pointer;
                        "
                    >
                        <input
                            type="checkbox"
                            v-model="printOptions.showPriceSemiBulk"
                        />
                        <span>Prix semi-gros</span>
                    </label>
                    <label
                        style="
                            display: flex;
                            align-items: center;
                            gap: 0.75rem;
                            margin-bottom: 1rem;
                            cursor: pointer;
                        "
                    >
                        <input
                            type="checkbox"
                            v-model="printOptions.showPriceBulk"
                        />
                        <span>Prix gros</span>
                    </label>
                    <label
                        style="
                            display: flex;
                            align-items: center;
                            gap: 0.75rem;
                            margin-bottom: 1rem;
                            cursor: pointer;
                        "
                    >
                        <input
                            type="checkbox"
                            v-model="printOptions.showPurchasePrice"
                        />
                        <span>Prix d'achat</span>
                    </label>
                </div>

                <div style="display: flex; gap: 1rem">
                    <button
                        @click="showPrintOptionsModal = false"
                        class="btn btn-secondary"
                        style="flex: 1"
                    >
                        Annuler
                    </button>
                    <button
                        @click="printListWithOptions"
                        class="btn btn-print"
                        style="flex: 1"
                    >
                        <i class="fas fa-print"></i>
                        Imprimer
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Ajouter Produit -->
        <div
            v-if="showAddProductModal"
            class="modal-add-product"
            @click.self="closeAddProductModal()"
        >
            <div class="modal-content" style="padding: 2.5rem; max-width: 700px">
                <div
                    class="modal-header"
                    style="
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: 2rem;
                        padding-bottom: 1rem;
                        border-bottom: 2px solid #e9ecef;
                    "
                >
                    <h3 style="margin: 0; color: #2c3e50; font-size: 1.5rem">
                        <i class="fas fa-box" style="margin-right: 0.5rem; color: #007bff"></i>
                        Ajouter un nouveau produit
                    </h3>
                    <span
                        class="close"
                        @click="closeAddProductModal"
                        style="cursor: pointer; font-size: 1.8rem; color: #6c757d"
                    >
                        &times;
                    </span>
                </div>

                <form
                    enctype="multipart/form-data"
                    method="POST"
                    @submit.prevent="addProductForm"
                >
                    <!-- Informations de base -->
                    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 10px; margin-bottom: 1.5rem">
                        <h4 style="margin: 0 0 1rem 0; color: #495057; font-size: 1.1rem; font-weight: 600">
                            <i class="fas fa-info-circle" style="margin-right: 0.5rem"></i>
                            Informations de base
                        </h4>
                        
                        <!-- Nom du produit -->
                        <div class="form-group" style="margin-bottom: 1rem">
                            <label style="font-weight: 600; color: #495057; margin-bottom: 0.5rem; display: block">
                                Nom du produit <span style="color: #dc3545">*</span>
                            </label>
                            <input
                                v-model="name"
                                type="text"
                                class="form-control"
                                placeholder="Ex: Eau minérale 1.5L"
                                required
                                style="padding: 0.75rem; border-radius: 8px; border: 1px solid #ced4da"
                            />
                        </div>

                        <!-- Prix d'achat et Quantité -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem">
                            <div class="form-group">
                                <label style="font-weight: 600; color: #495057; margin-bottom: 0.5rem; display: block">
                                    Prix d'achat (FCFA) <span style="color: #dc3545">*</span>
                                </label>
                                <input
                                    v-model="purchase_price"
                                    type="number"
                                    class="form-control"
                                    step="0.01"
                                    min="0"
                                    placeholder="0"
                                    required
                                    style="padding: 0.75rem; border-radius: 8px; border: 1px solid #ced4da"
                                />
                            </div>

                            <div class="form-group">
                                <label style="font-weight: 600; color: #495057; margin-bottom: 0.5rem; display: block">
                                    Quantité initiale <span style="color: #dc3545">*</span>
                                </label>
                                <input
                                    v-model="quantity"
                                    type="number"
                                    class="form-control"
                                    step="0.01"
                                    min="0"
                                    placeholder="0"
                                    required
                                    style="padding: 0.75rem; border-radius: 8px; border: 1px solid #ced4da"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Type de produit avec toggles visuels -->
                    <div style="background: #fff; padding: 1.5rem; border-radius: 10px; margin-bottom: 1.5rem; border: 2px solid #e9ecef">
                        <h4 style="margin: 0 0 1rem 0; color: #495057; font-size: 1.1rem; font-weight: 600">
                            <i class="fas fa-tag" style="margin-right: 0.5rem"></i>
                            Type de produit
                        </h4>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem">
                            <!-- Produit consignable -->
                            <div 
                                @click="is_depositable = is_depositable ? 0 : 1; if(is_depositable) isReturnable = false"
                                style="
                                    padding: 1rem;
                                    border-radius: 8px;
                                    cursor: pointer;
                                    border: 2px solid;
                                    transition: all 0.3s;
                                "
                                :style="{
                                    borderColor: is_depositable ? '#28a745' : '#dee2e6',
                                    background: is_depositable ? '#d4edda' : '#f8f9fa'
                                }"
                            >
                                <div style="display: flex; align-items: center; gap: 0.75rem">
                                    <div 
                                        style="
                                            width: 20px;
                                            height: 20px;
                                            border-radius: 4px;
                                            border: 2px solid;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            flex-shrink: 0;
                                        "
                                        :style="{
                                            borderColor: is_depositable ? '#28a745' : '#ced4da',
                                            background: is_depositable ? '#28a745' : 'white'
                                        }"
                                    >
                                        <i v-if="is_depositable" class="fas fa-check" style="color: white; font-size: 0.75rem"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; color: #2c3e50">
                                            <i class="fas fa-recycle" style="margin-right: 0.5rem"></i>
                                            Produit consignable
                                        </div>
                                        <div style="font-size: 0.8rem; color: #6c757d; margin-top: 0.25rem">
                                            Bouteille avec consigne
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Produit avec emballage -->
                            <div 
                                @click="isReturnable = !isReturnable; if(isReturnable) is_depositable = 0"
                                style="
                                    padding: 1rem;
                                    border-radius: 8px;
                                    cursor: pointer;
                                    border: 2px solid;
                                    transition: all 0.3s;
                                "
                                :style="{
                                    borderColor: isReturnable ? '#17a2b8' : '#dee2e6',
                                    background: isReturnable ? '#d1ecf1' : '#f8f9fa'
                                }"
                            >
                                <div style="display: flex; align-items: center; gap: 0.75rem">
                                    <div 
                                        style="
                                            width: 20px;
                                            height: 20px;
                                            border-radius: 4px;
                                            border: 2px solid;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            flex-shrink: 0;
                                        "
                                        :style="{
                                            borderColor: isReturnable ? '#17a2b8' : '#ced4da',
                                            background: isReturnable ? '#17a2b8' : 'white'
                                        }"
                                    >
                                        <i v-if="isReturnable" class="fas fa-check" style="color: white; font-size: 0.75rem"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; color: #2c3e50">
                                            <i class="fas fa-box-open" style="margin-right: 0.5rem"></i>
                                            Produit avec emballage
                                        </div>
                                        <div style="font-size: 0.8rem; color: #6c757d; margin-top: 0.25rem">
                                            Emballage retournable
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Champs de prix selon le type de produit -->
                    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 10px; margin-bottom: 1.5rem">
                        <!-- Si produit consignable -->
                        <template v-if="is_depositable">
                            <h4 style="margin: 0 0 1rem 0; color: #495057; font-size: 1.1rem; font-weight: 600">
                                <i class="fas fa-money-bill-wave" style="margin-right: 0.5rem; color: #28a745"></i>
                                Prix de consignation
                            </h4>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #495057; margin-bottom: 0.5rem; display: block">
                                        Prix de consignation <span style="color: #dc3545">*</span>
                                    </label>
                                    <input
                                        v-model="deposit_price"
                                        type="number"
                                        class="form-control"
                                        step="0.01"
                                        min="0"
                                        required
                                        placeholder="0"
                                        style="padding: 0.75rem; border-radius: 8px; border: 1px solid #ced4da"
                                    />
                                </div>

                                <div class="form-group">
                                    <label style="font-weight: 600; color: #495057; margin-bottom: 0.5rem; display: block">
                                        Prix de rechargement <span style="color: #dc3545">*</span>
                                    </label>
                                    <input
                                        v-model="filling_price"
                                        type="number"
                                        class="form-control"
                                        step="0.01"
                                        min="0"
                                        required
                                        placeholder="0"
                                        style="padding: 0.75rem; border-radius: 8px; border: 1px solid #ced4da"
                                    />
                                </div>
                            </div>

                            <!-- Nouveaux champs de bénéfices pour produits consignables -->
                            <h4 style="margin: 0 0 1rem 0; color: #495057; font-size: 1.1rem; font-weight: 600">
                                <i class="fas fa-chart-line" style="margin-right: 0.5rem; color: #ffc107"></i>
                                Bénéfices
                            </h4>
                            
                            <div style="display: grid; grid-template-columns: 1fr; gap: 1rem">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #495057; margin-bottom: 0.5rem; display: block">
                                        Bénéfice lors d'une consignation <span style="color: #dc3545">*</span>
                                    </label>
                                    <input
                                        v-model="benefit_deposit"
                                        type="number"
                                        class="form-control"
                                        step="0.01"
                                        min="0"
                                        required
                                        placeholder="0"
                                        style="padding: 0.75rem; border-radius: 8px; border: 1px solid #ced4da"
                                    />
                                </div>

                                <div class="form-group">
                                    <label style="font-weight: 600; color: #495057; margin-bottom: 0.5rem; display: block">
                                        Bénéfice lors d'un rechargement <span style="color: #dc3545">*</span>
                                    </label>
                                    <input
                                        v-model="benefit_refill"
                                        type="number"
                                        class="form-control"
                                        step="0.01"
                                        min="0"
                                        required
                                        placeholder="0"
                                        style="padding: 0.75rem; border-radius: 8px; border: 1px solid #ced4da"
                                    />
                                </div>

                                <div class="form-group">
                                    <label style="font-weight: 600; color: #495057; margin-bottom: 0.5rem; display: block">
                                        Bénéfice lors d'une consignation + rechargement <span style="color: #dc3545">*</span>
                                    </label>
                                    <input
                                        v-model="benefit_deposit_refill"
                                        type="number"
                                        class="form-control"
                                        step="0.01"
                                        min="0"
                                        required
                                        placeholder="0"
                                        style="padding: 0.75rem; border-radius: 8px; border: 1px solid #ced4da"
                                    />
                                </div>
                            </div>
                        </template>

                        <!-- Si produit non consignable -->
                        <template v-else>
                            <h4 style="margin: 0 0 1rem 0; color: #495057; font-size: 1.1rem; font-weight: 600">
                                <i class="fas fa-money-bill-wave" style="margin-right: 0.5rem; color: #007bff"></i>
                                Prix de vente
                            </h4>
                            
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem">
                                <div class="form-group">
                                    <label style="font-weight: 600; color: #495057; margin-bottom: 0.5rem; display: block">
                                        Prix détail <span style="color: #dc3545">*</span>
                                    </label>
                                    <input
                                        v-model="price_detail"
                                        type="number"
                                        class="form-control"
                                        step="0.01"
                                        min="0"
                                        required
                                        placeholder="0"
                                        style="padding: 0.75rem; border-radius: 8px; border: 1px solid #ced4da"
                                    />
                                </div>

                                <div class="form-group">
                                    <label style="font-weight: 600; color: #495057; margin-bottom: 0.5rem; display: block">
                                        Prix semi-gros <span style="color: #dc3545">*</span>
                                    </label>
                                    <input
                                        v-model="price_semi_bulk"
                                        type="number"
                                        class="form-control"
                                        step="0.01"
                                        min="0"
                                        required
                                        placeholder="0"
                                        style="padding: 0.75rem; border-radius: 8px; border: 1px solid #ced4da"
                                    />
                                </div>

                                <div class="form-group">
                                    <label style="font-weight: 600; color: #495057; margin-bottom: 0.5rem; display: block">
                                        Prix gros <span style="color: #dc3545">*</span>
                                    </label>
                                    <input
                                        v-model="price_bulk"
                                        type="number"
                                        class="form-control"
                                        step="0.01"
                                        min="0"
                                        required
                                        placeholder="0"
                                        style="padding: 0.75rem; border-radius: 8px; border: 1px solid #ced4da"
                                    />
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Boutons -->
                    <div style="display: flex; gap: 1rem; margin-top: 2rem">
                        <button
                            type="submit"
                            class="btn-primary"
                            style="
                                flex: 1;
                                padding: 0.875rem;
                                font-size: 1rem;
                                font-weight: 600;
                                border-radius: 8px;
                                background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
                                border: none;
                                color: white;
                                cursor: pointer;
                                transition: transform 0.2s;
                            "
                            onmouseover="this.style.transform='translateY(-2px)'"
                            onmouseout="this.style.transform='translateY(0)'"
                        >
                            <i class="fas fa-save" style="margin-right: 0.5rem"></i>
                            Enregistrer le produit
                        </button>
                        <button
                            type="button"
                            @click="closeAddProductModal()"
                            style="
                                flex: 0 0 140px;
                                padding: 0.875rem;
                                font-size: 1rem;
                                font-weight: 600;
                                border-radius: 8px;
                                background: #6c757d;
                                color: white;
                                border: none;
                                cursor: pointer;
                                transition: all 0.2s;
                            "
                            onmouseover="this.style.background='#5a6268'"
                            onmouseout="this.style.background='#6c757d'"
                        >
                            <i class="fas fa-times" style="margin-right: 0.5rem"></i>
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

                    <div class="form-group">
                        <label>Note (optionnel)</label>
                        <input
                            v-model="stockNote"
                            type="text"
                            class="form-control"
                            placeholder="Ex: Réapprovisionnement fournisseur..."
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

        <!-- Modal Remove Stock -->
        <div v-if="showRemoveStockModal" class="modal-remove-stock">
            <div class="modal-content">
                <h3>
                    Retirer du stock pour
                    <strong>{{ selectedProduct.name }}</strong>
                </h3>

                <form @submit.prevent="submitRemoveStockForm">
                    <div class="form-group">
                        <label>Quantité à retirer</label>
                        <input
                            v-model.number="stockQuantity"
                            type="number"
                            min="0.01"
                            step="0.01"
                            class="form-control"
                            required
                        />
                    </div>

                    <div class="form-group">
                        <label>Note (optionnel)</label>
                        <input
                            v-model="stockNote"
                            type="text"
                            class="form-control"
                            placeholder="Ex: Produit endommagé, perte..."
                        />
                    </div>

                    <div
                        class="form-actions"
                        style="margin-top: 1rem; display: flex; gap: 1rem"
                    >
                        <button type="submit" class="btn btn-primary">
                            Retirer
                        </button>
                        <button
                            type="button"
                            class="btn btn-secondary"
                            @click="closeRemoveStockModal"
                        >
                            Annuler
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
                    <!-- 🔸 Si le produit n'est PAS consignable -->
                    <template v-if="currentProduct.is_depositable != 1">
                        <div class="form-group">
                            <label>Prix détail</label>
                            <input
                                type="number"
                                class="form-control"
                                v-model.number="editedPriceDetail"
                                :placeholder="
                                    formatAmount(currentProduct.price_detail)
                                "
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
                    </template>

                    <!-- 🔸 Si le produit EST consignable -->
                    <template v-else>
                        <div class="form-group">
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

                        <div class="form-group">
                            <label>Prix de recharge</label>
                            <input
                                type="number"
                                class="form-control"
                                v-model.number="editedFillingPrice"
                                :placeholder="currentProduct.filling_price"
                                min="0"
                                step="0.01"
                            />
                        </div>
                    </template>
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
    </div>
</template>

<script>
    
    export default {
        name: 'ProductsComponent',

        data() {
            return {
                message: 'Bonjour depuis Vue !',
                products: [], // stocke les produits
                showAddProductModal: false,
                showProducts: true,
                showStock: false,
                stocks: [],
                salesDetails: [],
                transactions: [],
                showAccounting: false,

                // Champs du formulaire produit
                name: '',
                purchase_price: '',
                quantity: '',

                // Prix classiques
                price_detail: '',
                price_semi_bulk: '',
                price_bulk: '',

                // Champs consignation
                is_depositable: 0, // Produit non consignable par défaut
                deposit_price: '',
                filling_price: '', // Nouveau : prix de rechargement
                
                isReturnable: false, // Produit sans emballage par défaut (0)
                
                benefit_deposit: '', // Bénéfice lors d'une consignation
                benefit_refill: '', // Bénéfice lors d'un rechargement
                benefit_deposit_refill: '', // Bénéfice lors d'une consignation + rechargement

                // Autres états
                searchQuery: '',
                statusFilter: 'Tous les statuts',
                sortOption: 'Nom (A-Z)',
                showEditPricesModal: false,
                currentProduct: null,

                // Édition de prix
                editedPriceDetail: null,
                editedPriceSemiBulk: null,
                editedPriceBulk: null,
                editedDepositPrice: null,
                editedFillingPrice: null, // pour modification éventuelle du prix de rechargement

                // Modales diverses
                showDeleteProductModal: false,
                showAddStockModal: false,
                showRemoveStockModal: false,
                showTransactions: false,

                // Stock
                selectedProduct: {},
                selectedProductId: '',
                stockQuantity: 1,
                stockNote: '',

                // Comptabilité
                accountingData: {
                    total_quantity_sold: 0,
                    total_revenue: 0,
                    total_cost: 0,
                    profit: 0,
                },

                currentPage: 1,
                itemsPerPage: 10,
                showPrintOptionsModal: false,
                printOptions: {
                    showPriceDetail: true,
                    showPriceSemiBulk: true,
                    showPriceBulk: true,
                    showPurchasePrice: false,
                },
            };
        },

        mounted() {
            this.fetchProducts();
        },

        computed: {
            filteredProducts() {
                let filtered = this.products;

                // Filtre par recherche
                if (this.searchQuery) {
                    filtered = filtered.filter((product) =>
                        product.name
                            .toLowerCase()
                            .includes(this.searchQuery.toLowerCase())
                    );
                }

                // Filtre par statut
                if (this.statusFilter === 'En stock (plus de 5)') {
                    filtered = filtered.filter((p) => p.quantity >= 5);
                } else if (
                    this.statusFilter === 'Stock faible (entre 1 et 5)'
                ) {
                    filtered = filtered.filter(
                        (p) => p.quantity > 0 && p.quantity < 5
                    );
                } else if (
                    this.statusFilter === 'Rupture de stock (stock a zero)'
                ) {
                    filtered = filtered.filter((p) => p.quantity == 0);
                }

                // Tri
                if (this.sortOption === 'Nom (A-Z)') {
                    filtered.sort((a, b) => a.name.localeCompare(b.name));
                } else if (this.sortOption === 'Nom (Z-A)') {
                    filtered.sort((a, b) => b.name.localeCompare(a.name));
                } else if (this.sortOption === 'Prix (croissant)') {
                    filtered.sort((a, b) => {
                        const priceA =
                            a.is_depositable == 1
                                ? a.deposit_price
                                : a.price_detail;
                        const priceB =
                            b.is_depositable == 1
                                ? b.deposit_price
                                : b.price_detail;
                        return (
                            parseFloat(priceA || 0) - parseFloat(priceB || 0)
                        );
                    });
                } else if (this.sortOption === 'Prix (décroissant)') {
                    filtered.sort((a, b) => {
                        const priceA =
                            a.is_depositable == 1
                                ? a.deposit_price
                                : a.price_detail;
                        const priceB =
                            b.is_depositable == 1
                                ? b.deposit_price
                                : b.price_detail;
                        return (
                            parseFloat(priceB || 0) - parseFloat(priceA || 0)
                        );
                    });
                } else if (this.sortOption === 'Quantité') {
                    filtered.sort(
                        (a, b) =>
                            parseFloat(b.quantity || 0) -
                            parseFloat(a.quantity || 0)
                    );
                }

                return filtered;
            },

            paginatedProducts() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                const end = start + this.itemsPerPage;
                return this.filteredProducts.slice(start, end);
            },

            totalPages() {
                return Math.ceil(
                    this.filteredProducts.length / this.itemsPerPage
                );
            },
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

            openPrintOptionsModal() {
                this.showPrintOptionsModal = true;
            },

            printListWithOptions() {
                const printWindow = window.open('', '_blank');

                const anyPriceSelected =
                    this.printOptions.showPriceDetail ||
                    this.printOptions.showPriceSemiBulk ||
                    this.printOptions.showPriceBulk ||
                    this.printOptions.showPurchasePrice;

                const totalValue = this.filteredProducts.reduce(
                    (sum, product) => {
                        let productValue = 0;

                        // If the product is depositary
                        if (product.is_depositable == 1) {
                            // We use the deposit price
                            productValue =
                                parseFloat(product.deposit_price || 0) *
                                parseFloat(product.quantity || 0);
                        } else {
                            // Otherwise, we use the detail price
                            productValue =
                                parseFloat(product.price_detail || 0) *
                                parseFloat(product.quantity || 0);
                        }

                        return sum + productValue;
                    },
                    0
                );

                let tableHeaders =
                    '<tr><th style="padding: 12px; border: 1px solid #ddd;">#</th><th style="padding: 12px; border: 1px solid #ddd;">Nom du produit</th>';

                if (this.printOptions.showPriceDetail) {
                    tableHeaders +=
                        '<th style="padding: 12px; border: 1px solid #ddd; text-align: right;">Prix détail</th>';
                }
                if (this.printOptions.showPriceSemiBulk) {
                    tableHeaders +=
                        '<th style="padding: 12px; border: 1px solid #ddd; text-align: right;">Prix semi-gros</th>';
                }
                if (this.printOptions.showPriceBulk) {
                    tableHeaders +=
                        '<th style="padding: 12px; border: 1px solid #ddd; text-align: right;">Prix gros</th>';
                }
                if (this.printOptions.showPurchasePrice) {
                    tableHeaders +=
                        '<th style="padding: 12px; border: 1px solid #ddd; text-align: right;">Prix d\'achat</th>';
                }

                tableHeaders +=
                    '<th style="padding: 12px; border: 1px solid #ddd; text-align: center;">Quantité</th><th style="padding: 12px; border: 1px solid #ddd; text-align: center;">Statut</th>';
                if (anyPriceSelected) {
                    tableHeaders +=
                        '<th style="padding: 12px; border: 1px solid #ddd; text-align: right;">Valeur totale</th>';
                }
                tableHeaders += '</tr>';

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

                    let priceToUse =
                        product.is_depositable == 1
                            ? parseFloat(product.deposit_price || 0)
                            : parseFloat(product.price_detail || 0);
                    let productTotalValue =
                        priceToUse * parseFloat(product.quantity || 0);

                    let row = `<tr><td style="padding: 12px; border: 1px solid #ddd;">${
                        index + 1
                    }</td><td style="padding: 12px; border: 1px solid #ddd;"><strong>${
                        product.name
                    }</strong></td>`;

                    if (
                        this.printOptions.showPriceDetail &&
                        product.is_depositable == 0
                    ) {
                        row += `<td style="padding: 12px; border: 1px solid #ddd; text-align: right;">${this.formatAmount(
                            product.price_detail
                        )} FCFA</td>`;
                    } else if (this.printOptions.showPriceDetail) {
                        row += `<td style="padding: 12px; border: 1px solid #ddd; text-align: right;">-</td>`;
                    }

                    if (
                        this.printOptions.showPriceSemiBulk &&
                        product.is_depositable == 0
                    ) {
                        row += `<td style="padding: 12px; border: 1px solid #ddd; text-align: right;">${this.formatAmount(
                            product.price_semi_bulk
                        )} FCFA</td>`;
                    } else if (this.printOptions.showPriceSemiBulk) {
                        row += `<td style="padding: 12px; border: 1px solid #ddd; text-align: right;">-</td>`;
                    }

                    if (
                        this.printOptions.showPriceBulk &&
                        product.is_depositable == 0
                    ) {
                        row += `<td style="padding: 12px; border: 1px solid #ddd; text-align: right;">${this.formatAmount(
                            product.price_bulk
                        )} FCFA</td>`;
                    } else if (this.printOptions.showPriceBulk) {
                        row += `<td style="padding: 12px; border: 1px solid #ddd; text-align: right;">-</td>`;
                    }

                    if (this.printOptions.showPurchasePrice) {
                        row += `<td style="padding: 12px; border: 1px solid #ddd; text-align: right;">${this.formatAmount(
                            product.purchase_price
                        )} FCFA</td>`;
                    }

                    row += `<td style="padding: 12px; border: 1px solid #ddd; text-align: center;">${product.quantity}</td>
                        <td style="padding: 12px; border: 1px solid #ddd; text-align: center;">
                            <span style="padding: 4px 12px; border-radius: 12px; background: ${statusColor}; font-size: 0.85rem; font-weight: 600;">
                                ${statusText}
                            </span>
                        </td>`;

                    if (anyPriceSelected) {
                        row += `<td style="padding: 12px; border: 1px solid #ddd; text-align: right;">${this.formatAmount(
                            productTotalValue
                        )} FCFA</td>`;
                    }
                    row += '</tr>';

                    tableRows += row;
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
                         <h1>SAGER MARKET</h1>
                            <p style="margin: auto; text-align: center;"><strong>Téléphone:</strong> +229 0196466625</p>
                            <p style="margin: auto; text-align: center;"><strong>IFU:</strong> 0202586942320</p>
                            <h2 style="margin: auto; text-align: center;">Inventaire des produits</h2>
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
                                ${tableHeaders}
                            </thead>
                            <tbody>
                                ${tableRows}
                                ${
                                    anyPriceSelected
                                        ? `<tr class="total-row">
                                    <td colspan="${this.getColspanForTotal()}" style="padding: 12px; border: 1px solid #ddd; text-align: right;">VALEUR TOTALE DE L'INVENTAIRE:</td>
                                    <td style="padding: 12px; border: 1px solid #ddd; text-align: right;">${this.formatAmount(
                                        totalValue
                                    )} FCFA</td>
                                </tr>`
                                        : ''
                                }
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
                this.showPrintOptionsModal = false;
            },

            getColspanForTotal() {
                let colspan = 2; // Numéro + Nom
                if (this.printOptions.showPriceDetail) colspan++;
                if (this.printOptions.showPriceSemiBulk) colspan++;
                if (this.printOptions.showPriceBulk) colspan++;
                if (this.printOptions.showPurchasePrice) colspan++;
                colspan += 2; // Quantité + Statut
                return colspan;
            },

            printList() {
                // This method is now less used and the behavior is handled by printListWithOptions
                const printWindow = window.open('', '_blank');

                const totalValue = this.filteredProducts.reduce(
                    (sum, product) => {
                        let productValue = 0;

                        // If the product is depositary
                        if (product.is_depositable == 1) {
                            // We use the deposit price
                            productValue =
                                parseFloat(product.deposit_price || 0) *
                                parseFloat(product.quantity || 0);
                        } else {
                            // Otherwise, we use the detail price
                            productValue =
                                parseFloat(product.price_detail || 0) *
                                parseFloat(product.quantity || 0);
                        }

                        return sum + productValue;
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

                    let priceToUse =
                        product.is_depositable == 1
                            ? parseFloat(product.deposit_price || 0)
                            : parseFloat(product.price_detail || 0);
                    let productTotalValue =
                        priceToUse * parseFloat(product.quantity || 0);

                    tableRows += `
                        <tr>
                            <td style="padding: 12px; border: 1px solid #ddd;">${
                                index + 1
                            }</td>
                            <td style="padding: 12px; border: 1px solid #ddd;"><strong>${
                                product.name
                            }</strong></td>
                            <td style="padding: 12px; border: 1px solid #ddd; text-align: right;">${this.formatAmount(
                                priceToUse
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
                                productTotalValue
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
                         <h1>SAGER MARKET</h1>
                            <p><strong>Téléphone:</strong> +229 0196466625</p>
                            <p><strong>IFU:</strong> 0202586942320</p>
                            <h2>Inventaire des produits</h2>
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
                // Reset all form fields when opening the modal
                this.name = '';
                this.purchase_price = '';
                this.quantity = '';
                this.price_detail = '';
                this.price_semi_bulk = '';
                this.price_bulk = '';
                this.is_depositable = 0;
                this.isReturnable = false;
                this.deposit_price = '';
                this.filling_price = '';
                this.benefit_deposit = '';
                this.benefit_refill = '';
                this.benefit_deposit_refill = '';

                this.showAddProductModal = true;
            },
            closeAddProductModal() {
                this.showAddProductModal = false;
            },
            submitForm() {
                let productData = {};

                // If the product is depositary → update only the two fields
                if (this.currentProduct.is_depositable == 1) {
                    productData = {
                        deposit_price: this.editedDepositPrice,
                        filling_price: this.editedFillingPrice,
                    };
                } else {
                    // Otherwise, update the classic prices
                    productData = {
                        price_detail: this.editedPriceDetail,
                        price_semi_bulk: this.editedPriceSemiBulk,
                        price_bulk: this.editedPriceBulk,
                    };
                }

                console.log('Data to send:', productData);

                axios
                    .put(`/products/${this.currentProduct.id}`, productData)
                    .then((response) => {
                        console.log('Price updated', response.data);
                        this.closeEditPricesModal();
                    })
                    .catch((error) => {
                        console.error(
                            'Update error:',
                            error.response?.data || error
                        );
                    });
            },

            openEditPricesModal(product) {
                this.currentProduct = product;
                this.editedPriceDetail = product.price_detail;
                this.editedPriceSemiBulk = product.price_semi_bulk;
                this.editedPriceBulk = product.price_bulk;
                this.editedDepositPrice = product.deposit_price;
                this.editedFillingPrice = product.filling_price;
                this.showEditPricesModal = true;
            },
            closeEditPricesModal() {
                this.showEditPricesModal = false;
                this.currentProduct = null;
                this.editedPriceDetail = null;
                this.editedPriceSemiBulk = null;
                this.editedPriceBulk = null;
                this.editedDepositPrice = null;
                this.editedFillingPrice = null;
            },
            savePrice() {
                if (this.currentProduct) {
                    const productId = this.currentProduct.id;

                    // ✅ Prepare the payload dynamically
                    let payload = {};

                    if (this.currentProduct.is_depositable == 1) {
                        // If the product is depositary → only deposit_price and filling_price
                        payload = {
                            deposit_price: this.editedDepositPrice,
                            filling_price: this.editedFillingPrice,
                        };
                    } else {
                        // Otherwise → classic prices + filling_price
                        payload = {
                            price_detail: this.editedPriceDetail,
                            price_semi_bulk: this.editedPriceSemiBulk,
                            price_bulk: this.editedPriceBulk,
                            filling_price: this.editedFillingPrice,
                        };
                    }

                    axios
                        .post(`/products/${productId}/update-prices`, payload)
                        .then(() => {
                            alert('Prix mis à jour avec succès.');

                            // ✅ Update the current object locally
                            if (this.currentProduct.is_depositable == 1) {
                                this.currentProduct.deposit_price =
                                    this.editedDepositPrice;
                                this.currentProduct.filling_price =
                                    this.editedFillingPrice;
                            } else {
                                this.currentProduct.price_detail =
                                    this.editedPriceDetail;
                                this.currentProduct.price_semi_bulk =
                                    this.editedPriceSemiBulk;
                                this.currentProduct.price_bulk =
                                    this.editedPriceBulk;
                                this.currentProduct.filling_price =
                                    this.editedFillingPrice;
                            }

                            // ✅ Reset and close the modal
                            this.showEditPricesModal = false;
                            this.currentProduct = null;
                            this.editedPriceDetail = null;
                            this.editedPriceSemiBulk = null;
                            this.editedPriceBulk = null;
                            this.editedDepositPrice = null;
                            this.editedFillingPrice = null;
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

                formData.append('is_depositable', this.is_depositable ? 1 : 0);
                formData.append('isReturnable', this.isReturnable ? 1 : 0);
                
                if (this.is_depositable) {
                    formData.append('deposit_price', this.deposit_price);
                    formData.append('filling_price', this.filling_price);
                    formData.append('benefit_deposit', this.benefit_deposit);
                    formData.append('benefit_refill', this.benefit_refill);
                    formData.append('benefit_deposit_refill', this.benefit_deposit_refill);
                }

                // 🔍 Displaying the sent content
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
                        this.fetchProducts(); // Refresh the product list
                        this.closeAddProductModal(); // Close the modal after adding

                        this.name = '';
                        this.purchase_price = '';
                        this.quantity = '';
                        this.price_detail = '';
                        this.price_semi_bulk = '';
                        this.price_bulk = '';
                        this.is_depositable = 0;
                        this.deposit_price = '';
                        this.filling_price = '';
                        this.isReturnable = false;
                        this.benefit_deposit = '';
                        this.benefit_refill = '';
                        this.benefit_deposit_refill = '';
                    })
                    .catch((error) => {
                        console.error('Erreur lors de l\'ajout du produit:', error);
                        alert('Erreur lors de l\'ajout du produit.');
                    });
            },
            
            handleReturnableChange() {
                // If "with packaging" is checked, ensure "depositable" is set to No
                if (this.isReturnable) {
                    this.is_depositable = 0;
                }
            },

            openAddStockModal(product) {
                this.selectedProduct = product;
                this.selectedProductId = product.id;
                this.stockQuantity = 1;
                this.stockNote = '';
                this.showAddStockModal = true;
            },

            closeAddStockModal() {
                this.showAddStockModal = false;
                this.selectedProduct = {};
                this.stockQuantity = 0;
                this.stockNote = '';
            },

            submitAddStockForm() {
                // Displaying the data sent
                console.log('Données envoyées pour la mise à jour du stock:', {
                    productId: this.selectedProductId,
                    quantity: this.stockQuantity,
                    note: this.stockNote,
                });

                axios
                    .post(`/products/${this.selectedProductId}/add-stock`, {
                        quantity: this.stockQuantity,
                        note: this.stockNote,
                    })
                    .then((response) => {
                        alert('Stock ajouté avec succès !');
                        this.fetchProducts(); // Refresh the product list
                        this.closeAddStockModal(); // Close the modal
                    })
                    .catch((error) => {
                        alert('Erreur lors de l’ajout du stock.');
                        console.error(error);
                    });
            },

            openRemoveStockModal(product) {
                this.selectedProduct = product;
                this.selectedProductId = product.id;
                this.stockQuantity = 1;
                this.stockNote = '';
                this.showRemoveStockModal = true;
            },

            closeRemoveStockModal() {
                this.showRemoveStockModal = false;
                this.selectedProduct = {};
                this.stockQuantity = 0;
                this.stockNote = '';
            },

            submitRemoveStockForm() {
                axios
                    .post(`/products/${this.selectedProductId}/remove-stock`, {
                        quantity: this.stockQuantity,
                        note: this.stockNote,
                    })
                    .then((response) => {
                        alert('Stock retiré avec succès !');
                        this.fetchProducts();
                        this.closeRemoveStockModal();
                    })
                    .catch((error) => {
                        alert('Erreur lors du retrait du stock.');
                        console.error(error);
                    });
            },

            // Method to print product history (added)
            printProductHistory(productId) {
                axios
                    .get(`/stock/${productId}`) // Ensure this route returns stock history data
                    .then((response) => {
                        const stocks = response.data;
                        const product = this.products.find(
                            (p) => p.id === productId
                        ); // Find the corresponding product

                        if (!stocks || stocks.length === 0 || !product) {
                            alert(
                                'Aucun historique de stock trouvé pour ce produit.'
                            );
                            return;
                        }

                        const printWindow = window.open('', '_blank');
                        const htmlContent = `
                            <!DOCTYPE html>
                            <html>
                            <head>
                                <title>Historique du Stock - ${
                                    product.name
                                }</title>
                                <style>
                                    body { font-family: Arial, sans-serif; margin: 20px; }
                                    h1 { text-align: center; color: #333; margin-bottom: 20px; }
                                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                                    th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
                                    th { background-color: #f2f2f2; }
                                    .product-info { margin-bottom: 20px; }
                                    @media print {
                                        button { display: none; }
                                    }
                                </style>
                            </head>
                            <body>
                                <h1>Historique du Stock pour : ${
                                    product.name
                                }</h1>
                                <div class="product-info">
                                    <p><strong>Date d'impression :</strong> ${new Date().toLocaleString(
                                        'fr-FR'
                                    )}</p>
                                </div>
                                <table>
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
                                        ${stocks
                                            .map(
                                                (stock) => `
                                            <tr>
                                                <td>${this.formatDateTime(
                                                    stock.created_at
                                                )}</td>
                                                <td>${stock.initial_stock}</td>
                                                <td>${stock.label}</td>
                                                <td>${stock.quantity}</td>
                                                <td>${stock.final_stock}</td>
                                            </tr>
                                        `
                                            )
                                            .join('')}
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
                    })
                    .catch((error) => {
                        console.error(
                            "Erreur lors de la récupération de l'historique:",
                            error
                        );
                        alert("Impossible de charger l'historique du stock.");
                    });
            },
        },
    };
</script>

<style scoped>
    .stock-content {
        padding: 20px;
    }

    .products-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .products-header h2 {
        margin: 0;
        color: #333;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: background-color 0.2s ease;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
        border: none;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .btn-print {
        background-color: #17a2b8;
        color: white;
        border: none;
    }

    .btn-print:hover {
        background-color: #117a8b;
    }

    .search-input {
        padding: 8px 12px;
        border: 1px solid #ccc;
        border-radius: 5px;
        min-width: 200px;
    }

    .filter-select {
        padding: 8px 12px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: white;
    }

    .showProducts,
    .showStock,
    .showAccounting {
        margin-top: 20px;
    }

    .table-container {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        overflow-x: auto; /* Pour permettre le scroll horizontal sur petits écrans */
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }

    .table-header h3 {
        margin: 0;
        color: #333;
        font-size: 1.25rem;
    }

    .table-header strong {
        color: #555;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th,
    .table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #eee;
        vertical-align: middle; /* Centre le contenu verticalement */
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #555;
        text-transform: uppercase;
        font-size: 0.85rem;
    }

    .table tbody tr:hover {
        background-color: #f1f1f1;
    }

    .action-buttons {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }

    .btn-sm {
        padding: 5px 10px;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        font-size: 0.8rem;
    }

    .btn-edit {
        background-color: #ffc107;
        color: white;
    }

    .btn-edit:hover {
        background-color: #e0a800;
    }

    .btn-delete {
        background-color: #dc3545;
        color: white;
    }

    .btn-delete:hover {
        background-color: #c82333;
    }

    .btn-success {
        background-color: #28a745;
        color: white;
    }

    .btn-success:hover {
        background-color: #218838;
    }

    .pagination-container {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-top: 20px;
        gap: 10px;
    }

    .pagination-btn {
        padding: 8px 15px;
        border: 1px solid #007bff;
        background-color: white;
        color: #007bff;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .pagination-btn:hover:not(:disabled) {
        background-color: #007bff;
        color: white;
    }

    .pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .pagination-info {
        font-weight: 600;
        color: #555;
    }

    /* Modales */
    .modal-add-product,
    .modal-add-stock,
    .modal-remove-stock,
    .modal-edit-prices,
    .modal-delete-product {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal-content {
        background: white;
        padding: 20px;
        border-radius: 10px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        position: relative;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 1.3rem;
        color: #333;
    }

    .close {
        font-size: 1.8rem;
        color: #aaa;
        cursor: pointer;
        transition: color 0.2s ease;
    }

    .close:hover {
        color: #777;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #555;
    }

    .form-control {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 1rem;
        box-sizing: border-box; /* Important pour que padding n'affecte pas la largeur */
    }

    .form-control:focus {
        border-color: #007bff;
        outline: none;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
    }

    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
    }

    /* Styles for specific modal elements */
    .modal-edit-prices .modal-body,
    .modal-delete-product .modal-body {
        padding: 10px 0;
    }

    .modal-edit-prices .form-group,
    .modal-delete-product .modal-body p {
        margin-bottom: 15px;
    }

    .modal-edit-prices .btn-primary,
    .modal-delete-product button {
        padding: 10px 20px;
    }

    .modal-edit-prices button:last-child,
    .modal-delete-product button:last-child {
        background-color: #6c757d;
        color: white;
        border: none;
    }

    .modal-edit-prices button:last-child:hover,
    .modal-delete-product button:last-child:hover {
        background-color: #5a6268;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .products-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .header-actions {
            flex-direction: column;
            width: 100%;
        }

        .search-input,
        .filter-select,
        .btn {
            width: 100%;
            margin-bottom: 10px;
        }

        .table-container {
            overflow-x: auto;
        }

        .table thead {
            display: none; /* Hide table header on small screens */
        }

        .table,
        .table tbody,
        .table tr,
        .table td {
            display: block;
            width: 100%;
        }

        .table td {
            text-align: right;
            padding-left: 50%;
            position: relative;
            border-bottom: 1px solid #ccc; /* Add a border to separate rows */
        }

        .table td::before {
            content: attr(data-label);
            position: absolute;
            left: 10px;
            width: calc(50% - 20px);
            padding-right: 10px;
            white-space: nowrap;
            text-align: left;
            font-weight: bold;
            color: #333;
        }

        .action-buttons {
            justify-content: flex-end; /* Align action buttons to the right */
        }

        .pagination-container {
            justify-content: center;
        }
    }
</style>
