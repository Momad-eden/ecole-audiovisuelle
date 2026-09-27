<?php

namespace App\Filament\Resources\Applications\Pages;

use App\Enums\ApplicationStatus;
use App\Enums\UserRole;
use App\Exceptions\BusinessRuleException;
use App\Filament\Resources\Applications\ApplicationResource;
use App\Filament\Resources\Students\StudentResource;
use App\Models\Application;
use App\Services\ApplicationWorkflow;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Carbon;

class ViewApplication extends ViewRecord
{
    protected static string $resource = ApplicationResource::class;

    public function getTitle(): string
    {
        return "Candidature {$this->record->reference} — {$this->record->full_name}";
    }

    private function workflow(): ApplicationWorkflow
    {
        return app(ApplicationWorkflow::class);
    }

    private function run(callable $callback, string $success): void
    {
        try {
            $callback();
            Notification::make()->title($success)->success()->send();
            $this->record->refresh()->load(['offering.cohort.program', 'offering.track', 'student', 'decider', 'events.user']);
        } catch (BusinessRuleException $e) {
            Notification::make()->title($e->getMessage())->danger()->send();
        }
    }

    /** @return array<string, string> */
    private function transitionOptions(): array
    {
        $isDecider = auth()->user()->hasRole(UserRole::DIRECTEUR, UserRole::GESTIONNAIRE);

        return collect($this->record->status->allowedTransitions())
            ->reject(fn (ApplicationStatus $s) => $s === ApplicationStatus::INTERVIEW_SCHEDULED || ($s->isDecision() && ! $isDecider))
            ->mapWithKeys(fn (ApplicationStatus $s) => [$s->value => $s->getLabel()])
            ->all();
    }

    protected function getHeaderActions(): array
    {
        /** @var Application $record */
        $record = $this->record;

        return [
            Action::make('enroll')
                ->label('Inscrire comme étudiant')
                ->icon('heroicon-o-academic-cap')
                ->color('success')
                ->visible(fn () => $record->status === ApplicationStatus::ACCEPTED && auth()->user()->hasRole(UserRole::DIRECTEUR, UserRole::GESTIONNAIRE))
                ->requiresConfirmation()
                ->modalDescription('Un dossier étudiant (matricule) et une inscription seront créés, avec les frais actuels de l\'offre.')
                ->action(function () use ($record) {
                    try {
                        $enrollment = $this->workflow()->enroll($record, auth()->user());
                        Notification::make()->title('Candidat inscrit.')->success()->send();
                        $this->redirect(StudentResource::getUrl('view', ['record' => $enrollment->student_id]));
                    } catch (BusinessRuleException $e) {
                        Notification::make()->title($e->getMessage())->danger()->send();
                    }
                }),
            Action::make('transition')
                ->label('Changer d\'étape')
                ->icon('heroicon-o-arrow-path')
                ->visible(fn () => $this->transitionOptions() !== [])
                ->schema([
                    Select::make('status')->label('Nouvelle étape')->options(fn () => $this->transitionOptions())->required(),
                    Textarea::make('comment')->label('Commentaire (facultatif)')->rows(3),
                ])
                ->action(fn (array $data) => $this->run(
                    fn () => $this->workflow()->transition($record, ApplicationStatus::from($data['status']), auth()->user(), $data['comment'] ?? null),
                    'Étape mise à jour.'
                )),
            Action::make('interview')
                ->label('Planifier l\'entretien')
                ->icon('heroicon-o-calendar-days')
                ->visible(fn () => $record->status->canTransitionTo(ApplicationStatus::INTERVIEW_SCHEDULED))
                ->schema([
                    DateTimePicker::make('interview_at')->label('Date et heure')->required()->minDate(now()->startOfDay())->seconds(false),
                    TextInput::make('interview_location')->label('Lieu')->placeholder('EMSI, salle de réunion'),
                ])
                ->action(fn (array $data) => $this->run(function () use ($record, $data) {
                    $record->update($data);
                    $this->workflow()->transition($record, ApplicationStatus::INTERVIEW_SCHEDULED, auth()->user(),
                        'Entretien le '.Carbon::parse($data['interview_at'])->format('d/m/Y à H:i'));
                }, 'Entretien planifié.')),
            ActionGroup::make([
                Action::make('note')
                    ->label('Ajouter une note')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->schema([Textarea::make('comment')->label('Note interne')->required()->rows(4)])
                    ->action(fn (array $data) => $this->run(fn () => $this->workflow()->addNote($record, auth()->user(), $data['comment']), 'Note ajoutée.')),
                EditAction::make()->label('Corriger le dossier'),
            ]),
        ];
    }
}
