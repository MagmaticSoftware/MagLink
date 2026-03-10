<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QrCodeResource\Pages;
use App\Models\QrCode;
use Filament\Forms;

use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QrCodeResource extends Resource
{
    protected static ?string $model = QrCode::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-qr-code';

    protected static \UnitEnum|string|null $navigationGroup = 'Contenuti';

    protected static ?string $navigationLabel = 'QR Code';

    protected static ?string $modelLabel = 'QR Code';

    protected static ?string $pluralModelLabel = 'QR Code';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Schemas\Components\Section::make('Informazioni QR Code')
                    ->components([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->label('Tipo')
                            ->options([
                                'url' => 'URL',
                                'email' => 'Email',
                                'phone' => 'Telefono',
                                'sms' => 'SMS',
                                'wifi' => 'WiFi',
                                'vcard' => 'vCard',
                                'text' => 'Testo',
                            ])
                            ->required(),
                        Forms\Components\Select::make('format')
                            ->label('Formato')
                            ->options([
                                'svg' => 'SVG',
                                'png' => 'PNG',
                                'eps' => 'EPS',
                            ])
                            ->default('svg'),
                        Forms\Components\Textarea::make('description')
                            ->label('Descrizione')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])->columns(2),

                Schemas\Components\Section::make('Payload & Opzioni')
                    ->components([
                        Forms\Components\KeyValue::make('payload')
                            ->label('Payload'),
                        Forms\Components\KeyValue::make('options')
                            ->label('Opzioni di personalizzazione'),
                    ]),

                Schemas\Components\Section::make('Impostazioni')
                    ->components([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Attivo')
                            ->default(true),
                        Forms\Components\Toggle::make('require_consent')
                            ->label('Richiedi Consenso'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Utente')
                    ->searchable(),
                Tables\Columns\TextColumn::make('scans')
                    ->label('Scansioni')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Attivo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('last_scanned_at')
                    ->label('Ultima Scansione')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                        'sms' => 'SMS',
                        'wifi' => 'WiFi',
                        'vcard' => 'vCard',
                        'text' => 'Testo',
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
            'index' => Pages\ListQrCodes::route('/'),
            'create' => Pages\CreateQrCode::route('/create'),
            'view' => Pages\ViewQrCode::route('/{record}'),
            'edit' => Pages\EditQrCode::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
