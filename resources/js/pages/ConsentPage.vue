<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ExternalLink, Shield, Info } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';

interface Props {
  type: 'link' | 'qrcode';
  slug: string;
  destination: string;
  title?: string;
  privacyVersion: string;
  privacyText: string;
}

const props = defineProps<Props>();

const screenResolution = ref('');

onMounted(() => {
  // Cattura la risoluzione dello schermo
  screenResolution.value = `${window.screen.width}x${window.screen.height}`;
});

const acceptConsent = () => {
  // Redirect con consenso e dati schermo
  const url = new URL(window.location.href);
  url.searchParams.set('consent', 'true');
  url.searchParams.set('screen', screenResolution.value);
  window.location.href = url.toString();
};

const declineConsent = () => {
  // Redirect senza consenso
  const url = new URL(window.location.href);
  url.searchParams.set('consent', 'false');
  window.location.href = url.toString();
};

const getTypeLabel = () => {
  return props.type === 'link' ? 'link' : 'codice QR';
};
</script>

<template>
  <Head title="Conferma reindirizzamento" />
  
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
      <!-- Card principale -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
          <div class="flex items-center gap-3">
            <div class="bg-white/20 p-3 rounded-lg">
              <ExternalLink class="w-6 h-6 text-white" />
            </div>
            <div>
              <h1 class="text-2xl font-bold text-white">Stai lasciando MagLink</h1>
              <p class="text-blue-100 text-sm mt-1">Conferma per continuare</p>
            </div>
          </div>
        </div>

        <!-- Corpo -->
        <div class="px-8 py-8 space-y-6">
          <!-- Messaggio principale -->
          <div class="flex items-start gap-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
            <Info class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
            <div>
              <h2 class="font-semibold text-gray-900 dark:text-white mb-2">
                Stai per essere reindirizzato su un sito esterno
              </h2>
              <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                Hai cliccato su un {{ getTypeLabel() }} che ti porterà a:
              </p>
              <div class="bg-white dark:bg-gray-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                <p v-if="title" class="font-semibold text-gray-900 dark:text-white mb-1">{{ title }}</p>
                <a :href="destination" 
                   class="text-blue-600 dark:text-blue-400 hover:underline break-all text-sm"
                   target="_blank">
                  {{ destination }}
                </a>
              </div>
            </div>
          </div>

          <!-- Privacy notice -->
          <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-start gap-3 mb-4">
              <Shield class="w-5 h-5 text-indigo-600 dark:text-indigo-400 flex-shrink-0 mt-0.5" />
              <div class="flex-1">
                <h3 class="font-semibold text-gray-900 dark:text-white">Raccolta dati e privacy</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Versione {{ privacyVersion }}</p>
              </div>
            </div>
            
            <div class="space-y-3 text-sm text-gray-600 dark:text-gray-300">
              <!-- Privacy Policy Text (GDPR Compliant) -->
              <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
                <p class="text-sm leading-relaxed">{{ privacyText }}</p>
              </div>
              
              <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                <p class="font-medium text-gray-900 dark:text-white mb-2">Puoi scegliere se:</p>
                <ul class="space-y-2">
                  <li class="flex items-start gap-2">
                    <span class="text-green-500 mt-0.5">✓</span>
                    <span><strong>Accettare:</strong> verranno raccolti dati anonimi (browser, dispositivo, paese, lingua) per statistiche</span>
                  </li>
                  <li class="flex items-start gap-2">
                    <span class="text-orange-500 mt-0.5">−</span>
                    <span><strong>Rifiutare:</strong> verrà registrato solo un conteggio della visita, senza alcun dato personale</span>
                  </li>
                </ul>
              </div>

              <p class="text-xs text-gray-500 dark:text-gray-400 pt-2">
                Accettando, il tuo consenso sarà registrato insieme alla versione {{ privacyVersion }} dell'informativa privacy e al timestamp del {{ new Date().toLocaleDateString('it-IT') }}.
              </p>
            </div>
          </div>

          <!-- Buttons -->
          <div class="flex flex-col sm:flex-row gap-3 pt-4">
            <button
              @click="acceptConsent"
              class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-4 px-6 rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
              <span class="flex items-center justify-center gap-2">
                <Shield class="w-5 h-5" />
                Accetta e continua
              </span>
            </button>
            
            <button
              @click="declineConsent"
              class="flex-1 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-semibold py-4 px-6 rounded-xl transition-all">
              <span class="flex items-center justify-center gap-2">
                <ExternalLink class="w-5 h-5" />
                Rifiuta e continua
              </span>
            </button>
          </div>

          <!-- Footer info -->
          <p class="text-center text-xs text-gray-500 dark:text-gray-400 pt-4">
            Scegliendo una delle opzioni accetti di essere reindirizzato al sito di destinazione
          </p>
        </div>
      </div>

      <!-- Powered by -->
      <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-6">
        Powered by <strong>MagLink</strong> - Link & QR Code Management
      </p>
    </div>
  </div>
</template>
