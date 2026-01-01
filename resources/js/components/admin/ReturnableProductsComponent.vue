<template>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <main class="main-content" id="app">
        <div class="returnables-content">
            <!-- Header Section -->
            <div class="header-section">
                <div class="section-header">
                    <h2 style="margin: 0; color: #333">
                        <i class="fas fa-cube"></i>
                        Gestion des Produits avec Emballages
                    </h2>
                    <p
                        style="
                            margin: 0.5rem 0 0 0;
                            color: #666;
                            font-size: 0.9rem;
                        "
                    >
                        Remise et retour des produits consignés
                    </p>
                </div>

                <!-- Statistics Dashboard -->
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon">📦</div>
                        <div class="stat-number">{{ totalGiven }}</div>
                        <div class="stat-label">Total Remis</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">↩️</div>
                        <div class="stat-number">{{ totalReturned }}</div>
                        <div class="stat-label">Total Retournés</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">⏳</div>
                        <div class="stat-number">{{ totalPending }}</div>
                        <div class="stat-label">En Attente</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">📊</div>
                        <div class="stat-number">{{ activeTransactions }}</div>
                        <div class="stat-label">Opérations Actives</div>
                    </div>
                </div>
            </div>

            <!-- Controls Section -->
            <div class="controls-section">
                <div class="search-filters">
                    <div class="search-box">
                        <input
                            type="text"
                            v-model="searchQuery"
                            placeholder="Rechercher par client ou produit..."
                            class="search-input"
                        />
                        <i class="fas fa-search"></i>
                    </div>

                    <div class="filters-group">
                        <select v-model="filterClient" class="select-filter">
                            <option value="">Tous les clients</option>
                            <option
                                v-for="client in uniqueClients"
                                :key="client.id"
                                :value="client.id"
                            >
                                {{ client.name }}
                            </option>
                        </select>

                        <select v-model="filterStatus" class="select-filter">
                            <option value="">Tous les statuts</option>
                            <option value="pending">Non retourné</option>
                            <option value="partial">
                                Partiellement retourné
                            </option>
                            <option value="complete">
                                Complètement retourné
                            </option>
                        </select>

                        <select v-model="sortOption" class="select-filter">
                            <option>Date (récent)</option>
                            <option>Date (ancien)</option>
                            <option>Client (A-Z)</option>
                            <option>Quantité restante (croissant)</option>
                            <option>Quantité restante (décroissant)</option>
                        </select>
                    </div>
                </div>

                <div class="action-buttons">
                    <button
                        @click="openCreateTransactionModal"
                        class="btn-primary"
                    >
                        <i class="fas fa-plus-circle"></i>
                        Nouvelle Remise
                    </button>
                    <button @click="printAll" class="btn-secondary">
                        <i class="fas fa-print"></i>
                        Imprimer Tous
                    </button>
                </div>
            </div>

            <!-- Transactions List View -->
            <div class="table-container">
                <div class="table-wrapper">
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Référence</th>
                                <th>Date Remise</th>
                                <th>Produits</th>
                                <th>Qté Remise</th>
                                <th>Qté Retournée</th>
                                <th>En Attente</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="transaction in paginatedTransactions"
                                :key="transaction.id"
                                :class="getTransactionRowClass(transaction)"
                            >
                                <td data-label="Client">
                                    <strong>
                                        {{ transaction.client_name }}
                                    </strong>
                                </td>
                                <td data-label="Référence" class="reference">
                                    {{ transaction.reference }}
                                </td>
                                <td data-label="Date">
                                    {{ formatDate(transaction.date) }}
                                </td>
                                <td data-label="Produits">
                                    <span class="product-count-badge">
                                        {{
                                            getProductCountForTransaction(
                                                transaction.id
                                            )
                                        }}
                                        produit(s)
                                    </span>
                                </td>
                                <td data-label="Qté Remise" class="qty-center">
                                    {{ getTotalQuantityGiven(transaction.id) }}
                                </td>
                                <td
                                    data-label="Qté Retournée"
                                    class="qty-center"
                                >
                                    {{
                                        getTotalQuantityReturned(transaction.id)
                                    }}
                                </td>
                                <td
                                    data-label="En Attente"
                                    class="qty-center pending-qty"
                                >
                                    {{
                                        getTotalQuantityPending(transaction.id)
                                    }}
                                </td>
                                <td data-label="Statut">
                                    <span
                                        :style="
                                            getTransactionStatusBadgeStyle(
                                                transaction
                                            )
                                        "
                                    >
                                        {{
                                            getTransactionStatusText(
                                                transaction
                                            )
                                        }}
                                    </span>
                                </td>
                                <td data-label="Actions" class="actions-cell">
                                    <button
                                        @click="openDetailsModal(transaction)"
                                        class="btn-small btn-eye"
                                        title="Voir détails"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button
                                        @click="
                                            openRecordReturnModal(transaction)
                                        "
                                        class="btn-small btn-reply"
                                        title="Enregistrer retour"
                                    >
                                        <i class="fas fa-reply"></i>
                                    </button>
                                    <button
                                        @click="printTransaction(transaction)"
                                        class="btn-small btn-print"
                                        title="Imprimer"
                                    >
                                        <i class="fas fa-print"></i>
                                    </button>
                                    <button
                                        @click="openDeleteModal(transaction)"
                                        class="btn-small btn-delete"
                                        title="Supprimer"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr
                                v-if="paginatedTransactions.length === 0"
                                class="empty-row"
                            >
                                <td
                                    colspan="9"
                                    style="
                                        text-align: center;
                                        padding: 2rem;
                                        color: #999;
                                    "
                                >
                                    <i
                                        class="fas fa-inbox"
                                        style="
                                            font-size: 2rem;
                                            margin-bottom: 0.5rem;
                                        "
                                    ></i>
                                    <p>Aucune opération trouvée</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination" v-if="totalPages > 1">
                    <button
                        @click="currentPage = Math.max(1, currentPage - 1)"
                        :disabled="currentPage === 1"
                        class="btn-pagination"
                    >
                        <i class="fas fa-chevron-left"></i>
                        Précédent
                    </button>
                    <span class="pagination-info">
                        Page {{ currentPage }} / {{ totalPages }}
                    </span>
                    <button
                        @click="
                            currentPage = Math.min(totalPages, currentPage + 1)
                        "
                        :disabled="currentPage === totalPages"
                        class="btn-pagination"
                    >
                        Suivant
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>

            <!-- Modal: Nouvelle Remise de Produits -->
            <div
                v-if="showCreateModal"
                class="modal-overlay"
                @click.self="closeCreateModal"
            >
                <div class="modal-container modal-large">
                    <div class="modal-header">
                        <h5>
                            <i class="fas fa-plus-circle"></i>
                            Nouvelle Remise de Produits avec Emballages
                        </h5>
                        <button @click="closeCreateModal" class="modal-close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="submitNewTransaction">
                            <!-- Client Selection -->
                            <div class="form-section">
                                <h4 style="margin-bottom: 1rem; color: #333">
                                    Information Client
                                </h4>
                                <div class="form-group">
                                    <label>Sélectionner le client *</label>
                                    <div class="customer-search-container">
                                        <input
                                            type="text"
                                            class="form-control"
                                            v-model="
                                                newTransaction.customerSearch
                                            "
                                            @input="
                                                filterNewTransactionCustomers
                                            "
                                            @focus="
                                                showNewCustomerDropdown = true
                                            "
                                            placeholder="Rechercher un client..."
                                            required
                                        />
                                        <div
                                            v-if="
                                                showNewCustomerDropdown &&
                                                filteredNewCustomers.length > 0
                                            "
                                            class="customer-dropdown"
                                        >
                                            <div
                                                v-for="client in filteredNewCustomers"
                                                :key="client.id"
                                                @click="
                                                    selectNewTransactionCustomer(
                                                        client
                                                    )
                                                "
                                                class="customer-dropdown-item"
                                            >
                                                <div class="customer-item-name">
                                                    {{ client.name }}
                                                </div>
                                                <div
                                                    class="customer-item-phone"
                                                >
                                                    {{ client.phone }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    v-if="newTransaction.selectedClient"
                                    class="selected-client-info"
                                >
                                    <div class="info-item">
                                        <span class="info-label">Client :</span>
                                        <span class="info-value">
                                            {{
                                                newTransaction.selectedClient
                                                    .name
                                            }}
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">
                                            Téléphone :
                                        </span>
                                        <span class="info-value">
                                            {{
                                                newTransaction.selectedClient
                                                    .phone
                                            }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Products Selection -->
                            <div
                                class="form-section"
                                v-if="newTransaction.selectedClient"
                            >
                                <h4 style="margin-bottom: 1rem; color: #333">
                                    Sélection des Produits avec Emballages
                                </h4>

                                <div class="products-list">
                                    <div
                                        v-for="(
                                            line, index
                                        ) in newTransaction.productLines"
                                        :key="index"
                                        class="product-line-form"
                                    >
                                        <div class="product-line-header">
                                            <span class="line-number">
                                                Produit {{ index + 1 }}
                                            </span>
                                            <button
                                                v-if="index > 0"
                                                type="button"
                                                @click="
                                                    removeProductLine(index)
                                                "
                                                class="btn-remove-line"
                                            >
                                                <i class="fas fa-times"></i>
                                                Supprimer
                                            </button>
                                        </div>

                                        <!-- Product Selection with Search -->
                                        <div class="form-group">
                                            <label>
                                                Produit avec emballage *
                                            </label>
                                            <div
                                                class="product-search-container"
                                            >
                                                <input
                                                    type="text"
                                                    class="form-control product-search-input"
                                                    v-model="line.searchQuery"
                                                    @input="
                                                        onProductSearch(index)
                                                    "
                                                    @focus="
                                                        line.showDropdown = true
                                                    "
                                                    placeholder="Rechercher un produit..."
                                                    required
                                                />
                                                <div
                                                    v-if="
                                                        line.showDropdown &&
                                                        getFilteredReturnableProducts(
                                                            index
                                                        ).length > 0
                                                    "
                                                    class="product-dropdown"
                                                >
                                                    <div
                                                        v-for="product in getFilteredReturnableProducts(
                                                            index
                                                        )"
                                                        :key="product.id"
                                                        @click="
                                                            selectProductForLine(
                                                                index,
                                                                product
                                                            )
                                                        "
                                                        class="product-dropdown-item"
                                                    >
                                                        <div
                                                            class="product-item-name"
                                                        >
                                                            {{ product.name }}
                                                        </div>
                                                        <div
                                                            class="product-item-stock"
                                                        >
                                                            Stock:
                                                            {{
                                                                product.quantity
                                                            }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Quantity -->
                                        <div
                                            class="form-group"
                                            v-if="line.selectedProduct"
                                        >
                                            <label>Quantité à remettre *</label>
                                            <div class="quantity-input-group">
                                                <input
                                                    type="number"
                                                    class="form-control quantity-input"
                                                    v-model.number="
                                                        line.quantity
                                                    "
                                                    min="0.01"
                                                    step="0.01"
                                                    :max="
                                                        line.selectedProduct
                                                            .quantity
                                                    "
                                                    placeholder="Quantité"
                                                    required
                                                />
                                                <span class="quantity-info">
                                                    Stock disponible:
                                                    {{
                                                        line.selectedProduct
                                                            .quantity
                                                    }}
                                                </span>
                                            </div>
                                        </div>

                                        <hr
                                            v-if="
                                                index <
                                                newTransaction.productLines
                                                    .length -
                                                    1
                                            "
                                            style="
                                                margin: 1.5rem 0;
                                                border: none;
                                                border-top: 1px solid #ddd;
                                            "
                                        />
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    @click="addProductLine"
                                    class="btn-add-line"
                                >
                                    <i class="fas fa-plus"></i>
                                    Ajouter un produit
                                </button>
                            </div>

                            <!-- Comment -->
                            <div
                                class="form-section"
                                v-if="newTransaction.selectedClient"
                            >
                                <div class="form-group">
                                    <label>Commentaire</label>
                                    <textarea
                                        v-model="newTransaction.comment"
                                        class="form-control"
                                        rows="3"
                                        placeholder="Remarques supplémentaires..."
                                    ></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button @click="closeCreateModal" class="btn-secondary">
                            <i class="fas fa-times"></i>
                            Annuler
                        </button>
                        <button
                            @click="submitNewTransaction"
                            :disabled="!canSubmitNewTransaction"
                            class="btn-primary"
                        >
                            <i class="fas fa-save"></i>
                            Enregistrer la Remise
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal: Détails Complets de la Transaction -->
            <div
                v-if="showDetailsModal"
                class="modal-overlay"
                @click.self="closeDetailsModal"
            >
                <div class="modal-container modal-large">
                    <div class="modal-header">
                        <h5>
                            <i class="fas fa-file-invoice-dollar"></i>
                            Détails de la Remise -
                            {{ selectedTransaction?.reference }}
                        </h5>
                        <button @click="closeDetailsModal" class="modal-close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body" v-if="selectedTransaction">
                        <!-- Header Info -->
                        <div class="details-header">
                            <div class="detail-group">
                                <div class="detail-item">
                                    <span class="detail-label">Client :</span>
                                    <span class="detail-value">
                                        {{ selectedTransaction.client_name }}
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">
                                        Référence :
                                    </span>
                                    <span class="detail-value">
                                        {{ selectedTransaction.reference }}
                                    </span>
                                </div>
                            </div>
                            <div class="detail-group">
                                <div class="detail-item">
                                    <span class="detail-label">Date :</span>
                                    <span class="detail-value">
                                        {{
                                            formatDate(selectedTransaction.date)
                                        }}
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">
                                        Commentaire :
                                    </span>
                                    <span class="detail-value">
                                        {{
                                            selectedTransaction.comment || 'N/A'
                                        }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Products Table -->
                        <div class="details-table-section">
                            <h4 style="margin-bottom: 1rem; color: #333">
                                Produits Remis
                            </h4>
                            <table class="details-table">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th>Remis</th>
                                        <th>Retourné</th>
                                        <th>En Attente</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="product in getProductsForTransaction(
                                            selectedTransaction.id
                                        )"
                                        :key="product.id"
                                    >
                                        <td>
                                            {{
                                                getProductName(
                                                    product.product_id
                                                )
                                            }}
                                        </td>
                                        <td class="qty-center">
                                            {{ product.quantity_given }}
                                        </td>
                                        <td class="qty-center">
                                            {{ product.quantity_returned }}
                                        </td>
                                        <td class="qty-center pending-qty">
                                            {{
                                                product.quantity_given -
                                                product.quantity_returned
                                            }}
                                        </td>
                                        <td>
                                            <span
                                                :style="
                                                    getProductStatusBadgeStyle(
                                                        product
                                                    )
                                                "
                                            >
                                                {{
                                                    getProductStatusText(
                                                        product
                                                    )
                                                }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Returns History -->
                        <div
                            class="details-table-section"
                            v-if="
                                getReturnsForTransaction(selectedTransaction.id)
                                    .length > 0
                            "
                        >
                            <h4 style="margin-bottom: 1rem; color: #333">
                                Historique des Retours
                            </h4>
                            <table class="details-table">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th>Quantité Retournée</th>
                                        <th>Date de Retour</th>
                                        <th>Commentaire</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="returnItem in getReturnsForTransaction(
                                            selectedTransaction.id
                                        )"
                                        :key="returnItem.id"
                                    >
                                        <td>
                                            {{
                                                getProductName(
                                                    returnItem.product_id
                                                )
                                            }}
                                        </td>
                                        <td class="qty-center">
                                            {{ returnItem.quantity_returned }}
                                        </td>
                                        <td>
                                            {{ formatDate(returnItem.date) }}
                                        </td>
                                        <td>{{ returnItem.comment || '-' }}</td>
                                        <td class="actions-cell">
                                            <button
                                                @click="
                                                    openEditReturnModal(
                                                        returnItem
                                                    )
                                                "
                                                class="btn-small btn-action"
                                                title="Modifier"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button
                                                @click="
                                                    openDeleteReturnModal(
                                                        returnItem
                                                    )
                                                "
                                                class="btn-small btn-danger"
                                                title="Supprimer"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            @click="closeDetailsModal"
                            class="btn-secondary"
                        >
                            <i class="fas fa-times"></i>
                            Fermer
                        </button>
                        <button
                            @click="printTransaction(selectedTransaction)"
                            class="btn-primary"
                        >
                            <i class="fas fa-print"></i>
                            Imprimer
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal: Enregistrer un Retour (IMPROVED) -->
            <div
                v-if="showReturnModal"
                class="modal-overlay"
                @click.self="closeReturnModal"
            >
                <div class="modal-container">
                    <div class="modal-header">
                        <h5>
                            <i class="fas fa-reply"></i>
                            Enregistrer les Retours
                        </h5>
                        <button @click="closeReturnModal" class="modal-close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body" v-if="selectedTransaction">
                        <div class="return-info-section">
                            <div class="info-item">
                                <span class="info-label">Client :</span>
                                <span class="info-value">
                                    {{ selectedTransaction.client_name }}
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Référence :</span>
                                <span class="info-value">
                                    {{ selectedTransaction.reference }}
                                </span>
                            </div>
                        </div>

                        <!-- Improved return modal with product selection and quantity input for each product -->
                        <div class="products-for-return">
                            <h4 style="margin-bottom: 1rem; color: #333">
                                Sélectionner les Produits à Retourner
                            </h4>
                            <div
                                v-for="product in getProductsForReturn(
                                    selectedTransaction.id
                                )"
                                :key="product.id"
                                class="return-product-item"
                            >
                                <div class="product-header">
                                    <div class="product-info">
                                        <span class="product-name">
                                            {{
                                                getProductName(
                                                    product.product_id
                                                )
                                            }}
                                        </span>
                                        <span class="product-status">
                                            Remis:
                                            {{ product.quantity_given }} |
                                            Retourné:
                                            {{ product.quantity_returned }}
                                        </span>
                                    </div>
                                </div>
                                <div class="return-inputs">
                                    <div class="form-group">
                                        <label>
                                            Quantité à retourner
                                            <small>
                                                (max:
                                                {{
                                                    product.quantity_given -
                                                    product.quantity_returned
                                                }})
                                            </small>
                                        </label>
                                        <input
                                            type="number"
                                            v-model.number="
                                                returnData[product.id].quantity
                                            "
                                            min="0"
                                            step="0.01"
                                            :max="
                                                product.quantity_given -
                                                product.quantity_returned
                                            "
                                            class="form-control"
                                            placeholder="Quantité"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Date de retour *</label>
                            <input
                                type="date"
                                v-model="returnDate"
                                class="form-control"
                                required
                            />
                        </div>

                        <div class="form-group">
                            <label>Commentaire</label>
                            <textarea
                                v-model="returnComment"
                                class="form-control"
                                rows="3"
                                placeholder="Remarques sur le retour..."
                            ></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button @click="closeReturnModal" class="btn-secondary">
                            <i class="fas fa-times"></i>
                            Annuler
                        </button>
                        <button
                            @click="submitReturn"
                            :disabled="!canSubmitReturn"
                            class="btn-primary"
                        >
                            <i class="fas fa-save"></i>
                            Enregistrer Retour
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal: Supprimer Retour -->
            <div
                v-if="showDeleteReturnModal"
                class="modal-overlay"
                @click.self="closeDeleteReturnModal"
            >
                <div class="modal-container modal-confirm">
                    <div class="modal-header">
                        <h5>
                            <i class="fas fa-exclamation-triangle"></i>
                            Confirmer la suppression du retour
                        </h5>
                    </div>
                    <div class="modal-body">
                        <p>
                            Êtes-vous sûr de vouloir supprimer ce retour de
                            {{ getProductName(returnToDelete?.product_id) }} ?
                        </p>
                        <p v-if="returnToDelete">
                            Quantité:
                            <strong>
                                {{ returnToDelete.quantity_returned }}
                            </strong>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button
                            @click="closeDeleteReturnModal"
                            class="btn-secondary"
                        >
                            <i class="fas fa-times"></i>
                            Annuler
                        </button>
                        <button @click="deleteReturn" class="btn-danger">
                            <i class="fas fa-trash"></i>
                            Supprimer
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal: Supprimer Transaction -->
            <div
                v-if="showDeleteModal"
                class="modal-overlay"
                @click.self="closeDeleteModal"
            >
                <div class="modal-container modal-confirm">
                    <div class="modal-header">
                        <h5>
                            <i class="fas fa-exclamation-triangle"></i>
                            Confirmer la suppression
                        </h5>
                    </div>
                    <div class="modal-body">
                        <p>
                            Êtes-vous sûr de vouloir supprimer cette opération
                            de remise ?
                        </p>
                        <p v-if="transactionToDelete">
                            <strong>Client :</strong>
                            {{ transactionToDelete.client_name }}
                            <br />
                            <strong>Référence :</strong>
                            {{ transactionToDelete.reference }}
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button @click="closeDeleteModal" class="btn-secondary">
                            <i class="fas fa-times"></i>
                            Annuler
                        </button>
                        <button @click="deleteTransaction" class="btn-danger">
                            <i class="fas fa-trash"></i>
                            Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<script>
    const axios = window.axios;

    export default {
        name: 'ReturnableProductsComponent',
        data() {
            return {
                // Data
                customers: [],
                products: [],
                transactions: [],
                transactionProducts: [],
                stockReturns: [],
                allProducts: [],

                // Search & Filters
                searchQuery: '',
                filterClient: '',
                filterStatus: '',
                sortOption: 'Date (récent)',
                currentPage: 1,
                itemsPerPage: 10,

                // Modal States
                showCreateModal: false,
                showDetailsModal: false,
                showReturnModal: false,
                showDeleteReturnModal: false,
                showDeleteModal: false,
                showNewCustomerDropdown: false,

                // New Transaction Form
                newTransaction: {
                    customerSearch: '',
                    selectedClient: null,
                    productLines: [
                        {
                            selectedProduct: null,
                            searchQuery: '',
                            showDropdown: false,
                            quantity: 0,
                        },
                    ],
                    comment: '',
                },

                // Selected Items
                selectedTransaction: null,
                transactionToDelete: null,
                returnToDelete: null,

                // Return Form
                returnDate: new Date().toISOString().split('T')[0],
                returnComment: '',
                returnData: {},

                // Filtered Data
                filteredNewCustomers: [],
            };
        },

        computed: {
            paginatedTransactions() {
                let filtered = this.getFilteredTransactions();
                const start = (this.currentPage - 1) * this.itemsPerPage;
                return filtered.slice(start, start + this.itemsPerPage);
            },

            totalPages() {
                return Math.ceil(
                    this.getFilteredTransactions().length / this.itemsPerPage
                );
            },

            uniqueClients() {
                // Ensure unique clients are correctly extracted
                const uniqueClientsMap = new Map();
                this.customers.forEach((client) => {
                    if (!uniqueClientsMap.has(client.id)) {
                        uniqueClientsMap.set(client.id, client);
                    }
                });
                return Array.from(uniqueClientsMap.values());
            },

            totalGiven() {
                return this.transactionProducts
                    .reduce(
                        (sum, p) => sum + (parseFloat(p.quantity_given) || 0),
                        0
                    )
                    .toFixed(2);
            },

            totalReturned() {
                return this.stockReturns
                    .reduce(
                        (sum, r) =>
                            sum + (parseFloat(r.quantity_returned) || 0),
                        0
                    )
                    .toFixed(2);
            },

            totalPending() {
                const given = parseFloat(this.totalGiven) || 0;
                const returned = parseFloat(this.totalReturned) || 0;
                return (given - returned).toFixed(2);
            },

            activeTransactions() {
                return this.transactions.filter(
                    (t) => parseFloat(this.getTotalQuantityPending(t.id)) > 0
                ).length;
            },

            canSubmitNewTransaction() {
                return (
                    this.newTransaction.selectedClient &&
                    this.newTransaction.productLines.some(
                        (line) => line.selectedProduct && line.quantity > 0
                    )
                );
            },

            canSubmitReturn() {
                return (
                    this.returnDate &&
                    Object.values(this.returnData).some(
                        (item) => item.quantity > 0
                    )
                );
            },
        },

        mounted() {
            this.fetchAllData();
        },

        methods: {
            async fetchAllData() {
                try {
                    console.log('[v0] Fetching all data...');
                    await Promise.all([
                        this.fetchClients(),
                        this.fetchProducts(),
                        this.fetchTransactions(),
                        this.fetchTransactionProducts(),
                        this.fetchStockReturns(),
                    ]);
                    console.log('[v0] All data fetched successfully');
                } catch (error) {
                    console.error('[v0] Error fetching data:', error);
                    alert('Erreur lors du chargement des données');
                }
            },

            async fetchClients() {
                try {
                    const route = 'http://127.0.0.1:8000/clientslist';
                    console.log('[v0] REQUEST: GET - Route:', route);
                    const response = await axios.get(route);
                    console.log('[v0] RESPONSE: Clients fetched', {
                        count: response.data.length,
                        data: response.data,
                    });
                    this.customers = response.data;
                } catch (error) {
                    console.error('[v0] ERROR fetching clients:', {
                        route: 'http://127.0.0.1:8000/clientslist',
                        error: error.message,
                        response: error.response?.data,
                    });
                }
            },

            async fetchProducts() {
                try {
                    const route = 'http://127.0.0.1:8000/productsList';
                    console.log('[v0] REQUEST: GET - Route:', route);
                    const response = await axios.get(route);
                    console.log('[v0] RESPONSE: Products fetched', {
                        count: response.data.length,
                        data: response.data,
                    });
                    this.allProducts = response.data.filter(
                        (p) => p.isReturnable === 1
                    );
                } catch (error) {
                    console.error('[v0] ERROR fetching products:', {
                        route: 'http://127.0.0.1:8000/productsList',
                        error: error.message,
                        response: error.response?.data,
                    });
                }
            },

            async fetchTransactions() {
                try {
                    const route =
                        'http://127.0.0.1:8000/returnable-products-transactions';
                    console.log('[v0] REQUEST: GET - Route:', route);
                    const response = await axios.get(route);
                    console.log('[v0] RESPONSE: Transactions fetched', {
                        count: response.data.length,
                        data: response.data,
                    });
                    this.transactions = response.data;
                } catch (error) {
                    console.error('[v0] ERROR fetching transactions:', {
                        route: 'http://127.0.0.1:8000/returnable-products-transactions',
                        error: error.message,
                        response: error.response?.data,
                    });
                }
            },

            async fetchTransactionProducts() {
                try {
                    const route =
                        'http://127.0.0.1:8000/returnable-products-list';
                    console.log('[v0] REQUEST: GET - Route:', route);
                    const response = await axios.get(route);
                    console.log('[v0] RESPONSE: Transaction products fetched', {
                        count: response.data.length,
                        data: response.data,
                    });
                    this.transactionProducts = response.data;
                } catch (error) {
                    console.error('[v0] ERROR fetching transaction products:', {
                        route: 'http://127.0.0.1:8000/returnable-products-list',
                        error: error.message,
                        response: error.response?.data,
                    });
                }
            },

            async fetchStockReturns() {
                try {
                    const route =
                        'http://127.0.0.1:8000/stocks-returnable-products';
                    console.log('[v0] REQUEST: GET - Route:', route);
                    const response = await axios.get(route);
                    console.log('[v0] RESPONSE: Stock returns fetched', {
                        count: response.data.length,
                        data: response.data,
                    });
                    this.stockReturns = response.data;
                } catch (error) {
                    console.error('[v0] ERROR fetching stock returns:', {
                        route: 'http://127.0.0.1:8000/stocks-returnable-products',
                        error: error.message,
                        response: error.response?.data,
                    });
                }
            },

            // Filter & Search Methods
            getFilteredTransactions() {
                let filtered = this.transactions;

                if (this.searchQuery) {
                    const query = this.searchQuery.toLowerCase();
                    filtered = filtered.filter(
                        (t) =>
                            t.client_name.toLowerCase().includes(query) ||
                            t.reference.toLowerCase().includes(query) ||
                            this.getProductsForTransaction(t.id).some((p) =>
                                this.getProductName(p.product_id)
                                    .toLowerCase()
                                    .includes(query)
                            )
                    );
                }

                if (this.filterClient) {
                    filtered = filtered.filter(
                        (t) => t.client_id === parseInt(this.filterClient)
                    );
                }

                if (this.filterStatus) {
                    filtered = filtered.filter(
                        (t) =>
                            this.getTransactionStatus(t) === this.filterStatus
                    );
                }

                return this.sortTransactions(filtered);
            },

            sortTransactions(items) {
                const sorted = [...items];
                switch (this.sortOption) {
                    case 'Date (ancien)':
                        return sorted.sort(
                            (a, b) => new Date(a.date) - new Date(b.date)
                        );
                    case 'Client (A-Z)':
                        return sorted.sort((a, b) =>
                            a.client_name.localeCompare(b.client_name)
                        );
                    case 'Quantité restante (croissant)':
                        return sorted.sort(
                            (a, b) =>
                                parseFloat(this.getTotalQuantityPending(a.id)) -
                                parseFloat(this.getTotalQuantityPending(b.id))
                        );
                    case 'Quantité restante (décroissant)':
                        return sorted.sort(
                            (a, b) =>
                                parseFloat(this.getTotalQuantityPending(b.id)) -
                                parseFloat(this.getTotalQuantityPending(a.id))
                        );
                    default: // Date récent
                        return sorted.sort(
                            (a, b) => new Date(b.date) - new Date(a.date)
                        );
                }
            },

            // Data Retrieval Methods
            getProductsForTransaction(transactionId) {
                return this.transactionProducts.filter(
                    (p) => p.returnable_product_id === transactionId
                );
            },

            getProductsForReturn(transactionId) {
                return this.getProductsForTransaction(transactionId).filter(
                    (p) => p.quantity_given > p.quantity_returned
                );
            },

            getReturnsForTransaction(transactionId) {
                return this.stockReturns.filter(
                    (r) => r.returnable_product_id === transactionId
                );
            },

            getProductCountForTransaction(transactionId) {
                return this.getProductsForTransaction(transactionId).length;
            },

            getTotalQuantityGiven(transactionId) {
                return this.getProductsForTransaction(transactionId)
                    .reduce(
                        (sum, p) => sum + (parseFloat(p.quantity_given) || 0),
                        0
                    )
                    .toFixed(2);
            },

            getTotalQuantityReturned(transactionId) {
                return this.getProductsForTransaction(transactionId)
                    .reduce(
                        (sum, p) =>
                            sum + (parseFloat(p.quantity_returned) || 0),
                        0
                    )
                    .toFixed(2);
            },

            getTotalQuantityPending(transactionId) {
                const given =
                    parseFloat(this.getTotalQuantityGiven(transactionId)) || 0;
                const returned =
                    parseFloat(this.getTotalQuantityReturned(transactionId)) ||
                    0;
                return (given - returned).toFixed(2);
            },

            getProductName(productId) {
                const product = this.allProducts.find(
                    (p) => p.id === productId
                );
                return product ? product.name : 'Produit inconnu';
            },

            // Status Methods
            getTransactionStatus(transaction) {
                const pending =
                    parseFloat(this.getTotalQuantityPending(transaction.id)) ||
                    0;
                const given =
                    parseFloat(this.getTotalQuantityGiven(transaction.id)) || 0;

                if (pending === 0 && given > 0) return 'complete';
                if (
                    parseFloat(this.getTotalQuantityReturned(transaction.id)) >
                    0
                )
                    return 'partial';
                return 'pending';
            },

            getTransactionStatusText(transaction) {
                switch (this.getTransactionStatus(transaction)) {
                    case 'complete':
                        return 'Complètement retourné';
                    case 'partial':
                        return 'Partiellement retourné';
                    default:
                        return 'Non retourné';
                }
            },

            getTransactionStatusBadgeStyle(transaction) {
                const status = this.getTransactionStatus(transaction);
                const styles = {
                    complete: {
                        bg: '#e8f5e9',
                        color: '#388e3c',
                        border: '#a5d6a7',
                    },
                    partial: {
                        bg: '#fff3e0',
                        color: '#f57c00',
                        border: '#ffcc80',
                    },
                    pending: {
                        bg: '#ffebee',
                        color: '#c62828',
                        border: '#ef9a9a',
                    },
                };
                const style = styles[status] || styles.pending;
                return {
                    backgroundColor: style.bg,
                    color: style.color,
                    border: `1px solid ${style.border}`,
                    padding: '0.4rem 0.8rem',
                    borderRadius: '20px',
                    fontSize: '0.85rem',
                    fontWeight: '600',
                    display: 'inline-block',
                };
            },

            getTransactionRowClass(transaction) {
                const status = this.getTransactionStatus(transaction);
                return `row-${status}`;
            },

            getProductStatusText(product) {
                if (
                    parseFloat(product.quantity_given) ===
                    parseFloat(product.quantity_returned)
                )
                    return 'Complètement retourné';
                if (parseFloat(product.quantity_returned) > 0)
                    return 'Partiellement retourné';
                return 'Non retourné';
            },

            getProductStatusBadgeStyle(product) {
                const pending =
                    parseFloat(product.quantity_given) -
                    parseFloat(product.quantity_returned);
                const styles = {
                    complete: {
                        bg: '#e8f5e9',
                        color: '#388e3c',
                        border: '#a5d6a7',
                    },
                    partial: {
                        bg: '#fff3e0',
                        color: '#f57c00',
                        border: '#ffcc80',
                    },
                    pending: {
                        bg: '#ffebee',
                        color: '#c62828',
                        border: '#ef9a9a',
                    },
                };
                const type =
                    pending === 0
                        ? 'complete'
                        : parseFloat(product.quantity_returned) > 0
                        ? 'partial'
                        : 'pending';
                const style = styles[type];
                return {
                    backgroundColor: style.bg,
                    color: style.color,
                    border: `1px solid ${style.border}`,
                    padding: '0.4rem 0.8rem',
                    borderRadius: '20px',
                    fontSize: '0.85rem',
                    fontWeight: '600',
                    display: 'inline-block',
                };
            },

            // Modal Methods
            openCreateTransactionModal() {
                this.newTransaction = {
                    customerSearch: '',
                    selectedClient: null,
                    productLines: [
                        {
                            selectedProduct: null,
                            searchQuery: '',
                            showDropdown: false,
                            quantity: '',
                        },
                    ],
                    comment: '',
                };
                this.filteredNewCustomers = [...this.customers];
                this.showCreateModal = true;
            },

            closeCreateModal() {
                this.showCreateModal = false;
                this.newTransaction.customerSearch = '';
                this.newTransaction.selectedClient = null;
            },

            openDetailsModal(transaction) {
                this.selectedTransaction = transaction;
                this.showDetailsModal = true;
            },

            closeDetailsModal() {
                this.showDetailsModal = false;
                this.selectedTransaction = null;
            },

            openRecordReturnModal(transaction) {
                this.selectedTransaction = transaction;
                this.returnDate = new Date().toISOString().split('T')[0];
                this.returnComment = '';
                this.returnData = {};

                this.getProductsForReturn(transaction.id).forEach((product) => {
                    // Ensure product.id is valid before setting
                    if (product && product.id !== undefined) {
                        this.$set(this.returnData, product.id, { quantity: 0 });
                    }
                });

                this.showReturnModal = true;
            },

            closeReturnModal() {
                this.showReturnModal = false;
                this.selectedTransaction = null;
                this.returnData = {};
            },

            openEditReturnModal(returnItem) {
                // Logic to edit return item
                console.log('[v0] Edit return:', returnItem);
                // You might want to open a modal similar to the return modal, pre-filled with returnItem data
            },

            openDeleteReturnModal(returnItem) {
                this.returnToDelete = returnItem;
                this.showDeleteReturnModal = true;
            },

            closeDeleteReturnModal() {
                this.showDeleteReturnModal = false;
                this.returnToDelete = null;
            },

            openDeleteModal(transaction) {
                this.transactionToDelete = transaction;
                this.showDeleteModal = true;
            },

            closeDeleteModal() {
                this.showDeleteModal = false;
                this.transactionToDelete = null;
            },

            // Customer Search
            filterNewTransactionCustomers() {
                const query = this.newTransaction.customerSearch.toLowerCase();
                this.filteredNewCustomers = this.customers.filter(
                    (c) =>
                        c.name.toLowerCase().includes(query) ||
                        c.phone.includes(query)
                );
            },

            selectNewTransactionCustomer(customer) {
                this.newTransaction.selectedClient = customer;
                this.newTransaction.customerSearch = customer.name;
                this.showNewCustomerDropdown = false;
            },

            // Product Search
            onProductSearch(index) {
                // This function might need to be more sophisticated if you want real-time filtering as user types
                // For now, it's primarily a placeholder for potential future enhancements.
                this.newTransaction.productLines[index].showDropdown = true;
            },

            getFilteredReturnableProducts(index) {
                const query =
                    this.newTransaction.productLines[
                        index
                    ].searchQuery.toLowerCase();
                const selectedProductIds = this.newTransaction.productLines
                    .filter((_, i) => i !== index && _.selectedProduct)
                    .map((line) => line.selectedProduct.id);

                return this.allProducts.filter(
                    (p) =>
                        p.name.toLowerCase().includes(query) &&
                        !selectedProductIds.includes(p.id) &&
                        p.isReturnable === 1
                );
            },

            selectProductForLine(index, product) {
                this.newTransaction.productLines[index].selectedProduct =
                    product;
                this.newTransaction.productLines[index].searchQuery =
                    product.name;
                this.newTransaction.productLines[index].showDropdown = false;
            },

            addProductLine() {
                this.newTransaction.productLines.push({
                    selectedProduct: null,
                    searchQuery: '',
                    showDropdown: false,
                    quantity: 0,
                });
            },

            removeProductLine(index) {
                this.newTransaction.productLines.splice(index, 1);
            },

            // Form Submissions
            async submitNewTransaction() {
                if (!this.canSubmitNewTransaction) {
                    alert('Veuillez compléter le formulaire');
                    return;
                }

                const route = 'http://127.0.0.1:8000/returnable-products';

                const products = this.newTransaction.productLines
                    .filter((l) => l.selectedProduct && l.quantity > 0)
                    .map((l) => ({
                        product_id: l.selectedProduct.id,
                        quantity_given: Number(l.quantity),
                    }));

                // Date/heure Afrique de l’Ouest (WAT = UTC+1)
                const now = new Date();
                const formatter = new Intl.DateTimeFormat('sv-SE', {
                    timeZone: 'Africa/Lagos', // WAT
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false,
                });
                const datetime = formatter.format(now).replace('T', ' ');
                // => YYYY-MM-DD HH:mm:ss

                const reference = `REM-${datetime
                    .slice(0, 10)
                    .replace(/-/g, '')}-${Math.random()
                    .toString(36)
                    .slice(2, 5)
                    .toUpperCase()}`;

                const payload = {
                    client_id: this.newTransaction.selectedClient.id,
                    date: datetime, // DATETIME WAT envoyé au backend
                    comment: this.newTransaction.comment,
                    reference,
                    items: products,
                };

                console.group('[SUBMIT REMISE]');
                console.log('Route:', route);
                console.log('Payload:', payload);
                console.groupEnd();

                try {
                    const response = await axios.post(route, payload);

                    console.group('[RESPONSE REMISE]');
                    console.log(response.data);
                    console.groupEnd();

                    if (response.data.success) {
                        alert('Remise enregistrée');
                        this.closeCreateModal();
                        await this.fetchAllData();
                    } else {
                        alert(response.data.message || 'Erreur serveur');
                    }
                } catch (error) {
                    console.error('[ERROR REMISE]', error.response || error);
                    alert(error.response?.data?.message || error.message);
                }
            },
            async submitReturn() {
                if (!this.canSubmitReturn) {
                    alert('Veuillez entrer au moins une quantité à retourner');
                    return;
                }

                const returnsData = Object.entries(this.returnData)
                    .filter(([_, data]) => data.quantity > 0)
                    .map(([productId, data]) => ({
                        returnable_product_id: productId,
                        quantity_returned: data.quantity,
                        date: this.returnDate,
                        comment: this.returnComment,
                    }));

                // Check if returnsData is empty, if so, don't proceed
                if (returnsData.length === 0) {
                    alert('Aucune quantité à retourner sélectionnée.');
                    return;
                }

                try {
                    // Log each return request
                    for (const returnItem of returnsData) {
                        const route =
                            'http://127.0.0.1:8000/stocks-returnable-products';
                        console.log('[v0] REQUEST: POST - Route:', route);
                        console.log('[v0] PAYLOAD:', returnItem);

                        const response = await axios.post(route, returnItem);
                        console.log('[v0] RESPONSE: Return recorded', {
                            status: response.status,
                            data: response.data,
                        });
                    }

                    alert('Retours enregistrés avec succès');
                    this.closeReturnModal();
                    await this.fetchAllData();
                } catch (error) {
                    console.error('[v0] ERROR submitting returns:', {
                        route: 'http://127.0.0.1:8000/stocks-returnable-products',
                        payload: returnsData,
                        error: error.message,
                        response: error.response?.data,
                    });
                    alert(
                        "Erreur lors de l'enregistrement des retours: " +
                            (error.response?.data?.message || error.message)
                    );
                }
            },

            async deleteReturn() {
                if (!this.returnToDelete) return;

                try {
                    await axios.delete(
                        `http://127.0.0.1:8000/stocks-returnable-products/${this.returnToDelete.id}`
                    );
                    alert('Retour supprimé avec succès');
                    this.closeDeleteReturnModal();
                    await this.fetchAllData();
                } catch (error) {
                    console.error('[v0] Error deleting return:', error);
                    alert(
                        'Erreur lors de la suppression: ' +
                            (error.response?.data?.message || error.message)
                    );
                }
            },

            async deleteTransaction() {
                if (!this.transactionToDelete) return;

                try {
                    await axios.delete(
                        `http://127.0.0.1:8000/returnable-products-transactions/${this.transactionToDelete.id}`
                    );
                    alert('Opération supprimée avec succès');
                    this.closeDeleteModal();
                    await this.fetchAllData();
                } catch (error) {
                    console.error('[v0] Error deleting transaction:', error);
                    alert(
                        'Erreur lors de la suppression: ' +
                            (error.response?.data?.message || error.message)
                    );
                }
            },

            // Print Methods
            printTransaction(transaction) {
                if (!transaction) return; // Prevent errors if transaction is null

                const products = this.getProductsForTransaction(transaction.id);
                const returns = this.getReturnsForTransaction(transaction.id);
                const client = transaction.client_name;

                let html = `
                    <html>
                    <head>
                        <meta charset="UTF-8">
                        <title>Remise de Produits - ${
                            transaction.reference
                        }</title>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                margin: 0;
                                padding: 20px;
                                color: #333;
                            }
                            .company-header {
                                text-align: center;
                                margin-bottom: 30px;
                                border-bottom: 3px solid #667eea;
                                padding-bottom: 20px;
                            }
                            .company-header h1 {
                                color: #667eea;
                                margin: 0 0 10px 0;
                                font-size: 2rem;
                            }
                            .company-header p {
                                margin: 5px 0;
                                color: #666;
                                font-size: 0.9rem;
                            }
                            .title {
                                font-size: 1.5rem;
                                font-weight: bold;
                                margin: 20px 0 10px 0;
                                color: #764ba2;
                            }
                            .transaction-info {
                                background: #f8f9fa;
                                padding: 15px;
                                border-radius: 6px;
                                margin-bottom: 20px;
                                border-left: 4px solid #667eea;
                            }
                            .info-row {
                                margin-bottom: 8px;
                                display: flex;
                                justify-content: space-between;
                            }
                            .info-label {
                                font-weight: 600;
                                color: #333;
                            }
                            .info-value {
                                color: #666;
                            }
                            table {
                                width: 100%;
                                border-collapse: collapse;
                                margin: 20px 0;
                            }
                            th {
                                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                color: white;
                                padding: 12px;
                                text-align: left;
                                font-weight: bold;
                            }
                            td {
                                border: 1px solid #ddd;
                                padding: 10px;
                                text-align: left;
                            }
                            .total-row {
                                font-weight: bold;
                                background-color: #f9f9f9;
                            }
                            .section-header {
                                font-weight: 600;
                                margin-top: 20px;
                                margin-bottom: 10px;
                                color: #333;
                            }
                            .footer {
                                margin-top: 40px;
                                text-align: center;
                                color: #999;
                                font-size: 0.85rem;
                                border-top: 1px solid #ddd;
                                padding-top: 20px;
                            }
                            .qty-right { text-align: right; }
                            @media print {
                                body { margin: 0; padding: 10mm; }
                            }
                        </style>
                    </head>
                    <body>
                        <!-- SAGER Header with company info, IFU and phone -->
                        <div class="company-header">
                            <h1>SAGER</h1>
                            <p>Votre partenaire de confiance pour tous vos besoins en boissons et gaz domestique<br>
                            Distribution professionnelle • Vente en gros et détail</p>
                            <p><strong>Téléphone:</strong> +229 0196466625</p>
                            <p><strong>IFU:</strong> 0202586942320</p>
                        </div>

                        <div class="title">REMISE DE PRODUITS AVEC EMBALLAGES</div>
                        <p style="text-align: center; color: #999; font-size: 0.9rem;">Référence: ${
                            transaction.reference
                        }</p>

                        <div class="transaction-info">
                            <div class="info-row">
                                <span class="info-label">Client :</span>
                                <span class="info-value">${client}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Date de remise :</span>
                                <span class="info-value">${this.formatDate(
                                    transaction.date
                                )}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Imprimé le :</span>
                                <span class="info-value">${new Date().toLocaleDateString(
                                    'fr-FR'
                                )}</span>
                            </div>
                            ${
                                transaction.comment
                                    ? `<div class="info-row">
                                <span class="info-label">Commentaire :</span>
                                <span class="info-value">${transaction.comment}</span>
                            </div>`
                                    : ''
                            }
                        </div>

                        <div class="section-header">Produits Remis</div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th class="qty-right">Qté Remise</th>
                                    <th class="qty-right">Qté Retournée</th>
                                    <th class="qty-right">En Attente</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${products
                                    .map(
                                        (p) => `
                                    <tr>
                                        <td>${this.getProductName(
                                            p.product_id
                                        )}</td>
                                        <td class="qty-right">${parseFloat(
                                            p.quantity_given || 0
                                        ).toFixed(2)}</td>
                                        <td class="qty-right">${parseFloat(
                                            p.quantity_returned || 0
                                        ).toFixed(2)}</td>
                                        <td class="qty-right">${(
                                            parseFloat(p.quantity_given || 0) -
                                            parseFloat(p.quantity_returned || 0)
                                        ).toFixed(2)}</td>
                                        <td>${this.getProductStatusText(p)}</td>
                                    </tr>
                                `
                                    )
                                    .join('')}
                                <tr class="total-row">
                                    <td><strong>TOTAL</strong></td>
                                    <td class="qty-right"><strong>${this.getTotalQuantityGiven(
                                        transaction.id
                                    )}</strong></td>
                                    <td class="qty-right"><strong>${this.getTotalQuantityReturned(
                                        transaction.id
                                    )}</strong></td>
                                    <td class="qty-right"><strong>${this.getTotalQuantityPending(
                                        transaction.id
                                    )}</strong></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>

                        ${
                            returns.length > 0
                                ? `
                        <div class="section-header">Historique des Retours</div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th class="qty-right">Quantité</th>
                                    <th>Date de Retour</th>
                                    <th>Commentaire</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${returns
                                    .map(
                                        (r) => `
                                    <tr>
                                        <td>${this.getProductName(
                                            r.product_id
                                        )}</td>
                                        <td class="qty-right">${parseFloat(
                                            r.quantity_returned || 0
                                        ).toFixed(2)}</td>
                                        <td>${this.formatDate(r.date)}</td>
                                        <td>${r.comment || '-'}</td>
                                    </tr>
                                `
                                    )
                                    .join('')}
                            </tbody>
                        </table>
                        `
                                : ''
                        }

                        <!-- Footer with thanks and app name -->
                        <div class="footer">
                            <p>Merci de votre confiance</p>
                            <p>Rapport généré avec l'application SagerMarket</p>
                        </div>
                    </body>
                    </html>
                `;

                const printWindow = window.open('', '', 'width=900,height=600');
                printWindow.document.write(html);
                printWindow.document.close();
                setTimeout(() => printWindow.print(), 250);
            },

            printAll() {
                // Fetch all transactions for printing, not just the currently paginated ones
                const allTransactionsToPrint = this.getFilteredTransactions();

                if (allTransactionsToPrint.length === 0) {
                    alert('Aucune opération à imprimer');
                    return;
                }

                let html = `
                    <html>
                    <head>
                        <meta charset="UTF-8">
                        <title>Rapport Produits Consignés</title>
                        <style>
                            body { font-family: Arial, sans-serif; margin: 0; padding: 20px; color: #333; }
                            .company-header {
                                text-align: center;
                                margin-bottom: 30px;
                                border-bottom: 3px solid #667eea;
                                padding-bottom: 20px;
                            }
                            .company-header h1 {
                                color: #667eea;
                                margin: 0 0 10px 0;
                                font-size: 2rem;
                            }
                            .company-header p {
                                margin: 5px 0;
                                color: #666;
                                font-size: 0.9rem;
                            }
                            .title { font-size: 1.5rem; font-weight: bold; margin: 20px 0 10px 0; color: #764ba2; }
                            table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                            th {
                                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                color: white;
                                padding: 12px;
                                text-align: left;
                                font-weight: bold;
                            }
                            td { border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 12px; }
                            .footer { margin-top: 40px; text-align: center; color: #999; font-size: 0.85rem; border-top: 1px solid #ddd; padding-top: 20px; }
                            .qty-right { text-align: right; }
                            .page-break { page-break-after: always; } /* For breaking pages if many transactions */
                            @media print {
                                body { margin: 0; padding: 10mm; }
                                .page-break { page-break-after: always; }
                            }
                        </style>
                    </head>
                    <body>
                        <!-- SAGER Header -->
                        <div class="company-header">
                            <h1>SAGER</h1>
                            <p>Votre partenaire de confiance pour tous vos besoins en boissons et gaz domestique<br>
                            Distribution professionnelle • Vente en gros et détail</p>
                            <p><strong>Téléphone:</strong> +229 0196466625</p>
                            <p><strong>IFU:</strong> 0202586942320</p>
                        </div>

                        <div class="title">RAPPORTS DES PRODUITS CONSIGNÉS</div>

                        <table>
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Référence</th>
                                    <th>Date</th>
                                    <th class="qty-right">Remis</th>
                                    <th class="qty-right">Retourné</th>
                                    <th class="qty-right">Attente</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${allTransactionsToPrint
                                    .map((t, index) => {
                                        // Add page break if needed for large reports
                                        const pageBreakHtml =
                                            index > 0 && index % 15 === 0
                                                ? '<div class="page-break"></div>'
                                                : '';
                                        return `
                                        ${pageBreakHtml}
                                        <tr>
                                            <td>${t.client_name}</td>
                                            <td>${t.reference}</td>
                                            <td>${this.formatDate(t.date)}</td>
                                            <td class="qty-right">${this.getTotalQuantityGiven(
                                                t.id
                                            )}</td>
                                            <td class="qty-right">${this.getTotalQuantityReturned(
                                                t.id
                                            )}</td>
                                            <td class="qty-right">${this.getTotalQuantityPending(
                                                t.id
                                            )}</td>
                                            <td>${this.getTransactionStatusText(
                                                t
                                            )}</td>
                                        </tr>
                                    `;
                                    })
                                    .join('')}
                            </tbody>
                        </table>

                        <!-- Footer -->
                        <div class="footer">
                            <p>Merci de votre confiance</p>
                            <p>Rapport généré avec l'application SagerMarket</p>
                        </div>
                    </body>
                    </html>
                `;

                const printWindow = window.open('', '', 'width=900,height=600');
                printWindow.document.write(html);
                printWindow.document.close();
                setTimeout(() => printWindow.print(), 250);
            },

            // Utility Methods
            formatDate(dateString) {
                if (!dateString) return '';
                try {
                    return new Date(dateString).toLocaleDateString('fr-FR', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit',
                    });
                } catch (e) {
                    console.error('Error formatting date:', dateString, e);
                    return dateString; // Return original string if parsing fails
                }
            },
        },
    };
</script>

<style scoped>
    /* ... existing styles from the original component ... */

    /* Updated button styles to match ProductsComponent appearance -->
    .btn-eye {
        background-color: #28a745;
        color: white;
    }

    .btn-eye:hover {
        background-color: #218838;
    }

    .btn-reply {
        background-color: #667eea;
        color: white;
    }

    .btn-reply:hover {
        background-color: #5568d3;
    }

    .btn-print {
        background-color: #17a2b8;
        color: white;
    }

    .btn-print:hover {
        background-color: #138496;
    }

    .btn-delete {
        background-color: #dc3545;
        color: white;
    }

    .btn-delete:hover {
        background-color: #c82333;
    }

    .main-content {
        width: 100%;
        background: #f5f5f5;
        min-height: 100vh;
        padding: 20px;
    }

    .returnables-content {
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Header Section */
    .header-section {
        background: white;
        border-radius: 8px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .section-header {
        margin-bottom: 20px;
    }

    .section-header h2 {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 28px;
    }

    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }

    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
    }

    .stat-icon {
        font-size: 24px;
        margin-bottom: 10px;
    }

    .stat-number {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 13px;
        opacity: 0.9;
    }

    /* Controls Section */
    .controls-section {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
        display: flex;
        flex-direction: column;
        gap: 15px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .search-filters {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .search-box {
        position: relative;
        display: flex;
        align-items: center;
    }

    .search-input {
        width: 100%;
        padding: 10px 40px 10px 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
    }

    .search-box i {
        position: absolute;
        right: 15px;
        color: #999;
        pointer-events: none;
    }

    .filters-group {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 10px;
    }

    .select-filter {
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        background: white;
        cursor: pointer;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
    }

    .btn-primary,
    .btn-secondary {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-primary {
        background: #667eea;
        color: white;
    }

    .btn-primary:hover {
        background: #5568d3;
    }

    .btn-secondary {
        background: #f0f0f0;
        color: #333;
        border: 1px solid #ddd;
    }

    .btn-secondary:hover {
        background: #e8e8e8;
    }

    /* Table Section */
    .table-container {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        /* REMOVED: overflow-x: auto; from here to allow table-wrapper to handle it */
    }

    .table-wrapper {
        overflow-x: auto;
        width: 100%;
        -webkit-overflow-scrolling: touch; /* For smoother scrolling on iOS */
    }

    .products-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px; /* Ensure table doesn't shrink too much */
    }

    .products-table thead {
        /* Changed background to gradient to match other elements */
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        position: sticky; /* Keep header visible on scroll */
        top: 0;
        z-index: 10; /* Ensure header is above tbody */
    }

    .products-table th {
        padding: 12px;
        text-align: left;
        font-weight: 600;
        white-space: nowrap; /* Prevent text wrapping in headers */
    }

    .products-table td {
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
    }

    .products-table tbody tr {
        transition: background 0.2s;
    }

    .products-table tbody tr:hover {
        background: #f9f9f9;
    }

    .row-complete {
        border-left: 4px solid #28a745;
    }

    .row-partial {
        border-left: 4px solid #ffc107;
    }

    .row-pending {
        border-left: 4px solid #dc3545;
    }

    .qty-center {
        text-align: center !important;
    }

    .pending-qty {
        font-weight: bold;
        color: #dc3545;
    }

    .reference {
        font-family: monospace;
        font-size: 12px;
        color: #666;
    }

    .product-count-badge {
        background: #e7f3ff;
        color: #0066cc;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .actions-cell {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
        justify-content: center; /* Center actions horizontally */
    }

    .btn-small {
        padding: 6px 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        transition: all 0.2s;
        white-space: nowrap; /* Prevent wrapping */
    }

    /* Button styles moved up to be applied by .btn-eye, .btn-reply etc. */
    /* .btn-action {
        background: #667eea;
        color: white;
    }

    .btn-action:hover {
        background: #5568d3;
    }

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background: #c82333;
    } */

    .empty-row {
        height: 200px;
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 15px;
        margin-top: 20px;
    }

    .btn-pagination {
        padding: 8px 16px;
        border: 1px solid #667eea; /* Matched primary color */
        background: white;
        color: #667eea; /* Matched primary color */
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-pagination:hover:not(:disabled) {
        background: #667eea; /* Matched primary button hover */
        color: white;
    }

    .btn-pagination:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .pagination-info {
        color: #666;
        font-size: 14px;
        font-weight: 500;
    }

    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .modal-container {
        background: white;
        border-radius: 8px;
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }

    .modal-large {
        max-width: 900px;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid #ddd;
    }

    .modal-header h5 {
        margin: 0;
        color: #333;
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #999;
        padding: 0;
        width: 30px;
        height: 30px;
    }

    .modal-close:hover {
        color: #333;
    }

    .modal-body {
        padding: 20px;
    }

    .modal-footer {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        padding: 20px;
        border-top: 1px solid #ddd;
    }

    /* Form Styles */
    .form-section {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #f0f0f0;
    }

    .form-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-weight: 500;
        color: #333;
        font-size: 14px;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    .customer-search-container {
        position: relative;
    }

    .customer-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-top: none;
        border-radius: 0 0 6px 6px;
        max-height: 250px;
        overflow-y: auto;
        z-index: 100;
    }

    .customer-dropdown-item {
        padding: 12px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s;
    }

    .customer-dropdown-item:hover {
        background: #f9f9f9;
    }

    .customer-item-name {
        font-weight: 600;
        color: #333;
    }

    .customer-item-phone {
        font-size: 12px;
        color: #999;
        margin-top: 2px;
    }

    .selected-client-info {
        background: #f0f7ff;
        padding: 15px;
        border-radius: 6px;
        margin-top: 10px;
        border-left: 4px solid #667eea;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .info-label {
        font-weight: 600;
        color: #333;
    }

    .info-value {
        color: #666;
    }

    /* Product Lines */
    .products-list {
        background: #fafafa;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 15px;
    }

    .product-line-form {
        background: white;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 15px;
    }

    .product-line-form:last-child {
        margin-bottom: 0;
    }

    .product-line-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f0f0f0;
    }

    .line-number {
        font-weight: 600;
        color: #333;
    }

    .btn-remove-line {
        padding: 6px 12px;
        background: #ffe7e7;
        color: #dc3545;
        border: none;
        border-radius: 4px;
        font-size: 12px;
        cursor: pointer;
    }

    .btn-remove-line:hover {
        background: #ffcccc;
    }

    .product-search-container {
        position: relative;
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
        border-top: none;
        border-radius: 0 0 6px 6px;
        max-height: 250px;
        overflow-y: auto;
        z-index: 100;
    }

    .product-dropdown-item {
        padding: 12px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s;
    }

    .product-dropdown-item:hover {
        background: #f9f9f9;
    }

    .product-item-name {
        font-weight: 600;
        color: #333;
    }

    .product-item-stock {
        font-size: 12px;
        color: #0066cc;
        margin-top: 2px;
    }

    .quantity-input-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .quantity-input {
        flex: 1;
    }

    .quantity-info {
        font-size: 12px;
        color: #999;
        white-space: nowrap;
    }

    .btn-add-line {
        padding: 10px 20px;
        background: #e7f3ff;
        color: #0066cc;
        border: 1px solid #cce5ff;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-add-line:hover {
        background: #cce5ff;
    }

    /* Details Modal */
    .details-header {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        background: #f9f9f9;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 20px;
    }

    .detail-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .detail-label {
        font-weight: 600;
        color: #333;
        font-size: 13px;
    }

    .detail-value {
        color: #666;
        font-size: 14px;
    }

    .details-table-section {
        margin-bottom: 30px;
    }

    .details-table {
        width: 100%;
        border-collapse: collapse;
    }

    .details-table thead {
        background: #f9f9f9;
    }

    .details-table th {
        padding: 12px;
        text-align: left;
        font-weight: 600;
        border-bottom: 1px solid #ddd;
    }

    .details-table td {
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
    }

    /* Return Modal */
    .return-info-section {
        background: #f0f7ff;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 20px;
        border-left: 4px solid #667eea;
    }

    .products-for-return {
        margin-bottom: 20px;
    }

    .return-product-item {
        background: #fafafa;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 15px;
        border-left: 4px solid #667eea;
    }

    .product-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .product-info {
        /* Added for better structure within product-header */
        display: flex;
        flex-direction: column;
    }

    .product-name {
        font-weight: 600;
        color: #333;
    }

    .product-status {
        font-size: 12px;
        color: #666;
    }

    .return-inputs {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
    }

    /* Confirmation Modal */
    .modal-confirm {
        max-width: 400px;
    }

    .modal-confirm .modal-body {
        padding: 30px;
        text-align: center;
    }

    .modal-confirm p {
        margin-bottom: 10px;
        color: #666;
    }

    .modal-confirm strong {
        color: #333;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .products-table th,
        .products-table td {
            padding: 10px;
            font-size: 13px;
        }
    }

    @media (max-width: 768px) {
        .header-section {
            padding: 20px;
        }

        .section-header h2 {
            font-size: 22px;
        }

        .stats-container {
            grid-template-columns: repeat(2, 1fr);
        }

        .controls-section {
            padding: 15px;
        }

        .search-filters {
            gap: 10px;
        }

        .filters-group {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column;
            gap: 10px;
        }

        .btn-primary,
        .btn-secondary {
            width: 100%;
            justify-content: center;
        }

        .table-wrapper {
            overflow-x: auto; /* Ensure horizontal scrolling for tables on smaller screens */
            -webkit-overflow-scrolling: touch; /* For smoother scrolling on iOS */
        }

        .products-table {
            min-width: 100%; /* Allow table to take full width if needed */
            font-size: 12px; /* Reduce font size for smaller screens */
        }

        .products-table th,
        .products-table td {
            padding: 8px;
            font-size: 12px;
            white-space: normal; /* Allow wrapping in cells */
        }

        .products-table th {
            white-space: nowrap; /* Keep headers from wrapping for better alignment */
        }

        .product-count-badge {
            font-size: 11px;
            padding: 3px 6px;
        }

        .actions-cell {
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        .btn-small {
            width: 100%;
            padding: 5px 8px;
            font-size: 11px;
            justify-content: center;
        }

        .modal-container {
            width: 95%;
            max-width: 100%;
        }

        .modal-header h5 {
            font-size: 16px;
        }

        .modal-footer {
            flex-direction: column-reverse;
            align-items: center;
        }

        .details-header {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .info-row {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
