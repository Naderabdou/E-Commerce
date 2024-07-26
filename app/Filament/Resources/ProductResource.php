<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\App;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductResource\RelationManagers;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?int $navigationSort = 4;



    public static function getModelLabel(): string
    {
        return __('Product');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Products');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Group::make()->schema([
                    Section::make(__('Product Information'))
                        ->description(__('This is the main information about the product.'))
                        ->schema([
                            TextInput::make('name_ar')
                                ->label(__('Name Arabic'))
                                ->placeholder(__('Enter Arabic Name'))
                                ->required()
                                ->maxLength(255),

                            TextInput::make('name_en')
                                ->required()
                                ->label(__('Name English'))
                                ->placeholder(__('Enter English Name'))
                                ->live(onBlur: true)
                                ->maxLength(255)
                                ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                    if (($get('slug') ?? '') !== Str::slug($old)) {
                                        return;
                                    }

                                    $set('slug', Str::slug($state));
                                }),

                            TextInput::make('slug')
                                ->required()
                                ->unique(Product::class, 'slug', ignoreRecord: true)
                                ->disabled()
                                ->dehydrated(),

                            MarkdownEditor::make('description_ar')
                                ->label(__('Description Arabic'))
                                ->required()
                                ->placeholder(__('Enter Arabic Description'))
                                ->fileAttachmentsDirectory('products')
                                ->columnSpanFull(),
                            MarkdownEditor::make('description_en')
                                ->label(__('Description English'))
                                ->required()
                                ->placeholder(__('Enter English Description'))
                                ->fileAttachmentsDirectory('products')
                                ->columnSpanFull(),
                        ])->columns(3),
                    Section::make(__('Product Images'))
                        ->description(__('This is the main images about the product.'))
                        ->collapsible(true)
                        ->schema([
                            Forms\Components\FileUpload::make('images')
                                ->label(__('Images'))
                                ->image()
                                ->required()

                                ->disk('public')->directory('products')
                                ->preserveFilenames()
                                ->multiple()
                                ->maxFiles(5)
                                ->reorderable(),

                        ]),

                ])->columnSpan(3),

                Group::make()->schema([
                    Section::make(__('price Information'))
                        ->description(__('This is the price information about the product.'))
                        ->collapsible(true)
                        ->schema([
                            TextInput::make(__('price'))
                                ->label(__('Price'))
                                ->required()
                                ->numeric()
                                ->minValue(0)
                                ->prefix('USD')


                        ]),
                    Section::make(__('Associations'))
                        ->description(__('This is the associations information about the product.'))
                        ->collapsible(true)
                        ->schema([
                            Forms\Components\Select::make('category_id')
                                ->label(__('Category'))
                                ->searchable()
                                ->preload()
                                ->relationship('category', 'name_' . App::getLocale())
                                ->required(),

                            Forms\Components\Select::make('brand_id')
                                ->label(__('Brand'))
                                ->relationship('brand', 'name_' . App::getLocale())
                                ->required()
                                ->searchable()
                                ->preload(),

                        ]),

                    Section::make(__('Status'))
                        ->description(__('This is the status information about the product.'))
                        ->collapsible(true)
                        ->schema([
                            Forms\Components\Toggle::make('in_stock')
                                ->label(__('In Stock'))
                                ->default(true)
                                ->required(),

                            Forms\Components\Toggle::make('is_active')
                                ->label(__('Is Active'))
                                ->default(true)
                                ->required(),
                            Forms\Components\Toggle::make('is_featured')
                                ->label(__('Is Featured'))
                                ->default(true)
                                ->required(),
                            Forms\Components\Toggle::make('on_sale')
                                ->label(__('Is On Sale'))
                                ->default(true)
                                ->required(),

                        ]),

                ])->columnSpan(1),
            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name_ar')
                    ->searchable()
                    ->label(__('Name Arabic')),
                TextColumn::make('name_en')
                    ->searchable()
                    ->label(__('Name English')),
                TextColumn::make('category.name_' . App::getLocale())
                    ->searchable()
                    ->label(__('Category')),
                TextColumn::make('brand.name_' . App::getLocale())
                    ->searchable()
                    ->label(__('Brand')),
                TextColumn::make('price')
                    ->label(__('Price'))
                    ->money('USD')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label(__('Is Active'))
                    ->boolean(),

                IconColumn::make('is_featured')
                    ->label(__('Is Featured'))
                    ->boolean(),


                IconColumn::make('in_stock')
                    ->label(__('In Stock'))
                    ->boolean(),
                IconColumn::make('on_sale')
                    ->label(__('Is On Sale'))
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label(__('Categories'))
                    ->relationship('category', 'name_' . App::getLocale()),
                    SelectFilter::make('brand_id')
                    ->label(__('Brands'))
                    ->relationship('brand', 'name_' . App::getLocale())


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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
