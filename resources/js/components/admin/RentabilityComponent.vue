<template>
    <div>
        <div class="sales-content">
            <div class="sales-history">
                <div class="history-header">
                    <h3>Rentabilité</h3>
                    <div
                        style="
                            display: flex;
                            gap: 1rem;
                            align-items: center;
                            flex-wrap: wrap;
                        "
                    >
                        <button
                            @click="printList"
                            class="btn-primary"
                            style="background: #17a2b8; padding: 0.5rem 1rem"
                        >
                            <i class="fas fa-print"></i>
                            Imprimer la liste
                        </button>
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
                            <option>Toutes les ventes</option>
                            <option>À une date précise</option>
                        </select>
                        <div v-if="filterPeriod === 'À une date précise'">
                            <input
                                type="date"
                                v-model="selectedDate"
                                style="
                                    padding: 0.5rem;
                                    border: 1px solid #ddd;
                                    border-radius: 5px;
                                "
                            />
                            <button
                                @click="fetchDailyRentability"
                                style="
                                    padding: 0.5rem 1rem;
                                    background: #667eea;
                                    color: white;
                                    border: none;
                                    border-radius: 5px;
                                    cursor: pointer;
                                "
                            >
                                Voir
                            </button>
                        </div>
                    </div>
                </div>
                <table class="table" v-if="paginatedRentability.length > 0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Bénéfice</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(day, index) in paginatedRentability"
                            :key="index"
                        >
                            <td data-label="Date">{{ formatDate(day.day) }}</td>
                            <td data-label="Bénéfice">
                                <strong>
                                    {{ formatAmount(day.total_profit) }} FCFA
                                </strong>
                            </td>
                            <td>
                                <button
                                    class="invoice-btn"
                                    @click="showRentabilityDetails(day.day)"
                                >
                                    <i
                                        class="fas fa-eye"
                                        style="margin: 2px"
                                    ></i>
                                    Voir
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-else class="no-sales-message">
                    <strong>
                        Aucune vente disponible pour la période sélectionnée
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
                <div class="modal-overlay" v-if="showSaleModal">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div
                                class="modal-header bg-primary text-white d-flex justify-content-between align-items-center"
                            >
                                <h4 class="mb-0">
                                    Rentabilité du
                                    {{ formatDate(selectedDay) }} - Total
                                    bénéfices :
                                    {{
                                        formatAmount(
                                            details.reduce(
                                                (sum, sale) =>
                                                    sum +
                                                    sale.products.reduce(
                                                        (s, p) =>
                                                            s + (p.profit || 0),
                                                        0
                                                    ),
                                                0
                                            )
                                        )
                                    }}
                                    FCFA
                                </h4>
                                <button
                                    @click="closeSaleModal"
                                    class="btn btn-close text-white"
                                >
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="modal-body p-2">
                                <table
                                    class="table table-bordered table-striped text-center align-middle"
                                >
                                    <thead class="table-dark">
                                        <tr>
                                            <th data-label="Client">Client</th>
                                            <th data-label="Produit">
                                                Produit
                                            </th>
                                            <th data-label="Quantité">
                                                Quantité
                                            </th>
                                            <th data-label="PA">
                                                Prix d'achat (FCFA)
                                            </th>
                                            <th data-label="PV">
                                                Prix de vente (FCFA)
                                            </th>
                                            <th data-label="Total vente">
                                                Total vente (FCFA)
                                            </th>
                                            <th data-label="Bénéfice">
                                                Bénéfice (FCFA)
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template
                                            v-for="(sale, i) in details"
                                            :key="i"
                                        >
                                            <tr
                                                v-for="(p, j) in sale.products"
                                                :key="j"
                                            >
                                                <td data-label="Client">
                                                    {{ sale.client_name }}
                                                </td>
                                                <td data-label="Produit">
                                                    {{
                                                        p.name || p.product_name
                                                    }}
                                                </td>
                                                <td data-label="Quantité">
                                                    {{ p.quantity }}
                                                </td>
                                                <td data-label="PA">
                                                    {{
                                                        formatAmount(
                                                            p.purchase_price ||
                                                                0
                                                        )
                                                    }}
                                                </td>
                                                <td data-label="PV">
                                                    {{ formatAmount(p.price) }}
                                                </td>
                                                <td data-label="Total vente">
                                                    {{
                                                        formatAmount(
                                                            p.quantity * p.price
                                                        )
                                                    }}
                                                </td>
                                                <td data-label="Bénéfice">
                                                    {{ formatAmount(p.profit) }}
                                                </td>
                                            </tr>
                                        </template>
                                        <tr class="table-info fw-bold">
                                            <td
                                                colspan="5"
                                                data-label="Recapitulatif"
                                            ></td>
                                            <td data-label="Ventes">
                                                {{
                                                    formatAmount(
                                                        details.reduce(
                                                            (sum, sale) =>
                                                                sum +
                                                                sale.products.reduce(
                                                                    (s, p) =>
                                                                        s +
                                                                        p.quantity *
                                                                            p.price,
                                                                    0
                                                                ),
                                                            0
                                                        )
                                                    )
                                                }}
                                            </td>
                                            <td
                                                data-label="Bénéfice"
                                                class="fw-bold"
                                            >
                                                {{
                                                    formatAmount(
                                                        details.reduce(
                                                            (sum, sale) =>
                                                                sum +
                                                                sale.products.reduce(
                                                                    (s, p) =>
                                                                        s +
                                                                        (p.profit ||
                                                                            0),
                                                                    0
                                                                ),
                                                            0
                                                        )
                                                    )
                                                }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'RentabilityComponent',
        data() {
            return {
                rentability: [],
                details: [],
                selectedDay: null,
                selectedDate: null,
                showSaleModal: false,
                filterPeriod: 'Toutes les ventes',
                currentPage: 1,
                perPage: 10,
            };
        },
        mounted() {
            this.fetchRentabilityData();
        },
        methods: {
            fetchRentabilityData() {
                axios
                    .get('/rentabilityApi')
                    .then((res) => (this.rentability = res.data))
                    .catch((err) =>
                        console.error('Erreur récupération rentabilité:', err)
                    );
            },
            showRentabilityDetails(day) {
                this.selectedDay = day;

                if (!day) return;

                const parts = day.split('-');
                const formattedDate = `${parts[2]}/${parts[1]}/${parts[0]}`;

                console.log(
                    'Datee choisie (format dd/mm/yyyy) :',
                    formattedDate
                );

                console.log(
                    'URL appelée :',
                    '/rentabilityApi/daily?date=' + formattedDate
                );

                axios
                    .get('/rentabilityApi/daily', {
                        params: { date: formattedDate },
                    })
                    .then((res) => {
                        this.details = res.data.map((sale) => ({
                            id: sale.sale_id,
                            client_name: sale.client_name,
                            seller_name: sale.seller_name || '-',
                            total: sale.total_sale,
                            products: sale.products.map((p) => ({
                                name: p.product_name,
                                quantity: p.quantity,
                                price: parseFloat(p.price),
                                purchase_price: parseFloat(p.purchase_price),
                                profit: parseFloat(p.profit),
                            })),
                        }));

                        this.showSaleModal = true;
                        console.log(this.details);
                    })
                    .catch((err) => {
                        console.error(
                            'Erreur récupération rentabilité du jour:',
                            err
                        );
                        alert('Impossible de récupérer les détails du jour.');
                    });
            },
            fetchDailyRentability() {
                if (!this.selectedDate) {
                    alert('Veuillez choisir une date.');
                    return;
                }

                const dateForApi = this.selectedDate;

                axios
                    .get('/rentability/daily', {
                        params: { date: dateForApi },
                    })
                    .then((res) => {
                        this.details = res.data.map((sale) => ({
                            id: sale.sale_id,
                            client_name: sale.client_name,
                            seller_name: sale.seller_name || '-',
                            total: parseFloat(sale.total_sale),
                            products: sale.products.map((p) => ({
                                name: p.product_name,
                                quantity: parseFloat(p.quantity),
                                price: parseFloat(p.price),
                                purchase_price: parseFloat(p.purchase_price),
                                profit: parseFloat(p.profit),
                            })),
                        }));

                        this.selectedDay = this.selectedDate;
                        this.showSaleModal = true;
                    })
                    .catch((err) =>
                        console.error(
                            'Erreur récupération rentabilité du jour:',
                            err
                        )
                    );
            },
            closeSaleModal() {
                this.showSaleModal = false;
                this.details = [];
            },
            formatDate(dateStr) {
                const date = new Date(dateStr);
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();
                return `${day}/${month}/${year}`;
            },
            formatAmount(value) {
                return Number(value).toLocaleString('fr-FR');
            },
            printList() {
                const printWindow = window.open('', '_blank');

                const totalProfit = this.filteredRentability.reduce(
                    (sum, day) => sum + parseFloat(day.total_profit),
                    0
                );

                let tableRows = '';
                this.filteredRentability.forEach((day, index) => {
                    tableRows += `
                        <tr>
                            <td style="padding: 12px; border: 1px solid #ddd;">${
                                index + 1
                            }</td>
                            <td style="padding: 12px; border: 1px solid #ddd;">${this.formatDate(
                                day.day
                            )}</td>
                            <td style="padding: 12px; border: 1px solid #ddd; text-align: right;"><strong>${this.formatAmount(
                                day.total_profit
                            )} FCFA</strong></td>
                        </tr>
                    `;
                });

                const htmlContent = `
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Rapport de Rentabilité</title>
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
                        <h1>Rapport de Rentabilité</h1>
                        <div class="info">
                            <p><strong>Période:</strong> ${
                                this.filterPeriod
                            }</p>
                            <p><strong>Date d'impression:</strong> ${new Date().toLocaleString(
                                'fr-FR'
                            )}</p>
                            <p><strong>Nombre de jours:</strong> ${
                                this.filteredRentability.length
                            }</p>
                        </div>
                        
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th style="text-align: right;">Bénéfice</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows}
                                <tr class="total-row">
                                    <td colspan="2" style="padding: 12px; border: 1px solid #ddd; text-align: right;">BÉNÉFICE TOTAL:</td>
                                    <td style="padding: 12px; border: 1px solid #ddd; text-align: right;">${this.formatAmount(
                                        totalProfit
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
            goToPage(page) {
                if (page >= 1 && page <= this.totalPages)
                    this.currentPage = page;
            },
        },
        computed: {
            filteredRentability() {
                const now = new Date();
                return this.rentability.filter((day) => {
                    const saleDate = new Date(day.day);
                    switch (this.filterPeriod) {
                        case "Aujourd'hui":
                            return (
                                saleDate.toDateString() === now.toDateString()
                            );
                        case 'Hier':
                            const yesterday = new Date(now);
                            yesterday.setDate(now.getDate() - 1);
                            return (
                                saleDate.toDateString() ===
                                yesterday.toDateString()
                            );
                        case 'Cette semaine':
                            const firstDayOfWeek = new Date(now);
                            firstDayOfWeek.setDate(
                                now.getDate() - now.getDay() + 1
                            );
                            firstDayOfWeek.setHours(0, 0, 0, 0);
                            const lastDayOfWeek = new Date(firstDayOfWeek);
                            lastDayOfWeek.setDate(firstDayOfWeek.getDate() + 6);
                            lastDayOfWeek.setHours(23, 59, 59, 999);
                            return (
                                saleDate >= firstDayOfWeek &&
                                saleDate <= lastDayOfWeek
                            );
                        case 'Ce mois':
                            return (
                                saleDate.getMonth() === now.getMonth() &&
                                saleDate.getFullYear() === now.getFullYear()
                            );
                        case 'Toutes les ventes':
                        case 'À une date précise':
                        default:
                            return true;
                    }
                });
            },
            totalPages() {
                return Math.ceil(
                    this.filteredRentability.length / this.perPage
                );
            },
            paginatedRentability() {
                const start = (this.currentPage - 1) * this.perPage;
                return this.filteredRentability.slice(
                    start,
                    start + this.perPage
                );
            },
        },
    };
</script>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f5f6fa;
        color: #333;
    }

    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        width: 250px;
        height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .sidebar-header {
        padding: 2rem 1rem;
        text-align: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-header h2 {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .sidebar-menu {
        list-style: none;
        padding: 1rem 0;
    }

    .sidebar-menu li {
        margin: 0.5rem 0;
    }

    .sidebar-menu a {
        display: flex;
        align-items: center;
        padding: 1rem 1.5rem;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
    }

    .sidebar-menu a:hover,
    .sidebar-menu a.active {
        background: rgba(255, 255, 255, 0.1);
        border-left-color: white;
    }

    .sidebar-menu i {
        margin-right: 1rem;
        width: 20px;
    }

    .main-content {
        margin-left: 250px;
        min-height: 100vh;
        transition: all 0.3s ease;
    }

    .header {
        background: white;
        padding: 1rem 2rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header h1 {
        color: #333;
        font-size: 1.8rem;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        background: #667eea;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .sales-content {
        padding: 2rem;
    }

    .sales-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        cursor: pointer;
        font-size: 1rem;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }

    .sales-form {
        background: white;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }

    .customer-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid #eee;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #333;
    }

    .form-control {
        padding: 0.75rem;
        border: 2px solid #e1e5e9;
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 5px #764ba2aa;
    }

    .product-lines {
        margin-top: 2rem;
    }

    .product-line {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr auto;
        gap: 1rem;
        align-items: end;
        margin-bottom: 1rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 10px;
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }

    .product-line:hover {
        border-color: #667eea;
        background: #f0f4ff;
    }

    .product-line.first-line {
        background: white;
        border-color: #667eea;
    }

    .btn-remove {
        background: #dc3545;
        color: white;
        border: none;
        padding: 0.75rem;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-remove:hover {
        background: #c82333;
        transform: scale(1.05);
    }

    .btn-add-line {
        background: #28a745;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        cursor: pointer;
        font-size: 1rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1rem;
    }

    .btn-add-line:hover {
        background: #218838;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
    }

    .cart-section {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 10px;
        margin-top: 2rem;
    }

    .cart-summary {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 1rem;
        align-items: center;
        margin-bottom: 1rem;
    }

    .cart-total {
        font-size: 1.5rem;
        font-weight: bold;
        color: #667eea;
        text-align: right;
    }

    .cart-items-count {
        color: #666;
        font-size: 0.9rem;
    }

    .sales-history {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .history-header {
        padding: 1.5rem;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th,
    .table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #eee;
    }

    .table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #333;
    }

    .table tbody tr {
        transition: background 0.3s ease;
    }

    .table tbody tr:hover {
        background: #f8f9fa;
    }

    .invoice-btn {
        background: #28a745;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 5px;
        cursor: pointer;
        font-size: 0.9rem;
        margin-right: 0.5rem;
        transition: all 0.3s ease;
    }

    .invoice-btn:hover {
        background: #218838;
        transform: translateY(-1px);
    }

    .print-btn {
        background: #17a2b8;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 5px;
        cursor: pointer;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .print-btn:hover {
        background: #138496;
        transform: translateY(-1px);
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 2000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        animation: fadeIn 0.3s ease;
    }

    .modal-content {
        background: white;
        margin: 2% auto;
        padding: 0;
        border-radius: 15px;
        width: 90%;
        max-width: 800px;
        animation: slideIn 0.3s ease;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid #eee;
    }

    .close {
        font-size: 1.5rem;
        cursor: pointer;
        color: #999;
    }

    .close:hover {
        color: #333;
    }

    .invoice {
        padding: 2rem;
    }

    .invoice-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #667eea;
    }

    .company-info h2 {
        color: #667eea;
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .invoice-info {
        text-align: right;
    }

    .invoice-number {
        font-size: 1.2rem;
        font-weight: bold;
        color: #333;
    }

    .invoice-date {
        color: #666;
        margin-top: 0.5rem;
    }

    .invoice-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .invoice-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 2rem;
    }

    .invoice-table th,
    .invoice-table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #eee;
    }

    .invoice-table th {
        background: #f8f9fa;
        font-weight: 600;
    }

    .invoice-total {
        text-align: right;
        font-size: 1.3rem;
        font-weight: bold;
        color: #667eea;
        margin-top: 1rem;
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

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 0;
        }

        .sales-header {
            flex-direction: column;
            align-items: stretch;
        }

        .customer-info {
            grid-template-columns: 1fr;
        }

        .product-line {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }

        .invoice-details {
            grid-template-columns: 1fr;
        }
    }

    .menu-toggle {
        display: none;
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #333;
    }

    @media (max-width: 768px) {
        .menu-toggle {
            display: block;
        }
    }
</style>

<style>
    @media (max-width: 768px) {
        table.table,
        thead,
        tbody,
        th,
        td,
        tr {
            display: block;
            width: 100%;
        }

        thead tr {
            display: none;
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
            padding-left: 50%;
            text-align: left;
            border: none;
            border-bottom: 1px solid #eee;
            box-sizing: border-box;
            min-height: 40px;
            vertical-align: top;
        }

        tbody td:last-child {
            border-bottom: 0;
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
    }
</style>

<style>
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
    }
</style>

<style>
    .modal-content {
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        padding: 1.5rem;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #fff;
        color: #333;
    }

    .modal-header {
        border-bottom: 1px solid #ddd;
        padding-bottom: 0.75rem;
        position: relative;
    }

    .btn-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 28px;
        height: 28px;
        opacity: 0.7;
        transition: opacity 0.2s ease;
        cursor: pointer;
    }

    .btn-close:hover {
        opacity: 1;
    }

    .modal-body p {
        margin-bottom: 0.6rem;
        font-size: 1rem;
    }

    .modal-body ul {
        list-style-type: disc;
        margin-left: 1.2rem;
        margin-top: 0.4rem;
        color: #444;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        border-top: 1px solid #ddd;
        padding-top: 1rem;
    }

    .modal-footer .btn-download {
        background-color: #764ba2;
        color: white;
        padding: 0.5rem 1.25rem;
        border: none;
        border-radius: 5px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .modal-footer .btn-download:hover {
        background-color: #0056b3;
    }
</style>

<style>
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .pagination-container {
        display: flex;
        justify-content: center;
        margin-top: 2rem;
    }

    .pagination {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        background-color: #fff;
    }

    .page-item {
        display: inline;
    }

    .page-link {
        color: #495057;
        padding: 10px 15px;
        text-decoration: none;
        transition: background-color 0.3s;
        border: 1px solid #dee2e6;
    }

    .page-item:first-child .page-link {
        border-top-left-radius: 5px;
        border-bottom-left-radius: 5px;
    }

    .page-item:last-child .page-link {
        border-top-right-radius: 5px;
        border-bottom-right-radius: 5px;
    }

    .page-item.active .page-link {
        background-color: #764ba2;
        color: white;
        border-color: #764ba2;
        font-weight: bold;
    }

    .page-item.disabled .page-link {
        color: #6c757d;
        cursor: not-allowed;
        background-color: #e9ecef;
    }

    .page-item:not(.active) .page-link:hover {
        background-color: #f8f9fa;
    }
</style>

<style>
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .modal-dialog {
        max-width: 90%;
    }
    .btn-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
    }
</style>
