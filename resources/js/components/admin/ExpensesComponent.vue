<template>
    <div class="expenses-content">
        <!-- Improved statistics section with gradient cards and colors -->
        <div class="statistics-section">
            <div class="stat-card stat-card-1">
                <div class="stat-icon"><i class="fas fa-chart-bar"></i></div>
                <div class="stat-info">
                    <div class="stat-label">Nombre total de dépenses</div>
                    <div class="stat-value">{{ filteredExpenses.length }}</div>
                </div>
            </div>
            <div class="stat-card stat-card-2">
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Montant total</div>
                    <div class="stat-value">
                        {{ formatAmount(totalAmount) }} FCFA
                    </div>
                </div>
            </div>
            <div class="stat-card stat-card-3">
                <div class="stat-icon"><i class="fas fa-calculator"></i></div>
                <div class="stat-info">
                    <div class="stat-label">Moyenne par dépense</div>
                    <div class="stat-value">
                        {{
                            filteredExpenses.length > 0
                                ? formatAmount(
                                      totalAmount / filteredExpenses.length
                                  )
                                : 0
                        }}
                        FCFA
                    </div>
                </div>
            </div>
            <div class="stat-card stat-card-4">
                <div class="stat-icon"><i class="fas fa-pie-chart"></i></div>
                <div class="stat-info">
                    <div class="stat-label">Dépenses par type</div>
                    <div class="type-badge-container">
                        <span
                            v-for="type in expenseTypes"
                            :key="type"
                            v-if="getTotalByType(type) > 0"
                            class="type-badge"
                        >
                            {{ type }}: {{ formatAmount(getTotalByType(type)) }}
                        </span>
                    </div>
                    <div
                        v-if="filteredExpenses.length === 0"
                        class="no-expenses-message"
                    >
                        Aucune dépense
                    </div>
                </div>
            </div>
        </div>

        <!-- Header Actions -->
        <div class="expenses-header">
            <h2>Gestion des Dépenses</h2>
            <div class="header-actions">
                <button class="btn btn-primary" @click="openAddExpenseModal">
                    <i class="fas fa-plus"></i>
                    Ajouter une dépense
                </button>

                <button @click="printList" class="btn btn-print">
                    <i class="fas fa-print"></i>
                    Imprimer
                </button>

                <input
                    type="text"
                    class="search-input"
                    placeholder="Rechercher dans les descriptions..."
                    v-model="searchQuery"
                />
            </div>
        </div>

        <!-- Filters Section -->
        <div class="filters-section">
            <select class="filter-select" v-model="filterType">
                <option value="">Tous les types</option>
                <option v-for="type in expenseTypes" :key="type" :value="type">
                    {{ type }}
                </option>
            </select>

            <select class="filter-select" v-model="dateFilterMode">
                <option value="">Toutes les dates</option>
                <option value="specific">Date précise</option>
                <option value="range">Entre deux dates</option>
            </select>

            <input
                v-if="dateFilterMode === 'specific'"
                type="date"
                class="filter-select"
                v-model="filterDate"
            />

            <div v-if="dateFilterMode === 'range'" class="date-range">
                <input
                    type="date"
                    class="filter-select"
                    v-model="filterDateStart"
                    placeholder="Date de début"
                />
                <span>à</span>
                <input
                    type="date"
                    class="filter-select"
                    v-model="filterDateEnd"
                    placeholder="Date de fin"
                />
            </div>

            <select class="filter-select" v-model="sortOption">
                <option>Type (A-Z)</option>
                <option>Type (Z-A)</option>
                <option>Montant (croissant)</option>
                <option>Montant (décroissant)</option>
                <option>Date (récent)</option>
                <option>Date (ancien)</option>
            </select>
        </div>

        <!-- Expenses Table -->
        <div class="table-container" v-if="filteredExpenses.length > 0">
            <table class="table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Montant (FCFA)</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="expense in paginatedExpenses" :key="expense.id">
                        <td>
                            <span
                                class="type-badge"
                                :class="'type-' + sanitizeType(expense.type)"
                            >
                                {{ expense.type }}
                            </span>
                        </td>
                        <td>{{ expense.description }}</td>
                        <td class="amount">
                            {{ formatAmount(expense.amount) }} FCFA
                        </td>
                        <td>
                            {{
                                new Date(expense.date).toLocaleDateString(
                                    'fr-FR'
                                )
                            }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination" v-if="filteredExpenses.length > 0">
            <button
                @click="currentPage--"
                :disabled="currentPage === 1"
                class="btn btn-pagination"
            >
                <i class="fas fa-chevron-left"></i>
                Précédent
            </button>
            <span class="page-info">
                Page {{ currentPage }} / {{ totalPages }}
            </span>
            <button
                @click="currentPage++"
                :disabled="currentPage === totalPages || totalPages === 0"
                class="btn btn-pagination"
            >
                Suivant
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>

        <!-- Empty State -->
        <div class="empty-state" v-else>
            <i class="fas fa-inbox"></i>
            <p>Aucune dépense trouvée</p>
        </div>

        <!-- Fixed modal with proper v-model binding and validation -->
        <div
            class="modal-overlay"
            v-if="showAddExpenseModal"
            @click.self="closeAddExpenseModal"
        >
            <div class="modal">
                <div class="modal-header">
                    <h3>Ajouter une dépense</h3>
                    <button class="btn-close" @click="closeAddExpenseModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Type de dépense *</label>
                        <select v-model="newExpense.type" class="form-input">
                            <option value="">-- Sélectionner un type --</option>
                            <option
                                v-for="type in expenseTypes"
                                :key="type"
                                :value="type"
                            >
                                {{ type }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Description *</label>
                        <textarea
                            v-model="newExpense.description"
                            class="form-input"
                            rows="4"
                            placeholder="Détails de la dépense"
                        ></textarea>
                    </div>
                    <div class="form-group">
                        <label>Montant (FCFA) *</label>
                        <input
                            v-model.number="newExpense.amount"
                            type="number"
                            class="form-input"
                            placeholder="0.00"
                        />
                    </div>
                    <div class="form-group">
                        <label>Date *</label>
                        <input
                            v-model="newExpense.date"
                            type="date"
                            class="form-input"
                        />
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        class="btn btn-secondary"
                        @click="closeAddExpenseModal"
                    >
                        Annuler
                    </button>
                    <button class="btn btn-primary" @click="addExpense">
                        Ajouter
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'ExpensesComponent',
        data() {
            return {
                expenses: [],
                showAddExpenseModal: false,

                searchQuery: '',
                filterType: '',
                dateFilterMode: '',
                filterDate: '',
                filterDateStart: '',
                filterDateEnd: '',
                sortOption: 'Date (récent)',

                currentPage: 1,
                itemsPerPage: 10,

                newExpense: {
                    type: '',
                    description: '',
                    amount: null,
                    date: new Date().toISOString().split('T')[0],
                },

                expenseTypes: [
                    'Loyer',
                    'Salaire',
                    'Fournisseurs',
                    'Électricité / Eau / Internet',
                    'Marketing / Publicité',
                    'Maintenance / Réparation',
                    'Transport / Livraison',
                    'Taxes / Impôts',
                    'Formations / Développement du personnel',
                    'Autres',
                ],
            };
        },

        mounted() {
            this.fetchExpenses();
        },

        methods: {
            async fetchExpenses() {
                try {
                    const response = await axios.get(
                        `${window.location.origin}/expensesList`
                    );
                    if (response.data.success) {
                        this.expenses = response.data.data;
                    }
                } catch (error) {
                    console.error(
                        'Erreur lors de la récupération des dépenses:',
                        error
                    );
                }
            },

            openAddExpenseModal() {
                this.newExpense = {
                    type: '',
                    description: '',
                    amount: null,
                    date: new Date().toISOString().split('T')[0],
                };
                this.showAddExpenseModal = true;
            },

            closeAddExpenseModal() {
                this.showAddExpenseModal = false;
            },

            async addExpense() {
                if (
                    !this.newExpense.type ||
                    !this.newExpense.description ||
                    !this.newExpense.amount
                ) {
                    alert('Veuillez remplir tous les champs obligatoires');
                    return;
                }

                try {
                    const response = await axios.post(
                        `${window.location.origin}/expenses/add`,
                        {
                            type: this.newExpense.type,
                            description: this.newExpense.description,
                            amount: this.newExpense.amount,
                            date: this.newExpense.date,
                        }
                    );

                    if (response.data.success) {
                        this.expenses.push(response.data.data);
                        this.closeAddExpenseModal();
                        alert('Dépense ajoutée avec succès');
                    }
                } catch (error) {
                    console.error(
                        "Erreur lors de l'ajout de la dépense:",
                        error
                    );
                    alert("Erreur lors de l'ajout de la dépense");
                }
            },

            async deleteExpense(id) {
                if (
                    !confirm(
                        'Êtes-vous sûr de vouloir supprimer cette dépense ?'
                    )
                )
                    return;

                try {
                    const response = await axios.delete(
                        `${window.location.origin}/expenses/${id}`
                    );
                    if (response.data.success) {
                        this.expenses = this.expenses.filter(
                            (e) => e.id !== id
                        );
                        alert('Dépense supprimée avec succès');
                    }
                } catch (error) {
                    console.error('Erreur lors de la suppression:', error);
                    alert('Erreur lors de la suppression');
                }
            },

            printList() {
                const printContent = `
                <html>
                    <head>
                        <title>Dépenses - SAger Market</title>
                        <style>
                            * { margin: 0; padding: 0; box-sizing: border-box; }
                            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                            .print-header {
                                text-align: center;
                                margin-bottom: 2rem;
                                border-bottom: 2px solid #333;
                                padding-bottom: 1rem;
                            }
                            .company-info { margin-bottom: 0.5rem; font-weight: bold; }
                            table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
                            th, td { border: 1px solid #ddd; padding: 0.75rem; text-align: left; }
                            th { background-color: #f8f9fa; font-weight: bold; }
                            .amount { text-align: right; }
                            .total-row { background-color: #e9ecef; font-weight: bold; }
                            @media print { body { margin: 0; } }
                        </style>
                    </head>
                    <body>
                        <div class="print-header">
                            <div class="company-info">SAger Market</div>
                            <div>Tél: +229 0196466625</div>
                            <div>IFU: 0202586942320</div>
                            <h2 style="margin-top: 1rem;">Rapport de Dépenses</h2>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th class="amount">Montant (FCFA)</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${this.filteredExpenses
                                    .map(
                                        (expense) => `
                                    <tr>
                                        <td>${expense.type}</td>
                                        <td>${expense.description}</td>
                                        <td class="amount">${this.formatAmount(
                                            expense.amount
                                        )}</td>
                                        <td>${new Date(
                                            expense.date
                                        ).toLocaleDateString('fr-FR')}</td>
                                    </tr>
                                `
                                    )
                                    .join('')}
                                <tr class="total-row">
                                    <td colspan="2">Total</td>
                                    <td class="amount">${this.formatAmount(
                                        this.totalAmount
                                    )}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </body>
                </html>
            `;

                const printWindow = window.open('', '', 'height=600,width=800');
                printWindow.document.write(printContent);
                printWindow.document.close();
                printWindow.print();
            },

            formatAmount(amount) {
                return parseFloat(amount).toLocaleString('fr-FR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                });
            },

            formatDate(date) {
                return new Date(date).toLocaleDateString('fr-FR', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                });
            },

            getTypeColor(type) {
                return this.typeColors[type] || '#95A5A6';
            },

            nextPage() {
                if (this.currentPage < this.totalPages) {
                    this.currentPage++;
                }
            },

            previousPage() {
                if (this.currentPage > 1) {
                    this.currentPage--;
                }
            },

            getTotalByType(type) {
                return this.filteredExpenses
                    .filter((e) => e.type === type)
                    .reduce((sum, e) => sum + parseFloat(e.amount), 0);
            },

            sanitizeType(type) {
                return type
                    .toLowerCase()
                    .replace(/\s+/g, '-')
                    .replace(/[^a-z0-9-]/g, '');
            },
        },

        computed: {
            filteredExpenses() {
                let filtered = this.expenses;

                // Filter by search query
                if (this.searchQuery) {
                    const query = this.searchQuery.toLowerCase();
                    filtered = filtered.filter(
                        (e) =>
                            e.description.toLowerCase().includes(query) ||
                            e.type.toLowerCase().includes(query)
                    );
                }

                // Filter by type
                if (this.filterType) {
                    filtered = filtered.filter(
                        (e) => e.type === this.filterType
                    );
                }

                // Filter by date
                if (this.dateFilterMode === 'specific' && this.filterDate) {
                    filtered = filtered.filter(
                        (e) =>
                            this.formatDate(e.date) ===
                            this.formatDate(this.filterDate)
                    );
                } else if (
                    this.dateFilterMode === 'range' &&
                    this.filterDateStart &&
                    this.filterDateEnd
                ) {
                    const start = new Date(this.filterDateStart);
                    const end = new Date(this.filterDateEnd);
                    filtered = filtered.filter((e) => {
                        const expenseDate = new Date(e.date);
                        return expenseDate >= start && expenseDate <= end;
                    });
                }

                // Sort
                switch (this.sortOption) {
                    case 'Type (A-Z)':
                        filtered.sort((a, b) => a.type.localeCompare(b.type));
                        break;
                    case 'Type (Z-A)':
                        filtered.sort((a, b) => b.type.localeCompare(a.type));
                        break;
                    case 'Montant (croissant)':
                        filtered.sort(
                            (a, b) =>
                                parseFloat(a.amount) - parseFloat(b.amount)
                        );
                        break;
                    case 'Montant (décroissant)':
                        filtered.sort(
                            (a, b) =>
                                parseFloat(b.amount) - parseFloat(a.amount)
                        );
                        break;
                    case 'Date (récent)':
                        filtered.sort(
                            (a, b) => new Date(b.date) - new Date(a.date)
                        );
                        break;
                    case 'Date (ancien)':
                        filtered.sort(
                            (a, b) => new Date(a.date) - new Date(b.date)
                        );
                        break;
                }

                this.currentPage = 1;
                return filtered;
            },

            paginatedExpenses() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                const end = start + this.itemsPerPage;
                return this.filteredExpenses.slice(start, end);
            },

            totalPages() {
                return Math.ceil(
                    this.filteredExpenses.length / this.itemsPerPage
                );
            },

            totalAmount() {
                return this.filteredExpenses.reduce(
                    (sum, expense) => sum + parseFloat(expense.amount),
                    0
                );
            },
        },
    };
</script>

<style scoped>
    .expenses-content {
        padding: 20px;
        background: #f8f9fa;
        min-height: 100vh;
    }

    /* Colorful statistics cards with gradients */
    .statistics-section {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        display: flex;
        gap: 1rem;
        padding: 1.5rem;
        border-radius: 12px;
        color: white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .stat-card-1 {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .stat-card-2 {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .stat-card-3 {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .stat-card-4 {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }

    .stat-icon {
        font-size: 2.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0.8;
    }

    .stat-info {
        flex: 1;
    }

    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 1.8rem;
        font-weight: bold;
    }

    .type-badge-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .type-badge {
        display: inline-block;
        background: rgba(255, 255, 255, 0.2);
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        white-space: nowrap;
        font-weight: 500;
    }

    .no-expenses-message {
        font-size: 0.9rem;
        opacity: 0.8;
        margin-top: 0.5rem;
    }

    /* Header */
    .expenses-header {
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .expenses-header h2 {
        color: #333;
        font-size: 1.8rem;
        font-weight: 600;
    }

    .header-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        align-items: center;
    }

    .search-input,
    .filter-select {
        padding: 0.75rem 1rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 0.95rem;
        background: white;
        flex: 1;
        min-width: 150px;
    }

    .search-input {
        flex: 2;
        min-width: 250px;
    }

    /* Buttons */
    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.95rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    .btn-print {
        background: #17a2b8;
        color: white;
    }

    .btn-print:hover {
        background: #138496;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(23, 162, 184, 0.4);
    }

    .btn-icon {
        padding: 8px 12px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 14px;
    }

    .btn-delete {
        background: #dc3545;
        color: white;
    }

    .btn-delete:hover {
        background: #c82333;
    }

    /* Filters */
    .filters-section {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        align-items: center;
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .date-range {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .date-range input {
        padding: 0.75rem 1rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 0.95rem;
    }

    /* Table */
    .table-container {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
        overflow: hidden;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table thead {
        background: #f8f9fa;
    }

    .table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
    }

    .table td {
        padding: 1rem;
        border-bottom: 1px solid #dee2e6;
    }

    .table tbody tr:hover {
        background: #f8f9fa;
    }

    .type-badge {
        display: inline-block;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
        white-space: nowrap;
        color: white;
    }

    .type-badge.type-loyer {
        background: #667eea;
    }
    .type-badge.type-salaires {
        background: #f093fb;
    }
    .type-badge.type-fournisseurs {
        background: #4facfe;
    }
    .type-badge.type-electricite-eau-internet {
        background: #43e97b;
    }
    .type-badge.type-marketing-publicite {
        background: #f5576c;
    }
    .type-badge.type-maintenance-reparation {
        background: #ffa502;
    }
    .type-badge.type-transport-livraison {
        background: #1ea179;
    }
    .type-badge.type-taxes-impots {
        background: #eb3b5a;
    }
    .type-badge.type-formations-developpement-du-personnel {
        background: #5f27cd;
    }
    .type-badge.type-autres {
        background: #a4b0bd;
    }

    .amount {
        text-align: right;
        font-weight: 500;
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        margin-top: 2rem;
    }

    .btn-pagination {
        padding: 0.5rem 1rem;
        background: #f0f0f0;
        border: 1px solid #ddd;
        color: #333;
    }

    .btn-pagination:hover:not(:disabled) {
        background: #e0e0e0;
    }

    .btn-pagination:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .page-info {
        color: #666;
        font-weight: 500;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 10px;
        color: #999;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 15px;
        display: block;
        opacity: 0.5;
    }

    /* Modal */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .modal {
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid #e9ecef;
    }

    .modal-header h3 {
        margin: 0;
        color: #333;
    }

    .btn-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #666;
    }

    .btn-close:hover {
        color: #333;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #333;
    }

    .form-input {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 0.95rem;
        font-family: inherit;
    }

    .form-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        padding: 1.5rem;
        border-top: 1px solid #e9ecef;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .statistics-section {
            grid-template-columns: 1fr;
        }

        .expenses-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .header-actions {
            width: 100%;
            flex-direction: column;
        }

        .search-input {
            min-width: 100%;
        }

        .filters-section {
            flex-direction: column;
        }

        .filter-select,
        .date-range input {
            width: 100%;
        }

        .date-range {
            width: 100%;
            justify-content: space-between;
        }

        .date-range span {
            flex: 0 0 auto;
        }

        .table {
            font-size: 0.9rem;
        }

        .table th,
        .table td {
            padding: 0.75rem 0.5rem;
        }

        .modal {
            width: 95%;
            max-height: 95vh;
        }
    }
</style>
