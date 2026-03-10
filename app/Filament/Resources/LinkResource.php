<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LinkResource\Pages;
use App\Models\Link;
use Filament\Forms;

use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LinkResource extends Resource
{
    protected static ?string $model = Link::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-link';

    protected static \UnitEnum|string|null $navigationGroup = 'Contenuti';

    protected static ?string $navigationLabel = 'Link Brevi';

    protected static ?string $modelLabel = 'Link';

    protected static ?string $pluralModelLabel = 'Link';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Schemas\Components\Section::make('Dettagli Link')
                    ->components([
                        Forms\Components\TextInput::make('title')
                            ->label('Titolo')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('url')
                            ->label('URL di destinazione')
                            ->required()
                            ->url()
                            ->maxLength(2048),
                        Forms\Components\Textarea::make('description')
                            ->label('Descrizione')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])->columns(2),

                Schemas\Components\Section::make('Impostazioni')
                    ->components([
                        Forms\Components\Select::make('type')
                            ->label('Tipo')
                            ->options([
                                'url' => 'URL',
                                'email' => 'Email',
                                'phone' => 'Telefono',
                            ])
                            ->default('url')
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Attivo')
                            ->default(true),
                        Forms\Components\Toggle::make('require_consent')
                            ->label('Richiedi Consenso'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Titolo')
                    ->searchable()
                    ->limit(25),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('url')
                    ->label('URL')
                    ->limit(40)
                    ->url(fn (Link $record) => $record->url, shouldOpenInNewTab: true),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Utente')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'url' => 'info',
                        'email' => 'warning',
                        'phone' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Attivo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Visite')
                    ->counts('views')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creato')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'url' => 'URL',
                        'email' => 'Email',
                        'phone' => 'Telefono',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Attivo'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLinks::route('/'),
            'create' => Pages\CreateLink::route('/create'),
            'view' => Pages\ViewLink::route('/{record}'),
            'edit' => Pages\EditLink::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
