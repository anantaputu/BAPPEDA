<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({ layout: AppLayout });

const props = defineProps({
    upload: Object,
    excelColumns: Object, 
    previewData: Array,   
    fields: Array, 
    autoMap: Object
});

const showToast = ref(false);
const toastMessage = ref('');
const toastType = ref('success');

// --- LOGIKA OTOMATISASI ---
// 1. Siapkan mapping awal dari autoMap
const initialMapping = { ...props.autoMap };
const initialNewFields = {};

// 2. Loop semua kolom Excel
Object.keys(props.excelColumns).forEach(colKey => {
    // Jika kolom ini TIDAK dikenali (tidak ada di autoMap)
    if (!initialMapping[colKey]) {
        // Otomatis set jadi "Buat Baru"
        initialMapping[colKey] = '__new__';

        // Otomatis isi Nama Field sesuai Header Excel
        initialNewFields[colKey] = {
            nama_field: props.excelColumns[colKey], 
            tipe_field: 'text' 
        };
    }
});

const form = useForm({
    mapping: initialMapping,
    new_fields: initialNewFields
});

const handleSelectChange = (colKey) => {
    // Jika user manual memilih "Buat Field Baru"
    if (form.mapping[colKey] === '__new__') {
        if (!form.new_fields[colKey]) {
            form.new_fields[colKey] = {
                nama_field: props.excelColumns[colKey], 
                tipe_field: 'text'
            };
        }
    }
};

const submit = () => {
    form.post(`/inputer/upload/${props.upload.id_upload}/mapping`, {
        onSuccess: () => showNotification('Data berhasil diproses!', 'success'),
        onError: (errors) => {
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

const getSelectedFieldInfo = (colKey) => {
    const fieldId = form.mapping[colKey];
    if (!fieldId || fieldId === '__new__') return null;
    return props.fields.find(f => f.id_field === fieldId);
};
</script>

<template>
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
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight flex items-center gap-3">
                    Mapping Data
                </h1>
                <p class="text-gray-500 text-sm font-medium mt-1">
                    Kolom hijau sudah cocok. Kolom biru akan dibuatkan field baru secara otomatis.
                </p>
            </div>
            
            <button @click="submit" :disabled="form.processing" class="bg-[#4A6CF7] text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition flex items-center gap-2 disabled:opacity-50 h-fit whitespace-nowrap">
                <svg v-if="form.processing" class="animate-spin h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="4" class="opacity-25"></circle><path d="M4 12a8 8 0 018-8" class="opacity-75"></path></svg>
                {{ form.processing ? 'Menyimpan...' : 'SIMPAN DATA' }}
            </button>
        </div>

        <div class="flex-1 bg-white border border-gray-400 rounded-xl overflow-hidden shadow-sm flex flex-col">
            <div class="overflow-auto flex-1 relative">
                <table class="w-full border-collapse min-w-max">
                    <thead class="bg-gray-50 sticky top-0 z-10 shadow-sm">
                        
                        <tr>
                            <th class="p-3 border-b border-r border-gray-200 w-16 bg-gray-100 text-center text-xs font-bold text-gray-400">#</th>
                            
                            <th v-for="(colName, colKey) in excelColumns" :key="'control-' + colKey" 
                                class="p-2 border-b border-r border-gray-200 min-w-[280px] bg-gray-50 align-top transition-colors duration-300"
                                :class="{
                                    'bg-green-50': form.mapping[colKey] && form.mapping[colKey] !== '__new__',
                                    'bg-blue-50': form.mapping[colKey] === '__new__'
                                }">
                                
                                <div class="space-y-2">
                                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                        Kolom Excel: {{ colName }}
                                    </div>
                                    
                                    <select v-model="form.mapping[colKey]" @change="handleSelectChange(colKey)"
                                        class="w-full text-xs font-bold border rounded-lg focus:ring-blue-500 focus:border-blue-500 py-2 shadow-sm transition-all"
                                        :class="{
                                            'border-green-400 text-green-800 bg-white': form.mapping[colKey] && form.mapping[colKey] !== '__new__',
                                            'border-blue-400 text-blue-800 bg-white': form.mapping[colKey] === '__new__',
                                            'border-gray-300 text-gray-500 bg-gray-50': !form.mapping[colKey]
                                        }">
                                        
                                        <option :value="null">-- Abaikan --</option>
                                        <option value="__new__" class="font-black text-blue-600 bg-blue-50">+ Buat Field Baru</option>
                                        
                                        <optgroup label="Field Database Tersedia">
                                            <option v-for="f in fields" :key="f.id_field" :value="f.id_field">
                                                {{ f.nama_field }}
                                            </option>
                                        </optgroup>
                                    </select>

                                    <div v-if="form.mapping[colKey] === '__new__'" class="bg-blue-100 p-3 rounded-lg border border-blue-200 space-y-2 animate-fade-in-down">
                                        
                                        <div>
                                            <label class="block text-[10px] font-bold text-blue-600 mb-1">Nama Field</label>
                                            <input type="text" v-model="form.new_fields[colKey].nama_field" 
                                                class="w-full text-xs border-blue-300 rounded focus:border-blue-500 focus:ring-blue-500 px-2 py-1 font-bold text-gray-700">
                                        </div>

                                        <div>
                                            <label class="block text-[10px] font-bold text-blue-600 mb-1">Tipe Data</label>
                                            <select v-model="form.new_fields[colKey].tipe_field" 
                                                class="w-full text-xs border-blue-300 rounded focus:border-blue-500 focus:ring-blue-500 px-2 py-1">
                                                <option value="text">Text (Bebas)</option>
                                                <option value="number">Angka / Nominal</option>
                                                <option value="date">Tanggal</option>
                                                <option value="year">Tahun</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div v-else-if="form.mapping[colKey]" class="flex items-center justify-between px-2">
                                        <span class="text-[10px] font-bold text-gray-400 uppercase">Tipe:</span>
                                        <span class="text-[10px] font-bold text-green-600 bg-green-100 px-2 py-0.5 rounded-full border border-green-200">
                                            {{ getSelectedFieldInfo(colKey)?.tipe_field || 'Text' }}
                                        </span>
                                    </div>

                                </div>
                            </th>
                        </tr>

                        <tr class="bg-gray-100 text-gray-600">
                            <th class="p-2 border-b border-r border-gray-300 text-center font-mono text-xs">1</th>
                            <th v-for="(colName, colKey) in excelColumns" :key="'header-' + colKey" class="p-3 border-b border-r border-gray-300 text-left text-xs font-extrabold uppercase tracking-tight text-gray-700 font-mono">
                                {{ colName }}
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white">
                        <tr v-for="(row, index) in previewData.slice(0, 10)" :key="index" class="hover:bg-gray-50 transition">
                            <td class="p-2 border-b border-r border-gray-200 text-center text-xs font-mono text-gray-400 bg-gray-50">{{ index + 2 }}</td>
                            <td v-for="(colName, colKey) in excelColumns" :key="'cell-' + index + '-' + colKey" 
                                class="p-3 border-b border-r border-gray-200 text-xs text-gray-600 font-mono whitespace-nowrap overflow-hidden max-w-xs truncate transition-colors"
                                :class="{
                                    'bg-green-50/20 font-medium text-gray-900': form.mapping[colKey] && form.mapping[colKey] !== '__new__',
                                    'bg-blue-50/20 font-medium text-gray-900': form.mapping[colKey] === '__new__'
                                }">
                                {{ row[colKey] !== null ? row[colKey] : '' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<style scoped>
.animate-fade-in-down {
    animation: fadeInDown 0.3s ease-out;
}
@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>