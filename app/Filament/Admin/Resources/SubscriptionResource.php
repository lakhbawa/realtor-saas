<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SubscriptionResource\Pages;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Platform Management';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Subscription Details')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Tenant')
                            ->options(User::where('is_admin', false)->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->disabled(fn ($record) => $record !== null),
                        Forms\Components\Select::make('plan_id')
                            ->label('Plan')
                            ->options(Plan::active()->ordered()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('billing_cycle')
                            ->options(Subscription::BILLING_CYCLES)
                            ->default('monthly')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options(Subscription::STATUSES)
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Stripe Information')
                    ->schema([
                        Forms\Components\TextInput::make('stripe_subscription_id')
                            ->label('Stripe Subscription ID')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('stripe_price_id')
                            ->label('Stripe Price ID')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Billing Period')
                    ->schema([
                        Forms\Components\DateTimePicker::make('current_period_start')
                            ->label('Current Period Start'),
                        Forms\Components\DateTimePicker::make('current_period_end')
                            ->label('Current Period End'),
                        Forms\Components\DateTimePicker::make('trial_ends_at')
                            ->label('Trial Ends At'),
                        Forms\Components\DateTimePicker::make('ends_at')
                            ->label('Subscription Ends At'),
                        Forms\Components\DateTimePicker::make('canceled_at')
                            ->label('Canceled At'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('plan.name')
                    ->label('Plan')
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('billing_cycle')
                    ->label('Billing')
                    ->formatStateUsing(fn ($state) => Subscription::BILLING_CYCLES[$state] ?? ucfirst($state)),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => fn ($state) => in_array($state, ['trialing', 'past_due']),
                        'danger' => fn ($state) => in_array($state, ['canceled', 'unpaid', 'incomplete_expired']),
                        'secondary' => 'incomplete',
                    ]),
                Tables\Columns\TextColumn::make('current_period_end')
                    ->label('Renews')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stripe_subscription_id')
                    ->label('Stripe ID')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(20),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('plan_id')
                    ->label('Plan')
                    ->options(Plan::pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('billing_cycle')
                    ->options(Subscription::BILLING_CYCLES),
                Tables\Filters\SelectFilter::make('status')
                    ->options(Subscription::STATUSES),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'view' => Pages\ViewSubscription::route('/{record}'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
