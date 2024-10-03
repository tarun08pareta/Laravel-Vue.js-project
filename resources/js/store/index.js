import { createStore } from 'vuex';
import createPersistedState from 'vuex-persistedstate';
import product from './modules/product';  // Import the product module
import todo from './modules/todo';
import cart from './modules/cart'
const store = createStore({
  modules: {
    product,
    todo,
    cart
  },
  plugins: [createPersistedState({
    path:['todo.todos','cart']
  })]
});

export default store;
