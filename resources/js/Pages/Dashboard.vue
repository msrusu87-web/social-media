<template>
  <div class="dashboard">
    <div class="dashboard-header">
      <h1>Dashboard</h1>
      <p>Welcome back, {{ user.name }}!</p>
    </div>

    <div class="dashboard-stats">
      <div class="stat-card">
        <div class="stat-icon posts">📝</div>
        <div class="stat-content">
          <div class="stat-value">{{ stats.posts }}</div>
          <div class="stat-label">Posts</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon followers">👥</div>
        <div class="stat-content">
          <div class="stat-value">{{ stats.followers }}</div>
          <div class="stat-label">Followers</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon engagement">❤️</div>
        <div class="stat-content">
          <div class="stat-value">{{ stats.engagement }}</div>
          <div class="stat-label">Engagement</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon ai-credits">✨</div>
        <div class="stat-content">
          <div class="stat-value">{{ stats.aiCredits }}</div>
          <div class="stat-label">AI Credits</div>
        </div>
      </div>
    </div>

    <div class="dashboard-grid">
      <div class="dashboard-main">
        <div class="quick-actions">
          <h2>Quick Actions</h2>
          <div class="actions-grid">
            <button @click="$router.push('/posts/create')" class="action-card primary">
              <span class="action-icon">✍️</span>
              <span class="action-label">Create Post</span>
            </button>
            <button @click="showScheduler = true" class="action-card">
              <span class="action-icon">📅</span>
              <span class="action-label">Schedule</span>
            </button>
            <button @click="$router.push('/analytics')" class="action-card">
              <span class="action-icon">📊</span>
              <span class="action-label">Analytics</span>
            </button>
            <button @click="$router.push('/settings')" class="action-card">
              <span class="action-icon">⚙️</span>
              <span class="action-label">Settings</span>
            </button>
          </div>
        </div>

        <div class="recent-posts">
          <div class="section-header">
            <h2>Recent Posts</h2>
            <a href="/posts" class="view-all">View All →</a>
          </div>
          <div class="posts-list">
            <div v-for="post in recentPosts" :key="post.id" class="post-item">
              <div class="post-info">
                <div class="post-title">{{ truncate(post.content, 60) }}</div>
                <div class="post-meta">
                  <span class="post-status" :class="post.status">{{ post.status }}</span>
                  <span class="post-time">{{ formatTime(post.created_at) }}</span>
                </div>
              </div>
              <div class="post-stats">
                <span>❤️ {{ post.likes_count }}</span>
                <span>💬 {{ post.comments_count }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="scheduled-posts">
          <div class="section-header">
            <h2>Scheduled Posts</h2>
            <a href="/posts?status=scheduled" class="view-all">View All →</a>
          </div>
          <div v-if="scheduledPosts.length === 0" class="empty-state">
            <p>No scheduled posts</p>
            <button @click="showScheduler = true" class="btn-schedule">Schedule a Post</button>
          </div>
          <div v-else class="posts-list">
            <div v-for="post in scheduledPosts" :key="post.id" class="post-item">
              <div class="post-info">
                <div class="post-title">{{ truncate(post.content, 60) }}</div>
                <div class="post-meta">
                  <span class="scheduled-time">
                    📅 {{ formatDateTime(post.scheduled_at) }}
                  </span>
                </div>
              </div>
              <div class="post-platforms">
                <span v-for="platform in post.platforms" :key="platform" class="platform-badge">
                  {{ platform }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="dashboard-sidebar">
        <div class="connected-accounts">
          <h3>Connected Accounts</h3>
          <div class="accounts-list">
            <div
              v-for="platform in platforms"
              :key="platform.id"
              class="account-item"
              :class="{ connected: platform.connected }"
            >
              <span class="platform-name">{{ platform.name }}</span>
              <button
                v-if="platform.connected"
                @click="disconnect(platform.id)"
                class="btn-disconnect"
              >
                Connected ✓
              </button>
              <button
                v-else
                @click="connect(platform.id)"
                class="btn-connect"
              >
                Connect
              </button>
            </div>
          </div>
        </div>

        <div class="subscription-card">
          <h3>Your Plan</h3>
          <div v-if="subscription" class="plan-info">
            <div class="plan-name">{{ subscription.plan.name }}</div>
            <div class="plan-details">
              <p>{{ subscription.plan.posts_limit }} posts/month</p>
              <p>{{ subscription.plan.ai_credits }} AI credits</p>
            </div>
            <button @click="$router.push('/subscriptions')" class="btn-upgrade">
              Manage Subscription
            </button>
          </div>
          <div v-else class="no-subscription">
            <p>No active subscription</p>
            <button @click="$router.push('/subscriptions')" class="btn-upgrade">
              View Plans
            </button>
          </div>
        </div>

        <div class="activity-feed">
          <h3>Recent Activity</h3>
          <div class="activity-list">
            <div v-for="activity in recentActivity" :key="activity.id" class="activity-item">
              <div class="activity-icon">{{ activity.icon }}</div>
              <div class="activity-content">
                <div class="activity-text">{{ activity.text }}</div>
                <div class="activity-time">{{ formatTime(activity.time) }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Dashboard',
  data() {
    return {
      user: {
        name: 'User Name',
      },
      stats: {
        posts: 0,
        followers: 0,
        engagement: 0,
        aiCredits: 0,
      },
      recentPosts: [],
      scheduledPosts: [],
      subscription: null,
      showScheduler: false,
      platforms: [
        { id: 'facebook', name: 'Facebook', connected: false },
        { id: 'instagram', name: 'Instagram', connected: false },
        { id: 'x', name: 'X (Twitter)', connected: false },
        { id: 'tiktok', name: 'TikTok', connected: false },
        { id: 'youtube', name: 'YouTube', connected: false },
        { id: 'pinterest', name: 'Pinterest', connected: false },
      ],
      recentActivity: [],
    };
  },
  mounted() {
    this.loadDashboardData();
  },
  methods: {
    async loadDashboardData() {
      try {
        const response = await fetch('/api/dashboard');
        const data = await response.json();
        
        this.user = data.user;
        this.stats = data.stats;
        this.recentPosts = data.recentPosts;
        this.scheduledPosts = data.scheduledPosts;
        this.subscription = data.subscription;
        this.recentActivity = data.recentActivity;
        
        // Update platform connection status
        if (data.connections) {
          data.connections.forEach(conn => {
            const platform = this.platforms.find(p => p.id === conn.platform);
            if (platform) platform.connected = conn.is_active;
          });
        }
      } catch (error) {
        console.error('Failed to load dashboard data:', error);
      }
    },
    async connect(platformId) {
      try {
        const response = await fetch(`/api/social/auth-url/${platformId}`);
        const data = await response.json();
        if (data.success) {
          window.location.href = data.url;
        }
      } catch (error) {
        console.error('Failed to connect platform:', error);
      }
    },
    async disconnect(platformId) {
      try {
        await fetch(`/api/social/disconnect/${platformId}`, { method: 'POST' });
        const platform = this.platforms.find(p => p.id === platformId);
        if (platform) platform.connected = false;
      } catch (error) {
        console.error('Failed to disconnect platform:', error);
      }
    },
    truncate(text, length) {
      if (text.length <= length) return text;
      return text.substring(0, length) + '...';
    },
    formatTime(timestamp) {
      const date = new Date(timestamp);
      const now = new Date();
      const diff = now - date;
      const hours = Math.floor(diff / 3600000);
      const days = Math.floor(diff / 86400000);

      if (hours < 24) return `${hours}h ago`;
      if (days < 7) return `${days}d ago`;
      return date.toLocaleDateString();
    },
    formatDateTime(timestamp) {
      return new Date(timestamp).toLocaleString();
    },
  },
};
</script>

<style scoped>
.dashboard {
  padding: 24px;
  max-width: 1400px;
  margin: 0 auto;
}

.dashboard-header h1 {
  font-size: 32px;
  font-weight: 700;
  margin-bottom: 8px;
}

.dashboard-header p {
  color: #666;
  font-size: 16px;
}

.dashboard-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin: 32px 0;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  display: flex;
  align-items: center;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.stat-icon {
  width: 56px;
  height: 56px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  margin-right: 16px;
}

.stat-icon.posts {
  background: #e3f2fd;
}

.stat-icon.followers {
  background: #f3e5f5;
}

.stat-icon.engagement {
  background: #fce4ec;
}

.stat-icon.ai-credits {
  background: #e8eaf6;
}

.stat-value {
  font-size: 28px;
  font-weight: 700;
  margin-bottom: 4px;
}

.stat-label {
  color: #666;
  font-size: 14px;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: 1fr 350px;
  gap: 24px;
}

.dashboard-main,
.dashboard-sidebar > div {
  background: white;
  border-radius: 12px;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.section-header h2 {
  font-size: 20px;
  font-weight: 600;
  margin: 0;
}

.view-all {
  color: #4CAF50;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}

.action-card {
  padding: 20px;
  border: 2px solid #e0e0e0;
  background: white;
  border-radius: 12px;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  transition: all 0.2s;
}

.action-card:hover {
  border-color: #4CAF50;
  transform: translateY(-2px);
}

.action-card.primary {
  background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
  border-color: #4CAF50;
  color: white;
}

.action-icon {
  font-size: 32px;
}

.action-label {
  font-weight: 600;
  font-size: 14px;
}

.posts-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.post-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  transition: background 0.2s;
}

.post-item:hover {
  background: #f8f8f8;
}

.post-title {
  font-weight: 500;
  margin-bottom: 8px;
}

.post-meta {
  display: flex;
  gap: 12px;
  font-size: 14px;
  color: #666;
}

.post-status {
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
}

.post-status.published {
  background: #e8f5e9;
  color: #2e7d32;
}

.post-status.draft {
  background: #f5f5f5;
  color: #666;
}

.post-status.scheduled {
  background: #fff3e0;
  color: #f57c00;
}

.post-stats {
  display: flex;
  gap: 16px;
  font-size: 14px;
  color: #666;
}

.empty-state {
  text-align: center;
  padding: 40px 20px;
  color: #666;
}

.btn-schedule,
.btn-upgrade,
.btn-connect,
.btn-disconnect {
  margin-top: 16px;
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.2s;
}

.btn-schedule,
.btn-upgrade {
  background: #4CAF50;
  color: white;
}

.connected-accounts h3,
.subscription-card h3,
.activity-feed h3 {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 16px;
}

.accounts-list,
.activity-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.account-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
}

.btn-connect {
  background: #4CAF50;
  color: white;
}

.btn-disconnect {
  background: #e0e0e0;
  color: #666;
}

.plan-name {
  font-size: 20px;
  font-weight: 600;
  margin-bottom: 12px;
}

.plan-details {
  color: #666;
  margin-bottom: 16px;
}

.activity-item {
  display: flex;
  gap: 12px;
  padding: 12px 0;
  border-bottom: 1px solid #f0f0f0;
}

.activity-icon {
  font-size: 24px;
}

.activity-text {
  font-size: 14px;
  margin-bottom: 4px;
}

.activity-time {
  font-size: 12px;
  color: #666;
}

.platform-badge {
  display: inline-block;
  padding: 4px 8px;
  background: #e3f2fd;
  color: #1976d2;
  border-radius: 4px;
  font-size: 12px;
  margin-right: 4px;
}

@media (max-width: 1024px) {
  .dashboard-grid {
    grid-template-columns: 1fr;
  }
}
</style>
