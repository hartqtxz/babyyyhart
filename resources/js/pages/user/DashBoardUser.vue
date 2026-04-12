<template>
  <div class="user-dashboard">
    <!-- Welcome Section -->
    <div class="welcome-section">
      <div class="welcome-content">
        <h1>Welcome back, {{ userName }}! 👋</h1>
        <p class="welcome-subtitle">Track your job applications and explore new opportunities</p>
      </div>
    </div>

    <!-- Stats Section -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">📝</div>
        <div class="stat-info">
          <p class="stat-label">Total Applications</p>
          <p class="stat-value">{{ stats.totalApplications }}</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-info">
          <p class="stat-label">Approved</p>
          <p class="stat-value">{{ stats.approved }}</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">⏳</div>
        <div class="stat-info">
          <p class="stat-label">Pending</p>
          <p class="stat-value">{{ stats.pending }}</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">❌</div>
        <div class="stat-info">
          <p class="stat-label">Rejected</p>
          <p class="stat-value">{{ stats.rejected }}</p>
        </div>
      </div>
    </div>

    <!-- Recent Jobs Section -->
    <div class="dashboard-section">
      <div class="section-header">
        <h2>Recently Posted Jobs</h2>
        <router-link to="/user/jobs" class="view-all-link">View All →</router-link>
      </div>

      <div v-if="recentJobs.length === 0" class="empty-state">
        <p>No jobs posted recently</p>
      </div>

      <div v-else class="jobs-preview">
        <div v-for="job in recentJobs.slice(0, 4)" :key="job.id" class="job-preview-card">
          <div class="job-preview-header">
            <h3>{{ job.title }}</h3>
            <span class="job-type-badge">{{ job.job_type || 'Full-time' }}</span>
          </div>
          <p class="job-company">{{ job.company }}</p>
          <div class="job-preview-meta">
            <span>📍 {{ job.location }}</span>
            <span v-if="job.salary">💰 {{ job.salary }}</span>
          </div>
          <button @click="applyJob(job.id)" class="btn-apply-preview">Apply Now</button>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="dashboard-section">
      <h2>Quick Actions</h2>
      <div class="quick-actions">
        <router-link to="/user/jobs" class="action-card">
          <div class="action-icon">🔍</div>
          <div class="action-text">
            <h3>Browse Jobs</h3>
            <p>Explore available positions</p>
          </div>
        </router-link>

        <router-link to="/user/my-applications" class="action-card">
          <div class="action-icon">📋</div>
          <div class="action-text">
            <h3>My Applications</h3>
            <p>Check application status</p>
          </div>
        </router-link>

        <router-link to="/user/profile" class="action-card">
          <div class="action-icon">👤</div>
          <div class="action-text">
            <h3>Edit Profile</h3>
            <p>Update your information</p>
          </div>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../../services/api'

const stats = ref({
  totalApplications: 0,
  approved: 0,
  pending: 0,
  rejected: 0,
})
const recentJobs = ref([])

const user = computed(() => {
  const userData = localStorage.getItem('user')
  return userData ? JSON.parse(userData) : null
})

const userName = computed(() => {
  return user.value?.name || 'User'
})

const fetchStats = async () => {
  try {
    const response = await api.get('/applications/stats')
    stats.value = response.data
  } catch (error) {
    console.error('Failed to fetch stats:', error)
  }
}

const fetchRecentJobs = async () => {
  try {
    const response = await api.get('/jobs?limit=4')
    recentJobs.value = response.data
  } catch (error) {
    console.error('Failed to fetch recent jobs:', error)
    // Mock data for demo
    recentJobs.value = [
      {
        id: 1,
        title: 'Senior Frontend Developer',
        company: 'Tech Corp',
        location: 'New York, NY',
        job_type: 'Full-time',
        salary: '$120k - $160k',
      },
      {
        id: 2,
        title: 'Backend Engineer',
        company: 'StartUp Inc',
        location: 'San Francisco, CA',
        job_type: 'Full-time',
        salary: '$110k - $150k',
      },
      {
        id: 3,
        title: 'Full Stack Developer',
        company: 'Digital Agency',
        location: 'Remote',
        job_type: 'Full-time',
        salary: '$100k - $140k',
      },
      {
        id: 4,
        title: 'UI/UX Designer',
        company: 'Creative Studios',
        location: 'Los Angeles, CA',
        job_type: 'Full-time',
        salary: '$90k - $130k',
      },
    ]
  }
}

const applyJob = (jobId) => {
  // Handle job application
  console.log('Applying for job:', jobId)
}

onMounted(() => {
  fetchStats()
  fetchRecentJobs()
})
</script>

<style scoped>
.user-dashboard {
  padding: 40px 20px;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  min-height: 100vh;
}

.welcome-section {
  background: white;
  border-radius: 12px;
  padding: 30px;
  margin-bottom: 30px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.welcome-content h1 {
  font-size: 32px;
  color: #1a202c;
  margin: 0 0 10px 0;
}

.welcome-subtitle {
  color: #718096;
  font-size: 16px;
  margin: 0;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 40px;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s;
}

.stat-card:hover {
  transform: translateY(-4px);
}

.stat-icon {
  font-size: 36px;
  min-width: 50px;
}

.stat-info {
  flex: 1;
}

.stat-label {
  color: #718096;
  font-size: 14px;
  margin: 0;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.stat-value {
  color: #2d3748;
  font-size: 28px;
  font-weight: bold;
  margin: 5px 0 0 0;
}

.dashboard-section {
  background: white;
  border-radius: 12px;
  padding: 30px;
  margin-bottom: 30px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
}

.section-header h2 {
  margin: 0;
  color: #1a202c;
  font-size: 24px;
}

.view-all-link {
  color: #3182ce;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s;
}

.view-all-link:hover {
  color: #2c3e50;
}

.empty-state {
  text-align: center;
  padding: 40px 20px;
  color: #a0aec0;
  font-size: 16px;
}

.jobs-preview {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
}

.job-preview-card {
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 20px;
  transition: all 0.3s;
}

.job-preview-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  border-color: #3182ce;
}

.job-preview-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 10px;
}

.job-preview-header h3 {
  margin: 0;
  color: #2d3748;
  font-size: 18px;
  flex: 1;
}

.job-type-badge {
  background: #ebf8ff;
  color: #2c5282;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  white-space: nowrap;
  margin-left: 10px;
}

.job-company {
  color: #3182ce;
  margin: 0 0 12px 0;
  font-weight: 500;
}

.job-preview-meta {
  display: flex;
  gap: 15px;
  margin: 15px 0;
  color: #718096;
  font-size: 14px;
}

.btn-apply-preview {
  width: 100%;
  padding: 10px 15px;
  background: #3182ce;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: background 0.2s;
}

.btn-apply-preview:hover {
  background: #2c5282;
}

.quick-actions {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
}

.action-card {
  display: flex;
  align-items: center;
  gap: 20px;
  padding: 20px;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  text-decoration: none;
  color: inherit;
  transition: all 0.3s;
}

.action-card:hover {
  border-color: #3182ce;
  background: #ebf8ff;
}

.action-icon {
  font-size: 32px;
}

.action-text h3 {
  margin: 0;
  color: #2d3748;
  font-size: 18px;
}

.action-text p {
  margin: 5px 0 0 0;
  color: #718096;
  font-size: 14px;
}

@media (max-width: 768px) {
  .user-dashboard {
    padding: 20px;
  }

  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
  }

  .stat-card {
    flex-direction: column;
    text-align: center;
  }

  .jobs-preview {
    grid-template-columns: 1fr;
  }

  .quick-actions {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 1024px) {
  .jobs-preview {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>