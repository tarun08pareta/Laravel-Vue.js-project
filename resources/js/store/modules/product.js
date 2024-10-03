// product.js
import axios from "axios";

const state = {
    products: [],
    categories: [], // For storing categories
    conditions: [],
    product: null,
    currentPage: 1, // To track the current page
    totalPages: 0,
};

const mutations = {
    SET_PRODUCTS(state, products) {
        state.products = products;
    },
    SET_PRODUCT(state, product) {
        state.product = product;
    },
    SET_PAGINATION(state, pagination) {
        state.currentPage = pagination.current_page;
        state.totalPages = pagination.last_page;
    },
    SET_CATEGORIES(state, categories) {
        state.categories = categories;
    },
    SET_CONDITIONS(state, conditions) {
        state.conditions = conditions;
    },
    ADD_PRODUCT(state, newProduct) {
        state.products.push(newProduct); // Add the new product to the state
    },
    REMOVE_PRODUCT(state, productId) {
        // Filter out the product with the given ID from the products list
        state.products = state.products.filter(product => product.id !== productId);
      },
};

const actions = {
    async fetchProducts({ commit }, page = 1) {
        try {
            const response = await axios.get(
                `${window.location.protocol}//${window.location.host}/api/products?page=${page}`
            );
            // console.log(response.data.products);
            commit("SET_PRODUCTS", response.data.products);
            commit("SET_PAGINATION", response.data.pagination);
        } catch (error) {
            console.error("Error fetching products:", error);
        }
    },

    async fetchProduct({ commit }, productId) {
        try {
            const response = await axios.get(
                `${window.location.protocol}//${window.location.host}/api/products/${productId}`
            );
            commit("SET_PRODUCT", response.data);
              console.log(response.data)
        } catch (error) {
            console.error("Error fetching product:", error);
        }
    },
    async fetchCategories({ commit }) {
        try {
            const response = await axios.get(
                `${window.location.protocol}//${window.location.host}/api/categories`
            );
            // console.log("cccat", response);
            commit("SET_CATEGORIES", response.data.categories);
        } catch (error) {
            console.error("Error fetching categories:", error);
        }
    },

    async fetchConditions({ commit }) {
        try {
            const response = await axios.get(
                `${window.location.protocol}//${window.location.host}/api/conditions`
            );
            if (response.data.status === 200) {
                commit("SET_CONDITIONS", response.data.conditions);
            } else {
                console.error(
                    "Error fetching conditions:",
                    response.data.error
                );
            }
        } catch (error) {
            console.error("Error fetching conditions:", error);
        }
    },
    async addProduct({ commit }, productData) {
        try {
            const response = await axios.post(
                `${window.location.protocol}//${window.location.host}/api/products`,
                productData,
                { headers: { 'Content-Type': 'multipart/form-data' } }
            );
            commit("ADD_PRODUCT", response.data); // Commit the new product to the state
        } catch (error) {
            console.error("Error adding product:", error);
        }
    },
    async deleteProduct({ commit ,dispatch  }, productId) {
        try {
            confirm("Are you sure you want to delete this product?")
          await axios.delete(`${window.location.protocol}//${window.location.host}/api/products/${productId}`);


          commit('REMOVE_PRODUCT', productId);
        
        dispatch('fetchProducts', state.products);

        } catch (error) {
          console.error("Error deleting product:", error);
        }
      },
};

const getters = {
    allProducts: (state) => state.products,
    singleProduct: (state) => state.product,
    currentPage: (state) => state.currentPage,
    totalPages: (state) => state.totalPages,

    allCategories: (state) => state.categories, // Getter for categories
    allConditions: (state) => state.conditions,
};

export default {
    namespaced: true, // Ensure the module is namespaced
    state,
    mutations,
    actions,
    getters,
};
