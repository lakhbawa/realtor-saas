<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SiteResource\Pages;
use App\Models\Site;
use App\Models\Template;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SiteResource extends Resource
{
    protected static ?string $model = Site::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationGroup = 'Tenants';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Site Owner')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Tenant')
                            ->options(User::where('is_admin', false)->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->disabled(fn ($record) => $record !== null),
                        Forms\Components\Select::make('template_id')
                            ->label('Template')
                            ->options(Template::where('is_active', true)->pluck('name', 'id'))
                            ->searchable(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Site Information')
                    ->schema([
                        Forms\Components\TextInput::make('site_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('tagline')
                            ->maxLength(255),
                        Forms\Components\ColorPicker::make('primary_color')
                            ->default('#3B82F6'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Images')
                    ->description('Upload professional photos and branding images')
                    ->schema([
                        Forms\Components\FileUpload::make('headshot')
                            ->label('Professional Headshot')
                            ->image()
                            ->directory('headshots')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('600')
                            ->imageResizeTargetHeight('600')
                            ->helperText('A professional photo. Square aspect ratio recommended.'),
                        Forms\Components\FileUpload::make('hero_image')
                            ->label('Hero Background Image')
                            ->image()
                            ->directory('hero-images')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth('1920')
                            ->imageResizeTargetHeight('1080')
                            ->helperText('Large banner image for homepage. Landscape orientation recommended.'),
                        Forms\Components\FileUpload::make('logo_path')
                            ->label('Logo')
                            ->image()
                            ->directory('logos')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->helperText('Business logo. PNG with transparent background recommended.'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Professional Information')
                    ->schema([
                        Forms\Components\TextInput::make('license_number')
                            ->label('License Number')
                            ->maxLength(100)
                            ->placeholder('DRE #01234567'),
                        Forms\Components\TextInput::make('brokerage')
                            ->label('Brokerage')
                            ->maxLength(255)
                            ->placeholder('Keller Williams Realty'),
                        Forms\Components\TextInput::make('years_experience')
                            ->label('Years of Experience')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(99),
                        Forms\Components\TextInput::make('specialties')
                            ->label('Specialties')
                            ->maxLength(500)
                            ->placeholder('Luxury Homes, First-Time Buyers')
                            ->helperText('Comma-separated list'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Homepage Stats')
                    ->description('These statistics are displayed on your homepage to showcase your achievements')
                    ->schema([
                        Forms\Components\TextInput::make('stat_properties_sold')
                            ->label('Properties Sold')
                            ->numeric()
                            ->minValue(0)
                            ->placeholder('150')
                            ->helperText('Total number of properties sold'),
                        Forms\Components\TextInput::make('stat_properties_sold_label')
                            ->label('Label')
                            ->maxLength(100)
                            ->placeholder('Properties Sold')
                            ->helperText('Custom label (optional)'),
                        Forms\Components\TextInput::make('stat_sales_volume')
                            ->label('Sales Volume ($M)')
                            ->numeric()
                            ->minValue(0)
                            ->placeholder('50')
                            ->helperText('Total sales volume in millions'),
                        Forms\Components\TextInput::make('stat_sales_volume_label')
                            ->label('Label')
                            ->maxLength(100)
                            ->placeholder('Sales Volume')
                            ->helperText('Custom label (optional)'),
                        Forms\Components\TextInput::make('stat_happy_clients')
                            ->label('Happy Clients')
                            ->numeric()
                            ->minValue(0)
                            ->placeholder('200')
                            ->helperText('Number of satisfied clients'),
                        Forms\Components\TextInput::make('stat_happy_clients_label')
                            ->label('Label')
                            ->maxLength(100)
                            ->placeholder('Happy Clients')
                            ->helperText('Custom label (optional)'),
                        Forms\Components\TextInput::make('stat_average_rating')
                            ->label('Average Rating')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(5)
                            ->step(0.1)
                            ->placeholder('4.9')
                            ->helperText('Average client rating (0-5)'),
                        Forms\Components\TextInput::make('stat_average_rating_label')
                            ->label('Label')
                            ->maxLength(100)
                            ->placeholder('Star Rating')
                            ->helperText('Custom label (optional)'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('address')
                            ->maxLength(255),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('city')
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('state')
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('zip')
                                    ->maxLength(20),
                            ]),
                    ]),

                Forms\Components\Section::make('About')
                    ->schema([
                        Forms\Components\RichEditor::make('bio')
                            ->label('About Me / Bio')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'bulletList',
                                'orderedList',
                            ])
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Social Media')
                    ->schema([
                        Forms\Components\TextInput::make('facebook')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('instagram')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('linkedin')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('twitter')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('youtube')
                            ->url()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('SEO')
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('meta_description')
                            ->rows(3),
                    ]),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_published')
                            ->label('Published'),
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
                Tables\Columns\ImageColumn::make('headshot')
                    ->label('Photo')
                    ->circular(),
                Tables\Columns\TextColumn::make('user.subdomain')
                    ->label('Subdomain')
                    ->searchable()
                    ->url(fn ($record) => $record->user ? "http://{$record->user->subdomain}.localhost:4300" : null, shouldOpenInNewTab: true),
                Tables\Columns\TextColumn::make('site_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('template.name')
                    ->label('Template')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean()
                    ->label('Published'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('template_id')
                    ->label('Template')
                    ->options(Template::pluck('name', 'id')),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Published'),
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
            ->defaultSort('updated_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSites::route('/'),
            'create' => Pages\CreateSite::route('/create'),
            'view' => Pages\ViewSite::route('/{record}'),
            'edit' => Pages\EditSite::route('/{record}/edit'),
        ];
    }
}
