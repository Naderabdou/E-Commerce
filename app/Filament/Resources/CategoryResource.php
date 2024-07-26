<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CategoryResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CategoryResource\RelationManagers;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?int $navigationSort = 3;


    protected static ?string $navigationIcon = 'heroicon-o-tag';
    public static function getModelLabel(): string
    {
        return __('Category');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Categories');
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
                                ->unique(Category::class, 'slug', ignoreRecord: true)
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

                        ->disk('public')->directory('categories')
                        ->preserveFilenames()
                        ->imageEditor(),


                    Forms\Components\Toggle::make('is_active')
                        ->label(__('Is Active'))
                        ->default(true)
                        ->required(),
                ])

                // Forms\Components\TextInput::make('name_ar')
                //     ->label(__('Name Arabic'))
                //     ->required()
                //     ->maxLength(255),
                // Forms\Components\TextInput::make('name_en')
                //     ->label(__('Name English'))
                //     ->required()
                //     ->maxLength(255),
                // Forms\Components\TextInput::make('slug')
                //     ->required()
                //     ->maxLength(255),
                // Forms\Components\FileUpload::make('image')
                //     ->label(__('Image'))
                //     ->image(),

                // Forms\Components\Toggle::make('is_active')
                //     ->required(),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
