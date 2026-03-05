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
    status: 'aktif',
    tahun: new Date().getFullYear(), // Tahun registrasi master data
    
    // Array untuk Capaian Nilai (Tahun/Nilai) diset mendatar
    values: [
        { tahun: new Date().getFullYear().toString(), nilai: '' } 
    ],
    // Array dinamis untuk Atribut Tambahan
    extra_fields_array: [] 
});

// =======================================================
// FUNGSI UNTUK CAPAIAN NILAI (WAKTU/ANGKA) MENDATAR
// =======================================================
const addValueRow = () => {
    const lastYear = form.values.length > 0 ? form.values[form.values.length - 1].tahun : new Date().getFullYear();
    const nextYear = !isNaN(lastYear) && String(lastYear).trim() !== '' ? String(parseInt(lastYear) + 1) : '';
    form.values.push({ tahun: nextYear, nilai: '' });
};

const removeValueRow = (index) => {
    if (form.values.length > 1) {
        form.values.splice(index, 1);
    } else {
        alert('Minimal harus ada satu kolom Waktu dan Nilai tersisa.');
    }
};

// =======================================================
// FUNGSI UNTUK ATRIBUT TAMBAHAN (JSON)
// =======================================================
const addExtraField = () => {
    form.extra_fields_array.push({ key: '', value: '' });
};

const removeExtraField = (index) => {
    form.extra_fields_array.splice(index, 1);
};

// =======================================================
// SUBMIT DATA (PROSES INSERT BARU)
// =======================================================
const submitData = () => {
    // Validasi Waktu/Nilai
    const hasEmptyValues = form.values.some(v => v.tahun === '' || String(v.nilai).trim() === '');
    if (hasEmptyValues) {
        alert("Mohon lengkapi semua kolom Waktu dan Nilai yang telah Anda tambahkan.");
        return;
    }

    // Validasi Atribut Tambahan
    const hasEmptyExtras = form.extra_fields_array.some(e => e.key.trim() === '');
    if (hasEmptyExtras) {
        alert("Nama Atribut Tambahan tidak boleh kosong.");
        return;
    }

    // Format array ke object untuk backend
    let formattedExtraFields = {};
    form.extra_fields_array.forEach(item => {
        if (item.key.trim() !== '') {
            formattedExtraFields[item.key.trim()] = item.value.trim();
        }
    });

    isLoading.value = true;
    
    // form.transform mengubah data sebelum dikirim
    form.transform((data) => ({
        ...data,
        extra_fields: formattedExtraFields 
    })).post('/inputer/data/store-single', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            alert('Data indikator baru berhasil disimpan!');
            router.visit('/inputer/dashboard');
        },
        onError: (errors) => {
            console.log(errors);
            alert('Gagal menyimpan. Periksa kembali inputan Anda.');
        },
        onFinish: () => isLoading.value = false
    });
};
</script>

<template>
    <Head title="Input Master Data" />

    <div class="mx-auto">
        <div class="bg-white rounded-[2.5rem] border border-gray-400 p-8 md:p-12">
            
            <div class="mb-10">
                <h1 class="text-3xl font-black text-[#000B58] tracking-tight uppercase">
                    Input <span class="text-[#00139E]">Master Data</span>
                </h1>
                <p class="text-gray-400 font-medium mt-2">Daftarkan metadata indikator baru dan isi capaian nilainya secara horizontal.</p>
            </div>

            <form @submit.prevent="submitData" class="space-y-10">
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 bg-gray-50/50 p-8 rounded-[2rem] border border-gray-100 relative">
                    <div class="md:col-span-4 mb-2">
                        <h3 class="text-[11px] font-black text-[#00139E] uppercase tracking-[0.3em] flex items-center gap-3">
                            <span class="w-8 h-[2px] bg-[#00139E]"></span>
                            Informasi Utama Data
                        </h3>
                    </div>

                    <div class="md:col-span-3 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Nama Indikator *</label>
                        <input v-model="form.nama_data" type="text" placeholder="Contoh: Jumlah Penduduk Miskin"
                            class="w-full bg-white border-gray-200 rounded-xl px-5 py-4 text-sm font-bold text-[#000B58] focus:ring-[#00139E]" 
                            :class="{'border-red-500': form.errors.nama_data}" />
                        <p v-if="form.errors.nama_data" class="text-red-500 text-xs ml-1 font-bold">{{ form.errors.nama_data }}</p>
                    </div>

                    <div class="md:col-span-1 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Status *</label>
                        <select v-model="form.status" class="w-full bg-white border-gray-200 rounded-xl px-5 py-4 text-sm font-bold text-[#000B58]">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>

                    <div class="md:col-span-1 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Tema Sektoral *</label>
                        <select v-model="form.id_tema" class="w-full bg-white border-gray-200 rounded-xl px-5 py-4 text-sm font-bold text-[#000B58]" required>
                            <option value="">Pilih Tema...</option>
                            <option v-for="t in tema" :key="t.id_tema" :value="t.id_tema">{{ t.nama_tema }}</option>
                        </select>
                    </div>

                    <div class="md:col-span-1 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Urusan *</label>
                        <select v-model="form.id_urusan" class="w-full bg-white border-gray-200 rounded-xl px-5 py-4 text-sm font-bold text-[#000B58]" required>
                            <option value="">Pilih Urusan...</option>
                            <option v-for="u in urusan" :key="u.id_urusan" :value="u.id_urusan">{{ u.nama_urusan }}</option>
                        </select>
                    </div>

                    <div class="md:col-span-1 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Bidang *</label>
                        <select v-model="form.id_bidang" class="w-full bg-white border-gray-200 rounded-xl px-5 py-4 text-sm font-bold text-[#000B58]" required>
                            <option value="">Pilih Bidang...</option>
                            <option v-for="b in bidang" :key="b.id_bidang" :value="b.id_bidang">{{ b.nama_bidang }}</option>
                        </select>
                    </div>

                    <div class="md:col-span-1 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Frekuensi Update *</label>
                        <select v-model="form.id_frekuensi" class="w-full bg-emerald-50 border-emerald-200 text-emerald-800 rounded-xl px-5 py-4 text-sm font-bold" required>
                            <option v-for="f in frekuensi" :key="f.id_frekuensi" :value="f.id_frekuensi">{{ f.nama_frekuensi }}</option>
                        </select>
                    </div>

                    <div class="md:col-span-1 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Satuan *</label>
                        <input v-model="form.satuan" type="text" placeholder="Cth: Jiwa, %, Km" class="w-full bg-white border-gray-200 rounded-xl px-5 py-4 text-sm font-bold text-[#000B58]" required />
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Sumber Data *</label>
                        <input v-model="form.sumber" type="text" placeholder="Instansi/Dinas" class="w-full bg-white border-gray-200 rounded-xl px-5 py-4 text-sm font-bold text-[#000B58]" required />
                    </div>

                    <div class="md:col-span-1 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Tahun Terbit</label>
                        <input v-model="form.tahun" type="number" class="w-full bg-white border-gray-200 rounded-xl px-5 py-4 text-sm font-bold text-[#000B58]" />
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Deskripsi Singkat</label>
                        <textarea v-model="form.deskripsi" rows="2" placeholder="Penjelasan mengenai indikator..." class="w-full bg-white border-gray-200 rounded-xl px-5 py-4 text-sm font-medium text-[#000B58]"></textarea>
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Kata Kunci (Tag)</label>
                        <textarea v-model="form.kata_kunci" rows="2" placeholder="Cth: ekonomi, bps (pisahkan dengan koma)" class="w-full bg-white border-gray-200 rounded-xl px-5 py-4 text-sm font-medium text-[#000B58] italic"></textarea>
                    </div>

                    <div class="md:col-span-4 border-t border-gray-200 mt-4 pt-6">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-[10px] font-black uppercase text-[#000B58] tracking-widest flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#00139E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                                Atribut Tambahan (Opsional)
                            </h4>
                            <button @click.prevent="addExtraField" class="bg-white text-[#00139E] border border-blue-200 hover:bg-blue-50 px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all shadow-sm">
                                + Tambah Field
                            </button>
                        </div>

                        <div v-if="form.extra_fields_array.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <TransitionGroup name="list" tag="div" class="contents">
                                <div v-for="(field, index) in form.extra_fields_array" :key="index" class="bg-white p-4 rounded-xl border border-gray-200 relative group flex flex-col gap-3">
                                    <div class="space-y-1">
                                        <label class="text-[9px] font-black uppercase tracking-widest text-gray-400 ml-1">Kunci (Nama Field)</label>
                                        <input v-model="field.key" type="text" placeholder="Cth: IUP" class="w-full bg-gray-50 border-gray-200 rounded-lg px-3 py-2 focus:ring-[#00139E] font-bold text-xs" required />
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[9px] font-black uppercase tracking-widest text-gray-400 ml-1">Nilai (Isi Keterangan)</label>
                                        <input v-model="field.value" type="text" placeholder="Isi..." class="w-full bg-gray-50 border-gray-200 rounded-lg px-3 py-2 focus:ring-[#00139E] text-xs" />
                                    </div>
                                    <button @click.prevent="removeExtraField(index)" class="absolute top-2 right-2 text-red-300 hover:text-red-500 bg-red-50 p-1 rounded-md transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </TransitionGroup>
                        </div>
                        <p v-else class="text-[10px] text-gray-400 italic">Belum ada atribut tambahan yang dibuat.</p>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-[2rem] overflow-hidden shadow-sm relative">
                    <div class="bg-[#000B58] p-5 flex flex-wrap justify-between items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-white font-black uppercase tracking-widest text-sm">Isi Capaian Nilai</h3>
                                <p class="text-blue-200 text-[10px] font-medium mt-0.5">Tambah kolom waktu periode ke samping.</p>
                            </div>
                        </div>
                        <button @click.prevent="addValueRow" class="bg-[#00D2FC] text-[#000B58] hover:bg-white px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-colors shadow-lg flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Kolom Waktu
                        </button>
                    </div>

                    <div class="overflow-x-auto custom-scrollbar bg-white">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr>
                                    <th class="p-4 bg-gray-50 border-b border-gray-200 border-r text-[10px] font-black text-gray-400 uppercase tracking-widest w-48 min-w-[200px] sticky left-0 z-20 shadow-[2px_0_5px_rgba(0,0,0,0.03)]">
                                        Atribut / Periode
                                    </th>
                                    <th v-for="(item, index) in form.values" :key="'head-'+index" class="p-4 bg-white border-b border-gray-200 min-w-[220px] border-r border-gray-100">
                                        <div class="flex items-center justify-between mb-1">
                                            <label class="text-[9px] font-black text-blue-600 uppercase">Waktu / Bulan / Tahun *</label>
                                            <button @click.prevent="removeValueRow(index)" class="text-red-400 hover:text-red-600 transition-colors bg-red-50 p-1.5 rounded-md">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                        <input v-model="item.tahun" type="text" placeholder="Misal: 2024" class="w-full bg-gray-50 border-gray-200 rounded-lg px-3 py-2.5 text-xs font-bold focus:ring-[#00139E]" required>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="p-4 bg-white border-b border-gray-100 border-r sticky left-0 z-10 shadow-[2px_0_5px_rgba(0,0,0,0.03)]">
                                        <span class="text-xs font-black text-[#000B58] uppercase tracking-wide">Isi Nilai Angka</span>
                                        <span class="block text-[10px] text-gray-400 mt-1">Gunakan (.) untuk desimal</span>
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
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Indikator Baru' }}
                    </button>
                </div>

            </form>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { height: 10px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 6px; border: 2px solid #f1f5f9; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

.list-enter-active, .list-leave-active { transition: all 0.3s ease; }
.list-enter-from, .list-leave-to { opacity: 0; transform: translateY(-10px); }
</style>