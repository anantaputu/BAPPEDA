<script setup>
import { useForm } from '@inertiajs/vue3'

defineProps({
    tema: Array,
    urusan: Array,
    bidang: Array,
    frekuensi: Array
})

const form = useForm({
    nama_indikator: '',
    deskripsi: '',
    id_tema: '',
    id_urusan: '',
    id_bidang: '',
    kata_kunci: '',
    satuan: '',
    id_frekuensi: '',
    sumber: '',
})
</script>

<template>
    <div class="p-6 max-w-5xl grid grid-cols-2 gap-8">
        <!-- FORM -->
        <form @submit.prevent="form.post(route('data.store'))" class="space-y-4">

            <div>
                <label>Nama Data</label>
                <input v-model="form.nama_indikator" class="input" />
            </div>

            <div>
                <label>Deskripsi</label>
                <textarea v-model="form.deskripsi" class="input h-24"></textarea>
            </div>

            <div>
                <label>Tema</label>
                <select v-model="form.id_tema" class="input">
                    <option value="">-- Pilih Tema --</option>
                    <option v-for="t in tema" :value="t.id_tema">{{ t.nama_tema }}</option>
                </select>
            </div>

            <div>
                <label>Urusan</label>
                <select v-model="form.id_urusan" class="input">
                    <option value="">-- Pilih Urusan --</option>
                    <option v-for="u in urusan" :value="u.id_urusan">{{ u.nama_urusan }}</option>
                </select>
            </div>

            <div>
                <label>Bidang</label>
                <select v-model="form.id_bidang" class="input">
                    <option value="">-- Pilih Bidang --</option>
                    <option v-for="b in bidang" :value="b.id_bidang">{{ b.nama_bidang }}</option>
                </select>
            </div>

            <div>
                <label>Kata Kunci</label>
                <input v-model="form.kata_kunci" class="input" />
            </div>

            <div>
                <label>Satuan</label>
                <input v-model="form.satuan" class="input" />
            </div>

            <div>
                <label>Frekuensi</label>
                <select v-model="form.id_frekuensi" class="input">
                    <option value="">-- Pilih Frekuensi --</option>
                    <option v-for="f in frekuensi" :value="f.id_frekuensi">
                        {{ f.nama_frekuensi }}
                    </option>
                </select>
            </div>

            <div>
                <label>Sumber</label>
                <input v-model="form.sumber" class="input" />
            </div>

            <button class="bg-blue-600 text-white px-6 py-2 rounded">
                SIMPAN
            </button>
        </form>

        <!-- KETERANGAN -->
        <div class="bg-green-50 p-6 rounded text-sm leading-relaxed">
            <h3 class="font-bold mb-2">KETERANGAN</h3>
            <ol class="list-decimal ml-4 space-y-1">
                <li>Nama Data: nama indikator</li>
                <li>Deskripsi: penjelasan indikator</li>
                <li>Tema: metadata (SDGs, RPJMD, dll)</li>
                <li>Urusan: bidang urusan pemerintahan</li>
                <li>Bidang: IK, PSDA, P2M</li>
                <li>Kata kunci: untuk pencarian</li>
                <li>Satuan: unit data</li>
                <li>Frekuensi: periode data</li>
                <li>Sumber: asal data</li>
            </ol>
        </div>
    </div>
</template>

<style>
.input {
    @apply w-full border rounded px-3 py-2;
}
</style>
