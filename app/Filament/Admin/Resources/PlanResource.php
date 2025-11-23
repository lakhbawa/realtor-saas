<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PlanResource\Pages;
use App\Models\Plan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Platform Management';

    protected static ?int $navigationSort = 5;

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Plan Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Pro Plan'),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true)
                            ->placeholder('pro'),
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->maxLength(500)
                            ->placeholder('Perfect for growing realtors...'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pricing')
                    ->description('Set prices in dollars. They will be stored in cents.')
                    ->schema([
                        Forms\Components\TextInput::make('monthly_price')
                            ->label('Monthly Price ($)')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->minValue(0)
                            ->step(0.01)
                            ->dehydrateStateUsing(fn ($state) => (int) ($state * 100))
                            ->formatStateUsing(fn ($state) => $state ? number_format($state / 100, 2) : null),
                        Forms\Components\TextInput::make('quarterly_price')
                            ->label('Quarterly Price ($)')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->minValue(0)
                            ->step(0.01)
                            ->dehydrateStateUsing(fn ($state) => (int) ($state * 100))
                            ->formatStateUsing(fn ($state) => $state ? number_format($state / 100, 2) : null)
                            ->helperText('Total for 3 months'),
                        Forms\Components\TextInput::make('annual_price')
                            ->label('Annual Price ($)')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->minValue(0)
                            ->step(0.01)
                            ->dehydrateStateUsing(fn ($state) => (int) ($state * 100))
                            ->formatStateUsing(fn ($state) => $state ? number_format($state / 100, 2) : null)
                            ->helperText('Total for 12 months'),
                        Forms\Components\TextInput::make('trial_days')
                            ->label('Trial Period (days)')
                            ->numeric()
                            ->default(14)
                            ->minValue(0)
                            ->maxValue(90),
                    ])
                    ->columns(4),

                Forms\Components\Section::make('Stripe Price IDs')
                    ->description('Enter the Stripe Price IDs for each billing cycle')
                    ->schema([
                        Forms\Components\TextInput::make('stripe_monthly_price_id')
                            ->label('Monthly Price ID')
                            ->maxLength(255)
                            ->placeholder('price_xxxxx'),
                        Forms\Components\TextInput::make('stripe_quarterly_price_id')
                            ->label('Quarterly Price ID')
                            ->maxLength(255)
                            ->placeholder('price_xxxxx'),
                        Forms\Components\TextInput::make('stripe_annual_price_id')
                            ->label('Annual Price ID')
                            ->maxLength(255)
                            ->placeholder('price_xxxxx'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Features (Display)')
                    ->description('Features shown to customers on pricing page')
                    ->schema([
                        Forms\Components\Repeater::make('features')
                            ->simple(
                                Forms\Components\TextInput::make('feature')
                                    ->required()
                                    ->placeholder('Unlimited property listings')
                            )
                            ->defaultItems(3)
                            ->reorderable()
                            ->addActionLabel('Add Feature'),
                    ]),

                Forms\Components\Section::make('Limits (Enforcement)')
                    ->description('Set limits for feature access control. Use -1 for unlimited.')
                    ->schema([
                        Forms\Components\TextInput::make('limits.max_properties')
                            ->label('Max Properties')
                            ->numeric()
                            ->default(10)
                            ->helperText('-1 for unlimited'),
                        Forms\Components\TextInput::make('limits.max_blog_posts')
                            ->label('Max Blog Posts')
                            ->numeric()
                            ->default(5)
                            ->helperText('-1 for unlimited'),
                        Forms\Components\TextInput::make('limits.max_pages')
                            ->label('Max Pages')
                            ->numeric()
                            ->default(3)
                            ->helperText('-1 for unlimited'),
                        Forms\Components\TextInput::make('limits.max_testimonials')
                            ->label('Max Testimonials')
                            ->numeric()
                            ->default(10)
                            ->helperText('-1 for unlimited'),
                        Forms\Components\TextInput::make('limits.max_images_per_property')
                            ->label('Max Images per Property')
                            ->numeric()
                            ->default(5)
                            ->helperText('-1 for unlimited'),
                        Forms\Components\Toggle::make('limits.can_use_custom_domain')
                            ->label('Custom Domain')
                            ->default(false),
                        Forms\Components\Toggle::make('limits.can_access_analytics')
                            ->label('Analytics Access')
                            ->default(false),
                        Forms\Components\Toggle::make('limits.can_remove_branding')
                            ->label('Remove Branding')
                            ->default(false),
                        Forms\Components\Toggle::make('limits.priority_support')
                            ->label('Priority Support')
                            ->default(false),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Inactive plans cannot be purchased'),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured')
                            ->default(false)
                            ->helperText('Highlight this plan on pricing page'),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('formatted_monthly_price')
                    ->label('Monthly')
                    ->sortable('monthly_price'),
                Tables\Columns\TextColumn::make('formatted_quarterly_price')
                    ->label('Quarterly'),
                Tables\Columns\TextColumn::make('formatted_annual_price')
                    ->label('Annual'),
                Tables\Columns\TextColumn::make('subscriptions_count')
                    ->counts('subscriptions')
                    ->label('Subscribers')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('duplicate')
                    ->label('Duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->action(function (Plan $record) {
                        $newPlan = $record->replicate();
                        $newPlan->name = $record->name . ' (Copy)';
                        $newPlan->slug = $record->slug . '-copy-' . time();
                        $newPlan->is_active = false;
                        $newPlan->save();
                    })
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
        ];
    }
}
