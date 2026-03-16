<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, onUnmounted } from 'vue';
import MadeWithLove from '@/components/MadeWithLove.vue';
import CookieBanner from '@/components/CookieBanner.vue';

function openCookiePreferences() {
    window.dispatchEvent(new CustomEvent('open-cookie-preferences'));
}
</script>

<template>
    <Head title="Cookie Policy — MagLink">
        <meta name="description" content="Cookie Policy di MagLink — Informativa sull'utilizzo degli strumenti di tracciamento ai sensi del GDPR." />
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div class="min-h-screen bg-[#13131c] text-white flex flex-col">
        <!-- Header -->
        <header class="bg-[#13131c]/95 backdrop-blur-md sticky top-0 z-50 border-b border-white/[0.07]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between py-3">
                    <Link :href="route('home')" class="flex items-center">
                        <svg width="132" height="35" viewBox="0 0 461 123" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><rect id="Convertito" x="0" y="0" width="460.879" height="122.234" style="fill:none;"/><g><g><path d="M149.577,88.04l0,-54.985l7.509,0l22.291,35.666l-4.302,0l22.214,-35.666l7.508,0l0,54.985l-10.559,0l0,-36.761l2.034,0.626l-15.487,24.872l-7.196,0l-15.486,-24.872l2.112,-0.626l-0,36.761l-10.638,0Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M230.765,88.822c-3.442,0 -6.544,-0.86 -9.308,-2.581c-2.764,-1.72 -4.928,-4.067 -6.492,-7.039c-1.564,-2.972 -2.346,-6.309 -2.346,-10.012c-0,-3.754 0.782,-7.117 2.346,-10.089c1.564,-2.973 3.728,-5.319 6.492,-7.04c2.764,-1.72 5.866,-2.581 9.308,-2.581c2.711,0 5.136,0.548 7.274,1.643c2.137,1.095 3.845,2.62 5.123,4.575c1.277,1.956 1.968,4.159 2.072,6.609l0,13.61c-0.104,2.503 -0.795,4.719 -2.072,6.648c-1.278,1.929 -2.986,3.455 -5.123,4.576c-2.138,1.121 -4.563,1.681 -7.274,1.681Zm1.877,-9.464c2.868,0 5.188,-0.951 6.961,-2.854c1.773,-1.904 2.659,-4.368 2.659,-7.392c0,-1.981 -0.404,-3.741 -1.212,-5.279c-0.808,-1.539 -1.929,-2.738 -3.363,-3.598c-1.434,-0.861 -3.116,-1.291 -5.045,-1.291c-1.877,0 -3.533,0.43 -4.967,1.291c-1.434,0.86 -2.555,2.059 -3.363,3.598c-0.808,1.538 -1.212,3.298 -1.212,5.279c-0,2.034 0.404,3.82 1.212,5.358c0.808,1.538 1.929,2.737 3.363,3.598c1.434,0.86 3.09,1.29 4.967,1.29Zm9.073,8.682l-0,-10.168l1.642,-9.229l-1.642,-9.073l-0,-9.308l10.168,0l-0,37.778l-10.168,0Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M276.755,104.778c-4.015,0 -7.561,-0.717 -10.637,-2.151c-3.077,-1.434 -5.527,-3.454 -7.352,-6.061l6.491,-6.492c1.46,1.721 3.09,3.037 4.889,3.95c1.799,0.912 3.976,1.368 6.531,1.368c3.181,0 5.697,-0.808 7.548,-2.424c1.851,-1.617 2.776,-3.859 2.776,-6.727l0,-9.464l1.721,-8.291l-1.643,-8.29l0,-9.934l10.168,0l0,35.823c0,3.754 -0.873,7.026 -2.62,9.816c-1.747,2.79 -4.158,4.967 -7.235,6.531c-3.076,1.564 -6.622,2.346 -10.637,2.346Zm-0.469,-17.755c-3.39,0 -6.44,-0.821 -9.151,-2.463c-2.712,-1.643 -4.837,-3.898 -6.375,-6.766c-1.538,-2.868 -2.307,-6.075 -2.307,-9.62c-0,-3.546 0.769,-6.727 2.307,-9.543c1.538,-2.815 3.663,-5.045 6.375,-6.687c2.711,-1.643 5.761,-2.464 9.151,-2.464c2.816,0 5.305,0.548 7.469,1.643c2.164,1.095 3.872,2.594 5.123,4.497c1.252,1.903 1.93,4.132 2.034,6.687l-0,11.889c-0.104,2.503 -0.795,4.732 -2.073,6.688c-1.277,1.955 -2.998,3.467 -5.162,4.536c-2.164,1.069 -4.628,1.603 -7.391,1.603Zm2.033,-9.307c1.878,-0 3.507,-0.404 4.889,-1.212c1.382,-0.809 2.464,-1.93 3.246,-3.364c0.782,-1.434 1.173,-3.063 1.173,-4.888c-0,-1.877 -0.391,-3.52 -1.173,-4.928c-0.782,-1.408 -1.864,-2.516 -3.246,-3.324c-1.382,-0.808 -3.011,-1.212 -4.889,-1.212c-1.877,-0 -3.519,0.404 -4.927,1.212c-1.408,0.808 -2.503,1.929 -3.285,3.363c-0.782,1.434 -1.173,3.064 -1.173,4.889c-0,1.773 0.391,3.376 1.173,4.81c0.782,1.434 1.877,2.568 3.285,3.402c1.408,0.835 3.05,1.252 4.927,1.252Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M307.337,88.04l0,-54.985l10.637,0l0,54.985l-10.637,0Zm7.822,0l-0,-9.464l28.001,0l-0,9.464l-28.001,0Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M349.73,88.04l-0,-37.778l10.324,0l0,37.778l-10.324,0Zm5.162,-43.878c-1.669,-0 -3.05,-0.561 -4.145,-1.682c-1.095,-1.121 -1.643,-2.516 -1.643,-4.185c0,-1.616 0.548,-2.998 1.643,-4.145c1.095,-1.147 2.476,-1.721 4.145,-1.721c1.721,0 3.115,0.574 4.184,1.721c1.069,1.147 1.604,2.529 1.604,4.145c-0,1.669 -0.535,3.064 -1.604,4.185c-1.069,1.121 -2.463,1.682 -4.184,1.682Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M394.547,88.04l-0,-21.665c-0,-2.243 -0.704,-4.068 -2.112,-5.475c-1.408,-1.408 -3.233,-2.112 -5.475,-2.112c-1.46,-0 -2.763,0.313 -3.911,0.938c-1.147,0.626 -2.046,1.513 -2.698,2.66c-0.652,1.147 -0.978,2.476 -0.978,3.989l-3.989,-2.034c0,-2.972 0.639,-5.566 1.917,-7.782c1.277,-2.216 3.05,-3.95 5.318,-5.202c2.268,-1.251 4.836,-1.877 7.704,-1.877c2.764,0 5.241,0.691 7.431,2.073c2.19,1.382 3.911,3.181 5.162,5.397c1.251,2.216 1.877,4.601 1.877,7.156l0,23.934l-10.246,0Zm-25.42,0l0,-37.778l10.246,0l0,37.778l-10.246,0Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M437.174,88.04l-14.626,-19.475l14.548,-18.303l11.81,0l-17.05,20.649l0.391,-5.006l17.442,22.135l-12.515,0Zm-24.09,0l-0,-56.549l10.246,-0l0,56.549l-10.246,0Z" style="fill:#fff;fill-rule:nonzero;"/></g><path d="M25.176,96.816c-8.679,-9.38 -13.986,-21.925 -13.986,-35.699c-0,-29.027 23.566,-52.592 52.592,-52.592c29.027,-0 52.592,23.565 52.592,52.592c0,11.105 -3.449,21.411 -9.334,29.904l-0.048,-2.446c-0.087,-4.064 -0.992,-8.013 -2.564,-11.613c-2.355,-5.4 -6.187,-10.048 -10.968,-13.413c-1.015,-0.717 -2.084,-1.371 -3.186,-1.97l0.378,19.084c1.223,2.212 1.954,4.708 2.01,7.379l0.327,16.807c-2.697,1.806 -5.571,3.37 -8.589,4.659l-0.521,-56.659c-0.085,-4.066 -0.987,-8.013 -2.559,-11.613c-2.353,-5.408 -6.18,-10.05 -10.965,-13.41c-4.771,-3.371 -10.546,-5.447 -16.648,-5.675c-4.059,-0.15 -7.951,0.536 -11.464,1.908c-5.273,2.058 -9.701,5.631 -12.803,10.228c-3.097,4.603 -4.848,10.264 -4.733,16.366l0.469,46.163Zm23.137,14.577c-3.028,-0.931 -5.94,-2.13 -8.708,-3.566l-0.577,-56.636c-0.031,-2.131 0.348,-4.106 1.088,-5.892c1.095,-2.689 2.997,-4.972 5.4,-6.528c2.406,-1.553 5.297,-2.4 8.473,-2.285c2.13,0.079 4.128,0.582 5.97,1.417c2.743,1.256 5.13,3.286 6.82,5.792c1.698,2.503 2.709,5.444 2.772,8.624l0.58,61.01c-2.082,0.251 -4.2,0.38 -6.349,0.38c-0.372,0 -0.742,-0.004 -1.112,-0.012l-0.529,-26.78c-0.034,-2.134 0.349,-4.101 1.078,-5.903c0.188,-0.454 0.419,-0.901 0.647,-1.341l-0.375,-19.087c-4.462,2.122 -8.217,5.377 -10.945,9.43c-3.091,4.597 -4.861,10.255 -4.722,16.366l0.489,25.011Z" style="fill:url(#_Linear1_cp);"/></g><defs><linearGradient id="_Linear1_cp" x1="0" y1="0" x2="1" y2="0" gradientUnits="userSpaceOnUse" gradientTransform="matrix(8.74093e-15,-105.184,142.75,6.44068e-15,63.7821,113.709)"><stop offset="0" style="stop-color:#ff1f2b;stop-opacity:1"/><stop offset="1" style="stop-color:#ff682f;stop-opacity:1"/></linearGradient></defs></svg>
                    </Link>
                    <Link :href="route('home')" class="text-sm text-gray-400 hover:text-white transition-colors">
                        ← Torna alla home
                    </Link>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="flex-1 py-16">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <article>
                    <!-- Page title -->
                    <header class="mb-12 pb-8 border-b border-white/[0.08]">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-600/10 border border-orange-500/20 mb-6">
                            <span class="text-xs text-orange-400 font-medium">magmatic.it</span>
                        </div>
                        <h1 class="text-3xl sm:text-4xl font-bold text-white mb-4">Cookie Policy</h1>
                        <p class="text-gray-400 leading-relaxed max-w-2xl mb-3">
                            Benvenuto nella cookie policy di magmatic.it. Questa policy ti aiuterà a comprendere quali cookie e tecnologie di tracciamento utilizziamo, come li utilizziamo e quali sono i tuoi diritti in merito.
                        </p>
                        <p class="text-sm text-gray-600">Ultima modifica: 19 febbraio 2025</p>
                    </header>

                    <div class="space-y-12">

                        <!-- Introduzione -->
                        <section>
                            <h2 class="text-xl font-bold text-white mb-5">Introduzione</h2>
                            <div class="space-y-4 text-gray-400 text-sm leading-relaxed">
                                <p>Questo documento contiene informazioni in merito alle tecnologie che consentono a questa Applicazione di raggiungere gli scopi descritti di seguito. Tali tecnologie permettono al Titolare di raccogliere e salvare informazioni (per esempio tramite l'utilizzo di Cookie) o di utilizzare risorse (per esempio eseguendo uno script) sul dispositivo dell'Utente quando quest'ultimo interagisce con questa Applicazione.</p>
                                <p>Per semplicità, in questo documento tali tecnologie sono sinteticamente definite "Strumenti di Tracciamento", salvo vi sia ragione di differenziare. Per esempio, sebbene i Cookie possano essere usati in browser sia web sia mobili, sarebbe fuori luogo parlare di Cookie nel contesto di applicazioni per dispositivi mobili, dal momento che si tratta di Strumenti di Tracciamento che richiedono la presenza di un browser. Per questo motivo, all'interno di questo documento il termine Cookie è utilizzato solo per indicare in modo specifico quel particolare tipo di Strumento di Tracciamento.</p>
                                <p>Alcune delle finalità per le quali vengono impiegati Strumenti di Tracciamento potrebbero, inoltre richiedere il consenso dell'Utente. Se viene prestato il consenso, esso può essere revocato liberamente in qualsiasi momento seguendo le istruzioni contenute in questo documento.</p>
                                <p>Questa Applicazione utilizza Strumenti di Tracciamento gestiti direttamente dal Titolare (comunemente detti Strumenti di Tracciamento "di prima parte") e Strumenti di Tracciamento che abilitano servizi forniti da terzi (comunemente detti Strumenti di Tracciamento "di terza parte"). Se non diversamente specificato all'interno di questo documento, tali terzi hanno accesso ai rispettivi Strumenti di Tracciamento.<br>Durata e scadenza dei Cookie e degli altri Strumenti di Tracciamento simili possono variare a seconda di quanto impostato dal Titolare o da ciascun fornitore terzo. Alcuni di essi scadono al termine della sessione di navigazione dell'Utente.</p>
                            </div>
                        </section>

                        <!-- Titolare -->
                        <section class="pt-8 border-t border-white/[0.08]">
                            <h2 class="text-xl font-bold text-white mb-5">Titolare del Trattamento dei Dati</h2>
                            <div class="bg-white/[0.03] border border-white/[0.08] rounded-xl p-5 text-sm text-gray-400 leading-relaxed space-y-2">
                                <p class="font-medium text-gray-300">Magmatic di Fabio De Notariis</p>
                                <p>Viale Giulio Cesare, 99<br>86035 Larino (CB)<br>P.IVA 01908340704</p>
                                <p><strong class="text-gray-300">Indirizzo email del Titolare:</strong> <a href="mailto:info@magmatic.it" class="text-orange-400 hover:text-orange-300">info@magmatic.it</a></p>
                            </div>
                        </section>

                        <!-- Strumenti di Tracciamento -->
                        <section class="pt-8 border-t border-white/[0.08]">
                            <h2 class="text-xl font-bold text-white mb-6">Come questa Applicazione utilizza gli Strumenti di Tracciamento</h2>

                            <!-- Necessari -->
                            <div class="mb-8">
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/10 text-green-400 border border-green-500/20">Necessari</span>
                                </div>
                                <p class="text-gray-400 text-sm mb-4 leading-relaxed">Questa Applicazione utilizza Cookie comunemente detti "tecnici" o altri Strumenti di Tracciamento analoghi per svolgere attività strettamente necessarie a garantire il funzionamento o la fornitura del Servizio.</p>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Strumenti di Tracciamento gestiti da terze parti</p>

                                <details class="group border border-white/10 rounded-xl bg-white/[0.02] overflow-hidden">
                                    <summary class="flex items-center gap-4 px-5 py-4 cursor-pointer hover:bg-white/[0.04] transition-colors [&::-webkit-details-marker]:hidden list-none">
                                        <div class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center flex-shrink-0 text-xs font-bold text-gray-400">G</div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-sm text-white">Google Tag Manager</p>
                                            <p class="text-xs text-gray-500 mt-0.5">Google Ireland Limited · Irlanda · Dati di utilizzo, Strumenti di Tracciamento</p>
                                        </div>
                                        <svg class="w-4 h-4 text-gray-500 transition-transform group-open:rotate-180 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </summary>
                                    <div class="px-5 pb-5 pt-4 border-t border-white/[0.06] text-sm text-gray-400 leading-relaxed space-y-3">
                                        <p>Google Tag Manager è un servizio di gestione dei tag fornito da Google Ireland Limited. Per conoscere l'utilizzo dei Dati da parte di Google, consulta la loro <a href="https://policies.google.com/technologies/partner-sites" target="_blank" rel="noopener noreferrer" class="text-orange-400 hover:text-orange-300 underline underline-offset-2">partner policy</a> e la loro <a href="https://business.safety.google/privacy/" target="_blank" rel="noopener noreferrer" class="text-orange-400 hover:text-orange-300 underline underline-offset-2">pagina dei Dati Commerciali</a>.</p>
                                        <div class="grid sm:grid-cols-2 gap-4 mt-4">
                                            <div class="bg-white/[0.03] rounded-lg p-3">
                                                <p class="text-xs font-semibold text-gray-300 mb-2">Dati Personali trattati:</p>
                                                <ul class="text-xs text-gray-500 space-y-1">
                                                    <li>· Dati di utilizzo</li>
                                                    <li>· Strumenti di Tracciamento</li>
                                                </ul>
                                            </div>
                                            <div class="bg-white/[0.03] rounded-lg p-3">
                                                <p class="text-xs font-semibold text-gray-300 mb-2">Servizio fornito da:</p>
                                                <p class="text-xs text-gray-500">Google Ireland Limited (Irlanda) – <a href="https://business.safety.google/privacy/" target="_blank" rel="noopener noreferrer" class="text-orange-400 hover:text-orange-300">Privacy Policy</a></p>
                                            </div>
                                        </div>
                                    </div>
                                </details>
                            </div>

                            <!-- Misurazione -->
                            <div class="mb-8">
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-500/10 text-blue-400 border border-blue-500/20">Misurazione</span>
                                </div>
                                <p class="text-gray-400 text-sm mb-4 leading-relaxed">Questa Applicazione utilizza Strumenti di Tracciamento per misurare il traffico e analizzare il comportamento degli Utenti per migliorare il Servizio.</p>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Strumenti di Tracciamento gestiti da terze parti</p>

                                <details class="group border border-white/10 rounded-xl bg-white/[0.02] overflow-hidden">
                                    <summary class="flex items-center gap-4 px-5 py-4 cursor-pointer hover:bg-white/[0.04] transition-colors [&::-webkit-details-marker]:hidden list-none">
                                        <div class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center flex-shrink-0 text-xs font-bold text-gray-400">G</div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-sm text-white">Google Analytics 4</p>
                                            <p class="text-xs text-gray-500 mt-0.5">Google Ireland Limited · Irlanda · Dati di utilizzo, statistiche sessioni</p>
                                        </div>
                                        <svg class="w-4 h-4 text-gray-500 transition-transform group-open:rotate-180 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </summary>
                                    <div class="px-5 pb-5 pt-4 border-t border-white/[0.06] text-sm text-gray-400 leading-relaxed space-y-3">
                                        <p>Google Analytics è un servizio di statistica fornito da Google Ireland Limited ("Google"). Google utilizza i Dati Personali raccolti allo scopo di tracciare ed esaminare l'utilizzo di questa Applicazione, compilare report e condividerli con gli altri servizi sviluppati da Google. In Google Analytics 4, gli indirizzi IP vengono utilizzati al momento della raccolta e poi eliminati prima che i dati vengano registrati in qualsiasi data center o server. Per saperne di più, è possibile consultare la <a href="https://support.google.com/analytics/answer/12017362?hl=it" target="_blank" rel="noopener noreferrer" class="text-orange-400 hover:text-orange-300 underline underline-offset-2">documentazione ufficiale di Google</a>.</p>
                                        <p>Per conoscere l'utilizzo dei Dati da parte di Google, consulta la loro <a href="https://policies.google.com/technologies/partner-sites" target="_blank" rel="noopener noreferrer" class="text-orange-400 hover:text-orange-300 underline underline-offset-2">partner policy</a>.</p>
                                        <div class="grid sm:grid-cols-2 gap-4 mt-4">
                                            <div class="bg-white/[0.03] rounded-lg p-3">
                                                <p class="text-xs font-semibold text-gray-300 mb-2">Dati Personali trattati:</p>
                                                <ul class="text-xs text-gray-500 space-y-1">
                                                    <li>· Dati di utilizzo</li>
                                                    <li>· numero di Utenti</li>
                                                    <li>· statistiche delle sessioni</li>
                                                    <li>· Strumenti di Tracciamento</li>
                                                </ul>
                                            </div>
                                            <div class="bg-white/[0.03] rounded-lg p-3">
                                                <p class="text-xs font-semibold text-gray-300 mb-2">Servizio e durata:</p>
                                                <p class="text-xs text-gray-500">Google Ireland Limited (Irlanda) – <a href="https://business.safety.google/privacy/" target="_blank" rel="noopener noreferrer" class="text-orange-400 hover:text-orange-300">Privacy Policy</a> – <a href="https://tools.google.com/dlpage/gaoptout?hl=en" target="_blank" class="text-orange-400 hover:text-orange-300">Opt out</a></p>
                                                <ul class="text-xs text-gray-500 mt-2 space-y-1">
                                                    <li>· _ga: 2 anni</li>
                                                    <li>· _ga_*: 2 anni</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </details>
                            </div>

                        </section>

                        <!-- Come gestire le preferenze -->
                        <section class="pt-8 border-t border-white/[0.08]">
                            <h2 class="text-xl font-bold text-white mb-5">Come gestire le preferenze su questa Applicazione</h2>
                            <div class="space-y-4 text-gray-400 text-sm leading-relaxed">
                                <p>Gli Utenti possono impostare o aggiornare le proprie preferenze tramite il relativo pannello delle scelte in materia di privacy disponibile su questa Applicazione.</p>

                                <!-- CTA gestisci preferenze -->
                                <div class="bg-orange-600/10 border border-orange-500/20 rounded-xl p-5 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-white text-sm">Gestisci le tue preferenze sui cookie</p>
                                        <p class="text-xs text-gray-400 mt-1">Puoi modificare o revocare il tuo consenso in qualsiasi momento.</p>
                                    </div>
                                    <button
                                        @click="openCookiePreferences"
                                        class="flex-shrink-0 text-xs bg-orange-600 hover:bg-orange-500 text-white font-semibold rounded-lg px-4 py-2 transition-colors"
                                    >
                                        Gestisci preferenze
                                    </button>
                                </div>

                                <p>Per quanto riguarda Strumenti di Tracciamento di terza parte, gli Utenti possono gestire le proprie preferenze visitando il relativo link di opt out (qualora disponibile), utilizzando gli strumenti descritti nella privacy policy della terza parte o contattando quest'ultima direttamente.</p>

                                <h3 class="font-bold text-white pt-2">Come controllare o eliminare Cookie tramite le impostazioni del browser</h3>
                                <p>Gli Utenti possono utilizzare le impostazioni del proprio browser per vedere, bloccare o cancellare i Cookie impostati sul proprio dispositivo. Le impostazioni del browser, tuttavia, non consentono un controllo granulare del consenso per categoria.</p>
                                <p>Informazioni su come gestire i Cookie nei browser più diffusi:</p>
                                <ul class="space-y-1.5 ml-4">
                                    <li><a href="https://support.google.com/chrome/answer/95647?hl=it" target="_blank" rel="noopener nofollow" class="text-orange-400 hover:text-orange-300 underline underline-offset-2">Google Chrome</a></li>
                                    <li><a href="https://support.mozilla.org/it/kb/Attivare%20e%20disattivare%20i%20cookie" target="_blank" rel="noopener nofollow" class="text-orange-400 hover:text-orange-300 underline underline-offset-2">Mozilla Firefox</a></li>
                                    <li><a href="https://support.apple.com/it-it/guide/safari/manage-cookies-and-website-data-sfri11471/" target="_blank" rel="noopener nofollow" class="text-orange-400 hover:text-orange-300 underline underline-offset-2">Apple Safari</a></li>
                                    <li><a href="https://support.microsoft.com/it-it/help/4027947" target="_blank" rel="noopener nofollow" class="text-orange-400 hover:text-orange-300 underline underline-offset-2">Microsoft Edge</a></li>
                                    <li><a href="https://support.brave.com/hc/articles/360022806212-How-do-I-use-Shields-while-browsing" target="_blank" rel="noopener nofollow" class="text-orange-400 hover:text-orange-300 underline underline-offset-2">Brave</a></li>
                                    <li><a href="https://help.opera.com/latest/web-preferences/#cookies" target="_blank" rel="noopener nofollow" class="text-orange-400 hover:text-orange-300 underline underline-offset-2">Opera</a></li>
                                </ul>

                                <h3 class="font-bold text-white pt-2">Come disattivare la pubblicità basata sugli interessi</h3>
                                <p>Fermo restando quanto precede, gli Utenti possono avvalersi delle informazioni presenti su <a href="http://www.youronlinechoices.eu/" target="_blank" rel="noopener nofollow" class="text-orange-400 hover:text-orange-300 underline underline-offset-2">YourOnlineChoices</a> (UE), <a href="https://thenai.org/about-online-advertising/" target="_blank" rel="noopener nofollow" class="text-orange-400 hover:text-orange-300 underline underline-offset-2">Network Advertising Initiative</a> (USA) e <a href="https://www.aboutads.info/consumers/" target="_blank" rel="noopener nofollow" class="text-orange-400 hover:text-orange-300 underline underline-offset-2">Digital Advertising Alliance</a> (USA).</p>

                                <h3 class="font-bold text-white pt-2">Conseguenze legate al rifiuto degli Strumenti di Tracciamento</h3>
                                <p>Gli Utenti sono liberi di decidere se permettere o meno l'utilizzo di Strumenti di Tracciamento. Tuttavia, si noti che gli Strumenti di Tracciamento consentono a questa Applicazione di fornire agli Utenti un'esperienza migliore e funzionalità avanzate. Pertanto, qualora l'Utente decida di bloccare l'utilizzo di Strumenti di Tracciamento, il Titolare potrebbe non essere in grado di fornire le relative funzionalità.</p>
                            </div>
                        </section>

                        <!-- Definizioni -->
                        <section class="pt-8 border-t border-white/[0.08]">
                            <h2 class="text-xl font-bold text-white mb-5">Definizioni e riferimenti legali</h2>
                            <div class="space-y-5 text-sm text-gray-400 leading-relaxed">
                                <div>
                                    <h3 class="font-semibold text-gray-300 mb-1">Dati Personali (o Dati)</h3>
                                    <p>Costituisce dato personale qualunque informazione che, direttamente o indirettamente, anche in collegamento con qualsiasi altra informazione, ivi compreso un numero di identificazione personale, renda identificata o identificabile una persona fisica.</p>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-300 mb-1">Dati di Utilizzo</h3>
                                    <p>Sono le informazioni raccolte automaticamente attraverso questa Applicazione, tra cui: gli indirizzi IP o i nomi a dominio dei computer utilizzati dall'Utente, gli indirizzi URI, l'orario della richiesta, il metodo utilizzato nell'inoltrare la richiesta al server, la dimensione del file ottenuto in risposta, il codice numerico indicante lo stato della risposta dal server, il paese di provenienza, le caratteristiche del browser e del sistema operativo utilizzati dal visitatore, le varie connotazioni temporali della visita e i dettagli relativi all'itinerario seguito all'interno dell'Applicazione.</p>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-300 mb-1">Utente</h3>
                                    <p>L'individuo che utilizza questa Applicazione che, salvo ove diversamente specificato, coincide con l'Interessato.</p>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-300 mb-1">Interessato</h3>
                                    <p>La persona fisica cui si riferiscono i Dati Personali.</p>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-300 mb-1">Titolare del Trattamento (o Titolare)</h3>
                                    <p>La persona fisica o giuridica che, singolarmente o insieme ad altri, determina le finalità e i mezzi del trattamento di dati personali e gli strumenti adottati, ivi comprese le misure di sicurezza relative al funzionamento ed alla fruizione di questa Applicazione.</p>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-300 mb-1">Cookie</h3>
                                    <p>I Cookie sono Strumenti di Tracciamento che consistono in piccole porzioni di dati conservate all'interno del browser dell'Utente.</p>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-300 mb-1">Strumento di Tracciamento</h3>
                                    <p>Per Strumento di Tracciamento s'intende qualsiasi tecnologia — es. Cookie, identificativi univoci, web beacons, script integrati, e-tag e fingerprinting — che consenta di tracciare gli Utenti, per esempio raccogliendo o salvando informazioni sul dispositivo dell'Utente.</p>
                                </div>
                                <div class="pt-4 border-t border-white/[0.06]">
                                    <h3 class="font-semibold text-gray-300 mb-1">Riferimenti legali</h3>
                                    <p>La presente policy è redatta sulla base di molteplici ordinamenti legislativi. Ove non diversamente specificato, questa policy riguarda esclusivamente questa Applicazione.</p>
                                </div>
                            </div>
                        </section>

                    </div>
                </article>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-[#0d0d14] border-t border-white/[0.07] py-8 mt-auto">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <MadeWithLove />
            </div>
        </footer>
    </div>

    <!-- Cookie Banner (for the floating preferences button) -->
    <CookieBanner />
</template>
