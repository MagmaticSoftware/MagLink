import { usePage } from '@inertiajs/vue3';
import type { PageProps } from '@/types/inertia';

/**
 * Wrapper per route() di Ziggy che aggiunge automaticamente il parametro tenant
 * se l'utente è autenticato e ha un tenant
 */
export function useRoute() {
    const page = usePage<PageProps>();
    
    return (name?: string, params?: any, absolute?: boolean) => {
        // Se non c'è nome route, ritorna la funzione route normale
        if (!name) {
            return route();
        }
        
        // Prepara i parametri
        let finalParams = params || {};
        
        // Se i parametri non sono un oggetto (es. un ID diretto), convertili
        // usando il nome corretto del parametro basato sulla route
        if (typeof finalParams !== 'object' || Array.isArray(finalParams)) {
            // Estrai il nome della risorsa dalla route (es: 'links.edit' -> 'link')
            const resourceMatch = name.match(/^(links|qrcodes|pages|page-blocks)\.(?:show|edit|update|destroy)/);
            if (resourceMatch) {
                const resource = resourceMatch[1];
                // Converti plurale in singolare per il nome del parametro
                const paramName = resource === 'links' ? 'link'
                    : resource === 'qrcodes' ? 'qrcode'
                    : resource === 'pages' ? 'page'
                    : resource === 'page-blocks' ? 'block'
                    : resource;
                
                finalParams = { [paramName]: finalParams };
            } else {
                finalParams = { id: finalParams };
            }
        }
        
        // Aggiungi automaticamente tenant se:
        // 1. L'utente è autenticato
        // 2. Ha un tenant
        // 3. Il parametro tenant non è già presente
        // 4. La route richiede un tenant (routes che iniziano con tenant., links., qrcodes., pages., profile., password., appearance, plans.)
        const tenantRoutes = ['tenant.', 'links.', 'qrcodes.', 'pages.', 'profile.', 'password.', 'appearance', 'plans.', 'checkout.', 'billing.', 'page-blocks.'];
        const needsTenant = tenantRoutes.some(prefix => name.startsWith(prefix));
        
        if (needsTenant && page.props.auth?.tenant && !finalParams.tenant) {
            finalParams = {
                tenant: page.props.auth.tenant,
                ...finalParams
            };
        }
        
        return route(name, finalParams, absolute);
    };
}
