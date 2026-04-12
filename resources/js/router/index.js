import { createRouter, createWebHistory } from 'vue-router'

import FrontPage from '../pages/FrontPage.vue'
import Login from '../pages/auth/Login.vue'
import Register from '../pages/auth/Register.vue'

import AdminLayout from '../layouts/AdminLayout.vue'
import Dashboard from '../pages/admin/Dashboard.vue'
import ManageJobs from '../pages/admin/ManageJobs.vue'
import ManageApplicants from '../pages/admin/ManageApplicants.vue'
import Users from '../pages/admin/Users.vue'
import Notification from '../pages/admin/notification.vue'

import UserLayout from '../layouts/UserLayout.vue'
import UserDashboard from '../pages/user/DashBoardUser.vue'
import AvailableJobs from '../pages/user/AvailableJobs.vue'
import MyApplications from '../pages/user/MyApplications.vue'
import Profile from '../pages/user/Profile.vue'

const routes = [
    { path: '/', component: FrontPage },
    { path: '/login', component: Login },
    { path: '/register', component: Register },

    {
        path: '/admin',
        component: AdminLayout,
        children: [
            { path: 'dashboard', component: Dashboard },
            { path: 'manage-jobs', component: ManageJobs },
            { path: 'manage-applicants', component: ManageApplicants },
            { path: 'users', component: Users },
            { path: 'notification', component: Notification },
        ],
    },

    {
        path: '/user',
        component: UserLayout,
        children: [
            { path: 'dashboard', component: UserDashboard },
            { path: 'jobs', component: AvailableJobs },
            { path: 'my-applications', component: MyApplications },
            { path: 'profile', component: Profile },
        ],
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router