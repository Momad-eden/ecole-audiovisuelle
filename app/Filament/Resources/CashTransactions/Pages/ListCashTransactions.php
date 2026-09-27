<?php

namespace App\Filament\Resources\CashTransactions\Pages;

use App\Filament\Resources\CashTransactions\CashTransactionResource;
use App\Filament\Widgets\CashOverview;
use App\Models\CashClosing;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;

class ListCashTransactions extends ListRecords
{
    protected static string $resource = CashTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')->label('Exporter le journal')->icon('heroicon-o-arrow-down-tray')->color('gray')
                ->visible(fn () => auth()->user()->can('viewAny', CashClosing::class))
                ->schema([
                    DatePicker::make('from')->label('Du')->default(now()->startOfMonth())->required(),
                    DatePicker::make('until')->label('Au')->default(now())->required()->afterOrEqual('from'),
                ])
                ->action(fn (array $data) => $this->redirect(route('admin.cash.export', $data))),
            CreateAction::make()->label('Nouvelle opération'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [CashOverview::class];
    }
}
