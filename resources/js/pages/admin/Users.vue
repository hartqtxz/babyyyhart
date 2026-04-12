<template>
    <div>
        <h2 class="mb-4">Manage Users</h2>

        <div class="row">
            <div v-for="user in users" :key="user.id" class="col-md-12 mb-3">
                <div class="card">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"  style="width: 60px; height: 60px;">
                                <span class="fs-5">{{ user.name.charAt(0).toUpperCase() }}</span>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-1">{{ user.name }}</h6>
                                <small class="text-muted">{{ user.email }}</small>
                                <br>
                                <small class="text-muted">{{ user.phone }}</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span :class="['badge', user.status === 'active' ? 'bg-success' : 'bg-danger']">
                                {{ user.status }}
                            </span>
                            <button @click="toggleUserStatus(user.id)" :class="['btn', 'btn-sm', user.status === 'active' ? 'btn-warning' : 'btn-success']">
                                {{ user.status === 'active' ? 'Block' : 'Unblock' }}
                            </button>
                            <button @click="deleteUser(user.id)" class="btn btn-sm btn-danger">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../../services/api'

const users = ref([])
const isLoading = ref(false)

onMounted(() => {
    fetchUsers()
})

const fetchUsers = async () => {
    try {
        isLoading.value = true
        const response = await api.get('/users')
        users.value = response.data
    } catch (error) {
        console.error('Error fetching users:', error)
        alert('Failed to fetch users')
    } finally {
        isLoading.value = false
    }
}

const toggleUserStatus = async (userId) => {
    try {
        await api.post(`/users/${userId}/toggle-status`)
        await fetchUsers()
    } catch (error) {
        console.error('Error toggling user status:', error)
        alert('Failed to update user status')
    }
}

const deleteUser = async (userId) => {
    if (confirm('Are you sure you want to delete this user?')) {
        try {
            await api.delete(`/users/${userId}`)
            await fetchUsers()
        } catch (error) {
            console.error('Error deleting user:', error)
            alert('Failed to delete user')
        }
    }
}
</script>
