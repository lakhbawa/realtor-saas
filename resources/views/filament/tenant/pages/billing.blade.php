<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Subscription Status Card --}}
        <x-filament::section>
            <x-slot name="heading">
                Current Plan
            </x-slot>

            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold">Realtor Pro Plan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">$39/month</p>
                </div>
                <div>
                    <x-filament::badge :color="$this->getSubscriptionStatusColor()">
                        {{ ucfirst($this->getSubscriptionStatus()) }}
                    </x-filament::badge>
                </div>
            </div>

            @if($this->getTrialEndsAt())
                <div class="mt-4 p-4 bg-warning-50 dark:bg-warning-900/20 rounded-lg">
                    <p class="text-sm text-warning-700 dark:text-warning-400">
                        <x-heroicon-s-clock class="w-4 h-4 inline mr-1" />
                        Your trial ends on {{ $this->getTrialEndsAt() }}
                    </p>
                </div>
            @endif
        </x-filament::section>

        {{-- Features Card --}}
        <x-filament::section>
            <x-slot name="heading">
                Plan Features
            </x-slot>

            <ul class="space-y-3">
                <li class="flex items-center gap-2">
                    <x-heroicon-s-check-circle class="w-5 h-5 text-success-500" />
                    <span>Custom subdomain website</span>
                </li>
                <li class="flex items-center gap-2">
                    <x-heroicon-s-check-circle class="w-5 h-5 text-success-500" />
                    <span>Unlimited property listings</span>
                </li>
                <li class="flex items-center gap-2">
                    <x-heroicon-s-check-circle class="w-5 h-5 text-success-500" />
                    <span>Contact form with email notifications</span>
                </li>
                <li class="flex items-center gap-2">
                    <x-heroicon-s-check-circle class="w-5 h-5 text-success-500" />
                    <span>Client testimonials</span>
                </li>
                <li class="flex items-center gap-2">
                    <x-heroicon-s-check-circle class="w-5 h-5 text-success-500" />
                    <span>3 professional templates</span>
                </li>
                <li class="flex items-center gap-2">
                    <x-heroicon-s-check-circle class="w-5 h-5 text-success-500" />
                    <span>SEO optimization</span>
                </li>
                <li class="flex items-center gap-2">
                    <x-heroicon-s-check-circle class="w-5 h-5 text-success-500" />
                    <span>Analytics dashboard</span>
                </li>
            </ul>
        </x-filament::section>

        {{-- Billing Actions --}}
        @if($this->hasActiveSubscription() && auth()->user()->stripe_subscription_id)
            <x-filament::section>
                <x-slot name="heading">
                    Cancel Subscription
                </x-slot>

                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    If you cancel, your subscription will remain active until the end of your current billing period.
                    Your website will become inactive after that date.
                </p>

                <x-filament::button
                    color="danger"
                    wire:click="cancelSubscription"
                    wire:confirm="Are you sure you want to cancel your subscription?"
                >
                    Cancel Subscription
                </x-filament::button>
            </x-filament::section>
        @endif

        {{-- Success/Cancel Messages --}}
        @if(request()->has('success'))
            <x-filament::section>
                <div class="p-4 bg-success-50 dark:bg-success-900/20 rounded-lg">
                    <p class="text-success-700 dark:text-success-400">
                        <x-heroicon-s-check-circle class="w-5 h-5 inline mr-2" />
                        Your subscription has been activated successfully!
                    </p>
                </div>
            </x-filament::section>
        @endif

        @if(request()->has('canceled'))
            <x-filament::section>
                <div class="p-4 bg-warning-50 dark:bg-warning-900/20 rounded-lg">
                    <p class="text-warning-700 dark:text-warning-400">
                        <x-heroicon-s-exclamation-triangle class="w-5 h-5 inline mr-2" />
                        Checkout was canceled. No charges were made.
                    </p>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
