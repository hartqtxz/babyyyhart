<template>
    <div>
        <h2 class="mb-4">MANAGE JOBS</h2>

        <div class="row mb-3">
            <div class="col-md-9">
                <div class="input-group">
                    <input v-model="searchQuery" type="text" class="form-control" placeholder="Search Job">
                    <button @click="filterJobs" class="btn btn-outline-secondary">Search</button>
                </div>
            </div>
            <div class="col-md-3">
                <button @click="openPostJobForm" class="btn btn-primary w-100">Post Job</button>
            </div>
        </div>

        <!-- Jobs Grid -->
        <div class="row g-3">
            <div class="col-lg-3 col-md-4 col-sm-6" v-for="job in filteredJobs" :key="job.id">
                <div class="card h-100 job-card">
                    <div class="card-body bg-primary text-white text-center p-4" style="min-height: 120px; display: flex; flex-direction: column; justify-content: center;">
                        <h5 class="mb-2 fw-bold">{{ job.title }}</h5>
                        <p class="mb-0">Workers Needed: <strong>{{ job.workers_needed }}</strong></p>
                    </div>

                    <div class="p-3" style="flex-grow: 1; display: flex; flex-direction: column; gap: 10px;">
                        <button @click="editJobModal(job)" class="btn btn-primary w-100" style="flex: 1; font-weight: 600;">Edit</button>
                        <button @click="deleteJob(job.id)" class="btn btn-danger w-100" style="flex: 1; font-weight: 600;">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Job Modal -->
        <div v-if="showModalForm" class="modal d-block" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ editingJob ? 'Edit Job' : 'Post New Job' }}</h5>
                        <button @click="closeModal" type="button" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Job Title</label>
                            <input v-model="formData.title" type="text" class="form-control" placeholder="Enter job title">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea v-model="formData.description" class="form-control" placeholder="Job description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Workers Needed</label>
                            <input v-model.number="formData.workers_needed" type="number" class="form-control" min="1">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Salary Min</label>
                                <input v-model.number="formData.salary_min" type="number" class="form-control" placeholder="Min salary">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Salary Max</label>
                                <input v-model.number="formData.salary_max" type="number" class="form-control" placeholder="Max salary">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input v-model="formData.location" type="text" class="form-control" placeholder="Job location">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Job Type</label>
                            <select v-model="formData.job_type" class="form-control">
                                <option value="">Select job type</option>
                                <option value="Full-time">Full-time</option>
                                <option value="Part-time">Part-time</option>
                                <option value="Contract">Contract</option>
                                <option value="Temporary">Temporary</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Experience Level</label>
                            <select v-model="formData.experience_level" class="form-control">
                                <option value="">Select experience level</option>
                                <option value="Entry Level">Entry Level</option>
                                <option value="Mid Level">Mid Level</option>
                                <option value="Senior">Senior</option>
                                <option value="Expert">Expert</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button @click="closeModal" type="button" class="btn btn-secondary">Cancel</button>
                        <button @click="saveJob" type="button" class="btn btn-primary">Save Job</button>
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
const searchQuery = ref('')
const showModalForm = ref(false)
const editingJob = ref(null)
const isLoading = ref(false)

const formData = ref({
    title: '',
    description: '',
    workers_needed: 1,
    salary_min: '',
    salary_max: '',
    location: '',
    job_type: '',
    experience_level: '',
    status: 'active'
})

const filteredJobs = computed(() => {
    if (!searchQuery.value) return jobs.value
    return jobs.value.filter(job => 
        job.title.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
})

onMounted(() => {
    fetchJobs()
})

const fetchJobs = async () => {
    try {
        isLoading.value = true
        const response = await api.get('/job-postings')
        jobs.value = response.data
    } catch (error) {
        console.error('Error fetching jobs:', error)
        alert('Failed to fetch jobs')
    } finally {
        isLoading.value = false
    }
}

const filterJobs = () => {
    // Filtering is handled by computed property
}

const openPostJobForm = () => {
    editingJob.value = null
    resetForm()
    showModalForm.value = true
}

const editJobModal = (job) => {
    editingJob.value = job.id
    formData.value = { ...job }
    showModalForm.value = true
}

const closeModal = () => {
    showModalForm.value = false
    resetForm()
}

const deleteJob = async (jobId) => {
    if (confirm('Are you sure you want to delete this job?')) {
        try {
            await api.delete(`/job-postings/${jobId}`)
            await fetchJobs()
        } catch (error) {
            console.error('Error deleting job:', error)
            alert('Failed to delete job')
        }
    }
}

const saveJob = async () => {
    try {
        if (editingJob.value) {
            await api.put(`/job-postings/${editingJob.value}`, formData.value)
        } else {
            await api.post('/job-postings', formData.value)
        }
        await fetchJobs()
        closeModal()
    } catch (error) {
        console.error('Error saving job:', error)
        alert('Failed to save job')
    }
}

const resetForm = () => {
    formData.value = {
        title: '',
        description: '',
        workers_needed: 1,
        salary_min: '',
        salary_max: '',
        location: '',
        job_type: '',
        experience_level: '',
        status: 'active'
    }
}
</script>

<style scoped>
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1050;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-dialog {
    width: 100%;
}

.job-card {
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    border: none;
    border-radius: 12px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.job-card:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    transform: translateY(-4px);
}

.job-card .card-body {
    border-radius: 12px 12px 0 0;
}
</style>