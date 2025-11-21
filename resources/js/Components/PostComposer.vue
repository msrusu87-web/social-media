<template>
  <div class="post-composer">
    <div class="composer-header">
      <h2>Create Post</h2>
    </div>

    <div class="composer-body">
      <textarea
        v-model="content"
        class="composer-textarea"
        placeholder="What's on your mind?"
        rows="6"
        @input="updateCharCount"
      ></textarea>

      <div class="char-count" :class="{ 'warning': charCount > 4500 }">
        {{ charCount }} / 5000 characters
      </div>

      <div class="media-upload">
        <label for="media-input" class="media-label">
          <svg class="media-icon" fill="currentColor" viewBox="0 0 20 20">
            <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" />
          </svg>
          Add Media
        </label>
        <input
          id="media-input"
          type="file"
          multiple
          accept="image/*,video/*"
          @change="handleMediaUpload"
          class="hidden"
        />
      </div>

      <div v-if="mediaFiles.length > 0" class="media-preview">
        <div v-for="(file, index) in mediaFiles" :key="index" class="media-item">
          <img v-if="file.type.startsWith('image/')" :src="file.preview" alt="Media preview" />
          <video v-else :src="file.preview" controls></video>
          <button @click="removeMedia(index)" class="remove-media">×</button>
        </div>
      </div>

      <div class="platform-selector">
        <h3>Select Platforms</h3>
        <div class="platforms-grid">
          <label v-for="platform in platforms" :key="platform.id" class="platform-checkbox">
            <input
              type="checkbox"
              :value="platform.id"
              v-model="selectedPlatforms"
            />
            <span>{{ platform.name }}</span>
          </label>
        </div>
      </div>

      <div class="ai-tools">
        <button @click="generateContent" class="btn btn-ai" :disabled="aiLoading">
          <span v-if="!aiLoading">✨ Generate with AI</span>
          <span v-else>Generating...</span>
        </button>
        <button @click="generateHashtags" class="btn btn-ai" :disabled="aiLoading">
          <span v-if="!aiLoading"># Generate Hashtags</span>
          <span v-else>Generating...</span>
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
      <button @click="saveDraft" class="btn btn-secondary">Save Draft</button>
      <button @click="publish" class="btn btn-primary" :disabled="!canPublish">
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
      charCount: 0,
      mediaFiles: [],
      selectedPlatforms: [],
      schedulePost: false,
      scheduledAt: null,
      aiLoading: false,
      platforms: [
        { id: 'facebook', name: 'Facebook' },
        { id: 'instagram', name: 'Instagram' },
        { id: 'x', name: 'X (Twitter)' },
        { id: 'tiktok', name: 'TikTok' },
        { id: 'youtube', name: 'YouTube' },
        { id: 'pinterest', name: 'Pinterest' },
      ],
    };
  },
  computed: {
    canPublish() {
      return this.content.trim().length > 0 && this.selectedPlatforms.length > 0;
    },
  },
  methods: {
    updateCharCount() {
      this.charCount = this.content.length;
    },
    handleMediaUpload(event) {
      const files = Array.from(event.target.files);
      files.forEach(file => {
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
    async generateContent() {
      this.aiLoading = true;
      try {
        const response = await fetch('/api/ai/generate', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ prompt: this.content || 'Generate engaging social media content' }),
        });
        const data = await response.json();
        if (data.success) {
          this.content = data.content;
          this.updateCharCount();
        }
      } catch (error) {
        console.error('AI generation failed:', error);
      } finally {
        this.aiLoading = false;
      }
    },
    async generateHashtags() {
      if (!this.content) return;
      this.aiLoading = true;
      try {
        const response = await fetch('/api/ai/hashtags', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ content: this.content }),
        });
        const data = await response.json();
        if (data.success) {
          this.content += '\n\n' + data.hashtags.join(' ');
          this.updateCharCount();
        }
      } catch (error) {
        console.error('Hashtag generation failed:', error);
      } finally {
        this.aiLoading = false;
      }
    },
    async saveDraft() {
      await this.savePost('draft');
    },
    async publish() {
      await this.savePost(this.schedulePost ? 'scheduled' : 'published');
    },
    async savePost(status) {
      const formData = new FormData();
      formData.append('content', this.content);
      formData.append('status', status);
      formData.append('platforms', JSON.stringify(this.selectedPlatforms));
      
      if (this.schedulePost && this.scheduledAt) {
        formData.append('scheduled_at', this.scheduledAt);
      }

      this.mediaFiles.forEach((media, index) => {
        formData.append(`media[${index}]`, media.file);
      });

      try {
        const response = await fetch('/api/posts', {
          method: 'POST',
          body: formData,
        });
        const data = await response.json();
        if (data.success) {
          this.$emit('post-created', data.post);
          this.resetForm();
        }
      } catch (error) {
        console.error('Post creation failed:', error);
      }
    },
    resetForm() {
      this.content = '';
      this.charCount = 0;
      this.mediaFiles = [];
      this.selectedPlatforms = [];
      this.schedulePost = false;
      this.scheduledAt = null;
    },
  },
};
</script>

<style scoped>
.post-composer {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  padding: 24px;
}

.composer-header h2 {
  margin: 0 0 20px 0;
  font-size: 24px;
  font-weight: 600;
}

.composer-textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 16px;
  resize: vertical;
}

.char-count {
  text-align: right;
  margin-top: 8px;
  color: #666;
  font-size: 14px;
}

.char-count.warning {
  color: #ff6b6b;
}

.media-upload {
  margin: 20px 0;
}

.media-label {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: #f0f0f0;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.2s;
}

.media-label:hover {
  background: #e0e0e0;
}

.media-icon {
  width: 20px;
  height: 20px;
}

.hidden {
  display: none;
}

.media-preview {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 12px;
  margin: 20px 0;
}

.media-item {
  position: relative;
  aspect-ratio: 1;
  overflow: hidden;
  border-radius: 8px;
}

.media-item img,
.media-item video {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.remove-media {
  position: absolute;
  top: 8px;
  right: 8px;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  border: none;
  border-radius: 50%;
  width: 28px;
  height: 28px;
  cursor: pointer;
  font-size: 20px;
}

.platform-selector {
  margin: 20px 0;
}

.platform-selector h3 {
  margin-bottom: 12px;
  font-size: 16px;
  font-weight: 600;
}

.platforms-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
}

.platform-checkbox {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 6px;
  cursor: pointer;
}

.ai-tools {
  display: flex;
  gap: 12px;
  margin: 20px 0;
}

.btn {
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.2s;
}

.btn-ai {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.btn-primary {
  background: #4CAF50;
  color: white;
}

.btn-secondary {
  background: #f0f0f0;
  color: #333;
}

.composer-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 20px;
}

.schedule-options {
  margin: 20px 0;
}

.schedule-input {
  margin-left: 12px;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 6px;
}
</style>
