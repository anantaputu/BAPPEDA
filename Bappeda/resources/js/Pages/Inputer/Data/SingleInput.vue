<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({ layout: AppLayout });

const props = defineProps({ 
    tema: Array, 
    urusan: Array, 
    bidang: Array, 
    frekuensi: Array 
});

const isLoading = ref(false);

const form = useForm({
    nama_indikator: '',
    deskripsi: '',
    id_tema: '',
    id_urusan: '',
    id_bidang: '',
    id_frekuensi: 1, 
    satuan: '',
    sumber: '',
    kata_kunci: '',
    values: [
        { tahun: new Date().getFullYear(), nilai: '' } 
    ]
});

const addValueRow = () => {
    const lastYear = form.values.length > 0 ? form.values[form.values.length - 1].tahun : new Date().getFullYear();
    form.values.push({ tahun: parseInt(lastYear) + 1, nilai: '' });
};

const removeValueRow = (index) => {
    if (form.values.length > 1) {
        form.values.splice(index, 1);
    }
};

const submitData = () => {
    const hasEmptyValues = form.values.some(v => v.tahun === '' || v.nilai === '');
    if (hasEmptyValues) {
        alert("Mohon lengkapi semua baris Tahun dan Nilai yang telah Anda tambahkan.");
        return;
    }

    isLoading.value = true;
    
    form.post('/inputer/data/store-single', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            router.visit('/inputer/dashboard');
        },
        onFinish: () => isLoading.value = false
    });
};
</script>

<template>
    <Head title="Input Indikator Tunggal" />

    <div class="mx-auto">
        <!-- <Link href="/inputer/dashboard" class="flex items-center gap-2 text-[#A2B5CB] hover:text-[#00139E] transition-colors mb-6 group">
            <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span class="font-bold uppercase tracking-widest text-[10px]">Kembali ke Dashboard</span>
        </Link> -->

        <div class="bg-white rounded-[2.5rem] p-12 shadow-2xl shadow-gray-100 border border-gray-400">
            <div class="mb-12">
                <h1 class="text-4xl font-black text-gray-900 tracking-tight">
                    Input <span class="text-[#00139E]">Indikator Manual</span>
                </h1>
                <p class="text-gray-400 font-medium mt-2">Daftarkan data indikator baru secara presisi ke dalam sistem Smart City.</p>
            </div>

            <form @submit.prevent="submitData" class="space-y-12">
                
                <div class="space-y-8">
                    <h3 class="text-[11px] font-black text-[#00139E] uppercase tracking-[0.3em] flex items-center gap-3">
                        <span class="w-8 h-[2px] bg-[#00139E]"></span>
                        01. Informasi Utama Data
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="md:col-span-2 space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-4">Nama Indikator <span class="text-red-500">*</span></label>
                            <input v-model="form.nama_indikator" type="text" placeholder="Contoh: Jumlah Penduduk Miskin" 
                                class="w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all font-bold text-gray-800"
                                :class="{'border-red-500': form.errors.nama_indikator}">
                            <p v-if="form.errors.nama_indikator" class="text-red-500 text-xs ml-4 font-bold">{{ form.errors.nama_indikator }}</p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-4">Satuan <span class="text-red-500">*</span></label>
                            <input v-model="form.satuan" type="text" placeholder="Jiwa, %, Km, dll" 
                                class="w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all font-bold text-gray-800">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-4">Sumber Data <span class="text-red-500">*</span></label>
                            <input v-model="form.sumber" type="text" placeholder="Instansi/Dinas terkait" 
                                class="w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all font-bold text-gray-800">
                        </div>

                        <div class="md:col-span-2 space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-4">Deskripsi Singkat</label>
                            <textarea v-model="form.deskripsi" placeholder="Penjelasan mengenai indikator ini..." 
                                class="w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all font-medium min-h-[120px]"></textarea>
                        </div>
                    </div>
                </div>

                <div class="space-y-8">
                    <div class="flex justify-between items-center">
                        <h3 class="text-[11px] font-black text-[#00139E] uppercase tracking-[0.3em] flex items-center gap-3">
                            <span class="w-8 h-[2px] bg-[#00139E]"></span>
                            02. Masukkan Nilai Data
                        </h3>
                        <button @click.prevent="addValueRow" 
                            class="bg-blue-50 text-[#00139E] hover:bg-[#00139E] hover:text-white px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2 border border-blue-100 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Baris
                        </button>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <TransitionGroup name="list" tag="div" class="space-y-4">
                            <div v-for="(item, index) in form.values" :key="index" 
                                class="flex flex-col md:flex-row gap-6 items-end bg-gray-50/50 p-8 rounded-[2rem] border border-gray-200 relative group transition-all hover:border-[#00139E]/30">
                                
                                <div class="flex-1 w-full space-y-2">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-4">Waktu / Tahun *</label>
                                    <input v-model="item.tahun" type="text" placeholder="2024" 
                                        class="w-full bg-white border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all font-black text-gray-800">
                                </div>
                                
                                <div class="flex-[2] w-full space-y-2">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-4">Nilai Data *</label>
                                    <input v-model="item.nilai" type="text" placeholder="0.00" 
                                        class="w-full bg-white border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all font-black text-[#00139E] text-lg">
                                </div>

                                <button v-if="form.values.length > 1" @click.prevent="removeValueRow(index)" 
                                    class="p-4 text-red-300 hover:text-red-500 hover:bg-red-50 rounded-2xl transition-all mb-1">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </TransitionGroup>
                    </div>
                </div>

                <div class="space-y-8">
                    <h3 class="text-[11px] font-black text-[#00139E] uppercase tracking-[0.3em] flex items-center gap-3">
                        <span class="w-8 h-[2px] bg-[#00139E]"></span>
                        03. Klasifikasi Metadata
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-4">Tema Sektoral *</label>
                            <select v-model="form.id_tema" class="w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all font-bold text-gray-800 appearance-none">
                                <option value="">Pilih Tema...</option>
                                <option v-for="t in tema" :key="t.id_tema" :value="t.id_tema">{{ t.nama_tema }}</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-4">Urusan Pemerintahan *</label>
                            <select v-model="form.id_urusan" class="w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all font-bold text-gray-800 appearance-none">
                                <option value="">Pilih Urusan...</option>
                                <option v-for="u in urusan" :key="u.id_urusan" :value="u.id_urusan">{{ u.nama_urusan }}</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-4">Bidang / Instansi *</label>
                            <select v-model="form.id_bidang" class="w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all font-bold text-gray-800 appearance-none">
                                <option value="">Pilih Bidang...</option>
                                <option v-for="b in bidang" :key="b.id_bidang" :value="b.id_bidang">{{ b.nama_bidang }}</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-4">Frekuensi Pelaporan *</label>
                            <select v-model="form.id_frekuensi" class="w-full bg-gray-50 border-gray-200 rounded-2xl px-6 py-4 focus:ring-4 focus:ring-blue-100 focus:border-[#00139E] transition-all font-bold text-gray-800 appearance-none">
                                <option v-for="f in frekuensi" :key="f.id_frekuensi" :value="f.id_frekuensi">{{ f.nama_frekuensi }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="pt-10 flex items-center justify-end gap-6 border-t border-gray-100">
                    <Link href="/inputer/dashboard" class="px-8 py-4 text-gray-400 font-bold hover:text-gray-600 transition-colors uppercase tracking-widest text-[10px]">
                        Batal
                    </Link>
                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="bg-[#00139E] text-white px-12 py-5 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-[#000B58] shadow-2xl shadow-blue-200 transition-all disabled:opacity-50 flex items-center gap-3 active:scale-95"
                    >
                        <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ form.processing ? 'Sedang Menyimpan...' : 'Simpan Data Indikator' }}
                    </button>
                </div>
            </form>
        </div>
        <div class="h-20"></div>
    </div>
</template>

<style scoped>
.list-enter-active,
.list-leave-active {
  transition: all 0.4s ease;
}
.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}
</style>