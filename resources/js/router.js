import {createWebHistory, createRouter} from 'vue-router';
import ProductsList from './components/ProductsList.vue';
import AddProduct from './components/AddProduct.vue';
import UpdateProduct from './components/updateProduct.vue';
import ProductTable from './components/ProductTable.vue';
import ViewProduct from './components/viewProduct.vue';
import Pagnation from './components/pagnation.vue';
import StateManagment from './components/stateManagment.vue';
import AddProductByState from './components/addProductByState.vue';
import Todo from './components/todo.vue';
import CartProducts from './components/cartProducts.vue';

export const routes = [
    {
        name: 'ProductsList',
        path: '/',
        component: ProductsList
    },
    {
        name: 'AddProduct',
        path: '/add-product',
        component: AddProduct
    },
    {
        path: '/update-product/:id',
        name: 'UpdateProduct',
        component: () => import('./components/updateProduct.vue'),
      },
      {
        path:'/product-table',
        name:'productTable',
        component:ProductTable
      },{
        path:'/product-view/:id',
        name:'viewProduct',
        component:ViewProduct
      },
      {
        path:'/pagnation-page',
        name:'pagnation-page',
        component:Pagnation
      },{
        path:'/stateManagment-page',
        name:'stateManagment-page',
        component:StateManagment
      },{
        path:'/addProByState-page',
        name:'addProByState-page',
        component:AddProductByState
      },{
        path:'/todo-page',
        name:'todo-page',
        component:Todo
      },{
        path:'/cartProduct-page',
        name:'cartProduct-page',
        component:CartProducts
      }

];

const router = createRouter({
    history: createWebHistory(),
    routes: routes
});

export default router;
