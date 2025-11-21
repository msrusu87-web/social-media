<template>
  <div class="platform-preview">
    <div class="preview-tabs">
      <button
        v-for="platform in platforms"
        :key="platform.id"
        @click="selectedPlatform = platform.id"
        :class="['tab', { active: selectedPlatform === platform.id }]"
      >
        {{ platform.name }}
      </button>
    </div>

    <div class="preview-container">
      <div v-if="selectedPlatform === 'facebook'" class="facebook-preview">
        <div class="fb-header">
          <div class="fb-avatar"></div>
          <div class="fb-info">
            <div class="fb-name">{{ userName }}</div>
            <div class="fb-time">Just now</div>
          </div>
        </div>
        <div class="fb-content">{{ content }}</div>
        <div v-if="media.length > 0" class="fb-media">
          <img v-if="media[0]" :src="media[0]" alt="Media" />
        </div>
        <div class="fb-actions">
          <button>👍 Like</button>
          <button>💬 Comment</button>
          <button>↗️ Share</button>
        </div>
      </div>

      <div v-if="selectedPlatform === 'instagram'" class="instagram-preview">
        <div class="ig-header">
          <div class="ig-avatar"></div>
          <div class="ig-username">{{ userName }}</div>
        </div>
        <div v-if="media.length > 0" class="ig-media">
          <img :src="media[0]" alt="Media" />
        </div>
        <div class="ig-actions">
          <span>❤️</span>
          <span>💬</span>
          <span>📤</span>
        </div>
        <div class="ig-content">
          <span class="ig-username">{{ userName }}</span> {{ content }}
        </div>
      </div>

      <div v-if="selectedPlatform === 'x'" class="x-preview">
        <div class="x-header">
          <div class="x-avatar"></div>
          <div class="x-info">
            <div class="x-name">{{ userName }}</div>
            <div class="x-handle">@{{ userName.toLowerCase().replace(' ', '_') }}</div>
          </div>
        </div>
        <div class="x-content">{{ truncate(content, 280) }}</div>
        <div v-if="media.length > 0" class="x-media">
          <img :src="media[0]" alt="Media" />
        </div>
        <div class="x-actions">
          <button>💬</button>
          <button>🔁</button>
          <button>❤️</button>
          <button>📊</button>
        </div>
      </div>

      <div v-if="selectedPlatform === 'tiktok'" class="tiktok-preview">
        <div class="tiktok-container">
          <div v-if="media.length > 0" class="tiktok-video">
            <video :src="media[0]" controls></video>
          </div>
          <div class="tiktok-overlay">
            <div class="tiktok-info">
              <div class="tiktok-username">@{{ userName.toLowerCase().replace(' ', '_') }}</div>
              <div class="tiktok-caption">{{ content }}</div>
            </div>
            <div class="tiktok-sidebar">
              <button>❤️</button>
              <button>💬</button>
              <button>↗️</button>
            </div>
          </div>
        </div>
      </div>

      <div v-if="selectedPlatform === 'youtube'" class="youtube-preview">
        <div v-if="media.length > 0" class="yt-video">
          <video :src="media[0]" controls></video>
        </div>
        <div class="yt-info">
          <div class="yt-title">{{ truncate(content, 100) }}</div>
          <div class="yt-metadata">
            <span>{{ userName }}</span> • 0 views • Just now
          </div>
        </div>
      </div>

      <div v-if="selectedPlatform === 'pinterest'" class="pinterest-preview">
        <div class="pin-card">
          <div v-if="media.length > 0" class="pin-image">
            <img :src="media[0]" alt="Pin" />
          </div>
          <div class="pin-content">
            <h3>{{ truncate(content, 100) }}</h3>
            <div class="pin-user">{{ userName }}</div>
          </div>
        </div>
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
    userName: {
      type: String,
      default: 'User Name',
    },
  },
  data() {
    return {
      selectedPlatform: 'facebook',
      platforms: [
        { id: 'facebook', name: 'Facebook' },
        { id: 'instagram', name: 'Instagram' },
        { id: 'x', name: 'X' },
        { id: 'tiktok', name: 'TikTok' },
        { id: 'youtube', name: 'YouTube' },
        { id: 'pinterest', name: 'Pinterest' },
      ],
    };
  },
  methods: {
    truncate(text, length) {
      if (text.length <= length) return text;
      return text.substring(0, length) + '...';
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
  background: #f8f8f8;
}

.tab {
  padding: 12px 20px;
  border: none;
  background: none;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  color: #666;
  transition: all 0.2s;
}

.tab.active {
  color: #4CAF50;
  border-bottom: 2px solid #4CAF50;
  background: white;
}

.preview-container {
  padding: 24px;
  min-height: 400px;
}

/* Facebook Preview */
.facebook-preview {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
}

.fb-header {
  display: flex;
  align-items: center;
  margin-bottom: 12px;
}

.fb-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #e0e0e0;
  margin-right: 12px;
}

.fb-name {
  font-weight: 600;
  font-size: 15px;
}

.fb-time {
  color: #65676b;
  font-size: 13px;
}

.fb-content {
  margin: 12px 0;
  font-size: 15px;
  line-height: 1.4;
}

.fb-media img {
  width: 100%;
  border-radius: 8px;
  margin: 12px 0;
}

.fb-actions {
  display: flex;
  gap: 16px;
  padding-top: 12px;
  border-top: 1px solid #e0e0e0;
}

.fb-actions button {
  border: none;
  background: none;
  cursor: pointer;
  font-size: 15px;
  color: #65676b;
}

/* Instagram Preview */
.instagram-preview {
  max-width: 500px;
  margin: 0 auto;
}

.ig-header {
  display: flex;
  align-items: center;
  margin-bottom: 12px;
}

.ig-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
  margin-right: 12px;
}

.ig-username {
  font-weight: 600;
  font-size: 14px;
}

.ig-media img {
  width: 100%;
  aspect-ratio: 1;
  object-fit: cover;
  margin: 12px 0;
}

.ig-actions {
  display: flex;
  gap: 16px;
  font-size: 24px;
  margin: 12px 0;
}

.ig-content {
  font-size: 14px;
  line-height: 1.4;
}

/* X (Twitter) Preview */
.x-preview {
  max-width: 600px;
}

.x-header {
  display: flex;
  align-items: flex-start;
  margin-bottom: 12px;
}

.x-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: #1DA1F2;
  margin-right: 12px;
}

.x-name {
  font-weight: 700;
  font-size: 15px;
}

.x-handle {
  color: #536471;
  font-size: 15px;
}

.x-content {
  font-size: 15px;
  line-height: 1.4;
  margin: 12px 0;
}

.x-media img {
  width: 100%;
  border-radius: 16px;
  margin: 12px 0;
}

.x-actions {
  display: flex;
  gap: 64px;
  margin-top: 12px;
}

.x-actions button {
  border: none;
  background: none;
  cursor: pointer;
  font-size: 18px;
}

/* TikTok Preview */
.tiktok-preview {
  background: black;
  border-radius: 8px;
  overflow: hidden;
  max-width: 400px;
  margin: 0 auto;
}

.tiktok-container {
  position: relative;
  aspect-ratio: 9/16;
}

.tiktok-video video {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.tiktok-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 20px;
  color: white;
}

.tiktok-username {
  font-weight: 600;
  margin-bottom: 8px;
}

.tiktok-sidebar {
  position: absolute;
  right: 12px;
  bottom: 100px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.tiktok-sidebar button {
  border: none;
  background: none;
  color: white;
  font-size: 32px;
  cursor: pointer;
}

/* YouTube Preview */
.youtube-preview {
  max-width: 640px;
}

.yt-video video {
  width: 100%;
  aspect-ratio: 16/9;
  background: black;
  border-radius: 12px;
}

.yt-info {
  padding: 12px 0;
}

.yt-title {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 8px;
}

.yt-metadata {
  color: #606060;
  font-size: 14px;
}

/* Pinterest Preview */
.pinterest-preview {
  max-width: 300px;
  margin: 0 auto;
}

.pin-card {
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.pin-image img {
  width: 100%;
  display: block;
}

.pin-content {
  padding: 16px;
}

.pin-content h3 {
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 8px;
}

.pin-user {
  color: #767676;
  font-size: 14px;
}
</style>
