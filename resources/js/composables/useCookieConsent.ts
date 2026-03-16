import { ref, computed, readonly } from 'vue';

export type ConsentCategory = 'analytics' | 'marketing';

export interface CookieConsentData {
    v: string;
    timestamp: number;
    necessary: true;
    analytics: boolean;
    marketing: boolean;
}

// Current privacy policy version - bump this string to re-ask users after policy changes
const CONSENT_VERSION = '1.0';
const STORAGE_KEY = 'maglink_cookie_consent';

const stored = ref<CookieConsentData | null>(null);

function loadFromStorage(): CookieConsentData | null {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) return null;
        const parsed: CookieConsentData = JSON.parse(raw);
        // Invalidate if version changed
        if (parsed.v !== CONSENT_VERSION) return null;
        return parsed;
    } catch {
        return null;
    }
}

function persist(data: CookieConsentData) {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
    } catch {
        // silently fail (private browsing, storage full, etc.)
    }
    stored.value = data;
    window.dispatchEvent(new CustomEvent('cookie-consent-updated', { detail: data }));
}

// Singleton state - initialised once on first composable use
let initialised = false;

export function useCookieConsent() {
    if (!initialised) {
        stored.value = loadFromStorage();
        initialised = true;
    }

    /** true once the user has made a choice (any choice) */
    const hasDecided = computed(() => stored.value !== null);

    /** true if analytics cookies are accepted */
    const analyticsAllowed = computed(() => stored.value?.analytics ?? false);

    /** true if marketing cookies are accepted */
    const marketingAllowed = computed(() => stored.value?.marketing ?? false);

    function acceptAll() {
        persist({
            v: CONSENT_VERSION,
            timestamp: Date.now(),
            necessary: true,
            analytics: true,
            marketing: true,
        });
    }

    function rejectAll() {
        persist({
            v: CONSENT_VERSION,
            timestamp: Date.now(),
            necessary: true,
            analytics: false,
            marketing: false,
        });
    }

    function saveCustom(analytics: boolean, marketing: boolean) {
        persist({
            v: CONSENT_VERSION,
            timestamp: Date.now(),
            necessary: true,
            analytics,
            marketing,
        });
    }

    return {
        hasDecided: readonly(hasDecided),
        analyticsAllowed: readonly(analyticsAllowed),
        marketingAllowed: readonly(marketingAllowed),
        acceptAll,
        rejectAll,
        saveCustom,
    };
}
