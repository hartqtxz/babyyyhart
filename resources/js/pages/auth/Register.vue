<template>
    <div class="register-container">
        <div class="auth-card">
            <!-- Logo -->
            <div class="logo-section">
                <div class="logo-circle">
                    <img :src="logoUrl" alt="Job Portal Logo" class="logo-img">
                </div>
            </div>

            <!-- Title -->
            <h1 class="auth-title">Register</h1>

            <!-- Form -->
            <form @submit.prevent="handleRegister" class="auth-form">
                <div class="form-group">
                    <input 
                        v-model="form.name" 
                        type="text" 
                        placeholder="Full Name"
                        class="form-input"
                        required
                    />
                </div>

                <div class="form-group">
                    <input 
                        v-model="form.email" 
                        type="email" 
                        placeholder="Email"
                        class="form-input"
                        required
                    />
                </div>

                <div class="form-group">
                    <input 
                        v-model="form.phone" 
                        type="tel" 
                        placeholder="Phone Number"
                        class="form-input"
                    />
                </div>

                <div class="form-group password-group">
                    <input 
                        v-model="form.password" 
                        :type="showPassword ? 'text' : 'password'"
                        placeholder="Password"
                        class="form-input"
                        required
                    />
                    <button 
                        type="button" 
                        @click="showPassword = !showPassword"
                        class="toggle-password"
                    >
                        <i style="font-size: 18px;">👁️</i>
                    </button>
                </div>

                <div class="form-group password-group">
                    <input 
                        v-model="form.password_confirmation" 
                        :type="showConfirmPassword ? 'text' : 'password'"
                        placeholder="Confirm Password"
                        class="form-input"
                        required
                    />
                    <button 
                        type="button" 
                        @click="showConfirmPassword = !showConfirmPassword"
                        class="toggle-password"
                    >
                        <i style="font-size: 18px;">👁️</i>
                    </button>
                </div>

                <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
                <p v-if="successMessage" class="success-message">{{ successMessage }}</p>

                <button type="submit" class="auth-btn">Register</button>
            </form>

            <!-- Login Link -->
            <p class="auth-link">
                Already have an account? <router-link to="/login">Login here</router-link>
            </p>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../../services/api'
import logoUrl from '../../../../public/assets/images/logo.png'

const router = useRouter()
const showPassword = ref(false)
const showConfirmPassword = ref(false)

const form = reactive({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
})

const errorMessage = ref('')
const successMessage = ref('')

const handleRegister = async () => {
    errorMessage.value = ''
    successMessage.value = ''

    // Validate email domain - both @portal.com and @gmail.com allowed
    const allowedDomains = ['@portal.com', '@gmail.com']
    const isValidDomain = allowedDomains.some(domain => form.email.endsWith(domain))
    
    if (!isValidDomain) {
        errorMessage.value = 'Only @portal.com (admin) or @gmail.com (user) email addresses are allowed'
        return
    }

    // Validate password match
    if (form.password !== form.password_confirmation) {
        errorMessage.value = 'Passwords do not match'
        return
    }

    try {
        await api.post('/auth/register', form)

        successMessage.value = 'Registered successfully! Redirecting to login...'

        setTimeout(() => {
            router.push('/login')
        }, 1500)
    } catch (error) {
        if (error.response?.data?.errors) {
            const errors = error.response.data.errors
            errorMessage.value = Object.values(errors).flat().join(' ')
        } else {
            errorMessage.value = 'Registration failed'
        }
    }
}
</script>

<style scoped>
.register-container {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(135deg, #5a5a5a 0%, #3a3a3a 100%);
    padding: 20px;
}

.auth-card {
    width: 100%;
    max-width: 450px;
    background: rgba(60, 60, 60, 0.8);
    backdrop-filter: blur(10px);
    padding: 50px 40px;
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.logo-section {
    display: flex;
    justify-content: center;
    margin-bottom: 30px;
}

.logo-circle {
    width: 80px;
    height: 80px;
    background-color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}

.logo-img {
    width: 60px;
    height: auto;
}

.auth-title {
    text-align: center;
    color: #ffc107;
    font-size: 32px;
    font-weight: bold;
    margin-bottom: 35px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.auth-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.form-group {
    position: relative;
}

.form-input {
    width: 100%;
    padding: 14px 16px;
    background: white;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    color: #333;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.form-input:focus {
    outline: none;
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.2);
    background: #fff;
}

.form-input::placeholder {
    color: #999;
}

.password-group {
    position: relative;
}

.toggle-password {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    font-size: 18px;
    padding: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.error-message {
    color: #ff6b6b;
    font-size: 14px;
    margin: 5px 0 0 0;
    text-align: center;
}

.success-message {
    color: #51cf66;
    font-size: 14px;
    margin: 5px 0 0 0;
    text-align: center;
}

.auth-btn {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);
    color: #333;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
    margin-top: 10px;
}

.auth-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(255, 193, 7, 0.4);
}

.auth-btn:active {
    transform: translateY(0);
}

.auth-link {
    text-align: center;
    color: #999;
    font-size: 14px;
    margin-top: 25px;
}

.auth-link a {
    color: #ffc107;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.auth-link a:hover {
    color: #ffb300;
}
</style>