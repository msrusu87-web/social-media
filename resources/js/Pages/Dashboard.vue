<template>
  <div class="dashboard">
    <div class="dashboard-header">
      <h1>Welcome, {{ user.name }}!</h1>
      <button @click="createPost" class="btn-primary">Create Post</button>
    </div>

    <div class="dashboard-stats">
      <div class="stat-card">
        <div class="stat-icon">📝</div>
        <div class="stat-content">
          <h3>{{ stats.posts }}</h3>
          <p>Posts</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-content">
          <h3>{{ stats.followers }}</h3>
          <p>Followers</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">❤️</div>
        <div class="stat-content">
          <h3>{{ stats.likes }}</h3>
          <p>Total Likes</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">📊</div>
        <div class="stat-content">
          <h3>{{ stats.engagement }}</h3>
          <p>Engagement Rate</p>
        </div>
      </div>
    </div>

    <div class="dashboard-grid">
      <div class="main-content">
        <div class="section">
          <h2>Recent Posts</h2>
          <div class="recent-posts">
            <div
              v-for="post in recentPosts"
              :key="post.id"
              class="post-preview"
              @click="viewPost(post)"
            >
              <div class="post-preview-header">
                <span class="post-status" :class="post.status">{{ post.status }}</span>
                <span class="post-date">{{ formatDate(post.created_at) }}</span>
              </div>
              <p class="post-preview-content">{{ truncate(post.content, 100) }}</p>
              <div class="post-preview-platforms">
                <span
                  v-for="platform in post.platforms"
                  :key="platform"
                  class="platform-badge"
                >
                  {{ platform }}
                </span>
              </div>
              <div class="post-preview-stats">
                <span>❤️ {{ post.likes_count }}</span>
                <span>💬 {{ post.comments_count }}</span>
                <span>🔄 {{ post.reposts_count }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="section">
          <h2>Scheduled Posts</h2>
          <div class="scheduled-posts">
            <div
              v-for="post in scheduledPosts"
              :key="post.id"
              class="scheduled-post"
            >
              <div class="scheduled-time">
                📅 {{ formatDate(post.scheduled_at) }}
              </div>
              <p>{{ truncate(post.content, 80) }}</p>
              <div class="scheduled-actions">
                <button @click="editPost(post)">Edit</button>
                <button @click="deletePost(post)">Delete</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="sidebar">
        <div class="section">
          <h3>Connected Platforms</h3>
          <div class="connected-platforms">
            <div
              v-for="platform in connectedPlatforms"
              :key="platform.id"
              class="platform-item"
              :class="{ active: platform.is_active }"
            >
              <span>{{ platform.platform }}</span>
              <span class="status-indicator"></span>
            </div>
          </div>
          <button @click="connectPlatform" class="btn-secondary">
            + Connect Platform
          </button>
        </div>

        <div class="section">
          <h3>Quick Stats</h3>
          <div class="quick-stats">
            <div class="quick-stat">
              <span>Posts This Month</span>
              <strong>{{ stats.monthlyPosts }}</strong>
            </div>
            <div class="quick-stat">
              <span>Avg. Engagement</span>
              <strong>{{ stats.avgEngagement }}%</strong>
            </div>
            <div class="quick-stat">
              <span>Best Platform</span>
              <strong>{{ stats.bestPlatform }}</strong>
            </div>
          </div>
        </div>

        <div class="section">
          <h3>Subscription</h3>
          <div v-if="subscription" class="subscription-info">
            <p><strong>{{ subscription.plan.name }}</strong></p>
            <p>Renews {{ formatDate(subscription.current_period_end) }}</p>
            <button @click="manageSub scription" class="btn-secondary">
              Manage
            </button>
          </div>
          <div v-else class="no-subscription">
            <p>No active subscription</p>
            <button @click="upgradePlan" class="btn-primary">
              Upgrade Now
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Dashboard',
  props: {
    user: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      stats: {
        posts: 0,
        followers: 0,
        likes: 0,
        engagement: '0%',
        monthlyPosts: 0,
        avgEngagement: 0,
        bestPlatform: 'N/A',
      },
      recentPosts: [],
      scheduledPosts: [],
      connectedPlatforms: [],
      subscription: null,
    };
  },
  mounted() {
    this.loadDashboardData();
  },
  methods: {
    async loadDashboardData() {
      // Simulate API calls
      try {
        // Load stats, posts, platforms, subscription
      } catch (error) {
        console.error('Failed to load dashboard data:', error);
      }
    },
    createPost() {
      this.$router.push('/posts/create');
    },
    viewPost(post) {
      this.$router.push(`/posts/${post.id}`);
    },
    editPost(post) {
      this.$router.push(`/posts/${post.id}/edit`);
    },
    async deletePost(post) {
      if (confirm('Are you sure you want to delete this post?')) {
        // Call API to delete post
      }
    },
    connectPlatform() {
      this.$router.push('/settings/connections');
    },
    manageSubscription() {
      this.$router.push('/subscriptions');
    },
    upgradePlan() {
      this.$router.push('/plans');
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString();
    },
    truncate(text, length) {
      return text.length > length ? text.substring(0, length) + '...' : text;
    },
  },
};
</script>

<style scoped>
.dashboard {
  padding: 20px;
  max-width: 1400px;
  margin: 0 auto;
}

.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.dashboard-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 15px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.stat-icon {
  font-size: 36px;
}

.stat-content h3 {
  font-size: 28px;
  margin: 0;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 20px;
}

.section {
  background: white;
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.post-preview {
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 15px;
  margin-bottom: 15px;
  cursor: pointer;
  transition: all 0.3s;
}

.post-preview:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.post-status {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  text-transform: uppercase;
}

.post-status.published {
  background: #d4edda;
  color: #155724;
}

.post-status.scheduled {
  background: #fff3cd;
  color: #856404;
}

.post-status.draft {
  background: #e2e3e5;
  color: #383d41;
}

.platform-badge {
  display: inline-block;
  background: #667eea;
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  margin-right: 5px;
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
  width: 100%;
  margin-top: 10px;
}
</style>
