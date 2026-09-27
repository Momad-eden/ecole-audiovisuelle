<?php

namespace App\Filament\Resources\Programs;

use App\Enums\Audience;
use App\Enums\ProgramKind;
use App\Enums\PublicationStatus;
use App\Filament\Resources\Programs\Pages\CreateProgram;
use App\Filament\Resources\Programs\Pages\EditProgram;
use App\Filament\Resources\Programs\Pages\ListPrograms;
use App\Filament\Resources\Programs\RelationManagers\CohortsRelationManager;
use App\Filament\Support\Fields;
use App\Models\Program;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProgramResource extends Resource
{
    protected static ?string $model = Program::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string|\UnitEnum|null $navigationGroup = 'Formations';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'formation';

    protected static ?string $pluralModelLabel = 'formations et programmes';

    protected static ?string $navigationLabel = 'Formations';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()->columnSpanFull()->persistTabInQueryString()->tabs([
                Tab::make('Présentation')->schema([
                    Section::make()->columns(2)->schema([
                        TextInput::make('title')->label('Intitulé')->required()->maxLength(150)->columnSpanFull(),
                        Select::make('audience')->label('Public')->options(Audience::class)->required()
                            ->helperText('« Professionnels » : visible uniquement dans l\'Espace Professionnels.'),
                        Select::make('kind')->label('Type')->options(ProgramKind::class)->required(),
                        TextInput::make('level_label')->label('Niveau / titre délivré')->placeholder('Ex. Certification de niveau BTS (Bac+2)'),
                        TextInput::make('duration_label')->label('Durée')->placeholder('Ex. 9 mois (1 080 h)'),
                        Textarea::make('summary')->label('Résumé (cartes et référencement)')->rows(3)->maxLength(300)->columnSpanFull(),
                        RichEditor::make('description')->label('Présentation détaillée')
                            ->toolbarButtons([['bold', 'italic', 'link'], ['h2', 'h3'], ['bulletList', 'orderedList', 'blockquote'], ['undo', 'redo']])
                            ->columnSpanFull(),
                    ]),
                ]),
                Tab::make('Contenu pédagogique')->schema([
                    TagsInput::make('skills')->label('Compétences visées')->placeholder('Ajouter une compétence puis Entrée'),
                    TagsInput::make('outcomes')->label('Débouchés')->placeholder('Ex. Régisseur lumière'),
                    TagsInput::make('prerequisites')->label('Prérequis')->placeholder('Ex. Titulaire d\'un CPS ou d\'un CS'),
                    TagsInput::make('equipment')->label('Équipements utilisés')->placeholder('Ex. Console GrandMA3'),
                ]),
                Tab::make('Image et publication')->schema([
                    Section::make('Visuel')->columns(2)->schema(Fields::image('cover_image', 'programs')),
                    Fields::publication(),
                    Fields::seo(),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->reorderable('position')
            ->columns([
                TextColumn::make('title')->label('Formation')->searchable()->wrap()->weight('bold'),
                TextColumn::make('audience')->label('Public')->badge(),
                TextColumn::make('kind')->label('Type'),
                TextColumn::make('status')->label('État')->badge(),
            ])
            ->filters([
                SelectFilter::make('audience')->label('Public')->options(Audience::class),
                SelectFilter::make('status')->label('État')->options(PublicationStatus::class),
                TrashedFilter::make(),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getRelations(): array
    {
        return [CohortsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrograms::route('/'),
            'create' => CreateProgram::route('/create'),
            'edit' => EditProgram::route('/{record}/edit'),
        ];
    }
}
