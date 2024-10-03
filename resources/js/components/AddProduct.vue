<template>
  <div class="add-product">
    <h2 class="component-title">{{ title }}</h2>
    <form @submit.prevent="addProduct">
      <div class="form-first-group">
        <div>
          <div class="first-input">
            <label for="name">Product Name</label>
            <input type="text" v-model="product.name" id="name" />
          </div>
          <div>
            <label for="price">Product Price</label>
            <input type="number" v-model="product.price" id="price" />
          </div>
          <div>
            <label for="discount">Product Discount (%)</label>
            <input
              type="number"
              v-model="product.discount"
              id="discount"
              placeholder="Enter discount in %"
            />
          </div>
          <div>
            <label for="finalPrice">Final Price</label>
            <input type="number" :value="finalPrice" id="finalPrice" disabled />
          </div>
        </div>
        <div>
          <div>
            <label for="status">Product Status</label>
            <select v-model="product.status" id="status">
              <option value="0">Inactive</option>
              <option value="1">Active</option>
            </select>
          </div>
          <label for="description">Product Description</label>
          <textarea v-model="product.description" id="description"></textarea>
        </div>
      </div>
      <div class="form-second-group">
        <div>
          <label for="image">Product Image</label>
          <input
            type="file"
            id="image"
            accept="image/*"
            v-on:change="onFileSelected"
          />
          <div v-if="imagePreview">
            <img
              :src="imagePreview"
              alt="Image Preview"
              class="image-preview"
            />
          </div>
        </div>
        <div>
          <label for="category">Product Category</label>
          <select v-model="product.category_id" id="category">
            <option value="">Select Category</option>
            <option
              v-for="category in categories"
              :key="category.id"
              :value="category.id"
            >
              {{ category.name }}
            </option>
          </select>
        </div>
        <div>
          <label for="condition">Product Condition</label>
          <select v-model="product.condition_id" id="condition">
            <option value="">Select Condition</option>
            <option
              v-for="condition in conditions"
              :key="condition.id"
              :value="condition.id"
            >
              {{ condition.name }}
            </option>
          </select>
        </div>
      </div>
      <button type="submit" class="submit--btn">Add Product</button>
    </form>
  </div>
</template>

<script>
import { useToastr } from "../toastr";
import axios from "axios";

const toastr = useToastr();

export default {
  name: "AddProduct",

  data() {
    return {
      title: "Add Product",
      categories: [],
      conditions: [],
      product: {
        name: "",
        description: "",
        price: 0,
        discount: 0, // Add discount field
        category_id: null,
        condition_id: null,
        image: null,
        status: 1,
      },
      imagePreview: null,
    };
  },

  computed: {
    // Computed property to calculate the final price
    finalPrice() {
      let discountAmount = (this.product.price * this.product.discount) / 100;
      return this.product.price - discountAmount;
    },
  },

  methods: {
    getCategories() {
      axios
        .get(
          `${window.location.protocol}//${window.location.host}/api/categories`
        )
        .then((response) => {
          this.categories = response.data.categories;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getConditions() {
      axios
        .get(
          `${window.location.protocol}//${window.location.host}/api/conditions`
        )
        .then((response) => {
          if (response.data.status === 200) {
            this.conditions = response.data.conditions;
          } else {
            console.error("Error fetching conditions:", response.data.error);
          }
        })
        .catch((error) => {
          console.error("Error during API request:", error);
        });
    },

    onFileSelected(event) {
      this.product.image = event.target.files[0];
      this.imagePreview = URL.createObjectURL(this.product.image);
    },

    addProduct() {
      // Create a FormData object if you are including file uploads
      let formData = new FormData();
      formData.append("name", this.product.name);
      formData.append("description", this.product.description);
      formData.append("price", this.product.price);
      formData.append("discount", this.product.discount);
      formData.append("category_id", this.product.category_id);
      formData.append("condition_id", this.product.condition_id);
      formData.append("status", this.product.status);
      if (this.product.image) {
        formData.append("image", this.product.image);
      }

      axios
        .post(
          `${window.location.protocol}//${window.location.host}/api/products`,
          formData,
          {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          }
        )
        .then((response) => {
          toastr.success("Product created successfully !!");
          this.$router.push({ path: "/" });
        })
        .catch((error) => {
          // Log the full error object
          console.error("AxiosError:", error);

          // Extract and display error message
          const errorMessage = error.response
            ? error.response.data.message || "An error occurred"
            : "No response from server";
          toastr.warning(errorMessage);
        });
    },
  },

  created() {
    this.getCategories();
    this.getConditions();
  },
};
</script>

<style>
.image-preview{
    width: 100px;
}
/* Custom Toastr Styles */

.toastr {
    font-family: Arial, sans-serif;
  }

  .toast-success {
    background-color: #28a745; /* Green for success */
    color: white; /* White text */
  }

  .toast-error {
    background-color: #dc3545; /* Red for error */
    color: white; /* White text */
  }

  .toast-warning {
    background-color: #ffc107; /* Yellow for warning */
    color: black; /* Black text */
  }

  .toast-info {
    background-color: #17a2b8; /* Blue for info */
    color: white; /* White text */
  }

</style>
