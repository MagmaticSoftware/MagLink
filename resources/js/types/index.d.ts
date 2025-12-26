import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
    isAdmin?: boolean;
    tenant?: Tenant;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    label: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface Trial {
    active: boolean;
    expired: boolean;
    days_left: number;
    ends_at: string | null;
}

export interface Subscription {
    name: string | null;
    key: string | null;
    billing_type: 'monthly' | 'yearly' | null;
    active: boolean;
    ends_at: string | null;
    created_at: string | null;
    stripe_price: string | null;
    on_grace_period: boolean;
    cancelled: boolean;
}

export interface Billing {
    isNewUser: boolean;
    hasActiveTrial: boolean;
    canStartTrial: boolean;
    isSubscribed: boolean;
    onFreePlan: boolean;
    currentPlanName: string | null;
    currentPlanKey: string | null;
    hasAccess: boolean;
    limits: {
        links: number;
        qrcodes: number;
        qrcodes_dynamic?: number;
        pages: number;
        blocks_per_page?: number;
    };
}

export interface SharedData extends PageProps {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
    trial: Trial;
    subscription: Subscription | null;
    billing: Billing;
    plans: Record<string, any>;
}

export interface User {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    trial_ends_at?: string | null;
}

export type BreadcrumbItemType = BreadcrumbItem;
