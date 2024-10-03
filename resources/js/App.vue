<template>
  <div class="container">
    <div class="nav-links">
      <div>
        <span><router-link to="/">Products List</router-link></span>
        <span><router-link to="/add-product">Add Product</router-link></span>
        <span><router-link to="/pagnation-page">pagnation</router-link></span>
        <span><router-link to="/stateManagment-page">state</router-link></span>
        <span
          ><router-link to="/addProByState-page"
            >Add Product State</router-link
          ></span
        >
        <span><router-link to="/todo-page">To Do List</router-link></span>
        <span
          ><router-link to="/cartProduct-page">cart products</router-link></span
        >
      </div>
      <div class="classs">
          <div>
            <!-- <img src="../../public/images/icon.jpg" alt=""  > -->
            <select  v-model="currentLocale" @change="changeLanguage">
                <option value="en">English</option>
                <option value="hi">हिंदी</option>
                <option value="sr">संस्कृत</option>
            <option value="ko">한국어</option>
            <option value="es">Español</option>
            <!-- Add more languages as needed -->
          </select>
        </div>
        <div>
            <h3 class="logo"><router-link to="/">YouCan</router-link></h3>
        </div>
      </div>
    </div>
    <div class="main">
      <router-view></router-view>
    </div>
  </div>
</template>

  <script>
export default {
  data() {
    return {
      currentLocale: "en",
    };
  },
  methods: {
    changeLanguage() {
      //   if (this.$i18n) {
      //     this.$i18n.locale = this.currentLocale; // Updated to use 'global'

      //   } else{
      //     console.error("I18n instance is undefined");
      //   }
      this.$i18n.locale = this.currentLocale;
    }, // This should now work
  },
};
</script>

<style >
.classs{
    display: flex;
    gap: 5px;
}

.classs img{
    width: 50px;
}
</style>
