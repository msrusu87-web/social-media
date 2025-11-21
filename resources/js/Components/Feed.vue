<template>
  <div class="feed-container">
    <div class="feed-header">
      <h2>{{ title }}</h2>
      <div class="feed-actions">
        <button @click="refreshFeed" class="refresh-btn">🔄 Refresh</button>
      </div>
    </div>

    <div v-if="loading" class="loading">
      <p>Loading posts...</p>
    </div>

    <div v-else class="feed-posts">
      <div v-for="post in posts" :key="post.id" class="post-card">
        <div class="post-header">
          <div class="user-info">
            <img
              :src="post.user.profile?.avatar || '/default-avatar.png'"
              :alt="post.user.name"
              class="user-avatar"
            />
            <div class="user-details">
              <h4 class="user-name">{{ post.user.name }}</h4>
              <span class="post-time">{{ formatTime(post.created_at) }}</span>
            </div>
          </div>
          <div class="post-menu">
            <button @click="toggleMenu(post.id)">⋮</button>
          </div>
        </div>

        <div class="post-content">
          <p>{{ post.content }}</p>
          <div v-if="post.media && post.media.length" class="post-media">
            <img
              v-for="(media, index) in post.media"
              :key="index"
              :src="media"
              alt="Post media"
            />
          </div>
        </div>

        <div class="post-stats">
          <span>{{ post.likes_count }} likes</span>
          <span>{{ post.comments_count }} comments</span>
          <span>{{ post.reposts_count }} reposts</span>
        </div>

        <div class="post-actions">
          <button
            @click="toggleLike(post)"
            :class="['action-btn', { liked: post.isLiked }]"
          >
            {{ post.isLiked ? '❤️' : '🤍' }} Like
          </button>
          <button @click="showComments(post)" class="action-btn">
            💬 Comment
          </button>
          <button @click="repost(post)" class="action-btn">
            🔄 Repost
          </button>
          <button @click="sharePost(post)" class="action-btn">
            📤 Share
          </button>
        </div>

        <div v-if="showingComments === post.id" class="comments-section">
          <div v-for="comment in post.comments" :key="comment.id" class="comment">
            <img
              :src="comment.user.profile?.avatar || '/default-avatar.png'"
              :alt="comment.user.name"
              class="comment-avatar"
            />
            <div class="comment-content">
              <strong>{{ comment.user.name }}</strong>
              <p>{{ comment.content }}</p>
              <span class="comment-time">{{ formatTime(comment.created_at) }}</span>
            </div>
          </div>
          <div class="comment-input">
            <input
              v-model="newComment[post.id]"
              type="text"
              placeholder="Write a comment..."
              @keyup.enter="postComment(post)"
            />
            <button @click="postComment(post)">Send</button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="hasMore" class="load-more">
      <button @click="loadMore" class="load-more-btn">Load More</button>
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
      loading: false,
      hasMore: true,
      page: 1,
      showingComments: null,
      newComment: {},
    };
  },
  mounted() {
    this.loadPosts();
  },
  methods: {
    async loadPosts() {
      this.loading = true;
      try {
        // Simulate API call
        // const response = await axios.get(`/api/feed/${this.feedType}?page=${this.page}`);
        // this.posts = response.data.posts;
        this.loading = false;
      } catch (error) {
        console.error('Failed to load posts:', error);
        this.loading = false;
      }
    },
    async loadMore() {
      this.page++;
      await this.loadPosts();
    },
    async refreshFeed() {
      this.page = 1;
      this.posts = [];
      await this.loadPosts();
    },
    async toggleLike(post) {
      post.isLiked = !post.isLiked;
      post.likes_count += post.isLiked ? 1 : -1;
      // Call API to like/unlike
    },
    showComments(post) {
      this.showingComments = this.showingComments === post.id ? null : post.id;
    },
    async postComment(post) {
      const content = this.newComment[post.id];
      if (!content) return;
      
      // Call API to post comment
      this.newComment[post.id] = '';
      post.comments_count++;
    },
    async repost(post) {
      // Call API to repost
      post.reposts_count++;
    },
    sharePost(post) {
      // Implement share functionality
      console.log('Sharing post:', post.id);
    },
    toggleMenu(postId) {
      console.log('Toggle menu for post:', postId);
    },
    formatTime(timestamp) {
      // Simple time formatting
      return new Date(timestamp).toLocaleString();
    },
  },
};
</script>

<style scoped>
.feed-container {
  max-width: 800px;
  margin: 0 auto;
}

.feed-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.post-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
  padding: 20px;
}

.post-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
}

.post-content {
  margin: 15px 0;
}

.post-media {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 10px;
  margin-top: 10px;
}

.post-media img {
  width: 100%;
  border-radius: 8px;
}

.post-actions {
  display: flex;
  gap: 10px;
  padding-top: 15px;
  border-top: 1px solid #e0e0e0;
}

.action-btn {
  flex: 1;
  padding: 8px;
  border: none;
  background: #f5f5f5;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.3s;
}

.action-btn:hover {
  background: #e0e0e0;
}

.action-btn.liked {
  color: #e74c3c;
}
</style>
