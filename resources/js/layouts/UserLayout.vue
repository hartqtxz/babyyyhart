<template>
    <div class="user-layout">
        <header class="user-header">
            <div class="header-brand">
                <img :src="logoUrl" alt="Logo" class="logo" />
                <span class="brand-title">JOB PORTAL</span>
            </div>

            <nav class="user-nav">
            </nav>

            <div class="header-actions">
                <button @click="toggleNotifications" class="notification-btn">
                    🔔
                    <span v-if="notificationCount > 0" class="badge">{{ notificationCount }}</span>
                </button>

                <!-- Notifications Dropdown -->
                <div v-if="showNotifications" class="notifications-dropdown">
                    <div class="notifications-header">
                        <h3>Notifications</h3>
                        <button v-if="notifications.length > 0" @click="markAllAsRead" class="mark-read-btn">Mark all as read</button>
                    </div>
                    <div class="notifications-list">
                        <div v-if="notifications.length === 0" class="no-notifications">
                            <p>No notifications</p>
                        </div>
                        <div v-else>
                            <div 
                                v-for="notification in notifications" 
                                :key="notification.id" 
                                class="notification-item"
                                :class="{ unread: !notification.is_read }"
                                @click="markAsRead(notification.id)"
                            >
                                <div class="notification-icon">
                                    <span v-if="notification.type === 'new_application'">📝</span>
                                    <span v-else-if="notification.type === 'application_approved'">✅</span>
                                    <span v-else-if="notification.type === 'application_rejected'">❌</span>
                                    <span v-else>🔔</span>
                                </div>
                                <div class="notification-content">
                                    <p class="notification-message">{{ notification.message }}</p>
                                    <p class="notification-time">{{ formatTime(notification.created_at) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="user-menu">
                    <img :src="userAvatar" :alt="userName" class="user-avatar" @click="toggleUserMenu" />
                    <div v-if="showUserMenu" class="user-dropdown">
                        <div class="user-info">
                            <p class="user-name">{{ userName }}</p>
                            <p class="user-email">{{ userEmail }}</p>
                        </div>
                        <a href="#" @click.prevent="logout" class="logout-link">Logout</a>
                    </div>
                </div>
            </div>
        </header>

        <main class="user-main">
            <router-view />
        </main>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import logoUrl from '../../../public/assets/images/logo.png'
import api from '../services/api'

const router = useRouter()
const route = useRoute()
const showUserMenu = ref(false)
const showNotifications = ref(false)
const notificationCount = ref(0)
const notifications = ref([])

const user = computed(() => {
    const userData = localStorage.getItem('user')
    return userData ? JSON.parse(userData) : null
})

const userName = computed(() => {
    return user.value?.name || 'User'
})

const userEmail = computed(() => {
    return user.value?.email || ''
})

const userAvatar = computed(() => {
    if (user.value?.avatar) return user.value.avatar
    const initials = userName.value.charAt(0).toUpperCase()
    const bgColor = user.value?.id ? (user.value.id * 123456) % 360 : 0
    return `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="hsl(${bgColor}, 70%, 50%)"/><text x="50" y="50" font-size="50" fill="white" text-anchor="middle" dy=".3em" font-weight="bold">${initials}</text></svg>`
})

const isActive = (path) => {
    return route.path === path
}

const toggleUserMenu = () => {
    showUserMenu.value = !showUserMenu.value
}

const toggleNotifications = () => {
    showNotifications.value = !showNotifications.value
    if (showNotifications.value) {
        fetchNotifications()
    }
}

const formatTime = (dateString) => {
    const date = new Date(dateString)
    const now = new Date()
    const diffMs = now - date
    const diffMins = Math.floor(diffMs / 60000)
    const diffHours = Math.floor(diffMs / 3600000)
    const diffDays = Math.floor(diffMs / 86400000)

    if (diffMins < 1) return 'just now'
    if (diffMins < 60) return `${diffMins}m ago`
    if (diffHours < 24) return `${diffHours}h ago`
    if (diffDays < 7) return `${diffDays}d ago`
    return date.toLocaleDateString()
}

const markAsRead = async (notificationId) => {
    try {
        await api.put(`/notifications/${notificationId}`, { is_read: true })
        fetchNotifications()
    } catch (error) {
        console.error('Failed to mark notification as read:', error)
    }
}

const markAllAsRead = async () => {
    try {
        await Promise.all(
            notifications.value
                .filter(n => !n.is_read)
                .map(n => api.put(`/notifications/${n.id}`, { is_read: true }))
        )
        fetchNotifications()
    } catch (error) {
        console.error('Failed to mark all notifications as read:', error)
    }
}

const logout = () => {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    router.push('/login')
}

const fetchNotifications = async () => {
    try {
        const response = await api.get('/notifications')
        notifications.value = response.data
        notificationCount.value = response.data.filter(n => !n.is_read).length
    } catch (error) {
        console.error('Failed to fetch notifications:', error)
    }
}

// Set up interval to fetch notifications every 30 seconds
let notificationInterval = null

onMounted(() => {
    if (!user.value) {
        router.push('/login')
    }
    fetchNotifications()
    
    // Automatically refresh notifications every 30 seconds
    notificationInterval = setInterval(() => {
        fetchNotifications()
    }, 30000)
})

onBeforeUnmount(() => {
    if (notificationInterval) {
        clearInterval(notificationInterval)
    }
})
</script>

<style scoped>
.user-layout {
    min-height: 100vh;
    background: #f5f5f5;
}

.user-header {
    background: linear-gradient(135deg, #003d82 0%, #005aba 100%);
    color: white;
    padding: 12px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    flex-wrap: wrap;
    gap: 20px;
}

.header-brand {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 20px;
    font-weight: 700;
}

.logo {
    height: 45px;
    width: auto;
}

.brand-title {
    letter-spacing: 1px;
}

.user-nav {
    display: flex;
    gap: 20px;
    flex: 1;
    justify-content: center;
}

.nav-link {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    padding: 8px 12px;
    border-radius: 6px;
}

.nav-link:hover {
    color: white;
    background: rgba(255, 255, 255, 0.1);
}

.nav-link.active {
    color: white;
    background: rgba(255, 255, 255, 0.2);
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 20px;
}

.notification-btn {
    position: relative;
    background: none;
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.notification-btn:hover {
    transform: scale(1.1);
}

.badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #ff6b6b;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 600;
}

.user-menu {
    position: relative;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid white;
    transition: all 0.3s ease;
}

.user-avatar:hover {
    transform: scale(1.1);
}

.user-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    min-width: 200px;
    margin-top: 8px;
    z-index: 1000;
    color: #333;
}

.user-info {
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.user-name {
    font-weight: 600;
    margin: 0 0 4px 0;
    color: #003d82;
}

.user-email {
    font-size: 13px;
    color: #999;
    margin: 0;
}

.logout-link {
    display: block;
    padding: 12px 15px;
    color: #e74c3c;
    text-decoration: none;
    border-radius: 0 0 8px 8px;
    transition: all 0.3s ease;
    text-align: center;
}

.logout-link:hover {
    background: #f5f5f5;
}

.user-main {
    max-width: 1400px;
    margin: 0 auto;
}

@media (max-width: 768px) {
    .user-header {
        flex-direction: column;
        gap: 15px;
    }

    .user-nav {
        width: 100%;
        gap: 10px;
    }

    .header-brand {
        width: 100%;
    }

    .header-actions {
        justify-content: center;
    }
}

/* Notifications Dropdown Styles */
.notification-btn {
    background: none;
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    position: relative;
    transition: transform 0.2s;
}

.notification-btn:hover {
    transform: scale(1.1);
}

.badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #f87171;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
}

.notifications-dropdown {
    position: absolute;
    top: 60px;
    right: 10px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    width: 380px;
    max-height: 500px;
    z-index: 1000;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.notifications-header {
    padding: 15px;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.notifications-header h3 {
    margin: 0;
    color: #1f2937;
    font-size: 16px;
    font-weight: 600;
}

.mark-read-btn {
    background: none;
    border: none;
    color: #3b82f6;
    cursor: pointer;
    font-size: 12px;
    font-weight: 600;
    transition: color 0.2s;
}

.mark-read-btn:hover {
    color: #2563eb;
}

.notifications-list {
    overflow-y: auto;
    max-height: 400px;
}

.no-notifications {
    padding: 30px;
    text-align: center;
    color: #9ca3af;
    font-size: 14px;
}

.notification-item {
    display: flex;
    gap: 12px;
    padding: 12px 15px;
    border-bottom: 1px solid #f3f4f6;
    cursor: pointer;
    transition: background 0.2s;
}

.notification-item:hover {
    background: #f9fafb;
}

.notification-item.unread {
    background: #eff6ff;
}

.notification-icon {
    font-size: 24px;
    min-width: 30px;
    text-align: center;
}

.notification-content {
    flex: 1;
}

.notification-message {
    margin: 0 0 4px 0;
    font-size: 13px;
    color: #1f2937;
    line-height: 1.4;
}

.notification-time {
    margin: 0;
    font-size: 12px;
    color: #9ca3af;
}
</style>
