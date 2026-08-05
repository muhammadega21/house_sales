<header
    class="sticky top-0 z-30 flex min-h-16 w-full items-center justify-between border-b border-gray-200 bg-white px-4 md:px-6 shadow-sm">
    <!-- Sidebar Toggle Button & Search Bar -->
    <div class="flex items-center gap-4">
        <button @click="sidebarOpen = true" class="text-gray-500 hover:text-gray-700 focus:outline-none lg:hidden">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Global Search Input -->
        <div class="relative hidden sm:block w-64 md:w-80">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input type="text" placeholder="Cari unit, konsumen, booking..."
                class="w-full rounded-lg border border-gray-300 bg-gray-50 py-1.5 pl-10 pr-4 text-sm text-gray-800 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 transition">
        </div>
    </div>

    <!-- Right Profile & Notification Controls -->
    <div class="flex items-center gap-4">
        <!-- Notification Dropdown -->
        <div class="relative" x-data="{ open: false }">
            @php
                $notifications = [];
                $unreadCount = 0;
                if (auth()->check() && optional(auth()->user()->role)->value === 'marketing') {
                    $notifications = auth()->user()->marketingNotifications()->latest()->limit(5)->get();
                    $unreadCount = auth()->user()->marketingNotifications()->where('is_read', false)->count();
                }
            @endphp
            <button @click="open = !open"
                class="relative rounded-full p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none transition">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                @if ($unreadCount > 0)
                    <span
                        class="absolute top-1 right-1 flex h-3.5 w-3.5 items-center justify-center rounded-full bg-red-500 text-[10px] text-white">{{ $unreadCount }}</span>
                @endif
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" @click.away="open = false" x-transition
                class="absolute right-0 mt-2 w-80 rounded-lg border border-gray-200 bg-white py-2 shadow-lg z-50">
                <div class="px-4 py-2 border-b border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Notifikasi</h3>
                </div>
                <div class="max-h-64 overflow-y-auto divide-y divide-gray-100">
                    @forelse($notifications as $notification)
                        <div class="block px-4 py-3 hover:bg-gray-50 transition">
                            <p class="text-sm font-semibold text-gray-800">{{ $notification->title }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $notification->message }}</p>
                            <span
                                class="text-[10px] text-gray-400">{{ $notification->created_at?->diffForHumans() }}</span>
                        </div>
                    @empty
                        <div class="px-4 py-4 text-sm text-gray-500">Belum ada notifikasi terbaru.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- User Profile Dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-3 focus:outline-none">
                <div
                    class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-600 font-bold text-white shadow">
                    {{ strtoupper(substr(auth()->user()->nama_lengkap ?? 'U', 0, 1)) }}
                </div>
                <div class="hidden md:block text-left">
                    <p class="text-sm font-semibold text-gray-800 leading-tight">
                        {{ auth()->user()->nama_lengkap ?? 'User' }}</p>
                    <p class="text-xs text-gray-500 capitalize">
                        {{ is_object(auth()->user()?->role) ? auth()->user()->role->value : auth()->user()?->role }}</p>
                </div>
                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" @click.away="open = false" x-transition
                class="absolute right-0 mt-2 w-48 rounded-lg border border-gray-200 bg-white py-1 shadow-lg z-50">
                <div class="px-4 py-2 border-b border-gray-100 md:hidden">
                    <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->nama_lengkap ?? 'User' }}</p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Keluar / Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
