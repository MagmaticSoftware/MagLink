<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();

const sidebarNavItems: NavItem[] = [
    {
        title: 'Profile',
        href: route('profile.edit', { tenant: page.props.auth.tenant }),
    },
    {
        title: 'Password',
        href: route('password.edit', { tenant: page.props.auth.tenant }),
    },
    {
        title: 'Appearance',
        href: route('appearance', { tenant: page.props.auth.tenant }),
    },
];

const currentPath = page.props.ziggy?.location ? new URL(page.props.ziggy.location).pathname : '';

const isCurrentPath = (href: string) => {
    try {
        return currentPath === new URL(href).pathname;
    } catch {
        return currentPath === href;
    }
};
</script>

<template>
    <div class="px-4 py-6">
        <Heading title="Settings" description="Manage your profile and account settings" />

        <div class="flex flex-col space-y-8 md:space-y-0 lg:flex-row lg:space-x-12 lg:space-y-0">
            <aside class="w-full max-w-xl lg:w-48">
                <nav class="flex flex-col space-x-0 space-y-1">
                    <Link
                        v-for="item in sidebarNavItems"
                        :key="item.href"
                        :href="item.href"
                        class="flex w-full items-center justify-start rounded-md px-3 py-2 text-sm font-medium text-surface-600 dark:text-surface-400 transition-colors hover:bg-surface-100 dark:hover:bg-surface-800 hover:text-surface-900 dark:hover:text-surface-100"
                        :class="{ 'bg-surface-100 dark:bg-surface-800 !text-surface-900 dark:!text-surface-50': isCurrentPath(item.href) }"
                    >
                        {{ item.title }}
                    </Link>
                </nav>
            </aside>

            <div class="my-6 md:hidden border-t border-surface-200 dark:border-surface-700" />

            <div class="flex-1 md:max-w-2xl">
                <section class="max-w-xl space-y-12">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
