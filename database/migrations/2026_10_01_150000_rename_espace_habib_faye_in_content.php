<?php

use App\Models\AgendaEvent;
use App\Models\Faq;
use App\Models\News;
use App\Models\Page;
use App\Models\Place;
use App\Models\Program;
use App\Models\Service;
use App\Models\Setting;
use App\Support\CentreCulturelRename;
use Illuminate\Database\Migrations\Migration;

/**
 * Suite du renommage en « Centre culturel Habib Faye » : l'ancien nom « Espace Habib Faye » restait dans
 * quelques textes (pages, services, événements, lieux, actualités, FAQ, formations, réglages). Mêmes règles
 * (CentreCulturelRename) ; la traduction anglaise des textes changés repart en file d'elle-même.
 */
return new class extends Migration
{
    private const FIELDS = [
        Page::class => ['title', 'seo', 'blocks', 'draft_blocks'],
        Service::class => ['name', 'summary', 'description'],
        AgendaEvent::class => ['title', 'venue', 'summary', 'content'],
        Place::class => ['name', 'tagline', 'description', 'highlights'],
        News::class => ['title', 'excerpt', 'content'],
        Faq::class => ['question', 'answer'],
        Program::class => ['title', 'summary', 'description'],
        Setting::class => ['description', 'seo_title', 'seo_description'],
    ];

    public function up(): void
    {
        foreach (self::FIELDS as $model => $fields) {
            $query = method_exists($model, 'bootSoftDeletes') ? $model::withTrashed() : $model::query();
            foreach ($query->get() as $record) {
                foreach ($fields as $field) {
                    $record->{$field} = CentreCulturelRename::deep($record->{$field});
                }
                if ($record->isDirty()) {
                    $record->save();
                }
            }
        }
    }

    public function down(): void
    {
        // Pas de retour automatique : l'équipe a pu retoucher les textes depuis.
    }
};
