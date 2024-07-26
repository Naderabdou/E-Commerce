<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AddressRelationManager extends RelationManager
{
    protected static string $relationship = 'address';

    public static function getTitle(): string
    {
        return __('addresses');
    }
    public static function getModelLabel(): string
    {
        return __('address');
    }

    public static function getPluralModelLabel(): string
    {
        return __('addresses');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('first_name')
                    ->required()
                    ->label(__('First Name'))
                    ->placeholder(__('Enter the first name'))
                    ->maxLength(255),
                TextInput::make('last_name')
                    ->required()
                    ->label(__('Last Name'))
                    ->placeholder(__('Enter the last name'))
                    ->maxLength(255),
                TextInput::make('phone')
                    ->required()
                    ->label(__('Phone'))
                    ->placeholder(__('Enter the phone number'))
                    ->maxLength(255)
                    ->tel(),
                TextInput::make('city')
                    ->required()
                    ->label(__('City'))
                    ->placeholder(__('Enter the city'))
                    ->maxLength(255),
                TextInput::make('state')
                    ->required()
                    ->label(__('State'))
                    ->placeholder(__('Enter the state'))
                    ->maxLength(255),
                TextInput::make('zip_code')
                    ->required()
                    ->label(__('Zip Code'))
                    ->placeholder(__('Enter the zip code'))
                    ->maxLength(255),
                Forms\Components\Textarea::make('street_address')
                    ->required()
                    ->label(__('Street Address'))
                    ->placeholder(__('Enter the street address'))
                    ->maxLength(255)
                    ->columnSpanFull()
                ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('street_address')
            ->columns([
                Tables\Columns\TextColumn::make('street_address')
                ->label(__('Street Address'))
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
