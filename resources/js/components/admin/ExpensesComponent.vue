<template>
    <div class="expenses-content">
        <!-- Statistics section -->
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
        </div>

        <!-- Header Actions -->
        <div class="expenses-header">
            <h2>Gestion des Dépenses</h2>
            <div class="header-actions">
                <button
                    class="btn btn-primary"
                    @click="showAddExpenseDiv = true"
                >
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
                />
                <span>à</span>
                <input
                    type="date"
                    class="filter-select"
                    v-model="filterDateEnd"
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
                        <!-- Added Actions column -->
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="expense in paginatedExpenses" :key="expense.id">
                        <td data-label="Type">
                            <span
                                class="type-badge"
                                :style="getBadgeStyle(expense.type)"
                            >
                                {{ expense.type }}
                            </span>
                        </td>

                        <td data-label="Description">
                            {{ expense.description }}
                        </td>

                        <td data-label="Montant" class="amount">
                            {{ formatAmount(expense.amount) }} FCFA
                        </td>

                        <td data-label="Date">
                            {{
                                new Date(expense.date).toLocaleDateString(
                                    'fr-FR'
                                )
                            }}
                        </td>

                        <td data-label="Actions">
                            <div class="action-buttons">
                                <button
                                    class="btn-sm btn-edit"
                                    title="Modifier"
                                    @click="openEditExpenseModal(expense)"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button
                                    class="btn-sm btn-delete"
                                    title="Supprimer"
                                    @click="confirmDeleteExpense(expense)"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
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

        <!-- Pop-up Div for Adding Expense -->
        <div
            v-if="showAddExpenseDiv"
            class="popup-overlay"
            @click.self="closeAddExpenseDiv"
        >
            <div class="popup-box">
                <div class="popup-header">
                    <h3>Ajouter une dépense</h3>
                    <button class="popup-close-btn" @click="closeAddExpenseDiv">
                        X
                    </button>
                </div>
                <div class="popup-body">
                    <div class="form-group">
                        <label>Type de dépense *</label>
                        <select v-model="newExpense.type">
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
                            rows="4"
                            placeholder="Détails de la dépense"
                        ></textarea>
                    </div>
                    <div class="form-group">
                        <label>Montant (FCFA) *</label>
                        <input
                            v-model.number="newExpense.amount"
                            type="number"
                            min="0"
                            step="0.01"
                        />
                    </div>
                    <div class="form-group">
                        <label>Date *</label>
                        <input v-model="newExpense.date" type="date" />
                    </div>
                </div>
                <div class="popup-footer">
                    <button
                        class="btn btn-secondary"
                        @click="closeAddExpenseDiv"
                    >
                        Annuler
                    </button>
                    <button class="btn btn-primary" @click="addExpense">
                        Ajouter
                    </button>
                </div>
            </div>
        </div>

        <!-- Added Edit Expense Modal -->
        <div
            v-if="showEditExpenseDiv"
            class="popup-overlay"
            @click.self="closeEditExpenseDiv"
        >
            <div class="popup-box">
                <div class="popup-header">
                    <h3>Modifier la dépense</h3>
                    <button
                        class="popup-close-btn"
                        @click="closeEditExpenseDiv"
                    >
                        X
                    </button>
                </div>
                <div class="popup-body">
                    <div class="form-group">
                        <label>Type de dépense *</label>
                        <select v-model="editExpense.type">
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
                            v-model="editExpense.description"
                            rows="4"
                            placeholder="Détails de la dépense"
                        ></textarea>
                    </div>
                    <div class="form-group">
                        <label>Montant (FCFA) *</label>
                        <input
                            v-model.number="editExpense.amount"
                            type="number"
                            min="0"
                            step="0.01"
                        />
                    </div>
                    <div class="form-group">
                        <label>Date *</label>
                        <input v-model="editExpense.date" type="date" />
                    </div>
                </div>
                <div class="popup-footer">
                    <button
                        class="btn btn-secondary"
                        @click="closeEditExpenseDiv"
                    >
                        Annuler
                    </button>
                    <button class="btn btn-primary" @click="updateExpense">
                        Modifier
                    </button>
                </div>
            </div>
        </div>

        <!-- Added Delete Confirmation Modal -->
        <div
            v-if="showDeleteConfirmation"
            class="popup-overlay"
            @click.self="showDeleteConfirmation = false"
        >
            <div class="popup-box" style="max-width: 400px">
                <div
                    class="popup-header"
                    style="
                        background: linear-gradient(
                            135deg,
                            #dc3545 0%,
                            #c82333 100%
                        );
                    "
                >
                    <h3>Confirmer la suppression</h3>
                    <button
                        class="popup-close-btn"
                        @click="showDeleteConfirmation = false"
                    >
                        X
                    </button>
                </div>
                <div class="popup-body">
                    <p style="color: #495057; line-height: 1.6">
                        Êtes-vous sûr de vouloir supprimer cette dépense ? Cette
                        action est irréversible.
                    </p>
                    <div
                        v-if="expenseToDelete"
                        style="
                            margin-top: 1rem;
                            padding: 1rem;
                            background: #f8f9fa;
                            border-radius: 8px;
                        "
                    >
                        <p style="margin: 0.25rem 0">
                            <strong>Type:</strong>
                            {{ expenseToDelete.type }}
                        </p>
                        <p style="margin: 0.25rem 0">
                            <strong>Montant:</strong>
                            {{ formatAmount(expenseToDelete.amount) }} FCFA
                        </p>
                        <p style="margin: 0.25rem 0">
                            <strong>Date:</strong>
                            {{
                                new Date(
                                    expenseToDelete.date
                                ).toLocaleDateString('fr-FR')
                            }}
                        </p>
                    </div>
                </div>
                <div class="popup-footer">
                    <button
                        class="btn btn-secondary"
                        @click="showDeleteConfirmation = false"
                    >
                        Annuler
                    </button>
                    <button class="btn btn-danger" @click="deleteExpense">
                        Supprimer
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
                showAddExpenseDiv: false,
                showEditExpenseDiv: false,
                showDeleteConfirmation: false,
                expenseToDelete: null,
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
                editExpense: {
                    id: null,
                    type: '',
                    description: '',
                    amount: null,
                    date: '',
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
            closeAddExpenseDiv() {
                this.showAddExpenseDiv = false;
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
                if (this.newExpense.amount <= 0) {
                    alert('Le montant doit être supérieur à 0');
                    return;
                }
                try {
                    const response = await axios.post(
                        `${window.location.origin}/expenses/add`,
                        this.newExpense
                    );
                    if (response.data.success) {
                        await this.fetchExpenses();
                        this.closeAddExpenseDiv();
                        alert('Dépense ajoutée avec succès');
                        this.newExpense = {
                            type: '',
                            description: '',
                            amount: null,
                            date: new Date().toISOString().split('T')[0],
                        };
                    }
                } catch (error) {
                    console.error(
                        "Erreur lors de l'ajout de la dépense:",
                        error
                    );
                    alert("Erreur lors de l'ajout de la dépense");
                }
            },
            formatAmount(amount) {
                return parseFloat(amount).toLocaleString('fr-FR', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0,
                });
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

            getBadgeStyle(type) {
                const colorMap = {
                    Loyer: {
                        bg: '#e3f2fd',
                        color: '#1976d2',
                        border: '#90caf9',
                    },
                    Salaire: {
                        bg: '#e8f5e9',
                        color: '#388e3c',
                        border: '#a5d6a7',
                    },
                    Fournisseurs: {
                        bg: '#fff3e0',
                        color: '#f57c00',
                        border: '#ffcc80',
                    },
                    'Électricité / Eau / Internet': {
                        bg: '#fff9c4',
                        color: '#f9a825',
                        border: '#fff59d',
                    },
                    'Marketing / Publicité': {
                        bg: '#f3e5f5',
                        color: '#7b1fa2',
                        border: '#ce93d8',
                    },
                    'Maintenance / Réparation': {
                        bg: '#ffebee',
                        color: '#c62828',
                        border: '#ef9a9a',
                    },
                    'Transport / Livraison': {
                        bg: '#e0f2f1',
                        color: '#00796b',
                        border: '#80cbc4',
                    },
                    'Taxes / Impôts': {
                        bg: '#fce4ec',
                        color: '#c2185b',
                        border: '#f48fb1',
                    },
                    'Formations / Développement du personnel': {
                        bg: '#e8eaf6',
                        color: '#3f51b5',
                        border: '#9fa8da',
                    },
                    Autres: {
                        bg: '#f5f5f5',
                        color: '#616161',
                        border: '#e0e0e0',
                    },
                };

                const colors = colorMap[type] || colorMap['Autres'];
                return {
                    backgroundColor: colors.bg,
                    color: colors.color,
                    border: `1px solid ${colors.border}`,
                    padding: '0.4rem 0.8rem',
                    borderRadius: '20px',
                    fontSize: '0.85rem',
                    fontWeight: '600',
                    display: 'inline-block',
                };
            },

            openEditExpenseModal(expense) {
                this.editExpense = {
                    id: expense.id,
                    type: expense.type,
                    description: expense.description,
                    amount: expense.amount,
                    date: expense.date,
                };
                this.showEditExpenseDiv = true;
            },

            closeEditExpenseDiv() {
                this.showEditExpenseDiv = false;
                this.editExpense = {
                    id: null,
                    type: '',
                    description: '',
                    amount: null,
                    date: '',
                };
            },

            async updateExpense() {
                if (
                    !this.editExpense.type ||
                    !this.editExpense.description ||
                    !this.editExpense.amount
                ) {
                    alert('Veuillez remplir tous les champs obligatoires');
                    return;
                }

                if (this.editExpense.amount <= 0) {
                    alert('Le montant doit être supérieur à 0');
                    return;
                }

                try {
                    const response = await axios.post(
                        `${window.location.origin}/expenses/update/${this.editExpense.id}`,
                        this.editExpense
                    );

                    if (response.data.success) {
                        await this.fetchExpenses();
                        this.closeEditExpenseDiv();
                        alert('Dépense modifiée avec succès');
                    }
                } catch (error) {
                    console.error(
                        'Erreur lors de la modification de la dépense:',
                        error
                    );
                    alert('Erreur lors de la modification de la dépense');
                }
            },
            confirmDeleteExpense(expense) {
                this.expenseToDelete = expense;
                this.showDeleteConfirmation = true;
            },

            async deleteExpense() {
                if (!this.expenseToDelete) return;

                try {
                    const response = await axios.post(
                        `${window.location.origin}/expenses/delete/${this.expenseToDelete.id}`
                    );

                    if (response.data.success) {
                        await this.fetchExpenses();
                        this.showDeleteConfirmation = false;
                        this.expenseToDelete = null;
                        alert('Dépense supprimée avec succès');
                    }
                } catch (error) {
                    console.error(
                        'Erreur lors de la suppression de la dépense:',
                        error
                    );
                    alert('Erreur lors de la suppression de la dépense');
                }
            },
            printList() {
                let totalAmount = 0;
                const tableRows = this.filteredExpenses
                    .map((expense, index) => {
                        totalAmount += parseFloat(expense.amount);
                        return `
                            <tr>
                                <td style="padding: 12px; border: 1px solid #ddd;">${
                                    index + 1
                                }</td>
                                <td style="padding: 12px; border: 1px solid #ddd;">${new Date(
                                    expense.date
                                ).toLocaleDateString('fr-FR')}</td>
                                <td style="padding: 12px; border: 1px solid #ddd;">${
                                    expense.type
                                }</td>
                                <td style="padding: 12px; border: 1px solid #ddd;">${
                                    expense.description
                                }</td>
                                <td style="padding: 12px; border: 1px solid #ddd; text-align: right;">${this.formatAmount(
                                    expense.amount
                                )} FCFA</td>
                            </tr>
                        `;
                    })
                    .join('');

                const htmlContent = `
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Liste des Dépenses</title>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                margin: 20px;
                                color: #333;
                            }
                            h1 {
                                color: #667eea;
                                text-align: center;
                                margin-bottom: 10px;
                            }
                            h2 {
                                text-align: center;
                                margin-bottom: 20px;
                                color: #333;
                            }
                            .company-info {
                                text-align: center;
                                margin-bottom: 30px;
                            }
                            .company-info p {
                                margin: 5px 0;
                                color: #666;
                            }
                            .info {
                                background: #f8f9fa;
                                padding: 15px;
                                border-radius: 8px;
                                margin-bottom: 20px;
                                border-left: 4px solid #667eea;
                            }
                            .info p {
                                margin: 5px 0;
                            }
                            table {
                                width: 100%;
                                border-collapse: collapse;
                                margin-top: 20px;
                                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                            }
                            thead {
                                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                color: white;
                            }
                            th {
                                padding: 12px;
                                text-align: left;
                                border: 1px solid #667eea;
                            }
                            th:last-child {
                                text-align: right;
                            }
                            tbody tr:nth-child(even) {
                                background: #f8f9fa;
                            }
                            tbody tr:hover {
                                background: #e9ecef;
                            }
                            .total-row {
                                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
                                color: white;
                                font-weight: bold;
                            }
                            button {
                                margin-top: 20px;
                                padding: 10px 20px;
                                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                color: white;
                                border: none;
                                border-radius: 8px;
                                cursor: pointer;
                                font-size: 16px;
                                box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
                            }
                            button:hover {
                                transform: translateY(-2px);
                                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
                            }
                            @media print {
                                button { display: none; }
                            }
                        </style>
                    </head>
                    <body>
                        <h1>SAGER MARKET</h1>
                        <div class="company-info">
                            <p><strong>Téléphone:</strong> +229 0196466625</p>
                            <p><strong>IFU:</strong> 0202586942320</p>
                        </div>
                        <h2>Liste des Dépenses</h2>
                        <div class="info">
                            <p><strong>Date d'impression:</strong> ${new Date().toLocaleString(
                                'fr-FR'
                            )}</p>
                            <p><strong>Nombre total de dépenses:</strong> ${
                                this.filteredExpenses.length
                            }</p>
                        </div>

                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th style="text-align: right;">Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows}
                                <tr class="total-row">
                                    <td colspan="4" style="padding: 12px; border: 1px solid #667eea; text-align: right;">TOTAL:</td>
                                    <td style="padding: 12px; border: 1px solid #667eea; text-align: right;">${this.formatAmount(
                                        totalAmount
                                    )} FCFA</td>
                                </tr>
                            </tbody>
                        </table>

                        <button onclick="window.print()">Imprimer</button>
                    </body>
                    </html>
                `;

                const printWindow = window.open('', '', 'height=600,width=800');
                printWindow.document.write(htmlContent);
                printWindow.document.close();
            },
        },
        computed: {
            filteredExpenses() {
                let filtered = this.expenses;
                if (this.searchQuery)
                    filtered = filtered.filter((e) =>
                        e.description
                            .toLowerCase()
                            .includes(this.searchQuery.toLowerCase())
                    );
                if (this.filterType)
                    filtered = filtered.filter(
                        (e) => e.type === this.filterType
                    );
                if (this.dateFilterMode === 'specific' && this.filterDate)
                    filtered = filtered.filter(
                        (e) => e.date === this.filterDate
                    );
                if (
                    this.dateFilterMode === 'range' &&
                    this.filterDateStart &&
                    this.filterDateEnd
                ) {
                    const start = new Date(this.filterDateStart),
                        end = new Date(this.filterDateEnd);
                    filtered = filtered.filter((e) => {
                        const d = new Date(e.date);
                        return d >= start && d <= end;
                    });
                }
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
                return filtered;
            },
            totalAmount() {
                return this.filteredExpenses.reduce(
                    (sum, e) => sum + parseFloat(e.amount),
                    0
                );
            },
            totalPages() {
                return Math.ceil(
                    this.filteredExpenses.length / this.itemsPerPage
                );
            },
            paginatedExpenses() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                return this.filteredExpenses.slice(
                    start,
                    start + this.itemsPerPage
                );
            },
        },
        watch: {
            filteredExpenses() {
                this.currentPage = 1;
            },
        },
    };
</script>

<style scoped>
    /* Applied ClaimsComponent color scheme and design system */

    /* Header */
    .expenses-header {
        margin-bottom: 2rem;
    }

    .expenses-header h2 {
        color: #333;
        margin-bottom: 1.5rem;
        font-size: 1.8rem;
        font-weight: 600;
    }

    .header-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }

    /* Statistics section */
    .statistics-section {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-left: 4px solid #667eea;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .stat-icon {
        font-size: 2.5rem;
        color: #667eea;
    }

    .stat-info {
        flex: 1;
    }

    .stat-label {
        color: #6c757d;
        font-size: 0.9rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        color: #667eea;
        font-size: 1.8rem;
        font-weight: 700;
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

    .btn-pagination {
        padding: 0.75rem 1rem;
        border: 1px solid #667eea;
        background: white;
        color: #667eea;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-pagination:hover:not(:disabled) {
        background: #667eea;
        color: white;
    }

    .btn-pagination:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Inputs */
    .search-input,
    .filter-select {
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 0.95rem;
        min-width: 200px;
        transition: border-color 0.3s ease;
    }

    .search-input:focus,
    .filter-select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    /* Filters Section */
    .filters-section {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 2rem;
        align-items: center;
    }

    .date-range {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .date-range span {
        color: #6c757d;
        font-weight: 500;
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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        border: none;
    }

    .table tbody tr {
        border-bottom: 1px solid #dee2e6;
        transition: background-color 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .table td {
        padding: 1rem;
        color: #495057;
    }

    .amount {
        font-weight: 600;
        color: #667eea;
    }

    .type-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        background: #e9ecef;
        color: #495057;
        display: inline-block;
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        margin-top: 2rem;
        padding: 1.5rem;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .page-info {
        font-weight: 500;
        color: #667eea;
        min-width: 120px;
        text-align: center;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .empty-state i {
        font-size: 3rem;
        color: #dee2e6;
        margin-bottom: 1rem;
    }

    .empty-state p {
        color: #6c757d;
        font-size: 1.1rem;
    }

    /* Pop-up Overlay */
    .popup-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .popup-box {
        background: white;
        border-radius: 12px;
        padding: 0;
        width: 500px;
        max-width: 90%;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        max-height: 90vh;
        overflow-y: auto;
    }

    .popup-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px 12px 0 0;
    }

    .popup-header h3 {
        margin: 0;
        font-size: 1.3rem;
        font-weight: 600;
    }

    .popup-close-btn {
        cursor: pointer;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        font-size: 1.2rem;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.3s ease;
    }

    .popup-close-btn:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .popup-body {
        padding: 2rem;
    }

    .popup-body .form-group {
        margin-bottom: 1.5rem;
    }

    .popup-body label {
        display: block;
        margin-bottom: 0.5rem;
        color: #495057;
        font-weight: 500;
        font-size: 0.95rem;
    }

    .popup-body select,
    .popup-body input,
    .popup-body textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: border-color 0.3s ease;
    }

    .popup-body select:focus,
    .popup-body input:focus,
    .popup-body textarea:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .popup-body textarea {
        resize: vertical;
        font-family: inherit;
    }

    .popup-footer {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 0 0 12px 12px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .statistics-section {
            grid-template-columns: 1fr;
        }

        .header-actions {
            flex-direction: column;
            width: 100%;
        }

        .search-input,
        .filter-select {
            width: 100%;
        }

        .table-container {
            overflow-x: auto;
        }

        .pagination {
            flex-wrap: wrap;
        }

        .btn-pagination {
            flex: 1;
            min-width: 100px;
        }
    }

    /* Added action buttons styles */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-start;
    }

    .btn-sm {
        padding: 0.5rem 0.75rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-edit {
        background-color: #ffc107;
        color: white;
    }

    .btn-edit:hover {
        background-color: #e0a800;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
    }

    .btn-delete {
        background-color: #dc3545;
        color: white;
    }

    .btn-delete:hover {
        background-color: #c82333;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
    }

    /* Added danger button style for delete confirmation */
    .btn-danger {
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
        background: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background: #c82333;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
    }
</style>
