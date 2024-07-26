<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\RelationManagers\OrdersRelationManager;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;





    public static function getModelLabel(): string
    {
        return __('User');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Users');
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
                Forms\Components\TextInput::make('name')
                    ->label(__('Name'))
                    ->required()
                    ->maxlength(255)
                    ->minlength(3)
                    ->placeholder(__('Enter your name')),
                Forms\Components\TextInput::make('email')
                    ->label(__('Email'))
                    ->required()
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->placeholder(__('Enter your email address')),
                    Forms\Components\DatetimePicker::make('email_verified_at')
                    ->label(__('Email Verified At'))
                    ->default(now()),
                Forms\Components\TextInput::make('password')
                    ->label(__('Password'))

                    ->password()
                    ->dehydrated(fn ($state) => filled($state) )
                    ->required(fn (Page $livewire) : bool => $livewire instanceof CreateRecord)
                    ->minlength(8)
                    ->placeholder(__('Enter your password')),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->label(__('Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label(__('Email Verified At'))
                    ->sortable()
                    ->dateTime(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->sortable()
                    ->dateTime(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->sortable()
                    ->dateTime(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->icon('heroicon-m-ellipsis-horizontal')->color('info')->tooltip(__('Actions')),



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
            OrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
