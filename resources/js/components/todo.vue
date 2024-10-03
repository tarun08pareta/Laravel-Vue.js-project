<template>
    <div class="todo-list">
      <h2>To-Do List</h2>

      <!-- Input for adding new to-do -->
      <div class="input-container">
        <input v-model="newTodo" placeholder="Add a new to-do" />
        <button @click="submitTodo">Add</button>
      </div>

      <!-- Displaying the to-do list -->
      <ul class="todo-items">
        <li v-for="(todo, index) in todos" :key="index" class="todo-item">
          <span>{{ todo }}</span>
          <button @click="removeTodoItem(index)" class="remove-btn">
            &times;
          </button>
        </li>
      </ul>
    </div>
  </template>

  <script>
  import { mapActions, mapGetters } from 'vuex';

  export default {
    data() {
      return {
        newTodo: ''  // To store the new to-do input
      };
    },
    computed: {
      ...mapGetters('todo', ['allTodos']),  // Get the to-do items from Vuex
      todos() {
        return this.allTodos;  // Alias for the allTodos getter
      }
    },
    methods: {
      ...mapActions('todo', ['addTodo', 'removeTodo']),  // Map the Vuex actions

      // Method to submit a new to-do
      submitTodo() {
        if (this.newTodo.trim()) {
          this.addTodo(this.newTodo);  // Dispatch addTodo action
          this.newTodo = '';  // Clear the input
        }
      },

      // Method to remove a to-do
      removeTodoItem(index) {
        this.removeTodo(index);  // Dispatch removeTodo action
      }
    }
  };
  </script>

<style scoped>
/* Global container styling */
.todo-list {
  max-width: 400px;
  margin: 50px auto;
  padding: 20px;
  background-color: #f9f9f9;
  border-radius: 12px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  font-family: 'Arial', sans-serif;
}

/* Heading style */
.todo-list h2 {
  text-align: center;
  color: #333;
  margin-bottom: 20px;
  font-size: 24px;
  font-weight: bold;
}

/* Input container */
.input-container {
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
}

input {
  width: 75%;
  padding: 10px;
  border: 2px solid #ddd;
  border-radius: 6px;
  font-size: 16px;
  transition: border-color 0.3s ease;
}

input:focus {
  border-color: #007bff;
  outline: none;
}

button {
  padding: 10px 15px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 16px;
  transition: background-color 0.3s ease;
}

button:hover {
  background-color: #0056b3;
}

/* To-do items */
.todo-items {
  list-style-type: none;
  padding: 0;
}

.todo-item {
  background-color: #fff;
  padding: 12px 15px;
  margin-bottom: 10px;
  border-radius: 8px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: background-color 0.3s ease;
}

.todo-item:hover {
  background-color: #f1f1f1;
}

.remove-btn {
  background-color: transparent;
  border: none;
  color: #ff4d4f;
  font-size: 20px;
  cursor: pointer;
  transition: color 0.3s ease;
}

.remove-btn:hover {
  color: #d32f2f;
}

/* Responsive design */
@media (max-width: 500px) {
  .input-container {
    flex-direction: column;
  }

  input {
    width: 100%;
    margin-bottom: 10px;
  }

  button {
    width: 100%;
  }
}
</style>
