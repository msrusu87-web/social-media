<template>
  <div class="platform-preview">
    <div class="tabs flex border-b mb-4">
      <button
        v-for="platform in platforms"
        :key="platform"
        @click="selectedPlatform = platform"
        :class="[
          'px-4 py-2 font-medium transition',
          selectedPlatform === platform
            ? 'border-b-2 border-blue-600 text-blue-600'
            : 'text-gray-500 hover:text-gray-700'
        ]"
      >
        {{ platformNames[platform] }}
      </button>
    </div>

    <!-- Facebook Preview -->
    <div v-if="selectedPlatform === 'facebook'" class="preview-container">
      <div class="bg-white rounded-lg shadow p-4 max-w-md mx-auto">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-10 h-10 bg-gray-300 rounded-full"></div>
          <div>
            <div class="font-semibold">{{ userName }}</div>
            <div class="text-xs text-gray-500">Just now • 🌍</div>
          </div>
        </div>
        <div class="text-sm mb-3 whitespace-pre-wrap">{{ content }}</div>
        <div v-if="media.length > 0" class="mb-3">
          <img :src="media[0]" class="w-full rounded" />
        </div>
        <div class="flex justify-between text-gray-500 text-sm pt-2 border-t">
          <span>👍 Like</span>
          <span>💬 Comment</span>
          <span>↗️ Share</span>
        </div>
      </div>
    </div>

    <!-- Instagram Preview -->
    <div v-if="selectedPlatform === 'instagram'" class="preview-container">
      <div class="bg-white rounded-lg shadow max-w-md mx-auto">
        <div class="flex items-center justify-between p-3">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-gray-300 rounded-full"></div>
            <span class="font-semibold text-sm">{{ userName }}</span>
          </div>
          <span class="text-2xl">⋯</span>
        </div>
        <div v-if="media.length > 0" class="w-full aspect-square bg-gray-200">
          <img :src="media[0]" class="w-full h-full object-cover" />
        </div>
        <div v-else class="w-full aspect-square bg-gray-200 flex items-center justify-center">
          <span class="text-gray-400">No media</span>
        </div>
        <div class="p-3">
          <div class="flex gap-3 mb-2 text-xl">
            <span>❤️</span>
            <span>💬</span>
            <span>↗️</span>
          </div>
          <div class="text-sm">
            <span class="font-semibold">{{ userName }}</span>
            <span class="ml-1">{{ truncateContent(content, 100) }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Twitter/X Preview -->
    <div v-if="selectedPlatform === 'twitter'" class="preview-container">
      <div class="bg-white rounded-lg shadow p-4 max-w-md mx-auto">
        <div class="flex gap-3">
          <div class="w-12 h-12 bg-gray-300 rounded-full flex-shrink-0"></div>
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
              <span class="font-bold">{{ userName }}</span>
              <span class="text-gray-500 text-sm">@{{ userName.toLowerCase().replace(' ', '') }}</span>
              <span class="text-gray-500 text-sm">· 1m</span>
            </div>
            <div class="text-sm mb-2 whitespace-pre-wrap">{{ content }}</div>
            <div v-if="media.length > 0" class="mb-3 rounded-2xl overflow-hidden">
              <img :src="media[0]" class="w-full" />
            </div>
            <div v-if="content.length > 280" class="text-red-500 text-xs mb-2">
              ⚠ Tweet exceeds 280 characters ({{ content.length }})
            </div>
            <div class="flex justify-between text-gray-500 text-sm max-w-md">
              <span>💬</span>
              <span>🔄</span>
              <span>❤️</span>
              <span>📊</span>
              <span>↗️</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- LinkedIn Preview -->
    <div v-if="selectedPlatform === 'linkedin'" class="preview-container">
      <div class="bg-white rounded-lg shadow p-4 max-w-md mx-auto">
        <div class="flex items-start gap-2 mb-3">
          <div class="w-12 h-12 bg-gray-300 rounded-full"></div>
          <div class="flex-1">
            <div class="font-semibold text-sm">{{ userName }}</div>
            <div class="text-xs text-gray-500">Professional Title</div>
            <div class="text-xs text-gray-500">1m • 🌍</div>
          </div>
        </div>
        <div class="text-sm mb-3 whitespace-pre-wrap">{{ content }}</div>
        <div v-if="media.length > 0" class="mb-3">
          <img :src="media[0]" class="w-full" />
        </div>
        <div class="flex justify-between text-gray-600 text-sm pt-2 border-t">
          <span>👍 Like</span>
          <span>💬 Comment</span>
          <span>↗️ Share</span>
          <span>📤 Send</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  content: {
    type: String,
    default: ''
  },
  media: {
    type: Array,
    default: () => []
  },
  platforms: {
    type: Array,
    default: () => ['facebook', 'instagram', 'twitter']
  },
  userName: {
    type: String,
    default: 'User Name'
  }
});

const selectedPlatform = ref(props.platforms[0] || 'facebook');

const platformNames = {
  facebook: 'Facebook',
  instagram: 'Instagram',
  twitter: 'X (Twitter)',
  linkedin: 'LinkedIn'
};

const truncateContent = (text, maxLength) => {
  if (text.length <= maxLength) return text;
  return text.substring(0, maxLength) + '...';
};
</script>

<style scoped>
.preview-container {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 2rem;
  border-radius: 0.5rem;
}
</style>
