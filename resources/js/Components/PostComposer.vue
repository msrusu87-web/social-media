<template>
    <div class="post-composer">
        <textarea
            v-model="content"
            placeholder="What's on your mind?"
            class="w-full p-4 border rounded-lg resize-none"
            rows="4"
        ></textarea>
        
        <div class="mt-4 flex items-center justify-between">
            <div class="flex space-x-2">
                <button
                    v-for="platform in platforms"
                    :key="platform.value"
                    @click="togglePlatform(platform.value)"
                    :class="[
                        'px-3 py-2 rounded-md text-sm',
                        selectedPlatforms.includes(platform.value)
                            ? 'bg-blue-500 text-white'
                            : 'bg-gray-200 text-gray-700'
                    ]"
                >
                    {{ platform.label }}
                </button>
            </div>
            
            <button
                @click="submitPost"
                :disabled="!content.trim() || loading"
                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
            >
                {{ loading ? 'Posting...' : 'Post' }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const content = ref('');
const loading = ref(false);
const selectedPlatforms = ref([]);

const platforms = [
    { value: 'facebook', label: 'Facebook' },
    { value: 'instagram', label: 'Instagram' },
    { value: 'x', label: 'X' },
    { value: 'tiktok', label: 'TikTok' },
    { value: 'youtube', label: 'YouTube' },
    { value: 'pinterest', label: 'Pinterest' },
];

const togglePlatform = (platform) => {
    const index = selectedPlatforms.value.indexOf(platform);
    if (index > -1) {
        selectedPlatforms.value.splice(index, 1);
    } else {
        selectedPlatforms.value.push(platform);
    }
};

const submitPost = () => {
    loading.value = true;
    
    router.post('/posts', {
        content: content.value,
        platforms: selectedPlatforms.value,
    }, {
        onSuccess: () => {
            content.value = '';
            selectedPlatforms.value = [];
            loading.value = false;
        },
        onError: () => {
            loading.value = false;
        },
    });
};
</script>
