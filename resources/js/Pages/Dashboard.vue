<template>
    <AuthenticatedLayout>
        <div class="space-y-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h1 class="text-2xl font-bold mb-4">Dashboard</h1>
                <PostComposer />
            </div>
            
            <Feed :posts="posts" />
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PostComposer from '@/Components/PostComposer.vue';
import Feed from '@/Components/Feed.vue';

defineProps({
    posts: {
        type: Object,
        required: true,
    },
});
</script>
