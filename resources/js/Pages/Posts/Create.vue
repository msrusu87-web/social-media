<template>
    <AuthenticatedLayout>
        <div class="bg-white shadow rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-6">Create Post</h1>
            
            <PostComposer />
            
            <div class="mt-8 space-y-4">
                <h2 class="text-xl font-semibold">Platform Previews</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <PlatformPreview
                        v-for="platform in selectedPlatforms"
                        :key="platform"
                        :platform="platform"
                        :content="content"
                        :media-url="mediaUrl"
                        :user="user"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PostComposer from '@/Components/PostComposer.vue';
import PlatformPreview from '@/Components/PlatformPreview.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);

const content = ref('');
const mediaUrl = ref(null);
const selectedPlatforms = ref(['facebook', 'instagram']);
</script>
