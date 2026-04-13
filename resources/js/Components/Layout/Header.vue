<script setup>
defineEmits(['toggle-sidebar']);
</script>

<template>
    <header class="fixed top-0 right-0 left-0 lg:left-64 z-40 bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl shadow-sm dark:shadow-none transition-all duration-200 ease-in-out">
        <div class="flex justify-between items-center px-4 md:px-8 py-3 w-full">
            <!-- Mobile Toggle -->
            <button type="button" class="lg:hidden p-2 text-gray-600 hover:bg-gray-100 rounded-lg mr-2" @click="$emit('toggle-sidebar')">
                <span class="material-symbols-outlined">menu</span>
            </button>

        <!-- Dashboard Title / Product Name -->
        <div class="flex items-center gap-4 flex-1 max-w-xl">
          <div class="relative w-full">
            <h1 class="text-xl font-black text-on-surface uppercase tracking-tight flex items-center gap-2">
              <span class="material-symbols-outlined text-primary" data-icon="auto_awesome">auto_awesome</span>
              Curated Canvas
            </h1>
          </div>
        </div>

        <!-- Right Header Actions -->
        <div class="flex items-center gap-2">
          <div class="h-8 w-[1px] bg-outline-variant/30 mx-2"></div>
          <!-- User Profile -->
          <div class="flex items-center gap-3 pl-2">
            <div class="text-right hidden sm:block">
              <p class="text-sm font-semibold text-gray-900">Alex Rivers</p>
              <p class="text-[10px] font-medium text-on-surface-variant/70 uppercase tracking-tighter">Super Admin</p>
            </div>
            <img alt="User profile" class="w-9 h-9 rounded-full border-2 border-primary-container/20 object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBKjFC81p1hdAJnjvGDoQo4LZxpXGzLz2BS04kNRf3zgwcY9-rBThlqJwf1lr0PG6PR6uCv9wS_rYgsKovJRn5snGFaYVqKmpysGj2UBuyrM32-lA1L4XX2ZaG5N9CTxUUu2YcV9bJ7m4qEv-QXJolbvAKDHOxn2QissB8CwGbQ5y7ktNxBeezCWNKG2xF7sndT3HS-BhC0dFpE1jXXWy-bQ88UIn0q5lDMvQ58pgkhLvpT-ZujBHlz1ec9PsJ_HazXU7qWEOZ2LA0" />
          </div>
        </div>
        </div>
    </header>
</template>