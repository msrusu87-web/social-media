<template>
  <div class="dashboard min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex justify-between items-center">
          <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
          <Link href="/posts/create" class="btn-primary">
            + Create Post
          </Link>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500">Total Posts</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.totalPosts }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
              <span class="text-2xl">📝</span>
            </div>
          </div>
          <div class="mt-2 text-sm text-gray-600">
            <span class="text-green-600">↑ {{ stats.postsThisMonth }}</span> this month
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500">Followers</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.followers }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
              <span class="text-2xl">👥</span>
            </div>
          </div>
          <div class="mt-2 text-sm text-gray-600">
            <span class="text-green-600">↑ {{ stats.newFollowers }}</span> new this week
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500">Engagement</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.engagement }}%</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
              <span class="text-2xl">📊</span>
            </div>
          </div>
          <div class="mt-2 text-sm text-gray-600">
            <span class="text-green-600">↑ 2.5%</span> from last week
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500">Posts Remaining</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.postsRemaining }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
              <span class="text-2xl">⚡</span>
            </div>
          </div>
          <div class="mt-2 text-sm text-gray-600">
            of {{ stats.postsLimit }} per month
          </div>
        </div>
      </div>

      <!-- Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Posts -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow">
          <div class="p-6 border-b">
            <h2 class="text-xl font-semibold text-gray-900">Recent Posts</h2>
          </div>
          <div class="divide-y">
            <div
              v-for="post in recentPosts"
              :key="post.id"
              class="p-6 hover:bg-gray-50 transition cursor-pointer"
              @click="viewPost(post.id)"
            >
              <div class="flex justify-between items-start mb-2">
                <div class="flex-1">
                  <p class="text-sm text-gray-900 line-clamp-2">{{ post.content }}</p>
                </div>
                <span
                  :class="[
                    'ml-4 px-2 py-1 text-xs rounded-full',
                    statusClasses[post.status]
                  ]"
                >
                  {{ post.status }}
                </span>
              </div>
              <div class="flex items-center gap-4 text-sm text-gray-500">
                <span>📅 {{ formatDate(post.created_at) }}</span>
                <span>❤️ {{ post.likes_count }}</span>
                <span>💬 {{ post.comments_count }}</span>
                <div class="flex gap-1">
                  <span v-for="platform in post.platforms" :key="platform">
                    {{ platformIcons[platform] }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <!-- Connected Accounts -->
          <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Connected Accounts</h3>
            <div class="space-y-3">
              <div
                v-for="connection in socialConnections"
                :key="connection.id"
                class="flex items-center justify-between p-3 bg-gray-50 rounded"
              >
                <div class="flex items-center gap-2">
                  <span class="text-xl">{{ platformIcons[connection.platform] }}</span>
                  <span class="text-sm font-medium">{{ connection.platform_username }}</span>
                </div>
                <span
                  :class="[
                    'w-2 h-2 rounded-full',
                    connection.is_active ? 'bg-green-500' : 'bg-red-500'
                  ]"
                ></span>
              </div>
            </div>
            <Link href="/settings/social" class="mt-4 block text-center text-sm text-blue-600 hover:text-blue-700">
              + Add Account
            </Link>
          </div>

          <!-- Scheduled Posts -->
          <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Upcoming Posts</h3>
            <div class="space-y-3">
              <div
                v-for="post in scheduledPosts"
                :key="post.id"
                class="p-3 bg-blue-50 rounded"
              >
                <p class="text-sm text-gray-900 line-clamp-1">{{ post.content }}</p>
                <p class="text-xs text-gray-500 mt-1">
                  📅 {{ formatDate(post.scheduled_at) }}
                </p>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="space-y-2">
              <Link href="/posts/create" class="block w-full text-center py-2 px-4 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                Create Post
              </Link>
              <Link href="/feed" class="block w-full text-center py-2 px-4 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition">
                View Feed
              </Link>
              <Link href="/analytics" class="block w-full text-center py-2 px-4 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition">
                Analytics
              </Link>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
  stats: {
    type: Object,
    default: () => ({
      totalPosts: 0,
      postsThisMonth: 0,
      followers: 0,
      newFollowers: 0,
      engagement: 0,
      postsRemaining: 0,
      postsLimit: 0
    })
  },
  recentPosts: {
    type: Array,
    default: () => []
  },
  scheduledPosts: {
    type: Array,
    default: () => []
  },
  socialConnections: {
    type: Array,
    default: () => []
  }
});

const statusClasses = {
  draft: 'bg-gray-100 text-gray-700',
  scheduled: 'bg-yellow-100 text-yellow-700',
  published: 'bg-green-100 text-green-700',
  failed: 'bg-red-100 text-red-700'
};

const platformIcons = {
  facebook: '📘',
  instagram: '📷',
  twitter: '🐦',
  linkedin: '💼'
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const viewPost = (postId) => {
  router.visit(`/posts/${postId}`);
};
</script>

<style scoped>
.btn-primary {
  @apply px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium;
}

.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
