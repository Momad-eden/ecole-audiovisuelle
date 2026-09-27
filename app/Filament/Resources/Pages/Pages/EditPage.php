<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Enums\PublicationStatus;
use App\Filament\Resources\Pages\PageResource;
use App\Models\Page;
use App\Models\PageRevision;
use App\Support\PreviewToken;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    public function getSubheading(): ?string
    {
        /** @var Page $page */
        $page = $this->record;

        return match (true) {
            $page->status !== PublicationStatus::PUBLISHED => 'Cette page n\'est pas encore publiée.',
            $page->hasUnpublishedChanges() => 'Des modifications n\'ont pas encore été publiées.',
            default => 'La version en ligne est à jour.',
        };
    }

    protected function getHeaderActions(): array
    {
        /** @var Page $page */
        $page = $this->record;

        return [
            Action::make('preview')->label('Aperçu')->icon('heroicon-o-eye')->color('gray')
                ->url(fn () => rtrim(config('services.frontend.url'), '/').'/api/preview?token='.PreviewToken::make('page', $page->id), true),
            Action::make('publish')->label('Publier')->icon('heroicon-o-globe-alt')->color('success')
                ->requiresConfirmation()
                ->modalDescription('La version affichée sur le site sera remplacée par le brouillon actuel. Pensez à enregistrer vos dernières modifications avant.')
                ->action(function () use ($page) {
                    $this->save(false);
                    $page->refresh()->publish(auth()->user());
                    Notification::make()->title('Page publiée.')->success()->send();
                    $this->refreshFormData(['status']);
                }),
            ActionGroup::make([
                Action::make('restore')->label('Revenir à une version précédente')->icon('heroicon-o-clock')
                    ->visible(fn () => $page->revisions()->exists())
                    ->schema([
                        Select::make('revision')->label('Version publiée le')->required()
                            ->options(fn () => $page->revisions()->with('user')->limit(30)->get()
                                ->mapWithKeys(fn (PageRevision $r) => [$r->id => $r->created_at->format('d/m/Y à H:i').($r->user ? ' — '.$r->user->name : '')])->all()),
                    ])
                    ->action(function (array $data) use ($page) {
                        $page->restore($page->revisions()->findOrFail($data['revision']));
                        $this->fillForm();
                        Notification::make()->title('Version restaurée dans le brouillon. Vérifiez puis publiez.')->success()->send();
                    }),
                Action::make('unpublish')->label('Retirer du site')->icon('heroicon-o-eye-slash')->color('warning')
                    ->visible(fn () => $page->status === PublicationStatus::PUBLISHED && ! $page->is_locked)
                    ->requiresConfirmation()
                    ->action(function () use ($page) {
                        $page->update(['status' => PublicationStatus::DRAFT]);
                        Notification::make()->title('Page retirée du site.')->success()->send();
                    }),
                DeleteAction::make()->visible(fn () => ! $page->is_locked),
            ]),
        ];
    }
}
