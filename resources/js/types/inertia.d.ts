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
        [key: string]: any;
    };
    [key: string]: any;
}