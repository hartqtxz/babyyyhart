<template>
    <div class="my-applications">
        <!-- Header Section -->
        <div class="page-header">
            <h1>My Applications</h1>
            <p class="header-subtitle">Track and manage all your job applications</p>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-tabs">
                <button 
                    :class="['filter-btn', { active: activeFilter === 'all' }]"
                    @click="activeFilter = 'all'"
                >
                    All
                    <span class="count">{{ applications.length }}</span>
                </button>
                <button 
                    :class="['filter-btn', { active: activeFilter === 'Pending' }]"
                    @click="activeFilter = 'Pending'"
                >
                    Pending
                    <span class="count">{{ getStatusCount('Pending') }}</span>
                </button>
                <button 
                    :class="['filter-btn', { active: activeFilter === 'Approved' }]"
                    @click="activeFilter = 'Approved'"
                >
                    Approved
                    <span class="count">{{ getStatusCount('Approved') }}</span>
                </button>
                <button 
                    :class="['filter-btn', { active: activeFilter === 'Rejected' }]"
                    @click="activeFilter = 'Rejected'"
                >
                    Rejected
                    <span class="count">{{ getStatusCount('Rejected') }}</span>
                </button>
            </div>
        </div>

        <div v-if="filteredApplications.length === 0" class="no-applications">
            <div class="empty-illustration">📭</div>
            <p class="empty-title">No applications yet</p>
            <p class="empty-description">Start exploring jobs and apply to positions that interest you</p>
            <router-link to="/user/jobs" class="btn-browse">Browse Available Jobs</router-link>
        </div>

        <div v-else class="applications-list">
            <div v-for="app in filteredApplications" :key="app.id" class="application-card" :class="app.status">
                <div class="card-header">
                    <div class="header-left">
                        <h3>{{ app.job_posting?.title }}</h3>
                        <p class="company">{{ app.job_posting?.company }}</p>
                    </div>
                    <span class="status-badge" :class="app.status">{{ capitalizeStatus(app.status) }}</span>
                </div>

                <div class="card-body">
                    <div class="detail-grid">
                        <div class="detail">
                            <span class="detail-label">📍 Location</span>
                            <span class="detail-value">{{ app.job_posting?.location || 'N/A' }}</span>
                        </div>
                        <div class="detail">
                            <span class="detail-label">💰 Salary</span>
                            <span class="detail-value">{{ app.job_posting?.salary || 'Negotiable' }}</span>
                        </div>
                        <div class="detail">
                            <span class="detail-label">📅 Applied</span>
                            <span class="detail-value">{{ formatDate(app.created_at) }}</span>
                        </div>
                        <div class="detail">
                            <span class="detail-label">🏢 Type</span>
                            <span class="detail-value">{{ app.job_posting?.job_type || 'Full-time' }}</span>
                        </div>
                    </div>
                </div>

                <div class="card-actions">
                    <button @click="viewJobDetails(app.job_posting)" class="btn-view">
                        View Job Details
                    </button>
                    <button v-if="app.status === 'Pending'" @click="confirmWithdraw(app.id)" class="btn-withdraw">
                        Withdraw Application
                    </button>
                </div>
            </div>
        </div>

        <!-- Job Detail Modal -->
        <div v-if="selectedJob" class="modal-overlay" @click="closeModal">
            <div class="modal-content" @click.stop>
                <button class="modal-close" @click="closeModal">&times;</button>

                <div class="modal-header">
                    <h2>{{ selectedJob.title }}</h2>
                    <p class="modal-company">{{ selectedJob.company }}</p>
                </div>

                <div class="modal-body">
                    <div class="job-info-grid">
                        <div class="info-item">
                            <span class="info-icon">📍</span>
                            <div>
                                <p class="info-label">Location</p>
                                <p class="info-value">{{ selectedJob.location }}</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="info-icon">💰</span>
                            <div>
                                <p class="info-label">Salary</p>
                                <p class="info-value">{{ selectedJob.salary || 'Negotiable' }}</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="info-icon">🏢</span>
                            <div>
                                <p class="info-label">Type</p>
                                <p class="info-value">{{ selectedJob.job_type || 'Full-time' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="description-section">
                        <h3>Job Description</h3>
                        <p>{{ selectedJob.description }}</p>
                    </div>
                </div>

                <div class="modal-footer">
                    <button @click="closeModal" class="btn-close">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '../../services/api'

const applications = ref([])
const selectedJob = ref(null)
const activeFilter = ref('all')

const capitalizeStatus = (status) => {
    if (status === 'all') return 'All'
    return status.charAt(0).toUpperCase() + status.slice(1)
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    })
}

const filteredApplications = computed(() => {
    if (activeFilter.value === 'all') return applications.value
    return applications.value.filter(app => app.status === activeFilter.value)
})

const getStatusCount = (status) => {
    if (status === 'all') return applications.value.length
    return applications.value.filter(app => app.status === status).length
}

const fetchApplications = async () => {
    try {
        const response = await api.get('/applicants/my-applications')
        applications.value = response.data || []
    } catch (error) {
        console.error('Failed to fetch applications:', error)
    }
}

const viewJobDetails = (job) => {
    selectedJob.value = job
}

const closeModal = () => {
    selectedJob.value = null
}

const confirmWithdraw = (applicationId) => {
    if (confirm('Are you sure you want to withdraw this application? This action cannot be undone.')) {
        withdrawApplication(applicationId)
    }
}

const withdrawApplication = async (applicationId) => {
    try {
        await api.delete(`/applicants/${applicationId}`)
        alert('Application withdrawn successfully')
        fetchApplications()
    } catch (error) {
        alert('Failed to withdraw application')
    }
}

onMounted(() => {
    fetchApplications()
})
</script>

<style scoped>
.my-applications {
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

.filter-section {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.filter-tabs {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.filter-btn {
    padding: 10px 20px;
    border: 2px solid #e2e8f0;
    background: white;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    color: #718096;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-btn:hover {
    border-color: #3182ce;
    color: #3182ce;
}

.filter-btn.active {
    background: #3182ce;
    color: white;
    border-color: #3182ce;
}

.filter-btn .count {
    background: rgba(0, 0, 0, 0.2);
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
}

.no-applications {
    background: white;
    border-radius: 12px;
    padding: 60px 40px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.empty-illustration {
    font-size: 64px;
    margin-bottom: 20px;
}

.empty-title {
    color: #2d3748;
    font-size: 24px;
    font-weight: 600;
    margin: 0 0 10px 0;
}

.empty-description {
    color: #718096;
    font-size: 16px;
    margin: 0 0 30px 0;
}

.btn-browse {
    display: inline-block;
    padding: 12px 35px;
    background: #3182ce;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-browse:hover {
    background: #2c5282;
    transform: translateY(-2px);
}

.applications-list {
    display: grid;
    gap: 20px;
}

.application-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s;
    border-left: 5px solid #a0aec0;
}

.application-card.pending { border-left-color: #f6ad55; }
.application-card.approved { border-left-color: #68d391; }
.application-card.rejected { border-left-color: #fc8181; }

.application-card:hover {
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 20px;
    border-bottom: 1px solid #e2e8f0;
}

.header-left h3 {
    color: #2d3748;
    margin: 0 0 5px 0;
    font-size: 18px;
}

.company {
    color: #3182ce;
    margin: 0;
    font-weight: 500;
}

.status-badge {
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    white-space: nowrap;
    text-transform: uppercase;
}

.status-badge.pending {
    background: #feebc8;
    color: #7c2d12;
}

.status-badge.approved {
    background: #c6f6d5;
    color: #22543d;
}

.status-badge.rejected {
    background: #fed7d7;
    color: #742a2a;
}

.card-body {
    padding: 20px;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 20px;
}

.detail {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.detail-label {
    color: #a0aec0;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-value {
    color: #2d3748;
    font-size: 14px;
    font-weight: 500;
}

.card-actions {
    display: flex;
    gap: 10px;
    padding: 20px;
    border-top: 1px solid #e2e8f0;
    background: #f7fafc;
}

.btn-view,
.btn-withdraw {
    flex: 1;
    padding: 10px 15px;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 14px;
}

.btn-view {
    background: #3182ce;
    color: white;
}

.btn-view:hover {
    background: #2c5282;
}

.btn-withdraw {
    background: #fc8181;
    color: white;
}

.btn-withdraw:hover {
    background: #f56565;
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
    top: 15px;
    right: 15px;
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
}

.modal-close:hover {
    background: #cbd5e0;
}

.modal-header {
    padding: 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.modal-header h2 {
    margin: 0 0 5px 0;
    font-size: 26px;
}

.modal-company {
    margin: 0;
    opacity: 0.9;
}

.modal-body {
    padding: 30px;
}

.job-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.info-item {
    display: flex;
    gap: 15px;
}

.info-icon {
    font-size: 24px;
}

.info-label {
    color: #a0aec0;
    font-size: 12px;
    text-transform: uppercase;
    margin: 0;
}

.info-value {
    color: #2d3748;
    font-weight: 600;
    margin: 4px 0 0 0;
}

.description-section {
    border-top: 1px solid #e2e8f0;
    padding-top: 20px;
}

.description-section h3 {
    color: #2d3748;
    margin: 0 0 12px 0;
}

.description-section p {
    color: #4a5568;
    line-height: 1.6;
    margin: 0;
}

.modal-footer {
    padding: 20px 30px;
    border-top: 1px solid #e2e8f0;
    background: #f7fafc;
}

.btn-close {
    width: 100%;
    padding: 12px;
    background: #3182ce;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-close:hover {
    background: #2c5282;
}

@media (max-width: 768px) {
    .my-applications {
        padding: 20px;
    }

    .page-header h1 {
        font-size: 24px;
    }

    .filter-tabs {
        flex-direction: column;
    }

    .filter-btn {
        width: 100%;
        justify-content: center;
    }

    .card-header {
        flex-direction: column;
        gap: 10px;
    }

    .detail-grid {
        grid-template-columns: 1fr 1fr;
    }

    .card-actions {
        flex-direction: column;
    }
}
</style>
