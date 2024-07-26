<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Actions\Action;
use PhpParser\Node\Expr\Cast\String_;
use Filament\Forms\Components\Actions;
use App\Filament\Resources\OrderResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';


    // public static function getTitle ()  {
    //     return __('Orders');
    // }
    //  protected static ?string $title = 'nader';

    protected  static string $page= '4';

    public static function getTitle(): string
    {
        return __('Orders');
    }

    public static function getModelLabel(): string
    {
        return __('Orders');
    }




    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
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
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Action::make(__('View Order'))
                        ->url(function (Order $record): string {
                            return OrderResource::getUrl('view', ['record' => $record]);
                        })->color('info')->icon('heroicon-m-eye'),

                    Tables\Actions\DeleteAction::make(),
                ])->label(__('Actions')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
