<?php

namespace App\Filament\Tenant\Pages;

use App\Models\Site;
use App\Models\Template;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class SiteSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.tenant.pages.site-settings';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public function mount(): void
    {
        $site = $this->getSite();

        $this->form->fill([
            'business_name' => $site?->business_name ?? auth()->user()->name,
            'tagline' => $site?->tagline,
            'email' => $site?->email ?? auth()->user()->email,
            'phone' => $site?->phone,
            'address' => $site?->address,
            'license_number' => $site?->license_number,
            'bio' => $site?->bio,
            'template_id' => $site?->template_id,
            'primary_color' => $site?->primary_color ?? '#4F46E5',
            'secondary_color' => $site?->secondary_color ?? '#10B981',
            'facebook_url' => $site?->social_links['facebook'] ?? null,
            'instagram_url' => $site?->social_links['instagram'] ?? null,
            'linkedin_url' => $site?->social_links['linkedin'] ?? null,
            'twitter_url' => $site?->social_links['twitter'] ?? null,
            'youtube_url' => $site?->social_links['youtube'] ?? null,
            'meta_title' => $site?->seo_settings['meta_title'] ?? null,
            'meta_description' => $site?->seo_settings['meta_description'] ?? null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Business Info')
                            ->schema([
                                Forms\Components\TextInput::make('business_name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('tagline')
                                    ->maxLength(255)
                                    ->placeholder('Your trusted real estate partner'),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required(),
                                Forms\Components\TextInput::make('phone')
                                    ->tel(),
                                Forms\Components\TextInput::make('license_number')
                                    ->placeholder('DRE# 12345678'),
                                Forms\Components\Textarea::make('address')
                                    ->rows(2),
                                Forms\Components\RichEditor::make('bio')
                                    ->label('About Me / Bio')
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Appearance')
                            ->schema([
                                Forms\Components\Select::make('template_id')
                                    ->label('Website Template')
                                    ->options(Template::where('is_active', true)->pluck('name', 'id'))
                                    ->required()
                                    ->native(false),
                                Forms\Components\ColorPicker::make('primary_color')
                                    ->label('Primary Color'),
                                Forms\Components\ColorPicker::make('secondary_color')
                                    ->label('Secondary Color'),
                                Forms\Components\FileUpload::make('logo')
                                    ->image()
                                    ->directory('logos')
                                    ->visibility('public'),
                                Forms\Components\FileUpload::make('headshot')
                                    ->image()
                                    ->directory('headshots')
                                    ->visibility('public'),
                                Forms\Components\FileUpload::make('hero_image')
                                    ->image()
                                    ->directory('heroes')
                                    ->visibility('public'),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Social Links')
                            ->schema([
                                Forms\Components\TextInput::make('facebook_url')
                                    ->label('Facebook')
                                    ->url()
                                    ->prefix('https://'),
                                Forms\Components\TextInput::make('instagram_url')
                                    ->label('Instagram')
                                    ->url()
                                    ->prefix('https://'),
                                Forms\Components\TextInput::make('linkedin_url')
                                    ->label('LinkedIn')
                                    ->url()
                                    ->prefix('https://'),
                                Forms\Components\TextInput::make('twitter_url')
                                    ->label('Twitter/X')
                                    ->url()
                                    ->prefix('https://'),
                                Forms\Components\TextInput::make('youtube_url')
                                    ->label('YouTube')
                                    ->url()
                                    ->prefix('https://'),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('SEO')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->label('Meta Title')
                                    ->maxLength(60)
                                    ->helperText('Recommended: 50-60 characters'),
                                Forms\Components\Textarea::make('meta_description')
                                    ->label('Meta Description')
                                    ->maxLength(160)
                                    ->rows(3)
                                    ->helperText('Recommended: 150-160 characters'),
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
            'business_name' => $data['business_name'],
            'tagline' => $data['tagline'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'license_number' => $data['license_number'],
            'bio' => $data['bio'],
            'template_id' => $data['template_id'],
            'primary_color' => $data['primary_color'],
            'secondary_color' => $data['secondary_color'],
            'social_links' => [
                'facebook' => $data['facebook_url'],
                'instagram' => $data['instagram_url'],
                'linkedin' => $data['linkedin_url'],
                'twitter' => $data['twitter_url'],
                'youtube' => $data['youtube_url'],
            ],
            'seo_settings' => [
                'meta_title' => $data['meta_title'],
                'meta_description' => $data['meta_description'],
            ],
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
