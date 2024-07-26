<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Order;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Support\Enums\IconPosition;
use App\Filament\Resources\OrderResource;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{

    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 2;

    protected function getTableHeading(): string
    {
        return __('Latest Orders');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(OrderResource::getEloquentQuery())
            ->defaultPaginationPageOption(25)
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('Order ID')),

                Tables\Columns\TextColumn::make('grand_total')->label(__('Grand Total'))
                    ->searchable()
                    ->sortable()
                    ->numeric()
                    ->money(fn ($record) => $record->currency),



                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()

                    ->color(fn ($record) => match ($record->status) {
                        'new' => 'info',
                        'pending' => 'warning',
                        'processing' => 'success',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    })->getStateUsing(function ($record) {
                        return __($record->status);
                    })
                    ->icon(fn ($record) => match ($record->status) {
                        'new' => 'heroicon-m-sparkles',
                        'processing' => 'heroicon-m-arrow-path',
                        'shipped' => 'heroicon-m-truck',
                        'delivered' => 'heroicon-m-check-badge',
                        'canceled' => 'heroicon-m-x-circle',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label(__('Payment Method'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label(__('Payment Status'))
                    ->badge()
                    ->color(fn ($record) => match ($record->payment_status) {
                        'pending' => 'warning',
                        'completed' => 'success',
                        'failed' => 'danger',
                    })->getStateUsing(function ($record) {
                        return __($record->payment_status);
                    })
                    ->icon(fn ($record) => match ($record->payment_status) {
                        'pending' => 'heroicon-m-clock',
                        'completed' => 'heroicon-m-check-badge',
                        'failed' => 'heroicon-m-x-circle',
                    })

                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Order Date'))
                    ->dateTime()



            ])
            ->actions([

                    Tables\Actions\Action::make(__('View Order'))
                    ->label(__('View Order'))
                        ->url(function (Order $record): string {
                            return OrderResource::getUrl('view', ['record' => $record]);
                        })->color('info')->icon('heroicon-m-eye')
                        ,

            ]);
    }
}
