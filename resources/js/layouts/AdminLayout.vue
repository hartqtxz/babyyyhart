<template>
    <div style="display: flex; min-height: 100vh;">
        <Sidebar />
        <div style="flex: 1; overflow-y: auto;" class="bg-light">
            <div class="p-4" style="min-height: 100vh;">
                <router-view />
            </div>
        </div>
    </div>
</template>

<script setup>
import Sidebar from '../components/admin/SideBar.vue'
</script>