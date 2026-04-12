<template>
    <div class="sidebar bg-primary text-white d-flex flex-column" style="width: 280px; min-height: 100vh; padding: 0;">
        <!-- Logo Section -->
        <div class="logo-section p-4 text-center" style="border-bottom: 1px solid rgba(255,255,255,0.2);">
            <div class="logo-container mb-3">
                <div class="logo-circle">
                    <img :src="logoUrl" alt="Job Portal Logo" style="width: 50px; height: auto;">
                </div>
            </div>
            <h5 class="mb-0 fw-bold">JOB PORTAL</h5>
        </div>

        <!-- Navigation Menu -->
        <nav class="d-flex flex-column gap-1 p-2 flex-grow-1" style="overflow-y: auto;">
            <router-link to="/admin/dashboard" class="btn btn-light w-100 text-start">
                <span class="me-2">📊</span> Dashboard
            </router-link>

            <router-link to="/admin/manage-jobs" class="btn btn-info w-100 text-start">
                <span class="me-2">💼</span> Manage Jobs
            </router-link>

            <router-link to="/admin/manage-applicants" class="btn btn-info w-100 text-start">
                <span class="me-2">👥</span> Manage Applicants
            </router-link>

            <router-link to="/admin/users" class="btn btn-info w-100 text-start">
                <span class="me-2">🧑</span> Manage Users
            </router-link>

            <router-link to="/admin/notification" class="btn btn-info w-100 text-start">
                <span class="me-2">🔔</span> Notifications
            </router-link>
        </nav>

        <!-- Profile Section -->
        <div class="profile-section p-3" style="border-top: 1px solid rgba(255,255,255,0.2);">
            <div class="d-flex align-items-center justify-content-between mb-3 p-2 rounded" style="background: rgba(255,255,255,0.1);">
                <div class="d-flex align-items-center gap-2 flex-grow-1">
                    <div class="profile-avatar rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: rgba(255,255,255,0.25); font-weight: bold;">
                        {{ userInitial }}
                    </div>
                    <div style="min-width: 0; flex: 1;">
                        <div class="text-white fw-bold" style="font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ currentUser?.name }}</div>
                        <small class="text-white" style="opacity: 0.8; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ currentUser?.email }}</small>
                    </div>
                </div>
            </div>
            <button @click="logout" class="btn btn-danger w-100 text-white fw-bold">
                <span class="me-2">🚪</span> Sign Out
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../../services/api'

const router = useRouter()
const currentUser = ref(null)
const logoUrl = '/assets/images/logo.png'

const userInitial = computed(() => {
    if (currentUser.value?.name) {
        return currentUser.value.name.charAt(0).toUpperCase()
    }
    return 'U'
})

onMounted(() => {
    const userString = localStorage.getItem('user')
    if (userString) {
        currentUser.value = JSON.parse(userString)
    }
})

const logout = async () => {
    try {
        // Remove auth data
        localStorage.removeItem('user')
        localStorage.removeItem('token')
        
        // Redirect to login
        router.push('/login')
    } catch (error) {
        console.error('Logout error:', error)
        localStorage.removeItem('user')
        localStorage.removeItem('token')
        router.push('/login')
    }
}
</script>

<style scoped>
.sidebar {
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
}

.logo-section {
    flex-shrink: 0;
}

.logo-container {
    display: flex;
    justify-content: center;
}

.logo-circle {
    width: 70px;
    height: 70px;
    background-color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
}

.logo-circle img {
    width: 50px;
    height: auto;
}

nav {
    flex-grow: 1;
    overflow-y: auto;
}

.profile-section {
    flex-shrink: 0;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.btn {
    font-size: 16px;
    padding: 10px 15px;
    border-radius: 6px;
    border: none;
    transition: all 0.3s ease;
}

.btn-light {
    background-color: #ffffff;
    color: #0d47a1;
    font-weight: 500;
}

.btn-light:hover {
    background-color: #e3f2fd;
    transform: translateX(5px);
    color: #0d47a1;
}

.btn-info {
    background-color: rgba(255, 255, 255, 0.15);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.2);
    font-weight: 500;
}

.btn-info:hover {
    background-color: rgba(255, 255, 255, 0.25);
    transform: translateX(5px);
    color: #ffffff;
}

.btn-danger {
    background-color: #dc3545;
    border: none;
    font-size: 14px;
    padding: 8px 12px;
}

.btn-danger:hover {
    background-color: #c82333;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
}

.profile-avatar {
    flex-shrink: 0;
    font-size: 18px;
    color: white;
}
</style>