<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\PropertyResource\Pages;
use App\Models\Property;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('description')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Property Status')
                                    ->options([
                                        'active' => 'Active',
                                        'pending' => 'Pending',
                                        'sold' => 'Sold',
                                    ])
                                    ->required()
                                    ->default('active'),
                                Forms\Components\Select::make('listing_status')
                                    ->label('Listing Type')
                                    ->options([
                                        'for_sale' => 'For Sale',
                                        'for_rent' => 'For Rent',
                                    ])
                                    ->required()
                                    ->default('for_sale'),
                                Forms\Components\Toggle::make('is_featured')
                                    ->label('Featured Property')
                                    ->helperText('Show on homepage'),
                            ]),
                    ])->columns(2),

                Forms\Components\Section::make('Property Details')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->maxValue(999999999999),
                        Forms\Components\TextInput::make('bedrooms')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(50),
                        Forms\Components\TextInput::make('bathrooms')
                            ->numeric()
                            ->step(0.5)
                            ->minValue(0)
                            ->maxValue(50),
                        Forms\Components\TextInput::make('square_feet')
                            ->numeric()
                            ->label('Square Feet')
                            ->suffix('sqft'),
                        Forms\Components\TextInput::make('year_built')
                            ->numeric()
                            ->label('Year Built')
                            ->minValue(1800)
                            ->maxValue(date('Y') + 5)
                            ->placeholder(date('Y')),
                    ])->columns(5),

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
                            ->maxLength(500)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('city')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('state')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('zip')
                            ->label('ZIP Code')
                            ->maxLength(20),
                    ])->columns(3),

                Forms\Components\Section::make('Images & Media')
                    ->schema([
                        Forms\Components\FileUpload::make('featured_image')
                            ->label('Main Featured Image')
                            ->image()
                            ->directory('properties')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('1200')
                            ->imageResizeTargetHeight('675')
                            ->helperText('This is the main image shown in listings. 16:9 aspect ratio recommended.'),
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
                            ->collapseAllAction(
                                fn (Forms\Components\Actions\Action $action) => $action->label('Collapse All'),
                            )
                            ->addActionLabel('Add Image')
                            ->helperText('Add multiple images for the property gallery'),
                        Forms\Components\TextInput::make('video_url')
                            ->label('Video URL (Optional)')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://youtube.com/watch?v=...')
                            ->helperText('YouTube or Vimeo video URL'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Image')
                    ->circular(),
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
                    ->formatStateUsing(fn (string $state): string => $state === 'for_sale' ? 'For Sale' : 'For Rent'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'pending',
                        'secondary' => 'sold',
                    ]),
                Tables\Columns\TextColumn::make('bedrooms')
                    ->label('Beds')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bathrooms')
                    ->label('Baths')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'pending' => 'Pending',
                        'sold' => 'Sold',
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }
}
