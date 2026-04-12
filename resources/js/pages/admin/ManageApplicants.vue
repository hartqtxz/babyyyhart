<template>
    <div class="manage-applicants">
        <div class="page-header">
            <h1>Manage Applicants</h1>
            <p>Review and manage job applications</p>
        </div>

        <!-- Filters -->
        <div class="filters">
            <button 
                :class="['filter-btn', { active: filterStatus === 'all' }]"
                @click="filterStatus = 'all'; fetchApplicants()"
            >
                All <span class="badge">{{ applicants.length }}</span>
            </button>
            <button 
                :class="['filter-btn', { active: filterStatus === 'Pending' }]"
                @click="filterStatus = 'Pending'; fetchApplicants()"
            >
                Pending <span class="badge">{{ getStatusCount('Pending') }}</span>
            </button>
            <button 
                :class="['filter-btn', { active: filterStatus === 'Approved' }]"
                @click="filterStatus = 'Approved'; fetchApplicants()"
            >
                Approved <span class="badge">{{ getStatusCount('Approved') }}</span>
            </button>
            <button 
                :class="['filter-btn', { active: filterStatus === 'Rejected' }]"
                @click="filterStatus = 'Rejected'; fetchApplicants()"
            >
                Rejected <span class="badge">{{ getStatusCount('Rejected') }}</span>
            </button>
        </div>

        <!-- Applicants List -->
        <div class="applicants-container">
            <div v-if="applicants.length === 0" class="no-data">
                <p>No applicants found</p>
            </div>

            <div v-else class="applicants-list">
                <div v-for="applicant in filteredApplicants" :key="applicant.id" class="applicant-card">
                    <div class="card-header">
                        <div class="applicant-info">
                            <h3>{{ applicant.user?.name }}</h3>
                            <p class="email">{{ applicant.user?.email }}</p>
                        </div>
                        <span :class="['status-badge', applicant.status]">
                            {{ capitalizeStatus(applicant.status) }}
                        </span>
                    </div>

                    <div class="card-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <strong>Position</strong>
                                <p>{{ applicant.jobPosting?.title }}</p>
                            </div>
                            <div class="info-item">
                                <strong>Company</strong>
                                <p>{{ applicant.jobPosting?.company }}</p>
                            </div>
                            <div class="info-item">
                                <strong>Phone</strong>
                                <p>{{ applicant.phone || 'N/A' }}</p>
                            </div>
                            <div class="info-item">
                                <strong>Date Applied</strong>
                                <p>{{ formatDate(applicant.created_at) }}</p>
                            </div>
                        </div>

                        <div class="cover-letter" v-if="applicant.cover_letter">
                            <strong>Cover Letter</strong>
                            <p>{{ truncate(applicant.cover_letter, 150) }}</p>
                        </div>

                        <div class="resume-section" v-if="applicant.resume_path || applicant.resume_link">
                            <strong>Resume/CV</strong>
                            <div v-if="applicant.resume_path" class="resume-link">
                                <a :href="getResumeUrl(applicant.resume_path)" target="_blank" class="btn-link">
                                    📄 Download Resume (Uploaded)
                                </a>
                            </div>
                            <div v-else-if="applicant.resume_link" class="resume-link">
                                <a :href="applicant.resume_link" target="_blank" class="btn-link">
                                    📄 View Resume (Link)
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-actions">
                        <button 
                            @click="approveApplicant(applicant.id)"
                            class="btn btn-approve"
                        >
                            ✓ Approve
                        </button>
                        <button 
                            @click="rejectApplicant(applicant.id)"
                            class="btn btn-reject"
                        >
                            ✕ Reject
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../../services/api'

const applicants = ref([])
const filterStatus = ref('all')
const isLoading = ref(false)

const filteredApplicants = computed(() => {
    if (filterStatus.value === 'all') return applicants.value
    return applicants.value.filter(a => a.status === filterStatus.value)
})

const getStatusCount = (status) => {
    return applicants.value.filter(a => a.status === status).length
}

const capitalizeStatus = (status) => {
    return status.charAt(0).toUpperCase() + status.slice(1)
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    })
}

const truncate = (text, length) => {
    if (!text) return ''
    return text.length > length ? text.substring(0, length) + '...' : text
}

const getResumeUrl = (resumePath) => {
    return `/storage/${resumePath}`
}

onMounted(() => {
    fetchApplicants()
})

const fetchApplicants = async () => {
    try {
        isLoading.value = true
        const response = await api.get('/applicants')
        applicants.value = response.data || []
    } catch (error) {
        console.error('Error fetching applicants:', error)
    } finally {
        isLoading.value = false
    }
}

const approveApplicant = async (applicantId) => {
    try {
        await api.put(`/applicants/${applicantId}`, { status: 'Approved' })
        await fetchApplicants()
    } catch (error) {
        console.error('Error approving applicant:', error)
        alert('Failed to approve applicant')
    }
}

const rejectApplicant = async (applicantId) => {
    try {
        await api.put(`/applicants/${applicantId}`, { status: 'Rejected' })
        await fetchApplicants()
    } catch (error) {
        console.error('Error rejecting applicant:', error)
        alert('Failed to reject applicant')
    }
}
</script>

<style scoped>
.manage-applicants {
    padding: 30px 20px;
}

.page-header {
    margin-bottom: 30px;
}

.page-header h1 {
    color: #003d82;
    font-size: 28px;
    margin: 0 0 8px 0;
}

.page-header p {
    color: #666;
    margin: 0;
}

.filters {
    display: flex;
    gap: 12px;
    margin-bottom: 30px;
}

.filter-btn {
    padding: 10px 18px;
    border: 2px solid #ddd;
    background: white;
    border-radius: 6px;
    font-weight: 600;
    color: #666;
    cursor: pointer;
    transition: all 0.3s ease;
}

.filter-btn:hover {
    border-color: #2563eb;
    color: #2563eb;
}

.filter-btn.active {
    background: #2563eb;
    color: white;
    border-color: #2563eb;
}

.filter-btn .badge {
    display: inline-block;
    margin-left: 8px;
    padding: 2px 8px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 12px;
    font-size: 12px;
    font-weight: 700;
    min-width: 20px;
    text-align: center;
}

.filter-btn.active .badge {
    background: rgba(255, 255, 255, 0.4);
    color: white;
}

.filter-btn:hover .badge {
    background: rgba(37, 99, 235, 0.2);
}

.applicants-container {
    max-width: 1000px;
}

.no-data {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 12px;
    color: #999;
}

.applicants-list {
    display: grid;
    gap: 20px;
}

.applicant-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    border-left: 4px solid #2563eb;
}

.applicant-card:hover {
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    padding: 20px;
    background: #f9f9f9;
    border-bottom: 1px solid #eee;
}

.applicant-info h3 {
    color: #003d82;
    margin: 0 0 4px 0;
    font-size: 18px;
}

.email {
    color: #666;
    font-size: 13px;
    margin: 0;
}

.status-badge {
    display: inline-block;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}

.status-badge.pending {
    background: #fef3c7;
    color: #92400e;
}

.status-badge.approved {
    background: #d1fae5;
    color: #065f46;
}

.status-badge.rejected {
    background: #fee2e2;
    color: #991b1b;
}

.card-body {
    padding: 20px;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.info-item strong {
    display: block;
    color: #333;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
}

.info-item p {
    color: #555;
    margin: 0;
}

.cover-letter {
    background: #f9f9f9;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 15px;
}

.cover-letter strong {
    display: block;
    color: #333;
    margin-bottom: 8px;
}

.cover-letter p {
    color: #666;
    margin: 0;
    line-height: 1.5;
}

.resume-link {
    margin-bottom: 15px;
}

.resume-section {
    background: #f0f4ff;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 15px;
    border-left: 3px solid #2563eb;
}

.resume-section strong {
    display: block;
    color: #003d82;
    margin-bottom: 8px;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-link {
    color: #2563eb;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.btn-link:hover {
    color: #1d4ed8;
}

.card-actions {
    display: flex;
    gap: 12px;
    padding: 15px 20px;
    background: #f9f9f9;
    border-top: 1px solid #eee;
}

.btn {
    flex: 1;
    padding: 10px 16px;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
}

.btn-approve {
    background: #26c281;
    color: white;
}

.btn-approve:hover {
    background: #1ea869;
}

.btn-reject {
    background: #e74c3c;
    color: white;
}

.btn-reject:hover {
    background: #c0392b;
}

@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }

    .card-header {
        flex-direction: column;
        gap: 12px;
    }

    .card-actions {
        flex-direction: column;
    }
}
</style>
