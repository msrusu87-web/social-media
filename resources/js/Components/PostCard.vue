<template>
    <div class="post-card border rounded-lg p-4 hover:shadow-lg transition">
        <div class="flex items-start space-x-3">
            <img
                :src="post.user.avatar || '/default-avatar.png'"
                alt="Avatar"
                class="w-10 h-10 rounded-full"
            />
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-semibold">{{ post.user.name }}</div>
                        <div class="text-sm text-gray-500">
                            @{{ post.user.username }} · {{ formatDate(post.created_at) }}
                        </div>
                    </div>
                    <button
                        v-if="canEdit"
                        class="text-gray-500 hover:text-gray-700"
                    >
                        •••
                    </button>
                </div>
                
                <div class="mt-2 whitespace-pre-wrap">{{ post.content }}</div>
                
                <div v-if="post.media_url" class="mt-2">
                    <img :src="post.media_url" alt="Post media" class="rounded-lg max-w-full" />
                </div>
                
                <div class="mt-4 flex items-center space-x-6 text-gray-500">
                    <button
                        @click="toggleLike"
                        :class="[
                            'flex items-center space-x-1 hover:text-red-500',
                            isLiked ? 'text-red-500' : ''
                        ]"
                    >
                        <span>❤</span>
                        <span>{{ post.likes_count || 0 }}</span>
                    </button>
                    
                    <button class="flex items-center space-x-1 hover:text-blue-500">
                        <span>💬</span>
                        <span>{{ post.comments_count || 0 }}</span>
                    </button>
                    
                    <button
                        @click="repost"
                        :class="[
                            'flex items-center space-x-1 hover:text-green-500',
                            isReposted ? 'text-green-500' : ''
                        ]"
                    >
                        <span>🔁</span>
                        <span>{{ post.reposts_count || 0 }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    post: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const canEdit = computed(() => user.value && user.value.id === props.post.user_id);
const isLiked = ref(false);
const isReposted = ref(false);

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
};

const toggleLike = () => {
    router.post(`/posts/${props.post.id}/like`, {}, {
        preserveScroll: true,
    });
};

const repost = () => {
    router.post(`/posts/${props.post.id}/repost`, {}, {
        preserveScroll: true,
    });
};
</script>
