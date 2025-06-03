<template>
    <div class="status-cards mb-4">
        <div class="row g-3">
            <StatusCard
                title="Pending"
                count="pendingCount"
                icon="clock"
                color="warning"
            />
            <StatusCard
                title="Verified"
                count="verifiedCount"
                icon="check-circle"
                color="success"
            />
            <StatusCard
                title="Rejected"
                count="rejectedCount"
                icon="times-circle"
                color="danger"
            />
        </div>
    </div>
</template>

<script setup>
import StatusCard from "@/Components/StatusCard.vue";

defineProps({
    pendingCount: Number,
    verifiedCount: Number,
    rejectedCount: Number,
});
</script>
