<!-- resources/js/pages/dashboard/components/LineChart.vue -->
<template>
  <div class="w-full h-full">
    <canvas ref="chartRef"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, onUnmounted } from 'vue';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const props = defineProps({
  data: {
    type: Object,
    required: true
  },
  options: {
    type: Object,
    default: () => ({})
  }
});

const chartRef = ref(null);
let chartInstance = null;

const initializeChart = () => {
  if (chartInstance) {
    chartInstance.destroy();
  }

  if (chartRef.value) {
    chartInstance = new Chart(chartRef.value.getContext('2d'), {
      type: 'line',
      data: props.data,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        ...props.options
      }
    });
  }
};

onMounted(() => {
  initializeChart();
});

watch(
  () => props.data,
  () => {
    initializeChart();
  },
  { deep: true }
);

onUnmounted(() => {
  if (chartInstance) {
    chartInstance.destroy();
  }
});
</script>
