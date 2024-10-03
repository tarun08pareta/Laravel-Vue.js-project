

const state = {
    cartItems: [],
  };

  const getters = {
    allCartItems: (state) => state.cartItems,
  };

  const actions = {
    addToCart({ commit }, product) {
      commit('ADD_TO_CART', product);
    },
    removeFromCart({ commit }, productId) {
      commit('REMOVE_FROM_CART', productId);
    },
  };

  const mutations = {
    ADD_TO_CART(state, product) {
      const itemExists = state.cartItems.find((item) => item.id === product.id);


      if (itemExists) {
        itemExists.quantity += 1;
      } else {
        state.cartItems.push({ ...product, quantity: 1 });
      }
    },
    REMOVE_FROM_CART(state, productId) {
      state.cartItems = state.cartItems.filter((item) => item.id !== productId);
    },
  };

  export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
  };
