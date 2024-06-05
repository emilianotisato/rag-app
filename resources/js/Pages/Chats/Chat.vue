<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router, useForm, Head, Link } from "@inertiajs/vue3";

const props = defineProps({
  messages: Array,
  chat: Object,
});

const formData = useForm({
  prompt: '',
});

function submit() {
  router.post(route("chats.store"), formData);
}
</script>

<template>
  <Head :title="chat.name" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex">
        <Link :href="route('chats.index')" class="mr-2">
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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ chat.name }}</h2>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <ul role="list" class="space-y-6">
          <template v-for="message in messages" :key="message.id">
            <li v-if="message.is_user" class="relative flex gap-x-4">
              <div class="absolute left-0 top-0 flex w-6 justify-center -bottom-6">
                <div class="w-px bg-gray-200"></div>
              </div>
              <div
                class="relative flex h-6 w-6 flex-none items-center justify-center bg-white"
              >
                <div
                  class="h-1.5 w-1.5 rounded-full bg-gray-100 ring-1 ring-gray-300"
                ></div>
              </div>
              <p class="flex-auto py-0.5 text-xs leading-5 text-gray-500">
                <span class="font-medium text-gray-900">You: </span>
                {{ message.content }}
              </p>
              
            </li>
            <li v-else class="relative flex gap-x-4">
              <div class="absolute left-0 top-0 flex w-6 justify-center -bottom-6">
                <div class="w-px bg-gray-200"></div>
              </div>
              <img
                src="https://images.unsplash.com/photo-1550525811-e5869dd03032?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                alt=""
                class="relative mt-3 h-6 w-6 flex-none rounded-full bg-gray-50"
              />
              <div class="flex-auto rounded-md p-3 ring-1 ring-inset ring-gray-200">
                <div class="flex justify-between gap-x-4">
                  <div class="py-0.5 text-xs leading-5 text-gray-500">
                    <span class="font-medium text-gray-900">AI</span>
                  </div>
                  
                </div>
                <p class="text-sm leading-6 text-gray-500">{{ message.content }}</p>
              </div>
            </li>
          </template>
        </ul>

        <!-- New comment form -->
        <div class="mt-6 flex gap-x-3">
          <img
            src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
            alt=""
            class="h-6 w-6 flex-none rounded-full bg-gray-50"
          />
          <form @submit.prevent="submit" class="relative flex-auto">
            <div
              class="overflow-hidden rounded-lg pb-12 shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-indigo-600"
            >
              <label for="comment" class="sr-only">Write down your next question...</label>
              <textarea
                v-model="formData.prompt"
                rows="2"
                name="comment"
                id="comment"
                class="block w-full resize-none border-0 bg-transparent py-1.5 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6"
                placeholder="Escribe tu siguiente pregunta..."
              ></textarea>
            </div>

            <div class="absolute inset-x-0 bottom-0 flex justify-between py-2 pl-3 pr-2">
              <button
                type="submit"
                class="rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
              >
                Send
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
