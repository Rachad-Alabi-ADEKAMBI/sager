import { createApp } from 'vue';
import SalesComponent from './components/admin/SalesComponent.vue';
import ProductsComponent from './components/admin/ProductsComponent.vue';
import StocksComponent from './components/admin/StocksComponent.vue';
import SellersComponent from './components/admin/SellersComponent.vue';
import RentabilityComponent from './components/admin/RentabilityComponent.vue';
import ProformasComponent from './components/admin/ProformasComponent.vue';

import SaleComponent from './components/seller/SaleComponent.vue';
import ProformaComponent from './components/seller/ProformaComponent.vue';

const app = createApp({});
app.component('sales-component', SalesComponent);
app.component('products-component', ProductsComponent);
app.component('stocks-component', StocksComponent);
app.component('sellers-component', SellersComponent);
app.component('rentability-component', RentabilityComponent);
app.component('proformas-component', ProformasComponent);

app.component('sale-component', SaleComponent);
app.component('proforma-component', ProformaComponent);


app.mount('#app');
