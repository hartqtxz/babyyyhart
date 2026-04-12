<template>
    <div class="user-profile">
        <!-- Header Section -->
        <div class="page-header">
            <h1>My Profile</h1>
            <p class="header-subtitle">Manage your account settings and personal information</p>
        </div>

        <div class="profile-container">
            <!-- Profile Card -->
            <div class="profile-card">
                <div class="card-header">
                    <div class="profile-avatar-section">
                        <img :src="userAvatar" :alt="userName" class="profile-avatar" />
                        <div class="avatar-overlay">
                            <button class="btn-change-avatar">📷</button>
                        </div>
                    </div>
                    <div class="profile-intro">
                        <h2>{{ userName }}</h2>
                        <p class="profile-email">{{ userEmail }}</p>
                        <p class="profile-joined">Member since {{ memberSince }}</p>
                    </div>
                </div>

                <form @submit.prevent="updateProfile" class="profile-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Full Name *</label>
                            <input 
                                id="name"
                                v-model="profileForm.name" 
                                type="text" 
                                required 
                                class="form-input"
                                placeholder="Enter your full name"
                            />
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input 
                                id="email"
                                v-model="profileForm.email" 
                                type="email" 
                                required 
                                class="form-input"
                                placeholder="Enter your email"
                            />
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input 
                                id="phone"
                                v-model="profileForm.phone" 
                                type="tel" 
                                class="form-input"
                                placeholder="Enter your phone number"
                            />
                        </div>

                        <div class="form-group">
                            <label for="location">Location</label>
                            <input 
                                id="location"
                                v-model="profileForm.location" 
                                type="text" 
                                class="form-input"
                                placeholder="Enter your location"
                            />
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="bio">Bio</label>
                        <textarea 
                            id="bio"
                            v-model="profileForm.bio" 
                            rows="5" 
                            class="form-input"
                            placeholder="Tell employers about yourself, your skills, and experience..."
                        ></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-save">Save Changes</button>
                        <button type="button" @click="resetForm" class="btn-cancel">Cancel</button>
                    </div>

                    <p v-if="successMessage" class="success-message">✓ {{ successMessage }}</p>
                    <p v-if="errorMessage" class="error-message">✕ {{ errorMessage }}</p>
                </form>
            </div>

            <!-- Password Card -->
            <div class="password-card">
                <h3 class="card-title">🔒 Change Password</h3>

                <form @submit.prevent="changePassword" class="password-form">
                    <div class="form-group">
                        <label for="current-password">Current Password</label>
                        <input 
                            id="current-password"
                            v-model="passwordForm.current_password" 
                            type="password" 
                            required 
                            class="form-input"
                            placeholder="Enter your current password"
                        />
                    </div>

                    <div class="form-group">
                        <label for="new-password">New Password</label>
                        <input 
                            id="new-password"
                            v-model="passwordForm.new_password" 
                            type="password" 
                            required 
                            class="form-input"
                            placeholder="Enter your new password"
                        />
                        <p class="password-hint">Password must be at least 8 characters</p>
                    </div>

                    <div class="form-group">
                        <label for="confirm-password">Confirm New Password</label>
                        <input 
                            id="confirm-password"
                            v-model="passwordForm.confirm_password" 
                            type="password" 
                            required 
                            class="form-input"
                            placeholder="Confirm your new password"
                        />
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-save">Update Password</button>
                    </div>

                    <p v-if="passwordSuccessMessage" class="success-message">✓ {{ passwordSuccessMessage }}</p>
                    <p v-if="passwordErrorMessage" class="error-message">✕ {{ passwordErrorMessage }}</p>
                </form>
            </div>
        </div>

        <!-- Account Actions -->
        <div class="account-actions">
            <h3>Account Settings</h3>
            <div class="actions-grid">
                <div class="action-item">
                    <div class="action-icon">🔐</div>
                    <div class="action-content">
                        <p class="action-title">Two-Factor Authentication</p>
                        <p class="action-description">Add an extra layer of security to your account</p>
                    </div>
                    <button class="btn-action">Enable</button>
                </div>

                <div class="action-item">
                    <div class="action-icon">👤</div>
                    <div class="action-content">
                        <p class="action-title">Privacy Settings</p>
                        <p class="action-description">Control who can see your profile</p>
                    </div>
                    <button class="btn-action">Manage</button>
                </div>

                <div class="action-item danger">
                    <div class="action-icon">🗑️</div>
                    <div class="action-content">
                        <p class="action-title">Delete Account</p>
                        <p class="action-description">Permanently delete your account and data</p>
                    </div>
                    <button class="btn-action btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import api from '../../services/api'

const profileForm = reactive({
    name: '',
    email: '',
    phone: '',
    location: '',
    bio: '',
})

const passwordForm = reactive({
    current_password: '',
    new_password: '',
    confirm_password: '',
})

const originalForm = ref(null)
const successMessage = ref('')
const errorMessage = ref('')
const passwordSuccessMessage = ref('')
const passwordErrorMessage = ref('')

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

const memberSince = computed(() => {
    if (!user.value?.created_at) return 'recently'
    return new Date(user.value.created_at).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
    })
})

const userAvatar = computed(() => {
    if (user.value?.avatar) return user.value.avatar
    const initials = userName.value.charAt(0).toUpperCase()
    const bgColor = user.value?.id ? (user.value.id * 123456) % 360 : 0
    return `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="hsl(${bgColor}, 70%, 50%)"/><text x="50" y="50" font-size="50" fill="white" text-anchor="middle" dy=".3em" font-weight="bold">${initials}</text></svg>`
})

const resetForm = () => {
    if (originalForm.value) {
        Object.assign(profileForm, originalForm.value)
    }
}

const updateProfile = async () => {
    successMessage.value = ''
    errorMessage.value = ''

    try {
        await api.put(`/users/${user.value.id}`, profileForm)
        
        // Update localStorage
        const updatedUser = { ...user.value, ...profileForm }
        localStorage.setItem('user', JSON.stringify(updatedUser))
        
        // Update original form
        originalForm.value = { ...profileForm }
        
        successMessage.value = 'Profile updated successfully!'
        setTimeout(() => {
            successMessage.value = ''
        }, 3000)
    } catch (error) {
        errorMessage.value = error.response?.data?.message || 'Failed to update profile'
    }
}

const changePassword = async () => {
    passwordSuccessMessage.value = ''
    passwordErrorMessage.value = ''

    if (passwordForm.new_password.length < 8) {
        passwordErrorMessage.value = 'Password must be at least 8 characters'
        return
    }

    if (passwordForm.new_password !== passwordForm.confirm_password) {
        passwordErrorMessage.value = 'New passwords do not match'
        return
    }

    try {
        await api.post(`/users/${user.value.id}/change-password`, {
            current_password: passwordForm.current_password,
            new_password: passwordForm.new_password,
        })

        passwordSuccessMessage.value = 'Password changed successfully!'
        passwordForm.current_password = ''
        passwordForm.new_password = ''
        passwordForm.confirm_password = ''
        
        setTimeout(() => {
            passwordSuccessMessage.value = ''
        }, 3000)
    } catch (error) {
        if (error.response?.data?.message) {
            passwordErrorMessage.value = error.response.data.message
        } else {
            passwordErrorMessage.value = 'Failed to change password'
        }
    }
}

onMounted(() => {
    // Initialize form with user data
    profileForm.name = user.value?.name || ''
    profileForm.email = user.value?.email || ''
    profileForm.phone = user.value?.phone || ''
    profileForm.location = user.value?.location || ''
    profileForm.bio = user.value?.bio || ''
    
    // Store original form for reset
    originalForm.value = { ...profileForm }
})
</script>

<style scoped>
.user-profile {
    padding: 40px 20px;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
}

.page-header {
    margin-bottom: 40px;
}

.page-header h1 {
    color: #1a202c;
    font-size: 32px;
    margin: 0 0 10px 0;
}

.header-subtitle {
    color: #718096;
    font-size: 16px;
    margin: 0;
}

.profile-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
    margin-bottom: 40px;
    max-width: 1200px;
}

.profile-card,
.password-card {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.card-header {
    display: flex;
    gap: 25px;
    margin-bottom: 30px;
    padding-bottom: 30px;
    border-bottom: 2px solid #e2e8f0;
}

.profile-avatar-section {
    position: relative;
}

.profile-avatar {
    width: 100px;
    height: 100px;
    border-radius: 12px;
    border: 3px solid #3182ce;
    object-fit: cover;
}

.avatar-overlay {
    position: absolute;
    bottom: 0;
    right: 0;
    background: #3182ce;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    opacity: 0;
    transition: opacity 0.3s;
}

.profile-avatar-section:hover .avatar-overlay {
    opacity: 1;
}

.btn-change-avatar {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
}

.profile-intro h2 {
    color: #1a202c;
    margin: 0 0 5px 0;
    font-size: 24px;
}

.profile-email {
    color: #3182ce;
    margin: 0 0 5px 0;
    font-weight: 500;
}

.profile-joined {
    color: #a0aec0;
    margin: 0;
    font-size: 14px;
}

.card-title {
    color: #1a202c;
    margin: 0 0 20px 0;
    font-size: 18px;
}

.profile-form,
.password-form {
    display: flex;
    flex-direction: column;
    gap: 0;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

label {
    color: #2d3748;
    font-weight: 600;
    font-size: 14px;
}

.form-input {
    padding: 12px 16px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
    transition: all 0.3s ease;
    background: #f7fafc;
}

.form-input:focus {
    outline: none;
    border-color: #3182ce;
    background: white;
    box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
}

.form-input::placeholder {
    color: #cbd5e0;
}

textarea.form-input {
    resize: vertical;
    min-height: 120px;
}

.password-hint {
    color: #a0aec0;
    font-size: 12px;
    margin: 4px 0 0 0;
}

.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 25px;
}

.btn-save,
.btn-cancel {
    padding: 12px 30px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
}

.btn-save {
    background: #3182ce;
    color: white;
    flex: 1;
}

.btn-save:hover {
    background: #2c5282;
    transform: translateY(-2px);
}

.btn-cancel {
    background: #e2e8f0;
    color: #2d3748;
    flex: 1;
}

.btn-cancel:hover {
    background: #cbd5e0;
}

.success-message {
    color: #22863a;
    background: #f0f9ff;
    border: 1px solid #dcfce7;
    padding: 12px 16px;
    border-radius: 6px;
    margin-top: 15px;
    font-size: 14px;
}

.error-message {
    color: #7f1d1d;
    background: #fef2f2;
    border: 1px solid #fecaca;
    padding: 12px 16px;
    border-radius: 6px;
    margin-top: 15px;
    font-size: 14px;
}

.account-actions {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    max-width: 1200px;
}

.account-actions h3 {
    color: #1a202c;
    margin: 0 0 25px 0;
    font-size: 20px;
}

.actions-grid {
    display: grid;
    gap: 20px;
}

.action-item {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 20px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    transition: all 0.3s;
}

.action-item:hover {
    border-color: #3182ce;
    background: #ebf8ff;
}

.action-item.danger:hover {
    border-color: #fc8181;
    background: #fff5f5;
}

.action-icon {
    font-size: 32px;
    min-width: 50px;
    text-align: center;
}

.action-content {
    flex: 1;
}

.action-title {
    color: #2d3748;
    font-weight: 600;
    margin: 0;
}

.action-description {
    color: #a0aec0;
    font-size: 14px;
    margin: 4px 0 0 0;
}

.btn-action {
    padding: 8px 20px;
    background: #3182ce;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 14px;
}

.btn-action:hover {
    background: #2c5282;
}

.btn-action.btn-danger {
    background: #fc8181;
}

.btn-action.btn-danger:hover {
    background: #f56565;
}

@media (max-width: 968px) {
    .profile-container {
        grid-template-columns: 1fr;
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .card-header {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
}

@media (max-width: 768px) {
    .user-profile {
        padding: 20px;
    }

    .page-header h1 {
        font-size: 24px;
    }

    .form-actions {
        flex-direction: column;
    }

    .action-item {
        flex-direction: column;
        text-align: center;
    }

    .action-content {
        text-align: center;
    }
}

@media (max-width: 768px) {
    .profile-container {
        grid-template-columns: 1fr;
    }

    .profile-header {
        flex-direction: column;
        text-align: center;
    }
}
</style>
