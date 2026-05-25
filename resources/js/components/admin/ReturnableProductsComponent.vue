<template>
    <main class="main-content packaging-main" id="packaging-app">
        <div class="statistics-section packaging-stats">
            <div class="stat-card stat-card-1">
                <div class="stat-icon">
                    <i class="fas fa-box-open"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Remises enregistrées</div>
                    <div class="stat-value">{{ recordsList.length }}</div>
                </div>
            </div>
            <div class="stat-card stat-card-2">
                <div class="stat-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Retours en attente</div>
                    <div class="stat-value">
                        {{
                            filteredRecordsList.filter(
                                (record) => computeStatus(record) === 'pending',
                            ).length
                        }}
                    </div>
                </div>
            </div>
            <div class="stat-card stat-card-3">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Retours complets</div>
                    <div class="stat-value">
                        {{
                            filteredRecordsList.filter(
                                (record) =>
                                    computeStatus(record) === 'complete',
                            ).length
                        }}
                    </div>
                </div>
            </div>
        </div>

        <div class="sales-content packaging-wrapper">
            <!-- Header Section -->
            <div class="sales-form packaging-header-box">
                <div class="packaging-title-block">
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
            </div>

            <!-- Controls Section -->
            <div class="sales-form packaging-controls-box">
                <div class="packaging-search-area">
                    <div class="packaging-search-field">
                        <input
                            type="text"
                            v-model="txtSearch"
                            placeholder="Rechercher par client ou produit..."
                            class="packaging-search-input"
                        />
                        <i class="fas fa-search"></i>
                    </div>

                    <div class="packaging-filters-row">
                        <select
                            v-model="selectedClientFilter"
                            class="packaging-select"
                        >
                            <option value="">Tous les clients</option>
                            <option
                                v-for="cli in clientOptions"
                                :key="cli.id"
                                :value="cli.id"
                            >
                                {{ cli.name }}
                            </option>
                        </select>

                        <select
                            v-model="selectedStatusFilter"
                            class="packaging-select"
                        >
                            <option value="">Tous les statuts</option>
                            <option value="pending">Non retourné</option>
                            <option value="partial">
                                Partiellement retourné
                            </option>
                            <option value="complete">
                                Complètement retourné
                            </option>
                            <option value="cancelled">Annulé</option>
                        </select>

                        <select
                            v-model="selectedSortBy"
                            class="packaging-select"
                        >
                            <option>Date (récent)</option>
                            <option>Date (ancien)</option>
                            <option>Client (A-Z)</option>
                            <option>Quantité restante (croissant)</option>
                            <option>Quantité restante (décroissant)</option>
                        </select>
                    </div>

                    <div class="packaging-date-row">
                        <label class="packaging-date-label">
                            Filtrer par date:
                        </label>
                        <input
                            type="date"
                            v-model="dateFilterFrom"
                            class="packaging-date-input"
                            placeholder="Date début"
                        />
                        <span class="packaging-date-sep">à</span>
                        <input
                            type="date"
                            v-model="dateFilterTo"
                            class="packaging-date-input"
                            placeholder="Date fin"
                        />
                        <button
                            v-if="dateFilterFrom || dateFilterTo"
                            @click="resetDateFilters"
                            class="packaging-btn-clear-date"
                            title="Effacer les dates"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="packaging-action-btns">
                    <button
                        @click="showNewDeliveryModal"
                        class="packaging-btn-main"
                    >
                        <i class="fas fa-plus-circle"></i>
                        Nouvelle Remise
                    </button>
                    <button @click="printAllRecords" class="packaging-btn-alt">
                        <i class="fas fa-print"></i>
                        Imprimer Tous
                    </button>
                </div>
            </div>

            <!-- Transactions List View -->
            <div class="sales-history packaging-table-box">
                <div class="table-header-styled packaging-list-header">
                    <h3>Historique des remises d'emballages</h3>
                    <span class="table-count">
                        {{ filteredRecordsList.length }} opération(s)
                    </span>
                </div>

                <div class="packaging-table-scroll">
                    <table class="packaging-data-table">
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
                                v-for="record in displayedRecords"
                                :key="record.id"
                                :class="computeRowStyle(record)"
                            >
                                <td data-label="Client">
                                    <strong>{{ record.client_name }}</strong>
                                </td>
                                <td data-label="Référence" class="ref-cell">
                                    {{ record.reference }}
                                </td>
                                <td data-label="Date Remise">
                                    {{ formatDateDisplay(record.date) }}
                                </td>
                                <td data-label="Produits">
                                    <span class="packaging-product-badge">
                                        {{ countProductsInRecord(record.id) }}
                                        produit(s)
                                    </span>
                                </td>
                                <td
                                    data-label="Qté Remise"
                                    class="qty-centered"
                                >
                                    {{ sumGivenQty(record.id) }}
                                </td>
                                <td
                                    data-label="Qté Retournée"
                                    class="qty-centered"
                                >
                                    {{ sumReturnedQty(record.id) }}
                                </td>
                                <td
                                    data-label="En Attente"
                                    class="qty-centered pending-cell"
                                >
                                    {{ sumPendingQty(record.id) }}
                                </td>
                                <td data-label="Statut">
                                    <span :style="computeStatusStyle(record)">
                                        {{ computeStatusLabel(record) }}
                                    </span>
                                </td>
                                <td data-label="Actions" class="actions-col">
                                    <button
                                        @click="showRecordDetails(record)"
                                        class="packaging-btn-sm packaging-btn-view"
                                        title="Voir détails"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button
                                        @click="showReturnForm(record)"
                                        v-if="record.status === 'Fait'"
                                        class="packaging-btn-sm packaging-btn-return"
                                        title="Enregistrer retour"
                                    >
                                        <i class="fas fa-reply"></i>
                                    </button>
                                    <button
                                        @click="printSingleRecord(record)"
                                        class="packaging-btn-sm packaging-btn-print"
                                        title="Imprimer"
                                    >
                                        <i class="fas fa-print"></i>
                                    </button>
                                    <button
                                        v-if="record.status != `Annulé`"
                                        @click="showCancelConfirm(record)"
                                        class="packaging-btn-sm packaging-btn-cancel"
                                        :class="{
                                            'packaging-btn-disabled':
                                                !canCancelRecord(record),
                                        }"
                                        :disabled="!canCancelRecord(record)"
                                        :title="canCancelRecord(record) ? 'Annuler' : 'Annulation indisponible des qu un retour existe'"
                                    >
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr
                                v-if="displayedRecords.length === 0"
                                class="empty-data-row"
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
                                            display: block;
                                        "
                                    ></i>
                                    <p>Aucune opération trouvée</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination principale -->
                <div class="packaging-pagination">
                    <div class="packaging-pagination-info">
                        <span class="packaging-pagination-count">
                            {{ filteredRecordsList.length }} résultat(s)
                        </span>
                        <span
                            class="packaging-pagination-sep"
                            v-if="filteredRecordsList.length > 0"
                        >
                            ·
                        </span>
                        <span v-if="filteredRecordsList.length > 0">
                            {{ (pageNum - 1) * rowsPerPage + 1 }}-{{
                                Math.min(
                                    pageNum * rowsPerPage,
                                    filteredRecordsList.length,
                                )
                            }}
                            affichés
                        </span>
                    </div>

                    <div class="packaging-pagination-controls">
                        <button
                            @click="pageNum = Math.max(1, pageNum - 1)"
                            :disabled="pageNum === 1"
                            class="packaging-btn-page packaging-btn-page-arrow"
                            title="Page précédente"
                        >
                            <i class="fas fa-chevron-left"></i>
                        </button>

                        <div class="packaging-page-pills">
                            <template v-for="p in pageCount" :key="p">
                                <button
                                    v-if="
                                        p === 1 ||
                                        p === pageCount ||
                                        Math.abs(p - pageNum) <= 1
                                    "
                                    @click="pageNum = p"
                                    class="packaging-page-pill"
                                    :class="{ active: p === pageNum }"
                                >
                                    {{ p }}
                                </button>
                                <span
                                    v-else-if="p === 2 && pageNum > 4"
                                    class="packaging-page-ellipsis"
                                >
                                    ...
                                </span>
                                <span
                                    v-else-if="
                                        p === pageCount - 1 &&
                                        pageNum < pageCount - 3
                                    "
                                    class="packaging-page-ellipsis"
                                >
                                    ...
                                </span>
                            </template>
                        </div>

                        <button
                            @click="pageNum = Math.min(pageCount, pageNum + 1)"
                            :disabled="pageNum === pageCount || pageCount === 0"
                            class="packaging-btn-page packaging-btn-page-arrow"
                            title="Page suivante"
                        >
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>

                    <div class="packaging-per-page">
                        <label>Lignes :</label>
                        <select
                            v-model.number="rowsPerPage"
                            @change="pageNum = 1"
                            class="packaging-per-page-select"
                        >
                            <option :value="5">5</option>
                            <option :value="10">10</option>
                            <option :value="25">25</option>
                            <option :value="50">50</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Modal: Nouvelle Remise de Produits -->
            <div
                v-if="isNewDeliveryOpen"
                class="packaging-modal-bg"
                @click.self="hideNewDeliveryModal"
            >
                <div class="packaging-modal-box packaging-modal-lg">
                    <div class="packaging-modal-top">
                        <h5>
                            <i class="fas fa-plus-circle"></i>
                            Nouvelle Remise de Produits avec Emballages
                        </h5>
                        <button
                            @click="hideNewDeliveryModal"
                            class="packaging-modal-x"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="packaging-modal-content">
                        <form @submit.prevent="saveNewDelivery">
                            <!-- Client Selection -->
                            <div class="packaging-form-block">
                                <h4 style="margin-bottom: 1rem; color: #333">
                                    Information Client
                                </h4>
                                <div class="packaging-form-field">
                                    <label>Sélectionner le client *</label>
                                    <div class="packaging-client-search-box">
                                        <input
                                            type="text"
                                            class="packaging-input"
                                            v-model="
                                                newDeliveryData.clientSearchText
                                            "
                                            @input="searchClientsForDelivery"
                                            @focus="isClientListVisible = true"
                                            placeholder="Rechercher un client..."
                                            required
                                        />
                                        <div
                                            v-if="
                                                isClientListVisible &&
                                                matchingClients.length > 0
                                            "
                                            class="packaging-client-list"
                                        >
                                            <div
                                                v-for="cli in matchingClients"
                                                :key="cli.id"
                                                @click="
                                                    pickClientForDelivery(cli)
                                                "
                                                class="packaging-client-item"
                                            >
                                                <div
                                                    class="packaging-client-name"
                                                >
                                                    {{ cli.name }}
                                                </div>
                                                <div
                                                    class="packaging-client-phone"
                                                >
                                                    {{ cli.phone }}
                                                </div>
                                            </div>
                                        </div>
                                        <button
                                            v-if="
                                                newDeliveryData.clientSearchText &&
                                                !newDeliveryData.chosenClient &&
                                                matchingClients.length === 0
                                            "
                                            type="button"
                                            @click="showAddClientModal"
                                            class="packaging-btn-add-client"
                                        >
                                            <i class="fas fa-plus"></i>
                                            Créer "{{
                                                newDeliveryData.clientSearchText
                                            }}"
                                        </button>
                                    </div>
                                </div>

                                <div
                                    v-if="newDeliveryData.chosenClient"
                                    class="packaging-chosen-client-box"
                                >
                                    <div class="packaging-info-row">
                                        <span class="packaging-info-key">
                                            Client :
                                        </span>
                                        <span class="packaging-info-val">
                                            {{
                                                newDeliveryData.chosenClient
                                                    .name
                                            }}
                                        </span>
                                    </div>
                                    <div class="packaging-info-row">
                                        <span class="packaging-info-key">
                                            Téléphone :
                                        </span>
                                        <span class="packaging-info-val">
                                            {{
                                                newDeliveryData.chosenClient
                                                    .phone
                                            }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Products Selection -->
                            <div
                                class="packaging-form-block"
                                v-if="newDeliveryData.chosenClient"
                            >
                                <h4 style="margin-bottom: 1rem; color: #333">
                                    Sélection des Produits avec Emballages
                                </h4>

                                <div class="packaging-products-area">
                                    <div
                                        v-for="(
                                            row, idx
                                        ) in newDeliveryData.itemRows"
                                        :key="idx"
                                        class="packaging-product-row-form"
                                    >
                                        <div class="packaging-row-header">
                                            <span class="packaging-row-num">
                                                Produit {{ idx + 1 }}
                                            </span>
                                            <button
                                                v-if="idx > 0"
                                                type="button"
                                                @click="deleteItemRow(idx)"
                                                class="packaging-btn-remove-row"
                                            >
                                                <i class="fas fa-times"></i>
                                                Supprimer
                                            </button>
                                        </div>

                                        <div class="packaging-form-field">
                                            <label>
                                                Produit avec emballage *
                                            </label>
                                            <div
                                                class="packaging-product-search-box"
                                            >
                                                <input
                                                    type="text"
                                                    class="packaging-input packaging-product-input"
                                                    v-model="
                                                        row.productSearchText
                                                    "
                                                    @input="onItemSearch(idx)"
                                                    @focus="
                                                        row.isDropdownOpen = true
                                                    "
                                                    placeholder="Rechercher un produit..."
                                                    required
                                                />
                                                <div
                                                    v-if="
                                                        row.isDropdownOpen &&
                                                        getAvailableProducts(
                                                            idx,
                                                        ).length > 0
                                                    "
                                                    class="packaging-product-list"
                                                >
                                                    <div
                                                        v-for="prod in getAvailableProducts(
                                                            idx,
                                                        )"
                                                        :key="prod.id"
                                                        @click="
                                                            pickProductForRow(
                                                                idx,
                                                                prod,
                                                            )
                                                        "
                                                        class="packaging-product-item"
                                                    >
                                                        <div
                                                            class="packaging-product-name"
                                                        >
                                                            {{ prod.name }}
                                                        </div>
                                                        <div
                                                            class="packaging-product-stock"
                                                        >
                                                            Stock:
                                                            {{ prod.quantity }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div
                                            class="packaging-form-field"
                                            v-if="row.pickedProduct"
                                        >
                                            <label>Quantité à remettre *</label>
                                            <div
                                                class="packaging-qty-input-box"
                                            >
                                                <input
                                                    type="number"
                                                    class="packaging-input packaging-qty-input"
                                                    v-model.number="
                                                        row.qtyAmount
                                                    "
                                                    min="0.01"
                                                    step="0.01"
                                                    :max="
                                                        row.pickedProduct
                                                            .quantity
                                                    "
                                                    placeholder="Quantité"
                                                    required
                                                />
                                                <span
                                                    class="packaging-qty-hint"
                                                >
                                                    Stock disponible:
                                                    {{
                                                        row.pickedProduct
                                                            .quantity
                                                    }}
                                                </span>
                                            </div>
                                        </div>

                                        <hr
                                            v-if="
                                                idx <
                                                newDeliveryData.itemRows
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
                                    @click="addNewItemRow"
                                    class="packaging-btn-add-row"
                                >
                                    <i class="fas fa-plus"></i>
                                    Ajouter un produit
                                </button>
                            </div>

                            <!-- Comment -->
                            <div
                                class="packaging-form-block"
                                v-if="newDeliveryData.chosenClient"
                            >
                                <div class="packaging-form-field">
                                    <label>Commentaire</label>
                                    <textarea
                                        v-model="newDeliveryData.noteText"
                                        class="packaging-input"
                                        rows="3"
                                        placeholder="Remarques supplémentaires..."
                                    ></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="packaging-modal-bottom">
                        <button
                            @click="hideNewDeliveryModal"
                            class="packaging-btn-alt"
                        >
                            <i class="fas fa-times"></i>
                            Annuler
                        </button>
                        <button
                            @click="saveNewDelivery"
                            :disabled="!isDeliveryFormValid"
                            class="packaging-btn-main"
                        >
                            <i class="fas fa-save"></i>
                            Enregistrer la Remise
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal: Détails Complets de la Transaction -->
            <div
                v-if="isDetailsOpen"
                class="packaging-modal-bg"
                @click.self="hideDetailsModal"
            >
                <div class="packaging-modal-box packaging-modal-lg">
                    <div class="packaging-modal-top">
                        <h5>
                            <i class="fas fa-file-invoice-dollar"></i>
                            Détails de la Remise - {{ activeRecord?.reference }}
                        </h5>
                        <div class="packaging-modal-top-actions">
                            <button
                                v-if="
                                    activeRecord &&
                                    getReturnsForRecord(activeRecord.id)
                                        .length > 0
                                "
                                @click="printReturnsHistory(activeRecord)"
                                class="packaging-btn-print-history"
                                title="Imprimer l'historique des retours"
                            >
                                <i class="fas fa-print"></i>
                                <span>Historique</span>
                            </button>
                            <button
                                @click="hideDetailsModal"
                                class="packaging-modal-x"
                            >
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="packaging-modal-content" v-if="activeRecord">
                        <!-- Header Info -->
                        <div class="packaging-details-head">
                            <div class="packaging-detail-col">
                                <div class="packaging-detail-row">
                                    <span class="packaging-detail-key">
                                        Client :
                                    </span>
                                    <span class="packaging-detail-val">
                                        {{ activeRecord.client_name }}
                                    </span>
                                </div>
                                <div class="packaging-detail-row">
                                    <span class="packaging-detail-key">
                                        Référence :
                                    </span>
                                    <span class="packaging-detail-val">
                                        {{ activeRecord.reference }}
                                    </span>
                                </div>
                            </div>
                            <div class="packaging-detail-col">
                                <div class="packaging-detail-row">
                                    <span class="packaging-detail-key">
                                        Date :
                                    </span>
                                    <span class="packaging-detail-val">
                                        {{
                                            formatDateDisplay(activeRecord.date)
                                        }}
                                    </span>
                                </div>
                                <div class="packaging-detail-row">
                                    <span class="packaging-detail-key">
                                        Commentaire :
                                    </span>
                                    <span class="packaging-detail-val">
                                        {{ activeRecord.comment || 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Products Table -->
                        <div class="packaging-details-section">
                            <h4 style="margin-bottom: 1rem; color: #333">
                                Produits Remis
                            </h4>
                            <table
                                class="packaging-details-tbl packaging-responsive-tbl"
                            >
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
                                        v-for="item in getItemsForRecord(
                                            activeRecord.id,
                                        )"
                                        :key="item.id"
                                    >
                                        <td data-label="Produit">
                                            {{
                                                findProductLabel(
                                                    item.product_id,
                                                )
                                            }}
                                        </td>
                                        <td data-label="Remis">
                                            {{ item.quantity_given }}
                                        </td>
                                        <td data-label="Retourné">
                                            {{
                                                getRealReturnedQtyForItem(
                                                    item.id,
                                                ).toFixed(2)
                                            }}
                                        </td>
                                        <td
                                            data-label="En Attente"
                                            class="pending-cell"
                                        >
                                            {{
                                                (
                                                    parseFloat(
                                                        item.quantity_given,
                                                    ) -
                                                    getRealReturnedQtyForItem(
                                                        item.id,
                                                    )
                                                ).toFixed(2)
                                            }}
                                        </td>
                                        <td data-label="Statut">
                                            <span
                                                :style="
                                                    computeItemStatusStyle(item)
                                                "
                                            >
                                                {{
                                                    computeItemStatusLabel(item)
                                                }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Returns History -->
                        <div
                            class="packaging-details-section"
                            v-if="
                                getReturnsForRecord(activeRecord.id).length > 0
                            "
                        >
                            <div class="packaging-section-header">
                                <div class="packaging-section-title-group">
                                    <h4 style="margin: 0; color: #333">
                                        Historique des Retours
                                    </h4>
                                    <span class="packaging-returns-count">
                                        {{ returnsTotalCount }} retour(s)
                                    </span>
                                </div>
                                <button
                                    @click="printReturnsHistory(activeRecord)"
                                    class="packaging-btn-print-history"
                                    title="Imprimer l'historique des retours"
                                >
                                    <i class="fas fa-print"></i>
                                    <span>Imprimer</span>
                                </button>
                            </div>

                            <table
                                class="packaging-details-tbl packaging-responsive-tbl"
                            >
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th>Quantité Retournée</th>
                                        <th>Date de Retour</th>
                                        <th>Commentaire</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="ret in pagedReturns"
                                        :key="ret.id"
                                    >
                                        <td data-label="Produit">
                                            {{
                                                findProductLabel(ret.product_id)
                                            }}
                                        </td>
                                        <td data-label="Quantité Retournée">
                                            {{ ret.quantity_returned }}
                                        </td>
                                        <td data-label="Date de Retour">
                                            {{ formatDateDisplay(ret.date) }}
                                        </td>
                                        <td data-label="Commentaire">
                                            {{ ret.comment || '-' }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Pagination historique des retours -->
                            <div class="packaging-returns-pagination">
                                <span class="packaging-returns-page-info">
                                    {{
                                        (returnsPageNum - 1) * returnsPerPage +
                                        1
                                    }}-{{
                                        Math.min(
                                            returnsPageNum * returnsPerPage,
                                            returnsTotalCount,
                                        )
                                    }}
                                    sur {{ returnsTotalCount }}
                                </span>

                                <div class="packaging-pagination-controls">
                                    <button
                                        @click="
                                            returnsPageNum = Math.max(
                                                1,
                                                returnsPageNum - 1,
                                            )
                                        "
                                        :disabled="returnsPageNum === 1"
                                        class="packaging-btn-page packaging-btn-page-arrow"
                                    >
                                        <i class="fas fa-chevron-left"></i>
                                    </button>

                                    <div class="packaging-returns-page-pills">
                                        <button
                                            v-for="p in returnsPageCount"
                                            :key="p"
                                            @click="returnsPageNum = p"
                                            class="packaging-page-pill"
                                            :class="{
                                                active: p === returnsPageNum,
                                            }"
                                        >
                                            {{ p }}
                                        </button>
                                    </div>

                                    <button
                                        @click="
                                            returnsPageNum = Math.min(
                                                returnsPageCount,
                                                returnsPageNum + 1,
                                            )
                                        "
                                        :disabled="
                                            returnsPageNum ===
                                                returnsPageCount ||
                                            returnsPageCount === 0
                                        "
                                        class="packaging-btn-page packaging-btn-page-arrow"
                                    >
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>

                                <div class="packaging-per-page">
                                    <label>Lignes :</label>
                                    <select
                                        v-model.number="returnsPerPage"
                                        @change="returnsPageNum = 1"
                                        class="packaging-per-page-select"
                                    >
                                        <option :value="3">3</option>
                                        <option :value="5">5</option>
                                        <option :value="10">10</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal d'enregistrement des retours -->
            <div
                v-if="isReturnFormOpen"
                class="packaging-modal-bg"
                @click.self="hideReturnForm"
            >
                <div class="packaging-modal-box packaging-modal-lg">
                    <div class="packaging-modal-top">
                        <h5>
                            <i class="fas fa-reply"></i>
                            Enregistrer les Retours
                        </h5>
                        <button
                            @click="hideReturnForm"
                            class="packaging-modal-x"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="packaging-modal-content" v-if="activeRecord">
                        <div class="packaging-return-info-box">
                            <div class="packaging-info-row">
                                <span class="packaging-info-key">Client :</span>
                                <span class="packaging-info-val">
                                    {{ activeRecord.client_name }}
                                </span>
                            </div>
                            <div class="packaging-info-row">
                                <span class="packaging-info-key">
                                    Référence :
                                </span>
                                <span class="packaging-info-val">
                                    {{ activeRecord.reference }}
                                </span>
                            </div>
                        </div>

                        <div class="packaging-return-items-area">
                            <h4 style="margin-bottom: 1rem; color: #333">
                                Sélectionner les Produits Retournés
                            </h4>

                            <div
                                v-if="
                                    getPendingItemsForReturn(activeRecord.id)
                                        .length === 0
                                "
                                class="packaging-no-items-msg"
                            >
                                <i class="fas fa-check-circle"></i>
                                <p>
                                    Tous les produits ont déjà été retournés
                                    pour cette transaction.
                                </p>
                            </div>

                            <div
                                v-for="item in getPendingItemsForReturn(
                                    activeRecord.id,
                                )"
                                :key="item.id"
                                class="packaging-return-item-box"
                                :class="{
                                    selected:
                                        returnFormData[item.id] &&
                                        returnFormData[item.id].isSelected,
                                }"
                            >
                                <div class="packaging-return-item-row">
                                    <label class="packaging-checkbox-wrap">
                                        <input
                                            type="checkbox"
                                            v-model="
                                                returnFormData[item.id]
                                                    .isSelected
                                            "
                                            @change="
                                                onReturnItemToggle(item.id)
                                            "
                                        />
                                        <span
                                            class="packaging-checkmark"
                                        ></span>
                                    </label>

                                    <div class="packaging-return-item-info">
                                        <span
                                            class="packaging-return-item-name"
                                        >
                                            {{
                                                findProductLabel(
                                                    item.product_id,
                                                )
                                            }}
                                        </span>
                                        <div class="packaging-return-item-qtys">
                                            <span
                                                class="packaging-qty-tag packaging-qty-given"
                                            >
                                                Remis: {{ item.quantity_given }}
                                            </span>
                                            <span
                                                class="packaging-qty-tag packaging-qty-back"
                                            >
                                                Déjà retourné:
                                                {{
                                                    getRealReturnedQtyForItem(
                                                        item.id,
                                                    ).toFixed(2)
                                                }}
                                            </span>
                                            <span
                                                class="packaging-qty-tag packaging-qty-wait"
                                            >
                                                En attente:
                                                {{
                                                    (
                                                        parseFloat(
                                                            item.quantity_given,
                                                        ) -
                                                        getRealReturnedQtyForItem(
                                                            item.id,
                                                        )
                                                    ).toFixed(2)
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    v-if="
                                        returnFormData[item.id] &&
                                        returnFormData[item.id].isSelected
                                    "
                                    class="packaging-return-qty-section"
                                >
                                    <label class="packaging-return-qty-label">
                                        Quantité retournée :
                                    </label>
                                    <div class="packaging-return-qty-box">
                                        <input
                                            type="number"
                                            v-model.number="
                                                returnFormData[item.id].qtyValue
                                            "
                                            min="0.01"
                                            step="0.01"
                                            :max="
                                                parseFloat(
                                                    item.quantity_given,
                                                ) -
                                                getRealReturnedQtyForItem(
                                                    item.id,
                                                )
                                            "
                                            class="packaging-input packaging-return-qty-field"
                                            placeholder="Quantité"
                                        />
                                        <span class="packaging-max-qty-hint">
                                            Max:
                                            {{
                                                (
                                                    parseFloat(
                                                        item.quantity_given,
                                                    ) -
                                                    getRealReturnedQtyForItem(
                                                        item.id,
                                                    )
                                                ).toFixed(2)
                                            }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="packaging-form-field">
                            <label>Date de retour *</label>
                            <input
                                type="date"
                                v-model="returnDateValue"
                                class="packaging-input"
                                required
                            />
                        </div>

                        <div class="packaging-form-field">
                            <label>Commentaire</label>
                            <textarea
                                v-model="returnNoteValue"
                                class="packaging-input"
                                rows="3"
                                placeholder="Remarques sur le retour..."
                            ></textarea>
                        </div>
                    </div>
                    <div class="packaging-modal-bottom">
                        <button
                            @click="hideReturnForm"
                            class="packaging-btn-alt"
                        >
                            <i class="fas fa-times"></i>
                            Annuler
                        </button>
                        <button
                            @click="saveReturnData"
                            :disabled="!isReturnFormValid"
                            class="packaging-btn-main"
                        >
                            <i class="fas fa-save"></i>
                            Enregistrer Retour
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal: Supprimer Retour -->
            <div
                v-if="isDeleteReturnOpen"
                class="packaging-modal-bg"
                @click.self="hideDeleteReturnModal"
            >
                <div class="packaging-modal-box packaging-modal-confirm">
                    <div class="packaging-modal-top">
                        <h5>
                            <i class="fas fa-exclamation-triangle"></i>
                            Confirmer la suppression du retour
                        </h5>
                    </div>
                    <div class="packaging-modal-content">
                        <p>
                            Êtes-vous sûr de vouloir supprimer ce retour de
                            {{ findProductLabel(returnToRemove?.product_id) }} ?
                        </p>
                        <p v-if="returnToRemove">
                            Quantité:
                            <strong>
                                {{ returnToRemove.quantity_returned }}
                            </strong>
                        </p>
                    </div>
                    <div class="packaging-modal-bottom">
                        <button
                            @click="hideDeleteReturnModal"
                            class="packaging-btn-alt"
                        >
                            <i class="fas fa-times"></i>
                            Annuler
                        </button>
                        <button
                            @click="confirmDeleteReturn"
                            class="packaging-btn-danger"
                        >
                            <i class="fas fa-trash"></i>
                            Supprimer
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal: Annuler Transaction -->
            <div
                v-if="isCancelConfirmOpen"
                class="packaging-modal-bg"
                @click.self="hideCancelConfirm"
            >
                <div class="packaging-modal-box packaging-modal-confirm">
                    <div class="packaging-modal-top">
                        <h5>
                            <i class="fas fa-exclamation-triangle"></i>
                            Confirmer l'annulation
                        </h5>
                    </div>
                    <div class="packaging-modal-content">
                        <p>
                            Êtes-vous sûr de vouloir annuler cette opération de
                            remise ?
                        </p>
                        <p v-if="recordToCancel">
                            <strong>Client :</strong>
                            {{ recordToCancel.client_name }}
                            <br />
                            <strong>Référence :</strong>
                            {{ recordToCancel.reference }}
                        </p>
                    </div>
                    <div class="packaging-modal-bottom">
                        <button
                            @click="hideCancelConfirm"
                            class="packaging-btn-alt"
                        >
                            <i class="fas fa-times"></i>
                            Retour
                        </button>
                        <button
                            @click="confirmCancelRecord"
                            class="packaging-btn-danger"
                        >
                            <i class="fas fa-times"></i>
                            Oui, Annuler
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal: Nouveau Client -->
            <div
                v-if="isAddClientOpen"
                class="packaging-modal-bg"
                @click.self="hideAddClientModal"
            >
                <div class="packaging-modal-box">
                    <div class="packaging-modal-top">
                        <h5>
                            <i class="fas fa-user-plus"></i>
                            Nouveau Client
                        </h5>
                        <button
                            @click="hideAddClientModal"
                            class="packaging-modal-x"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="packaging-modal-content">
                        <form @submit.prevent="saveNewClient">
                            <div class="packaging-form-field">
                                <label>Nom du client *</label>
                                <input
                                    type="text"
                                    class="packaging-input"
                                    v-model="newClientData.clientName"
                                    placeholder="Nom complet"
                                    required
                                />
                            </div>
                            <div class="packaging-form-field">
                                <label>Téléphone *</label>
                                <input
                                    type="text"
                                    class="packaging-input"
                                    v-model="newClientData.clientPhone"
                                    placeholder="Numéro de téléphone"
                                    required
                                />
                            </div>
                            <div class="packaging-form-field">
                                <label>Adresse</label>
                                <textarea
                                    class="packaging-input"
                                    v-model="newClientData.clientAddress"
                                    placeholder="Adresse complète"
                                    rows="3"
                                ></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="packaging-modal-bottom">
                        <button
                            @click="hideAddClientModal"
                            class="packaging-btn-alt"
                        >
                            <i class="fas fa-times"></i>
                            Annuler
                        </button>
                        <button
                            @click="saveNewClient"
                            :disabled="
                                !newClientData.clientName ||
                                !newClientData.clientPhone
                            "
                            class="packaging-btn-main"
                        >
                            <i class="fas fa-save"></i>
                            Créer le Client
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<script>
    export default {
        name: 'ReturnableProductsManager',
        data() {
            return {
                recordsList: [],
                recordItemsList: [],
                returnsList: [],
                productsCatalog: [],
                clientsCatalog: [],

                txtSearch: '',
                selectedClientFilter: '',
                selectedStatusFilter: '',
                dateFilterFrom: '',
                dateFilterTo: '',
                selectedSortBy: 'Date (récent)',

                pageNum: 1,
                rowsPerPage: 20,

                isNewDeliveryOpen: false,
                isDetailsOpen: false,
                isReturnFormOpen: false,
                isCancelConfirmOpen: false,
                isDeleteReturnOpen: false,
                isAddClientOpen: false,
                isClientListVisible: false,

                activeRecord: null,
                recordToCancel: null,
                returnToRemove: null,

                returnDateValue: '',
                returnNoteValue: '',
                returnFormData: {},

                newDeliveryData: {
                    clientSearchText: '',
                    chosenClient: null,
                    itemRows: [
                        {
                            pickedProduct: null,
                            productSearchText: '',
                            isDropdownOpen: false,
                            qtyAmount: '',
                        },
                    ],
                    noteText: '',
                },
                matchingClients: [],

                newClientData: {
                    clientName: '',
                    clientPhone: '',
                    clientAddress: '',
                },

                // Pagination historique des retours (modal détails)
                returnsPageNum: 1,
                returnsPerPage: 20,
            };
        },

        computed: {
            filteredRecordsList() {
                return this.applyFiltersAndSort();
            },
            displayedRecords() {
                const startIdx = (this.pageNum - 1) * this.rowsPerPage;
                return this.filteredRecordsList.slice(
                    startIdx,
                    startIdx + this.rowsPerPage,
                );
            },
            pageCount() {
                return Math.max(
                    1,
                    Math.ceil(
                        this.filteredRecordsList.length / this.rowsPerPage,
                    ),
                );
            },
            clientOptions() {
                const clientMap = new Map();
                if (Array.isArray(this.recordsList)) {
                    this.recordsList.forEach((rec) => {
                        if (rec.client_id && rec.client_name) {
                            clientMap.set(rec.client_id, {
                                id: rec.client_id,
                                name: rec.client_name,
                            });
                        }
                    });
                }
                return Array.from(clientMap.values());
            },
            isDeliveryFormValid() {
                if (!this.newDeliveryData.chosenClient) return false;
                return this.newDeliveryData.itemRows.some(
                    (row) => row.pickedProduct && row.qtyAmount > 0,
                );
            },
            isReturnFormValid() {
                if (
                    !this.returnFormData ||
                    typeof this.returnFormData !== 'object'
                )
                    return false;
                return Object.values(this.returnFormData).some(
                    (entry) => entry && entry.isSelected && entry.qtyValue > 0,
                );
            },
            pagedReturns() {
                if (!this.activeRecord) return [];
                const all = this.getReturnsForRecord(this.activeRecord.id);
                const start = (this.returnsPageNum - 1) * this.returnsPerPage;
                return all.slice(start, start + this.returnsPerPage);
            },
            returnsPageCount() {
                if (!this.activeRecord) return 0;
                return Math.ceil(
                    this.getReturnsForRecord(this.activeRecord.id).length /
                        this.returnsPerPage,
                );
            },
            returnsTotalCount() {
                if (!this.activeRecord) return 0;
                return this.getReturnsForRecord(this.activeRecord.id).length;
            },
        },

        async mounted() {
            await this.loadAllData();
        },

        methods: {
            async loadAllData() {
                try {
                    await Promise.all([
                        this.loadRecords(),
                        this.loadRecordItems(),
                        this.loadReturns(),
                        this.loadProducts(),
                        this.loadClients(),
                    ]);
                } catch (err) {
                    console.error(
                        '[PackagingManager] Error loading data:',
                        err,
                    );
                }
            },

            async loadRecords() {
                try {
                    const res = await axios.get(
                        `/returnable-products-transactions`,
                    );
                    this.recordsList = Array.isArray(res.data) ? res.data : [];
                } catch (err) {
                    this.recordsList = [];
                }
            },

            async loadRecordItems() {
                try {
                    const res = await axios.get(`/returnable-products-list`);
                    const rawData = Array.isArray(res.data) ? res.data : [];
                    this.recordItemsList = rawData.map((item) => ({
                        ...item,
                        quantity_given: parseFloat(item.quantity_given) || 0,
                        quantity_returned:
                            parseFloat(item.quantity_returned) || 0,
                    }));
                } catch (err) {
                    this.recordItemsList = [];
                }
            },

            async loadReturns() {
                try {
                    const res = await axios.get(`/returnable-products-returns`);
                    const rawData = Array.isArray(res.data) ? res.data : [];
                    this.returnsList = rawData.map((ret) => ({
                        ...ret,
                        quantity_returned:
                            parseFloat(ret.quantity_returned) || 0,
                    }));
                } catch (err) {
                    this.returnsList = [];
                }
            },

            async loadProducts() {
                try {
                    const res = await axios.get(`/productsList`);
                    this.productsCatalog = Array.isArray(res.data)
                        ? res.data
                        : [];
                } catch (err) {
                    this.productsCatalog = [];
                }
            },

            async loadClients() {
                try {
                    const res = await axios.get(`/clientslist`);
                    this.clientsCatalog = Array.isArray(res.data)
                        ? res.data
                        : [];
                } catch (err) {
                    this.clientsCatalog = [];
                }
            },

            applyFiltersAndSort() {
                if (!Array.isArray(this.recordsList)) return [];
                let result = [...this.recordsList];

                if (this.txtSearch) {
                    const query = this.txtSearch.toLowerCase();
                    result = result.filter(
                        (rec) =>
                            (rec.client_name &&
                                rec.client_name
                                    .toLowerCase()
                                    .includes(query)) ||
                            (rec.reference &&
                                rec.reference.toLowerCase().includes(query)) ||
                            this.getItemsForRecord(rec.id).some((item) =>
                                this.findProductLabel(item.product_id)
                                    .toLowerCase()
                                    .includes(query),
                            ),
                    );
                }

                if (this.selectedClientFilter) {
                    result = result.filter(
                        (rec) =>
                            Number(rec.client_id) ===
                            Number(this.selectedClientFilter),
                    );
                }

                if (this.selectedStatusFilter) {
                    result = result.filter(
                        (rec) =>
                            this.computeStatus(rec) ===
                            this.selectedStatusFilter,
                    );
                }

                if (this.dateFilterFrom || this.dateFilterTo) {
                    result = result.filter((rec) => {
                        const recDate = new Date(rec.date);
                        const fromDate = this.dateFilterFrom
                            ? new Date(this.dateFilterFrom)
                            : null;
                        const toDate = this.dateFilterTo
                            ? new Date(this.dateFilterTo)
                            : null;
                        if (fromDate && toDate)
                            return recDate >= fromDate && recDate <= toDate;
                        if (fromDate) return recDate >= fromDate;
                        if (toDate) return recDate <= toDate;
                        return true;
                    });
                }

                return this.sortRecords(result);
            },

            resetDateFilters() {
                this.dateFilterFrom = '';
                this.dateFilterTo = '';
            },

            sortRecords(records) {
                const sorted = [...records];
                switch (this.selectedSortBy) {
                    case 'Date (ancien)':
                        return sorted.sort(
                            (a, b) => new Date(a.date) - new Date(b.date),
                        );
                    case 'Client (A-Z)':
                        return sorted.sort((a, b) =>
                            (a.client_name || '').localeCompare(
                                b.client_name || '',
                            ),
                        );
                    case 'Quantité restante (croissant)':
                        return sorted.sort(
                            (a, b) =>
                                parseFloat(this.sumPendingQty(a.id)) -
                                parseFloat(this.sumPendingQty(b.id)),
                        );
                    case 'Quantité restante (décroissant)':
                        return sorted.sort(
                            (a, b) =>
                                parseFloat(this.sumPendingQty(b.id)) -
                                parseFloat(this.sumPendingQty(a.id)),
                        );
                    default:
                        return sorted.sort(
                            (a, b) => new Date(b.date) - new Date(a.date),
                        );
                }
            },

            getItemsForRecord(recordId) {
                if (!Array.isArray(this.recordItemsList)) return [];
                return this.recordItemsList.filter(
                    (item) =>
                        Number(item.returnable_product_id) === Number(recordId),
                );
            },

            getRealReturnedQtyForItem(itemId) {
                if (!Array.isArray(this.returnsList)) return 0;
                return this.returnsList
                    .filter(
                        (ret) =>
                            Number(ret.returnable_product_id) ===
                            Number(itemId),
                    )
                    .reduce(
                        (sum, ret) =>
                            sum + (parseFloat(ret.quantity_returned) || 0),
                        0,
                    );
            },

            getPendingItemsForReturn(recordId) {
                return this.getItemsForRecord(recordId).filter((item) => {
                    const given = parseFloat(item.quantity_given) || 0;
                    const returned = this.getRealReturnedQtyForItem(item.id);
                    return given > returned;
                });
            },

            getReturnsForRecord(recordId) {
                if (!Array.isArray(this.returnsList)) return [];
                const itemIds = this.getItemsForRecord(recordId).map((item) =>
                    Number(item.id),
                );
                return this.returnsList.filter((ret) =>
                    itemIds.includes(Number(ret.returnable_product_id)),
                );
            },

            countProductsInRecord(recordId) {
                return this.getItemsForRecord(recordId).length;
            },

            sumGivenQty(recordId) {
                return this.getItemsForRecord(recordId)
                    .reduce(
                        (total, item) =>
                            total + (parseFloat(item.quantity_given) || 0),
                        0,
                    )
                    .toFixed(2);
            },

            sumReturnedQty(recordId) {
                return this.getItemsForRecord(recordId)
                    .reduce(
                        (total, item) =>
                            total + this.getRealReturnedQtyForItem(item.id),
                        0,
                    )
                    .toFixed(2);
            },

            sumPendingQty(recordId) {
                return (
                    parseFloat(this.sumGivenQty(recordId)) -
                    parseFloat(this.sumReturnedQty(recordId))
                ).toFixed(2);
            },

            // â”€â”€â”€ BUG FIX 1 â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
            // Utilisation de Number() pour la comparaison afin d'éviter les
            // problèmes de type string vs integer (PHP renvoie des strings).
            canCancelRecordLegacy(record) {
                if (!record || record.status === 'Annulé') return false;
                return (parseFloat(this.sumReturnedQty(record.id)) || 0) === 0;
            },

            findProductLabel(productId) {
                if (!productId && productId !== 0) return 'Produit inconnu';
                if (!Array.isArray(this.productsCatalog))
                    return 'Produit inconnu';
                const prod = this.productsCatalog.find(
                    (p) => Number(p.id) === Number(productId),
                );
                return prod ? prod.name : `Produit #${productId} inconnu`;
            },
            // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

            computeStatus(record) {
                if (record.status === 'Annulé') return 'cancelled';
                const pending = parseFloat(this.sumPendingQty(record.id)) || 0;
                const given = parseFloat(this.sumGivenQty(record.id)) || 0;
                if (pending === 0 && given > 0) return 'complete';
                const returned =
                    parseFloat(this.sumReturnedQty(record.id)) || 0;
                if (returned > 0) return 'partial';
                return 'pending';
            },

            computeStatusLabel(record) {
                switch (this.computeStatus(record)) {
                    case 'complete':
                        return 'Complètement retourné';
                    case 'partial':
                        return 'Partiellement retourné';
                    case 'cancelled':
                        return 'Annulé';
                    default:
                        return 'Non retourné';
                }
            },

            computeStatusStyle(record) {
                const status = this.computeStatus(record);
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
                    cancelled: {
                        bg: '#f5f5f5',
                        color: '#757575',
                        border: '#bdbdbd',
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

            computeRowStyle(record) {
                return `row-${this.computeStatus(record)}`;
            },

            computeItemStatusLabel(item) {
                const given = parseFloat(item.quantity_given) || 0;
                const returned = this.getRealReturnedQtyForItem(item.id);
                if (given > 0 && returned >= given)
                    return 'Complètement retourné';
                if (returned > 0) return 'Partiellement retourné';
                return 'Non retourné';
            },

            computeItemStatusStyle(item) {
                const given = parseFloat(item.quantity_given) || 0;
                const returned = this.getRealReturnedQtyForItem(item.id);
                const pending = given - returned;
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
                let status = 'pending';
                if (pending === 0) status = 'complete';
                else if (returned > 0) status = 'partial';
                const style = styles[status];
                return {
                    backgroundColor: style.bg,
                    color: style.color,
                    border: `1px solid ${style.border}`,
                    padding: '0.3rem 0.6rem',
                    borderRadius: '12px',
                    fontSize: '0.8rem',
                    fontWeight: '600',
                    display: 'inline-block',
                };
            },

            showNewDeliveryModal() {
                this.newDeliveryData = {
                    clientSearchText: '',
                    chosenClient: null,
                    itemRows: [
                        {
                            pickedProduct: null,
                            productSearchText: '',
                            isDropdownOpen: false,
                            qtyAmount: '',
                        },
                    ],
                    noteText: '',
                };
                this.matchingClients = Array.isArray(this.clientsCatalog)
                    ? [...this.clientsCatalog]
                    : [];
                this.isNewDeliveryOpen = true;
            },

            hideNewDeliveryModal() {
                this.isNewDeliveryOpen = false;
                this.newDeliveryData.clientSearchText = '';
                this.newDeliveryData.chosenClient = null;
            },

            showRecordDetails(record) {
                this.activeRecord = record;
                this.returnsPageNum = 1;
                this.isDetailsOpen = true;
            },

            hideDetailsModal() {
                this.isDetailsOpen = false;
                this.activeRecord = null;
            },

            showReturnForm(record) {
                this.activeRecord = record;
                this.returnDateValue = new Date().toISOString().split('T')[0];
                this.returnNoteValue = '';
                const pendingItems = this.getPendingItemsForReturn(record.id);
                const formData = {};
                pendingItems.forEach((item) => {
                    if (item && item.id !== undefined) {
                        const given = parseFloat(item.quantity_given) || 0;
                        const returned = this.getRealReturnedQtyForItem(
                            item.id,
                        );
                        formData[item.id] = {
                            isSelected: false,
                            qtyValue: '',
                            productId: item.product_id,
                            maxQty: given - returned,
                        };
                    }
                });
                this.returnFormData = formData;
                this.isReturnFormOpen = true;
            },

            hideReturnForm() {
                this.isReturnFormOpen = false;
                this.activeRecord = null;
                this.returnFormData = {};
            },

            onReturnItemToggle(itemId) {
                if (
                    this.returnFormData[itemId] &&
                    !this.returnFormData[itemId].isSelected
                ) {
                    this.returnFormData[itemId].qtyValue = '';
                }
            },

            showDeleteReturnModal(ret) {
                this.returnToRemove = ret;
                this.isDeleteReturnOpen = true;
            },

            hideDeleteReturnModal() {
                this.isDeleteReturnOpen = false;
                this.returnToRemove = null;
            },

            canCancelRecord(record) {
                if (!record) return false;
                if (String(record.status || '').toLowerCase().includes('annul')) {
                    return false;
                }
                return (parseFloat(this.sumReturnedQty(record.id)) || 0) === 0;
            },

            showCancelConfirm(record) {
                if (!this.canCancelRecord(record)) return;
                this.recordToCancel = record;
                this.isCancelConfirmOpen = true;
            },

            hideCancelConfirm() {
                this.isCancelConfirmOpen = false;
                this.recordToCancel = null;
            },

            searchClientsForDelivery() {
                const query = (
                    this.newDeliveryData.clientSearchText || ''
                ).toLowerCase();
                if (!query) {
                    this.matchingClients = Array.isArray(this.clientsCatalog)
                        ? [...this.clientsCatalog]
                        : [];
                } else {
                    this.matchingClients = (this.clientsCatalog || []).filter(
                        (c) =>
                            (c.name && c.name.toLowerCase().includes(query)) ||
                            (c.phone && c.phone.includes(query)),
                    );
                }
                this.isClientListVisible = true;
            },

            pickClientForDelivery(client) {
                this.newDeliveryData.chosenClient = client;
                this.newDeliveryData.clientSearchText = client.name;
                this.isClientListVisible = false;
            },

            showAddClientModal() {
                this.newClientData.clientName =
                    this.newDeliveryData.clientSearchText;
                this.newClientData.clientPhone = '';
                this.newClientData.clientAddress = '';
                this.isAddClientOpen = true;
                this.isClientListVisible = false;
            },

            hideAddClientModal() {
                this.isAddClientOpen = false;
                this.newClientData = {
                    clientName: '',
                    clientPhone: '',
                    clientAddress: '',
                };
            },

            async saveNewClient() {
                if (
                    !this.newClientData.clientName ||
                    !this.newClientData.clientPhone
                ) {
                    alert('Veuillez remplir tous les champs obligatoires');
                    return;
                }
                try {
                    const res = await axios.post(`/clients`, {
                        name: this.newClientData.clientName,
                        phone: this.newClientData.clientPhone,
                        address: this.newClientData.clientAddress,
                    });
                    if (res.data.success) {
                        alert('Client créé avec succès');
                        await this.loadClients();
                        const newClient = this.clientsCatalog.find(
                            (c) =>
                                c.name === this.newClientData.clientName &&
                                c.phone === this.newClientData.clientPhone,
                        );
                        if (newClient) this.pickClientForDelivery(newClient);
                        this.hideAddClientModal();
                    } else {
                        alert(
                            res.data.message ||
                                'Erreur lors de la création du client',
                        );
                    }
                } catch (err) {
                    alert(
                        'Erreur lors de la création du client: ' +
                            (err.response?.data?.message || err.message),
                    );
                }
            },

            onItemSearch(idx) {
                this.newDeliveryData.itemRows[idx].isDropdownOpen = true;
            },

            getAvailableProducts(idx) {
                const query = (
                    this.newDeliveryData.itemRows[idx].productSearchText || ''
                ).toLowerCase();
                const selectedIds = this.newDeliveryData.itemRows
                    .filter((_, i) => i !== idx && _.pickedProduct)
                    .map((row) => row.pickedProduct.id);
                if (!Array.isArray(this.productsCatalog)) return [];
                return this.productsCatalog.filter(
                    (p) =>
                        p.name &&
                        p.name.toLowerCase().includes(query) &&
                        !selectedIds.includes(p.id) &&
                        (p.isReturnable === 1 ||
                            p.isReturnable === '1' ||
                            p.isReturnable === true),
                );
            },

            pickProductForRow(idx, product) {
                this.newDeliveryData.itemRows[idx].pickedProduct = product;
                this.newDeliveryData.itemRows[idx].productSearchText =
                    product.name;
                this.newDeliveryData.itemRows[idx].isDropdownOpen = false;
            },

            addNewItemRow() {
                this.newDeliveryData.itemRows.push({
                    pickedProduct: null,
                    productSearchText: '',
                    isDropdownOpen: false,
                    qtyAmount: '',
                });
            },

            deleteItemRow(idx) {
                this.newDeliveryData.itemRows.splice(idx, 1);
            },

            async saveNewDelivery() {
                if (!this.isDeliveryFormValid) {
                    alert('Veuillez compléter le formulaire');
                    return;
                }
                const items = this.newDeliveryData.itemRows
                    .filter((row) => row.pickedProduct && row.qtyAmount > 0)
                    .map((row) => ({
                        product_id: row.pickedProduct.id,
                        quantity_given: Number(row.qtyAmount),
                    }));

                const now = new Date();
                const formatter = new Intl.DateTimeFormat('sv-SE', {
                    timeZone: 'Africa/Lagos',
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false,
                });
                const datetime = formatter.format(now).replace('T', ' ');
                const reference = `REM-${datetime.slice(0, 10).replace(/-/g, '')}-${Math.random().toString(36).slice(2, 5).toUpperCase()}`;

                try {
                    const res = await axios.post(`/returnable-products`, {
                        client_id: this.newDeliveryData.chosenClient.id,
                        date: datetime,
                        comment: this.newDeliveryData.noteText,
                        reference,
                        items,
                    });
                    if (res.data.success) {
                        alert('Remise enregistrée');
                        this.hideNewDeliveryModal();
                        await this.loadAllData();
                    } else {
                        alert(res.data.message || 'Erreur serveur');
                    }
                } catch (err) {
                    alert(err.response?.data?.message || err.message);
                }
            },

            // â”€â”€â”€ BUG FIX 2 â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
            // Correction de la construction de selectedItems :
            // - On cherche le bon item dans recordItemsList (pas seulement les
            //   items en attente) pour éviter toute perte si l'item n'est plus
            //   dans getPendingItemsForReturn au moment du traitement.
            // - La payload envoyée au serveur contient uniquement les champs
            //   nécessaires ; le returnable_product_id au niveau racine est
            //   l'ID de la transaction (activeRecord.id) et chaque item porte
            //   son propre returnable_product_id (ID de l'item de liste).
            async saveReturnData() {
                if (!this.isReturnFormValid) {
                    alert(
                        'Veuillez sélectionner au moins un produit et entrer une quantité à retourner',
                    );
                    return;
                }

                const selectedItems = Object.entries(this.returnFormData)
                    .filter(
                        ([_, entry]) =>
                            entry && entry.isSelected && entry.qtyValue > 0,
                    )
                    .map(([itemId, entry]) => {
                        const numericId = Number(itemId);
                        // Chercher dans TOUS les items de la transaction,
                        // pas uniquement les items en attente
                        const item = this.getItemsForRecord(
                            this.activeRecord.id,
                        ).find((p) => Number(p.id) === numericId);
                        return {
                            returnable_product_id: numericId,   // ID de l'item (ligne produit)
                            product_id: item ? Number(item.product_id) : Number(entry.productId),
                            quantity_returned: parseFloat(entry.qtyValue) || 0,
                        };
                    });

                if (selectedItems.length === 0) {
                    alert('Aucune quantité à retourner sélectionnée.');
                    return;
                }

                try {
                    const res = await axios.post(
                        `/returnable-products-returns`,
                        {
                            returnable_product_id: this.activeRecord.id,
                            date: this.returnDateValue,
                            comment: this.returnNoteValue,
                            items: selectedItems,
                        },
                    );
                    alert('Retours enregistrés avec succès');
                    this.hideReturnForm();
                    await this.loadAllData();
                } catch (err) {
                    alert(
                        "Erreur lors de l'enregistrement des retours: " +
                            (err.response?.data?.message || err.message),
                    );
                }
            },
            // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

            async confirmDeleteReturn() {
                if (!this.returnToRemove) return;
                try {
                    await axios.post(
                        `/returnable-products-delete-return/${this.returnToRemove.id}`,
                    );
                    alert('Retour supprimé avec succès');
                    this.hideDeleteReturnModal();
                    await this.loadAllData();
                } catch (err) {
                    alert(
                        'Erreur lors de la suppression: ' +
                            (err.response?.data?.message || err.message),
                    );
                }
            },

            async confirmCancelRecord() {
                if (!this.recordToCancel) return;
                try {
                    await axios.post(
                        `/returnable-products-cancel/${this.recordToCancel.id}`,
                    );
                    alert('Opération annulée avec succès');
                    this.hideCancelConfirm();
                    await this.loadAllData();
                } catch (err) {
                    alert(
                        'Erreur annulation: ' +
                            (err.response?.data?.message || err.message),
                    );
                }
            },

            formatDateDisplay(date) {
                if (!date) return 'N/A';
                return new Date(date).toLocaleString('fr-FR', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                });
            },

            printAllRecords() {
                const printWindow = window.open('', '', 'height=600,width=900');
                printWindow.document.write(
                    this.buildPrintAllHtml(this.displayedRecords),
                );
                printWindow.document.close();
                setTimeout(() => printWindow.print(), 250);
            },

            printSingleRecord(record) {
                const printWindow = window.open('', '', 'height=600,width=900');
                printWindow.document.write(
                    this.buildPrintSingleHtml(
                        record,
                        this.getItemsForRecord(record.id),
                    ),
                );
                printWindow.document.close();
                setTimeout(() => printWindow.print(), 250);
            },

            printReturnsHistory(record) {
                const returns = this.getReturnsForRecord(record.id);
                if (returns.length === 0) {
                    alert('Aucun retour enregistré pour cette transaction.');
                    return;
                }
                const printWindow = window.open('', '', 'height=600,width=900');
                printWindow.document.write(
                    this.buildPrintReturnsHistoryHtml(record, returns),
                );
                printWindow.document.close();
                setTimeout(() => printWindow.print(), 250);
            },

            buildPrintReturnsHistoryHtml(record, returns) {
                const now = new Date().toLocaleString('fr-FR');
                const items = this.getItemsForRecord(record.id);

                // Grouper les retours par produit pour le résumé
                const summaryMap = {};
                returns.forEach((ret) => {
                    const label = this.findProductLabel(ret.product_id);
                    if (!summaryMap[label]) summaryMap[label] = 0;
                    summaryMap[label] += parseFloat(ret.quantity_returned) || 0;
                });

                // Lignes du tableau principal
                let returnRows = '';
                let totalReturned = 0;
                returns.forEach((ret) => {
                    const qty = parseFloat(ret.quantity_returned) || 0;
                    totalReturned += qty;
                    returnRows += `<tr>
                        <td>${this.findProductLabel(ret.product_id)}</td>
                        <td class="qty-col">${qty.toFixed(2)}</td>
                        <td>${this.formatDateDisplay(ret.date)}</td>
                        <td>${ret.comment || '-'}</td>
                    </tr>`;
                });

                // Lignes du résumé par produit
                let summaryRows = '';
                Object.entries(summaryMap).forEach(([name, qty]) => {
                    // Trouver la quantité remise pour ce produit
                    const item = items.find(
                        (i) => this.findProductLabel(i.product_id) === name,
                    );
                    const given = item
                        ? parseFloat(item.quantity_given) || 0
                        : 0;
                    const pending = given - qty;
                    const statusLabel =
                        pending <= 0
                            ? 'Complètement retourné'
                            : qty > 0
                              ? 'Partiellement retourné'
                              : 'Non retourné';
                    const statusColor = pending <= 0 ? '#0f5132' : '#856404';
                    const statusBg = pending <= 0 ? '#d1e7dd' : '#fff3cd';
                    summaryRows += `<tr>
                        <td>${name}</td>
                        <td class="qty-col">${given.toFixed(2)}</td>
                        <td class="qty-col">${qty.toFixed(2)}</td>
                        <td class="qty-col ${pending > 0 ? 'pending' : ''}">${pending.toFixed(2)}</td>
                        <td><span style="background:${statusBg};color:${statusColor};padding:3px 10px;border-radius:12px;font-size:12px;font-weight:bold;">${statusLabel}</span></td>
                    </tr>`;
                });

                return `<!DOCTYPE html>
<html>
<head>
    <title>Historique des Retours - ${record.reference}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; padding: 24px; background: #f8f9fa; color: #333; }
        .header { text-align:center; border-bottom: 3px solid #667eea; padding-bottom: 18px; margin-bottom: 24px; }
        .header h1 { color: #667eea; font-size: 1.8rem; }
        .header p { color: #666; margin-top: 4px; }
        .card { background: white; border-radius: 10px; padding: 24px; box-shadow: 0 2px 10px rgba(0,0,0,.08); margin-bottom: 20px; }
        .card-title { font-size: 1rem; font-weight: 700; color: #667eea; margin-bottom: 14px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #e8ecff; padding-bottom: 10px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .info-item { display: flex; gap: 8px; font-size: 14px; }
        .info-label { font-weight: 700; color: #667eea; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #667eea; color: white; padding: 11px 14px; text-align: left; font-size: 13px; }
        td { padding: 10px 14px; border-bottom: 1px solid #f0f0f0; font-size: 13px; }
        tr:last-child td { border-bottom: none; }
        tr:nth-child(even) { background: #f9f9fb; }
        .qty-col { text-align: center; font-weight: 600; }
        .pending { color: #c62828; }
        .total-row { background: #eef0ff !important; font-weight: 700; }
        .total-row td { border-top: 2px solid #667eea; font-size: 14px; }
        .badge-count { display: inline-block; background: #667eea; color: white; border-radius: 50%; width: 22px; height: 22px; text-align: center; line-height: 22px; font-size: 12px; font-weight: 700; }
        .footer { text-align: center; color: #999; font-size: 12px; margin-top: 24px; padding-top: 16px; border-top: 1px solid #ddd; }
        @media print { body { padding: 10mm; background: white; } .card { box-shadow: none; border: 1px solid #ddd; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>SAGER</h1>
        <p>Historique des Retours d'Emballages</p>
        <p><strong>Téléphone :</strong> +229 0196466625 &nbsp;|&nbsp; <strong>IFU :</strong> 0202586942320</p>
    </div>

    <!-- Infos transaction -->
    <div class="card">
        <div class="card-title">Informations de la Transaction</div>
        <div class="info-grid">
            <div class="info-item"><span class="info-label">Client :</span><span>${record.client_name}</span></div>
            <div class="info-item"><span class="info-label">Référence :</span><span>${record.reference}</span></div>
            <div class="info-item"><span class="info-label">Date de remise :</span><span>${this.formatDateDisplay(record.date)}</span></div>
            <div class="info-item"><span class="info-label">Date d'impression :</span><span>${now}</span></div>
            <div class="info-item"><span class="info-label">Nombre de retours :</span><span>${returns.length}</span></div>
            <div class="info-item"><span class="info-label">Total retourné :</span><span><strong>${totalReturned.toFixed(2)}</strong></span></div>
        </div>
    </div>

    <!-- Résumé par produit -->
    <div class="card">
        <div class="card-title">Récapitulatif par Produit</div>
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th class="qty-col">Qté Remise</th>
                    <th class="qty-col">Qté Retournée</th>
                    <th class="qty-col">En Attente</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                ${summaryRows}
            </tbody>
        </table>
    </div>

    <!-- Détail chronologique -->
    <div class="card">
        <div class="card-title">Détail Chronologique des Retours <span class="badge-count">${returns.length}</span></div>
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th class="qty-col">Quantité Retournée</th>
                    <th>Date de Retour</th>
                    <th>Commentaire</th>
                </tr>
            </thead>
            <tbody>
                ${returnRows}
                <tr class="total-row">
                    <td>TOTAL</td>
                    <td class="qty-col">${totalReturned.toFixed(2)}</td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Document généré le ${now} - Application SagerMarket</p>
    </div>
</body>
</html>`;
            },

            buildPrintAllHtml(records) {
                let rows = '';
                records.forEach((rec) => {
                    const status = this.computeStatusLabel(rec);
                    const statusClass = status.includes('Non')
                        ? 'status-pending'
                        : status.includes('Partiellement')
                          ? 'status-partial'
                          : 'status-complete';
                    rows += `<tr>
                        <td><strong>${rec.client_name}</strong></td>
                        <td>${rec.reference}</td>
                        <td>${this.formatDateDisplay(rec.date)}</td>
                        <td>${this.countProductsInRecord(rec.id)} produit(s)</td>
                        <td class="qty-center">${this.sumGivenQty(rec.id)}</td>
                        <td class="qty-center">${this.sumReturnedQty(rec.id)}</td>
                        <td class="qty-center">${this.sumPendingQty(rec.id)}</td>
                        <td><span class="status-badge ${statusClass}">${status}</span></td>
                    </tr>`;
                });
                return `<!DOCTYPE html><html><head><title>Produits avec Emballages</title>
                <style>* { margin:0; padding:0; box-sizing:border-box; } body { font-family:Arial,sans-serif; padding:20px; }
                h1 { text-align:center; margin-bottom:20px; color:#333; }
                table { width:100%; border-collapse:collapse; margin-bottom:20px; }
                th, td { padding:12px; text-align:left; border-bottom:1px solid #ddd; }
                th { background:#667eea; color:white; font-weight:bold; }
                tr:hover { background:#f5f5f5; }
                .status-badge { padding:4px 8px; border-radius:4px; font-size:12px; font-weight:bold; }
                .status-pending { background:#fff3cd; color:#856404; }
                .status-partial { background:#cfe2ff; color:#084298; }
                .status-complete { background:#d1e7dd; color:#0f5132; }
                .qty-center { text-align:center; }
                @media print { body { padding:10px; } }
                </style></head><body>
                <h1>Gestion des Produits avec Emballages</h1>
                <p style="text-align:center; margin-bottom:20px;">Remise et retour des produits consignés - ${new Date().toLocaleDateString('fr-FR')}</p>
                <table><thead><tr><th>Client</th><th>Référence</th><th>Date Remise</th><th>Produits</th>
                <th class="qty-center">Qté Remise</th><th class="qty-center">Qté Retournée</th>
                <th class="qty-center">En Attente</th><th>Statut</th></tr></thead>
                <tbody>${rows}</tbody></table></body></html>`;
            },

            // â”€â”€â”€ BUG FIX 3 â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
            // Utilisation de getRealReturnedQtyForItem() au lieu du champ
            // item.quantity_returned (qui peut être obsolète) pour calculer
            // les quantités réelles retournées dans le document d'impression.
            buildPrintSingleHtml(record, items) {
                const now = new Date().toLocaleString('fr-FR');
                let rows = '';
                let totalGiven = 0,
                    totalReturned = 0;
                items.forEach((item) => {
                    const given = parseFloat(item.quantity_given) || 0;
                    const returned = this.getRealReturnedQtyForItem(item.id);
                    const pending = given - returned;
                    totalGiven += given;
                    totalReturned += returned;
                    rows += `<tr>
                        <td>${item.product_name || this.findProductLabel(item.product_id)}</td>
                        <td class="qty-column">${given.toFixed(2)}</td>
                        <td class="qty-column">${returned.toFixed(2)}</td>
                        <td class="qty-column">${pending.toFixed(2)}</td>
                    </tr>`;
                });
                const totalPending = (totalGiven - totalReturned).toFixed(2);
                return `<!DOCTYPE html><html><head><title>Remise Emballages - ${record.reference}</title>
                <style>* { margin:0; padding:0; box-sizing:border-box; }
                body { font-family:Arial,sans-serif; padding:20px; background:#f8f9fa; color:#333; }
                .company-header { text-align:center; margin-bottom:30px; border-bottom:3px solid #667eea; padding-bottom:20px; }
                .company-header h1 { color:#667eea; font-size:2rem; }
                .company-header p { margin:5px 0; color:#666; }
                .transaction-card { background:white; border-radius:12px; padding:30px; box-shadow:0 4px 12px rgba(0,0,0,0.1); max-width:900px; margin:0 auto 20px; }
                h2 { color:#667eea; margin-bottom:15px; text-align:center; }
                .info-grid { display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:25px; padding:15px; background:#f8f9fa; border-radius:8px; }
                .info-item { display:flex; gap:10px; }
                .info-label { font-weight:bold; color:#667eea; }
                table { width:100%; border-collapse:collapse; margin:20px 0; }
                th { background:#667eea; color:white; padding:12px; text-align:left; font-weight:bold; }
                td { padding:12px; border-bottom:1px solid #ddd; }
                .qty-column { text-align:center; font-weight:bold; }
                .total-row { background:#f0f0f0; font-weight:bold; }
                .total-row td { border-top:2px solid #667eea; padding:15px 12px; }
                .footer { margin-top:40px; text-align:center; color:#666; font-size:0.9rem; border-top:2px solid #667eea; padding-top:20px; }
                @media print { body { padding:10mm; background:white; } }
                </style></head><body>
                <div class="company-header">
                    <h1>SAGER</h1>
                    <p>Votre partenaire de confiance pour tous vos besoins en boissons et gaz domestique</p>
                    <p><strong>Téléphone:</strong> +229 0196466625 &nbsp;|&nbsp; <strong>IFU:</strong> 0202586942320</p>
                </div>
                <div class="transaction-card">
                    <h2>REMISE DE PRODUITS AVEC EMBALLAGES</h2>
                    <div class="info-grid">
                        <div class="info-item"><span class="info-label">Client:</span><span>${record.client_name}</span></div>
                        <div class="info-item"><span class="info-label">Référence:</span><span>${record.reference}</span></div>
                        <div class="info-item"><span class="info-label">Date de remise:</span><span>${this.formatDateDisplay(record.date)}</span></div>
                        <div class="info-item"><span class="info-label">Date d'impression:</span><span>${now}</span></div>
                    </div>
                    <h3 style="color:#333; margin-bottom:10px;">Détails des Produits</h3>
                    <table><thead><tr><th>Produit</th><th class="qty-column">Qté Remise</th><th class="qty-column">Qté Retournée</th><th class="qty-column">En Attente</th></tr></thead>
                    <tbody>${rows}
                    <tr class="total-row"><td>TOTAL</td><td class="qty-column">${totalGiven.toFixed(2)}</td><td class="qty-column">${totalReturned.toFixed(2)}</td><td class="qty-column">${totalPending}</td></tr>
                    </tbody></table>
                </div>
                <div class="footer"><p>Merci de votre confiance</p><p>Rapport généré avec l'application SagerMarket</p></div>
                </body></html>`;
            },
            // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        },
    };
</script>

<style scoped>
    * {
        box-sizing: border-box;
    }

    .main-content {
        background: #f8f9fa;
    }

    .sales-content {
        max-width: 1400px;
        margin: 0 auto;
    }

    /* â”€â”€â”€ Buttons â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .packaging-btn-view {
        background: #17a2b8;
        color: white;
    }
    .packaging-btn-view:hover {
        background: #138496;
    }
    .packaging-btn-return {
        background: #28a745;
        color: white;
    }
    .packaging-btn-return:hover {
        background: #218838;
    }
    .packaging-btn-print {
        background: #6c757d;
        color: white;
    }
    .packaging-btn-print:hover {
        background: #5a6268;
    }
    .packaging-btn-cancel {
        background: #dc3545;
        color: white;
    }
    .packaging-btn-cancel:hover {
        background: #c82333;
    }

    .packaging-btn-disabled,
    .packaging-btn-sm:disabled {
        background: #d6d8db;
        color: #6c757d;
        cursor: not-allowed;
        opacity: 1;
        box-shadow: none;
    }

    .packaging-btn-sm:disabled:hover {
        background: #d6d8db;
    }

    .packaging-btn-danger {
        background: #dc3545;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }
    .packaging-btn-danger:hover {
        background: #c82333;
    }

    /* â”€â”€â”€ Layout â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .packaging-main {
        width: 100%;
        max-width: 100%;
        background: #f8f9fa;
        min-height: 100vh;
        padding: 20px;
        margin: 0;
        overflow-x: hidden;
    }
    .packaging-wrapper {
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
    }

    /* â”€â”€â”€ Header â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .packaging-header-box {
        background: white;
        border-radius: 8px;
        padding: 24px 28px;
        margin-bottom: 20px;
        border: 1px solid #e9ecef;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
    }
    .packaging-title-block h2 {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 24px;
        flex-wrap: wrap;
        color: #333;
    }

    /* â”€â”€â”€ Controls â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .packaging-controls-box {
        background: white;
        border-radius: 8px;
        padding: 24px 28px;
        margin-bottom: 20px;
        display: flex;
        flex-direction: column;
        gap: 15px;
        border: 1px solid #e9ecef;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
    }
    .packaging-search-area {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    .packaging-search-field {
        position: relative;
        flex: 1;
        max-width: 400px;
    }
    .packaging-search-input {
        width: 100%;
        padding: 10px 40px 10px 15px;
        border: 1px solid #ced4da;
        border-radius: 6px;
        font-size: 14px;
        background: #fff;
    }
    .packaging-search-input:focus {
        outline: none;
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.15);
    }
    .packaging-search-field i {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
    }
    .packaging-filters-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 10px;
    }
    .packaging-select {
        padding: 10px 12px;
        border: 1px solid #ced4da;
        border-radius: 6px;
        font-size: 14px;
        background: white;
        cursor: pointer;
    }
    .packaging-select:focus {
        outline: none;
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.15);
    }
    .packaging-date-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .packaging-date-label {
        font-size: 14px;
        color: #666;
        white-space: nowrap;
    }
    .packaging-date-input {
        padding: 8px 12px;
        border: 1px solid #ced4da;
        border-radius: 6px;
        font-size: 14px;
    }
    .packaging-date-input:focus {
        outline: none;
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.15);
    }
    .packaging-date-sep {
        color: #666;
    }
    .packaging-btn-clear-date {
        padding: 6px 10px;
        background: #ffe0e0;
        color: #dc3545;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    .packaging-btn-clear-date:hover {
        background: #ffcccc;
    }
    .packaging-action-btns {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .packaging-btn-main {
        padding: 10px 20px;
        background: #28a745;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background 0.2s;
    }
    .packaging-btn-main:hover {
        background: #218838;
    }
    .packaging-btn-main:disabled {
        background: #ccc;
        cursor: not-allowed;
    }

    .packaging-btn-alt {
        padding: 10px 20px;
        background: #6c757d;
        color: white;
        border: 1px solid #6c757d;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    .packaging-btn-alt:hover {
        background: #5a6268;
        border-color: #5a6268;
    }

    /* â”€â”€â”€ Main Table â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .packaging-table-box {
        background: white;
        border-radius: 8px;
        padding: 24px 28px;
        border: 1px solid #e9ecef;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
    }
    .packaging-table-scroll {
        overflow-x: auto;
    }
    .packaging-data-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 800px;
    }
    .packaging-data-table thead {
        background: #f8f9fa;
    }
    .packaging-data-table th {
        padding: 15px 10px;
        text-align: left;
        font-weight: 600;
        color: #333;
        border-bottom: 2px solid #e9ecef;
        white-space: nowrap;
    }
    .packaging-data-table td {
        padding: 12px 10px;
        border-bottom: 1px solid #f0f0f0;
    }
    .packaging-data-table tbody tr:hover {
        background: #f8f9fa;
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
    .row-cancelled {
        border-left: 4px solid #bdbdbd;
        opacity: 0.7;
    }

    .qty-centered {
        text-align: center;
    }
    .pending-cell {
        font-weight: bold;
        color: #dc3545;
    }
    .ref-cell {
        font-family: monospace;
        font-size: 12px;
    }

    .packaging-product-badge {
        padding: 4px 10px;
        background: #eaf6ec;
        color: #218838;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }
    .actions-col {
        display: flex;
        gap: 5px;
    }
    .packaging-btn-sm {
        padding: 6px 10px;
        border: none;
        border-radius: 4px;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .empty-data-row {
        background: #f9f9f9;
    }

    /* â”€â”€â”€ Pagination (commune) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .packaging-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 20px;
        padding-top: 16px;
        border-top: 1px solid #f0f0f0;
        flex-wrap: wrap;
    }
    .packaging-pagination-info {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: #888;
        flex-shrink: 0;
    }
    .packaging-pagination-count {
        font-weight: 700;
        color: #555;
    }
    .packaging-pagination-sep {
        color: #ccc;
    }
    .packaging-pagination-controls {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .packaging-btn-page {
        padding: 6px 12px;
        background: white;
        color: #555;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 13px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: all 0.15s;
    }
    .packaging-btn-page-arrow {
        width: 34px;
        height: 34px;
        padding: 0 !important;
        justify-content: center;
        border-radius: 8px;
    }
    .packaging-btn-page:hover:not(:disabled) {
        background: #f0f3ff;
        border-color: #667eea;
        color: #667eea;
    }
    .packaging-btn-page:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }
    .packaging-page-pills {
        display: flex;
        gap: 3px;
        align-items: center;
    }
    .packaging-page-pill {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        border: 1px solid #ddd;
        background: white;
        color: #555;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .packaging-page-pill:hover {
        border-color: #667eea;
        color: #667eea;
        background: #f0f3ff;
    }
    .packaging-page-pill.active {
        background: #667eea;
        color: white;
        border-color: #667eea;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.35);
    }
    .packaging-page-ellipsis {
        width: 28px;
        text-align: center;
        color: #aaa;
        font-size: 14px;
        user-select: none;
    }
    .packaging-per-page {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: #888;
    }
    .packaging-per-page-select {
        padding: 5px 8px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 13px;
        background: white;
        cursor: pointer;
    }
    .packaging-per-page-select:focus {
        outline: none;
        border-color: #667eea;
    }
    .packaging-page-info {
        font-size: 13px;
        color: #999;
    }

    /* â”€â”€â”€ Add-client button â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .packaging-btn-add-client {
        margin-top: 10px;
        padding: 10px 15px;
        background: #667eea;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        justify-content: center;
        transition: all 0.2s;
    }
    .packaging-btn-add-client:hover {
        background: #5568d3;
    }

    /* â”€â”€â”€ Modals â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .packaging-modal-bg {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 20px;
    }
    .packaging-modal-box {
        background: white;
        border-radius: 8px;
        width: 100%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }
    .packaging-modal-lg {
        max-width: 900px;
    }
    .packaging-modal-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid #ddd;
        position: sticky;
        top: 0;
        background: white;
        z-index: 10;
    }
    .packaging-modal-top h5 {
        margin: 0;
        color: #333;
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .packaging-modal-x {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #999;
        padding: 0;
        width: 30px;
        height: 30px;
    }
    .packaging-modal-x:hover {
        color: #333;
    }
    .packaging-modal-content {
        padding: 20px;
    }
    .packaging-modal-bottom {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        padding: 20px;
        border-top: 1px solid #ddd;
        position: sticky;
        bottom: 0;
        background: white;
    }

    /* â”€â”€â”€ Forms â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .packaging-form-block {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #f0f0f0;
    }
    .packaging-form-block:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    .packaging-form-field {
        margin-bottom: 15px;
    }
    .packaging-form-field label {
        display: block;
        margin-bottom: 6px;
        font-weight: 500;
        color: #333;
        font-size: 14px;
    }
    .packaging-input {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
        box-sizing: border-box;
    }
    .packaging-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    textarea.packaging-input {
        resize: vertical;
        min-height: 80px;
    }

    /* Client search */
    .packaging-client-search-box {
        position: relative;
    }
    .packaging-client-list {
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
    .packaging-client-item {
        padding: 12px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s;
    }
    .packaging-client-item:hover {
        background: #f9f9f9;
    }
    .packaging-client-name {
        font-weight: 600;
        color: #333;
    }
    .packaging-client-phone {
        font-size: 12px;
        color: #999;
        margin-top: 2px;
    }
    .packaging-chosen-client-box {
        background: #f0f7ff;
        padding: 15px;
        border-radius: 6px;
        margin-top: 10px;
        border-left: 4px solid #667eea;
    }
    .packaging-info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 14px;
        flex-wrap: wrap;
        gap: 5px;
    }
    .packaging-info-key {
        font-weight: 600;
        color: #333;
    }
    .packaging-info-val {
        color: #666;
    }

    /* Product rows */
    .packaging-products-area {
        background: #fafafa;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 15px;
    }
    .packaging-product-row-form {
        background: white;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 15px;
    }
    .packaging-product-row-form:last-child {
        margin-bottom: 0;
    }
    .packaging-row-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f0f0f0;
        flex-wrap: wrap;
        gap: 10px;
    }
    .packaging-row-num {
        font-weight: 600;
        color: #333;
    }
    .packaging-btn-remove-row {
        padding: 6px 12px;
        background: #ffe7e7;
        color: #dc3545;
        border: none;
        border-radius: 4px;
        font-size: 12px;
        cursor: pointer;
    }
    .packaging-btn-remove-row:hover {
        background: #ffcccc;
    }
    .packaging-product-search-box {
        position: relative;
    }
    .packaging-product-input {
        width: 100%;
    }
    .packaging-product-list {
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
    .packaging-product-item {
        padding: 12px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s;
    }
    .packaging-product-item:hover {
        background: #f9f9f9;
    }
    .packaging-product-name {
        font-weight: 600;
        color: #333;
    }
    .packaging-product-stock {
        font-size: 12px;
        color: #0066cc;
        margin-top: 2px;
    }
    .packaging-qty-input-box {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .packaging-qty-input {
        flex: 1;
        min-width: 100px;
    }
    .packaging-qty-hint {
        font-size: 12px;
        color: #999;
        white-space: nowrap;
    }
    .packaging-btn-add-row {
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
    .packaging-btn-add-row:hover {
        background: #cce5ff;
    }

    /* â”€â”€â”€ Details modal â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .packaging-details-head {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        background: #f9f9f9;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 20px;
    }
    .packaging-detail-col {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .packaging-detail-row {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .packaging-detail-key {
        font-weight: 600;
        color: #333;
        font-size: 13px;
    }
    .packaging-detail-val {
        color: #666;
        font-size: 14px;
    }
    .packaging-details-section {
        margin-bottom: 30px;
        overflow-x: auto;
    }

    .packaging-details-tbl {
        width: 100%;
        border-collapse: collapse;
    }
    .packaging-details-tbl thead {
        background: #f9f9f9;
    }
    .packaging-details-tbl th {
        padding: 12px;
        text-align: left;
        font-weight: 600;
        border-bottom: 1px solid #ddd;
        white-space: nowrap;
    }
    .packaging-details-tbl td {
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
    }

    /* â”€â”€â”€ Return modal â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .packaging-return-info-box {
        background: #f0f7ff;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 20px;
        border-left: 4px solid #667eea;
    }
    .packaging-return-items-area {
        margin-bottom: 20px;
    }
    .packaging-no-items-msg {
        text-align: center;
        padding: 30px;
        color: #28a745;
        background: #e8f5e9;
        border-radius: 6px;
    }
    .packaging-no-items-msg i {
        font-size: 2rem;
        margin-bottom: 10px;
        display: block;
    }
    .packaging-return-item-box {
        background: #fafafa;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 15px;
        border: 2px solid transparent;
        transition: all 0.2s;
    }
    .packaging-return-item-box.selected {
        border-color: #667eea;
        background: #f0f7ff;
    }
    .packaging-return-item-row {
        display: flex;
        align-items: flex-start;
        gap: 15px;
    }

    /* Custom checkbox */
    .packaging-checkbox-wrap {
        display: flex;
        align-items: center;
        position: relative;
        cursor: pointer;
        user-select: none;
        padding-top: 3px;
    }
    .packaging-checkbox-wrap input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }
    .packaging-checkmark {
        height: 22px;
        width: 22px;
        background-color: #fff;
        border: 2px solid #ddd;
        border-radius: 4px;
        transition: all 0.2s;
        position: relative;
        flex-shrink: 0;
    }
    .packaging-checkbox-wrap:hover input ~ .packaging-checkmark {
        border-color: #667eea;
    }
    .packaging-checkbox-wrap input:checked ~ .packaging-checkmark {
        background-color: #667eea;
        border-color: #667eea;
    }
    .packaging-checkmark::after {
        content: '';
        position: absolute;
        display: none;
        left: 6px;
        top: 2px;
        width: 6px;
        height: 11px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }
    .packaging-checkbox-wrap input:checked ~ .packaging-checkmark::after {
        display: block;
    }

    .packaging-return-item-info {
        flex: 1;
        min-width: 0;
    }
    .packaging-return-item-name {
        font-weight: 600;
        color: #333;
        font-size: 15px;
        display: block;
        margin-bottom: 8px;
    }
    .packaging-return-item-qtys {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .packaging-qty-tag {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }
    .packaging-qty-given {
        background: #e3f2fd;
        color: #1976d2;
    }
    .packaging-qty-back {
        background: #e8f5e9;
        color: #388e3c;
    }
    .packaging-qty-wait {
        background: #fff3e0;
        color: #f57c00;
    }

    .packaging-return-qty-section {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px dashed #ddd;
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }
    .packaging-return-qty-label {
        font-weight: 500;
        color: #333;
        font-size: 14px;
        white-space: nowrap;
    }
    .packaging-return-qty-box {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        min-width: 200px;
    }
    .packaging-return-qty-field {
        width: 120px;
        flex: 1;
        padding: 8px 12px;
    }
    .packaging-max-qty-hint {
        font-size: 12px;
        color: #999;
        white-space: nowrap;
    }

    /* â”€â”€â”€ Confirm modal â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .packaging-modal-confirm {
        max-width: 400px;
    }
    .packaging-modal-confirm .packaging-modal-content {
        padding: 30px;
        text-align: center;
    }
    .packaging-modal-confirm p {
        margin-bottom: 10px;
        color: #666;
    }
    .packaging-modal-confirm strong {
        color: #333;
    }

    /* â”€â”€â”€ Modal top actions (close + print) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .packaging-modal-top-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* â”€â”€â”€ Print history button â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .packaging-btn-print-history {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        background: #f0f4ff;
        color: #667eea;
        border: 1px solid #c5d0f8;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
    }
    .packaging-btn-print-history:hover {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    /* â”€â”€â”€ Section header with title + action â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .packaging-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }
    .packaging-section-title-group {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .packaging-returns-count {
        display: inline-flex;
        align-items: center;
        background: #e8ecff;
        color: #667eea;
        border-radius: 20px;
        padding: 2px 10px;
        font-size: 12px;
        font-weight: 700;
    }

    /* â”€â”€â”€ Returns history pagination â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    .packaging-returns-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: 14px;
        padding-top: 12px;
        border-top: 1px solid #f0f0f0;
        flex-wrap: wrap;
    }
    .packaging-returns-page-pills {
        display: flex;
        gap: 3px;
        align-items: center;
        flex-wrap: wrap;
    }
    .packaging-returns-page-info {
        font-size: 13px;
        color: #999;
        white-space: nowrap;
    }

    /* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
   RESPONSIVE - Tables dans les modals (data-label)
   â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */
    @media (max-width: 600px) {
        /* Tables dans les modals : passer en mode carte */
        .packaging-responsive-tbl thead {
            display: none;
        }
        .packaging-responsive-tbl tbody tr {
            display: block;
            margin-bottom: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
        }
        .packaging-responsive-tbl td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 14px;
            border-bottom: 1px solid #f5f5f5;
            font-size: 13px;
            gap: 8px;
        }
        .packaging-responsive-tbl td:last-child {
            border-bottom: none;
        }
        .packaging-responsive-tbl td::before {
            content: attr(data-label);
            font-weight: 700;
            color: #667eea;
            font-size: 12px;
            white-space: nowrap;
            flex-shrink: 0;
        }
    }

    /* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
   RESPONSIVE - Table principale
   â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */
    /* Visual alignment with the sales page */
    .packaging-main {
        background: #f4f6fb;
        padding: 24px 24px 24px 12px;
    }

    .packaging-wrapper,
    .packaging-stats {
        max-width: 1400px;
        margin-left: auto;
        margin-right: auto;
    }

    .packaging-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .packaging-stats .stat-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        min-height: 112px;
        padding: 1.5rem;
        border-radius: 15px;
        background: #ffffff;
        color: #333333;
        border: 1px solid #eef0f4;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .packaging-stats .stat-card-1 {
        background: #ffffff;
    }

    .packaging-stats .stat-card-2 {
        background: #ffffff;
    }

    .packaging-stats .stat-card-3 {
        background: #ffffff;
    }

    .packaging-stats .stat-icon {
        width: 58px;
        height: 58px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        font-size: 1.6rem;
        flex-shrink: 0;
    }

    .packaging-stats .stat-card-1 .stat-icon {
        background: rgba(102, 126, 234, 0.12);
        color: #667eea;
    }

    .packaging-stats .stat-card-2 .stat-icon {
        background: rgba(245, 87, 108, 0.12);
        color: #f5576c;
    }

    .packaging-stats .stat-card-3 .stat-icon {
        background: rgba(79, 172, 254, 0.14);
        color: #4facfe;
    }

    .packaging-stats .stat-label {
        margin-bottom: 0.35rem;
        color: #6c757d;
        font-size: 0.92rem;
        font-weight: 600;
    }

    .packaging-stats .stat-value {
        color: #333333;
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
    }

    .packaging-header-box,
    .packaging-controls-box,
    .packaging-table-box {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .packaging-header-box {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #eee;
    }

    .packaging-title-block h2 {
        font-size: 1.5rem;
        font-weight: 700;
    }

    .packaging-title-block h2 i {
        color: #667eea;
    }

    .packaging-title-block p {
        color: #6c757d !important;
    }

    .packaging-controls-box {
        padding: 1.5rem 2rem;
        flex-direction: row;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
    }

    .packaging-search-area {
        flex: 1;
    }

    .packaging-search-field {
        max-width: 100%;
    }

    .packaging-search-input,
    .packaging-select,
    .packaging-date-input {
        min-height: 43px;
        border: 2px solid #e1e5e9;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .packaging-search-input:focus,
    .packaging-select:focus,
    .packaging-date-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.12);
    }

    .packaging-date-label {
        color: #333;
        font-weight: 600;
    }

    .packaging-action-btns {
        justify-content: flex-end;
        min-width: 320px;
    }

    .packaging-btn-main,
    .packaging-btn-alt,
    .packaging-btn-danger,
    .packaging-btn-print-history,
    .packaging-btn-add-client,
    .packaging-btn-add-row {
        min-height: 43px;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 600;
    }

    .packaging-btn-main {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.25);
    }

    .packaging-btn-main:hover {
        background: linear-gradient(135deg, #5568d3 0%, #68408d 100%);
        transform: translateY(-2px);
    }

    .packaging-btn-alt,
    .packaging-btn-print-history {
        background: #17a2b8;
        border-color: #17a2b8;
        color: #ffffff;
    }

    .packaging-btn-alt:hover,
    .packaging-btn-print-history:hover {
        background: #138496;
        border-color: #138496;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(23, 162, 184, 0.25);
    }

    .packaging-table-box {
        padding: 0;
        overflow: hidden;
    }

    .packaging-list-header,
    .table-header-styled {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.5rem 2rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #ffffff;
    }

    .packaging-list-header h3 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .packaging-list-header .table-count {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.2);
        font-size: 0.9rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .packaging-table-scroll {
        background: #ffffff;
    }

    .packaging-data-table {
        min-width: 980px;
    }

    .packaging-data-table thead {
        background: #f8f9fa;
    }

    .packaging-data-table th {
        padding: 15px 12px;
        color: #495057;
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .packaging-data-table td {
        padding: 14px 12px;
        color: #333;
        vertical-align: middle;
    }

    .packaging-data-table tbody tr {
        transition: background 0.2s ease, transform 0.2s ease;
    }

    .packaging-data-table tbody tr:hover {
        background: #f8f9fa;
    }

    .packaging-product-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 28px;
        padding: 0.25rem 0.75rem;
        border: 1px solid #c3e6cb;
        border-radius: 20px;
        background: #d4edda;
        color: #155724;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .actions-col {
        display: grid;
        grid-template-columns: repeat(2, 38px);
        align-items: center;
        justify-content: flex-start;
        gap: 0.5rem;
    }

    .packaging-btn-sm {
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        border-radius: 6px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .packaging-btn-sm:hover:not(:disabled) {
        transform: translateY(-2px);
    }

    .packaging-btn-view {
        background: #17a2b8;
    }

    .packaging-btn-return {
        background: #28a745;
    }

    .packaging-btn-print {
        background: #667eea;
    }

    .packaging-btn-cancel {
        background: #dc3545;
    }

    .packaging-pagination {
        margin: 0;
        padding: 1.25rem 2rem;
        background: #ffffff;
    }

    .packaging-page-pill.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
        color: #ffffff;
    }

    .packaging-btn-page:hover:not(:disabled),
    .packaging-page-pill:hover {
        border-color: #667eea;
        color: #667eea;
        background: #f0f3ff;
    }

    .packaging-modal-box {
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .packaging-modal-top {
        padding: 1.5rem;
        border-bottom-color: #eee;
    }

    .packaging-modal-top h5 {
        font-size: 1.3rem;
    }

    .packaging-modal-bottom {
        padding: 1rem 1.5rem;
        border-top-color: #eee;
    }

    @media (max-width: 1024px) {
        .packaging-data-table th,
        .packaging-data-table td {
            padding: 10px 6px;
            font-size: 12px;
        }
        .packaging-filters-row {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .packaging-main {
            padding: 10px;
        }
        .packaging-stats {
            grid-template-columns: 1fr;
            gap: 12px;
        }
        .packaging-stats .stat-card {
            min-height: 92px;
            padding: 16px;
        }
        .packaging-header-box {
            padding: 15px;
        }
        .packaging-title-block h2 {
            font-size: 18px;
        }
        .packaging-controls-box {
            flex-direction: column;
            align-items: stretch;
            padding: 15px;
            overflow: hidden;
        }
        .packaging-search-area,
        .packaging-search-field {
            width: 100%;
            min-width: 0;
        }
        .packaging-filters-row {
            grid-template-columns: 1fr;
        }
        .packaging-date-row {
            display: grid;
            grid-template-columns: 1fr;
            align-items: stretch;
            gap: 8px;
        }
        .packaging-date-input {
            width: 100%;
        }
        .packaging-date-sep {
            text-align: center;
        }
        .packaging-action-btns {
            flex-direction: column;
            width: 100%;
            min-width: 0;
        }
        .packaging-btn-main,
        .packaging-btn-alt {
            width: 100%;
            justify-content: center;
        }
        .packaging-table-box {
            padding: 10px;
        }

        /* Table principale en cartes */
        .packaging-data-table {
            font-size: 12px;
            min-width: unset;
        }
        .packaging-data-table thead {
            display: none;
        }
        .packaging-data-table tbody tr {
            display: block;
            margin-bottom: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.07);
        }
        .packaging-data-table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 14px;
            border-bottom: 1px solid #f5f5f5;
            font-size: 13px;
            gap: 8px;
            text-align: right;
        }
        .packaging-data-table td:last-child {
            border-bottom: none;
        }
        .packaging-data-table td[data-label]::before {
            content: attr(data-label);
            font-weight: 700;
            color: #667eea;
            font-size: 12px;
            white-space: nowrap;
            flex-shrink: 0;
            text-align: left;
        }
        /* Ligne du client en header de carte */
        .packaging-data-table td:first-child {
            background: #f4f6ff;
            font-weight: 600;
            border-bottom: 2px solid #667eea;
        }
        /* Colonne actions */
        .actions-col {
            grid-template-columns: repeat(2, 38px);
            justify-content: flex-end;
            gap: 6px;
        }
        .packaging-btn-sm {
            padding: 6px 10px;
            font-size: 11px;
        }

        /* Couleurs de statut en haut de carte (border-top) */
        .row-complete {
            border-left: none;
            border-top: 4px solid #28a745;
        }
        .row-partial {
            border-left: none;
            border-top: 4px solid #ffc107;
        }
        .row-pending {
            border-left: none;
            border-top: 4px solid #dc3545;
        }
        .row-cancelled {
            border-left: none;
            border-top: 4px solid #bdbdbd;
        }

        .qty-centered {
            text-align: right !important;
        }
        .ref-cell {
            font-size: 11px;
            word-break: break-all;
        }

        /* Modals plein écran */
        .packaging-modal-bg {
            padding: 0;
            align-items: flex-end;
        }
        .packaging-modal-box {
            max-width: 100%;
            border-radius: 12px 12px 0 0;
            max-height: 92vh;
        }
        .packaging-modal-lg {
            max-width: 100%;
        }

        /* Pagination */
        .packaging-pagination {
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
        }
        .packaging-pagination-info {
            justify-content: center;
        }
        .packaging-pagination-controls {
            justify-content: center;
        }
        .packaging-per-page {
            justify-content: center;
        }
        .packaging-returns-pagination {
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }
        .packaging-returns-page-info {
            order: -1;
        }

        /* Details head */
        .packaging-details-head {
            grid-template-columns: 1fr;
        }

        /* Return modal */
        .packaging-return-item-row {
            flex-direction: column;
            gap: 10px;
        }
        .packaging-return-qty-section {
            flex-direction: column;
            align-items: flex-start;
        }
        .packaging-return-qty-box {
            width: 100%;
        }
        .packaging-return-qty-field {
            width: 100%;
        }
        .packaging-return-item-qtys {
            flex-direction: column;
            gap: 5px;
        }
    }

    @media (max-width: 480px) {
        .packaging-main {
            padding: 8px;
        }
        .packaging-header-box,
        .packaging-controls-box {
            padding: 12px;
        }
        .packaging-title-block h2 {
            font-size: 15px;
        }
        .packaging-search-input,
        .packaging-select,
        .packaging-input {
            font-size: 13px;
        }
        .packaging-modal-top h5 {
            font-size: 15px;
        }
        .packaging-modal-content {
            padding: 14px;
        }
        .packaging-modal-bottom {
            gap: 8px;
            padding: 14px;
            flex-direction: column;
        }
        .packaging-modal-bottom button {
            width: 100%;
            justify-content: center;
        }
        .packaging-btn-sm {
            padding: 5px 8px;
            font-size: 10px;
        }
        .packaging-qty-tag {
            padding: 3px 8px;
            font-size: 11px;
        }
        .packaging-data-table td {
            font-size: 12px;
            padding: 8px 12px;
        }
        .packaging-data-table td[data-label]::before {
            font-size: 11px;
        }
    }
</style>
