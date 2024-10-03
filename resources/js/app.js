import './bootstrap';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router.js'
import $ from 'jquery';
import 'datatables.net-bs4';
import 'bootstrap/dist/css/bootstrap.min.css';
// import 'bootstrap';
import { BootstrapVue3 } from 'bootstrap-vue-3';
import store from './store'
import i18n from './i18n';



const app = createApp(App);
app.use(router);
app.use(i18n);
app.mount('#app')
app.use(BootstrapVue3);
app.use(store);



// $(document).ready(function() {
//     $('#product-table').DataTable();
// });
// Vue.component('product-table', require('./components/ProductTable.vue').default);
