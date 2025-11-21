<template>
    <AuthenticatedLayout>
        <div class="bg-white shadow rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-6">Edit Profile</h1>
            
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        required
                    />
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Username</label>
                    <input
                        v-model="form.username"
                        type="text"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        required
                    />
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Bio</label>
                    <textarea
                        v-model="form.bio"
                        rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                    ></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Avatar</label>
                    <input
                        @change="handleFileUpload"
                        type="file"
                        accept="image/*"
                        class="mt-1 block w-full"
                    />
                </div>
                
                <div class="flex justify-end">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
                    >
                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.user.name,
    username: props.user.username,
    bio: props.user.profile?.bio || '',
    avatar: null,
});

const handleFileUpload = (event) => {
    form.avatar = event.target.files[0];
};

const submit = () => {
    form.post(`/profile/${props.user.id}`);
};
</script>
