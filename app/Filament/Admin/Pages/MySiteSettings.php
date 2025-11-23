<?php

namespace App\Filament\Admin\Pages;

use App\Models\Site;
use App\Models\Template;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class MySiteSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.admin.pages.my-site-settings';

    protected static ?string $navigationGroup = 'My Site';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Site Settings';

    protected static ?string $navigationLabel = 'Site Settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        // Only tenant users can access this page
        return auth()->user()?->isTenant() ?? false;
    }

    public function mount(): void
    {
        $site = $this->getSite();

        $this->form->fill([
            'site_name' => $site?->site_name ?? auth()->user()->name,
            'tagline' => $site?->tagline,
            'email' => $site?->email ?? auth()->user()->email,
            'phone' => $site?->phone,
            'address' => $site?->address,
            'city' => $site?->city,
            'state' => $site?->state,
            'zip' => $site?->zip,
            'bio' => $site?->bio,
            'license_number' => $site?->license_number,
            'brokerage' => $site?->brokerage,
            'years_experience' => $site?->years_experience,
            'specialties' => $site?->specialties,
            'template_id' => $site?->template_id,
            'primary_color' => $site?->primary_color ?? '#3B82F6',
            'logo_path' => $site?->logo_path,
            'headshot' => $site?->headshot,
            'hero_image' => $site?->hero_image,
            'facebook' => $site?->facebook,
            'instagram' => $site?->instagram,
            'linkedin' => $site?->linkedin,
            'twitter' => $site?->twitter,
            'youtube' => $site?->youtube,
            'meta_title' => $site?->meta_title,
            'meta_description' => $site?->meta_description,
            'is_published' => $site?->is_published ?? false,
            'stat_properties_sold' => $site?->stat_properties_sold,
            'stat_sales_volume' => $site?->stat_sales_volume,
            'stat_happy_clients' => $site?->stat_happy_clients,
            'stat_average_rating' => $site?->stat_average_rating,
            'stat_properties_sold_label' => $site?->stat_properties_sold_label,
            'stat_sales_volume_label' => $site?->stat_sales_volume_label,
            'stat_happy_clients_label' => $site?->stat_happy_clients_label,
            'stat_average_rating_label' => $site?->stat_average_rating_label,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Business Info')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                Forms\Components\TextInput::make('site_name')
                                    ->label('Business Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('John Smith Realty'),
                                Forms\Components\TextInput::make('tagline')
                                    ->maxLength(500)
                                    ->placeholder('Your Dream Home Awaits'),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->placeholder('john@example.com'),
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(50)
                                    ->placeholder('(555) 123-4567'),
                                Forms\Components\TextInput::make('address')
                                    ->maxLength(255)
                                    ->placeholder('123 Main Street'),
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('city')
                                            ->maxLength(100)
                                            ->placeholder('Los Angeles'),
                                        Forms\Components\TextInput::make('state')
                                            ->maxLength(100)
                                            ->placeholder('CA'),
                                        Forms\Components\TextInput::make('zip')
                                            ->label('ZIP Code')
                                            ->maxLength(20)
                                            ->placeholder('90210'),
                                    ]),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Professional Info')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                Forms\Components\RichEditor::make('bio')
                                    ->label('About Me / Bio')
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'bulletList',
                                        'orderedList',
                                    ])
                                    ->columnSpanFull()
                                    ->helperText('Tell your story. What makes you the best choice for your clients?'),
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
                                    ->maxValue(99)
                                    ->placeholder('15'),
                                Forms\Components\TextInput::make('specialties')
                                    ->label('Specialties')
                                    ->maxLength(500)
                                    ->placeholder('Luxury Homes, First-Time Buyers, Investment Properties')
                                    ->helperText('Comma-separated list of your specializations'),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Stats')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Forms\Components\Placeholder::make('stats_info')
                                    ->label('')
                                    ->content('These statistics are displayed on your homepage to showcase your achievements.')
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('stat_properties_sold')
                                    ->label('Properties Sold')
                                    ->numeric()
                                    ->minValue(0)
                                    ->placeholder('150')
                                    ->helperText('Total number of properties sold'),
                                Forms\Components\TextInput::make('stat_properties_sold_label')
                                    ->label('Custom Label')
                                    ->maxLength(100)
                                    ->placeholder('Properties Sold'),
                                Forms\Components\TextInput::make('stat_sales_volume')
                                    ->label('Sales Volume ($M)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->placeholder('50')
                                    ->helperText('Total sales volume in millions'),
                                Forms\Components\TextInput::make('stat_sales_volume_label')
                                    ->label('Custom Label')
                                    ->maxLength(100)
                                    ->placeholder('Sales Volume'),
                                Forms\Components\TextInput::make('stat_happy_clients')
                                    ->label('Happy Clients')
                                    ->numeric()
                                    ->minValue(0)
                                    ->placeholder('200')
                                    ->helperText('Number of satisfied clients'),
                                Forms\Components\TextInput::make('stat_happy_clients_label')
                                    ->label('Custom Label')
                                    ->maxLength(100)
                                    ->placeholder('Happy Clients'),
                                Forms\Components\TextInput::make('stat_average_rating')
                                    ->label('Average Rating')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(5)
                                    ->step(0.1)
                                    ->placeholder('4.9')
                                    ->helperText('Average client rating (0-5)'),
                                Forms\Components\TextInput::make('stat_average_rating_label')
                                    ->label('Custom Label')
                                    ->maxLength(100)
                                    ->placeholder('Star Rating'),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Images')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\FileUpload::make('headshot')
                                    ->label('Professional Headshot')
                                    ->image()
                                    ->directory('headshots')
                                    ->visibility('public')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('1:1')
                                    ->imageResizeTargetWidth('600')
                                    ->imageResizeTargetHeight('600')
                                    ->helperText('A professional photo of yourself. Square aspect ratio recommended.'),
                                Forms\Components\FileUpload::make('hero_image')
                                    ->label('Hero Background Image')
                                    ->image()
                                    ->directory('hero-images')
                                    ->visibility('public')
                                    ->imageResizeMode('cover')
                                    ->imageResizeTargetWidth('1920')
                                    ->imageResizeTargetHeight('1080')
                                    ->helperText('Large banner image for your homepage. Landscape orientation recommended.'),
                                Forms\Components\FileUpload::make('logo_path')
                                    ->label('Logo')
                                    ->image()
                                    ->directory('logos')
                                    ->visibility('public')
                                    ->helperText('Your business logo. PNG with transparent background recommended.'),
                            ])->columns(1),

                        Forms\Components\Tabs\Tab::make('Appearance')
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Forms\Components\Select::make('template_id')
                                    ->label('Website Template')
                                    ->options(Template::where('is_active', true)->pluck('name', 'id'))
                                    ->required()
                                    ->native(false)
                                    ->helperText('Choose a design that matches your brand'),
                                Forms\Components\ColorPicker::make('primary_color')
                                    ->label('Primary Brand Color')
                                    ->helperText('This color will be used throughout your website'),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Social Links')
                            ->icon('heroicon-o-share')
                            ->schema([
                                Forms\Components\TextInput::make('facebook')
                                    ->label('Facebook URL')
                                    ->url()
                                    ->maxLength(500)
                                    ->prefix('https://')
                                    ->placeholder('facebook.com/yourpage'),
                                Forms\Components\TextInput::make('instagram')
                                    ->label('Instagram URL')
                                    ->url()
                                    ->maxLength(500)
                                    ->prefix('https://')
                                    ->placeholder('instagram.com/yourhandle'),
                                Forms\Components\TextInput::make('linkedin')
                                    ->label('LinkedIn URL')
                                    ->url()
                                    ->maxLength(500)
                                    ->prefix('https://')
                                    ->placeholder('linkedin.com/in/yourprofile'),
                                Forms\Components\TextInput::make('twitter')
                                    ->label('Twitter/X URL')
                                    ->url()
                                    ->maxLength(500)
                                    ->prefix('https://')
                                    ->placeholder('twitter.com/yourhandle'),
                                Forms\Components\TextInput::make('youtube')
                                    ->label('YouTube URL')
                                    ->url()
                                    ->maxLength(500)
                                    ->prefix('https://')
                                    ->placeholder('youtube.com/@yourchannel'),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->label('Meta Title')
                                    ->maxLength(60)
                                    ->helperText('Recommended: 50-60 characters. This appears in search results.')
                                    ->placeholder('John Smith | Top Los Angeles Real Estate Agent'),
                                Forms\Components\Textarea::make('meta_description')
                                    ->label('Meta Description')
                                    ->rows(3)
                                    ->maxLength(160)
                                    ->helperText('Recommended: 150-160 characters. Describe your services.')
                                    ->placeholder('Award-winning Los Angeles real estate agent with 15+ years of experience...'),
                            ]),

                        Forms\Components\Tabs\Tab::make('Publishing')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Forms\Components\Toggle::make('is_published')
                                    ->label('Publish Website')
                                    ->helperText('Make your website visible to the public')
                                    ->onColor('success')
                                    ->offColor('danger'),
                            ]),
                    ])->columnSpanFull(),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $site = $this->getSite() ?? new Site(['user_id' => auth()->id()]);

        $site->fill([
            'site_name' => $data['site_name'],
            'tagline' => $data['tagline'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'city' => $data['city'],
            'state' => $data['state'],
            'zip' => $data['zip'],
            'bio' => $data['bio'],
            'license_number' => $data['license_number'],
            'brokerage' => $data['brokerage'],
            'years_experience' => $data['years_experience'],
            'specialties' => $data['specialties'],
            'template_id' => $data['template_id'],
            'primary_color' => $data['primary_color'],
            'logo_path' => $data['logo_path'],
            'headshot' => $data['headshot'],
            'hero_image' => $data['hero_image'],
            'facebook' => $data['facebook'],
            'instagram' => $data['instagram'],
            'linkedin' => $data['linkedin'],
            'twitter' => $data['twitter'],
            'youtube' => $data['youtube'],
            'meta_title' => $data['meta_title'],
            'meta_description' => $data['meta_description'],
            'is_published' => $data['is_published'],
            'stat_properties_sold' => $data['stat_properties_sold'],
            'stat_sales_volume' => $data['stat_sales_volume'],
            'stat_happy_clients' => $data['stat_happy_clients'],
            'stat_average_rating' => $data['stat_average_rating'],
            'stat_properties_sold_label' => $data['stat_properties_sold_label'],
            'stat_sales_volume_label' => $data['stat_sales_volume_label'],
            'stat_happy_clients_label' => $data['stat_happy_clients_label'],
            'stat_average_rating_label' => $data['stat_average_rating_label'],
        ]);

        $site->save();

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }

    protected function getSite(): ?Site
    {
        return Site::where('user_id', auth()->id())->first();
    }
}
