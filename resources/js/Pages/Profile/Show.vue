<template>
    <AuthenticatedLayout>
        <div class="space-y-6">
            <ProfileCard
                :user="user"
                :followers-count="followersCount"
                :following-count="followingCount"
                :posts-count="postsCount"
            />
            
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-bold mb-4">Posts</h2>
                <div v-if="user.posts.length === 0" class="text-gray-500 text-center py-8">
                    No posts yet
                </div>
                <div v-else class="space-y-4">
                    <PostCard
                        v-for="post in user.posts"
                        :key="post.id"
                        :post="post"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProfileCard from '@/Components/ProfileCard.vue';
import PostCard from '@/Components/PostCard.vue';

defineProps({
    user: {
        type: Object,
        required: true,
    },
    followersCount: {
        type: Number,
        required: true,
    },
    followingCount: {
        type: Number,
        required: true,
    },
    postsCount: {
        type: Number,
        required: true,
    },
});
</script>
