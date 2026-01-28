<script setup>
import { useForm, Head } from '@inertiajs/vue3'

defineProps({
    tema: Array,
    urusan: Array,
    bidang: Array,
    frekuensi: Array,
})

const form = useForm({
    nama_indikator: '',
    deskripsi: '',
    id_tema: '',
    id_urusan: '',
    id_bidang: '',
    id_frekuensi: '',
    kata_kunci: '',
    satuan: '',
    sumber: '',
    status: ''
})

const submit = () => {
    form.post(route('data.store'))
}
</script>

<template>
    <Head title="Input Data" />

    <div class="max-w-5xl mx-auto p-8 grid md:grid-cols-3 gap-6">
        <!-- FORM -->
        <div class="md:col-span-2 bg-white p-6 rounded shadow">
            <h1 class="text-xl font-bold mb-6">Input Data Indikator</h1>

            <div class="space-y-4">
                <input v-model="form.nama_indikator" type="text" placeholder="Nama Data / Indikator"
                    class="w-full border rounded px-3 py-2" />

                <textarea v-model="form.deskripsi" placeholder="Deskripsi"
                    class="w-full border rounded px-3 py-2"></textarea>

                <select v-model="form.id_tema" class="w-full border rounded px-3 py-2">
                    <option value="">Pilih Tema</option>
                    <option v-for="t in tema" :key="t.id_tema" :value="t.id_tema">
                        {{ t.nama_tema }}
                    </option>
                </select>

                <select v-model="form.id_urusan" class="w-full border rounded px-3 py-2">
                    <option value="">Pilih Urusan</option>
                    <option v-for="u in urusan" :key="u.id_urusan" :value="u.id_urusan">
                        {{ u.nama_urusan }}
                    </option>
                </select>

                <select v-model="form.id_bidang" class="w-full border rounded px-3 py-2">
                    <option value="">Pilih Bidang</option>
                    <option v-for="b in bidang" :key="b.id_bidang" :value="b.id_bidang">
                        {{ b.nama_bidang }}
                    </option>
                </select>

                <select v-model="form.id_frekuensi" class="w-full border rounded px-3 py-2">
                    <option value="">Pilih Frekuensi</option>
                    <option v-for="f in frekuensi" :key="f.id_frekuensi" :value="f.id_frekuensi">
                        {{ f.nama_frekuensi }}
                    </option>
                </select>

                <input v-model="form.kata_kunci" type="text" placeholder="Kata Kunci"
                    class="w-full border rounded px-3 py-2" />

                <input v-model="form.satuan" type="text" placeholder="Satuan"
                    class="w-full border rounded px-3 py-2" />

                <input v-model="form.sumber" type="text" placeholder="Sumber"
                    class="w-full border rounded px-3 py-2" />

                <input v-model="form.status" type="text" placeholder="Status"
                    class="w-full border rounded px-3 py-2" />

                <button @click="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    SIMPAN
                </button>
            </div>
        </div>

        <!-- KETERANGAN -->
        <div class="bg-green-50 p-5 rounded text-sm leading-relaxed">
            <strong>Keterangan:</strong>
            <ol class="list-decimal ml-4 mt-2 space-y-1">
                <li>Nama Data: nama indikator</li>
                <li>Deskripsi: penjelasan data</li>
                <li>Tema: SDGs, RPJMD, dll</li>
                <li>Urusan: Pendidikan, Kesehatan, dll</li>
                <li>Bidang: IK, PSDA, P2M</li>
                <li>Kata kunci: pencarian</li>
                <li>Satuan: %, orang, km</li>
                <li>Frekuensi: tahunan, bulanan</li>
                <li>Sumber: OPD / Instansi</li>
                <li>Status: aktif / nonaktif</li>
            </ol>
        </div>
    </div>
</template>
