<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Infolists;

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
                        Forms\Components\DateTimePicker::make('pro_lifetime_started_at')
                            ->label('Inizio Pro Lifetime')
                            ->helperText('Concede permanentemente i limiti del piano Enterprise, indipendentemente da Stripe.'),
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

    /** Etichette leggibili per i valori salvati in fase di registrazione */
    private const LABELS = [
        'usage_type' => ['personal' => 'Privato', 'company' => 'Azienda', 'freelancer' => 'Professionista', 'agency' => 'Agenzia', 'other' => 'Altro'],
        'discovery_source' => ['google' => 'Google', 'social' => 'Social network', 'word_of_mouth' => 'Passaparola', 'ad' => 'Pubblicità', 'blog' => 'Blog/Articolo', 'other' => 'Altro', 'direct' => 'Non indicato'],
        'main_goal' => ['monitorare_campagne' => 'Monitorare campagne', 'gestire_qr' => 'Gestire QR Code', 'accorciare_link' => 'Accorciare link', 'altro' => 'Altro'],
        'estimated_usage' => ['low' => 'Basso', 'medium' => 'Medio', 'high' => 'Alto'],
        'industry' => ['technology' => 'Tecnologia', 'marketing' => 'Marketing e pubblicità', 'ecommerce' => 'E-commerce', 'education' => 'Istruzione', 'healthcare' => 'Sanità', 'finance' => 'Finanza', 'real_estate' => 'Immobiliare', 'food' => 'Food & beverage', 'other' => 'Altro'],
        'size' => ['solo' => 'Solo titolare', '1-10' => '1-10 dipendenti', '11-50' => '11-50 dipendenti', '51-200' => '51-200 dipendenti', '200+' => 'Oltre 200 dipendenti'],
    ];

    private static function label(string $group): \Closure
    {
        return fn ($state) => self::LABELS[$group][$state] ?? $state;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Schemas\Components\Section::make('Account')
                    ->components([
                        Infolists\Components\TextEntry::make('first_name')->label('Nome'),
                        Infolists\Components\TextEntry::make('last_name')->label('Cognome'),
                        Infolists\Components\TextEntry::make('email')->label('Email')->copyable(),
                        Infolists\Components\TextEntry::make('tenant_id')->label('Tenant')->placeholder('—'),
                        Infolists\Components\TextEntry::make('created_at')->label('Registrato il')->dateTime('d/m/Y H:i'),
                        Infolists\Components\TextEntry::make('email_verified_at')->label('Email verificata il')->dateTime('d/m/Y H:i')->placeholder('Non verificata'),
                    ])->columns(3),

                Schemas\Components\Section::make('Abbonamento')
                    ->components([
                        Infolists\Components\TextEntry::make('plan')
                            ->label('Piano attuale')
                            ->getStateUsing(fn (User $record) => $record->currentPlanName() ?? ($record->onTrial() ? 'Trial' : 'Nessuno'))
                            ->badge(),
                        Infolists\Components\TextEntry::make('trial_ends_at')->label('Fine trial')->dateTime('d/m/Y')->placeholder('—'),
                        Infolists\Components\TextEntry::make('free_plan_started_at')->label('Inizio piano Free')->dateTime('d/m/Y')->placeholder('—'),
                        Infolists\Components\TextEntry::make('pro_lifetime_started_at')->label('Inizio Pro Lifetime')->dateTime('d/m/Y')->placeholder('—'),
                    ])->columns(4),

                Schemas\Components\Section::make('Azienda')
                    ->components([
                        Infolists\Components\TextEntry::make('companies.name')->label('Ragione sociale')->placeholder('—'),
                        Infolists\Components\TextEntry::make('companies.slug')->label('URL pubblico')->placeholder('—'),
                        Infolists\Components\TextEntry::make('companies.email')->label('Email aziendale')->placeholder('—'),
                        Infolists\Components\TextEntry::make('companies.billingProfile.phone')->label('Telefono')->placeholder('—'),
                        Infolists\Components\TextEntry::make('companies.industry')->label('Settore')->formatStateUsing(self::label('industry'))->placeholder('—'),
                        Infolists\Components\TextEntry::make('companies.size')->label('Dimensione')->formatStateUsing(self::label('size'))->placeholder('—'),
                    ])->columns(3),

                Schemas\Components\Section::make('Profilo e preferenze')
                    ->description('Dati raccolti nel wizard di registrazione')
                    ->components([
                        Infolists\Components\TextEntry::make('preferences.usage_type')->label('Profilo')->badge()->formatStateUsing(self::label('usage_type'))->placeholder('—'),
                        Infolists\Components\TextEntry::make('preferences.main_goal')->label('Obiettivo principale')->formatStateUsing(self::label('main_goal'))->placeholder('—'),
                        Infolists\Components\TextEntry::make('preferences.estimated_usage')->label('Utilizzo stimato')->formatStateUsing(self::label('estimated_usage'))->placeholder('—'),
                        Infolists\Components\TextEntry::make('preferences.discovery_source')->label('Come ci ha conosciuto')->formatStateUsing(self::label('discovery_source'))->placeholder('—'),
                        Infolists\Components\TextEntry::make('preferences.team_size')->label('Dimensione team')->formatStateUsing(self::label('size'))->placeholder('—'),
                        Infolists\Components\TextEntry::make('preferences.lang')->label('Lingua')->formatStateUsing(fn ($state) => strtoupper((string) $state))->placeholder('—'),
                        Infolists\Components\TextEntry::make('preferences.timezone')->label('Fuso orario')->placeholder('—'),
                    ])->columns(4),

                Schemas\Components\Section::make('Privacy e consensi')
                    ->components([
                        Infolists\Components\IconEntry::make('preferences.terms_and_conditions')->label('Termini di servizio')->boolean(),
                        Infolists\Components\TextEntry::make('preferences.terms_and_conditions_accepted_at')->label('Accettati il')->dateTime('d/m/Y H:i')->placeholder('—'),
                        Infolists\Components\IconEntry::make('preferences.privacy_policy')->label('Privacy policy')->boolean(),
                        Infolists\Components\TextEntry::make('preferences.privacy_policy_accepted_at')->label('Accettata il')->dateTime('d/m/Y H:i')->placeholder('—'),
                        Infolists\Components\IconEntry::make('preferences.newsletter')->label('Newsletter')->boolean(),
                        Infolists\Components\TextEntry::make('preferences.newsletter_accepted_at')->label('Iscritto il')->dateTime('d/m/Y H:i')->placeholder('—'),
                    ])->columns(2),
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
                Tables\Columns\TextColumn::make('companies.name')
                    ->label('Azienda')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('preferences.usage_type')
                    ->label('Profilo')
                    ->formatStateUsing(self::label('usage_type'))
                    ->badge()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('preferences.privacy_policy')
                    ->label('Privacy')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('preferences.newsletter')
                    ->label('Newsletter')
                    ->boolean()
                    ->toggleable(),
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
                Tables\Filters\SelectFilter::make('profile')
                    ->label('Profilo')
                    ->options(self::LABELS['usage_type'])
                    ->query(fn (Builder $query, array $data) => $query->when(
                        $data['value'] ?? null,
                        fn (Builder $q, $value) => $q->whereHas('preferences', fn (Builder $p) => $p->where('usage_type', $value))
                    )),
                Tables\Filters\Filter::make('newsletter')
                    ->label('Iscritti newsletter')
                    ->query(fn (Builder $query) => $query->whereHas('preferences', fn (Builder $q) => $q->where('newsletter', true))),
                Tables\Filters\Filter::make('on_pro_lifetime')
                    ->label('Pro Lifetime')
                    ->query(fn (Builder $query) => $query->whereNotNull('pro_lifetime_started_at')),
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
            ->with(['preferences', 'companies.billingProfile'])
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
