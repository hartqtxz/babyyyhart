<template>
    <div class="available-jobs">
        <!-- Page Header -->
        <div class="page-header">
            <h1>Explore Job Opportunities</h1>
            <p class="header-subtitle">Find your next dream job from our extensive collection of openings</p>
        </div>

        <!-- Search & Filter Section -->
        <div class="search-section">
            <div class="search-container">
                <div class="search-input-wrapper">
                    <span class="search-icon">🔍</span>
                    <input 
                        v-model="searchQuery" 
                        type="text" 
                        placeholder="Search job title, company, or location..."
                        class="search-input"
                        @keyup.enter="filterJobs"
                    />
                </div>
                <button @click="filterJobs" class="search-btn">
                    Search
                </button>
            </div>

            <div class="search-stats">
                <p>Showing <strong>{{ filteredJobs.length }}</strong> job{{ filteredJobs.length !== 1 ? 's' : '' }}</p>
            </div>
        </div>

        <!-- Jobs Listing -->
        <div class="jobs-container">
            <div v-if="filteredJobs.length === 0" class="no-jobs">
                <div class="no-jobs-content">
                    <div class="empty-icon">🔍</div>
                    <p class="empty-title">No jobs found</p>
                    <p class="empty-description">Try adjusting your search criteria to find more opportunities</p>
                </div>
            </div>

            <div v-else class="jobs-list">
                <div 
                    v-for="job in filteredJobs" 
                    :key="job.id" 
                    class="job-list-item"
                    @click="viewJob(job)"
                >
                    <div class="job-header">
                        <div class="job-title-section">
                            <h3>{{ job.title }}</h3>
                            <p class="company-name">{{ job.company }}</p>
                        </div>
                        <div class="job-meta">
                            <span class="job-type">{{ job.job_type || 'Full-time' }}</span>
                        </div>
                    </div>

                    <div class="job-summary">
                        <div class="detail-badge">
                            <span>📍</span> {{ job.location }}
                        </div>
                        <div class="detail-badge" v-if="job.salary">
                            <span>💰</span> {{ job.salary }}
                        </div>
                        <div class="detail-badge">
                            <span>👤</span> {{ job.posted_by_name }}
                        </div>
                    </div>

                    <p class="job-description">{{ truncateText(job.description, 120) }}</p>

                    <button class="btn-apply-quick" @click.stop="viewJob(job)">View & Apply →</button>
                </div>
            </div>
        </div>

        <!-- Job Detail & Application Modal -->
        <div v-if="selectedJob" class="modal-overlay" @click="closeModal">
            <div class="modal-content" @click.stop>
                <button class="modal-close" @click="closeModal">&times;</button>

                <div class="modal-header">
                    <div>
                        <h2>{{ selectedJob.title }}</h2>
                        <p class="company-name">{{ selectedJob.company }}</p>
                    </div>
                </div>

                <div class="modal-body">
                    <!-- Job Details Tabs -->
                    <div class="tabs">
                        <button 
                            :class="['tab', { active: activeTab === 'details' }]"
                            @click="activeTab = 'details'"
                        >
                            📋 Job Details
                        </button>
                        <button 
                            :class="['tab', { active: activeTab === 'apply' }]"
                            @click="activeTab = 'apply'"
                        >
                            ✉️ Apply Now
                        </button>
                    </div>

                    <!-- Details Tab -->
                    <div v-if="activeTab === 'details'" class="tab-content">
                        <div class="job-details-grid">
                            <div class="detail-item">
                                <strong>📍 Location</strong>
                                <p>{{ selectedJob.location }}</p>
                            </div>
                            <div class="detail-item">
                                <strong>💰 Salary</strong>
                                <p>{{ selectedJob.salary || 'Negotiable' }}</p>
                            </div>
                            <div class="detail-item">
                                <strong>🏢 Job Type</strong>
                                <p>{{ selectedJob.job_type || 'Full-time' }}</p>
                            </div>
                            <div class="detail-item">
                                <strong>👤 Posted By</strong>
                                <p>{{ selectedJob.posted_by_name }}</p>
                            </div>
                        </div>

                        <div class="description-section">
                            <h3>About This Job</h3>
                            <p>{{ selectedJob.description }}</p>
                        </div>
                    </div>

                    <!-- Apply Tab -->
                    <div v-if="activeTab === 'apply'" class="tab-content">
                        <form @submit.prevent="submitApplication" class="application-form">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input 
                                    v-model="applicationForm.name" 
                                    type="text" 
                                    required 
                                    class="form-input"
                                    placeholder="Enter your full name"
                                />
                            </div>

                            <div class="form-group">
                                <label>Phone Number</label>
                                <input 
                                    v-model="applicationForm.phone" 
                                    type="tel" 
                                    required 
                                    class="form-input"
                                    placeholder="Enter your phone number"
                                />
                            </div>

                            <div class="form-group">
                                <label>Cover Letter</label>
                                <textarea 
                                    v-model="applicationForm.cover_letter" 
                                    rows="5"
                                    placeholder="Tell the employer why you're a great fit for this position..."
                                    class="form-input"
                                    required
                                ></textarea>
                            </div>

                            <div class="form-group">
                                <label>Resume/CV File</label>
                                <input 
                                    @change="handleResumeUpload" 
                                    type="file" 
                                    accept=".pdf,.doc,.docx"
                                    class="form-input"
                                />
                                <p class="form-hint">Optional: Upload your resume or CV (PDF, DOC, DOCX)</p>
                                <div v-if="applicationForm.resume_file" class="file-preview">
                                    📄 {{ applicationForm.resume_file.name }}
                                </div>
                            </div>

                            <p v-if="applicationError" class="error-message">{{ applicationError }}</p>
                            <p v-if="applicationSuccess" class="success-message">{{ applicationSuccess }}</p>

                            <div class="form-actions">
                                <button type="submit" class="btn-submit" :disabled="isSubmittingApplication">
                                    {{ isSubmittingApplication ? '⏳ Submitting...' : '✓ Submit Application' }}
                                </button>
                                <button type="button" @click="activeTab = 'details'" class="btn-back">
                                    ← Back
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../../services/api'

const jobs = ref([])
const selectedJob = ref(null)
const activeTab = ref('details')
const searchQuery = ref('')
const isSubmittingApplication = ref(false)
const applicationError = ref('')
const applicationSuccess = ref('')

const applicationForm = ref({
    name: '',
    phone: '',
    cover_letter: '',
    resume_file: null,
})

const user = computed(() => {
    const userData = localStorage.getItem('user')
    return userData ? JSON.parse(userData) : null
})

const filteredJobs = computed(() => {
    if (!searchQuery.value) return jobs.value
    const query = searchQuery.value.toLowerCase()
    return jobs.value.filter(job =>
        job.title.toLowerCase().includes(query) ||
        job.company.toLowerCase().includes(query) ||
        job.location.toLowerCase().includes(query)
    )
})

const truncateText = (text, length) => {
    if (!text) return ''
    return text.length > length ? text.substring(0, length) + '...' : text
}

const fetchJobs = async () => {
    try {
        const response = await api.get('/job-postings')
        jobs.value = response.data
    } catch (error) {
        console.error('Failed to fetch jobs:', error)
    }
}

const filterJobs = () => {
    // Already filtered by computed property
}

const handleResumeUpload = (event) => {
    const file = event.target.files[0]
    if (file) {
        applicationForm.value.resume_file = file
    }
}

const viewJob = (job) => {
    selectedJob.value = job
    activeTab.value = 'details'
    applicationForm.value = {
        name: user.value?.name || '',
        phone: user.value?.phone || '',
        cover_letter: '',
        resume_file: null,
    }
}

const closeModal = () => {
    selectedJob.value = null
    activeTab.value = 'details'
    applicationError.value = ''
    applicationSuccess.value = ''
}

const submitApplication = async () => {
    if (!selectedJob.value) return

    isSubmittingApplication.value = true
    applicationError.value = ''
    applicationSuccess.value = ''

    try {
        // Prepare FormData for file upload
        const formData = new FormData()
        formData.append('user_id', user.value.id)
        formData.append('job_posting_id', selectedJob.value.id)
        formData.append('cover_letter', applicationForm.value.cover_letter)
        formData.append('phone', applicationForm.value.phone)
        if (applicationForm.value.resume_file) {
            formData.append('resume_file', applicationForm.value.resume_file)
        }

        // Submit application (backend automatically creates notification for employer)
        const response = await api.post('/applicants', formData)

        applicationSuccess.value = 'Application submitted successfully! The employer will review your application.'

        setTimeout(() => {
            closeModal()
            fetchJobs()
        }, 2000)
    } catch (error) {
        if (error.response?.status === 409) {
            applicationError.value = 'You have already applied for this job'
        } else if (error.response?.data?.message) {
            applicationError.value = error.response.data.message
        } else {
            applicationError.value = 'Failed to submit application'
        }
    } finally {
        isSubmittingApplication.value = false
    }
}

onMounted(() => {
    fetchJobs()
})
</script>

<style scoped>
.available-jobs {
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

.search-section {
    background: white;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 35px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.search-container {
    display: flex;
    gap: 12px;
    max-width: 900px;
    margin: 0 auto 15px auto;
}

.search-input-wrapper {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 12px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    padding: 0 14px;
    background: #f7fafc;
    transition: all 0.3s ease;
}

.search-input-wrapper:focus-within {
    border-color: #3182ce;
    background: white;
    box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
}

.search-icon {
    font-size: 18px;
    color: #a0aec0;
}

.search-input {
    flex: 1;
    border: none;
    background: none;
    padding: 12px 0;
    font-size: 15px;
    font-family: inherit;
}

.search-input:focus {
    outline: none;
}

.search-input::placeholder {
    color: #cbd5e0;
}

.search-btn {
    padding: 12px 30px;
    background: #3182ce;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.search-btn:hover {
    background: #2c5282;
    transform: translateY(-2px);
}

.search-stats {
    text-align: center;
    color: #718096;
    font-size: 14px;
}

.search-stats strong {
    color: #2d3748;
    font-weight: 600;
}

.jobs-container {
    max-width: 900px;
    margin: 0 auto;
}

.no-jobs {
    background: white;
    border-radius: 12px;
    padding: 80px 40px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.no-jobs-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
}

.empty-icon {
    font-size: 64px;
    opacity: 0.5;
}

.empty-title {
    color: #2d3748;
    font-size: 22px;
    font-weight: 600;
    margin: 0;
}

.empty-description {
    color: #718096;
    font-size: 15px;
    margin: 0;
}

.jobs-list {
    display: grid;
    gap: 16px;
}

.job-list-item {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    cursor: pointer;
    border-left: 4px solid #3182ce;
}

.job-list-item:hover {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
    transform: translateY(-4px);
    border-left-color: #2c5282;
}

.job-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 16px;
}

.job-title-section h3 {
    font-size: 20px;
    color: #1a202c;
    margin: 0 0 4px 0;
    font-weight: 700;
}

.job-title-section .company-name {
    color: #3182ce;
    font-size: 14px;
    margin: 0;
    font-weight: 500;
}

.job-meta {
    display: flex;
    gap: 8px;
}

.job-type {
    display: inline-block;
    padding: 6px 14px;
    background: #ebf8ff;
    color: #2c5282;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.job-summary {
    display: flex;
    gap: 16px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}

.detail-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #718096;
    font-size: 13px;
    font-weight: 500;
}

.job-description {
    color: #4a5568;
    line-height: 1.5;
    margin-bottom: 16px;
    font-size: 14px;
}

.btn-apply-quick {
    padding: 11px 24px;
    background: #3182ce;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
}

.btn-apply-quick:hover {
    background: #2c5282;
    transform: translateY(-2px);
}

/* Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    padding: 20px;
}

.modal-content {
    background: white;
    border-radius: 12px;
    max-width: 700px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.modal-close {
    position: absolute;
    top: 16px;
    right: 16px;
    background: #e2e8f0;
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    font-size: 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    transition: all 0.3s ease;
    color: #2d3748;
}

.modal-close:hover {
    background: #cbd5e0;
}

.modal-header {
    background: linear-gradient(135deg, #3182ce 0%, #2c5282 100%);
    color: white;
    padding: 30px;
}

.modal-header h2 {
    font-size: 26px;
    margin: 0 0 8px 0;
    font-weight: 700;
}

.modal-header .company-name {
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
}

.modal-body {
    padding: 30px;
}

.tabs {
    display: flex;
    gap: 12px;
    border-bottom: 2px solid #e2e8f0;
    margin-bottom: 24px;
}

.tab {
    padding: 12px 20px;
    background: none;
    border: none;
    font-weight: 600;
    color: #a0aec0;
    cursor: pointer;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
    transition: all 0.3s ease;
    font-size: 15px;
}

.tab.active {
    color: #3182ce;
    border-bottom-color: #3182ce;
}

.tab:hover {
    color: #3182ce;
}

.tab-content {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.job-details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 24px;
    background: #f7fafc;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.detail-item {
    flex: 1;
}

.detail-item strong {
    display: block;
    color: #2d3748;
    margin-bottom: 8px;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #718096;
}

.detail-item p {
    color: #2d3748;
    margin: 0;
    font-size: 15px;
    font-weight: 500;
}

.description-section {
    margin-top: 24px;
}

.description-section h3 {
    color: #2d3748;
    margin: 0 0 12px 0;
    font-size: 18px;
}

.description-section p {
    color: #4a5568;
    line-height: 1.7;
    margin: 0;
    white-space: pre-wrap;
}

.application-form {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-weight: 600;
    color: #2d3748;
    font-size: 15px;
}

.form-input {
    padding: 12px 14px;
    border: 2px solid #e2e8f0;
    border-radius: 6px;
    font-size: 14px;
    font-family: inherit;
    background: #f7fafc;
    transition: all 0.3s ease;
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

.form-hint {
    color: #a0aec0;
    font-size: 12px;
    margin: 4px 0 0 0;
}

.error-message {
    color: #7f1d1d;
    font-size: 14px;
    padding: 12px 14px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 6px;
    margin: 0;
}

.success-message {
    color: #22543d;
    font-size: 14px;
    padding: 12px 14px;
    background: #f0fdf4;
    border: 1px solid #dcfce7;
    border-radius: 6px;
    margin: 0;
}

.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 12px;
}

.btn-submit,
.btn-back {
    flex: 1;
    padding: 13px;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 15px;
}

.btn-submit {
    background: #3182ce;
    color: white;
}

.btn-submit:hover:not(:disabled) {
    background: #2c5282;
    transform: translateY(-2px);
}

.btn-submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-back {
    background: #e2e8f0;
    color: #2d3748;
}

.btn-back:hover {
    background: #cbd5e0;
}

@media (max-width: 768px) {
    .available-jobs {
        padding: 20px;
    }

    .page-header h1 {
        font-size: 24px;
    }

    .search-container {
        flex-direction: column;
    }

    .search-btn {
        width: 100%;
    }

    .job-details-grid {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column;
    }

    .modal-content {
        max-height: 95vh;
    }
}
</style>
