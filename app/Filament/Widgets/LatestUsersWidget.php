<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestUsersWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Ultimi Utenti Registrati')
            ->query(
                User::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Cognome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrato')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Verificato')
                    ->boolean()
                    ->getStateUsing(fn (User $record) => $record->email_verified_at !== null),
            ]);
    }
}
