<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineOptions({ layout: AppLayout });

defineProps({
    contacts: Array,
    pagination: Object,
});
</script>

<template>
    <Head title="Masukan Non-User" />

    <div class="min-h-full">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-5">
            <div>
                <h1 class="text-2xl font-black text-primary uppercase tracking-tight">
                    Masukan <span class="text-[#00139E]">Non-User</span>
                </h1>
                <p class="text-sm text-textsecondary font-medium">
                    Total Masukan: {{ pagination.total || 0 }}
                </p>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-400 bg-white shadow-sm">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-bgsoft text-left text-[10px] font-black text-textsecondary uppercase tracking-[0.2em] border-b border-gray-400">
                        <th class="p-8">Nama</th>
                        <th class="p-8">Pesan</th>
                        <th class="p-8 text-right">Tanggal</th>
                    </tr>
                </thead>

                <tbody class="text-sm font-bold text-primary">
                    <tr
                        v-for="contact in contacts"
                        :key="contact.id"
                        class="border-b border-gray-100 last:border-0 hover:bg-bgsoft/50 transition-all"
                    >
                        <td class="p-8 align-top">
                            <p class="text-md text-primary font-black uppercase tracking-tight">
                                {{ contact.name }}
                            </p>
                        </td>
                        <td class="p-8 align-top">
                            <p class="text-sm text-textsecondary font-medium leading-relaxed">
                                {{ contact.message }}
                            </p>
                        </td>
                        <td class="p-8 align-top text-right">
                            <p class="text-[11px] text-textsecondary font-bold uppercase tracking-widest">
                                {{ new Date(contact.created_at).toLocaleString('id-ID') }}
                            </p>
                        </td>
                    </tr>

                    <tr v-if="contacts.length === 0">
                        <td colspan="3" class="p-12 text-center text-[11px] font-black uppercase tracking-[0.2em] text-textsecondary/60">
                            Belum ada masukan dari non-user
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="pagination.links?.length > 3" class="mt-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <p class="text-[10px] font-black text-textsecondary uppercase tracking-widest">
                Menampilkan {{ pagination.from || 0 }} - {{ pagination.to || 0 }} dari {{ pagination.total || 0 }} Masukan
            </p>
            <div class="flex gap-2">
                <template v-for="(link, k) in pagination.links" :key="k">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        v-html="link.label"
                        class="px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all border"
                        :class="link.active
                            ? 'bg-primary text-white border-transparent shadow-lg shadow-primary/20 scale-110'
                            : 'bg-white text-textsecondary border-gray-200 hover:border-secondary hover:text-secondary hover:shadow-md'"
                    />
                </template>
            </div>
        </div>
    </div>
</template>
