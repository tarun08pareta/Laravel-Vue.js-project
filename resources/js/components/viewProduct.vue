<template>
  <div class="product-details">
    <button class="btn-back" @click="$router.push({ name: 'ProductsList' })">
      Back to Products
    </button>

    <div v-if="product" class="product-details-box">
      <button v-if="product.status === 1" class="status-button bg-success active">
        Active
      </button>

      <button v-else class="status-button bg-danger inactive">Inactive</button>

      <h2 class="product-title">{{ product.name }}</h2>
      <img :src="product.image" alt="Product Image" class="product-image" />
      <p><strong>Price:</strong> ${{ product.price }}</p>
      <p><strong>Discount:</strong> {{ product.discount }}%</p>
      <p><strong>Final Price:</strong> ${{ product.final_price }}</p>
      <p><strong>Description:</strong> {{ product.description }}</p>
      <!-- <p><strong>Condition:</strong> {{ product.condition_id }}</p> -->
    </div>
    <div v-else>
      <p>Loading product details...</p>
    </div>
  </div>
</template>
  <script>
import axios from "axios";
import { mapGetters } from "vuex";

export default {
  name: "ViewProduct",

  data() {
    return {
      product: null, // Will hold the product data
    };
  },
  created() {
    this.fetchProduct();
  },
  methods: {
//    ...mapGetters('product')

    fetchProduct() {
      const productId = this.$route.params.id; // Get the product ID from the URL
      // axios
      //   .get(`${window.location.protocol}//${window.location.host}/products/${productId}`) // Call your Laravel API endpoint
      axios
        .get(`http://localhost:8000/api/products/${productId}`)

        .then((response) => {
          // console.log('API response:', response.data);  // Log the full API response
          this.product = response.data.data;
          console.log("Product:", this.product);
        })
        .catch((error) => {
          console.error("Error fetching product:", error);
        });
    },
  },
};
</script>

<style scoped>
.product-details {
  max-width: 800px;
  margin: 40px auto;
  padding: 20px;
  border-radius: 15px;
  background-color: #f5f5f5;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
}

.product-details:hover {
  transform: scale(1.02);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
}

.product-details-box {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  opacity: 0;
  animation: fadeIn 1s forwards;
}

.product-title {
  font-size: 2rem;
  font-weight: 700;
  color: #333;
  margin-bottom: 15px;
  transition: color 0.3s ease-in-out;
}

.product-title:hover {
  color: #007bff;
}

.product-image {
  width: 250px;
  height: auto;
  margin: 20px 0;
  border: 4px solid #ddd;
  border-radius: 10px;
  transition: transform 0.3s ease-in-out, border-color 0.3s ease-in-out;
}

.product-image:hover {
  transform: scale(1.1);
  border-color: #007bff;
}

.btn-back {
  padding: 12px 24px;
  background-color: #007bff;
  color: #fff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  margin-bottom: 20px;
  transition: background-color 0.3s ease-in-out, transform 0.3s ease-in-out;
}

.btn-back:hover {
  background-color: #0056b3;
  transform: translateY(-2px);
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.status-button {
  font-size: 1.2rem; /* Larger font size */
  padding: 10px 20px; /* More padding for a bigger button */
  border: none; /* Remove border */
  border-radius: 5px; /* Rounded corners */
  color: #fff; /* White text */
  cursor: pointer; /* Pointer cursor on hover */
  transition: background-color 0.3s, transform 0.2s; /* Smooth transitions */
}

.status-button.active {
  background-color: #28a745; /* Green background for active */
}

.status-button.inactive {
  background-color: #dc3545; /* Red background for inactive */
}

/* Optional: Add hover effects */
.status-button:hover {
  transform: scale(1.05); /* Slightly enlarge on hover */
}
</style>
