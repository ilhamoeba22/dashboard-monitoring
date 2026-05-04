<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
  HomeIcon, 
  ChartPieIcon, 
  UsersIcon, 
  WalletIcon, 
  DocumentChartBarIcon,
  Bars3Icon,
  XMarkIcon,
  BellIcon
} from '@heroicons/vue/24/outline';

const sidebarOpen = ref(false);

const navigation = [
  { name: 'Executive Overview', href: '/', icon: HomeIcon, current: true },
  { name: 'Financing Metrics', href: '/financing', icon: ChartPieIcon, current: false },
  { name: 'Funding (CIF)', href: '/cif', icon: UsersIcon, current: false },
  { name: 'Deposit & Savings', href: '/funding', icon: WalletIcon, current: false },
  { name: 'Financial Reports', href: '/reporting', icon: DocumentChartBarIcon, current: false },
];
</script>

<template>
  <div class="min-h-screen bg-background-dark text-slate-200">
    <!-- Mobile sidebar -->
    <div v-show="sidebarOpen" class="relative z-50 lg:hidden" role="dialog" aria-modal="true">
      <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity" @click="sidebarOpen = false"></div>

      <div class="fixed inset-0 flex">
        <div class="relative flex w-full max-w-xs flex-1 transform flex-col glass-panel pb-4 pt-5 transition-all">
          <div class="absolute right-0 top-0 -mr-12 pt-2">
            <button type="button" class="relative ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" @click="sidebarOpen = false">
              <span class="sr-only">Close sidebar</span>
              <XMarkIcon class="h-6 w-6 text-white" aria-hidden="true" />
            </button>
          </div>
          <div class="flex shrink-0 items-center px-4">
            <div class="font-display font-bold text-2xl tracking-wider text-white flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-400 to-cyan-500 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                <span class="text-slate-900 font-bold text-xl">M</span>
              </div>
              BPRS HIK MCI
            </div>
          </div>
          <div class="mt-8 h-0 flex-1 overflow-y-auto">
            <nav class="space-y-2 px-3">
              <Link v-for="item in navigation" :key="item.name" :href="item.href" :class="[item.current ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:text-white hover:bg-white/5', 'group flex items-center gap-x-3 rounded-xl p-3 text-sm font-medium transition-all duration-300']">
                <component :is="item.icon" :class="[item.current ? 'text-emerald-400' : 'text-slate-500 group-hover:text-white', 'h-6 w-6 shrink-0 transition-colors']" aria-hidden="true" />
                {{ item.name }}
              </Link>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <!-- Static sidebar for desktop -->
    <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
      <div class="flex grow flex-col gap-y-8 overflow-y-auto glass-panel px-6 pb-4 ring-1 ring-white/10">
        <div class="flex h-20 shrink-0 items-center pt-4">
          <div class="font-display font-bold text-2xl tracking-wider text-white flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-cyan-500 flex items-center justify-center shadow-lg shadow-emerald-500/30">
              <span class="text-slate-900 font-black text-2xl">M</span>
            </div>
            MCI<span class="font-light text-slate-400">Dash</span>
          </div>
        </div>
        <nav class="flex flex-1 flex-col">
          <ul role="list" class="flex flex-1 flex-col gap-y-7">
            <li>
              <div class="text-xs font-semibold leading-6 text-slate-500 uppercase tracking-widest mb-4">Analytics Engine</div>
              <ul role="list" class="-mx-2 space-y-2">
                <li v-for="item in navigation" :key="item.name">
                  <Link :href="item.href" :class="[item.current ? 'bg-gradient-to-r from-emerald-500/20 to-transparent text-emerald-300 border-l-2 border-emerald-400' : 'text-slate-400 hover:text-white hover:bg-white/5 border-l-2 border-transparent', 'group flex items-center gap-x-3 p-3 text-sm font-medium transition-all duration-300 rounded-r-xl']">
                    <component :is="item.icon" :class="[item.current ? 'text-emerald-400' : 'text-slate-500 group-hover:text-emerald-300', 'h-6 w-6 shrink-0 transition-colors duration-300']" aria-hidden="true" />
                    {{ item.name }}
                  </Link>
                </li>
              </ul>
            </li>
            <li class="mt-auto">
              <div class="glass-card rounded-2xl p-4 relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/20 rounded-full blur-2xl"></div>
                <h4 class="text-sm font-semibold text-white mb-1">Active Database</h4>
                <div class="flex items-center gap-2 mt-2">
                  <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                  <span class="text-xs font-mono text-emerald-300">MCI_MAR26_01042026</span>
                </div>
                <p class="text-xs text-slate-400 mt-3">Connected & Synchronized</p>
              </div>
            </li>
          </ul>
        </nav>
      </div>
    </div>

    <!-- Top header & Content -->
    <div class="lg:pl-72 flex flex-col min-h-screen">
      <!-- Top header -->
      <div class="sticky top-0 z-40 flex h-20 shrink-0 items-center gap-x-4 border-b border-white/5 glass-panel px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
        <button type="button" class="-m-2.5 p-2.5 text-slate-400 hover:text-white lg:hidden" @click="sidebarOpen = true">
          <span class="sr-only">Open sidebar</span>
          <Bars3Icon class="h-6 w-6" aria-hidden="true" />
        </button>

        <!-- Separator -->
        <div class="h-6 w-px bg-slate-700 lg:hidden" aria-hidden="true"></div>

        <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
          <div class="flex flex-1 items-center">
            <h1 class="font-display text-xl font-semibold text-white">Dashboard Overview</h1>
          </div>
          <div class="flex items-center gap-x-4 lg:gap-x-6">
            <button type="button" class="-m-2.5 p-2.5 text-slate-400 hover:text-white relative transition-colors">
              <span class="sr-only">View notifications</span>
              <BellIcon class="h-6 w-6" aria-hidden="true" />
              <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
              <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-rose-500"></span>
            </button>

            <!-- Separator -->
            <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-slate-700" aria-hidden="true"></div>

            <!-- Profile dropdown -->
            <div class="flex items-center gap-x-4">
              <img class="h-9 w-9 rounded-full ring-2 ring-emerald-500/30 object-cover" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="" />
              <span class="hidden lg:flex lg:items-center">
                <span class="text-sm font-medium leading-6 text-white" aria-hidden="true">Direksi MCI</span>
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content Area -->
      <main class="flex-1 px-4 py-8 sm:px-6 lg:px-8 relative z-10">
        <slot />
      </main>
    </div>
  </div>
</template>
