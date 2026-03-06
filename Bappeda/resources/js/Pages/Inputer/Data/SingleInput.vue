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
    nama_data: '',
    deskripsi: '',
    id_tema: '',
    id_urusan: '',
    id_bidang: '',
    id_frekuensi: 1, 
    satuan: '',
    sumber: '',
    kata_kunci: '', 
    tahun: new Date().getFullYear(),
    values: [
        { tahun: new Date().getFullYear().toString(), nilai: '' } 
    ],
    extra_fields_array: [] 
});

// Logic Functions (Dipertahankan sesuai aslinya)
const addValueRow = () => {
    const lastYear = form.values.length > 0 ? form.values[form.values.length - 1].tahun : new Date().getFullYear();
    const nextYear = !isNaN(lastYear) && String(lastYear).trim() !== '' ? String(parseInt(lastYear) + 1) : '';
    form.values.push({ tahun: nextYear, nilai: '' });
};

const removeValueRow = (index) => {
    if (form.values.length > 1) form.values.splice(index, 1);
};

const addExtraField = () => {
    form.extra_fields_array.push({ key: '', value: '' });
};

const removeExtraField = (index) => {
    form.extra_fields_array.splice(index, 1);
};

const submitData = () => {
    const hasEmptyValues = form.values.some(v => v.tahun === '' || String(v.nilai).trim() === '');
    if (hasEmptyValues) { alert("Mohon lengkapi semua kolom Waktu dan Nilai."); return; }

    let formattedExtraFields = {};
    form.extra_fields_array.forEach(item => {
        if (item.key.trim() !== '') formattedExtraFields[item.key.trim()] = item.value.trim();
    });

    isLoading.value = true;
    form.transform((data) => ({
        ...data,
        extra_fields: formattedExtraFields 
    })).post('/inputer/data/store-single', {
        preserveScroll: true,
        onSuccess: () => router.visit('/inputer/data'),
        onFinish: () => isLoading.value = false
    });
};
</script>

<template>
    <Head title="Input Master Data" />

    <div class="max-w-6xl relative">
        <div class="absolute top-0 right-0 -translate-y-1/4 translate-x-1/4 w-96 h-96 bg-primary/5 rounded-full blur-3xl -z-10"></div>

        <div class="bg-white rounded-xl border border-gray-400 p-8 md:p-12 shadow-2xl shadow-primary/5 relative z-10">
            <div class="mb-12">
                <h1 class="text-3xl font-black text-primary leading-tight tracking-tight uppercase">
                    Input <span class="text-secondary">Master Data</span>
                </h1>
                <p class="text-textsecondary font-medium mt-2 leading-relaxed">Daftarkan metadata indikator baru dan isi capaian nilainya secara terstruktur.</p>
            </div>

            <form @submit.prevent="submitData" class="space-y-12">
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 bg-bgsoft/50 p-8 rounded-xl border border-gray-200 relative">
                    <div class="md:col-span-4 mb-2 flex items-center gap-4">
                        <h3 class="text-[11px] font-black text-primary uppercase tracking-[0.3em] flex items-center gap-3">
                            Informasi Utama Data
                        </h3>
                        <div class="flex-1 h-[1px] bg-gray-200"></div>
                    </div>

                    <div class="md:col-span-3 space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-textsecondary ml-2">Nama Indikator <span class="text-integritas">*</span></label>
                        <input v-model="form.nama_data" type="text" placeholder="Contoh: Jumlah Penduduk Miskin"
                            class="w-full bg-white border border-gray-400 rounded-xl px-6 py-4 font-bold text-primary focus:outline-none focus:border-secondary focus:ring-4 focus:ring-secondary/5 transition-all shadow-sm" 
                            :class="{'border-integritas': form.errors.nama_data}" />
                        <p v-if="form.errors.nama_data" class="text-integritas text-[10px] font-black uppercase ml-2 tracking-tighter">{{ form.errors.nama_data }}</p>
                    </div>

                    <div class="md:col-span-1 space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-textsecondary ml-2">Tema Sektoral *</label>
                        <select v-model="form.id_tema" class="w-full bg-white border border-gray-400 rounded-xl px-6 py-4 font-bold text-primary focus:border-secondary focus:ring-4 focus:ring-secondary/5 transition-all shadow-sm appearance-none cursor-pointer" required>
                            <option value="">Pilih Tema...</option>
                            <option v-for="t in tema" :key="t.id_tema" :value="t.id_tema">{{ t.nama_tema }}</option>
                        </select>
                    </div>

                    <div class="md:col-span-1 space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-textsecondary ml-2">Urusan *</label>
                        <select v-model="form.id_urusan" class="w-full bg-white border border-gray-400 rounded-xl px-6 py-4 font-bold text-primary focus:border-secondary shadow-sm appearance-none" required>
                            <option value="">Pilih Urusan...</option>
                            <option v-for="u in urusan" :key="u.id_urusan" :value="u.id_urusan">{{ u.nama_urusan }}</option>
                        </select>
                    </div>

                    <div class="md:col-span-1 space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-textsecondary ml-2">Bidang *</label>
                        <select v-model="form.id_bidang" class="w-full bg-white border border-gray-400 rounded-xl px-6 py-4 font-bold text-primary focus:border-secondary shadow-sm appearance-none" required>
                            <option value="">Pilih Bidang...</option>
                            <option v-for="b in bidang" :key="b.id_bidang" :value="b.id_bidang">{{ b.nama_bidang }}</option>
                        </select>
                    </div>

                    <div class="md:col-span-1 space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-secondary ml-2 font-black">Frekuensi Update *</label>
                        <select v-model="form.id_frekuensi" class="w-full bg-white border-secondary/30 border rounded-xl px-6 py-4 font-black text-secondary focus:ring-secondary shadow-sm appearance-none" required>
                            <option v-for="f in frekuensi" :key="f.id_frekuensi" :value="f.id_frekuensi">{{ f.nama_frekuensi }}</option>
                        </select>
                    </div>

                    <div class="md:col-span-1 space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-textsecondary ml-2">Satuan *</label>
                        <input v-model="form.satuan" type="text" placeholder="Jiwa, %, Km" class="w-full bg-white border border-gray-400 rounded-xl px-6 py-4 font-bold text-primary shadow-sm" required />
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-textsecondary ml-2">Sumber Data *</label>
                        <input v-model="form.sumber" type="text" placeholder="Instansi/Dinas terkait" class="w-full bg-white border border-gray-400 rounded-xl px-6 py-4 font-bold text-primary shadow-sm" required />
                    </div>

                    <div class="md:col-span-1 space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-textsecondary ml-2">Tahun Terbit</label>
                        <input v-model="form.tahun" type="number" class="w-full bg-white border border-gray-400 rounded-xl px-6 py-4 font-bold text-primary shadow-sm" />
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-textsecondary ml-2">Deskripsi</label>
                        <textarea v-model="form.deskripsi" rows="2" placeholder="Penjelasan singkat..." class="w-full bg-white border border-gray-400 rounded-xl px-6 py-4 font-bold text-primary shadow-sm"></textarea>
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-textsecondary ml-2 italic">Kata Kunci (Tag)</label>
                        <textarea v-model="form.kata_kunci" rows="2" placeholder="ekonomi, bps, sosial..." class="w-full bg-white border border-gray-400 rounded-xl px-6 py-4 font-bold text-primary italic shadow-sm"></textarea>
                    </div>
                </div>

                <div class="space-y-6 py-12">
                    <div class="flex justify-between items-center border-l-4 border-secondary pl-4">
                        <div>
                            <h4 class="text-sm font-black uppercase text-primary tracking-widest">Atribut Tambahan</h4>
                            <p class="text-[10px] text-textsecondary font-bold uppercase opacity-50 mt-1">Informasi pendukung metadata indikator</p>
                        </div>
                        <button @click.prevent="addExtraField" class="bg-bgsoft text-primary border border-gray-300 hover:bg-secondary hover:text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all active:scale-95 shadow-sm">
                            + Tambah Atribut
                        </button>
                    </div>

                    <div v-if="form.extra_fields_array.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <TransitionGroup name="list" tag="div" class="contents">
                            <div v-for="(field, index) in form.extra_fields_array" :key="index" class="bg-white p-6 rounded-xl border border-gray-400 relative group shadow-sm hover:border-secondary transition-all">
                                <div class="space-y-4">
                                    <div class="space-y-1">
                                        <label class="text-[9px] font-black uppercase tracking-widest text-textsecondary opacity-60 ml-1">Nama Atribut</label>
                                        <input v-model="field.key" type="text" placeholder="Cth: Nomenklatur" class="w-full bg-bgsoft border border-gray-200 rounded-lg px-4 py-2 font-black text-xs text-primary focus:ring-secondary" required />
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[9px] font-black uppercase tracking-widest text-textsecondary opacity-60 ml-1">Keterangan</label>
                                        <input v-model="field.value" type="text" placeholder="Isi detail..." class="w-full bg-white border border-gray-200 rounded-lg px-4 py-2 text-xs text-primary font-bold" />
                                    </div>
                                </div>
                                <button @click.prevent="removeExtraField(index)" class="absolute -top-3 -right-3 text-white bg-integritas hover:bg-red-700 w-8 h-8 rounded-full flex items-center justify-center transition-all shadow-lg scale-0 group-hover:scale-100 active:scale-90">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        </TransitionGroup>
                    </div>
                </div>

                <div class="border border-gray-400 rounded-xl overflow-hidden shadow-sm">
                    <div class="bg-primary p-6 flex flex-wrap justify-between items-center gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-white font-black uppercase tracking-widest text-sm leading-tight">Capaian Nilai Indikator</h3>
                                <p class="text-secondary font-black text-[10px] uppercase tracking-widest mt-1 opacity-80">Tambah periode pelaporan secara horizontal</p>
                            </div>
                        </div>
                        <button @click.prevent="addValueRow" class="bg-secondary text-white hover:bg-white hover:text-primary px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg active:scale-95 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Periode
                        </button>
                    </div>

                    <div class="overflow-x-auto custom-scrollbar bg-white">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-bgsoft divide-x divide-gray-200">
                                    <th class="p-6 text-[10px] font-black text-textsecondary uppercase tracking-[0.2em] w-64 sticky left-0 bg-bgsoft z-20 shadow-md">Kategori / Waktu</th>
                                    <th v-for="(item, index) in form.values" :key="'head-'+index" class="p-4 min-w-[240px] relative group bg-white">
                                        <div class="flex items-center justify-between gap-4">
                                            <input v-model="item.tahun" type="text" placeholder="2024" class="w-full bg-bgsoft border border-gray-200 rounded-lg px-4 py-2.5 text-xs font-black text-primary focus:ring-secondary focus:bg-white transition-all uppercase tracking-widest" required>
                                            <button @click.prevent="removeValueRow(index)" class="text-integritas hover:bg-integritas/10 p-2 rounded-lg transition-colors opacity-0 group-hover:opacity-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="divide-x divide-gray-100">
                                    <td class="p-6 bg-white border-t border-gray-100 sticky left-0 z-10 shadow-md">
                                        <span class="text-xs font-black text-primary uppercase tracking-wider">Capaian Nilai</span>
                                        <span class="block text-[9px] text-textsecondary font-bold mt-1 opacity-50 uppercase">Gunakan (.) untuk desimal</span>
                                    </td>
                                    <td v-for="(item, index) in form.values" :key="'val-'+index" class="p-4 border-t border-gray-100">
                                        <input v-model="item.nilai" type="text" placeholder="0.00" 
                                            class="w-full bg-inovasi/5 border border-inovasi/20 text-inovasi rounded-xl px-5 py-4 text-sm font-black text-center focus:ring-inovasi focus:bg-white transition-all shadow-sm" required>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pt-10 flex items-center justify-end gap-6 border-t border-bgsoft">
                    <Link href="/inputer/data" class="text-xs font-black uppercase tracking-widest text-textsecondary hover:text-integritas transition-colors">
                        Batal
                    </Link>
                    <button type="submit" :disabled="form.processing"
                        class="bg-primary text-white px-12 py-4 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-secondary shadow-xl shadow-primary/20 transition-all disabled:opacity-50 flex items-center gap-3 active:scale-95">
                        <svg v-if="form.processing" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Indikator Baru' }}
                    </button>
                </div>

            </form>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { height: 10px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #E2E8F0; border-radius: 20px; border: 3px solid white; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #0284C7; }

.list-enter-active, .list-leave-active { transition: all 0.3s ease; }
.list-enter-from, .list-leave-to { opacity: 0; transform: translateY(-12px); }
</style>