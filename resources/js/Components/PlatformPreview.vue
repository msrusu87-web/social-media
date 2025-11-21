<template>
    <div class="platform-preview border rounded-lg p-4">
        <h3 class="text-lg font-semibold mb-4">{{ platformName }} Preview</h3>
        
        <div class="preview-container">
            <div class="flex items-start space-x-3">
                <img
                    :src="user.avatar || '/default-avatar.png'"
                    alt="Avatar"
                    class="w-10 h-10 rounded-full"
                />
                <div class="flex-1">
                    <div class="font-semibold">{{ user.name }}</div>
                    <div class="text-sm text-gray-500">{{ user.username }}</div>
                    <div class="mt-2 whitespace-pre-wrap">{{ content }}</div>
                    
                    <div v-if="mediaUrl" class="mt-2">
                        <img :src="mediaUrl" alt="Post media" class="rounded-lg max-w-full" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    platform: {
        type: String,
        required: true,
    },
    content: {
        type: String,
        default: '',
    },
    mediaUrl: {
        type: String,
        default: null,
    },
    user: {
        type: Object,
        required: true,
    },
});

const platformName = computed(() => {
    const names = {
        facebook: 'Facebook',
        instagram: 'Instagram',
        x: 'X (Twitter)',
        tiktok: 'TikTok',
        youtube: 'YouTube',
        pinterest: 'Pinterest',
    };
    return names[props.platform] || props.platform;
});
</script>
