<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router, useForm, Head, Link } from "@inertiajs/vue3";

const props = defineProps({
  categories: Array,
  document: Object,
});

const formData = useForm({
  name: null,
  type: null,
  path: null,
  file: null,
  content: null,
});

function submit() {
  router.post(route("document.store"), formData, {
    forceFormData: true,
  });
}
</script>

<template>
  <Head title="Documents" />

  <AuthenticatedLayout>
    <template #header>
    <div class="flex">
      <Link :href="route('documents.index')" class="mr-2">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-6"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"
            />
          </svg>
        </Link>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Form</h2>
    </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form @submit.prevent="submit">
          <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input
              type="text"
              v-model="formData.name"
              id="name"
              name="name"
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
            />
          </div>
          <div class="mb-4">
            <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
            <select
              v-model="formData.type"
              id="type"
              name="type"
              class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
            >
              <option disabled>Choose an option</option>
              <template v-for="category in props.categories">
                <option :value="category.value">{{ category.label }}</option>
              </template>
            </select>
          </div>

          <div class="mb-4" v-if="formData.type === 'pdf'">
            <label for="path" class="block text-sm font-medium text-gray-700">File</label>
            <input
              id="path"
              type="file"
              @input="formData.file = $event.target.files[0]"
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
            />
            <progress v-if="formData.progress" :value="formData.progress.percentage" max="100">
              {{ formData.progress.percentage }}%
            </progress>
          </div>

          <div class="mb-4" v-if="formData.type === 'web_page'">
            <label for="path" class="block text-sm font-medium text-gray-700">URL</label>
            <input
              type="text"
              v-model="formData.path"
              id="path"
              name="path"
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
            />
          </div>

          <div class="mb-4" v-if="formData.type === 'raw_text'">
            <label for="content" class="block text-sm font-medium text-gray-700"
              >Content</label
            >
            <textarea
              v-model="formData.content"
              id="content"
              name="content"
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
            ></textarea>
          </div>

          <div class="mb-4">
            <button
              type="submit"
              class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            >
              Save
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
