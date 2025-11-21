<template>
    <div class="feed">
        <div v-if="posts.data.length === 0" class="text-center py-8 text-gray-500">
            No posts yet. Start following people or create your first post!
        </div>
        
        <div v-else class="space-y-4">
            <PostCard
                v-for="post in posts.data"
                :key="post.id"
                :post="post"
            />
        </div>
        
        <div v-if="posts.next_page_url" class="mt-6 text-center">
            <button
                @click="loadMore"
                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
                Load More
            </button>
        </div>
    </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import PostCard from './PostCard.vue';

const props = defineProps({
    posts: {
        type: Object,
        required: true,
    },
});

const loadMore = () => {
    if (props.posts.next_page_url) {
        router.visit(props.posts.next_page_url, {
            preserveScroll: true,
            preserveState: true,
        });
    }
};
</script>
