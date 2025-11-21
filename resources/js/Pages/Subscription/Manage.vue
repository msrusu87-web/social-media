<template>
    <AuthenticatedLayout>
        <div class="bg-white shadow rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-6">Manage Subscription</h1>
            
            <div v-if="currentSubscription" class="mb-8 p-4 bg-blue-50 rounded-lg">
                <h2 class="text-lg font-semibold">Current Plan: {{ currentSubscription.plan.name }}</h2>
                <p class="text-gray-600">Status: {{ currentSubscription.status }}</p>
                <p class="text-sm text-gray-500 mt-2">
                    Renews on: {{ formatDate(currentSubscription.ends_at) }}
                </p>
            </div>
            
            <h2 class="text-xl font-semibold mb-4">Available Plans</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    v-for="plan in plans"
                    :key="plan.id"
                    class="border rounded-lg p-6 hover:shadow-lg transition"
                >
                    <h3 class="text-xl font-bold mb-2">{{ plan.name }}</h3>
                    <p class="text-3xl font-bold mb-4">
                        ${{ plan.price }}<span class="text-sm text-gray-500">/month</span>
                    </p>
                    <p class="text-gray-600 mb-4">{{ plan.description }}</p>
                    <ul class="text-sm text-gray-600 mb-6 space-y-2">
                        <li>✓ {{ plan.post_limit === -1 ? 'Unlimited' : plan.post_limit }} posts/month</li>
                    </ul>
                    <button
                        @click="subscribe(plan)"
                        :disabled="isCurrentPlan(plan)"
                        :class="[
                            'w-full py-2 px-4 rounded-md font-semibold',
                            isCurrentPlan(plan)
                                ? 'bg-gray-200 text-gray-500 cursor-not-allowed'
                                : 'bg-blue-600 text-white hover:bg-blue-700'
                        ]"
                    >
                        {{ isCurrentPlan(plan) ? 'Current Plan' : 'Subscribe' }}
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    currentSubscription: {
        type: Object,
        default: null,
    },
    plans: {
        type: Array,
        required: true,
    },
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric',
    });
};

const isCurrentPlan = (plan) => {
    return props.currentSubscription && props.currentSubscription.plan_id === plan.id;
};

const subscribe = (plan) => {
    router.post(`/subscription/subscribe/${plan.id}`, {
        payment_method: 'pm_card_visa', // This would come from Stripe Elements
    });
};
</script>
