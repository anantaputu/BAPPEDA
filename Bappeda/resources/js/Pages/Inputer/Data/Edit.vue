<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Head, Link } from '@inertiajs/vue3';

defineOptions({ layout: AppLayout });

const props = defineProps({
    dataIndikator: Object, // Pastikan relasi 'values' di-load dari controller
    tema: Array,
    urusan: Array,
    bidang: Array,
    frekuensi: Array,
});

// Mapping data ke form
const form = useForm({
    nama_indikator: props.dataIndikator.nama_indikator,
    deskripsi: props.dataIndikator.deskripsi,
    id_tema: props.dataIndikator.id_tema,
    id_urusan: props.dataIndikator.id_urusan,
    id_bidang: props.dataIndikator.id_bidang,
    id_frekuensi: props.dataIndikator.id_frekuensi,
    satuan: props.dataIndikator.satuan,
    sumber: props.dataIndikator.sumber,
    status: props.dataIndikator.status,
    
    // Muat data array nilai (Atau beri 1 kolom kosong jika belum ada data)
    values: props.dataIndikator.values && props.dataIndikator.values.length > 0 
            ? props.dataIndikator.values.map(v => ({ tahun: v.tahun, nilai: v.nilai }))
            : [{ tahun: String(new Date().getFullYear()), nilai: '' }]
});

// FUNGSI: Menambah KOLOM baru ke samping
const addColumn = () => {
    const lastYear = form.values.length > 0 ? form.values[form.values.length - 1].tahun : new Date().getFullYear();
    // Jika formatnya angka (misal 2024), sarankan tahun berikutnya (2025)
    const nextYear = !isNaN(lastYear) && lastYear.trim() !== '' ? String(parseInt(lastYear) + 1) : '';
    
    form.values.push({ tahun: nextYear, nilai: '' });
};

// FUNGSI: Menghapus KOLOM
const removeColumn = (index) => {
    if (form.values.length > 1) {
        form.values.splice(index, 1);
    } else {
        alert('Minimal harus ada satu kolom data tersisa.');
    }
};

const submit = () => {
    // Validasi Sederhana
    const hasEmptyValues = form.values.some(v => v.tahun === '' || v.nilai === '');
    if (hasEmptyValues) {
        alert("Mohon lengkapi semua baris Waktu dan Nilai pada tabel.");
        return;
    }

    form.put(`/inputer/data/${props.dataIndikator.id_data}`, {
        preserveScroll: true,
        onSuccess: () => {
            // Berhasil, otomatis diarahkan oleh controller
        },
        onError: (errors) => {
            console.log(errors);
            alert('Gagal menyimpan. Periksa kembali form Anda.');
        }
    });
};
</script>

<template>
    <Head title="Edit Master Data" />

    <div class="min-h-screen mx-auto max-w-[95%] py-10">
        <div class="bg-white rounded-[3rem] shadow-xl border border-gray-100 p-8 md:p-12">
            
            <div class="mb-10">
                <h1 class="text-3xl font-black text-[#000B58] tracking-tight uppercase">
                    Edit <span class="text-[#00139E]">Master Data</span>
                </h1>
                <p class="text-gray-400 font-medium mt-2">Perbarui metadata dan edit capaian nilai secara horizontal.</p>
            </div>

            <form @submit.prevent="submit" class="space-y-10">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 bg-gray-50/50 p-8 rounded-[2rem] border border-gray-100">
                    <div class="md:col-span-4 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-500 ml-1">Nama Indikator</label>
                        <input v-model="form.nama_indikator" type="text" 
                            class="w-full bg-white border-gray-200 rounded-xl px-5 py-4 text-sm font-bold text-[#000B58] focus:ring-[#00139E]"
                            :class="{ 'border-red-500': form.errors.nama_indikator }" />
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-500 ml-1">Tema Sektoral</label>
                        <select v-model="form.id_tema" class="w-full bg-white border-gray-200 rounded-xl px-5 py-4 text-sm font-bold text-[#000B58] focus:ring-[#00139E]">
                            <option v-for="t in tema" :key="t.id_tema" :value="t.id_tema">{{ t.nama_tema }}</option>
                        </select>
                    </div>

                    <div class="md:col-span-1 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-500 ml-1">Satuan</label>
                        <input v-model="form.satuan" type="text" class="w-full bg-white border-gray-200 rounded-xl px-5 py-4 text-sm font-bold text-[#000B58] focus:ring-[#00139E]" />
                    </div>

                    <div class="md:col-span-1 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-500 ml-1">Frekuensi</label>
                        <select v-model="form.id_frekuensi" class="w-full bg-emerald-50 border-emerald-200 text-emerald-800 rounded-xl px-5 py-4 text-sm font-bold focus:ring-emerald-500">
                            <option v-for="f in frekuensi" :key="f.id_frekuensi" :value="f.id_frekuensi">{{ f.nama_frekuensi }}</option>
                        </select>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-[2rem] overflow-hidden shadow-sm relative">
                    <div class="bg-[#000B58] p-5 flex flex-wrap justify-between items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-white font-black uppercase tracking-widest text-sm">Capaian Nilai</h3>
                                <p class="text-blue-200 text-[10px] font-medium mt-0.5">Edit nilai atau tambah waktu periode ke samping.</p>
                            </div>
                        </div>
                        
                        <button @click.prevent="addColumn" class="bg-[#00D2FC] text-[#000B58] hover:bg-white px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-colors shadow-lg flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Kolom Data
                        </button>
                    </div>

                    <div class="overflow-x-auto custom-scrollbar bg-white">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr>
                                    <th class="p-4 bg-gray-50 border-b-2 border-gray-200 border-r text-[10px] font-black text-gray-400 uppercase tracking-widest w-48 min-w-[200px] sticky left-0 z-20 shadow-[2px_0_5px_rgba(0,0,0,0.03)]">
                                        Atribut / Periode
                                    </th>
                                    
                                    <th v-for="(item, index) in form.values" :key="'head-'+index" class="p-4 bg-gray-50 border-b-2 border-gray-200 min-w-[220px] relative group border-r border-gray-100">
                                        <label class="text-[9px] font-black text-blue-600 uppercase mb-1 block">Waktu / Bulan / Tahun *</label>
                                        <div class="flex items-center gap-2">
                                            <input v-model="item.tahun" type="text" placeholder="Misal: Jan 2024" class="w-full bg-white border-gray-300 rounded-lg px-3 py-2 text-xs font-bold focus:ring-[#00139E]" required>
                                            
                                            <button @click.prevent="removeColumn(index)" class="p-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-colors flex-shrink-0" title="Hapus Kolom">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="hover:bg-blue-50/30 transition-colors">
                                    <td class="p-4 bg-white border-b border-gray-100 border-r sticky left-0 z-10 shadow-[2px_0_5px_rgba(0,0,0,0.03)]">
                                        <span class="text-xs font-black text-[#000B58] uppercase tracking-wide">Isi Nilai Capaian</span>
                                        <span class="block text-[10px] text-gray-400 mt-1">Gunakan titik (.) untuk desimal</span>
                                    </td>
                                    
                                    <td v-for="(item, index) in form.values" :key="'val-'+index" class="p-4 border-b border-gray-100 border-r">
                                        <input v-model="item.nilai" type="text" placeholder="0.00" class="w-full bg-emerald-50/50 border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 text-sm font-black text-center focus:ring-emerald-500 focus:bg-emerald-50 transition-colors" required>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pt-6 flex items-center justify-end gap-4 border-t border-gray-100">
                    <Link href="/inputer/dashboard" class="px-8 py-4 text-gray-400 font-bold hover:text-gray-600 transition-colors uppercase tracking-widest text-xs">
                        Batal
                    </Link>
                    <button type="submit" :disabled="form.processing"
                        class="bg-[#000B58] text-white px-12 py-4 rounded-xl text-sm font-black uppercase tracking-widest hover:bg-[#00139E] transition-all shadow-xl shadow-blue-900/20 disabled:opacity-50 flex items-center gap-2">
                        <svg v-if="form.processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan Data' }}
                    </button>
                </div>
            </form>

        </div>
    </div>
</template>

<style scoped>
/* Scrollbar Kustom agar elegan saat di-scroll ke samping */
.custom-scrollbar::-webkit-scrollbar { height: 10px; width: 10px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 6px; border: 2px solid #f1f5f9; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>