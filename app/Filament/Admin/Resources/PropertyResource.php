<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PropertyResource\Pages;
use App\Models\Property;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 4;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Property Owner')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Tenant')
                            ->options(User::where('is_admin', false)->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                    ]),

                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('description')
                            ->columnSpanFull(),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Property Status')
                                    ->options([
                                        'active' => 'Active',
                                        'pending' => 'Pending',
                                        'sold' => 'Sold',
                                        'inactive' => 'Inactive',
                                    ])
                                    ->default('active'),
                                Forms\Components\Select::make('listing_status')
                                    ->label('Listing Type')
                                    ->options([
                                        'for_sale' => 'For Sale',
                                        'for_rent' => 'For Rent',
                                    ])
                                    ->default('for_sale'),
                                Forms\Components\Toggle::make('is_featured')
                                    ->label('Featured Property'),
                            ]),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Property Details')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->prefix('$')
                            ->maxValue(999999999),
                        Forms\Components\TextInput::make('bedrooms')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\TextInput::make('bathrooms')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.5),
                        Forms\Components\TextInput::make('square_feet')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\TextInput::make('year_built')
                            ->numeric()
                            ->label('Year Built')
                            ->minValue(1800)
                            ->maxValue(date('Y') + 5),
                    ])
                    ->columns(5),

                Forms\Components\Section::make('Features')
                    ->schema([
                        Forms\Components\TagsInput::make('features')
                            ->label('Property Features')
                            ->placeholder('Add features...')
                            ->helperText('Press Enter to add each feature (e.g., Pool, Garage, Hardwood Floors)')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Location')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('city')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('state')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('zip')
                            ->maxLength(20),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Images & Media')
                    ->schema([
                        Forms\Components\FileUpload::make('featured_image')
                            ->label('Main Featured Image')
                            ->image()
                            ->directory('properties')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->helperText('This is the main image shown in listings.'),
                        Forms\Components\Repeater::make('images')
                            ->relationship()
                            ->label('Additional Gallery Images')
                            ->schema([
                                Forms\Components\FileUpload::make('image_path')
                                    ->label('Image')
                                    ->image()
                                    ->directory('property-images')
                                    ->visibility('public')
                                    ->maxSize(5120)
                                    ->required(),
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Order')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->reorderable()
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->addActionLabel('Add Image')
                            ->helperText('Add multiple images for the property gallery'),
                        Forms\Components\TextInput::make('video_url')
                            ->label('Video URL (Optional)')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://youtube.com/watch?v=...')
                            ->helperText('YouTube or Vimeo video URL'),
                    ]),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                    ]),
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
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Image')
                    ->square(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('listing_status')
                    ->label('Type')
                    ->colors([
                        'success' => 'for_sale',
                        'info' => 'for_rent',
                    ])
                    ->formatStateUsing(fn (?string $state): string => $state === 'for_sale' ? 'For Sale' : ($state === 'for_rent' ? 'For Rent' : '-')),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'pending',
                        'danger' => 'sold',
                        'secondary' => 'inactive',
                    ]),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Tenant')
                    ->options(User::where('is_admin', false)->pluck('name', 'id'))
                    ->searchable(),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'pending' => 'Pending',
                        'sold' => 'Sold',
                        'inactive' => 'Inactive',
                    ]),
                Tables\Filters\SelectFilter::make('listing_status')
                    ->label('Listing Type')
                    ->options([
                        'for_sale' => 'For Sale',
                        'for_rent' => 'For Rent',
                    ]),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'view' => Pages\ViewProperty::route('/{record}'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }
}
