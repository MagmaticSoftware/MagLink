<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;

use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    protected static \UnitEnum|string|null $navigationGroup = 'Contenuti';

    protected static ?string $navigationLabel = 'Pagine';

    protected static ?string $modelLabel = 'Pagina';

    protected static ?string $pluralModelLabel = 'Pagine';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Schemas\Components\Section::make('Informazioni Pagina')
                    ->components([
                        Forms\Components\TextInput::make('title')
                            ->label('Titolo')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Descrizione')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])->columns(2),

                Schemas\Components\Section::make('Impostazioni')
                    ->components([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Attiva')
                            ->default(true),
                        Forms\Components\DatePicker::make('published_at')
                            ->label('Data Pubblicazione'),
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
                Tables\Columns\TextColumn::make('title')
                    ->label('Titolo')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Utente')
                    ->searchable(),
                Tables\Columns\TextColumn::make('blocks_count')
                    ->label('Blocchi')
                    ->counts('blocks')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('views')
                    ->label('Visite')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Attiva')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Pubblicata')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Pubblicata il')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creata')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Attiva'),
                Tables\Filters\Filter::make('published')
                    ->label('Pubblicata')
                    ->query(fn (Builder $query) => $query->whereNotNull('published_at')->where('is_active', true)),
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'view' => Pages\ViewPage::route('/{record}'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
