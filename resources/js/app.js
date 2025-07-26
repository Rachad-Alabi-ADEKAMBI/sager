import { createApp } from 'vue'
import SalesComponent from './components/admin/SalesComponent.vue'
import ProductsComponent from './components/admin/ProductsComponent.vue'
import StocksComponent from './components/admin/StocksComponent.vue'

import SaleComponent from './components/seller/SaleComponent.vue'



const app = createApp({})
app.component('sales-component', SalesComponent)
app.component('products-component', ProductsComponent)
app.component('stocks-component', StocksComponent)

app.component('sale-component', SaleComponent)

app.mount('#app')