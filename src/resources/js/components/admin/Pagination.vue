<template>
  <ul class="pagination">
    <li v-for="page in lastPage" :key="page">
      <div
        class="page"
        :class="(currentPage === page) && 'active-page'"
        @click="changePage(page)"
      >
        {{ page }}
      </div>
    </li>
  </ul>
</template>

<script setup>
defineProps({
  currentPage: {
    type: Number,
    required: true
  },
  lastPage: {
    type: Number,
    required: true
  },
});

const emits = defineEmits(['change-page']);

// methods
const changePage = (page) => {
  emits('change-page', page);
};
</script>