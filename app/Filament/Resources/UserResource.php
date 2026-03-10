<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;

use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';

    protected static \UnitEnum|string|null $navigationGroup = 'Utenti';

    protected static ?string $navigationLabel = 'Utenti';

    protected static ?string $modelLabel = 'Utente';

    protected static ?string $pluralModelLabel = 'Utenti';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Schemas\Components\Section::make('Informazioni Personali')
                    ->components([
                        Forms\Components\TextInput::make('first_name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->label('Cognome')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('Email verificata il'),
                    ])->columns(2),

                Schemas\Components\Section::make('Abbonamento & Trial')
                    ->components([
                        Forms\Components\DateTimePicker::make('trial_ends_at')
                            ->label('Fine Trial'),
                        Forms\Components\DateTimePicker::make('free_plan_started_at')
                            ->label('Inizio Piano Free'),
                    ])->columns(2),

                Schemas\Components\Section::make('Sicurezza')
                    ->components([
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context) => $context === 'create')
                            ->maxLength(255),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Cognome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Verificato')
                    ->boolean()
                    ->getStateUsing(fn (User $record) => $record->email_verified_at !== null),
                Tables\Columns\TextColumn::make('links_count')
                    ->label('Link')
                    ->counts('links')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('trial_ends_at')
                    ->label('Fine Trial')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrato')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Eliminato')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\Filter::make('email_verified')
                    ->label('Email Verificata')
                    ->query(fn (Builder $query) => $query->whereNotNull('email_verified_at')),
                Tables\Filters\Filter::make('on_trial')
                    ->label('In Trial')
                    ->query(fn (Builder $query) => $query->where('trial_ends_at', '>', now())),
                Tables\Filters\Filter::make('on_free_plan')
                    ->label('Piano Free')
                    ->query(fn (Builder $query) => $query->whereNotNull('free_plan_started_at')),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
                Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                    Actions\RestoreBulkAction::make(),
                    Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelationsManagers(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
