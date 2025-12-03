<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotSubscribed
{
    /**
     * Handle an incoming request.
     * 
     * Verifica se l'utente ha accesso all'app (trial attivo, abbonato, o può iniziare trial).
     * Se non ha accesso, reindirizza alla pagina dei piani.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if ($user) {
            // Se l'utente ha un abbonamento attivo, procedi
            if ($user->subscribed('default')) {
                // "Brucia" il diritto al trial se non è stato ancora fatto
                $user->burnTrial();
                return $next($request);
            }
            
            // Se l'utente è in trial, procedi
            if ($user->onTrial()) {
                return $next($request);
            }
            
            // Se l'utente può ancora iniziare un trial (trial_ends_at è NULL), permetti l'accesso
            // ma mostra il paywall per far scegliere se iniziare trial o acquistare
            if ($user->canStartTrial()) {
                return $next($request);
            }
            
            // Se non ha abbonamento, né trial attivo, né può iniziare un trial, reindirizza ai piani
            return redirect()->route('plans.index')
                ->with('error', 'Devi avere un abbonamento attivo o un trial per accedere a questa funzionalità.');
        }
        
        return $next($request);
    }
}
