<template>
    <div>
        <div class="sales-content">
            <div class="sales-history">
                <div class="history-header">
                    <h3>Historique des consignations</h3>
                    <div
                        style="
                            display: flex;
                            gap: 1rem;
                            align-items: center;
                            flex-wrap: wrap;
                        "
                    >
                        <select
                            v-model="filterPeriod"
                            style="
                                padding: 0.5rem;
                                border: 1px solid #ddd;
                                border-radius: 5px;
                            "
                        >
                            <option>Aujourd'hui</option>
                            <option>Hier</option>
                            <option>Cette semaine</option>
                            <option>Ce mois</option>
                            <option>Toutes les consignations</option>
                            <option>À une date précise</option>
                        </select>

                        <select
                            v-model="filterStatus"
                            style="
                                padding: 0.5rem;
                                border: 1px solid #ddd;
                                border-radius: 5px;
                            "
                        >
                            <option value="">Tous les statuts</option>
                            <option value="En cours">En cours</option>
                            <option value="Terminée">Terminée</option>
                            <option value="Annulée">Annulée</option>
                        </select>

                        <button
                            @click="printCurrentList"
                            class="btn-primary"
                            style="
                                background: #17a2b8;
                                color: white;
                                border: none;
                                padding: 0.5rem 1rem;
                                border-radius: 5px;
                                cursor: pointer;
                                display: inline-flex;
                                align-items: center;
                                gap: 0.5rem;
                            "
                        >
                            <i class="fas fa-print"></i>
                            Imprimer la liste
                        </button>

                        <div
                            v-if="filterPeriod === 'À une date précise'"
                            style="width: 100%; margin-top: 0.5rem"
                        >
                            <input type="date" v-model="selectedDate" />
                        </div>
                    </div>
                </div>

                <table class="table" v-if="paginatedDeposits.length > 0">
                    <thead>
                        <tr>
                            <th>N° Facture</th>
                            <th>Date</th>
                            <th>Produit</th>
                            <th>Prix unitaire</th>
                            <th>Quantité</th>
                            <th>Total</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="deposit in paginatedDeposits"
                            :key="deposit.id"
                        >
                            <td data-label="N° Vente">
                                <strong>N° {{ deposit.sale_id }} /FR-N</strong>
                            </td>
                            <td data-label="Date">
                                {{ formatDateTime(deposit.created_at) }}
                            </td>
                            <td data-label="Produit">
                                {{ deposit.product_name }}
                            </td>
                            <td data-label="Prix unitaire">
                                <strong>
                                    {{
                                        formatAmount(
                                            deposit.deposit_price_at_sale
                                        )
                                    }}
                                    FCFA
                                </strong>
                            </td>
                            <td data-label="Quantité">
                                {{ deposit.quantity }}
                            </td>
                            <td data-label="Total">
                                <strong>
                                    {{ formatAmount(deposit.total) }} FCFA
                                </strong>
                            </td>
                            <td data-label="Statut">
                                <span
                                    :class="getStatusClass(deposit.status)"
                                    style="
                                        padding: 0.25rem 0.75rem;
                                        border-radius: 20px;
                                        font-size: 0.85rem;
                                        font-weight: 600;
                                        display: inline-block;
                                    "
                                >
                                    {{ deposit.status }}
                                </span>
                            </td>
                            <td data-label="Actions">
                                <button
                                    class="invoice-btn"
                                    @click="showDepositDetails(deposit)"
                                    style="
                                        background: #667eea;
                                        color: white;
                                        border: none;
                                        padding: 0.5rem 1rem;
                                        border-radius: 5px;
                                        cursor: pointer;
                                        margin: 5px;
                                    "
                                >
                                    <i
                                        class="fas fa-eye"
                                        style="margin-right: 5px"
                                    ></i>
                                    Voir facture
                                </button>

                                <button
                                    v-if="
                                        deposit.status === 'En attente' ||
                                        deposit.status === 'En cours'
                                    "
                                    class="action-btn terminate-btn"
                                    @click="terminateDeposit(deposit.id)"
                                    style="
                                        background: #28a745;
                                        color: white;
                                        border: none;
                                        padding: 0.5rem 1rem;
                                        border-radius: 5px;
                                        cursor: pointer;
                                        margin: 5px;
                                    "
                                >
                                    <i class="fas fa-check"></i>
                                    Terminer
                                </button>

                                <button
                                    v-if="deposit.status === 'Terminée'"
                                    class="action-btn restore-btn"
                                    @click="restoreDeposit(deposit.id)"
                                    style="
                                        background: #ffc107;
                                        color: #333;
                                        border: none;
                                        padding: 0.5rem 1rem;
                                        border-radius: 5px;
                                        cursor: pointer;
                                        margin: 5px;
                                    "
                                >
                                    <i class="fas fa-undo"></i>
                                    Rétablir
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-else class="no-sales-message">
                    <strong>
                        Aucune consignation disponible pour la période
                        sélectionnée
                    </strong>
                </div>

                <div class="pagination-container" v-if="totalPages > 1">
                    <ul class="pagination">
                        <li
                            class="page-item"
                            :class="{ disabled: currentPage === 1 }"
                        >
                            <a
                                class="page-link"
                                href="#"
                                @click.prevent="goToPage(currentPage - 1)"
                            >
                                Précédent
                            </a>
                        </li>
                        <li
                            class="page-item"
                            v-for="page in totalPages"
                            :key="page"
                            :class="{ active: currentPage === page }"
                        >
                            <a
                                class="page-link"
                                href="#"
                                @click.prevent="goToPage(page)"
                            >
                                {{ page }}
                            </a>
                        </li>
                        <li
                            class="page-item"
                            :class="{ disabled: currentPage === totalPages }"
                        >
                            <a
                                class="page-link"
                                href="#"
                                @click.prevent="goToPage(currentPage + 1)"
                            >
                                Suivant
                            </a>
                        </li>
                    </ul>
                </div>

                <div v-if="showDepositModal" class="modal-overlay">
                    <div class="modal-container">
                        <div class="modal-header">
                            <h5>Détails de la consignation</h5>
                            <button
                                @click="closeDepositModal"
                                class="modal-close"
                            >
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>
                                <strong>N° Consignation :</strong>
                                {{ selectedDeposit.id }}
                            </p>
                            <p>
                                <strong>N° Vente :</strong>
                                {{ selectedDeposit.sale_id }}
                            </p>
                            <p>
                                <strong>Produit :</strong>
                                {{ selectedDeposit.product_name }}
                            </p>
                            <p>
                                <strong>Prix unitaire :</strong>
                                {{
                                    formatAmount(
                                        selectedDeposit.deposit_price_at_sale
                                    )
                                }}
                                FCFA
                            </p>
                            <p>
                                <strong>Quantité :</strong>
                                {{ selectedDeposit.quantity }}
                            </p>
                            <p>
                                <strong>Total :</strong>
                                {{ formatAmount(selectedDeposit.total) }} FCFA
                            </p>
                            <p>
                                <strong>Statut :</strong>
                                <span
                                    :class="
                                        getStatusClass(selectedDeposit.status)
                                    "
                                    style="
                                        padding: 0.25rem 0.75rem;
                                        border-radius: 20px;
                                        font-size: 0.85rem;
                                        font-weight: 600;
                                        display: inline-block;
                                        margin-left: 0.5rem;
                                    "
                                >
                                    {{ selectedDeposit.status }}
                                </span>
                            </p>
                            <p>
                                <strong>Date de création :</strong>
                                {{ formatDateTime(selectedDeposit.created_at) }}
                            </p>
                            <p>
                                <strong>Dernière mise à jour :</strong>
                                {{ formatDateTime(selectedDeposit.updated_at) }}
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button
                                @click="printDeposit(selectedDeposit.sale_id)"
                                class="btn-download"
                            >
                                <i class="fas fa-print"></i>
                                Imprimer Facture
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'DepositsComponent',

        data() {
            return {
                deposits: [],
                selectedDeposit: null,
                selectedDate: null,
                showDepositModal: false,
                filterPeriod: 'Toutes les consignations',
                filterStatus: '', // Added status filter

                // Pagination
                currentPage: 1,
                perPage: 10,
            };
        },

        mounted() {
            this.fetchDepositsData();
        },

        methods: {
            fetchDepositsData() {
                axios
                    .get('/depositsList')
                    .then((response) => {
                        console.log('[v0] Deposits data:', response.data);
                        this.deposits = response.data;
                    })
                    .catch((error) => {
                        console.error(
                            'Erreur lors de la récupération des consignations :',
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

            showDepositDetails(deposit) {
                this.selectedDeposit = deposit;
                this.showDepositModal = true;
            },

            closeDepositModal() {
                this.showDepositModal = false;
                this.selectedDeposit = null;
            },

            terminateDeposit(id) {
                if (
                    !confirm(
                        'Voulez-vous vraiment terminer cette consignation ?'
                    )
                ) {
                    return;
                }

                axios
                    .post(`/deposits/${id}/update`, {
                        status: 'Terminée', // <-- correspond exactement à l'ENUM
                    })
                    .then(() => {
                        alert('Consignation terminée avec succès.');
                        this.fetchDepositsData();
                    })
                    .catch((error) => {
                        alert(
                            'Erreur lors de la mise à jour de la consignation.'
                        );
                        console.error('[v0] Error terminating deposit:', error);
                    });
            },

            restoreDeposit(id) {
                if (
                    !confirm(
                        'Voulez-vous vraiment rétablir cette consignation ?'
                    )
                ) {
                    return;
                }

                axios
                    .post(`/deposits/${id}/update`, {
                        status: 'En cours', // <-- correspond exactement à l'ENUM
                    })
                    .then(() => {
                        alert('Consignation rétablie avec succès.');
                        this.fetchDepositsData();
                    })
                    .catch((error) => {
                        alert(
                            'Erreur lors de la mise à jour de la consignation.'
                        );
                        console.error('[v0] Error restoring deposit:', error);
                    });
            },

            printDeposit(depositId) {
                // Ouvre une nouvelle fenêtre avec l'URL correcte
                window.open(`/newInvoice/${depositId}`);
            },
            printCurrentList() {
                const printWindow = window.open('', '_blank');
                const printContent = this.generatePrintContent();

                printWindow.document.write(printContent);
                printWindow.document.close();
                printWindow.focus();

                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 250);
            },

            generatePrintContent() {
                let html = `
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Liste des Consignations</title>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                padding: 20px;
                            }
                            h1 {
                                text-align: center;
                                color: #333;
                                margin-bottom: 20px;
                            }
                            .filter-info {
                                text-align: center;
                                margin-bottom: 20px;
                                color: #666;
                            }
                            table {
                                width: 100%;
                                border-collapse: collapse;
                                margin-top: 20px;
                            }
                            th, td {
                                border: 1px solid #ddd;
                                padding: 12px;
                                text-align: left;
                            }
                            th {
                                background-color: #667eea;
                                color: white;
                                font-weight: bold;
                            }
                            tr:nth-child(even) {
                                background-color: #f9f9f9;
                            }
                            .status-badge {
                                padding: 4px 12px;
                                border-radius: 12px;
                                font-size: 0.85em;
                                font-weight: 600;
                                display: inline-block;
                            }
                            .status-completed {
                                background-color: #d4edda;
                                color: #155724;
                            }
                            .status-in-progress {
                                background-color: #fff3cd;
                                color: #856404;
                            }
                            .status-pending {
                                background-color: #f8d7da;
                                color: #721c24;
                            }
                            .total-row {
                                font-weight: bold;
                                background-color: #f0f0f0;
                            }
                            @media print {
                                body { padding: 0; }
                            }
                        </style>
                    </head>
                    <body>
                        <h1>Liste des Consignations</h1>
                        <div class="filter-info">
                            <p><strong>Période:</strong> ${
                                this.filterPeriod
                            }</p>
                            ${
                                this.filterStatus
                                    ? `<p><strong>Statut:</strong> ${this.filterStatus}</p>`
                                    : ''
                            }
                            <p><strong>Date d'impression:</strong> ${new Date().toLocaleString(
                                'fr-FR'
                            )}</p>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th>N° Consignation</th>
                                    <th>N° Vente</th>
                                    <th>Date</th>
                                    <th>Produit</th>
                                    <th>Prix unitaire</th>
                                    <th>Quantité</th>
                                    <th>Total</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                let totalAmount = 0;
                this.filteredDeposits.forEach((deposit) => {
                    totalAmount += parseFloat(deposit.total);
                    const statusClass = this.getStatusClass(
                        deposit.status
                    ).replace('status-', 'status-');
                    html += `
                        <tr>
                            <td>N° ${deposit.id}</td>
                            <td>N° ${deposit.sale_id}</td>
                            <td>${this.formatDateTime(deposit.created_at)}</td>
                            <td>${deposit.product_name}</td>
                            <td>${this.formatAmount(
                                deposit.deposit_price_at_sale
                            )} FCFA</td>
                            <td>${deposit.quantity}</td>
                            <td>${this.formatAmount(deposit.total)} FCFA</td>
                            <td><span class="status-badge ${statusClass}">${
                        deposit.status
                    }</span></td>
                        </tr>
                    `;
                });

                html += `
                            <tr class="total-row">
                                <td colspan="6" style="text-align: right;">TOTAL GÉNÉRAL:</td>
                                <td colspan="2">${this.formatAmount(
                                    totalAmount
                                )} FCFA</td>
                            </tr>
                        </tbody>
                    </table>
                    </body>
                    </html>
                `;

                return html;
            },

            getStatusClass(status) {
                if (status === 'Terminé') {
                    return 'status-completed';
                } else if (status === 'En cours') {
                    return 'status-in-progress';
                } else if (status === 'En attente') {
                    return 'status-pending';
                }
                return '';
            },

            goToPage(page) {
                if (page >= 1 && page <= this.totalPages) {
                    this.currentPage = page;
                }
            },
        },

        computed: {
            filteredDeposits() {
                const now = new Date();
                this.currentPage = 1;

                return this.deposits.filter((deposit) => {
                    const depositDate = new Date(deposit.created_at);

                    if (
                        this.filterStatus &&
                        deposit.status !== this.filterStatus
                    ) {
                        return false;
                    }

                    switch (this.filterPeriod) {
                        case 'Hier': {
                            const yesterday = new Date(now);
                            yesterday.setDate(now.getDate() - 1);
                            return (
                                depositDate.toDateString() ===
                                yesterday.toDateString()
                            );
                        }
                        case "Aujourd'hui":
                            return (
                                depositDate.toDateString() ===
                                now.toDateString()
                            );

                        case 'Cette semaine': {
                            const firstDayOfWeek = new Date(now);
                            firstDayOfWeek.setDate(
                                now.getDate() - now.getDay() + 1
                            );
                            firstDayOfWeek.setHours(0, 0, 0, 0);

                            const lastDayOfWeek = new Date(firstDayOfWeek);
                            lastDayOfWeek.setDate(firstDayOfWeek.getDate() + 6);
                            lastDayOfWeek.setHours(23, 59, 59, 999);

                            return (
                                depositDate >= firstDayOfWeek &&
                                depositDate <= lastDayOfWeek
                            );
                        }

                        case 'Ce mois': {
                            return (
                                depositDate.getMonth() === now.getMonth() &&
                                depositDate.getFullYear() === now.getFullYear()
                            );
                        }

                        case 'À une date précise': {
                            if (!this.selectedDate) return true;

                            const selected = new Date(this.selectedDate);
                            selected.setHours(0, 0, 0, 0);

                            const depositDay = new Date(deposit.created_at);
                            depositDay.setHours(0, 0, 0, 0);

                            return depositDay.getTime() === selected.getTime();
                        }

                        case 'Toutes les consignations':
                        default:
                            return true;
                    }
                });
            },

            totalPages() {
                return Math.ceil(this.filteredDeposits.length / this.perPage);
            },

            paginatedDeposits() {
                const start = (this.currentPage - 1) * this.perPage;
                const end = start + this.perPage;
                return this.filteredDeposits.slice(start, end);
            },
        },
    };
</script>

<style>
    .status-completed {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .status-in-progress {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .status-pending {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .action-btn {
        transition: all 0.3s ease;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .terminate-btn:hover {
        background: #218838 !important;
    }

    .print-btn:hover {
        background: #138496 !important;
    }

    .restore-btn:hover {
        background: #e0a800 !important;
    }

    /* Added modal styles */
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
        z-index: 2000;
        animation: fadeIn 0.3s ease;
    }

    .modal-container {
        background: white;
        border-radius: 15px;
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        animation: slideIn 0.3s ease;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid #eee;
    }

    .modal-header h5 {
        margin: 0;
        font-size: 1.5rem;
        color: #333;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #999;
        transition: color 0.3s ease;
    }

    .modal-close:hover {
        color: #333;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-body p {
        margin-bottom: 1rem;
        font-size: 1rem;
        line-height: 1.6;
    }

    .modal-body strong {
        color: #333;
        font-weight: 600;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        padding: 1rem 1.5rem;
        border-top: 1px solid #eee;
    }

    .btn-download {
        background: #667eea;
        color: white;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-download:hover {
        background: #5568d3;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes slideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Added responsive styles for mobile */
    @media (max-width: 768px) {
        table.table,
        thead,
        tbody,
        th,
        td,
        tr {
            display: block !important;
            width: 100% !important;
        }

        thead tr {
            display: none !important;
        }

        tbody tr {
            margin-bottom: 1.5rem;
            border: 1px solid #ccc;
            padding: 15px 10px;
            border-radius: 8px;
            background-color: #fff;
            box-sizing: border-box;
        }

        tbody td {
            position: relative;
            padding-left: 50% !important;
            text-align: left !important;
            border: none !important;
            border-bottom: 1px solid #eee !important;
            box-sizing: border-box;
            min-height: 40px;
            vertical-align: top;
        }

        tbody td:last-child {
            border-bottom: 0 !important;
        }

        tbody td::before {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            width: 45%;
            white-space: nowrap;
            font-weight: 600;
            content: attr(data-label);
            color: #333;
        }

        tbody td button {
            display: inline-flex;
            margin-right: 10px;
            margin-bottom: 5px;
        }

        .modal-container {
            width: 95%;
            max-height: 95vh;
        }
    }
</style>
