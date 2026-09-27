<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Demande au site Next.js de régénérer ses pages après une modification dans l'admin.
 * Un échec (site arrêté, réseau) n'empêche jamais l'enregistrement : le site se
 * mettra à jour à sa prochaine régénération périodique.
 */
class FrontendRevalidator
{
    /** @var array<int, string> */
    private array $pending = [];

    /**
     * En requête web, les demandes sont regroupées et envoyées une seule fois, après la réponse.
     * En console (imports, commandes) et en test, elles partent immédiatement.
     *
     * @param  array<int, string>  $tags
     */
    public function queue(array $tags = ['content']): void
    {
        if (! $this->isConfigured()) {
            return;
        }

        $first = $this->pending === [];
        $this->pending = array_values(array_unique([...$this->pending, ...$tags]));

        if (app()->runningInConsole()) {
            $this->flush();
        } elseif ($first) {
            app()->terminating(fn () => $this->flush());
        }
    }

    public function flush(): void
    {
        if ($this->pending === [] || ! $this->isConfigured()) {
            return;
        }

        $tags = $this->pending;
        $this->pending = [];

        try {
            Http::timeout(5)->post(rtrim((string) config('services.frontend.url'), '/').'/api/revalidate', [
                'secret' => config('services.frontend.revalidate_secret'),
                'tags' => $tags,
            ]);
        } catch (Throwable $e) {
            Log::warning('Régénération du site impossible : '.$e->getMessage());
        }
    }

    private function isConfigured(): bool
    {
        return filled(config('services.frontend.url')) && filled(config('services.frontend.revalidate_secret'));
    }
}
