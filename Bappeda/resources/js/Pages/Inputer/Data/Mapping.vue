<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
<<<<<<< HEAD
=======
import { ref } from 'vue';
>>>>>>> origin

defineOptions({ layout: AppLayout });

const props = defineProps({
    upload: Object,
    excelColumns: Object, 
    previewData: Array,   
    fields: Array,
<<<<<<< HEAD
    autoMap: Object 
});

const form = useForm({
    mapping: props.autoMap ? { ...props.autoMap } : {},
    new_fields: {}
});

const handleSelectChange = (colKey) => {
    if (form.mapping[colKey] === '__new__') {
        form.new_fields[colKey] = {
            nama_field: props.excelColumns[colKey],
            tipe_field: 'text'
        };
    } else if (form.new_fields[colKey]) {
        delete form.new_fields[colKey];
    }
};

const submit = () => {
    form.post(`/input-data/${props.upload.id_upload}/mapping`);
=======
    autoMap: Object
});

const showToast = ref(false);
const toastMessage = ref('');
const toastType = ref('success');

const form = useForm({
    mapping: props.autoMap ? { ...props.autoMap } : {}
});

const submit = () => {
    form.post(`/inputer/upload/${props.upload.id_upload}/mapping`, {
        onSuccess: () => showNotification('Data berhasil diproses!', 'success'),
        
        // PERBAIKAN: Tampilkan pesan asli dari server
        onError: (errors) => {
            // Ambil pesan error pertama yang dikirim server
            const message = Object.values(errors)[0] || 'Gagal memproses data.';
            showNotification(message, 'error');
        }
    });
};

const showNotification = (message, type = 'success') => {
    toastMessage.value = message;
    toastType.value = type;
    showToast.value = true;
    setTimeout(() => showToast.value = false, 4000);
};

// Helper visual nama field
const getMappedFieldName = (colKey) => {
    const fieldId = form.mapping[colKey];
    if (!fieldId) return null;
    const field = props.fields.find(f => f.id_field === fieldId);
    return field ? field.nama_field : null;
>>>>>>> origin
};
</script>

<template>
<<<<<<< HEAD
    <Head title="Mapping Data" />

    <div class="max-w-7xl mx-auto space-y-8">
        <div>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Mapping & Preview</h1>
            <p class="text-gray-400 text-xs font-bold mt-1 uppercase">Sinkronisasi kolom Excel dengan struktur database</p>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest w-1/4">Kolom Excel</th>
                        <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest w-1/3">Preview Data</th>
                        <th class="p-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Mapping Field</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-for="(colName, colKey) in excelColumns" :key="colKey" class="group">
                        <td class="p-6 align-top">
                            <div class="font-extrabold text-gray-900 text-sm">{{ colName }}</div>
                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">KODE: {{ colKey }}</div>
                        </td>
                        
                        <td class="p-6 align-top">
                            <div class="space-y-1">
                                <div v-for="(row, index) in previewData.slice(0, 3)" :key="index" class="text-xs text-gray-500 font-medium flex gap-2">
                                    <span class="text-blue-300">{{ index + 1 }}.</span>
                                    <span class="truncate">{{ row[colKey] || '(kosong)' }}</span>
                                </div>
                            </div>
                        </td>

                        <td class="p-6 align-top">
                            <select 
                                v-model="form.mapping[colKey]" 
                                @change="handleSelectChange(colKey)"
                                class="w-full border-gray-100 bg-gray-50 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-500 transition"
                            >
                                <option value="">-- Abaikan Kolom Ini --</option>
                                <option v-for="f in fields" :key="f.id_field" :value="f.id_field">
                                    {{ f.nama_field }}
                                </option>
                                <option value="__new__" class="font-black text-blue-600">+ TAMBAH FIELD BARU</option>
                            </select>

                            <div v-if="form.mapping[colKey] === '__new__'" class="mt-4 p-5 bg-blue-50/50 rounded-2xl border border-blue-100 space-y-4">
                                <div>
                                    <label class="text-[10px] font-black text-blue-800 uppercase">Nama Field Baru</label>
                                    <input v-model="form.new_fields[colKey].nama_field" class="w-full mt-1 border-blue-100 rounded-lg text-sm" />
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-blue-800 uppercase">Tipe Data</label>
                                    <select v-model="form.new_fields[colKey].tipe_field" class="w-full mt-1 border-blue-100 rounded-lg text-sm font-bold">
                                        <option value="text">Text</option>
                                        <option value="number">Number</option>
                                        <option value="date">Date</option>
                                    </select>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex justify-end">
            <button 
                @click="submit" 
                :disabled="form.processing"
                class="bg-green-600 text-white px-12 py-5 rounded-2xl font-black tracking-widest hover:bg-green-700 shadow-xl shadow-green-100 transition disabled:opacity-50"
            >
                {{ form.processing ? 'SEDANG MEMPROSES...' : 'SIMPAN & PROSES DATA' }}
            </button>
        </div>
    </div>
</template>
=======
    <Head title="Mapping Data Excel" />

    <Transition enter-active-class="transform ease-out duration-300 transition" enter-from-class="translate-y-2 opacity-0" enter-to-class="translate-y-0 opacity-100" leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="showToast" class="fixed top-20 right-5 z-50 bg-white shadow-xl rounded-xl border border-gray-100 p-4 flex items-center gap-3">
            <div :class="toastType === 'success' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'" class="p-2 rounded-full">
                <svg v-if="toastType === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </div>
            <div>
                <p class="font-bold text-gray-900 text-sm">{{ toastType === 'success' ? 'Sukses' : 'Error' }}</p>
                <p class="text-xs text-gray-500">{{ toastMessage }}</p>
            </div>
        </div>
    </Transition>

    <div class="py-6 px-4 sm:px-6 lg:px-8 h-[calc(100vh-4rem)] flex flex-col gap-6">
        
         <div class="flex flex-col md:flex-row justify-between items-end gap-4">
            <!-- <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight flex items-center gap-3">
                    Mapping Otomatis
                </h1>
                <p class="text-gray-500 text-sm font-medium mt-1">
                    Sistem mencocokkan nama kolom Excel dengan Field Database.
                </p>

                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                    <p class="text-[10px] font-black text-yellow-700 uppercase tracking-widest mb-2">
                        Agar Terdeteksi, Rename Header Excel Anda Menjadi:
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <span v-for="f in fields" :key="f.id_field" 
                              class="bg-white border border-yellow-300 text-gray-700 px-2 py-1 rounded text-xs font-mono font-bold select-all cursor-pointer hover:bg-yellow-100">
                            {{ f.nama_field }}
                        </span>
                    </div>
                </div>
            </div> -->
            
            <button @click="submit" :disabled="form.processing" class="bg-[#4A6CF7] text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition flex items-center gap-2 disabled:opacity-50 h-fit whitespace-nowrap">
                <svg v-if="form.processing" class="animate-spin h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="4" class="opacity-25"></circle><path d="M4 12a8 8 0 018-8" class="opacity-75"></path></svg>
                {{ form.processing ? 'Menyimpan...' : 'SIMPAN SEKARANG' }}
            </button>
        </div> 

        <div class="flex-1 bg-white border border-gray-300 rounded-xl overflow-hidden shadow-sm flex flex-col">
            <div class="overflow-auto flex-1 relative">
                <table class="w-full border-collapse min-w-max">
                    <thead class="bg-gray-50 sticky top-0 z-10 shadow-sm">
                        
                        <!-- <tr>
                            <th class="p-3 border-b border-r border-gray-200 w-16 bg-gray-100 text-center text-xs font-bold text-gray-400">#</th>
                            
                            <th v-for="(colName, colKey) in excelColumns" :key="'control-' + colKey" 
                                class="p-2 border-b border-r border-gray-200 min-w-[200px] bg-gray-50 align-top"
                                :class="{'bg-green-50/30': form.mapping[colKey]}">
                                
                                <div class="space-y-2">
                                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Kolom {{ colKey }}</div>
                                    
                                    <div v-if="form.mapping[colKey]" class="bg-green-100 text-green-700 px-3 py-2 rounded-lg text-xs font-bold text-center border border-green-200 shadow-sm flex items-center justify-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        {{ getMappedFieldName(colKey) }}
                                    </div>
                                    <div v-else class="bg-gray-100 text-gray-400 px-3 py-2 rounded-lg text-xs font-bold text-center border border-gray-200 border-dashed">
                                        Tidak Cocok (Diabaikan)
                                    </div>
                                </div>
                            </th>
                        </tr> -->

                        <tr class="bg-gray-100 text-gray-600">
                            <th class="p-2 border-b border-r border-gray-300 text-center font-mono text-xs">1</th>
                            <th v-for="(colName, colKey) in excelColumns" :key="'header-' + colKey" class="p-3 border-b border-r border-gray-300 text-left text-xs font-extrabold uppercase tracking-tight text-gray-700 font-mono">
                                {{ colName }}
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white">
                        <tr v-for="(row, index) in previewData.slice(0, 10)" :key="index" class="hover:bg-blue-50/30 transition">
                            <td class="p-2 border-b border-r border-gray-200 text-center text-xs font-mono text-gray-400 bg-gray-50">{{ index + 2 }}</td>
                            <td v-for="(colName, colKey) in excelColumns" :key="'cell-' + index + '-' + colKey" 
                                class="p-3 border-b border-r border-gray-200 text-xs text-gray-600 font-mono whitespace-nowrap overflow-hidden max-w-xs truncate"
                                :class="{'bg-green-50/10 text-gray-900 font-medium': form.mapping[colKey]}">
                                {{ row[colKey] !== null ? row[colKey] : '' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="bg-gray-50 border-t border-gray-200 p-3 text-xs text-gray-500 flex justify-between items-center">
                <span>Preview <strong>{{ previewData.length }}</strong> baris.</span>
            </div>
        </div>
    </div>
</template>

<style scoped>
.overflow-auto::-webkit-scrollbar { width: 8px; height: 8px; }
.overflow-auto::-webkit-scrollbar-track { background: #f1f1f1; }
.overflow-auto::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 4px; }
.overflow-auto::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }
</style>
>>>>>>> origin
