<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlatOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $routeName = $request->route()->getName();

        if ($routeName === 'plats.create') {
            // Vérification spécifique pour la route 'create'
            if (!$user->hasRole('administrator')) {
                return redirect()->back()->with('error', 'Vous devez être administrateur pour créer un plat');
            }
        } elseif ($routeName === 'plats.destroy') {
            $plat = $request->route('plat');
            // Vérification spécifique pour la route 'destroy'
            if (!$user->hasRole('administrator') && $user->id !== $plat->user_id) {
                return redirect()->back()->with('error', 'Vous devez être administrateur ou propriétaire pour supprimer ce plat');
            }
        } elseif ($routeName === 'plats.update') {
            $plat = $request->route('plat');
            if (!$user->hasRole('administrator') && $user->id !== $plat->user_id) {
                return redirect()->back()->with('error', 'Vous n\'avez pas l\'autorisation de modifier ce plat');
            }
        }

        return $next($request);
    }

}
