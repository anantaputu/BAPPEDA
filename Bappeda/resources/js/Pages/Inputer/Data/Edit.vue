<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AlertModal from '@/Components/Layout/AlertModal.vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue'

defineOptions({ layout: AppLayout });

const showAlertModal = ref(false);
const alertTitle = ref('Informasi');
const alertMessage = ref('');
const alertType = ref('info');

const openAlert = (title, message, type = 'info') => {
    alertTitle.value = title;
    alertMessage.value = message;
    alertType.value = type;
    showAlertModal.value = true;
};

const props = defineProps({
    dataIndikator: Object,
    tema: Array,
    urusan: Array,
    bidang: Array,
    frekuensi: Array,
    katakunci: Array,
});

// 1. Parsing Informasi Tambahan
let parsedExtraFields = {};
if (props.dataIndikator?.informasi_tambahan) {
    let rawExtra = props.dataIndikator.informasi_tambahan;
    if (typeof rawExtra === 'string') {
        try { rawExtra = JSON.parse(rawExtra); } catch (e) { rawExtra = {}; }
    }
    for (let key in rawExtra) {
        if (key.toLowerCase() !== 'nama data' && key.toLowerCase() !== 'nama indikator') {
            parsedExtraFields[key] = rawExtra[key] || ''; 
        }
    }
}

// 2. Mapping Form
const form = useForm({
    nama_data: props.dataIndikator.nama_data,
    deskripsi: props.dataIndikator.deskripsi,
    id_tema: props.dataIndikator.id_tema,
    id_urusan: props.dataIndikator.id_urusan,
    id_bidang: props.dataIndikator.id_bidang,
    id_frekuensi: props.dataIndikator.id_frekuensi,
    satuan: props.dataIndikator.satuan,
    sumber: props.dataIndikator.sumber,
    id_katakunci: Array.isArray(props.dataIndikator.katakunci)
        ? props.dataIndikator.katakunci.map(tag => tag.id_katakunci)
        : [],
    tahun_terbit: props.dataIndikator.tahun_terbit || '', 
    
    // Properti Extra Fields dan Values
    extra_fields: parsedExtraFields, 
    values: props.dataIndikator.values && props.dataIndikator.values.length > 0 
            ? props.dataIndikator.values.map(v => ({ tahun: v.tahun, nilai: v.nilai }))
            : [{ tahun: String(new Date().getFullYear()), nilai: '' }]
});

const addColumn = () => {
    const lastYear = form.values.length > 0 ? form.values[form.values.length - 1].tahun : new Date().getFullYear();
    const nextYear = !isNaN(lastYear) && lastYear.trim() !== '' ? String(parseInt(lastYear) + 1) : '';
    form.values.push({ tahun: nextYear, nilai: '' });
};

const removeColumn = (index) => {
    if (form.values.length > 1) {
        form.values.splice(index, 1);
    } else {
        openAlert('Tidak Bisa Dihapus', 'Minimal harus ada satu kolom data tersisa.', 'warning');
    }
};

const submit = () => {
    const hasEmptyValues = form.values.some(v => v.tahun === '' || v.nilai === '');
    if (hasEmptyValues) {
        openAlert('Validasi Gagal', 'Mohon lengkapi semua baris Waktu dan Nilai pada tabel.', 'warning');
        return;
    }

    form.put(`/inputer/data/${props.dataIndikator.id_data}`, {
        preserveScroll: true,
        onSuccess: () => {
            openAlert('Berhasil', 'Data berhasil diperbarui!', 'success');
        },
        onError: (errors) => {
            const firstError =
                errors?.nama_data ||
                errors?.tahun_terbit ||
                errors?.error ||
                Object.values(errors || {})[0];
            openAlert('Gagal Menyimpan', firstError || 'Periksa kembali inputan Anda.', 'error');
        }
    });
};
</script>

<template>
    <Head title="Edit Master Data" />

    <div class="min-h-full py-4">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-8">
            <div>
                <h1 class="text-2xl font-black text-primary uppercase tracking-tight">
                    Edit <span class="text-secondary">Data</span>
                </h1>
                <p class="text-[10px] text-textsecondary font-black uppercase tracking-[0.2em] mt-2 flex items-center gap-2">
                    <span class="w-2 h-2 bg-secondary rounded-full animate-pulse shadow-sm shadow-secondary/50"></span>
                    Sinkronisasi Metadata Dan Nilai Indikator
                </p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-400 p-8 md:p-10 shadow-2xl shadow-primary/5">
            <div class="mb-8">
                <h2 class="text-lg font-black text-primary uppercase tracking-[0.15em]">Formulir Perubahan</h2>
                <p class="text-xs font-bold text-textsecondary mt-2">Perbarui metadata master dan capaian nilai tanpa mengubah struktur data.</p>
            </div>

            <form @submit.prevent="submit" class="space-y-10">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 bg-bgsoft/40 p-8 rounded-xl border border-gray-300 relative">
                    
                    <div class="md:col-span-4 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-textsecondary ml-1">Nama Indikator</label>
                        <input v-model="form.nama_data" type="text" class="w-full bg-white border border-gray-400 rounded-xl px-5 py-4 text-sm font-bold text-primary focus:outline-none focus:border-secondary focus:ring-4 focus:ring-secondary/5 transition-all duration-300" />
                    </div>

                    <div class="md:col-span-1 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-textsecondary ml-1">Tema Sektoral</label>
                        <select v-model="form.id_tema" class="w-full bg-white border border-gray-400 rounded-xl px-5 py-4 text-sm font-bold text-primary focus:outline-none focus:border-secondary focus:ring-4 focus:ring-secondary/5 transition-all duration-300">
                            <option v-for="t in tema" :key="t.id_tema" :value="t.id_tema">{{ t.nama_tema }}</option>
                        </select>
                    </div>

                    <div class="md:col-span-1 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-textsecondary ml-1">Urusan</label>
                        <select v-model="form.id_urusan" class="w-full bg-white border border-gray-400 rounded-xl px-5 py-4 text-sm font-bold text-primary focus:outline-none focus:border-secondary focus:ring-4 focus:ring-secondary/5 transition-all duration-300">
                            <option v-for="u in urusan" :key="u.id_urusan" :value="u.id_urusan">{{ u.nama_urusan }}</option>
                        </select>
                    </div>

                    <div class="md:col-span-1 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-textsecondary ml-1">Bidang</label>
                        <select v-model="form.id_bidang" class="w-full bg-white border border-gray-400 rounded-xl px-5 py-4 text-sm font-bold text-primary focus:outline-none focus:border-secondary focus:ring-4 focus:ring-secondary/5 transition-all duration-300">
                            <option v-for="b in bidang" :key="b.id_bidang" :value="b.id_bidang">{{ b.nama_bidang }}</option>
                        </select>
                    </div>

                    <div class="md:col-span-1 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-inovasi ml-1">Frekuensi Update</label>
                        <select v-model="form.id_frekuensi" class="w-full bg-white border border-inovasi/30 text-inovasi rounded-xl px-5 py-4 text-sm font-bold focus:outline-none focus:border-inovasi focus:ring-4 focus:ring-inovasi/5 transition-all duration-300">
                            <option v-for="f in frekuensi" :key="f.id_frekuensi" :value="f.id_frekuensi">{{ f.nama_frekuensi }}</option>
                        </select>
                    </div>

                    <div class="md:col-span-1 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-textsecondary ml-1">Satuan</label>
                        <input v-model="form.satuan" type="text" class="w-full bg-white border border-gray-400 rounded-xl px-5 py-4 text-sm font-bold text-primary focus:outline-none focus:border-secondary focus:ring-4 focus:ring-secondary/5 transition-all duration-300" />
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-textsecondary ml-1">Sumber Data</label>
                        <input v-model="form.sumber" type="text" class="w-full bg-white border border-gray-400 rounded-xl px-5 py-4 text-sm font-bold text-primary focus:outline-none focus:border-secondary focus:ring-4 focus:ring-secondary/5 transition-all duration-300" />
                    </div>

                    <div class="md:col-span-1 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-textsecondary ml-1">Tahun Terbit</label>
                        <input v-model="form.tahun_terbit" type="number" class="w-full bg-white border border-gray-400 rounded-xl px-5 py-4 text-sm font-bold text-primary focus:outline-none focus:border-secondary focus:ring-4 focus:ring-secondary/5 transition-all duration-300" />
                    </div>

                    <template v-if="Object.keys(form.extra_fields).length > 0">
                        <div class="md:col-span-4 border-t border-gray-300 my-2 pt-6">
                            <h4 class="text-[10px] font-black uppercase text-primary tracking-widest flex items-center gap-2">
                                <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                                Atribut Tambahan
                            </h4>
                        </div>
                        <div v-for="(value, key) in form.extra_fields" :key="key" class="md:col-span-2 space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-textsecondary ml-1">{{ key }}</label>
                            <input v-model="form.extra_fields[key]" type="text" class="w-full bg-white border border-gray-400 rounded-xl px-5 py-4 text-sm font-medium text-primary focus:outline-none focus:border-secondary focus:ring-4 focus:ring-secondary/5 transition-all duration-300" />
                        </div>
                    </template>

                    <div class="md:col-span-2 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-textsecondary ml-1">Deskripsi</label>
                        <textarea v-model="form.deskripsi" rows="2" class="w-full bg-white border border-gray-400 rounded-xl px-5 py-4 text-sm font-medium text-primary focus:outline-none focus:border-secondary focus:ring-4 focus:ring-secondary/5 transition-all duration-300"></textarea>
                    </div>

                    <div class="md:col-span-2 space-y-3">
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-textsecondary ml-1 italic">
                            Kata Kunci / Tag <span class="text-[9px] font-medium text-gray-400">(Multi-select)</span>
                        </label>
                        <div class="flex flex-wrap gap-2 p-4 bg-white border border-gray-400 rounded-xl min-h-[108px] shadow-inner">
                            <label v-for="tag in katakunci" :key="tag.id_katakunci"
                                class="relative flex items-center gap-2 px-4 py-2 rounded-xl border cursor-pointer transition-all select-none"
                                :class="form.id_katakunci.includes(tag.id_katakunci)
                                    ? 'bg-secondary border-secondary text-white shadow-md'
                                    : 'bg-bgsoft border-gray-200 text-primary hover:border-secondary/50'">
                                
                                <input type="checkbox" :value="tag.id_katakunci" v-model="form.id_katakunci" class="hidden" />
                                <span class="text-[10px] font-black uppercase tracking-tighter">{{ tag.nama_katakunci }}</span>
                                <svg v-if="form.id_katakunci.includes(tag.id_katakunci)" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </label>
                            <p v-if="!katakunci || katakunci.length === 0" class="text-[10px] text-gray-400 italic">Master kata kunci belum tersedia.</p>
                        </div>
                    </div>
                </div>

                <div class="border border-gray-400 rounded-xl overflow-hidden shadow-sm relative bg-white">
                    <div class="bg-primary p-5 flex flex-wrap justify-between items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-white font-black uppercase tracking-widest text-sm">Capaian Nilai</h3>
                                <p class="text-blue-200 text-[10px] font-medium mt-0.5">Edit nilai atau tambah waktu periode ke samping.</p>
                            </div>
                        </div>
                        <button @click.prevent="addColumn" class="bg-secondary text-white hover:bg-white hover:text-primary px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-lg border border-secondary/20 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Kolom Data
                        </button>
                    </div>

                    <div class="overflow-x-auto custom-scrollbar bg-white">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr>
                                    <th class="p-4 bg-bgsoft border-b border-gray-300 border-r text-[10px] font-black text-textsecondary uppercase tracking-widest w-48 min-w-[200px] sticky left-0 z-20 shadow-[2px_0_5px_rgba(0,0,0,0.03)]">
                                        Atribut / Periode
                                    </th>
                                    <th v-for="(item, index) in form.values" :key="'head-'+index" class="p-4 bg-white border-b border-gray-200 min-w-[220px] border-r border-gray-100">
                                        <div class="flex items-center justify-between mb-1">
                                            <label class="text-[9px] font-black text-secondary uppercase">Waktu / Bulan / Tahun *</label>
                                            <button @click.prevent="removeColumn(index)" class="text-red-400 hover:text-red-600 transition-colors bg-red-50 p-1.5 rounded-md">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                        <input v-model="item.tahun" type="text" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2.5 text-xs font-bold text-primary focus:outline-none focus:border-secondary focus:ring-4 focus:ring-secondary/5 transition-all duration-300" required>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="p-4 bg-white border-b border-gray-100 border-r sticky left-0 z-10 shadow-[2px_0_5px_rgba(0,0,0,0.03)]">
                                        <span class="text-xs font-black text-primary uppercase tracking-wide">Isi Nilai Capaian</span>
                                        <span class="block text-[10px] text-textsecondary mt-1">Gunakan (.) untuk desimal</span>
                                    </td>
                                    <td v-for="(item, index) in form.values" :key="'val-'+index" class="p-4 border-b border-gray-100 border-r">
                                        <input v-model="item.nilai" type="text" placeholder="0.00" class="w-full bg-inovasi/5 border border-inovasi/30 text-inovasi rounded-xl px-4 py-3 text-sm font-black text-center focus:outline-none focus:ring-4 focus:ring-inovasi/10 focus:bg-inovasi/10 transition-all duration-300" required>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pt-6 flex items-center justify-end gap-4 border-t border-gray-200">
                    <Link href="/inputer/dashboard" class="px-8 py-4 text-textsecondary font-bold hover:text-primary transition-colors uppercase tracking-widest text-xs">
                        Batal
                    </Link>
                    <button type="submit" :disabled="form.processing"
                        class="bg-primary text-white px-12 py-4 rounded-xl text-sm font-black uppercase tracking-widest hover:bg-secondary transition-all shadow-lg shadow-primary/20 disabled:opacity-50 flex items-center gap-2">
                        <svg v-if="form.processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan Data' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <AlertModal
        :show="showAlertModal"
        :title="alertTitle"
        :description="alertMessage"
        :type="alertType"
        @close="showAlertModal = false"
    />
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { height: 10px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 6px; border: 2px solid #f1f5f9; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
