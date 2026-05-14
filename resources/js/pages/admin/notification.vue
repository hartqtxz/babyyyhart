<template>
    <div>
        <h2 class="mb-4">Notifications</h2>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Recent Notifications</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <div v-if="notifications.length === 0" class="p-3 text-center text-muted">
                            No notifications yet
                        </div>
                        <div v-for="notif in notifications" :key="notif.id" class="list-group-item d-flex justify-content-between align-items-start p-3" :style="{ background: notif.is_read ? 'white' : '#f8f9fa' }">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="fs-5">
                                        <i class="bi bi-bell"></i>
                                    </span>
                                    <h6 class="mb-0">{{ notif.title }}</h6>
                                </div>
                                <p class="mb-1">{{ notif.message }}</p>
                                <small class="text-secondary">{{ formatDate(notif.created_at) }}</small>
                            </div>
                            <div class="d-flex gap-2">
                                <span :class="['badge', getNotificationColor(notif.type)]">
                                    {{ notif.type }}
                                </span>
                                <div class="btn-group-vertical">
                                    <button v-if="!notif.is_read" @click="markAsRead(notif.id)" class="btn btn-sm btn-primary">Mark as read</button>
                                    <button @click="deleteNotification(notif.id)" class="btn btn-sm btn-danger">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-header">
                        <h5 class="mb-0">Notification Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="badge bg-info">Total: {{ notifications.length }}</span>
                        </div>
                        <div class="mb-3">
                            <span class="badge bg-warning">Unread: {{ unreadCount }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../../services/api'

const notifications = ref([])
const isLoading = ref(false)

const unreadCount = computed(() => {
    return notifications.value.filter(n => !n.is_read).length
})

onMounted(() => {
    fetchNotifications()
})

const fetchNotifications = async () => {
    try {
        isLoading.value = true
        console.log('Fetching notifications for authenticated user...')
        const response = await api.get('/notifications')
        console.log('Notifications response:', response.data)
        notifications.value = response.data
        if (response.data.length === 0) {
            console.warn('No notifications returned from API')
        }
    } catch (error) {
        console.error('Error fetching notifications:', error)
        if (error.response) {
            console.error('API Error:', error.response.status, error.response.data)
        }
        alert('Failed to fetch notifications. Check console for details.')
    } finally {
        isLoading.value = false
    }
}

const getNotificationColor = (type) => {
    switch(type) {
        case 'new_application': return 'bg-info'
        case 'application_approved': return 'bg-success'
        case 'application_rejected': return 'bg-danger'
        case 'Application': return 'bg-info'
        case 'Success': return 'bg-success'
        case 'Message': return 'bg-primary'
        case 'Alert': return 'bg-warning'
        case 'Warning': return 'bg-danger'
        default: return 'bg-secondary'
    }
}

const formatDate = (date) => {
    const now = new Date()
    const notifDate = new Date(date)
    const diff = now - notifDate
    const days = Math.floor(diff / (1000 * 60 * 60 * 24))
    const hours = Math.floor(diff / (1000 * 60 * 60))
    const minutes = Math.floor(diff / (1000 * 60))

    if (minutes < 60) return `${minutes} minutes ago`
    if (hours < 24) return `${hours} hours ago`
    if (days < 7) return `${days} days ago`
    return notifDate.toLocaleDateString()
}

const markAsRead = async (notificationId) => {
    try {
        await api.put(`/notifications/${notificationId}`, { is_read: true })
        await fetchNotifications()
    } catch (error) {
        console.error('Error marking notification as read:', error)
        alert('Failed to update notification')
    }
}

const deleteNotification = async (notificationId) => {
    try {
        await api.delete(`/notifications/${notificationId}`)
        await fetchNotifications()
    } catch (error) {
        console.error('Error deleting notification:', error)
        alert('Failed to delete notification')
    }
}
</script>
