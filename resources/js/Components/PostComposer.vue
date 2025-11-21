<template>
  <div class="post-composer">
    <div class="composer-header">
      <h2>Create Post</h2>
    </div>

    <div class="composer-body">
      <textarea
        v-model="content"
        placeholder="What's on your mind?"
        class="content-input"
        rows="5"
        @input="updatePreviews"
      ></textarea>

      <div class="media-upload">
        <input
          type="file"
          ref="mediaInput"
          multiple
          accept="image/*,video/*"
          @change="handleMediaUpload"
          class="file-input"
        />
        <button @click="$refs.mediaInput.click()" class="upload-btn">
          📎 Add Media
        </button>
      </div>

      <div v-if="mediaFiles.length" class="media-preview">
        <div v-for="(file, index) in mediaFiles" :key="index" class="media-item">
          <img v-if="file.type.startsWith('image')" :src="file.preview" alt="Preview" />
          <video v-else-if="file.type.startsWith('video')" :src="file.preview" controls></video>
          <button @click="removeMedia(index)" class="remove-btn">×</button>
        </div>
      </div>

      <div class="platform-selector">
        <h3>Select Platforms</h3>
        <div class="platforms">
          <label v-for="platform in platforms" :key="platform.id" class="platform-option">
            <input
              type="checkbox"
              v-model="selectedPlatforms"
              :value="platform.id"
              @change="updatePreviews"
            />
            <span>{{ platform.name }}</span>
          </label>
        </div>
      </div>

      <div class="ai-tools">
        <button @click="generateWithAI" class="ai-btn">
          🤖 Generate with AI
        </button>
        <button @click="improveContent" class="ai-btn">
          ✨ Improve Content
        </button>
        <button @click="generateHashtags" class="ai-btn">
          # Generate Hashtags
        </button>
      </div>

      <div class="schedule-options">
        <label>
          <input type="checkbox" v-model="schedulePost" />
          Schedule for later
        </label>
        <input
          v-if="schedulePost"
          type="datetime-local"
          v-model="scheduledAt"
          class="schedule-input"
        />
      </div>
    </div>

    <div class="composer-footer">
      <button @click="saveDraft" class="btn-secondary">Save Draft</button>
      <button @click="publish" class="btn-primary" :disabled="!canPublish">
        {{ schedulePost ? 'Schedule' : 'Publish' }}
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PostComposer',
  data() {
    return {
      content: '',
      mediaFiles: [],
      selectedPlatforms: [],
      schedulePost: false,
      scheduledAt: null,
      platforms: [
        { id: 'facebook', name: 'Facebook' },
        { id: 'instagram', name: 'Instagram' },
        { id: 'twitter', name: 'X (Twitter)' },
        { id: 'tiktok', name: 'TikTok' },
        { id: 'youtube', name: 'YouTube' },
        { id: 'pinterest', name: 'Pinterest' },
      ],
    };
  },
  computed: {
    canPublish() {
      return this.content.trim() && this.selectedPlatforms.length > 0;
    },
  },
  methods: {
    handleMediaUpload(event) {
      const files = Array.from(event.target.files);
      files.forEach((file) => {
        const reader = new FileReader();
        reader.onload = (e) => {
          this.mediaFiles.push({
            file,
            preview: e.target.result,
            type: file.type,
          });
        };
        reader.readAsDataURL(file);
      });
    },
    removeMedia(index) {
      this.mediaFiles.splice(index, 1);
    },
    updatePreviews() {
      this.$emit('content-changed', {
        content: this.content,
        platforms: this.selectedPlatforms,
      });
    },
    async generateWithAI() {
      // Call AI API to generate content
      console.log('Generating content with AI...');
    },
    async improveContent() {
      // Call AI API to improve content
      console.log('Improving content...');
    },
    async generateHashtags() {
      // Call AI API to generate hashtags
      console.log('Generating hashtags...');
    },
    saveDraft() {
      this.$emit('save-draft', this.getPostData());
    },
    publish() {
      this.$emit('publish', this.getPostData());
    },
    getPostData() {
      return {
        content: this.content,
        media: this.mediaFiles.map((m) => m.file),
        platforms: this.selectedPlatforms,
        scheduled_at: this.schedulePost ? this.scheduledAt : null,
      };
    },
  },
};
</script>

<style scoped>
.post-composer {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  padding: 20px;
}

.content-input {
  width: 100%;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 12px;
  font-size: 16px;
  resize: vertical;
}

.platform-selector {
  margin: 20px 0;
}

.platforms {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.platform-option {
  display: flex;
  align-items: center;
  gap: 5px;
}

.ai-tools {
  display: flex;
  gap: 10px;
  margin: 20px 0;
}

.composer-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
}

.btn-primary {
  background: #667eea;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

.btn-secondary {
  background: #e0e0e0;
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}
</style>
