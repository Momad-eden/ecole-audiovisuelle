<?php

namespace App\Filament\Resources\Faqs;

use App\Filament\Resources\Faqs\Pages\CreateFaq;
use App\Filament\Resources\Faqs\Pages\EditFaq;
use App\Filament\Resources\Faqs\Pages\ListFaqs;
use App\Filament\Support\FrenchLabels;
use App\Filament\Support\TranslationTab;
use App\Models\Faq;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    use FrenchLabels;

    protected static ?string $model = Faq::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static string|\UnitEnum|null $navigationGroup = 'Site';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'question';

    protected static ?string $pluralModelLabel = 'questions fréquentes';

    protected static ?string $recordTitleAttribute = 'question';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Tabs::make()->columnSpanFull()->persistTabInQueryString()->tabs([
                Tab::make('Français')->schema([
                    Section::make()->schema([
                        Select::make('group')->label('Rubrique')->options(Faq::GROUPS)->default('general')->required(),
                        TextInput::make('question')->label('Question')->required()->maxLength(200),
                        Textarea::make('answer')->label('Réponse')->required()->rows(5)->maxLength(2000),
                        Toggle::make('is_visible')->label('Visible sur le site')->default(true),
                    ]),
                ]),
                TranslationTab::make(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->reorderable('position')
            ->columns([
                TextColumn::make('question')->label('Question')->searchable()->wrap()->weight('bold'),
                TextColumn::make('group')->label('Rubrique')->formatStateUsing(fn (string $state) => Faq::GROUPS[$state] ?? $state)->badge(),
                IconColumn::make('is_visible')->label('Visible')->boolean(),
            ])
            ->filters([SelectFilter::make('group')->label('Rubrique')->options(Faq::GROUPS)])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFaqs::route('/'),
            'create' => CreateFaq::route('/create'),
            'edit' => EditFaq::route('/{record}/edit'),
        ];
    }
}
