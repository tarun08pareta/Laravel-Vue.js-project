
const state={
    todos: []
}

const mutations={
    ADD_TODO(state, todo) {
        state.todos.push(todo);  // Add new to-do item to the list
      },
      REMOVE_TODO(state, index) {
        state.todos.splice(index, 1);  // Remove the to-do item by index
      },
}

const actions = {
    addTodo({ commit }, todo) {
       
      commit('ADD_TODO', todo);  // Dispatch the mutation to add a to-do
    },
    removeTodo({ commit }, index) {
      commit('REMOVE_TODO', index);  // Dispatch the mutation to remove a to-do
    },
  };
  const getters = {
    allTodos: (state) => state.todos,  // Return all the to-do items
  };

  export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters,

  };
