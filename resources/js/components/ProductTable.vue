<template>
  <div>
    <table id="product-table" class="display table table-bordered table-hover">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Price</th>
          <th>Discount</th>
          <th>Final Price</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="product in products" :key="product.id">
          <td>{{ product.id }}</td>
          <td>{{ product.name }}</td>
          <td>{{ product.price }}</td>
          <td>{{ product.discount }}%</td>
          <td>{{ product.final_price }}</td>
          <td>
            <button class="btn btn-primary" @click="editProduct(product.id)">
              Edit
            </button>
            <button class="btn btn-danger" @click="deleteProduct(product.id)">
              Delete
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

  <script>
import axios from 'axios';
import { useToastr } from "../toastr";
    import $ from 'jquery';
      import 'datatables.net-dt/css/dataTables.dataTables.min.css';
      import 'datatables.net';
      import 'datatables.net-buttons';
      import 'datatables.net-buttons/js/buttons.html5.js';
      import 'datatables.net-buttons-dt/css/buttons.dataTables.min.css';
      import 'datatables.net-buttons/js/buttons.print.js';
      import 'datatables.net-buttons/js/buttons.colVis.js';
  // import 'bootstrap/dist/css/bootstrap.min.css';
//   import 'jszip';

export default {
  data() {
    return {
      products: [],
    };
  },
  mounted() {
    this.fetchProducts();
    // this.loadDataTable();
  },
  methods: {
    fetchProducts() {
      return axios
        .get(
          `${window.location.protocol}//${window.location.host}/api/products`
        )
        .then((response) => {
          this.products = response.data.products;
        //   console.log(response.data.products);
        this.$nextTick(() => {
            this.loadDataTable();
          });
        })
        .catch((error) => {
          console.error(error);
        });
    },
    loadDataTable() {
      // Dynamically load jQuery
      const jqueryScript = document.createElement("script");
      jqueryScript.src = "https://code.jquery.com/jquery-3.6.0.min.js";
      jqueryScript.onload = () => {
        // Dynamically load DataTables JavaScript
        const datatableScript = document.createElement("script");
        datatableScript.src =
          "https://cdn.datatables.net/2.1.6/js/dataTables.min.js";
        datatableScript.onload = () => {
          // Initialize DataTable after the script has loaded
          $("#product-table").DataTable();

          // Add click event listener for rows
          $("#product-table tbody").on("click", "tr", function () {
            const data = $("#product-table").DataTable().row(this).data();
            alert("You clicked on row with ID: " + data[0]);
          });
        };
        document.body.appendChild(datatableScript);
      };
      document.body.appendChild(jqueryScript);
    },
    editProduct(id) {
      console.log("Edit product with ID:", id);
      // Add edit logic
    },
    deleteProduct(id) {
      console.log("Delete product with ID:", id);
      // Add delete logic
    },
  },
};
</script>

  <style>
@import url("https://cdn.datatables.net/2.1.6/css/dataTables.dataTables.min.css");

/* Custom DataTable styling (optional) */
#product-table {
  width: 100%;
  border-collapse: collapse;
}

#product-table thead th {
  background-color: #f8f9fa;
  text-align: left;
}

#product-table tbody tr {
  cursor: pointer;
}

#product-table tbody tr:hover {
  background-color: #f1f1f1;
}
</style>
