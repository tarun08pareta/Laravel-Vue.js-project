<template>
  <div class="product-container">
    <h1 class="title">Product List</h1>
    <div class="products-list-container">
      <table id="product-table" class="table table-bordered table-hover">
        <thead class="thead-light">
          <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Discount</th>
            <th>Final Price</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in allProducts" :key="product.id">
            <td>{{ product.id }}</td>
            <td>
              <img
                :src="product.image"
                alt="Product Image"
                class="img-fluid img-thumbnail"
                v-if="product.image"
                style="width: 50px; height: auto"
              />
            </td>
            <td>{{ product.name }}</td>
            <td>$ {{ product.price }}</td>
            <td>{{ product.discount }}%</td>
            <td>$ {{ product.final_price }}</td>
            <td>
              <span v-if="product.status === 1" class="badge bg-success"
                >Active</span
              >
              <span v-else class="badge bg-danger">Inactive</span>
            </td>
            <td>
              <button class="btn btn-sm btn-primary">View</button>
              <button class="btn btn-sm btn-warning">Edit</button>
              <button
                class="btn btn-sm btn-danger"
                @click="deleteProduct(product.id)"
              >
                Delete
              </button>
              <button
                class="btn btn-sm btn-secondry"
                 @click="addProductToCart(product)"
              >
                Cart
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="pagination">
      <button
        @click="fetchProducts(currentPage - 1)"
        :disabled="currentPage <= 1"
      >
        Previous
      </button>
      <span>Page {{ currentPage }} of {{ totalPages }}</span>
      <button
        @click="fetchProducts(currentPage + 1)"
        :disabled="currentPage >= totalPages"
      >
        Next
      </button>
    </div>
  </div>
</template>
  <script>
import { mapActions, mapGetters } from "vuex";

export default {
  computed: {
    ...mapGetters("product", ["allProducts", "currentPage", "totalPages"]), // Access all products using the namespaced getter
  },
  methods: {
    ...mapActions("product", ["fetchProducts", "deleteProduct"]), // Map the fetchProducts action
    ...mapActions('cart', ['addToCart']),
    addProductToCart(product) {
      this.addToCart(product);  
      alert('Product added to cart!');
    },
  },
  created() {
    this.fetchProducts(); // Fetch products when the component mounts
  },
};
</script>
<style>
.product-container {
  width: 90%;
  margin: auto;
  padding: 20px;
  border-radius: 8px;
  background-color: #f9f9f9;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.title {
  text-align: center;
  font-size: 2rem;
  margin-bottom: 20px;
}

.product-table {
  width: 100%;
  border-collapse: collapse;
  margin: 20px 0;
}

.product-table th,
.product-table td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

.product-table th {
  background-color: #007bff;
  color: white;
}

.product-table tr:hover {
  background-color: #f1f1f1;
}

.product-image {
  max-width: 80px;
  border-radius: 4px;
}

.no-products {
  text-align: center;
  font-size: 1.2rem;
  color: #888;
}
</style>
