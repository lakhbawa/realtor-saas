<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TestimonialResource\Pages;
use App\Models\Property;
use App\Models\Testimonial;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ownership')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Tenant (Owner)')
                            ->options(User::where('is_admin', false)->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])->columns(1),

                Forms\Components\Section::make('Client Information')
                    ->description('Details about the client who provided this testimonial')
                    ->schema([
                        Forms\Components\TextInput::make('client_name')
                            ->required()
                            ->maxLength(255)
                            ->label('Client Name')
                            ->placeholder('John & Jane Smith'),
                        Forms\Components\TextInput::make('client_location')
                            ->maxLength(255)
                            ->label('Location')
                            ->placeholder('San Francisco, CA')
                            ->helperText('City and state of the client'),
                        Forms\Components\FileUpload::make('client_photo')
                            ->label('Client Photo')
                            ->image()
                            ->directory('testimonials/clients')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('300')
                            ->imageResizeTargetHeight('300')
                            ->helperText('Square photo recommended. Will be cropped to 300x300px.'),
                    ])->columns(2),

                Forms\Components\Section::make('Testimonial Content')
                    ->schema([
                        Forms\Components\Textarea::make('content')
                            ->required()
                            ->maxLength(1000)
                            ->rows(5)
                            ->columnSpanFull()
                            ->label('Testimonial')
                            ->placeholder('Share what the client said about their experience...'),
                        Forms\Components\Select::make('rating')
                            ->options([
                                5 => '5 Stars - Excellent',
                                4 => '4 Stars - Very Good',
                                3 => '3 Stars - Good',
                                2 => '2 Stars - Fair',
                                1 => '1 Star - Poor',
                            ])
                            ->required()
                            ->default(5)
                            ->native(false),
                        Forms\Components\TextInput::make('video_url')
                            ->label('Video Testimonial URL')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://youtube.com/watch?v=...')
                            ->helperText('Optional YouTube or Vimeo video testimonial'),
                    ])->columns(2),

                Forms\Components\Section::make('Transaction Details')
                    ->description('Link this testimonial to a specific transaction')
                    ->schema([
                        Forms\Components\Select::make('property_id')
                            ->label('Related Property')
                            ->options(function (Forms\Get $get) {
                                $userId = $get('user_id');
                                if (!$userId) {
                                    return [];
                                }
                                return Property::withoutGlobalScopes()
                                    ->where('user_id', $userId)
                                    ->orderBy('title')
                                    ->pluck('title', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->placeholder('Select a property (optional)')
                            ->helperText('Link to a property this client bought/sold/rented'),
                        Forms\Components\Select::make('transaction_type')
                            ->label('Transaction Type')
                            ->options([
                                'bought' => 'Bought a Home',
                                'sold' => 'Sold a Home',
                                'rented' => 'Rented a Home',
                                'bought_sold' => 'Bought & Sold',
                            ])
                            ->placeholder('Select transaction type')
                            ->native(false),
                        Forms\Components\DatePicker::make('transaction_date')
                            ->label('Transaction Date')
                            ->maxDate(now())
                            ->displayFormat('F Y')
                            ->helperText('When the transaction was completed'),
                    ])->columns(3),

                Forms\Components\Section::make('Display Settings')
                    ->schema([
                        Forms\Components\Toggle::make('is_published')
                            ->label('Published')
                            ->default(true)
                            ->helperText('Show this testimonial on the website'),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured')
                            ->default(false)
                            ->helperText('Highlight this testimonial prominently'),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->label('Display Order')
                            ->helperText('Lower numbers appear first'),
                    ])->columns(3),
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
                Tables\Columns\ImageColumn::make('client_photo')
                    ->label('Photo')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->client_name) . '&background=random'),
                Tables\Columns\TextColumn::make('client_name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->client_location),
                Tables\Columns\TextColumn::make('transaction_type')
                    ->label('Transaction')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'bought' => 'Bought',
                        'sold' => 'Sold',
                        'rented' => 'Rented',
                        'bought_sold' => 'Bought & Sold',
                        default => '-',
                    })
                    ->color(fn ($state) => match($state) {
                        'bought' => 'success',
                        'sold' => 'info',
                        'rented' => 'warning',
                        'bought_sold' => 'primary',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('rating')
                    ->formatStateUsing(fn (int $state): string => str_repeat('★', $state) . str_repeat('☆', 5 - $state))
                    ->html()
                    ->color('warning'),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured')
                    ->trueIcon('heroicon-o-star')
                    ->trueColor('warning'),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean()
                    ->label('Published'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Tenant')
                    ->options(User::where('is_admin', false)->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Published'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured'),
                Tables\Filters\SelectFilter::make('transaction_type')
                    ->options([
                        'bought' => 'Bought',
                        'sold' => 'Sold',
                        'rented' => 'Rented',
                        'bought_sold' => 'Bought & Sold',
                    ]),
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
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}
