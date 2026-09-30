<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Langue de l'API publique : `?locale=` dans la chaîne de requête (lecture comme envoi de formulaire),
 * `fr` ou `en` ; toute autre valeur ou son absence donne le français (spec R2 §4).
 */
class SetPublicLocale
{
    public const LOCALES = ['fr', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->query('locale');
        App::setLocale(is_string($locale) && in_array($locale, self::LOCALES, true) ? $locale : 'fr');

        return $next($request);
    }
}
