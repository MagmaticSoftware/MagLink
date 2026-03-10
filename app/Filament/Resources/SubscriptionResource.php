<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\StripeSubscription;
use Filament\Forms;

use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionResource extends Resource
{
    protected static ?string $model = StripeSubscription::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-credit-card';

    protected static \UnitEnum|string|null $navigationGroup = 'Abbonamenti';

    protected static ?string $navigationLabel = 'Abbonamenti';

    protected static ?string $modelLabel = 'Abbonamento';

    protected static ?string $pluralModelLabel = 'Abbonamenti';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Schemas\Components\Section::make('Dettagli Abbonamento')
                    ->components([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome Piano')
                            ->disabled(),
                        Forms\Components\TextInput::make('stripe_id')
                            ->label('Stripe Subscription ID')
                            ->disabled(),
                        Forms\Components\TextInput::make('stripe_status')
                            ->label('Stato Stripe')
                            ->disabled(),
                        Forms\Components\TextInput::make('stripe_price')
                            ->label('Price ID')
                            ->disabled(),
                    ])->columns(2),

                Schemas\Components\Section::make('Date')
                    ->components([
                        Forms\Components\DateTimePicker::make('trial_ends_at')
                            ->label('Fine Trial'),
                        Forms\Components\DateTimePicker::make('ends_at')
                            ->label('Scadenza'),
                        Forms\Components\DateTimePicker::make('current_period_end')
                            ->label('Fine Periodo Corrente'),
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
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Utente')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Piano')
                    ->badge(),
                Tables\Columns\TextColumn::make('stripe_id')
                    ->label('Stripe ID')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('stripe_status')
                    ->label('Stato')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'trialing' => 'warning',
                        'canceled', 'incomplete_expired' => 'danger',
                        'past_due', 'incomplete' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('stripe_price')
                    ->label('Price ID')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('current_period_end')
                    ->label('Fine Periodo')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('trial_ends_at')
                    ->label('Fine Trial')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ends_at')
                    ->label('Cancellato il')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creato')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('stripe_status')
                    ->label('Stato')
                    ->options([
                        'active' => 'Attivo',
                        'trialing' => 'Trial',
                        'canceled' => 'Cancellato',
                        'past_due' => 'Pagamento In Ritardo',
                        'incomplete' => 'Incompleto',
                        'incomplete_expired' => 'Scaduto',
                    ]),
                Tables\Filters\SelectFilter::make('name')
                    ->label('Piano')
                    ->options([
                        'default' => 'Default',
                    ]),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            'view' => Pages\ViewSubscription::route('/{record}'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('user');
    }
}
