<?php

namespace App\Filament\Widgets;

use App\Models\Link;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestLinksWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Ultimi Link Creati')
            ->query(
                Link::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titolo')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug'),
                Tables\Columns\TextColumn::make('url')
                    ->label('URL')
                    ->limit(40)
                    ->url(fn (Link $record) => $record->url, shouldOpenInNewTab: true),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Utente'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Attivo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creato')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ]);
    }
}
