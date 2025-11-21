<template>
  <div class="feed">
    <div class="feed-header">
      <h2>{{ title }}</h2>
      <div class="feed-filters">
        <button
          v-for="filter in filters"
          :key="filter.id"
          @click="activeFilter = filter.id"
          :class="['filter-btn', { active: activeFilter === filter.id }]"
        >
          {{ filter.label }}
        </button>
      </div>
    </div>

    <div class="feed-content">
      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>Loading posts...</p>
      </div>

      <div v-else-if="posts.length === 0" class="empty-state">
        <p>No posts found</p>
      </div>

      <div v-else class="posts-list">
        <div v-for="post in filteredPosts" :key="post.id" class="post-card">
          <div class="post-header">
            <div class="post-author">
              <div class="author-avatar"></div>
              <div class="author-info">
                <div class="author-name">{{ post.user.name }}</div>
                <div class="post-time">{{ formatTime(post.created_at) }}</div>
              </div>
            </div>
            <button class="post-menu">⋮</button>
          </div>

          <div class="post-content">
            <p>{{ post.content }}</p>
          </div>

          <div v-if="post.media && post.media.length > 0" class="post-media">
            <img
              v-for="(media, index) in post.media"
              :key="index"
              :src="media"
              alt="Post media"
            />
          </div>

          <div class="post-stats">
            <span>{{ post.likes_count || 0 }} likes</span>
            <span>{{ post.comments_count || 0 }} comments</span>
            <span>{{ post.reposts_count || 0 }} reposts</span>
          </div>

          <div class="post-actions">
            <button
              @click="toggleLike(post)"
              :class="['action-btn', { liked: post.isLiked }]"
            >
              {{ post.isLiked ? '❤️' : '🤍' }} Like
            </button>
            <button @click="openComments(post)" class="action-btn">
              💬 Comment
            </button>
            <button @click="repost(post)" class="action-btn">
              🔁 Repost
            </button>
            <button @click="share(post)" class="action-btn">
              📤 Share
            </button>
          </div>

          <div v-if="post.showComments" class="comments-section">
            <div v-for="comment in post.comments" :key="comment.id" class="comment">
              <div class="comment-avatar"></div>
              <div class="comment-content">
                <div class="comment-author">{{ comment.user.name }}</div>
                <p>{{ comment.content }}</p>
                <div class="comment-time">{{ formatTime(comment.created_at) }}</div>
              </div>
            </div>

            <div class="comment-input">
              <input
                v-model="commentText"
                type="text"
                placeholder="Write a comment..."
                @keyup.enter="addComment(post)"
              />
              <button @click="addComment(post)">Post</button>
            </div>
          </div>
        </div>
      </div>

      <div v-if="hasMore" class="load-more">
        <button @click="loadMore" :disabled="loadingMore">
          {{ loadingMore ? 'Loading...' : 'Load More' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Feed',
  props: {
    title: {
      type: String,
      default: 'Feed',
    },
    feedType: {
      type: String,
      default: 'home', // home, discover, trending
    },
  },
  data() {
    return {
      posts: [],
      loading: true,
      loadingMore: false,
      hasMore: true,
      page: 1,
      activeFilter: 'all',
      commentText: '',
      filters: [
        { id: 'all', label: 'All' },
        { id: 'following', label: 'Following' },
        { id: 'trending', label: 'Trending' },
      ],
    };
  },
  computed: {
    filteredPosts() {
      // Filter posts based on active filter
      return this.posts;
    },
  },
  mounted() {
    this.loadPosts();
  },
  methods: {
    async loadPosts() {
      this.loading = true;
      try {
        const response = await fetch(`/api/feed/${this.feedType}?page=${this.page}`);
        const data = await response.json();
        this.posts = data.posts;
        this.hasMore = data.hasMore;
      } catch (error) {
        console.error('Failed to load posts:', error);
      } finally {
        this.loading = false;
      }
    },
    async loadMore() {
      this.loadingMore = true;
      this.page++;
      try {
        const response = await fetch(`/api/feed/${this.feedType}?page=${this.page}`);
        const data = await response.json();
        this.posts.push(...data.posts);
        this.hasMore = data.hasMore;
      } catch (error) {
        console.error('Failed to load more posts:', error);
      } finally {
        this.loadingMore = false;
      }
    },
    async toggleLike(post) {
      try {
        const method = post.isLiked ? 'DELETE' : 'POST';
        await fetch('/api/likes', {
          method,
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            likeable_type: 'App\\Models\\Post',
            likeable_id: post.id,
          }),
        });
        post.isLiked = !post.isLiked;
        post.likes_count += post.isLiked ? 1 : -1;
      } catch (error) {
        console.error('Failed to toggle like:', error);
      }
    },
    openComments(post) {
      post.showComments = !post.showComments;
      if (post.showComments && !post.comments) {
        this.loadComments(post);
      }
    },
    async loadComments(post) {
      try {
        const response = await fetch(`/api/posts/${post.id}/comments`);
        const data = await response.json();
        post.comments = data.comments;
      } catch (error) {
        console.error('Failed to load comments:', error);
      }
    },
    async addComment(post) {
      if (!this.commentText.trim()) return;
      try {
        const response = await fetch(`/api/posts/${post.id}/comments`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ content: this.commentText }),
        });
        const data = await response.json();
        if (data.success) {
          post.comments.unshift(data.comment);
          post.comments_count++;
          this.commentText = '';
        }
      } catch (error) {
        console.error('Failed to add comment:', error);
      }
    },
    async repost(post) {
      try {
        await fetch(`/api/posts/${post.id}/repost`, {
          method: 'POST',
        });
        post.reposts_count++;
      } catch (error) {
        console.error('Failed to repost:', error);
      }
    },
    share(post) {
      // Implement share functionality
      if (navigator.share) {
        navigator.share({
          title: post.user.name,
          text: post.content,
          url: `/posts/${post.id}`,
        });
      }
    },
    formatTime(timestamp) {
      const date = new Date(timestamp);
      const now = new Date();
      const diff = now - date;
      const minutes = Math.floor(diff / 60000);
      const hours = Math.floor(diff / 3600000);
      const days = Math.floor(diff / 86400000);

      if (minutes < 60) return `${minutes}m ago`;
      if (hours < 24) return `${hours}h ago`;
      if (days < 7) return `${days}d ago`;
      return date.toLocaleDateString();
    },
  },
};
</script>

<style scoped>
.feed {
  max-width: 800px;
  margin: 0 auto;
}

.feed-header {
  padding: 20px;
  background: white;
  border-radius: 12px;
  margin-bottom: 20px;
}

.feed-header h2 {
  margin: 0 0 16px 0;
  font-size: 24px;
  font-weight: 600;
}

.feed-filters {
  display: flex;
  gap: 12px;
}

.filter-btn {
  padding: 8px 16px;
  border: 1px solid #e0e0e0;
  background: white;
  border-radius: 20px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s;
}

.filter-btn.active {
  background: #4CAF50;
  color: white;
  border-color: #4CAF50;
}

.loading,
.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #666;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #4CAF50;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 16px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.post-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.post-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.post-author {
  display: flex;
  align-items: center;
}

.author-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: #e0e0e0;
  margin-right: 12px;
}

.author-name {
  font-weight: 600;
  font-size: 16px;
}

.post-time {
  color: #666;
  font-size: 14px;
}

.post-menu {
  border: none;
  background: none;
  font-size: 20px;
  cursor: pointer;
  padding: 4px 8px;
}

.post-content {
  margin: 16px 0;
  line-height: 1.5;
}

.post-media {
  margin: 16px 0;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 8px;
}

.post-media img {
  width: 100%;
  border-radius: 8px;
}

.post-stats {
  display: flex;
  gap: 20px;
  padding: 12px 0;
  border-bottom: 1px solid #e0e0e0;
  color: #666;
  font-size: 14px;
}

.post-actions {
  display: flex;
  gap: 16px;
  padding: 12px 0;
}

.action-btn {
  flex: 1;
  padding: 8px;
  border: none;
  background: none;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  transition: background 0.2s;
  border-radius: 6px;
}

.action-btn:hover {
  background: #f0f0f0;
}

.action-btn.liked {
  color: #e74c3c;
}

.comments-section {
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid #e0e0e0;
}

.comment {
  display: flex;
  margin-bottom: 12px;
}

.comment-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #e0e0e0;
  margin-right: 12px;
  flex-shrink: 0;
}

.comment-content {
  flex: 1;
  background: #f0f0f0;
  padding: 8px 12px;
  border-radius: 12px;
}

.comment-author {
  font-weight: 600;
  font-size: 14px;
  margin-bottom: 4px;
}

.comment-time {
  font-size: 12px;
  color: #666;
  margin-top: 4px;
}

.comment-input {
  display: flex;
  gap: 8px;
  margin-top: 12px;
}

.comment-input input {
  flex: 1;
  padding: 8px 12px;
  border: 1px solid #e0e0e0;
  border-radius: 20px;
  font-size: 14px;
}

.comment-input button {
  padding: 8px 16px;
  background: #4CAF50;
  color: white;
  border: none;
  border-radius: 20px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
}

.load-more {
  text-align: center;
  padding: 20px;
}

.load-more button {
  padding: 12px 32px;
  background: white;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.2s;
}

.load-more button:hover:not(:disabled) {
  background: #f8f8f8;
}

.load-more button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>
