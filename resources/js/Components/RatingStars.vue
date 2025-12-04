<template>
  <div class="flex items-center space-x-1">
    <button
      v-for="star in 5"
      :key="star"
      @click="$emit('update:modelValue', star)"
      :class="[
        'text-2xl transition-all duration-200',
        star <= modelValue ? 'text-yellow-400' : 'text-gray-300',
        'hover:text-yellow-300 focus:outline-none'
      ]"
      :title="`Rate ${star} stars`"
    >
      ⭐
    </button>
    <span v-if="modelValue > 0" class="text-sm text-gray-600 ml-2">
      {{ modelValue }}/5
    </span>
  </div>
</template>

<script setup>
defineProps({
  modelValue: {
    type: Number,
    default: 0,
  },
});

defineEmits(['update:modelValue']);
</script>
