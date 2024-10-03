import { ref } from 'vue';

export function counter() {
  const count = ref(5);

  const increment = () => count.value++;
  const decrement = () => count.value--;

  return { count, increment, decrement };
}


