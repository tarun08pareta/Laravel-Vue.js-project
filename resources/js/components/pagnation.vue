<!-- <template>
  <div class="overflow-auto">
    <h1>tarunn</h1>

  </div>
</template>
<script>
import axios from "axios";

export default {
  data() {
    return {
      file: null,
      loading: false,
    };
  },
  methods: {
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
          // toastr.success('Product(s) added successfully!');
          this.file = null;
          this.$refs.file.value = null;
        })
        .catch((error) => {
          console.error("AxiosError:", error);
          const errorMessage = error.response
            ? error.response.data.message || "An error occurred"
            : "No response from server";
          // toastr.warning(errorMessage);
        })
        .finally(() => {
          this.loading = false; // Turn off loading spinner after request is done
        });
    },
  },
};
</script> -->


<!-- <template>
    <div>
      <div class="d-flex w-100 justify-content-between">

        <select v-model="param.pageLimit" class="page-limit-select">
          <option v-for="limit in limitArray" :key="limit" :value="limit">
            {{ limit }}
          </option>
        </select>
      </div>

      <table id="example" class="display" style="width: 100%">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Image</th>
            <th>Discount</th>

          </tr>
        </thead>
      </table>


      <div class="overflow-auto">
        <b-pagination
          v-model="param.currentPage"
          :total-rows="totalRows"
          :per-page="perPage"
          aria-controls="example"
        />
        <p class="mt-3">Current Page: {{ param.currentPage }}</p>
      </div>
    </div>
  </template>

  <script>
  import axios from "axios";
  import $ from "jquery";
  import "datatables.net-dt/css/dataTables.dataTables.min.css";
  import "datatables.net";
  import "bootstrap/dist/css/bootstrap.min.css";
  import { Bootstrap5Pagination } from "laravel-vue-pagination";

  export default {
    components: {
      Bootstrap5Pagination,
    },
    data() {
      return {
        limitArray: [1,2, 10, 15, 20, 25, 30],
        categories: [],
        conditions: [],
        // table: null,
        param: {
          currentPage: 1,
          pageLimit: 2,
        },
        products: [],
        totalRows:null,
        laravelData: {
          current_page: 1,
          data: [],
          total: 0,

        },
      };
    },
    computed: {
      rows() {
        // console.log('tarun',this.laravelData.total)
        return this.laravelData.length;

      },
      perPage() {
        // console.log('pareta',this.param.pageLimit)
        return this.param.pageLimit;
      },
    },
    methods: {
      fetchProducts() {
        axios
          .get(`${window.location.protocol}//${window.location.host}/api/products`, {
            params: {
              limit: this.param.pageLimit,
              page: this.param.currentPage,
            },
          })
          .then((response) => {
              console.log('data',response.data)
            this.laravelData = response.data;
            this.totalRows = response.data.pagination.total


            this.updateTableData(this.laravelData);
          })
          .catch((error) => {
            console.error("Error fetching products:", error);
          });
      },
      updateTableData(data) {
        //   console.log('new data',data)
          if (this.table) {

          this.table.clear();
          this.table.rows.add(data.products);
        //   console.log('new... data',data.products)
          this.table.draw();

        }
      },
      initializeDataTable() {
        this.table = $("#example").DataTable({
          paging: false,
          searching: false,
          columns: [
            { title: "ID", data: "id" },
            { title: "Name", data: "name" },
            { title: "Description", data: "description" },
            { title: "Price", data: "price" },
            {
              title: "Image",
              data: "image",
              render: (data, type, row) => {
                return data
                  ? `<img src="${data}" alt="${row.name}" style="width: 100px; height: auto;">`
                  : "No Image";
              },
            },
            { title: "Discount", data: "discount" },
            // { title: "Condition", data: "condition_id" },
            // {
            //   title: "Categories",
            //   data: (row) => row.categories.map((cat) => cat.name).join(", "),
            // },
          ],
        });
      },
    },
    watch: {
      "param.currentPage": function () {
        this.fetchProducts(); // Fetch products when currentPage changes
      },
      "param.pageLimit": function () {
        this.param.currentPage = 1;
        this.fetchProducts(); // Fetch products when pageLimit changes
      },
    },
    mounted() {
      this.initializeDataTable();
      this.fetchProducts();
    },
  };
  </script>

  <style scoped>
  .page-limit-select {
    margin-bottom: 20px;
  }
  </style> -->


  <!-- <template>
  <div id="app" class="jarvis-container">
    <div class="chat">
      <div class="conversation">

        <div
          v-for="(message, index) in conversation"
          :key="index"
          class="message"
        >
          <span
            :class="{
              'user-message': message.sender === 'user',
              'jarvis-message': message.sender === 'jarvis',
            }"
            >{{ message.text }}</span
          >
        </div>
      </div>


      <div class="input-section">
        <input
          v-model="command"
          @keyup.enter="handleCommand"
          placeholder="Ask me anything..."
        />
        <button @click="handleCommand">Send</button>
        <button @click="startListening">🎤 Speak</button>
      </div>
    </div>
  </div>
</template>

  <script>
export default {
  data() {
    return {
      command: "", // User's command input
      conversation: [], // List of conversation messages
    };
  },
  methods: {
    // Handle user command
    handleCommand() {
      const userMessage = this.command.trim();

      if (!userMessage) return;

      // Append user message to conversation
      this.conversation.push({ text: userMessage, sender: "user" });

      // Process command with a slight delay to mimic a real-time response
      setTimeout(() => {
        this.processCommand(userMessage.toLowerCase());
      }, 500);

      // Clear the input field
      this.command = "";
    },

    // Process command logic and provide conversational response
    processCommand(command) {
      let jarvisResponse = "";

      // Use conversational responses
      if (command.includes("open google")) {
        window.open("https://www.google.com", "_blank");
        jarvisResponse = "Got it! Opening Google for you.";
      } else if (command.includes("open youtube")) {
          const youtubeLink = "https://youtu.be/zmwfd8x0DrM?si=RW8Tq2eIQJTysYT1"; // Replace with actual video link
        //   const youtubeLink = "https://youtu.be/FAfPQd_ennY?si=HbP9T6Py7ZT5J4lx"; // Replace with actual video link
          window.open(youtubeLink, "_blank");
        jarvisResponse = "Sure! Let me get that YouTube video up for you.";
      } else if (command.includes("open calculator")) {
        window.open("https://www.online-calculator.com/", "_blank"); // Open online calculator
        jarvisResponse = "No problem, opening Calculator now!";
      } else if (command.includes("battery")) {
        this.getBatteryStatus();
        return;
      } else if (command.includes("how are you")) {
        jarvisResponse =
          "I'm just a bunch of code, but thanks for asking! How can I help you today?";
      } else if (command.includes("thank you")) {
        jarvisResponse = "You're welcome! Always here to assist.";
      } else if (command.includes("babu")) {
        const youtubeLink = "https://youtu.be/FAfPQd_ennY?si=HbP9T6Py7ZT5J4lx"; // Replace with actual video link
          window.open(youtubeLink, "_blank");
      } else {
        jarvisResponse =
          "Hmm, I'm not sure I understand that. Could you try asking in a different way?";
      }

      this.respondAsJarvis(jarvisResponse);
    },

    // Get battery status of the system
    getBatteryStatus() {
      navigator.getBattery().then((battery) => {
        const batteryLevel = Math.round(battery.level * 100) + "%";
        const response = `Your battery is at ${batteryLevel}. Need me to remind you to charge it later?`;
        this.respondAsJarvis(response);
      });
    },

    // Respond as JARVIS
    respondAsJarvis(response) {
      this.conversation.push({ text: response, sender: "jarvis" });
      this.speak(response); // Voice feedback
    },

    // Start voice recognition for commands
    startListening() {
      const recognition = new webkitSpeechRecognition(); // Web Speech API
      recognition.lang = "en-US";
      recognition.continuous = false;

      recognition.onresult = (event) => {
        const voiceCommand = event.results[0][0].transcript;
        this.command = voiceCommand;
        this.handleCommand();
      };

      recognition.start(); // Start listening to the user's voice
    },

    // Text-to-Speech with a more natural tone
    speak(text) {
      const speech = new SpeechSynthesisUtterance(text);
      speech.lang = "en-US";
      speech.pitch = 1.2; // Slightly higher pitch for a friendly tone
      speech.rate = 1; // Normal speed
      window.speechSynthesis.speak(speech);
    },
  },
};
</script>

  <style scoped>
/* Styles for the JARVIS assistant conversation layout */
.jarvis-container {
  max-width: 600px;
  margin: 50px auto;
  padding: 20px;
  background-color: #1f1f1f;
  border-radius: 10px;
  color: white;
  font-family: "Arial", sans-serif;
}

.chat {
  display: flex;
  flex-direction: column;
  height: 500px;
}

.conversation {
  flex: 1;
  overflow-y: auto;
  margin-bottom: 20px;
  padding: 10px;
  border: 1px solid #444;
  border-radius: 5px;
}

.message {
  padding: 10px;
  margin: 5px 0;
}

.user-message {
  text-align: right;
  background-color: #4a90e2;
  color: white;
  padding: 10px;
  border-radius: 10px;
  display: inline-block;
}

.jarvis-message {
  text-align: left;
  background-color: #444;
  padding: 10px;
  border-radius: 10px;
  display: inline-block;
}

.input-section {
  display: flex;
}

input {
  flex: 1;
  padding: 10px;
  font-size: 16px;
}

button {
  padding: 10px;
  margin-left: 10px;
  font-size: 16px;
  background-color: #4a90e2;
  color: white;
  border: none;
  cursor: pointer;
}

button:hover {
  background-color: #357ab8;
}
</style> -->


<template>
    <div>
      <p>Count: {{ count }}</p>
      <button @click="increment">Increment</button>
      <button @click="decrement">Decrement</button>
    </div>
  </template>

  <script setup>
  import { counter } from '../Counter';

  // Use the composable
  const { count, increment, decrement } = counter();
  </script>
