<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router, Head, Link } from "@inertiajs/vue3";

const props = defineProps({
  chats: Array,
});
</script>

<template>
  <Head title="Chats" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex">
        <Link :href="route('dashboard')" class="mr-2">
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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Your researches</h2>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="px-4 sm:px-6 lg:px-8 bg-white">
          <div class="sm:flex sm:items-center">
            <div v-if="chats.length > 0" class="sm:flex-auto">Your existing chats:</div>
            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none p-4">
              <button
                @click="router.visit(route('chats.create'))"
                type="button"
                class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm text-white font-semibold shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
              >
                New Research
              </button>
            </div>
          </div>
          <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
              <div
                v-if="chats.length > 0"
                class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8"
              >
                <table class="min-w-full divide-y divide-gray-300">
                  <thead>
                    <tr>
                      <th
                        scope="col"
                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                      >
                        Name
                      </th>

                      <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                        <span class="sr-only">Open</span>
                      </th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200">
                    <tr v-for="chat in chats" :key="chat.id">
                      <td
                        class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0"
                      >
                        {{ chat.name }}
                      </td>

                      <td
                        class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0"
                      >
                        <Link
                          :href="route('chats.show', { chat: chat.id })"
                          class="text-indigo-600 hover:text-indigo-900"
                        >
                          Open<span class="sr-only">, {{ chat.name }}</span>
                        </Link>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div
                v-else
                class="inline-block min-w-full py-4 align-middle sm:px-6 lg:px-8"
              >
                <h2>No chats yet... Start a new research.</h2>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
