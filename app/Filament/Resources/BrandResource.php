<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BrandResource\Pages;
use App\Models\Brand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';

    protected static ?int $navigationSort = 2;



    public static function getModelLabel(): string
    {
        return __('Brand');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Brands');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make([
                    Grid::make()
                        ->schema([

                            Forms\Components\TextInput::make('name_ar')
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
                            Forms\Components\TextInput::make('slug')
                                ->required()
                                ->unique(Brand::class, 'slug', ignoreRecord: true)
                                ->disabled()
                                ->dehydrated()
                                ->maxLength(255),

                        ])->columns(3),
                    Forms\Components\FileUpload::make('image')


                        ->label(__('Image'))
                        // ->rules(Rule::dimensions()->maxWidth(600)->maxHeight(800))

                        ->image()
                        ->required()
                        // ->validationMessages([
                        //     'dimensions' => __('image_error'),
                        // ])

                        ->disk('public')->directory('brands')
                        ->preserveFilenames()
                        ->imageEditor(),


                    Forms\Components\Toggle::make('is_active')
                        ->label(__('Is Active'))
                        ->default(true)
                        ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_ar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name_en')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }
}
