<script setup>
import { Line } from 'vue-chartjs';
import { 
    Chart as ChartJS, 
    CategoryScale, 
    LinearScale, 
    PointElement, 
    LineElement, 
    Title, 
    Tooltip, 
    Legend, 
    Filler 
} from 'chart.js';
import { computed } from 'vue';

// Registrasi modul Chart.js yang dibutuhkan untuk Line Chart
ChartJS.register(
    CategoryScale, 
    LinearScale, 
    PointElement, 
    LineElement, 
    Title, 
    Tooltip, 
    Legend, 
    Filler
);

const props = defineProps({
    chartData: {
        type: Object,
        default: () => ({ labels: [], datasets: [] })
    }
});

// Konfigurasi Tampilan Chart
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false }, // Sembunyikan legenda agar bersih
        tooltip: {
            backgroundColor: '#1e293b',
            padding: 12,
            titleFont: { size: 13 },
            bodyFont: { size: 12 },
            displayColors: false,
            callbacks: {
                label: (context) => ` ${context.raw} Dataset Baru`
            }
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            grid: { color: '#f1f5f9', borderDash: [5, 5] },
            ticks: { font: { size: 10 }, stepSize: 1 }
        },
        x: {
            grid: { display: false },
            ticks: { font: { size: 10 } }
        }
    },
    elements: {
        line: { tension: 0.4 } // Membuat garis melengkung (smooth)
    }
};

// Format Data agar sesuai ChartJS
const formattedData = computed(() => ({
    labels: props.chartData.labels,
    datasets: [{
        label: 'Pertumbuhan Data',
        data: props.chartData.values,
        borderColor: '#4A6CF7', // Warna Garis Biru Utama
        backgroundColor: (context) => {
            const ctx = context.chart.ctx;
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(74, 108, 247, 0.2)'); // Biru transparan atas
            gradient.addColorStop(1, 'rgba(74, 108, 247, 0)');   // Putih bawah
            return gradient;
        },
        borderWidth: 3,
        pointBackgroundColor: '#ffffff',
        pointBorderColor: '#4A6CF7',
        pointBorderWidth: 2,
        pointRadius: 4,
        pointHoverRadius: 6,
        fill: true // Aktifkan area fill di bawah garis
    }]
}));
</script>

<template>
    <div class="h-full w-full">
        <Line :data="formattedData" :options="chartOptions" />
    </div>
</template>