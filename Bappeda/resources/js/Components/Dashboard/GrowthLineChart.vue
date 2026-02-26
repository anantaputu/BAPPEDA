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
    }
});

// --- KONFIGURASI TAMPILAN CHART (THEME NAVY & ROYAL) ---
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: '#000B58', // Tooltip menggunakan Navy
            padding: 12,
            titleFont: { size: 13, weight: 'bold' },
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
            ticks: { 
                font: { size: 10, weight: 'bold' }, 
                color: '#64748b',
                stepSize: 1 
            }
        },
        x: {
            grid: { display: false },
            ticks: { font: { size: 10, weight: 'bold' }, color: '#64748b' }
        }
    },
    elements: {
        line: { tension: 0.4 } // Smooth curve
    }
};

// --- FORMAT DATA DENGAN GRADASI BIRU ---
const formattedData = computed(() => ({
    labels: props.chartData.labels,
    datasets: [{
        label: 'Pertumbuhan Data',
        data: props.chartData.values,
        borderColor: '#00139E', // Garis menggunakan Biru Royal
        backgroundColor: (context) => {
            const ctx = context.chart.ctx;
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(0, 19, 158, 0.15)'); // Royal transparan atas
            gradient.addColorStop(1, 'rgba(0, 11, 88, 0)');     // Navy menghilang ke bawah
            return gradient;
        },
        borderWidth: 3,
        pointBackgroundColor: '#ffffff',
        pointBorderColor: '#00139E', // Border point Biru Royal
        pointBorderWidth: 2,
        pointRadius: 4,
        pointHoverRadius: 6,
        fill: true
    }]
}));
</script>

<template>
    <section class="bg-white p-8 rounded-[3rem] shadow-sm border border-gray-400 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
            <div>
                <h3 class="font-black text-[#000B58] text-lg flex items-center gap-2 uppercase tracking-tight">
                    <svg class="w-5 h-5 text-[#00139E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    Tren Pertumbuhan Data
                </h3>
                <p class="text-sm text-gray-400 font-medium mt-1">Jumlah dataset baru yang diinput setiap bulan.</p>
            </div>
            
            <div class="bg-blue-50 px-4 py-2 rounded-xl text-[#00139E] font-black text-[10px] uppercase tracking-widest flex items-center gap-2 self-start sm:self-center border border-blue-100">
                <span class="w-2 h-2 rounded-full bg-[#00139E] animate-pulse"></span>
                12 Bulan Terakhir
            </div>
        </div>

        <div class="h-80 w-full relative min-w-0">
            <Line :data="formattedData" :options="chartOptions" />
        </div>
    </section>
</template>