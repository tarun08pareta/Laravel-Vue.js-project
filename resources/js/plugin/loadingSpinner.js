export default {
    install(Vue) {
      Vue.mixin({
        data() {
          return {
            isLoading: false, // global loading state for all components
          };
        },
        methods: {
          showSpinner() {
            this.isLoading = true; // Show spinner
          },
          hideSpinner() {
            this.isLoading = false; // Hide spinner
          },
        },
      });

      // Global directive for the spinner
      Vue.directive('loading', {
        bind(el, binding) {
          const spinner = document.createElement('div');
          spinner.classList.add('spinner');
          spinner.innerHTML = `<div class="loader"></div>`;
          el.appendChild(spinner);

          if (!binding.value) {
            spinner.style.display = 'none';
          }

          el._spinner = spinner;
        },
        update(el, binding) {
          if (binding.value) {
            el._spinner.style.display = 'block'; // Show spinner if loading is true
          } else {
            el._spinner.style.display = 'none';  // Hide spinner if loading is false
          }
        },
      });
    },
  };
