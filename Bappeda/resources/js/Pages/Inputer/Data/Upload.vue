<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

// Gunakan Layout Utama
defineOptions({ layout: AppLayout });

// Menerima Data Master yang dikirim dari Controller
const props = defineProps({
    data: Object
});

// Setup Form
const form = useForm({
    periode: new Date().getFullYear().toString(), // Default tahun saat ini
    file: null
});

// Handle Submit
const submit = () => {
    // FIX: Gunakan URL Manual untuk menghindari error route()
    // URL: /inputer/upload/{id_data}
    form.post(`/inputer/upload/${props.data.id_data}`, {
        forceFormData: true, 
        onProgress: (progress) => {
            console.log('Upload Progress:', progress.percentage);
        }
    });
};
</script>

<template>
    <Head title="Upload Data Excel" />

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6 flex items-center gap-2 text-sm text-gray-500 font-bold">
                <Link href="/inputer/data" class="hover:text-blue-600 transition">Daftar Data</Link>
                <span class="text-gray-300">/</span>
                <span class="text-gray-900">Upload Excel</span>
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-gray-100 relative">
                
                <div class="bg-[#4A6CF7] p-10 text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="flex justify-between items-start">
                            <div>
                                <h2 class="text-3xl font-black tracking-tight mb-2">Upload Data</h2>
                                <p class="text-blue-100 font-medium">Langkah 2: Unggah file Excel untuk indikator ini.</p>
                            </div>
                            <div class="bg-white/20 backdrop-blur-md px-4 py-2 rounded-xl border border-white/30 text-xs font-black uppercase tracking-widest">
                                ID: {{ data.id_data }}
                            </div>
                        </div>
                        
                        <div class="mt-8 bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-inner">
                            <h3 class="font-extrabold text-xl text-white mb-2">{{ data.nama_indikator }}</h3>
                            <div class="flex flex-wrap gap-4 text-xs font-bold uppercase tracking-widest text-blue-200 mt-3">
                                <span class="bg-blue-800/30 px-3 py-1 rounded-lg border border-blue-400/30">Satuan: {{ data.satuan }}</span>
                                <span class="bg-blue-800/30 px-3 py-1 rounded-lg border border-blue-400/30">Sumber: {{ data.sumber }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-16 -mt-16 blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 w-40 h-40 bg-blue-500/30 rounded-full -ml-10 -mb-10 blur-2xl"></div>
                </div>

                <div class="p-10">
                    <form @submit.prevent="submit" class="space-y-8">
                        
                        <div>
                            <label class="block text-sm font-black text-gray-700 mb-3 uppercase tracking-wide">Periode Data (Tahun)</label>
                            <input 
                                v-model="form.periode"
                                type="number" 
                                placeholder="Contoh: 2024"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-5 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#4A6CF7] transition font-bold text-gray-700 text-lg placeholder-gray-400"
                            />
                            <p v-if="form.errors.periode" class="text-red-500 text-xs mt-2 font-bold flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                {{ form.errors.periode }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-black text-gray-700 mb-3 uppercase tracking-wide">File Excel</label>
                            
                            <div class="relative group">
                                <div class="relative border-2 border-dashed border-gray-300 rounded-2xl p-10 text-center hover:bg-blue-50/50 hover:border-blue-400 transition duration-300 cursor-pointer group-hover:shadow-lg">
                                    <input 
                                        type="file" 
                                        @input="form.file = $event.target.files[0]"
                                        accept=".xlsx, .xls, .csv"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20"
                                    />
                                    
                                    <div class="space-y-4 relative z-10 pointer-events-none">
                                        <div class="mx-auto w-16 h-16 bg-blue-100 text-[#4A6CF7] rounded-full flex items-center justify-center group-hover:scale-110 group-hover:rotate-6 transition duration-300 shadow-sm">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                                        </div>
                                        
                                        <div v-if="form.file">
                                            <p class="font-bold text-[#4A6CF7] text-lg">{{ form.file.name }}</p>
                                            <p class="text-xs text-gray-400 font-bold uppercase mt-1">Siap untuk diupload</p>
                                        </div>
                                        <div v-else>
                                            <p class="font-bold text-gray-700 text-lg group-hover:text-[#4A6CF7] transition">Klik untuk upload file</p>
                                            <p class="text-sm text-gray-400 mt-1">atau tarik file Excel (.xlsx) ke sini</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <p v-if="form.errors.file" class="text-red-500 text-xs mt-2 font-bold flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                {{ form.errors.file }}
                            </p>
                            
                            <div v-if="form.progress" class="w-full bg-gray-100 rounded-full h-3 mt-4 overflow-hidden">
                                <div class="bg-[#4A6CF7] h-3 rounded-full transition-all duration-300 ease-out shadow-[0_0_10px_rgba(74,108,247,0.5)]" 
                                     :style="{ width: form.progress.percentage + '%' }">
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                            <Link 
                                href="/inputer/data" 
                                class="text-gray-400 font-bold text-sm hover:text-gray-600 transition px-4 py-2"
                            >
                                Batal
                            </Link>
                            
                            <button 
                                type="submit" 
                                :disabled="form.processing"
                                class="bg-[#4A6CF7] text-white px-10 py-4 rounded-xl font-black text-sm hover:bg-blue-700 transition shadow-lg disabled:opacity-70 disabled:cursor-not-allowed flex items-center gap-3 transform hover:-translate-y-1"
                            >
                                <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>{{ form.processing ? 'MENGUNGGAH...' : 'UPLOAD & LANJUT MAPPING' }}</span>
                                <svg v-if="!form.processing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</template>