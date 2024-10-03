
<template>
  <div class="products-list">
    <h2 class="component-title">{{ $t("title") }}</h2>
    <div class="div">
      <div class="bton">
        <div class="bton-button">
          <button class="btn btn-secondary" @click="copyData">
            {{ $t("copyData") }}
          </button>
          <button class="btn btn-secondary" @click="exportSelectedProducts">
            {{ $t("downloadCsv") }}
          </button>
        </div>
        <div class="import-class">
          <form @submit.prevent="importFile">
            <input type="file" ref="file" @change="handleFileUpload" />
            <button type="submit" :disabled="!file || loading">
              <span v-if="loading">Uploading...</span>
              <span v-else>{{ $t("importData") }}</span>
            </button>
          </form>
        </div>
      </div>

      <div class="seer-entry">
        <div class="item-per-page">
          <label for="entries-per-page" class="select-label">{{
            $t("itemsPerPage")
          }}</label>
          <select
            id="entries-per-page"
            v-model="perPage"
            @change="filterProducts"
            class="select-filter"
          >
            <option v-for="limit in limitArray" :key="limit" :value="limit">
              {{ limit }}
            </option>
          </select>
        </div>

        <div class="item-per-page">
          <button @click="toggleFilterForm">{{ $t("filters") }}</button>
        </div>
      </div>
    </div>

    <div v-if="showFilterForm" class="filter-sidebar">
      <form @submit.prevent="applyFilters">
        <div class="form-grid">
          <label for="search" class="form-label">{{
            $t("searchByProductName")
          }}</label>
          <input
            type="text"
            id="search"
            v-model="selectedName"
            placeholder="Search by product name"
            class="form-input"
          />
        </div>
        <div class="form-grid">
          <label for="category" class="form-label">{{
            $t("filterByCategory")
          }}</label>
          <select id="category" v-model="selectedCategory" class="form-select">
            <option value="">All</option>
            <option
              v-for="category in categories"
              :key="category.id"
              :value="category.id"
            >
              {{ category.name }}
            </option>
          </select>
        </div>
        <div class="form-grid">
          <label for="condition" class="form-label">{{
            $t("filterByCondition")
          }}</label>
          <select
            id="condition"
            v-model="selectedCondition"
            class="form-select"
          >
            <option value="">All</option>
            <option
              v-for="condition in conditions"
              :key="condition.id"
              :value="condition.id"
            >
              {{ condition.name }}
            </option>
          </select>
        </div>
        <div class="form-grid">
          <label for="order" class="form-label">{{ $t("sortByPrice") }}</label>
          <select id="order" v-model="selectedOrder" class="form-select">
            <option value="">Order</option>
            <option value="ASC">ASC</option>
            <option value="DESC">DESC</option>
          </select>
        </div>
        <div class="form-actions">
          <button type="submit" class="btn btn-primary">
            {{ $t("applyFilter") }}
          </button>
          <button type="reset" class="btn btn-secondary">
            {{ $t("resetFilter") }}
          </button>
        </div>
      </form>
    </div>

    <div class="products-list-container">
      <table id="product-table" class="table table-bordered table-hover">
        <thead class="thead-light">
          <tr>
            <th>
              <input
                type="checkbox"
                @change="selectAll"
                v-model="allSelected"
                class="custom-checkbox form-check-input"
              />
              {{ $t("all") }}
            </th>
            <th>{{ $t("id") }}</th>
            <th>{{ $t("image") }}</th>
            <th>{{ $t("name") }}</th>
            <th>{{ $t("price") }}</th>
            <th>{{ $t("discount") }}</th>
            <th>{{ $t("finalPrice") }}</th>
            <th>{{ $t("status") }}</th>
            <th>{{ $t("action") }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in products" :key="product.id">
            <td>
              <input
                type="checkbox"
                v-model="selectedProducts"
                :value="product.id"
                class="form-check-input custom-checkbox"
              />
            </td>
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
              <span v-if="product.status === 1" class="badge bg-success">{{
                $t("active")
              }}</span>
              <span v-else class="badge bg-danger">{{ $t("inactive") }}</span>
            </td>
            <td>
              <button
                class="btn btn-sm btn-primary"
                @click="viewProduct(product.id)"
              >
                {{ $t("view") }}
              </button>
              <button
                class="btn btn-sm btn-warning"
                @click="goToUpdate(product.id)"
              >
                {{ $t("edit") }}
              </button>
              <button
                class="btn btn-sm btn-danger"
                @click="deleteProduct(product.id)"
              >
                {{ $t("delete") }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <nav aria-label="Page navigation" v-if="totalPages > 1">
      <ul class="pagination justify-content-center">
        <li class="page-item" :class="{ disabled: currentPage === 1 }">
          <a
            class="page-link"
            href="#"
            @click.prevent="filterProducts(currentPage - 1)"
            >Previous</a
          >
        </li>
        <li
          v-for="page in paginationPages"
          :key="page"
          class="page-item"
          :class="{ active: page === currentPage }"
        >
          <a
            class="page-link"
            href="#"
            v-if="page !== '...'"
            @click.prevent="filterProducts(page)"
            >{{ page }}</a
          >
          <span v-else class="page-link">...</span>
        </li>
        <li class="page-item" :class="{ disabled: currentPage === totalPages }">
          <a
            class="page-link"
            href="#"
            @click.prevent="filterProducts(currentPage + 1)"
            >Next</a
          >
        </li>
      </ul>
    </nav>
  </div>
</template>

<script>
import { useToastr } from "../toastr";
import axios from "axios";
// import { parse } from 'json2csv';
import Papa from "papaparse";
import jsPDF from "jspdf";
import { Bootstrap5Pagination } from "laravel-vue-pagination";

const toastr = useToastr();

export default {
  name: "ProductsList",
  components: {
    Bootstrap5Pagination,
  },
  data() {
    return {
      title: "Products List",
      products: [],
      categories: [],
      conditions: [],
      limitArray: [1, 3, 4, 10, 15, 20],

      // filteredProducts: [],
      selectedCategory: "",
      selectedOrder: "",
      selectedCondition: "",
      selectedName: "",
      currentPage: 1,
      perPage: 4,
      totalItems: 0,
      maxPagesToShow: 2,
      totalPages: 0,

      pagination: {},
      showFilterForm: false,
      file: null,
      loading: false,
      selectedProducts: [], // Holds IDs of selected products
      allSelected: false,
      //   param: {
      //     currentPage: 1,
      //     pageLimit: 10,
      //   },
      //   totalRows: null,
      //   laravelData: {
      //     current_page: 1,
      //     data: [],
      //     total: 0,
      //   },
    };
  },

  methods: {
    toggleFilterForm() {
      // Toggles the filter form visibility when the dropdown is clicked
      this.showFilterForm = !this.showFilterForm;
      this.resetData();
    },
    resetData() {
      this.selectedName = "";
      this.selectedCategory = "";

      this.selectedOrder = "";
      this.selectedCondition = "";
    },
    selectAll() {
      if (this.allSelected) {
        this.selectedProducts = this.products.map((product) => product.id);
      } else {
        this.selectedProducts = [];
      }
    },
    handleFileUpload(event) {
      this.file = event.target.files[0];
    },
    importFile() {
      this.loading = true;
      const formData = new FormData();
      formData.append("file", this.file);

      axios
        .post(
          `${window.location.protocol}//${window.location.host}/api/products/import`,
          formData,
          {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          }
        )
        .then((response) => {
          console.log("Product added by CSV");
          toastr.success("Product(s) added successfully!");
          this.file = null;
          this.$refs.file.value = null;
        })
        .catch((error) => {
          console.error("AxiosError:", error);
          const errorMessage = error.response
            ? error.response.data.message || "An error occurred"
            : "No response from server";
          toastr.warning(errorMessage);
        })
        .finally(() => {
          this.loading = false; // Turn off loading spinner after request is done
        });
    },

    exportSelectedProducts() {
      if (this.selectedProducts.length === 0) {
        alert("Please select at least one product to export.");
        return;
      }
      const url = `${window.location.protocol}//${
        window.location.host
      }/api/products/export?ids=${this.selectedProducts.join(",")}`;
      window.open(url);
    },

    getProducts: function () {
      axios
        .get(
          `${window.location.protocol}//${window.location.host}/api/products`
        )
        .then((response) => {
          this.products = response.data.products;
          //   console.log('new',response.data.products)
          // this.$nextTick(() => {
          //   this.loadDataTable();
          // });
          //   console.log(response);
        })
        .catch((error) => {
          console.log(error);
        });
    },
    // loadDataTable() {
    //   // Dynamically load jQuery
    //   const jqueryScript = document.createElement("script");
    //   jqueryScript.src = "https://code.jquery.com/jquery-3.6.0.min.js";
    //   jqueryScript.onload = () => {
    //     // Dynamically load DataTables JavaScript
    //     const datatableScript = document.createElement("script");
    //     datatableScript.src =
    //       "https://cdn.datatables.net/2.1.6/js/dataTables.min.js";
    //     datatableScript.onload = () => {
    //       //   Initialize DataTable after the script has loaded
    //       $("#product-table").DataTable({
    //         data: this.products,
    //         // paging: false,
    //         // searching: false,
    //         // pageLength: 4,
    //         // lengthChange: true,
    //         // responsive: true,
    //       });

    //       // Add click event listener for rows
    //       $("#product-table tbody").on("click", "tr", function () {
    //         const data = $("#product-table").DataTable().row(this).data();
    //         alert("You clicked on row with ID: " + data[0]);
    //       });
    //     };
    //     document.body.appendChild(datatableScript);
    //   };
    //   document.body.appendChild(jqueryScript);
    // },

    getCategories: function () {
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

    // filterByCategory: function () {
    //   let url;
    //   if (this.selectedCategory === "") {
    //     // If "All" is selected (empty value), fetch all products
    //     url = `${window.location.protocol}//${window.location.host}/api/products`;
    //   } else {
    //     // If a specific category is selected, fetch products for that category
    //     url = `${window.location.protocol}//${window.location.host}/api/products/categories/${this.selectedCategory}`;
    //   }
    //   axios
    //     // .get(
    //     //   `${window.location.protocol}//${window.location.host}/api/products/categories/${this.selectedCategory}`
    //     // )
    //     .get(url)
    //     .then((response) => {
    //       this.products = response.data.products;
    //     })
    //     .catch((error) => {
    //       console.log(error);
    //     });
    // },

    // sortByOrder: function () {
    //   if (this.selectedOrder === "ASC") {
    //     this.products = this.products.sort((a, b) => a.price - b.price);
    //   }
    //   if (this.selectedOrder === "DESC") {
    //     this.products = this.products.sort((a, b) => b.price - a.price);
    //   }
    // },
    goToUpdate(productId) {
      this.$router.push({ name: "UpdateProduct", params: { id: productId } });
      console.log(productId);
    },
    viewProduct(productId) {
      this.$router.push({ name: "viewProduct", params: { id: productId } });
      console.log("View product with ID:", productId);
    },
    deleteProduct(productId) {
      if (confirm("Are you sure you want to delete this product?")) {
        axios
          .delete(
            `${window.location.protocol}//${window.location.host}/api/products/${productId}`
          )
          .then((response) => {
            if (response.status === 200) {
              //   this.products = this.products.filter(
              //     (product) => product.id !== productId
              //   );
              this.filterProducts();
              //   alert("Product deleted successfully");
              toastr.success("Product Deleted successfully !!");
            }
          })
          .catch((error) => {
            console.log(error);
            alert("An error occurred while deleting the product.");
          });
      }
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
    // filterByCondition() {
    //   if (this.selectedCondition === "") {
    //     this.getProducts(); // Fetch all products if no condition is selected
    //   } else {
    //     axios
    //       .get(
    //         `${window.location.protocol}//${window.location.host}/api/products/conditions/${this.selectedCondition}`
    //       )
    //       .then((response) => {
    //         this.products = response.data.products;
    //       })
    //       .catch((error) => {
    //         console.log(error);
    //       });
    //   }
    // },
    filterProducts(page = 1) {
      let queryParams = {
        page: page,
        perPage: this.perPage,
      };

      // Adding query parameters based on filters
      if (this.selectedCategory)
        queryParams.category_id = this.selectedCategory;

      if (this.selectedCondition)
        queryParams.condition_id = this.selectedCondition;
      if (this.selectedOrder) queryParams.price_order = this.selectedOrder;
      if (this.selectedName) queryParams.name = this.selectedName;

      axios
        .get(
          `${window.location.protocol}//${window.location.host}/api/products`,
          { params: queryParams }
        )
        .then((response) => {
          if (response.data && response.data.products) {
            this.products = response.data.products;
            this.totalItems = response.data.pagination.total || 0;
            this.totalPages = response.data.pagination.last_page || 0;
            this.currentPage = response.data.pagination.current_page || 1;
            this.pagination = response.data.pagination;

            this.showFilterForm = false;
          } else {
            this.products = [];
            this.totalItems = 0;
            alert("No products found.");
          }
        })
        .catch((error) => {
          console.error("Error fetching products:", error);
          this.products = [];
          this.totalItems = 0;
          alert("An error occurred while fetching products.");
        });
    },
    applyFilters() {
      this.filterProducts(1); // Always pass page 1 when applying filters
    },
    changePage(page) {
      if (typeof page === "number") {
        this.filterProducts(page); // Ensure correct page is passed
      }
    },

    // Download PDF using jsPDF
    downloadPDF() {
      const doc = new jsPDF();
      let row = 10;
      doc.text("Products List", 10, row);
      row += 10;

      // Add table headers
      doc.text("ID", 10, row);
      doc.text("Name", 30, row);
      doc.text("Price", 70, row);
      doc.text("Discount", 100, row);
      doc.text("Final Price", 140, row);
      row += 10;

      // Add product data
      this.products.forEach((product) => {
        doc.text(String(product.id), 10, row);
        doc.text(product.name, 30, row);
        doc.text(String(`$` + product.price), 70, row);
        doc.text(String(product.discount) + "%", 100, row);
        doc.text(String(`$` + product.final_price), 140, row);
        row += 10;
      });

      // Save the PDF
      doc.save("products_list.pdf");
    },

    copyData() {
      const csvData = this.products
        .map((product) =>
          [
            product.id,
            product.name,
            product.price,
            product.discount,
            product.final_price,
          ].join(",")
        )
        .join("\n");

      // Create a temporary textarea element to copy data
      const textarea = document.createElement("textarea");
      textarea.value = `ID,Name,Price,Discount,Final Price\n${csvData}`;
      document.body.appendChild(textarea);
      textarea.select();
      document.execCommand("copy");
      document.body.removeChild(textarea);

      // Notify user
      alert("Data copied to clipboard!");
    },
  },
  computed: {
    paginationPages() {
      let pages = [];

      // Always show the first page
      if (this.currentPage > 2) {
        pages.push(1);
        if (this.currentPage > 3) {
          pages.push("...");
        }
      }

      // Show the current page and its adjacent pages (1 before and 1 after)
      for (let i = this.currentPage - 1; i <= this.currentPage + 1; i++) {
        if (i > 0 && i <= this.totalPages) {
          pages.push(i);
        }
      }

      // Always show the last page
      if (this.currentPage < this.totalPages - 1) {
        if (this.currentPage < this.totalPages - 2) {
          pages.push("...");
        }
        pages.push(this.totalPages);
      }

      return pages;
    },
    // rows() {
    //     // console.log('tarun',this.laravelData.total)
    //     return this.laravelData.length;

    //   },
    //   perPage() {
    //     // console.log('pareta',this.param.pageLimit)
    //     return this.param.pageLimit;
    //   },
  },

  created() {
    this.getProducts();
    this.getCategories();
    this.getConditions();
    this.filterProducts();
  },
};
</script>

<style>
/* @import url("https://cdn.datatables.net/2.1.6/css/dataTables.dataTables.min.css"); */
@import url("https://cdn.datatables.net/2.1.6/css/dataTables.dataTables.min.css");
.active {
  background-color: #f44336 !important;
}

.outlined-price {
  text-decoration: line-through; /* Draws a line over the text */
  text-decoration-thickness: 1.6 px; /* Sets the thickness of the line */
  text-decoration-color: black; /* Sets the color of the line */
  margin-right: 5px;
}

.products-list-container {
  margin-top: 20px;
}

.table {
  width: 100%;
  margin-bottom: 1rem;
  background-color: #fff;
}

.thead-light th {
  background-color: #f8f9fa;
  color: #495057;
}

img.img-thumbnail {
  width: 50px;
  height: auto;
}

.btn-sm {
  margin-right: 5px;
}

.form-check-input[type="checkbox"] {
  border-radius: 0.25em;
  border: 1px solid black;
}

@import url("https://cdn.datatables.net/2.1.6/css/dataTables.dataTables.min.css");

/* Main container styling */
/* Container for buttons and selection elements */
.div {
  /* Maximum width for the container */
  margin: 0 auto;
  padding: 20px;
  background-color: #f8f9fa; /* Light background for contrast */
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/*import and other button  */
.bton {
  display: flex;
  justify-content: space-between;
  align-items: center;
  /* margin-top: 20px; */
  padding: 5px;
  background-color: #f9f9f9;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.bton-button {
  display: flex;
  gap: 10px;
}

.bton-button button {
  padding: 10px 20px;
  border-radius: 5px;
  border: none;
  cursor: pointer;
  font-size: 14px;
  transition: background-color 0.3s ease;
}

.bton-button .btn-secondary {
  background-color: #6c757d;
  color: white;
}

.bton-button .btn-secondary:hover {
  background-color: #5a6268;
}

.import-class {
  display: flex;
  align-items: center;
}

.import-class form {
  display: flex;
  gap: 10px;
}

.import-class input[type="file"] {
  font-size: 14px;
  padding: 5px;
}

.import-class button {
  padding: 10px 15px;
  background-color: #007bff;
  color: white;
  border-radius: 5px;
  border: none;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.import-class button:hover {
  background-color: #0056b3;
}

.import-class button:disabled {
  background-color: #b5b5b5;
  cursor: not-allowed;
}

.import-class span {
  font-weight: bold;
}

/* import and other button  */
.btn {
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  color: #fff;
  cursor: pointer;
  font-size: 16px;
  transition: background-color 0.3s, box-shadow 0.3s;
  display: inline-block;
}

/* Secondary button styling */
.btn-secondary {
  background-color: #6c757d;
}

.btn-secondary:hover {
  background-color: #5a6268;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Selector and label styling */
.seer-entry {
  display: flex;
  justify-content: space-between;
  gap: 20px; /* Space between elements */
}

/* Items per page styling */

/* Label styling */
.select-label {
  font-weight: 500;
  color: #333;
  font-size: 16px;
  margin-right: 10px; /* Space between label and select */
}

/* Select styling */
.select-filter {
  width: 100px; /* Fixed width for selects */
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 16px;
  background-color: #fff;
  transition: border-color 0.3s, box-shadow 0.3s;
}

.select-filter:focus {
  border-color: #007bff;
  box-shadow: 0 0 5px rgba(0, 123, 255, 0.2);
  outline: none;
}

/* Toggle button styling */
button {
  padding: 10px 15px;
  border: none;
  border-radius: 6px;
  background-color: #007bff;
  color: #fff;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s, box-shadow 0.3s;
}

button:hover {
  background-color: #0056b3;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .div {
    padding: 15px;
  }

  .seer-entry {
    flex-direction: column;
    gap: 15px;
  }

  .item-per-page {
    flex-direction: column;
    align-items: flex-start;
  }
}

/* apply css on filter */

/* Filter sidebar styling */
.filter-sidebar {
  background-color: #f9f9f9;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  width: 100%;

  /* margin: 0 auto; */
  margin-top: 10px;
  display: grid;
  gap: 20px;
}

/* Form grid layout */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 2fr; /* 1/3 for label, 2/3 for select/input */
  gap: 15px;
  align-items: center;
}

form {
  gap: 6px;
}

/* Form group styling */
.form-group {
  margin-bottom: 15px;
}

/* Input and Select styling */
.form-input,
.form-select {
  width: 100%;
  min-width: 200px; /* Minimum width */
  padding: 12px;
  border: 1px solid #ddd;
  border-radius: 6px;
  box-sizing: border-box;
  font-size: 16px;
  transition: border-color 0.3s, box-shadow 0.3s;
}

.form-input:focus,
.form-select:focus {
  border-color: #007bff;
  box-shadow: 0 0 5px rgba(0, 123, 255, 0.2);
  outline: none;
}

/* Label styling */
.form-label {
  font-weight: 500;
  color: #333;
  font-size: 16px;
}

/* Button styling */
.btn {
  padding: 12px 24px;
  border: none;
  border-radius: 6px;
  color: #fff;
  cursor: pointer;
  font-size: 16px;
  transition: background-color 0.3s, box-shadow 0.3s;
  display: inline-block;
  margin: 0 5px;
}

.btn-primary {
  background-color: #007bff;
}

.btn-primary:hover {
  background-color: #0056b3;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.btn-secondary {
  background-color: #6c757d;
}

.btn-secondary:hover {
  background-color: #5a6268;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .filter-sidebar {
    max-width: 100%;
    padding: 15px;
  }

  .form-grid {
    grid-template-columns: 1fr; /* Stack label and input/select vertically on small screens */
  }
}

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

/* apply css on filter  end */
</style>
