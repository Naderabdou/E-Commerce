<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Number;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\ToggleButtons;
use App\Filament\Resources\OrderResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\RelationManagers\AddressRelationManager;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?int $navigationSort = 5;

    public static function getModelLabel(): string
    {
        return __('Order');
    }

     public static function getPluralModelLabel(): string
    {
        return __('Orders');
    }

    public static function getNavigationBadge() : ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor() : ?string
    {
       return static::getModel()::count() > 10 ? 'success' : 'danger';
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make(__('Order Information'))->schema([
                        Select::make('user_id')
                            ->label(__('coustomer'))
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('payment_method')
                            ->label(__('Payment Method'))
                            ->options([
                                'cash' => __('Cash On Delivery'),
                                'credit_card' => __('Credit Card'),
                                'paypal' => __('Paypal'),
                                'stripe' => __('Stripe'),
                            ])
                            ->required(),
                        Select::make('payment_status')
                            ->label(__('Payment Status'))
                            ->options([
                                'pending' => __('Pending'),
                                'paid' => __('Paid'),
                                'failed' => __('Failed'),
                            ])
                            ->required()
                            ->default('pending'),


                        ToggleButtons::make('status')
                            ->label(__('Status'))
                            ->options([
                                'new' => __('New'),
                                'processing' => __('Processing'),
                                'shipped' => __('Shipped'),
                                'delivered' => __('Delivered'),
                                'canceled' => __('Canceled'),
                            ])
                            ->colors([
                                'new' => 'info',
                                'processing' => 'warning',
                                'shipped' => 'success',
                                'delivered' => 'success',
                                'canceled' => 'danger',
                            ])
                            ->icons([
                                'new' => 'heroicon-m-sparkles',
                                'processing' => 'heroicon-m-arrow-path',
                                'shipped' => 'heroicon-m-truck',
                                'delivered' => 'heroicon-m-check-badge',
                                'canceled' => 'heroicon-m-x-circle',
                            ])
                            ->inline()
                            ->required()

                            ->default('new'),

                        Select::make('currency')
                            ->label(__('Currency'))
                            ->options([
                                'USD' => __('USD'),
                                'EUR' => __('EUR'),
                                'GBP' => __('GBP'),
                                'CAD' => __('CAD'),
                                'AUD' => __('AUD'),
                                'EGP' => __('EGP'),
                            ])
                            ->default('EGP')
                            ->required(),
                        Select::make('shipping_method')
                            ->label(__('Shipping Method'))
                            ->options([
                                // 'standard' => __('Standard'),
                                // 'express' => __('Express'),
                                'Fedex' => __('Fedex'),
                                'DHL' => __('DHL'),
                                'Aramex' => __('Aramex'),
                                'UPS' => __('UPS'),
                                'USPS' => __('USPS'),
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->label(__('Notes'))
                            ->columnSpanFull()



                    ])->columns(2),

                    Section::make(__('Order Items'))->schema([
                        Repeater::make('items')
                            ->label(__('Items'))
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->label(__('Product'))
                                    ->relationship('product', 'name_' . app()->getLocale())
                                    ->searchable()
                                    ->preload()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->required()
                                    ->reactive()
                                    ->columnSpan(4)
                                    ->afterStateUpdated(fn ($state, Set $set) =>  $set('unit_amount', Product::find($state)?->price ?? 0))
                                    ->afterStateUpdated(fn ($state, Set $set) =>  $set('total_amount', Product::find($state)?->price ?? 0)),

                                TextInput::make('quantity')
                                    ->label(__('Quantity'))
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->required()
                                    ->columnSpan(2)
                                    ->reactive()
                                    ->required()

                                    ->afterStateUpdated(function ($state, Set $set, Get  $get) {
                                        $set('total_amount', $get('unit_amount') * $state);
                                    }),
                                TextInput::make('unit_amount')
                                    ->label(__('Unit Amount'))
                                    ->numeric()
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpan(3),
                                TextInput::make('total_amount')
                                    ->label(__('Total Amount'))
                                    ->numeric()
                                    ->required()
                                    ->dehydrated()
                                    ->columnSpan(3),
                            ])->columns(12),

                        Placeholder::make('grand_total_placeholder')
                            ->label(__('Grand Total'))
                            ->content(function (Get $get, Set $set) {
                                $total = 0;
                                if (!$repeater = $get('items')) {
                                    return $total;
                                }
                                foreach ($repeater  as $key => $item) {
                                    $total += $get('items.' . $key . '.total_amount');
                                }
                                $set('grand_total', $total);
                                return Number::currency($total, $get('currency'));
                                // $set('grand_total_placeholder' , $get('items')->sum('total_amount'));

                            }),
                        Hidden::make('grand_total')
                            ->default(0)


                    ])

                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label(__('Customer'))->searchable()->sortable(),
                TextColumn::make('grand_total')->label(__('Grand Total'))
                    ->searchable()
                    ->sortable()
                    ->numeric()
                    ->money(fn ($record) => $record->currency),
                TextColumn::make('payment_method')
                    ->label(__('Payment Method'))
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        return __($record->payment_method);
                    }),
                TextColumn::make('payment_status')->label(__('Payment Status'))->searchable()->sortable()->getStateUsing(function ($record) {
                    return __($record->payment_status);
                }),
                TextColumn::make('currency')->label(__('Currency'))->searchable()->sortable()->getStateUsing(function ($record) {
                    return __($record->currency);
                }),
                TextColumn::make('shipping_method')->label(__('Shipping Method'))->searchable()->sortable()->getStateUsing(function ($record) {
                    return __($record->shipping_method);
                }),
                SelectColumn::make('status')->label(__('Status'))
                    ->options([
                        'new' => __('New'),
                        'processing' => __('Processing'),
                        'shipped' => __('Shipped'),
                        'delivered' => __('Delivered'),
                        'canceled' => __('Canceled'),
                    ])->sortable(),
                TextColumn::make('created_at')->label(__('Created At'))->sortable()->dateTime()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label(__('Updated At'))->sortable()->dateTime()->toggleable(isToggledHiddenByDefault: true),











            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AddressRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
