import { createI18n } from 'vue-i18n';
import en from './locales/en.json';
import it from './locales/it.json';
import es from './locales/es.json';
import de from './locales/de.json';
import pt from './locales/pt.json';

const supportedLocales = ['en', 'it', 'es', 'de', 'pt'];

// Detect browser language or use stored preference
const getBrowserLocale = (): string => {
    const stored = localStorage.getItem('locale');
    if (stored && supportedLocales.includes(stored)) {
        return stored;
    }
    
    const browserLang = navigator.language.split('-')[0];
    return supportedLocales.includes(browserLang) ? browserLang : 'en';
};

export const i18n = createI18n({
    legacy: false,
    locale: getBrowserLocale(),
    fallbackLocale: 'en',
    messages: {
        en,
        it,
        es,
        de,
        pt
    },
    globalInjection: true
});

export const availableLocales = [
    { code: 'en', name: 'English', flag: '🇬🇧' },
    { code: 'it', name: 'Italiano', flag: '🇮🇹' },
    { code: 'es', name: 'Español', flag: '🇪🇸' },
    { code: 'de', name: 'Deutsch', flag: '🇩🇪' },
    { code: 'pt', name: 'Português', flag: '🇵🇹' }
];

export const setLocale = (locale: string) => {
    if (supportedLocales.includes(locale)) {
        i18n.global.locale.value = locale;
        localStorage.setItem('locale', locale);
        document.documentElement.setAttribute('lang', locale);
    }
};
