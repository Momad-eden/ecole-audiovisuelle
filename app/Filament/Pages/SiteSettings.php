<?php

namespace App\Filament\Pages;

use App\Enums\UserRole;
use App\Models\Setting;
use App\Services\Translation\DeepLGlossary;
use App\Services\Translation\TranslationQuota;
use App\Services\Translation\Translator;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Throwable;

/**
 * @property-read Schema $form
 */
class SiteSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Paramètres du site';

    protected static ?string $title = 'Paramètres du site';

    protected string $view = 'filament.pages.site-settings';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole(UserRole::DIRECTEUR, UserRole::COMMUNICATION) ?? false;
    }

    public function mount(): void
    {
        $this->form->fill(Setting::current()->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            Section::make('Identité')->columns(2)->schema([
                TextInput::make('school_name')->label('Nom officiel de l\'école')->required()->maxLength(150)->columnSpanFull(),
                Textarea::make('description')->label('Présentation courte (pied de page)')->rows(2)->maxLength(300)->columnSpanFull(),
                FileUpload::make('logo')->label('Logo')->image()->disk('public')->directory('settings')->maxSize(2048),
            ]),
            Section::make('Coordonnées')->columns(2)->schema([
                TextInput::make('phone')->label('Téléphone')->tel()->placeholder('+221 77 000 00 00'),
                TextInput::make('whatsapp')->label('WhatsApp')->tel()->placeholder('+221 77 000 00 00'),
                TextInput::make('email')->label('E-mail de contact')->email(),
                TextInput::make('website')->label('Site web')->url(),
                TextInput::make('address')->label('Adresse')->columnSpanFull(),
                TextInput::make('opening_hours')->label('Horaires d\'accueil')->placeholder('Du lundi au vendredi, 8 h 30 – 18 h'),
                TextInput::make('map_url')->label('Lien Google Maps')->url(),
            ]),
            Section::make('Réseaux sociaux')->columns(3)->schema([
                TextInput::make('facebook')->label('Facebook')->url(),
                TextInput::make('instagram')->label('Instagram')->url(),
                TextInput::make('youtube')->label('YouTube')->url(),
                TextInput::make('tiktok')->label('TikTok')->url(),
                TextInput::make('linkedin')->label('LinkedIn')->url(),
                TextInput::make('twitter')->label('X (Twitter)')->url(),
            ]),
            Section::make('Référencement par défaut')->columns(1)->collapsed()->schema([
                TextInput::make('seo_title')->label('Titre du site dans Google')->maxLength(70),
                Textarea::make('seo_description')->label('Description dans Google')->maxLength(160)->rows(2),
            ]),
            Section::make('Traduction anglaise')->columns(1)->schema([
                TextEntry::make('translation_quota')->hiddenLabel()->state(fn () => $this->quotaLine()),
                Toggle::make('auto_translate')->label('Traduction automatique activée')
                    ->helperText('À chaque publication, les textes sont traduits en anglais ; vous pouvez ensuite les relire et les corriger.'),
                Repeater::make('translation_glossary')->label('Lexique de traduction')
                    ->helperText('Termes toujours traduits de la même façon. Pour un nom à ne jamais traduire, écrivez-le deux fois à l\'identique.')
                    ->columns(2)->defaultItems(0)->addActionLabel('Ajouter un terme')
                    ->schema([
                        TextInput::make('fr')->label('Terme français')->required()->maxLength(200),
                        TextInput::make('en')->label('Traduction anglaise')->required()->maxLength(200),
                    ]),
            ]),
        ]);
    }

    /** État du quota DeepL ; ne lève jamais d'erreur (DeepL injoignable → message d'attente). */
    public function quotaLine(): string
    {
        if (! app(Translator::class)->isAvailable()) {
            return 'Traduction automatique non configurée : traduisez à la main dans l\'onglet Anglais des fiches';
        }

        try {
            ['used' => $used, 'limit' => $limit] = app(TranslationQuota::class)->usage();
        } catch (Throwable) {
            return 'Quota indisponible pour le moment';
        }

        $format = fn (int $n) => number_format($n, 0, ',', ' ');
        $line = "Caractères traduits ce mois-ci : {$format($used)} / {$format($limit)}";

        return $limit > 0 && $used >= floor(0.95 * $limit)
            ? $line.' — Quota gratuit de traduction atteint ce mois-ci'
            : $line;
    }

    protected function getFormActions(): array
    {
        return [Action::make('save')->label('Enregistrer')->submit('save')];
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $state['translation_glossary'] = array_values(array_map(
            fn (array $pair) => ['fr' => trim((string) ($pair['fr'] ?? '')), 'en' => trim((string) ($pair['en'] ?? ''))],
            $state['translation_glossary'] ?? [],
        ));

        $setting = Setting::current();
        $glossaryChanged = ($setting->translation_glossary ?? []) !== $state['translation_glossary'];
        $setting->update($state);

        Notification::make()->title('Paramètres enregistrés.')->success()->send();

        if ($glossaryChanged && app(Translator::class)->isAvailable()) {
            try {
                app(DeepLGlossary::class)->sync($state['translation_glossary']);
            } catch (Throwable $e) {
                report($e);
                Notification::make()->title('Lexique enregistré, mais DeepL ne l\'a pas encore reçu')
                    ->body('Les prochaines traductions n\'en tiendront pas compte. Réenregistrez le lexique un peu plus tard.')
                    ->danger()->persistent()->send();
            }
        }
    }
}
