<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                @php
                    $role = auth()->check() ? auth()->user()->role : null;
                    $firstChild = ($role === 'parent' && auth()->check()) ? auth()->user()->children()->first() : null;
                    $unreadMessageCount = auth()->check() ? \App\Models\Message::where('receiver_id', auth()->id())->whereNull('read_at')->count() : 0;
                    $pendingPaymentsBadge = ($role === 'manager') ? \App\Models\Invoice::where('payment_status', 'payment_submitted')->count() : 0;
                @endphp

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if ($role === 'parent')
                        <x-nav-link :href="route('parent.dashboard')" :active="request()->routeIs('parent.dashboard')">
                            {{ __('common.dashboard') }}
                        </x-nav-link>

                        <x-nav-link :href="route('parent.children.index')" :active="request()->routeIs('parent.children.*')">
                            {{ __('common.my_children') }}
                        </x-nav-link>

                        <x-nav-link :href="route('parent.incidents.index')" :active="request()->routeIs('parent.incidents.*')">
                            {{ __('common.incidents') }}
                        </x-nav-link>

                        <x-nav-link :href="route('parent.invoices.index')" :active="request()->routeIs('parent.invoices.*')">
                            {{ __('common.invoices') }}
                        </x-nav-link>

                        @if($firstChild)
                            <x-nav-link :href="route('parent.milestones.show', $firstChild)" :active="request()->routeIs('parent.milestones.*')">
                                {{ __('common.milestones') }}
                            </x-nav-link>
                        @endif

                        <x-nav-link :href="route('parent.messages.index')" :active="request()->routeIs('parent.messages.*')">
                            {{ __('common.messages') }}
                            @if($unreadMessageCount > 0)
                                <span class="ml-1 inline-flex items-center justify-center rounded-full bg-red-500 text-white text-xs font-bold w-5 h-5">
                                    {{ $unreadMessageCount > 99 ? '99+' : $unreadMessageCount }}
                                </span>
                            @endif
                        </x-nav-link>

                    @elseif ($role === 'carer')
                        <x-nav-link :href="route('carer.dashboard')" :active="request()->routeIs('carer.dashboard')">
                            {{ __('common.dashboard') }}
                        </x-nav-link>

                        <x-nav-link :href="route('carer.milestones.index')" :active="request()->routeIs('carer.milestones.*')">
                            {{ __('common.milestones') }}
                        </x-nav-link>

                        <x-nav-link :href="route('carer.messages.index')" :active="request()->routeIs('carer.messages.*')">
                            {{ __('common.messages') }}
                            @if($unreadMessageCount > 0)
                                <span class="ml-1 inline-flex items-center justify-center rounded-full bg-red-500 text-white text-xs font-bold w-5 h-5">
                                    {{ $unreadMessageCount > 99 ? '99+' : $unreadMessageCount }}
                                </span>
                            @endif
                        </x-nav-link>

                    @elseif ($role === 'manager')
                        <x-nav-link :href="route('manager.dashboard')" :active="request()->routeIs('manager.dashboard')">
                            {{ __('common.dashboard') }}
                        </x-nav-link>

                        <x-nav-link :href="route('manager.children.index')" :active="request()->routeIs('manager.children.*')">
                            {{ __('common.children') }}
                        </x-nav-link>

                        <x-nav-link :href="route('manager.parents.index')" :active="request()->routeIs('manager.parents.*')">
                            {{ __('common.parents') }}
                        </x-nav-link>

                        <x-nav-link :href="route('manager.carers.index')" :active="request()->routeIs('manager.carers.*')">
                            {{ __('common.carers') }}
                        </x-nav-link>

                        <x-nav-link :href="route('manager.invoices.index')" :active="request()->routeIs('manager.invoices.*')">
                            {{ __('common.invoices') }}
                            @if($pendingPaymentsBadge > 0)
                                <span class="ml-1 inline-flex items-center justify-center rounded-full bg-amber-500 text-white text-xs font-bold w-5 h-5">
                                    {{ $pendingPaymentsBadge }}
                                </span>
                            @endif
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-4">

            <!-- 🌍 Language Switcher -->
            <form method="POST" action="{{ route('locale.switch') }}">
                @csrf
                <select
                    name="locale"
                    onchange="this.form.submit()"
                    class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>
                        {{ __('common.english') }}
                    </option>
                    <option value="pt" {{ app()->getLocale() === 'pt' ? 'selected' : '' }}>
                        {{ __('common.portuguese') }}
                    </option>
                    <option value="pl" {{ app()->getLocale() === 'pl' ? 'selected' : '' }}>
                        {{ __('common.polish') }}
                    </option>
                    <option value="ro" {{ app()->getLocale() === 'ro' ? 'selected' : '' }}>
                            {{ __('common.romanian') }}
                    </option>
                </select>
            </form>


            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('common.profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('common.log_out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if ($role === 'parent')
                @if($firstChild)
                    <x-responsive-nav-link :href="route('parent.milestones.show', $firstChild)" :active="request()->routeIs('parent.milestones.*')">
                        {{ __('common.milestones') }}
                    </x-responsive-nav-link>
                @endif

                <x-responsive-nav-link :href="route('parent.invoices.index')" :active="request()->routeIs('parent.invoices.*')">
                    {{ __('common.invoices') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('parent.incidents.index')" :active="request()->routeIs('parent.incidents.*')">
                    {{ __('common.incidents') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('parent.messages.index')" :active="request()->routeIs('parent.messages.*')">
                    {{ __('common.messages') }}
                    @if($unreadMessageCount > 0)
                        <span class="ml-1 inline-flex items-center justify-center rounded-full bg-red-500 text-white text-xs font-bold w-5 h-5">
                            {{ $unreadMessageCount > 99 ? '99+' : $unreadMessageCount }}
                        </span>
                    @endif
                </x-responsive-nav-link>

            @elseif ($role === 'carer')
                <x-responsive-nav-link :href="route('carer.milestones.index')" :active="request()->routeIs('carer.milestones.*')">
                    {{ __('common.milestones') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('carer.messages.index')" :active="request()->routeIs('carer.messages.*')">
                    {{ __('common.messages') }}
                    @if($unreadMessageCount > 0)
                        <span class="ml-1 inline-flex items-center justify-center rounded-full bg-red-500 text-white text-xs font-bold w-5 h-5">
                            {{ $unreadMessageCount > 99 ? '99+' : $unreadMessageCount }}
                        </span>
                    @endif
                </x-responsive-nav-link>

            @elseif ($role === 'manager')
                <x-responsive-nav-link :href="route('manager.children.index')" :active="request()->routeIs('manager.children.*')">
                    {{ __('common.children') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('manager.parents.index')" :active="request()->routeIs('manager.parents.*')">
                    {{ __('common.parents') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('manager.carers.index')" :active="request()->routeIs('manager.carers.*')">
                    {{ __('common.carers') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('manager.invoices.index')" :active="request()->routeIs('manager.invoices.*')">
                    {{ __('common.invoices') }}
                    @if($pendingPaymentsBadge > 0)
                        <span class="ml-1 inline-flex items-center justify-center rounded-full bg-amber-500 text-white text-xs font-bold w-5 h-5">
                            {{ $pendingPaymentsBadge }}
                        </span>
                    @endif
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('common.profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('common.log_out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>