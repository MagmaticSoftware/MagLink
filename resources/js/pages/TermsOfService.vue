<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import MadeWithLove from '@/components/MadeWithLove.vue';
import CookieBanner from '@/components/CookieBanner.vue';
</script>

<template>
    <Head title="Termini di Servizio — MagLink">
        <meta name="description" content="Termini di Servizio di MagLink — Leggi le condizioni per l'utilizzo della piattaforma." />
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div class="min-h-screen bg-[#13131c] text-white flex flex-col">
        <!-- Header -->
        <header class="bg-[#13131c]/95 backdrop-blur-md sticky top-0 z-50 border-b border-white/[0.07]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between py-3">
                    <Link :href="route('home')" class="flex items-center">
                        <svg width="132" height="35" viewBox="0 0 461 123" version="1.1" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><rect x="0" y="0" width="460.879" height="122.234" style="fill:none;"/><g><g><path d="M149.577,88.04l0,-54.985l7.509,0l22.291,35.666l-4.302,0l22.214,-35.666l7.508,0l0,54.985l-10.559,0l0,-36.761l2.034,0.626l-15.487,24.872l-7.196,0l-15.486,-24.872l2.112,-0.626l-0,36.761l-10.638,0Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M230.765,88.822c-3.442,0 -6.544,-0.86 -9.308,-2.581c-2.764,-1.72 -4.928,-4.067 -6.492,-7.039c-1.564,-2.972 -2.346,-6.309 -2.346,-10.012c-0,-3.754 0.782,-7.117 2.346,-10.089c1.564,-2.973 3.728,-5.319 6.492,-7.04c2.764,-1.72 5.866,-2.581 9.308,-2.581c2.711,0 5.136,0.548 7.274,1.643c2.137,1.095 3.845,2.62 5.123,4.575c1.277,1.956 1.968,4.159 2.072,6.609l0,13.61c-0.104,2.503 -0.795,4.719 -2.072,6.648c-1.278,1.929 -2.986,3.455 -5.123,4.576c-2.138,1.121 -4.563,1.681 -7.274,1.681Zm1.877,-9.464c2.868,0 5.188,-0.951 6.961,-2.854c1.773,-1.904 2.659,-4.368 2.659,-7.392c0,-1.981 -0.404,-3.741 -1.212,-5.279c-0.808,-1.539 -1.929,-2.738 -3.363,-3.598c-1.434,-0.861 -3.116,-1.291 -5.045,-1.291c-1.877,0 -3.533,0.43 -4.967,1.291c-1.434,0.86 -2.555,2.059 -3.363,3.598c-0.808,1.538 -1.212,3.298 -1.212,5.279c-0,2.034 0.404,3.82 1.212,5.358c0.808,1.538 1.929,2.737 3.363,3.598c1.434,0.86 3.09,1.29 4.967,1.29Zm9.073,8.682l-0,-10.168l1.642,-9.229l-1.642,-9.073l-0,-9.308l10.168,0l-0,37.778l-10.168,0Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M276.755,104.778c-4.015,0 -7.561,-0.717 -10.637,-2.151c-3.077,-1.434 -5.527,-3.454 -7.352,-6.061l6.491,-6.492c1.46,1.721 3.09,3.037 4.889,3.95c1.799,0.912 3.976,1.368 6.531,1.368c3.181,0 5.697,-0.808 7.548,-2.424c1.851,-1.617 2.776,-3.859 2.776,-6.727l0,-9.464l1.721,-8.291l-1.643,-8.29l0,-9.934l10.168,0l0,35.823c0,3.754 -0.873,7.026 -2.62,9.816c-1.747,2.79 -4.158,4.967 -7.235,6.531c-3.076,1.564 -6.622,2.346 -10.637,2.346Zm-0.469,-17.755c-3.39,0 -6.44,-0.821 -9.151,-2.463c-2.712,-1.643 -4.837,-3.898 -6.375,-6.766c-1.538,-2.868 -2.307,-6.075 -2.307,-9.62c-0,-3.546 0.769,-6.727 2.307,-9.543c1.538,-2.815 3.663,-5.045 6.375,-6.687c2.711,-1.643 5.761,-2.464 9.151,-2.464c2.816,0 5.305,0.548 7.469,1.643c2.164,1.095 3.872,2.594 5.123,4.497c1.252,1.903 1.93,4.132 2.034,6.687l-0,11.889c-0.104,2.503 -0.795,4.732 -2.073,6.688c-1.277,1.955 -2.998,3.467 -5.162,4.536c-2.164,1.069 -4.628,1.603 -7.391,1.603Zm2.033,-9.307c1.878,-0 3.507,-0.404 4.889,-1.212c1.382,-0.809 2.464,-1.93 3.246,-3.364c0.782,-1.434 1.173,-3.063 1.173,-4.888c-0,-1.877 -0.391,-3.52 -1.173,-4.928c-0.782,-1.408 -1.864,-2.516 -3.246,-3.324c-1.382,-0.808 -3.011,-1.212 -4.889,-1.212c-1.877,-0 -3.519,0.404 -4.927,1.212c-1.408,0.808 -2.503,1.929 -3.285,3.363c-0.782,1.434 -1.173,3.064 -1.173,4.889c-0,1.773 0.391,3.376 1.173,4.81c0.782,1.434 1.877,2.568 3.285,3.402c1.408,0.835 3.05,1.252 4.927,1.252Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M307.337,88.04l0,-54.985l10.637,0l0,54.985l-10.637,0Zm7.822,0l-0,-9.464l28.001,0l-0,9.464l-28.001,0Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M349.73,88.04l-0,-37.778l10.324,0l0,37.778l-10.324,0Zm5.162,-43.878c-1.669,-0 -3.05,-0.561 -4.145,-1.682c-1.095,-1.121 -1.643,-2.516 -1.643,-4.185c0,-1.616 0.548,-2.998 1.643,-4.145c1.095,-1.147 2.476,-1.721 4.145,-1.721c1.721,0 3.115,0.574 4.184,1.721c1.069,1.147 1.604,2.529 1.604,4.145c-0,1.669 -0.535,3.064 -1.604,4.185c-1.069,1.121 -2.463,1.682 -4.184,1.682Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M394.547,88.04l-0,-21.665c-0,-2.243 -0.704,-4.068 -2.112,-5.475c-1.408,-1.408 -3.233,-2.112 -5.475,-2.112c-1.46,-0 -2.763,0.313 -3.911,0.938c-1.147,0.626 -2.046,1.513 -2.698,2.66c-0.652,1.147 -0.978,2.476 -0.978,3.989l-3.989,-2.034c0,-2.972 0.639,-5.566 1.917,-7.782c1.277,-2.216 3.05,-3.95 5.318,-5.202c2.268,-1.251 4.836,-1.877 7.704,-1.877c2.764,0 5.241,0.691 7.431,2.073c2.19,1.382 3.911,3.181 5.162,5.397c1.251,2.216 1.877,4.601 1.877,7.156l0,23.934l-10.246,0Zm-25.42,0l0,-37.778l10.246,0l0,37.778l-10.246,0Z" style="fill:#fff;fill-rule:nonzero;"/><path d="M437.174,88.04l-14.626,-19.475l14.548,-18.303l11.81,0l-17.05,20.649l0.391,-5.006l17.442,22.135l-12.515,0Zm-24.09,0l-0,-56.549l10.246,-0l0,56.549l-10.246,0Z" style="fill:#fff;fill-rule:nonzero;"/></g><path d="M25.176,96.816c-8.679,-9.38 -13.986,-21.925 -13.986,-35.699c-0,-29.027 23.566,-52.592 52.592,-52.592c29.027,-0 52.592,23.565 52.592,52.592c0,11.105 -3.449,21.411 -9.334,29.904l-0.048,-2.446c-0.087,-4.064 -0.992,-8.013 -2.564,-11.613c-2.355,-5.4 -6.187,-10.048 -10.968,-13.413c-1.015,-0.717 -2.084,-1.371 -3.186,-1.97l0.378,19.084c1.223,2.212 1.954,4.708 2.01,7.379l0.327,16.807c-2.697,1.806 -5.571,3.37 -8.589,4.659l-0.521,-56.659c-0.085,-4.066 -0.987,-8.013 -2.559,-11.613c-2.353,-5.408 -6.18,-10.05 -10.965,-13.41c-4.771,-3.371 -10.546,-5.447 -16.648,-5.675c-4.059,-0.15 -7.951,0.536 -11.464,1.908c-5.273,2.058 -9.701,5.631 -12.803,10.228c-3.097,4.603 -4.848,10.264 -4.733,16.366l0.469,46.163Zm23.137,14.577c-3.028,-0.931 -5.94,-2.13 -8.708,-3.566l-0.577,-56.636c-0.031,-2.131 0.348,-4.106 1.088,-5.892c1.095,-2.689 2.997,-4.972 5.4,-6.528c2.406,-1.553 5.297,-2.4 8.473,-2.285c2.13,0.079 4.128,0.582 5.97,1.417c2.743,1.256 5.13,3.286 6.82,5.792c1.698,2.503 2.709,5.444 2.772,8.624l0.58,61.01c-2.082,0.251 -4.2,0.38 -6.349,0.38c-0.372,0 -0.742,-0.004 -1.112,-0.012l-0.529,-26.78c-0.034,-2.134 0.349,-4.101 1.078,-5.903c0.188,-0.454 0.419,-0.901 0.647,-1.341l-0.375,-19.087c-4.462,2.122 -8.217,5.377 -10.945,9.43c-3.091,4.597 -4.861,10.255 -4.722,16.366l0.489,25.011Z" style="fill:url(#_Linear1_tos);"/></g><defs><linearGradient id="_Linear1_tos" x1="0" y1="0" x2="1" y2="0" gradientUnits="userSpaceOnUse" gradientTransform="matrix(8.74093e-15,-105.184,142.75,6.44068e-15,63.7821,113.709)"><stop offset="0" style="stop-color:#ff1f2b;stop-opacity:1"/><stop offset="1" style="stop-color:#ff682f;stop-opacity:1"/></linearGradient></defs></svg>
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
                            <span class="text-xs text-orange-400 font-medium">MagLink</span>
                        </div>
                        <h1 class="text-3xl sm:text-4xl font-bold text-white mb-4">Termini di Servizio</h1>
                        <p class="text-gray-400 leading-relaxed max-w-2xl mb-3">
                            Leggendo e utilizzando MagLink, accetti i presenti Termini di Servizio. Ti invitiamo a leggerli con attenzione prima di registrarti o utilizzare il Servizio.
                        </p>
                        <p class="text-sm text-gray-600">Ultima modifica: 17 marzo 2026</p>
                    </header>

                    <div class="space-y-12 text-sm text-gray-400 leading-relaxed">

                        <!-- 1. Accettazione -->
                        <section>
                            <h2 class="text-xl font-bold text-white mb-4">1. Accettazione dei Termini</h2>
                            <p>Utilizzando la piattaforma MagLink (il "Servizio"), gestita da <strong class="text-gray-300">Magmatic di Fabio De Notariis</strong>, Viale Giulio Cesare 99, 86035 Larino (CB), P.IVA 01908340704 (il "Titolare"), dichiari di aver letto, compreso e accettato integralmente i presenti Termini di Servizio ("Termini"), la nostra <Link :href="route('privacy-policy')" class="text-orange-400 hover:text-orange-300 underline underline-offset-2">Privacy Policy</Link> e la nostra <Link :href="route('cookie-policy')" class="text-orange-400 hover:text-orange-300 underline underline-offset-2">Cookie Policy</Link>.</p>
                            <p class="mt-3">Se accedi al Servizio per conto di un'azienda o di un altro soggetto giuridico, dichiari di avere l'autorità per vincolare tale soggetto ai presenti Termini.</p>
                            <p class="mt-3">Se non accetti i Termini, ti preghiamo di non utilizzare il Servizio.</p>
                        </section>

                        <!-- 2. Descrizione del Servizio -->
                        <section class="pt-8 border-t border-white/[0.08]">
                            <h2 class="text-xl font-bold text-white mb-4">2. Descrizione del Servizio</h2>
                            <p>MagLink è una piattaforma SaaS che consente agli utenti registrati di:</p>
                            <ul class="mt-3 space-y-2 ml-4">
                                <li>· Creare e gestire <strong class="text-gray-300">link abbreviati</strong> con redirect personalizzato;</li>
                                <li>· Generare e gestire <strong class="text-gray-300">QR Code</strong> statici e dinamici;</li>
                                <li>· Creare <strong class="text-gray-300">pagine link</strong> (landing page) personalizzabili con blocchi di contenuto;</li>
                                <li>· Monitorare le <strong class="text-gray-300">statistiche di accesso</strong> (click, provenienza geografica, device, sorgente);</li>
                                <li>· Gestire il tutto tramite un <strong class="text-gray-300">pannello di controllo</strong> dedicato al proprio account.</li>
                            </ul>
                            <p class="mt-4">Il Servizio è disponibile in piani gratuiti e a pagamento. Le funzionalità disponibili dipendono dal piano sottoscritto.</p>
                        </section>

                        <!-- 3. Registrazione e Account -->
                        <section class="pt-8 border-t border-white/[0.08]">
                            <h2 class="text-xl font-bold text-white mb-4">3. Registrazione e Account</h2>
                            <p>Per accedere al Servizio è necessario creare un account fornendo informazioni veritiere, accurate, aggiornate e complete. Sei responsabile di:</p>
                            <ul class="mt-3 space-y-2 ml-4">
                                <li>· Mantenere la riservatezza delle credenziali di accesso (username e password);</li>
                                <li>· Tutte le attività svolte tramite il tuo account, autorizzate o meno;</li>
                                <li>· Notificare immediatamente il Titolare in caso di accesso non autorizzato all'indirizzo <a href="mailto:info@magmatic.it" class="text-orange-400 hover:text-orange-300">info@magmatic.it</a>.</li>
                            </ul>
                            <p class="mt-4">È vietato creare account multipli per aggirare i limiti del piano gratuito, o per qualsiasi scopo fraudolento.</p>
                        </section>

                        <!-- 4. Uso Accettabile -->
                        <section class="pt-8 border-t border-white/[0.08]">
                            <h2 class="text-xl font-bold text-white mb-4">4. Uso Accettabile e Condotte Vietate</h2>
                            <p>Accetti di utilizzare il Servizio esclusivamente per scopi legali e conformi ai presenti Termini. È espressamente vietato utilizzare MagLink per:</p>
                            <ul class="mt-3 space-y-2 ml-4">
                                <li>· Distribuire contenuti illegali, offensivi, diffamatori, osceni, violenti o che incitino all'odio;</li>
                                <li>· Diffondere malware, spyware, phishing o qualsiasi altro contenuto dannoso;</li>
                                <li>· Spam, invio di comunicazioni commerciali non richieste o truffe;</li>
                                <li>· Violare diritti di proprietà intellettuale di terzi (copyright, marchi, brevetti);</li>
                                <li>· Raccogliere dati personali di terzi senza consenso;</li>
                                <li>· Aggirare misure tecniche di sicurezza del Servizio;</li>
                                <li>· Rivendere o sublicenziare il Servizio senza autorizzazione scritta del Titolare;</li>
                                <li>· Qualsiasi attività che violi la normativa vigente in Italia, nell'Unione Europea o nel paese in cui operi.</li>
                            </ul>
                            <p class="mt-4">Il Titolare si riserva il diritto di rimuovere qualsiasi contenuto o sospendere qualsiasi account che violi le presenti disposizioni, senza preavviso.</p>
                        </section>

                        <!-- 5. Proprietà Intellettuale -->
                        <section class="pt-8 border-t border-white/[0.08]">
                            <h2 class="text-xl font-bold text-white mb-4">5. Proprietà Intellettuale</h2>
                            <p>Il Servizio e tutti i suoi contenuti originali (inclusi software, design, logo, interfaccia, testi e grafica) sono di proprietà esclusiva del Titolare e sono protetti dalle leggi applicabili in materia di diritto d'autore, marchi e proprietà intellettuale.</p>
                            <p class="mt-3">Nessuna disposizione dei presenti Termini ti trasferisce diritti di proprietà intellettuale sul Servizio o sui suoi contenuti. È vietato riprodurre, distribuire, modificare o creare opere derivate senza previa autorizzazione scritta.</p>
                            <p class="mt-3">Con riguardo ai contenuti che carichi o crei attraverso il Servizio (link, pagine, QR Code, testi e immagini), rimani l'unico titolare dei relativi diritti. Concedi tuttavia al Titolare una licenza limitata, non esclusiva e non trasferibile per gestire, archiviare e rendere disponibili tali contenuti nell'ambito della fornitura del Servizio.</p>
                        </section>

                        <!-- 6. Piani e Pagamenti -->
                        <section class="pt-8 border-t border-white/[0.08]">
                            <h2 class="text-xl font-bold text-white mb-4">6. Piani, Prezzi e Pagamenti</h2>
                            <p>MagLink offre un piano gratuito con limiti di utilizzo e piani a pagamento ("Piani Premium") con funzionalità aggiuntive. La descrizione aggiornata dei piani e dei relativi prezzi è disponibile nella pagina <Link :href="route('home')" class="text-orange-400 hover:text-orange-300 underline underline-offset-2">Prezzi</Link> del sito.</p>
                            <ul class="mt-3 space-y-2 ml-4">
                                <li>· I pagamenti sono elaborati in modo sicuro tramite <strong class="text-gray-300">Stripe</strong>. Il Titolare non conserva dati di pagamento.</li>
                                <li>· I Piani Premium sono fatturati anticipatamente su base mensile o annuale, secondo la scelta effettuata.</li>
                                <li>· I canoni versati non sono rimborsabili, salvo diversa disposizione di legge o previsione specifica nel piano sottoscritto.</li>
                                <li>· Il Titolare si riserva il diritto di modificare i prezzi dei piani con un preavviso di almeno 30 giorni.</li>
                                <li>· In caso di mancato pagamento, l'accesso alle funzionalità Premium potrà essere sospeso fino alla regolarizzazione.</li>
                            </ul>
                        </section>

                        <!-- 7. Disponibilità e Modifiche al Servizio -->
                        <section class="pt-8 border-t border-white/[0.08]">
                            <h2 class="text-xl font-bold text-white mb-4">7. Disponibilità e Modifiche al Servizio</h2>
                            <p>Il Titolare si impegna a garantire la disponibilità del Servizio, ma non può assicurare un uptime ininterrotto. Il Servizio potrebbe essere temporaneamente non disponibile per manutenzione, aggiornamenti o per cause di forza maggiore.</p>
                            <p class="mt-3">Il Titolare si riserva il diritto di modificare, sospendere o interrompere, in tutto o in parte, il Servizio in qualsiasi momento, con o senza preavviso. In caso di interruzione definitiva del Servizio, sarà fornito un preavviso ragionevole agli utenti con piani a pagamento attivi.</p>
                        </section>

                        <!-- 8. Privacy e Dati Personali -->
                        <section class="pt-8 border-t border-white/[0.08]">
                            <h2 class="text-xl font-bold text-white mb-4">8. Privacy e Trattamento dei Dati Personali</h2>
                            <p>Il trattamento dei tuoi dati personali è disciplinato dalla nostra <Link :href="route('privacy-policy')" class="text-orange-400 hover:text-orange-300 underline underline-offset-2">Privacy Policy</Link>, che forma parte integrante dei presenti Termini. Utilizzando il Servizio, accetti le pratiche di trattamento dei dati descritte nella Privacy Policy.</p>
                            <p class="mt-3">L'utilizzo di cookie e tecnologie di tracciamento è disciplinato dalla nostra <Link :href="route('cookie-policy')" class="text-orange-400 hover:text-orange-300 underline underline-offset-2">Cookie Policy</Link>.</p>
                        </section>

                        <!-- 9. Limitazione di Responsabilità -->
                        <section class="pt-8 border-t border-white/[0.08]">
                            <h2 class="text-xl font-bold text-white mb-4">9. Limitazione di Responsabilità</h2>
                            <p>Nei limiti consentiti dalla legge applicabile, il Titolare non sarà responsabile per danni indiretti, incidentali, speciali, consequenziali o punitivi derivanti dall'utilizzo o dall'impossibilità di utilizzare il Servizio, inclusi ma non limitati a: perdita di dati, perdita di profitti, interruzione dell'attività o danni alla reputazione.</p>
                            <p class="mt-3">Il Servizio è fornito "così com'è" e "secondo disponibilità", senza garanzie di alcun tipo, esplicite o implicite. Il Titolare non garantisce che il Servizio sia privo di errori, che i difetti vengano corretti o che il Servizio o i server che lo rendono disponibile siano privi di virus o altri componenti dannosi.</p>
                            <p class="mt-3">La responsabilità complessiva del Titolare nei confronti dell'utente per qualsiasi reclamo derivante dall'utilizzo del Servizio non supererà l'importo pagato dall'utente nei 12 mesi precedenti al reclamo.</p>
                        </section>

                        <!-- 10. Indennizzo -->
                        <section class="pt-8 border-t border-white/[0.08]">
                            <h2 class="text-xl font-bold text-white mb-4">10. Indennizzo</h2>
                            <p>Accetti di difendere, indennizzare e manlevare il Titolare, i suoi collaboratori, dipendenti e aventi causa da qualsiasi reclamo, responsabilità, danno, perdita e spesa (incluse le ragionevoli spese legali) derivanti o connessi a: (a) l'utilizzo del Servizio in violazione dei presenti Termini; (b) contenuti da te caricati o diffusi tramite il Servizio; (c) la violazione di diritti di terzi.</p>
                        </section>

                        <!-- 11. Risoluzione e Sospensione -->
                        <section class="pt-8 border-t border-white/[0.08]">
                            <h2 class="text-xl font-bold text-white mb-4">11. Risoluzione e Sospensione dell'Account</h2>
                            <p>Puoi chiudere il tuo account in qualsiasi momento accedendo alle impostazioni del tuo profilo o contattando il supporto all'indirizzo <a href="mailto:info@magmatic.it" class="text-orange-400 hover:text-orange-300">info@magmatic.it</a>. La chiusura dell'account non dà diritto al rimborso dei canoni già versati.</p>
                            <p class="mt-3">Il Titolare può sospendere o chiudere il tuo account con effetto immediato in caso di:</p>
                            <ul class="mt-3 space-y-2 ml-4">
                                <li>· Violazione dei presenti Termini;</li>
                                <li>· Segnalazione di attività fraudolente o illegali;</li>
                                <li>· Mancato pagamento dei canoni dovuti;</li>
                                <li>· Richiesta delle autorità competenti.</li>
                            </ul>
                            <p class="mt-3">Alla chiusura dell'account, i tuoi dati saranno trattati come descritto nella Privacy Policy. I link abbreviati e i QR Code creati potrebbero cessare di funzionare.</p>
                        </section>

                        <!-- 12. Legge applicabile -->
                        <section class="pt-8 border-t border-white/[0.08]">
                            <h2 class="text-xl font-bold text-white mb-4">12. Legge Applicabile e Foro Competente</h2>
                            <p>I presenti Termini sono regolati e interpretati secondo la legge italiana, in conformità con la normativa dell'Unione Europea applicabile (incluso il Regolamento GDPR 2016/679).</p>
                            <p class="mt-3">Per qualsiasi controversia derivante dai presenti Termini o dall'utilizzo del Servizio, le parti si impegnano a ricercare una soluzione amichevole. In mancanza di accordo, la controversia sarà devoluta alla competenza esclusiva del Tribunale di Campobasso, salvo diversa previsione inderogabile di legge a tutela dei consumatori (es. D.Lgs. 206/2005).</p>
                        </section>

                        <!-- 13. Modifiche ai Termini -->
                        <section class="pt-8 border-t border-white/[0.08]">
                            <h2 class="text-xl font-bold text-white mb-4">13. Modifiche ai Termini</h2>
                            <p>Il Titolare si riserva il diritto di modificare i presenti Termini in qualsiasi momento. In caso di modifiche sostanziali, gli utenti registrati saranno informati via email o tramite avviso nella piattaforma con un preavviso di almeno 14 giorni.</p>
                            <p class="mt-3">Continuando a utilizzare il Servizio dopo l'entrata in vigore delle modifiche, accetti i nuovi Termini. Se non accetti le modifiche, devi cessare di utilizzare il Servizio e puoi richiedere la chiusura del tuo account.</p>
                            <p class="mt-3">La data dell'ultima modifica è indicata in cima a questo documento.</p>
                        </section>

                        <!-- 14. Contatti -->
                        <section class="pt-8 border-t border-white/[0.08]">
                            <h2 class="text-xl font-bold text-white mb-4">14. Contatti</h2>
                            <p>Per qualsiasi domanda relativa ai presenti Termini di Servizio, puoi contattarci:</p>
                            <div class="mt-4 bg-white/[0.03] border border-white/[0.08] rounded-xl p-5 space-y-2">
                                <p class="font-medium text-gray-300">Magmatic di Fabio De Notariis</p>
                                <p>Viale Giulio Cesare, 99<br>86035 Larino (CB)<br>P.IVA 01908340704</p>
                                <p><strong class="text-gray-300">Email:</strong> <a href="mailto:info@magmatic.it" class="text-orange-400 hover:text-orange-300">info@magmatic.it</a></p>
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

    <CookieBanner />
</template>
