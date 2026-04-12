<template>
    <div>
        <h3>Admin Dashboard</h3>

        <div class="row mt-4">
            <div class="col-md-3" v-for="card in cards" :key="card.title">
                <div :class="['card text-white p-4', card.color]">
                    <h5>{{ card.title }}</h5>
                    <h2>{{ card.count }}</h2>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const cards = [
    { title: 'Jobs Posted', count: 5, color: 'bg-primary' },
    { title: 'Applicants', count: 15, color: 'bg-warning' },
    { title: 'Users', count: 5, color: 'bg-success' },
    { title: 'Notifications', count: 12, color: 'bg-danger' },
]
</script>