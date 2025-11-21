<template>
  <div class="platform-preview">
    <div class="preview-tabs">
      <button
        v-for="platform in platforms"
        :key="platform"
        @click="selectedPlatform = platform"
        :class="['tab', { active: selectedPlatform === platform }]"
      >
        {{ platform }}
      </button>
    </div>

    <div class="preview-content">
      <div v-if="selectedPlatform === 'facebook'" class="facebook-preview">
        <div class="preview-header">
          <div class="user-info">
            <div class="avatar"></div>
            <div class="user-details">
              <div class="username">{{ username }}</div>
              <div class="timestamp">Just now</div>
            </div>
          </div>
        </div>
        <div class="preview-body">
          <p class="post-content">{{ content }}</p>
          <div v-if="media.length" class="media-grid">
            <img v-for="(item, index) in media" :key="index" :src="item" alt="Media" />
          </div>
        </div>
        <div class="preview-footer">
          <span>👍 Like</span>
          <span>💬 Comment</span>
          <span>↗️ Share</span>
        </div>
      </div>

      <div v-else-if="selectedPlatform === 'instagram'" class="instagram-preview">
        <div class="preview-header">
          <div class="user-info">
            <div class="avatar"></div>
            <span class="username">{{ username }}</span>
          </div>
        </div>
        <div class="preview-body">
          <div v-if="media.length" class="media-container">
            <img :src="media[0]" alt="Media" />
          </div>
          <div class="actions">
            <span>❤️</span>
            <span>💬</span>
            <span>📤</span>
          </div>
          <p class="post-content">
            <strong>{{ username }}</strong> {{ content }}
          </p>
        </div>
      </div>

      <div v-else-if="selectedPlatform === 'twitter'" class="twitter-preview">
        <div class="preview-header">
          <div class="avatar"></div>
          <div class="tweet-content">
            <div class="user-info">
              <span class="username">{{ username }}</span>
              <span class="handle">@{{ username }}</span>
              <span class="timestamp">· Just now</span>
            </div>
            <p class="post-content">{{ content }}</p>
            <div v-if="media.length" class="media-grid">
              <img v-for="(item, index) in media" :key="index" :src="item" alt="Media" />
            </div>
            <div class="actions">
              <span>💬</span>
              <span>🔄</span>
              <span>❤️</span>
              <span>📤</span>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="generic-preview">
        <p>Preview for {{ selectedPlatform }} coming soon...</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PlatformPreview',
  props: {
    content: {
      type: String,
      default: '',
    },
    media: {
      type: Array,
      default: () => [],
    },
    platforms: {
      type: Array,
      required: true,
    },
    username: {
      type: String,
      default: 'User',
    },
  },
  data() {
    return {
      selectedPlatform: this.platforms[0] || 'facebook',
    };
  },
  watch: {
    platforms(newPlatforms) {
      if (!newPlatforms.includes(this.selectedPlatform)) {
        this.selectedPlatform = newPlatforms[0] || 'facebook';
      }
    },
  },
};
</script>

<style scoped>
.platform-preview {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.preview-tabs {
  display: flex;
  border-bottom: 1px solid #e0e0e0;
  background: #f5f5f5;
}

.tab {
  padding: 12px 20px;
  border: none;
  background: transparent;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s;
}

.tab.active {
  background: white;
  border-bottom: 2px solid #667eea;
}

.preview-content {
  padding: 20px;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

.avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #ccc;
}

.username {
  font-weight: 600;
}

.post-content {
  margin: 10px 0;
  line-height: 1.5;
}

.media-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 10px;
  margin: 10px 0;
}

.media-grid img {
  width: 100%;
  border-radius: 8px;
}

.actions {
  display: flex;
  gap: 20px;
  margin: 10px 0;
  font-size: 20px;
}

.facebook-preview,
.instagram-preview,
.twitter-preview {
  max-width: 600px;
  margin: 0 auto;
}

.preview-footer {
  display: flex;
  gap: 20px;
  padding-top: 10px;
  border-top: 1px solid #e0e0e0;
  margin-top: 10px;
}
</style>
