interface AuthUser {
    id: number;
    company: {
        id: number;
        [key: string]: any;
    };
    tenant_id: string;
    [key: string]: any;
}

export interface PageProps {
    auth: {
        user: AuthUser;
        tenant: string | null;
        isAdmin?: boolean;
        [key: string]: any;
    };
    trial?: {
        active: boolean;
        expired: boolean;
        days_left: number;
        ends_at: string | null;
    };
    subscription?: {
        name: string | null;
        key: string | null;
        billing_type: 'monthly' | 'yearly' | null;
        active: boolean;
        ends_at: string | null;
        created_at: string | null;
        stripe_price: string | null;
        on_grace_period: boolean;
        cancelled: boolean;
    } | null;
    billing?: {
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
    };
    plans?: Record<string, any>;
    [key: string]: any;
}