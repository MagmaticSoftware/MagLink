<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { SidebarMenu, SidebarMenuButton, SidebarMenuItem, useSidebar } from '@/components/ui/sidebar';
import { type SharedData, type User } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronUp, FileDigit, Settings, CreditCard } from 'lucide-vue-next';
import UserMenuContent from './UserMenuContent.vue';
import Menu from './volt/Menu.vue';
import SecondaryButton from './volt/SecondaryButton.vue';
import Badge from './volt/Badge.vue';
import { ref } from 'vue';

import { useDark, useToggle } from '@vueuse/core';
import { LogOut, Moon, SunMedium } from 'lucide-vue-next';


const isDark = useDark();
const toggleDark = useToggle(isDark);
const page = usePage<SharedData>();
const user = page.props.auth.user as User;
const { isMobile, state } = useSidebar();

const MenuItems = [
    {
        label: 'Settings',
        icon: Settings,
        href: route('profile.edit', { tenant: page.props.auth.tenant }),
    },
    {
        label: 'Piani e Prezzi',
        icon: CreditCard,
        href: route('plans.index', { tenant: page.props.auth.tenant }),
    },
];

const menu = ref();

const toggle = (event) => {
    menu.value.toggle(event);
};
</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem>
            <SecondaryButton type="button" plain @click="toggle" aria-haspopup="true" aria-controls="overlay_menu"><UserInfo :user="user" /> <ChevronUp /></SecondaryButton>
            <Menu ref="menu" id="overlay_menu" :model="MenuItems" :popup="true">
                <template #start>
                    <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                        <UserInfo :user="user" :show-email="true" />
                    </div>
                </template>
                <template #item="{ item }">
                    <Link class="flex items-center" :href="item.href">
                        <template v-if="item.icon">
                            <component :is="item.icon" class="mr-3" :size="18" />
                        </template>
                        <span>{{ item.label }}</span>
                        <Badge v-if="item.badge" class="ml-auto" :value="item.badge" />
                        <span v-if="item.shortcut" class="ml-auto border border-surface rounded bg-emphasis text-muted-color text-xs p-1">{{ item.shortcut }}</span>
                    </Link>
                </template>
                <template #end>
                    <button type="button" class="block w-full" @click="toggleDark()">
                        <SunMedium v-if="isDark" class="mr-2 h-4 w-4" aria-hidden="true" />
                        <Moon v-else class="mr-2 h-4 w-4" aria-hidden="true" />
                        {{ isDark ? 'Light' : 'Dark' }} mode
                    </button>
                    <Link class="block w-full" method="post" :href="route('logout')" as="button">
                        <LogOut class="mr-2 h-4 w-4" />
                        Log out
                    </Link>
                </template>
            </Menu>
        </SidebarMenuItem>
    </SidebarMenu>
</template>
