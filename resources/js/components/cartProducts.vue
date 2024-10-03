<template>
    <div class="cart">
      <h2>Your Cart</h2>
      <table class="cart-table">
        <thead>
          <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Discount</th>
            <th>Final Price</th>
            <th>Quantity</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <!-- Loop through the cart items -->
          <tr v-for="item in cartItems" :key="item.id">
            <!-- Product Image -->
            <td>
              <img
                :src="item.image"
                alt="Product Image"
                v-if="item.image"
                class="img-fluid img-thumbnail"
                style="width: 50px; height: auto"
              />
            </td>
            <!-- Product Details -->
            <td>{{ item.name }}</td>
            <td>$ {{ item.price }}</td>
            <td>{{ item.discount }}%</td>
            <td>$ {{ item.final_price }}</td>
            <td>{{ item.quantity }}</td>
            <!-- Remove from Cart Button -->
            <td>
              <button @click="removeFromCart(item.id)" class="btn btn-danger">
                Remove
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>

  <script>
  import { mapGetters, mapActions } from 'vuex';

  export default {
    computed: {
      ...mapGetters('cart', ['allCartItems']),  // Get cart items
      cartItems() {
        return this.allCartItems;
      },
    },
    methods: {
      ...mapActions('cart', ['removeFromCart']),  // Remove from cart action
    },
  };
  </script>

<style scoped>
.cart {
  padding: 20px;
  background-color: #f8f9fa;
  border-radius: 5px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.cart h2 {
  text-align: center;
  margin-bottom: 20px;
}

.cart-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

.cart-table th,
.cart-table td {
  padding: 10px;
  text-align: center;
  border: 1px solid #dee2e6;
}

.cart-table th {
  background-color: #007bff;
  color: white;
}

.cart-table tr:nth-child(even) {
  background-color: #f2f2f2;
}

.img-fluid {
  border-radius: 5px;
}

.btn-danger {
  background-color: #dc3545;
  color: white;
  border: none;
  padding: 5px 10px;
  border-radius: 5px;
  cursor: pointer;
}

.btn-danger:hover {
  background-color: #c82333;
}
</style>
