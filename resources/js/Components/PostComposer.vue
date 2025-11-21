<template>
  <div class="post-composer bg-white rounded-lg shadow-md p-6">
    <div class="mb-4">
      <textarea
        v-model="content"
        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
        rows="5"
        placeholder="What's on your mind?"
        @input="handleInput"
      ></textarea>
      <div class="text-sm text-gray-500 mt-2 flex justify-between">
        <span>{{ characterCount }} / 5000 characters</span>
        <span v-if="twitterWarning" class="text-orange-500">
          ⚠ Exceeds Twitter's 280 character limit
        </span>
      </div>
    </div>

    <!-- AI Content Tools -->
    <div v-if="aiEnabled" class="mb-4 p-4 bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg">
      <h3 class="text-sm font-semibold text-gray-700 mb-2">✨ AI Tools</h3>
      <div class="flex gap-2 flex-wrap">
        <button
          @click="generateContent"
          class="px-3 py-1 bg-purple-600 text-white text-sm rounded-md hover:bg-purple-700 transition"
          :disabled="loading"
        >
          {{ loading ? 'Generating...' : 'Generate Content' }}
        </button>
        <button
          @click="improveContent"
          class="px-3 py-1 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition"
          :disabled="loading || !content"
        >
          Improve Content
        </button>
        <button
          @click="generateHashtags"
          class="px-3 py-1 bg-indigo-600 text-white text-sm rounded-md hover:indigo-700 transition"
          :disabled="loading || !content"
        >
          Generate Hashtags
        </button>
      </div>
      <div v-if="aiPrompt" class="mt-2">
        <input
          v-model="prompt"
          type="text"
          class="w-full p-2 border border-gray-300 rounded text-sm"
          placeholder="Enter a prompt for AI generation..."
        />
      </div>
    </div>

    <!-- Media Upload -->
    <div class="mb-4">
      <label class="block text-sm font-medium text-gray-700 mb-2">Media</label>
      <div class="flex items-center gap-2">
        <input
          ref="fileInput"
          type="file"
          multiple
          accept="image/*,video/*"
          @change="handleFileUpload"
          class="hidden"
        />
        <button
          @click="$refs.fileInput.click()"
          class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition"
        >
          📷 Add Media
        </button>
      </div>
      <div v-if="mediaFiles.length > 0" class="mt-3 grid grid-cols-3 gap-2">
        <div
          v-for="(file, index) in mediaFiles"
          :key="index"
          class="relative group"
        >
          <img
            :src="file.preview"
            class="w-full h-24 object-cover rounded"
          />
          <button
            @click="removeMedia(index)"
            class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition"
          >
            ×
          </button>
        </div>
      </div>
    </div>

    <!-- Platform Selection -->
    <div class="mb-4">
      <label class="block text-sm font-medium text-gray-700 mb-2">Publish To</label>
      <div class="flex gap-4">
        <label
          v-for="platform in availablePlatforms"
          :key="platform.id"
          class="flex items-center gap-2 cursor-pointer"
        >
          <input
            v-model="selectedPlatforms"
            type="checkbox"
            :value="platform.id"
            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
          />
          <span class="text-sm">{{ platform.name }}</span>
        </label>
      </div>
    </div>

    <!-- Schedule -->
    <div class="mb-4">
      <label class="flex items-center gap-2 cursor-pointer">
        <input
          v-model="schedulePost"
          type="checkbox"
          class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
        />
        <span class="text-sm font-medium text-gray-700">Schedule for later</span>
      </label>
      <div v-if="schedulePost" class="mt-2">
        <input
          v-model="scheduledAt"
          type="datetime-local"
          class="w-full p-2 border border-gray-300 rounded"
        />
      </div>
    </div>

    <!-- Actions -->
    <div class="flex justify-between items-center">
      <button
        @click="saveDraft"
        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition"
      >
        Save Draft
      </button>
      <button
        @click="publish"
        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition font-medium"
        :disabled="!canPublish"
      >
        {{ schedulePost ? 'Schedule Post' : 'Publish Now' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
  aiEnabled: {
    type: Boolean,
    default: false
  },
  availablePlatforms: {
    type: Array,
    default: () => []
  }
});

const content = ref('');
const prompt = ref('');
const aiPrompt = ref(false);
const loading = ref(false);
const mediaFiles = ref([]);
const selectedPlatforms = ref([]);
const schedulePost = ref(false);
const scheduledAt = ref('');

const characterCount = computed(() => content.value.length);
const twitterWarning = computed(() => 
  selectedPlatforms.value.includes('twitter') && content.value.length > 280
);
const canPublish = computed(() => 
  content.value.trim().length > 0 && selectedPlatforms.value.length > 0
);

const handleInput = () => {
  // Real-time character count
};

const generateContent = async () => {
  aiPrompt.value = true;
  if (!prompt.value) return;
  
  loading.value = true;
  try {
    const response = await axios.post('/api/ai/generate', {
      prompt: prompt.value,
      platforms: selectedPlatforms.value
    });
    content.value = response.data.content;
  } catch (error) {
    console.error('Failed to generate content:', error);
  } finally {
    loading.value = false;
    aiPrompt.value = false;
  }
};

const improveContent = async () => {
  loading.value = true;
  try {
    const response = await axios.post('/api/ai/improve', {
      content: content.value
    });
    content.value = response.data.content;
  } catch (error) {
    console.error('Failed to improve content:', error);
  } finally {
    loading.value = false;
  }
};

const generateHashtags = async () => {
  loading.value = true;
  try {
    const response = await axios.post('/api/ai/hashtags', {
      content: content.value
    });
    content.value += '\n\n' + response.data.hashtags.join(' ');
  } catch (error) {
    console.error('Failed to generate hashtags:', error);
  } finally {
    loading.value = false;
  }
};

const handleFileUpload = (event) => {
  const files = Array.from(event.target.files);
  files.forEach(file => {
    const reader = new FileReader();
    reader.onload = (e) => {
      mediaFiles.value.push({
        file,
        preview: e.target.result
      });
    };
    reader.readAsDataURL(file);
  });
};

const removeMedia = (index) => {
  mediaFiles.value.splice(index, 1);
};

const saveDraft = () => {
  submitPost('draft');
};

const publish = () => {
  const status = schedulePost.value ? 'scheduled' : 'published';
  submitPost(status);
};

const submitPost = (status) => {
  const formData = new FormData();
  formData.append('content', content.value);
  formData.append('status', status);
  formData.append('platforms', JSON.stringify(selectedPlatforms.value));
  
  if (schedulePost.value && scheduledAt.value) {
    formData.append('scheduled_at', scheduledAt.value);
  }
  
  mediaFiles.value.forEach((item, index) => {
    formData.append(`media[${index}]`, item.file);
  });

  router.post('/posts', formData, {
    onSuccess: () => {
      // Reset form
      content.value = '';
      mediaFiles.value = [];
      selectedPlatforms.value = [];
      schedulePost.value = false;
      scheduledAt.value = '';
    }
  });
};
</script>
