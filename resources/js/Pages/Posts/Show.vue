<template>
    <AuthenticatedLayout>
        <div class="space-y-6">
            <div class="bg-white shadow rounded-lg p-6">
                <PostCard :post="post" />
            </div>
            
            <div class="bg-white shadow rounded-lg p-6">
                <CommentSection
                    :post-id="post.id"
                    :comments="post.comments"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PostCard from '@/Components/PostCard.vue';
import CommentSection from '@/Components/CommentSection.vue';

defineProps({
    post: {
        type: Object,
        required: true,
    },
});
</script>
