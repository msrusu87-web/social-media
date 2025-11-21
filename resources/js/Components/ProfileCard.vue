<template>
    <div class="profile-card border rounded-lg p-6">
        <div class="flex items-start space-x-4">
            <img
                :src="user.avatar || '/default-avatar.png'"
                alt="Avatar"
                class="w-20 h-20 rounded-full"
            />
            <div class="flex-1">
                <h2 class="text-2xl font-bold">{{ user.name }}</h2>
                <p class="text-gray-500">@{{ user.username }}</p>
                
                <p v-if="user.profile?.bio" class="mt-2">{{ user.profile.bio }}</p>
                
                <div class="mt-4 flex space-x-6">
                    <div>
                        <span class="font-semibold">{{ followersCount }}</span>
                        <span class="text-gray-500 ml-1">Followers</span>
                    </div>
                    <div>
                        <span class="font-semibold">{{ followingCount }}</span>
                        <span class="text-gray-500 ml-1">Following</span>
                    </div>
                    <div>
                        <span class="font-semibold">{{ postsCount }}</span>
                        <span class="text-gray-500 ml-1">Posts</span>
                    </div>
                </div>
                
                <div v-if="!isOwnProfile" class="mt-4">
                    <button
                        @click="toggleFollow"
                        :class="[
                            'px-6 py-2 rounded-md font-semibold',
                            isFollowing
                                ? 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                : 'bg-blue-600 text-white hover:bg-blue-700'
                        ]"
                    >
                        {{ isFollowing ? 'Unfollow' : 'Follow' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    followersCount: {
        type: Number,
        default: 0,
    },
    followingCount: {
        type: Number,
        default: 0,
    },
    postsCount: {
        type: Number,
        default: 0,
    },
});

const page = usePage();
const currentUser = computed(() => page.props.auth?.user);
const isOwnProfile = computed(() => currentUser.value && currentUser.value.id === props.user.id);
const isFollowing = ref(false);

const toggleFollow = () => {
    const endpoint = isFollowing.value ? 'unfollow' : 'follow';
    router.post(`/users/${props.user.id}/${endpoint}`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            isFollowing.value = !isFollowing.value;
        },
    });
};
</script>
