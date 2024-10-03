<template>
    <div class="form-container">
      <div class="form-header">
        <h2>Add New Product</h2>
      </div>
      <form class="product-form" @submit.prevent="submitProduct">
        <!-- Product Name -->
        <div class="form-group">
          <label for="name">Product Name</label>
          <input type="text" v-model="product.name" id="name" required />
        </div>

        <!-- Description -->
        <div class="form-group">
          <label for="description">Description</label>
          <textarea v-model="product.description" id="description" required></textarea>
        </div>

        <!-- Category and Condition (side by side) -->
        <div class="form-group inline-group">
          <div class="form-group-half">
            <label for="category">Category</label>
            <select v-model="product.category_id" id="category" required>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>

          <div class="form-group-half">
            <label for="condition">Condition</label>
            <select v-model="product.condition_id" id="condition" required>
              <option v-for="condition in conditions" :key="condition.id" :value="condition.id">
                {{ condition.name }}
              </option>
            </select>
          </div>
        </div>

        <!-- Price and Discount (side by side) -->
        <div class="form-group inline-group">
          <div class="form-group-half">
            <label for="price">Price</label>
            <input type="number" v-model="product.price" id="price" required />
          </div>

          <div class="form-group-half">
            <label for="discount">Discount (%)</label>
            <input type="number" v-model="product.discount" id="discount" required />
          </div>
        </div>

        <!-- Final Price -->
        <div class="form-group">
          <label for="finalPrice">Final Price</label>
          <input type="number" :value="finalPrice" id="finalPrice" disabled />
        </div>

        <!-- Status -->
        <div class="form-group">
          <label for="status">Product Status</label>
          <select v-model="product.status" id="status">
            <option value="0">Inactive</option>
            <option value="1">Active</option>
          </select>
        </div>

        <!-- Image Upload -->
        <div class="form-group">
          <label for="image">Product Image</label>
          <input type="file" id="image" accept="image/*" @change="onFileSelected" />
          <div v-if="imagePreview" class="image-preview-wrapper">
            <img :src="imagePreview" alt="Image Preview" class="image-preview" />
          </div>
        </div>

        <button type="submit" class="submit-btn">Add Product</button>
      </form>
    </div>
  </template>


  <script>
import { mapActions, mapGetters } from "vuex";

export default {
  data() {
    return {
      product: {
        name: "",
        description: "",
        category_id: "",
        condition_id: "",
        price: "",
        discount: "",
        final_price: "",
        status: "",
        image: "",
      },
      imagePreview: null,
    };
  },
  computed: {
    ...mapGetters("product", ["allCategories", "allConditions"]),
    categories() {
      return this.allCategories; // Get categories from Vuex
    },
    conditions() {
      return this.allConditions; // Get conditions from Vuex
    },
    finalPrice() {
      let discountAmount = (this.product.price * this.product.discount) / 100;
      return this.product.price - discountAmount;
    },
  },
  methods: {
    ...mapActions("product", ["addProduct"]),

    submitProduct() {
      const formData = new FormData();

      // Append each product field to formData
      formData.append("name", this.product.name);
      formData.append("description", this.product.description);
      formData.append("category_id", this.product.category_id);
      formData.append("condition_id", this.product.condition_id);
      formData.append("price", this.product.price);
      formData.append("discount", this.product.discount);
      formData.append("final_price", this.product.final_price);
      formData.append("status", this.product.status);

      // Append the file if it exists
      if (this.product.image) {
        formData.append("image", this.product.image);
      }

      // Dispatch the action to add a new product
      this.addProduct(formData)
        .then(() => {
          this.$router.push({ path: "/stateManagment-page" });
        })
        .catch((error) => {
          console.error("Error adding product:", error);
        });
    },

    onFileSelected(event) {
      this.product.image = event.target.files[0];
      this.imagePreview = URL.createObjectURL(this.product.image);
    },
  },
  mounted() {
    this.$store.dispatch("product/fetchCategories");
    this.$store.dispatch("product/fetchConditions");
  },
};
</script>

<style scoped>
/* Form Container */
.form-container {
  max-width: 600px;
  margin: 40px auto;
  padding: 30px;
  background-color: #fff;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Form Header */
.form-header {
  text-align: center;
  margin-bottom: 20px;
}

h2 {
  font-size: 24px;
  color: #333;
  margin: 0;
  font-weight: 600;
}

/* Form Group */
.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  font-size: 14px;
  color: #333;
  margin-bottom: 8px;
  font-weight: 500;
}

.form-group input,
.form-group textarea,
.form-group select {
  width: 100%;
  padding: 12px;
  font-size: 14px;
  border: 1px solid #ddd;
  border-radius: 6px;
  background-color: #f9f9f9;
  transition: border-color 0.3s ease;
}

.form-group textarea {
  resize: vertical;
  min-height: 100px;
}

/* Inline Group for Side by Side Layout */
.inline-group {
  display: flex;
  justify-content: space-between;
}

.form-group-half {
  width: 48%;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
  border-color: #007bff;
  outline: none;
}

/* Image Upload Section */
.image-preview-wrapper {
  margin-top: 10px;
}

.image-preview {
  display: block;
  width: 100%;
  max-width: 300px;
  border-radius: 10px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Submit Button */
.submit-btn {
  width: 100%;
  padding: 12px;
  background-color: #007bff;
  color: #fff;
  font-size: 16px;
  font-weight: bold;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.submit-btn:hover {
  background-color: #0056b3;
}

/* Responsive Design */
@media (max-width: 768px) {
  .inline-group {
    flex-direction: column;
  }

  .form-group-half {
    width: 100%;
  }

  h2 {
    font-size: 20px;
  }

  .submit-btn {
    font-size: 14px;
  }
}
</style>

