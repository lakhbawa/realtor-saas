<x-filament-panels::page>
    {{-- Success/Cancel Messages --}}
    @if(request('success'))
        <x-filament::section>
            <div class="flex items-center gap-3 text-success-600 dark:text-success-400">
                <x-heroicon-o-check-circle class="w-6 h-6" />
                <span class="font-medium">Payment successful! Your subscription is now active.</span>
            </div>
        </x-filament::section>
    @endif

    @if(request('canceled'))
        <x-filament::section>
            <div class="flex items-center gap-3 text-warning-600 dark:text-warning-400">
                <x-heroicon-o-exclamation-circle class="w-6 h-6" />
                <span class="font-medium">Checkout was canceled. You can try again when you're ready.</span>
            </div>
        </x-filament::section>
    @endif

    {{-- Current Subscription Status --}}
    @php
        $subscription = $this->getSubscription();
        $currentPlan = $this->getCurrentPlan();
        $status = $this->getSubscriptionStatus();
    @endphp

    <x-filament::section>
        <x-slot name="heading">Current Subscription</x-slot>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                <x-filament::badge :color="$this->getSubscriptionStatusColor()">
                    {{ ucfirst($status) }}
                </x-filament::badge>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Plan</p>
                <p class="text-lg font-semibold">
                    {{ $currentPlan?->name ?? 'No Plan' }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Billing Cycle</p>
                <p class="text-lg font-semibold">
                    {{ $subscription?->formatted_billing_cycle ?? '-' }}
                </p>
            </div>

            @if($subscription?->current_period_end)
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $subscription->canceled_at ? 'Access Until' : 'Next Billing Date' }}
                    </p>
                    <p class="text-lg font-semibold">
                        {{ $subscription->current_period_end->format('M j, Y') }}
                    </p>
                </div>
            @endif

            @if($subscription?->onTrial())
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Trial Ends</p>
                    <p class="text-lg font-semibold text-info-600">
                        {{ $subscription->trial_ends_at->format('M j, Y') }}
                    </p>
                </div>
            @endif
        </div>

        @if($subscription?->canceled_at && $subscription?->ends_at?->isFuture())
            <div class="mt-4 p-4 bg-warning-50 dark:bg-warning-950 rounded-lg">
                <p class="text-warning-700 dark:text-warning-300">
                    Your subscription is scheduled to cancel on {{ $subscription->ends_at->format('M j, Y') }}.
                    <button wire:click="resumeSubscription" class="underline font-medium hover:no-underline">
                        Resume subscription
                    </button>
                </p>
            </div>
        @endif

        @if($this->hasActiveSubscription() && !$subscription?->canceled_at)
            <div class="mt-4 flex gap-3">
                <x-filament::button
                    color="gray"
                    wire:click="redirectToCustomerPortal"
                >
                    Manage Billing
                </x-filament::button>

                <x-filament::button
                    color="danger"
                    outlined
                    wire:click="cancelSubscription"
                    wire:confirm="Are you sure you want to cancel your subscription? You will retain access until the end of your billing period."
                >
                    Cancel Subscription
                </x-filament::button>
            </div>
        @endif
    </x-filament::section>

    {{-- Available Plans --}}
    <x-filament::section>
        <x-slot name="heading">
            {{ $this->hasActiveSubscription() ? 'Change Plan' : 'Choose a Plan' }}
        </x-slot>

        {{-- Billing Cycle Toggle --}}
        <div class="flex justify-center mb-8">
            <div class="inline-flex rounded-lg bg-gray-100 dark:bg-gray-800 p-1">
                <button
                    wire:click="$set('selectedBillingCycle', 'monthly')"
                    class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $selectedBillingCycle === 'monthly' ? 'bg-white dark:bg-gray-700 shadow' : 'text-gray-500 hover:text-gray-700' }}"
                >
                    Monthly
                </button>
                <button
                    wire:click="$set('selectedBillingCycle', 'quarterly')"
                    class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $selectedBillingCycle === 'quarterly' ? 'bg-white dark:bg-gray-700 shadow' : 'text-gray-500 hover:text-gray-700' }}"
                >
                    Quarterly
                </button>
                <button
                    wire:click="$set('selectedBillingCycle', 'annual')"
                    class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $selectedBillingCycle === 'annual' ? 'bg-white dark:bg-gray-700 shadow' : 'text-gray-500 hover:text-gray-700' }}"
                >
                    Annual
                    @php
                        $firstPlan = $this->getPlans()->first();
                    @endphp
                    @if($firstPlan && $firstPlan->annual_savings_percent > 0)
                        <span class="ml-1 text-xs text-success-600">Save {{ $firstPlan->annual_savings_percent }}%</span>
                    @endif
                </button>
            </div>
        </div>

        {{-- Plans Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($this->getPlans() as $plan)
                @php
                    $isCurrentPlan = $currentPlan?->id === $plan->id && $subscription?->billing_cycle === $selectedBillingCycle;
                    $price = match($selectedBillingCycle) {
                        'monthly' => $plan->formatted_monthly_price,
                        'quarterly' => $plan->formatted_quarterly_price,
                        'annual' => $plan->formatted_annual_price,
                        default => $plan->formatted_monthly_price,
                    };
                    $perMonth = match($selectedBillingCycle) {
                        'quarterly' => $plan->quarterly_monthly_equivalent,
                        'annual' => $plan->annual_monthly_equivalent,
                        default => null,
                    };
                @endphp

                <div class="relative rounded-xl border-2 p-6 {{ $plan->is_featured ? 'border-primary-500 ring-2 ring-primary-500/20' : 'border-gray-200 dark:border-gray-700' }} {{ $isCurrentPlan ? 'bg-primary-50 dark:bg-primary-950' : '' }}">
                    @if($plan->is_featured)
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                            <span class="bg-primary-500 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                Most Popular
                            </span>
                        </div>
                    @endif

                    @if($isCurrentPlan)
                        <div class="absolute -top-3 right-4">
                            <span class="bg-success-500 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                Current Plan
                            </span>
                        </div>
                    @endif

                    <h3 class="text-xl font-bold">{{ $plan->name }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">{{ $plan->description }}</p>

                    <div class="mt-4">
                        <span class="text-4xl font-bold">{{ $price }}</span>
                        <span class="text-gray-500">/{{ $selectedBillingCycle === 'monthly' ? 'mo' : $selectedBillingCycle }}</span>

                        @if($perMonth)
                            <p class="text-sm text-gray-500 mt-1">{{ $perMonth }}/month</p>
                        @endif
                    </div>

                    @if($plan->trial_days > 0 && !$this->hasActiveSubscription())
                        <p class="text-sm text-success-600 mt-2">
                            {{ $plan->trial_days }}-day free trial
                        </p>
                    @endif

                    <ul class="mt-6 space-y-3">
                        @foreach($plan->features ?? [] as $feature)
                            <li class="flex items-start gap-2">
                                <x-heroicon-o-check class="w-5 h-5 text-success-500 flex-shrink-0 mt-0.5" />
                                <span class="text-sm">{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-6">
                        @if($isCurrentPlan)
                            <x-filament::button
                                class="w-full"
                                disabled
                            >
                                Current Plan
                            </x-filament::button>
                        @else
                            <x-filament::button
                                class="w-full"
                                :color="$plan->is_featured ? 'primary' : 'gray'"
                                wire:click="selectPlan({{ $plan->id }}, '{{ $selectedBillingCycle }}')"
                                wire:loading.attr="disabled"
                            >
                                {{ $this->hasActiveSubscription() ? 'Switch to ' . $plan->name : 'Get Started' }}
                            </x-filament::button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Subscribe Button --}}
        @if($selectedPlanId && (!$currentPlan || $currentPlan->id != $selectedPlanId || $subscription?->billing_cycle !== $selectedBillingCycle))
            <div class="mt-8 flex justify-center">
                <x-filament::button
                    size="lg"
                    wire:click="redirectToCheckout"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="redirectToCheckout">
                        {{ $this->hasActiveSubscription() ? 'Change Subscription' : 'Subscribe Now' }}
                    </span>
                    <span wire:loading wire:target="redirectToCheckout">
                        Redirecting to checkout...
                    </span>
                </x-filament::button>
            </div>
        @endif
    </x-filament::section>

    {{-- Plan Limits Info --}}
    @if($currentPlan)
        <x-filament::section collapsible collapsed>
            <x-slot name="heading">Your Plan Limits</x-slot>

            @php
                $limits = $currentPlan->limits ?? [];
            @endphp

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @if(isset($limits['max_properties']))
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <p class="text-2xl font-bold">{{ $limits['max_properties'] == -1 ? '∞' : $limits['max_properties'] }}</p>
                        <p class="text-sm text-gray-500">Properties</p>
                    </div>
                @endif

                @if(isset($limits['max_blog_posts']))
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <p class="text-2xl font-bold">{{ $limits['max_blog_posts'] == -1 ? '∞' : $limits['max_blog_posts'] }}</p>
                        <p class="text-sm text-gray-500">Blog Posts</p>
                    </div>
                @endif

                @if(isset($limits['max_pages']))
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <p class="text-2xl font-bold">{{ $limits['max_pages'] == -1 ? '∞' : $limits['max_pages'] }}</p>
                        <p class="text-sm text-gray-500">Custom Pages</p>
                    </div>
                @endif

                @if(isset($limits['max_testimonials']))
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <p class="text-2xl font-bold">{{ $limits['max_testimonials'] == -1 ? '∞' : $limits['max_testimonials'] }}</p>
                        <p class="text-sm text-gray-500">Testimonials</p>
                    </div>
                @endif
            </div>

            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="flex items-center gap-2">
                    @if($limits['can_use_custom_domain'] ?? false)
                        <x-heroicon-o-check-circle class="w-5 h-5 text-success-500" />
                    @else
                        <x-heroicon-o-x-circle class="w-5 h-5 text-gray-400" />
                    @endif
                    <span class="text-sm">Custom Domain</span>
                </div>

                <div class="flex items-center gap-2">
                    @if($limits['can_access_analytics'] ?? false)
                        <x-heroicon-o-check-circle class="w-5 h-5 text-success-500" />
                    @else
                        <x-heroicon-o-x-circle class="w-5 h-5 text-gray-400" />
                    @endif
                    <span class="text-sm">Analytics</span>
                </div>

                <div class="flex items-center gap-2">
                    @if($limits['can_remove_branding'] ?? false)
                        <x-heroicon-o-check-circle class="w-5 h-5 text-success-500" />
                    @else
                        <x-heroicon-o-x-circle class="w-5 h-5 text-gray-400" />
                    @endif
                    <span class="text-sm">Remove Branding</span>
                </div>

                <div class="flex items-center gap-2">
                    @if($limits['priority_support'] ?? false)
                        <x-heroicon-o-check-circle class="w-5 h-5 text-success-500" />
                    @else
                        <x-heroicon-o-x-circle class="w-5 h-5 text-gray-400" />
                    @endif
                    <span class="text-sm">Priority Support</span>
                </div>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
