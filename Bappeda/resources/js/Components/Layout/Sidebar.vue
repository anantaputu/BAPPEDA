<script setup>
import { Link } from '@inertiajs/vue3';
import SidebarItem from './SidebarItem.vue';
import IconifyIcon from '@/Components/Base/IconifyIcon.vue';

defineProps({
    role: String,
    menuGroups: Array,
    activeUrl: String,
    openMasterData: Boolean,
    logoPath: String,
    isSidebarOpen: Boolean,
});

defineEmits(['toggleMasterData', 'logout', 'toggleSidebar']);
</script>

<template>
    <aside 
        :class="[isSidebarOpen ? 'w-[20rem]' : 'w-0 opacity-0 -translate-x-[150%]']"
        class="fixed left-0 top-0 z-40 flex h-full flex-col border-r border-gray-400 bg-white p-8 transition-all duration-500 ease-in-out"
    >
        <button 
            @click="$emit('toggleSidebar')"
            class="absolute top-1/2 -right-5 transform -translate-y-1/2 w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center hover:bg-secondary transition-all duration-300 group z-50"
            :class="{'fixed left-8': !isSidebarOpen}"
        >
            <IconifyIcon 
                icon="solar:double-alt-arrow-left-bold"
                width="20"
                height="20"
                class="w-5 h-5 transition-transform duration-500"
                :class="{'rotate-180': !isSidebarOpen}"
            />
        </button>

        <div class="flex h-full flex-col">
            <Link class="mb-10 flex items-center gap-4" href="/">
                    <div>
                        <img :src="logoPath" alt="Logo" class="w-14 h-14 object-contain">
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[1.25rem] font-black leading-none tracking-tight uppercase text-primary">
                            DATA<span class="text-secondary">BAPPEDA</span>
                        </span>
                        <span class="ui-eyebrow mt-2">NTB Terintegrasi</span>
                    </div>
            </Link>

            <div class="no-scrollbar flex-1 space-y-8 overflow-y-auto">
                <div v-for="group in menuGroups" :key="group.label">
                    <p v-if="group.label" class="ui-eyebrow mb-4 ml-4 opacity-60">
                        {{ group.label }}
                    </p>
                    <div class="space-y-2 px-2">
                        <SidebarItem 
                            v-for="item in group.items" :key="item.name"
                            :item="item"
                            :isOpen="item.name === 'Master Data' ? openMasterData : false"
                            :activeUrl="activeUrl"
                            @toggle="$emit('toggleMasterData')"
                        />
                    </div>
                </div>
            </div>

            <div class="mt-auto border-t border-bgsoft pt-6">
                <button @click="$emit('logout')" 
                    class="ui-eyebrow group flex w-full items-center gap-4 px-4 py-4 text-left text-textsecondary transition-all duration-300 hover:bg-integritas/5 hover:text-integritas"
                    style="border-radius: var(--radius-soft);">
                    <IconifyIcon icon="solar:logout-3-bold" width="20" height="20" class="transition-transform group-hover:-translate-x-1" />
                    Keluar Sesi
                </button>
            </div>
        </div>
    </aside>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
