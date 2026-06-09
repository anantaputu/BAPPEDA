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

// Registrasi modul Chart.js
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
        default: () => ({ labels: [], values: [] })
    },
    xAxisLabel: {
        type: String,
        default: 'Periode Bulanan'
    },
    yAxisLabel: {
        type: String,
        default: 'Jumlah Dataset'
    }
});

// --- KONFIGURASI TAMPILAN CHART (TEBAL & KONSISTEN) ---
const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: '#1F3A63', // primary
            padding: 12,
            titleFont: { size: 13, weight: 'bold' },
            bodyFont: { size: 12, weight: 'bold' },
            displayColors: false,
            cornerRadius: 8,
            callbacks: {
                label: (context) => ` ${context.raw} Dataset Baru`
            }
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            title: {
                display: true,
                text: props.yAxisLabel,
                color: '#1F3A63',
                font: { size: 12, weight: 'bold' }
            },
            grid: { 
                display: false,
                color: '#EEF2F5', // bgsoft
                lineWidth: 2, // Ditebalkan
                drawBorder: false 
            },
            ticks: { 
                font: { size: 10, weight: 'bold' }, 
                color: '#4B5563', // textsecondary
                stepSize: 1 
            }
        },
        x: {
            title: {
                display: true,
                text: props.xAxisLabel,
                color: '#1F3A63',
                font: { size: 12, weight: 'bold' }
            },
            grid: { 
                display: false,
                color: '#EEF2F5', // bgsoft
                lineWidth: 2, // Ditebalkan
                drawBorder: false
            },
            ticks: { 
                font: { size: 10, weight: 'bold' }, 
                color: '#4B5563' // textsecondary
            }
        }
    },
    elements: {
        line: { tension: 0.4 } // Smooth curve
    }
}));

const formattedData = computed(() => ({
    labels: props.chartData.labels,
    datasets: [{
        label: 'Pertumbuhan Data',
        data: props.chartData.values,
        borderColor: '#0284C7', // secondary
        backgroundColor: (context) => {
            const ctx = context.chart.ctx;
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(2, 132, 199, 0.2)'); // secondary transparan
            gradient.addColorStop(1, 'rgba(31, 58, 99, 0)');     // primary menghilang
            return gradient;
        },
        borderWidth: 3,
        pointBackgroundColor: '#ffffff',
        pointBorderColor: '#0284C7', // secondary
        pointBorderWidth: 2,
        pointRadius: 4,
        pointHoverRadius: 6,
        fill: true
    }]
}));
</script>

<template>
    <section class="bg-white p-8 rounded-xl shadow-sm border border-gray-400 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
            <div>
                <h3 class="font-black text-primary text-sm flex items-center gap-2 uppercase tracking-[0.2em] border-l-4 border-secondary pl-4">
                    Tren Pertumbuhan Data
                </h3>
                <p class="text-[10px] text-textsecondary font-bold uppercase tracking-wider mt-2 ml-5">
                    Monitoring input dataset bulanan tahun {{ new Date().getFullYear() }}
                </p>
            </div>
            
            <div class="bg-bgsoft px-4 py-2 rounded-xl text-primary font-black text-[10px] uppercase tracking-widest flex items-center gap-2 self-start sm:self-center border border-gray-200">
                <span class="w-2 h-2 rounded-full bg-secondary animate-pulse shadow-sm shadow-secondary/50"></span>
                Real-time Update
            </div>
        </div>

        <div class="h-80 w-full relative min-w-0">
            <Line :data="formattedData" :options="chartOptions" />
        </div>
    </section>
</template>
