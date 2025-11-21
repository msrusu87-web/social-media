<template>
    <div class="comment-section">
        <h3 class="text-lg font-semibold mb-4">Comments</h3>
        
        <div v-if="user" class="mb-4">
            <textarea
                v-model="newComment"
                placeholder="Write a comment..."
                class="w-full p-3 border rounded-lg resize-none"
                rows="3"
            ></textarea>
            <button
                @click="submitComment"
                :disabled="!newComment.trim() || loading"
                class="mt-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
            >
                {{ loading ? 'Posting...' : 'Post Comment' }}
            </button>
        </div>
        
        <div v-if="comments.length === 0" class="text-gray-500 text-center py-4">
            No comments yet. Be the first to comment!
        </div>
        
        <div v-else class="space-y-4">
            <div
                v-for="comment in comments"
                :key="comment.id"
                class="flex items-start space-x-3"
            >
                <img
                    :src="comment.user.avatar || '/default-avatar.png'"
                    alt="Avatar"
                    class="w-8 h-8 rounded-full"
                />
                <div class="flex-1">
                    <div class="bg-gray-100 rounded-lg p-3">
                        <div class="font-semibold">{{ comment.user.name }}</div>
                        <div class="text-sm">{{ comment.content }}</div>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        {{ formatDate(comment.created_at) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    postId: {
        type: Number,
        required: true,
    },
    comments: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const newComment = ref('');
const loading = ref(false);

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const submitComment = () => {
    loading.value = true;
    
    router.post(`/posts/${props.postId}/comments`, {
        content: newComment.value,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            newComment.value = '';
            loading.value = false;
        },
        onError: () => {
            loading.value = false;
        },
    });
};
</script>
