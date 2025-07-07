<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3'
import MadeWithLove from '@/components/MadeWithLove.vue';
import PageHeader from '@/components/admin/PageHeader.vue';
import Menu from '@/../../src/volt/Menu.vue';
import SecondaryButton from '@/../../src/volt/SecondaryButton.vue';
import {
  Dialog,
  DialogPanel,
  TransitionChild,
  TransitionRoot,
} from '@headlessui/vue';
import {
  Bars3Icon,
  Cog6ToothIcon,
  HomeIcon,
  XMarkIcon,
  SunIcon,
  MoonIcon,
  MagnifyingGlassIcon,
  ChevronDownIcon,
} from '@heroicons/vue/24/outline';
import { useDark, useToggle } from '@vueuse/core';
import ApplicationLogo from '@/components/ApplicationLogo.vue';

const isDark = useDark();
const toggleDark = useToggle(isDark);

const sidebarOpen = ref(false);

defineProps({
	title: null,
});

const logout = () => {
	router.post(route('logout'));
};

const navigation = [
	{ name: 'Dashboard', href: route('admin.index'), icon: HomeIcon, current: route().current('admin.index') },
];

const userMenu = ref();
const userMenuItems = ref([
    {
        label: 'Options',
        items: [
			{
				label: 'Profile',
				icon: 'pi pi-user',
			}
        ]
    }
]);

const toggle = (event) => {
    userMenu.value.toggle(event);
};
</script>

<template>

	<Head :title="title">
		<link rel="apple-touch-icon" sizes="60x60" href="/favicon/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
		<link rel="manifest" href="/favicon/site.webmanifest">
		<link rel="mask-icon" href="/favicon/safari-pinned-tab.svg" color="#5bbad5">
		<meta name="msapplication-TileColor" content="#da532c">
		<meta name="theme-color" content="#ffffff">
	</Head>

	<TransitionRoot as="template" :show="sidebarOpen">
		<Dialog as="div" class="relative z-50 lg:hidden" @close="sidebarOpen = false">
			<TransitionChild as="template" enter="transition-opacity ease-linear duration-300" enter-from="opacity-0"
				enter-to="opacity-100" leave="transition-opacity ease-linear duration-300" leave-from="opacity-100"
				leave-to="opacity-0">
				<div class="fixed inset-0 bg-gray-950/80" />
			</TransitionChild>

			<div class="fixed inset-0 flex">
				<TransitionChild as="template" enter="transition ease-in-out duration-300 transform"
					enter-from="-translate-x-full" enter-to="translate-x-0"
					leave="transition ease-in-out duration-300 transform" leave-from="translate-x-0"
					leave-to="-translate-x-full">
					<DialogPanel class="relative flex flex-1 w-full max-w-xs mr-16">
						<TransitionChild as="template" enter="ease-in-out duration-300" enter-from="opacity-0"
							enter-to="opacity-100" leave="ease-in-out duration-300" leave-from="opacity-100"
							leave-to="opacity-0">
							<div class="absolute top-0 flex justify-center w-16 pt-5 left-full">
								<button type="button" class="-m-2.5 p-2.5" @click="sidebarOpen = false">
									<span class="sr-only">Close sidebar</span>
									<XMarkIcon class="w-6 h-6 text-white" aria-hidden="true" />
								</button>
							</div>
						</TransitionChild>
						<!-- Sidebar component, swap this element with another sidebar if you like -->
						<div
							class="flex flex-col px-6 pb-4 overflow-y-auto bg-white dark:bg-gray-950 dark:ring-1 dark:ring-white/10 grow gap-y-5">
							<div class="flex items-center h-16 shrink-0">
								<Link :href="route('admin.index')">
								<ApplicationLogo class="block w-auto h-9" :is-dark="isDark" />
								</Link>
							</div>
							<nav class="flex flex-col flex-1">
								<ul role="list" class="flex flex-col flex-1 gap-y-7">
									<li>
										<ul role="list" class="-mx-2 space-y-1">
											<li v-for="item in navigation" :key="item.name">
												<Link :href="item.href"
													:class="[item.current ? 'bg-gray-50 text-indigo-600 dark:bg-gray-800 dark:text-white' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800', 'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold']">
												<component :is="item.icon"
													:class="[item.current ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600', 'h-6 w-6 shrink-0']"
													aria-hidden="true" />
												{{ item.name }}
												</Link>
											</li>
										</ul>
									</li>
									<li class="mt-auto">
										<Link href="#"
											class="flex p-2 -mx-2 text-sm font-semibold leading-6 text-gray-700 rounded-md group gap-x-3 hover:bg-gray-50 hover:text-indigo-600 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white">
										<Cog6ToothIcon
											class="w-6 h-6 text-gray-400 dark:text-gray-100 shrink-0 group-hover:text-indigo-600"
											aria-hidden="true" />
										Impostazioni
										</Link>
									</li>
								</ul>
							</nav>
						</div>
					</DialogPanel>
				</TransitionChild>
			</div>
		</Dialog>
	</TransitionRoot>

	<!-- Static sidebar for desktop -->
	<div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
		<!-- Sidebar component, swap this element with another sidebar if you like -->
		<div
			class="flex flex-col px-6 pb-4 overflow-y-auto bg-white border-r border-gray-200 dark:border-gray-800 dark:bg-gray-950 grow gap-y-5 dark:ring-1 ring-white/10">
			<div class="flex items-center h-16 shrink-0">
				<Link :href="route('admin.index')">
				<ApplicationLogo class="block w-auto h-9" :is-dark="isDark" />
				</Link>
			</div>
			<nav class="flex flex-col flex-1">
				<ul role="list" class="flex flex-col flex-1 gap-y-7">
					<li>
						<ul role="list" class="-mx-2 space-y-1">
							<li v-for="item in navigation" :key="item.name">
								<Link :href="item.href"
									:class="[item.current ? 'bg-gray-50 text-indigo-600 dark:bg-gray-800 dark:text-white' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800', 'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold']">
								<component :is="item.icon"
									:class="[item.current ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600', 'h-6 w-6 shrink-0']"
									aria-hidden="true" />
								{{ item.name }}
								</Link>
							</li>
						</ul>
					</li>
					<li class="mt-auto">
						<Link href="#"
							class="flex p-2 -mx-2 text-sm font-semibold leading-6 text-gray-700 rounded-md dark:text-gray-400 group gap-x-3 hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:text-white hover:text-indigo-600">
						<Cog6ToothIcon class="w-6 h-6 text-gray-400 shrink-0 group-hover:text-indigo-600"
							aria-hidden="true" />
						Impostazioni
						</Link>
					</li>
				</ul>
			</nav>
		</div>
	</div>

	<div class="flex flex-col items-stretch justify-between min-h-screen lg:pl-72">
		<div
			class="sticky top-0 z-40 flex items-center h-16 px-4 bg-white border-b border-gray-200 shadow-xs dark:bg-gray-950 dark:border-gray-800 shrink-0 gap-x-4 sm:gap-x-6 sm:px-6 lg:px-8">
			<button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden" @click="sidebarOpen = true">
				<span class="sr-only">Open sidebar</span>
				<Bars3Icon class="w-6 h-6" aria-hidden="true" />
			</button>

			<!-- Separator -->
			<div class="w-px h-6 bg-gray-200 dark:bg-gray-950/10 lg:hidden" aria-hidden="true" />

			<div class="flex self-stretch flex-1 gap-x-4 lg:gap-x-6">
				<form class="relative flex flex-1" action="#" method="GET">
					<label for="search-field" class="sr-only">Search</label>
					<MagnifyingGlassIcon class="absolute inset-y-0 left-0 w-5 h-full text-gray-400 pointer-events-none"
						aria-hidden="true" />
					<input id="search-field"
						class="block w-full h-full py-0 pl-8 pr-0 border-0 text-gray-950 dark:text-gray-50 dark:placeholder:text-gray-300 dark:bg-gray-950 placeholder:text-gray-400 focus:ring-0 sm:text-sm"
						placeholder="Search..." type="search" name="search" />
				</form>
				<div class="flex items-center gap-x-4 lg:gap-x-6">

					<NotificationBellMenu :unreadNotifications="$page.props.unreadNotificationsCount" />

					<button type="button" class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500" @click="toggleDark()">
						<span class="sr-only">{{ isDark ? 'Light' : 'Dark' }} mode</span>
						<SunIcon v-if="isDark" class="w-6 h-6" aria-hidden="true" />
						<MoonIcon v-else class="w-6 h-6" aria-hidden="true" />
					</button>

					<!-- Separator -->
					<div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200 dark:lg:bg-gray-950/10"
						aria-hidden="true" />

					<!-- Profile dropdown -->
					<SecondaryButton @click="toggle" aria-haspopup="true" aria-controls="overlay_menu">
						<span class="sr-only">Open user menu</span>
							<img class="w-8 h-8 rounded-full bg-gray-50"
								src="https://upload.wikimedia.org/wikipedia/commons/7/7c/Profile_avatar_placeholder_large.png?20150327203541"
								alt="" />
						<span class="hidden lg:flex lg:items-center">
							<span class="ml-4 text-sm font-semibold leading-6 text-gray-700 dark:text-gray-400"
								aria-hidden="true">{{ $page.props.auth.user.email }} </span>
							<ChevronDownIcon class="w-5 h-5 ml-2 text-gray-400" aria-hidden="true" />
						</span>
					</SecondaryButton>
					<Menu ref="userMenu" id="overlay_menu" :model="userMenuItems" :popup="true" />
					<!-- <Menu as="div" class="relative">
						<MenuButton class="-m-1.5 flex items-center p-1.5">
							<span class="sr-only">Open user menu</span>
							<img class="w-8 h-8 rounded-full bg-gray-50"
								src="https://upload.wikimedia.org/wikipedia/commons/7/7c/Profile_avatar_placeholder_large.png?20150327203541"
								alt="" />
							<span class="hidden lg:flex lg:items-center">
								<span class="ml-4 text-sm font-semibold leading-6 text-gray-700 dark:text-gray-400"
									aria-hidden="true">{{ $page.props.auth.user.first_name }} {{
										$page.props.auth.user.last_name }}</span>
								<ChevronDownIcon class="w-5 h-5 ml-2 text-gray-400" aria-hidden="true" />
							</span>
						</MenuButton>
						<transition enter-active-class="transition duration-100 ease-out"
							enter-from-class="transform scale-95 opacity-0"
							enter-to-class="transform scale-100 opacity-100"
							leave-active-class="transition duration-75 ease-in"
							leave-from-class="transform scale-100 opacity-100"
							leave-to-class="transform scale-95 opacity-0">
							<MenuItems
								class="absolute right-0 z-10 mt-2.5 w-32 origin-top-right rounded-md bg-white dark:bg-gray-800 py-2 shadow-lg ring-1 ring-gray-950/5 dark:ring-gray-200/5 focus:outline-hidden">
								<MenuItem v-slot="{ active }">
								<Link
									:class="[active ? 'bg-gray-50 dark:bg-gray-950' : '', 'block px-3 py-1 text-sm leading-6 text-gray-950 dark:text-gray-50']"
									:href="route('profile.show')" :active="route().current('profile.show')">
								Profilo</Link>
								</MenuItem>
								<MenuItem v-slot="{ active }" v-if="$page.props.auth.user.company">
								<Link
									:class="[active ? 'bg-gray-50 dark:bg-gray-950' : '', 'block px-3 py-1 text-sm leading-6 text-gray-950 dark:text-gray-50']"
									:href="route('company.edit', { company: $page.props.auth.user.company })"
									:active="route().current('company.edit')">
								Dati Aziendali
								</Link>
								</MenuItem>
								<MenuItem v-slot="{ active }">
								<Link
									:class="[active ? 'bg-gray-50 dark:bg-gray-950' : '', 'block px-3 py-1 text-sm leading-6 text-gray-950 dark:text-gray-50']"
									:href="route('spark.portal')" :active="route().current('spark.portal')">
								Abbonamento</Link>
								</MenuItem>
								<MenuItem v-slot="{ active }">
								<Link method="post" :href="route('logout')"
									:class="[active ? 'bg-gray-50 dark:bg-gray-950' : '', 'block px-3 py-1 text-sm leading-6 text-gray-950 dark:text-gray-50']">
								Esci
								</Link>
								</MenuItem>
							</MenuItems>
						</transition>
					</Menu> -->
				</div>
			</div>
		</div>

		<PageHeader>
			<template #pageTitle>{{ title }}</template>
		</PageHeader>

		<main class="grow pt-5 pb-10 dark:bg-gray-950">
			<!-- Page Content -->
			<div class="flex flex-row flex-wrap items-start justify-start px-4 sm:px-6 lg:px-8">
				<slot />
			</div>
		</main>
		<MadeWithLove></MadeWithLove>
	</div>
</template>
