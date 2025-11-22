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
            'site_name' => $site?->site_name ?? auth()->user()->name,
            'tagline' => $site?->tagline,
            'email' => $site?->email ?? auth()->user()->email,
            'phone' => $site?->phone,
            'address' => $site?->address,
            'city' => $site?->city,
            'state' => $site?->state,
            'zip' => $site?->zip,
            'bio' => $site?->bio,
            'template_id' => $site?->template_id,
            'primary_color' => $site?->primary_color ?? '#3B82F6',
            'logo_path' => $site?->logo_path,
            'facebook' => $site?->facebook,
            'instagram' => $site?->instagram,
            'linkedin' => $site?->linkedin,
            'twitter' => $site?->twitter,
            'meta_title' => $site?->meta_title,
            'meta_description' => $site?->meta_description,
            'is_published' => $site?->is_published ?? false,
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
                                Forms\Components\TextInput::make('site_name')
                                    ->label('Business Name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('tagline')
                                    ->maxLength(500)
                                    ->placeholder('Your trusted real estate partner'),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required(),
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('address')
                                    ->maxLength(255),
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('city')
                                            ->maxLength(100),
                                        Forms\Components\TextInput::make('state')
                                            ->maxLength(100),
                                        Forms\Components\TextInput::make('zip')
                                            ->label('ZIP Code')
                                            ->maxLength(20),
                                    ]),
                                Forms\Components\Textarea::make('bio')
                                    ->label('About Me / Bio')
                                    ->rows(5)
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
                                Forms\Components\FileUpload::make('logo_path')
                                    ->label('Logo')
                                    ->image()
                                    ->directory('logos')
                                    ->visibility('public')
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Social Links')
                            ->schema([
                                Forms\Components\TextInput::make('facebook')
                                    ->label('Facebook URL')
                                    ->url()
                                    ->maxLength(500),
                                Forms\Components\TextInput::make('instagram')
                                    ->label('Instagram URL')
                                    ->url()
                                    ->maxLength(500),
                                Forms\Components\TextInput::make('linkedin')
                                    ->label('LinkedIn URL')
                                    ->url()
                                    ->maxLength(500),
                                Forms\Components\TextInput::make('twitter')
                                    ->label('Twitter/X URL')
                                    ->url()
                                    ->maxLength(500),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('SEO')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->label('Meta Title')
                                    ->maxLength(255)
                                    ->helperText('Recommended: 50-60 characters'),
                                Forms\Components\Textarea::make('meta_description')
                                    ->label('Meta Description')
                                    ->rows(3)
                                    ->helperText('Recommended: 150-160 characters'),
                            ]),

                        Forms\Components\Tabs\Tab::make('Publishing')
                            ->schema([
                                Forms\Components\Toggle::make('is_published')
                                    ->label('Publish Website')
                                    ->helperText('Make your website visible to the public'),
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
            'template_id' => $data['template_id'],
            'primary_color' => $data['primary_color'],
            'logo_path' => $data['logo_path'],
            'facebook' => $data['facebook'],
            'instagram' => $data['instagram'],
            'linkedin' => $data['linkedin'],
            'twitter' => $data['twitter'],
            'meta_title' => $data['meta_title'],
            'meta_description' => $data['meta_description'],
            'is_published' => $data['is_published'],
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
