<script setup>
defineProps({
    topics: Array
});
</script>

<template>
    <div class="py-8">
        <div class="text-center mb-10">
            <h2 class="text-xl font-black text-gray-900">Kategori Sumber Data </h2>
            <p class="text-sm text-gray-500 mt-1">Data Berdasarkan Bidang Bidang</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
            <a href="#" 
               v-for="(topic, index) in topics" 
               :key="index" 
               class="group bg-white rounded-2xl p-6 border border-gray-400 shadow-sm hover:shadow-md hover:border-blue-400 transition flex flex-col items-center justify-center text-center"
            >
                <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="topic.icon" />
                    </svg>
                </div>
                <span class="font-bold text-sm text-gray-700 group-hover:text-blue-800">{{ topic.name }}</span>
            </a>
        </div>
    </div>
</template>