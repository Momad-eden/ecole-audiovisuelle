<?php

namespace App\Filament\Widgets;

use App\Enums\TranslationStatus;
use App\Filament\Resources\Translations\TranslationResource;
use App\Models\Translation;
use App\Services\Translation\TranslationQuota;
use App\Services\Translation\Translator;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Throwable;

/** Tableau de bord : traductions à relire, en échec, et quota DeepL du mois (spec R2 §6). */
class TranslationsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 4;

    protected ?string $heading = 'Site en anglais';

    public static function canView(): bool
    {
        return auth()->user()?->can('viewAny', Translation::class) ?? false;
    }

    protected function getStats(): array
    {
        $count = fn (TranslationStatus $status) => Translation::where('locale', 'en')->where('status', $status)->count();
        $url = TranslationResource::getUrl('index');

        return [
            Stat::make('À relire', $count(TranslationStatus::AUTO))->description('Traductions automatiques')->color('warning')->url($url),
            Stat::make('En échec', $count(TranslationStatus::FAILED))->description('Le site affiche le français')->color('danger')->url($url),
            $this->quota(),
        ];
    }

    /** Ne lève jamais d'erreur : DeepL injoignable → message d'attente. */
    private function quota(): Stat
    {
        if (! app(Translator::class)->isAvailable()) {
            return Stat::make('Quota DeepL', '—')->description('Traduction automatique non configurée');
        }

        try {
            ['used' => $used, 'limit' => $limit] = app(TranslationQuota::class)->usage();
        } catch (Throwable) {
            return Stat::make('Quota DeepL', '—')->description('Quota indisponible pour le moment');
        }

        $format = fn (int $n) => number_format($n, 0, ',', ' ');

        return Stat::make('Quota DeepL', "{$format($used)} / {$format($limit)}")
            ->description('caractères ce mois-ci')
            ->color($limit > 0 && $used >= floor(0.95 * $limit) ? 'danger' : 'gray');
    }
}
