<template>
  <div class="update-product">
    <h2 class="component-title">Update Product</h2>
    <form @submit.prevent="updateProduct">
      <div class="form-first-group">
        <div>
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
        <div>
          <label for="status">Product Status</label>
          <select v-model="product.status" id="status">
            <option value="0">Inactive</option>
            <option value="1">Active</option>
          </select>
        </div>
      </div>

      <!-- Description Section -->
      <div class="form-description-group">
        <div>
          <label for="description">Product Description</label>
          <textarea v-model="product.description" id="description"></textarea>
        </div>
      </div>

      <button type="submit" class="submit--btn">Update Product</button>
    </form>
  </div>
</template>

  <script>
import { useToastr } from "../toastr";
import axios from "axios";
const toastr = useToastr();

export default {
  name: "UpdateProduct",
  data() {
    return {
      product: {
        id: "",
        name: "",
        description: "",
        price: 0,
        discount: 0,
        category_id: null,
        condition_id: null,
        image: null,
        status: "",
      },
      categories: [],
      conditions: [],
      imagePreview: null,
    };
  },
  computed: {
    finalPrice() {
      let discountAmount = (this.product.price * this.product.discount) / 100;
      return this.product.price - discountAmount;
    },
  },
  methods: {
    // Fetch the product details to prefill the form
    fetchProduct() {
      const productId = this.$route.params.id;
      console.log("Product ID:", productId);
      axios
        .get(
          `${window.location.protocol}//${window.location.host}/api/products/${productId}`
        )
        .then((response) => {
          console.log("API Response:", response.data.data);
          //   if (response.data && response.data.data) {
          this.product = response.data.data;
          this.imagePreview = this.product.image || null;
          console.log("data", this.product); // Log product after setting it

          //   }
        })
        .catch((error) => {
          console.error("Error fetching product:", error);
        });
    },
    getCategories() {
      axios
        .get(
          `${window.location.protocol}//${window.location.host}/api/categories`
        )
        .then((response) => {
          this.categories = response.data.categories;
          //   console.log('category',this.categories)
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
          this.conditions = response.data.conditions;
        })
        .catch((error) => {
          console.error("Error fetching conditions:", error);
        });
    },
    onFileSelected(event) {
      this.product.image = event.target.files[0];
      // Display image preview
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          this.imagePreview = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    },

    // Update the product
    updateProduct() {
      const productId = this.$route.params.id; // Get the product ID from the route
      let formData = new FormData();
      formData.append("name", this.product.name);
      formData.append("description", this.product.description);
      formData.append("price", this.product.price);
      formData.append("discount", this.product.discount);
      formData.append("status", this.product.status);
      formData.append("_method", "PUT");
      formData.append("category_id", this.product.category_id);
      formData.append("condition_id", this.product.condition_id);
      if (this.product.image) {
        formData.append("image", this.product.image);
      }

      axios
        .post(
          `${window.location.protocol}//${window.location.host}/api/products/${productId}`,
          formData,
          {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          }
        )
        .then((response) => {
          toastr.success("Product updated successfully !!");
          console.log(response);
          this.$router.push({ path: "/" });
        })
        .catch((error) => {
          console.error("AxiosError:", error);
          const errorMessage = error.response
            ? error.response.data.message || "An error occurred"
            : "No response from server";
          toastr.warning(errorMessage);
        });
    },
  },
  created() {
    this.fetchProduct();
    this.getCategories();
    this.getConditions();
  },
  // created() {
  //     const productId = this.$route.params.id;
  //     console.log(productId); // Fetch product ID from route params
  //     this.getProductDetails(productId);
  //     this.getCategories();
  //     this.fetchConditions();
  //     this.fetchProductCategory(productId);
  // }
};
</script>

<style>
.update-product {
  max-width: 900px;
  margin: 20px auto;
  padding: 40px;
  background-color: #f9f9f9;
  border-radius: 12px;
  box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
}

.component-title {
  text-align: center;
  font-size: 28px;
  font-weight: bold;
  margin-bottom: 30px;
  color: #2c3e50;
  letter-spacing: 1px;
}

form {
  display: flex;
  flex-direction: column;
}

.form-first-group,
.form-second-group {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 25px;
}

.form-first-group > div,
.form-second-group > div {
  flex: 1;
  margin-bottom: 20px;
}

.form-description-group {
  margin-top: 30px;
}

label {
  display: block;
  font-weight: 700;
  margin-bottom: 10px;
  color: #333;
}

input[type="text"],
input[type="number"],
textarea,
select {
  width: 100%;
  padding: 14px;
  border-radius: 10px;
  border: 1px solid #ccc;
  font-size: 16px;
  background-color: #fff;
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
  transition: border-color 0.3s, box-shadow 0.3s;
}

textarea {
  min-height: 150px;
  resize: vertical;
  padding: 12px;
}

input[type="text"]:focus,
input[type="number"]:focus,
textarea:focus,
select:focus {
  border-color: #3498db;
  box-shadow: 0 0 8px rgba(52, 152, 219, 0.4);
}

input[type="file"] {
  padding: 12px;
  background-color: #f1f1f1;
  border: 1px solid #ddd;
  border-radius: 10px;
  cursor: pointer;
}

input[type="file"]:hover {
  border-color: #2980b9;
}

input[type="file"]:focus {
  border-color: #3498db;
  box-shadow: 0 0 8px rgba(52, 152, 219, 0.4);
}

.image-preview {
  max-width: 100%;
  max-height: 250px;
  object-fit: cover;
  border: 3px solid #ecf0f1;
  margin-top: 20px;
  border-radius: 10px;
}

.submit--btn {
  background-color: #b83375;
  color: #fff;
  border: none;
  padding: 16px 22px;
  font-size: 18px;
  font-weight: bold;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  margin-top: 30px;
  text-align: center;
}

.submit--btn:hover {
  background-color: #962b5f;
}

.submit--btn:focus {
  outline: none;
  background-color: #2c3e50;
}

@media (max-width: 768px) {
  .form-first-group,
  .form-second-group {
    flex-direction: column;
  }

  input[type="text"],
  input[type="number"],
  textarea,
  select,
  .submit--btn {
    font-size: 14px;
    padding: 12px;
  }

  .submit--btn {
    margin-top: 25px;
  }
}
</style>
