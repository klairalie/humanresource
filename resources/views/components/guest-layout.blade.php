<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>WELCOME TO HR DASHBOARD</title>

    @vite(['resources/css/app.css',
    'resources/js/face-api.min.js',
    'resources/js/cam.js'])

    <!-- Alpine.js -->
    <script src="//unpkg.com/alpinejs" defer></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Prevent x-cloak flash -->
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;600&display=swap" rel="stylesheet">
</head>

<body class="text-black h-full overflow-hidden bg-[#F4F7FA]" x-data="{ sidebarOpen: false }">

   <!-- Navbar -->
<nav
   class="fixed top-0 left-0 right-0 z-40 bg-[#2C2C2C] backdrop-blur-md px-6 py-3 text-white flex justify-between items-center shadow-md">

    <!-- Left: Logo -->
    <a href="{{ route('show.dashboard') }}">
        <div>
            <img src="{{ url('/3Rs_logo.png') }}" alt="company logo" class="h-10 w-auto">
        </div>
    </a>

    <!-- Right: Notification + Profile -->
<!-- Right: Notification + Profile -->
<div class="flex items-center space-x-4">
    @php
        $notifications = $notifications ?? collect();
    @endphp

    <!-- Notification Bell Dropdown -->
    <div 
        class="relative"
        x-data="{
            open: false,
            unreadCount: {{ $notifications->count() }},
            markAllAsRead() {
                fetch('{{ route('notifications.markAllRead') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                }).then(() => {
                    this.unreadCount = 0; 
                    this.open = false;    
                });
            }
        }"
    >
    @if (in_array(session('user_position'), ['Human resource manager']))
        <!-- Bell Button -->
        <button @click="open = !open" class="relative focus:outline-none">
            <i data-lucide="bell" class="w-6 h-6 text-white hover:text-gray-300"></i>

            <template x-if="unreadCount > 0">
                <span
                    class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5"
                    x-text="unreadCount"
                ></span>
            </template>
        </button>

        <!-- Dropdown Panel -->
        <div 
            x-cloak
            x-show="open" 
            @click.away="open = false" 
            x-transition
            class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-xl z-50 cloak:hidden"
        >
            <!-- Header -->
            <div class="p-3 border-b flex items-center gap-2 font-semibold text-gray-700">
                <i data-lucide="bell-ring" class="w-4 h-4 text-blue-600"></i>
                Notifications
            </div>

            <!-- Notification List -->
            <ul class="max-h-80 overflow-y-auto divide-y text-sm text-gray-700">
                @forelse($notifications as $notif)
                    <li>
                        <a href="{{ $notif['link'] }}"
                           class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition">
                            @switch($notif['type'])
                                @case('leave')
                                    <i data-lucide="calendar-days" class="w-5 h-5 text-blue-500 mt-1"></i>
                                    @break
                                @case('service')
                                    <i data-lucide="tool" class="w-5 h-5 text-green-500 mt-1"></i>
                                    @break
                                @case('overtime')
                                    <i data-lucide="clock-8" class="w-5 h-5 text-indigo-500 mt-1"></i>
                                    @break
                                @case('applicant')
                                    <i data-lucide="user-plus" class="w-5 h-5 text-yellow-500 mt-1"></i>
                                    @break
                                @case('queue')
                                    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500 mt-1"></i>
                                    @break
                            @endswitch

                            <div class="flex-1">
                                <p class="font-medium text-gray-800">{{ $notif['message'] }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ \Carbon\Carbon::parse($notif['time'])->diffForHumans() }}
                                </p>
                            </div>
                        </a>
                    </li>
                @empty
                    <li class="py-4 text-center text-gray-500 text-sm">
                        No new notifications
                    </li>
                @endforelse
            </ul>

            <!-- Footer -->
            @if($notifications->count() > 0)
                <div 
                    class="border-t text-center py-2 text-xs text-gray-500 hover:text-blue-600 cursor-pointer"
                    @click="markAllAsRead"
                >
                    Mark all as read
                </div>
            @endif
        </div>
    @endif
    </div>


    <!-- Profile Dropdown -->
    <div x-data="{ open: false }" class="relative">
        <div @click="open = !open" class="flex items-center space-x-2 cursor-pointer select-none">

            <!-- Profile Picture with Active Dot -->
            <div class="relative w-12 h-10 flex items-center justify-center rounded-full bg-orange-400 border text-white font-bold text-sm">
                {{ strtoupper(substr(session('user_email') ?? 'U', 0, 1)) }}
                <span class="absolute bottom-0 right-0 block w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
            </div>

            @if(session('acting_as'))
                <div class="text-sm text-gray-600">
                    Acting as {{ session('acting_as') }}
                </div>
            @endif

            <span class="font-medium">
                {{ session('user_email') ?? 'user@example.com' }} 
            </span>

            <i data-lucide="chevron-down" class="w-5 h-5 text-gray-500"></i>
        </div>

        <!-- Dropdown Menu -->
        <div x-show="open" x-cloak @click.away="open = false" x-transition
            class="absolute right-0 mt-2 w-44 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
            @if (in_array(session('user_position'), ['Human resource manager']))
                <a href="{{ route('settings.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                    <i data-lucide="settings" class="w-5 h-5 inline mr-2 text-gray-500"></i> Settings
                </a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                    <i data-lucide="log-out" class="w-5 h-5 inline mr-2 text-gray-500"></i> Logout
                </button>
            </form>
        </div>
    </div>
</div>

</nav>


    <div class="h-full w-full flex pt-15 bg-[#F4F7FA] text-white">

    <!-- Sidebar -->
    <div x-cloak 
         @mouseenter="sidebarOpen = true" 
         @mouseleave="sidebarOpen = false"
         class="group bg-gradient-to-b from-[#e65c33] to-[#b53d20] p-4 shadow-md flex flex-col space-y-4 fixed top-14 left-0 bottom-0 z-30 transition-all duration-300 ease-in-out"
         :class="sidebarOpen ? 'w-64' : 'w-20'">

       <!-- Sidebar Header -->
<div class="flex items-center justify-center space-x-2 mt-17 mb-18 pb-5 border-b border-black">
    <i data-lucide="briefcase" class="w-8 h-10 text-white "></i>
    <h4 class="logo-text text-center font-bold text-xl" x-show="sidebarOpen" x-transition>
        Human Resource
    </h4>
</div>


        <div class="flex-1 p-3 space-y-8">
            @php
                use Illuminate\Support\Facades\DB;
                $userPosition = session('user_position');
                $permissionKey = 'employeeProfile';

                $hasPermission = DB::table('position_permissions')
                    ->where('position', $userPosition)
                    ->where('permission_id', $permissionKey)
                    ->where('is_allowed', 1)
                    ->exists();
            @endphp

            @if ($userPosition === 'Human resource manager' || ($userPosition === 'Administrative manager' && $hasPermission))
                <a href="{{ route('show.dashboard') }}" class="block">
                    <div class="flex items-center rounded-md cursor-pointer bg-[#FFFFFF38] hover:bg-[#FFFFFF50] text-white transition-all duration-200"
                         :class="sidebarOpen ? 'p-4 space-x-4 justify-start' : 'py-3 px-6 justify-center'">
                        <i data-lucide="home" class="w-6 h-6 shrink-0"></i>
                        <span x-show="sidebarOpen" x-transition>Dashboard</span>
                    </div>
                </a>
            @endif

            @if (in_array(session('user_position'), ['Human resource manager']))
                <a href="{{ route('show.listapplicants') }}" class="block">
                    <div class="flex items-center space-x-4 p-3 rounded-md cursor-pointer hover:bg-[#FFFFFF50] text-white">
                        <i data-lucide="id-card" class="w-6 h-6 shrink-0"></i>
                        <span x-show="sidebarOpen" x-transition>Manage Applicants</span>
                    </div>
                </a>
            @endif

            @php
                $userPosition = session('user_position');
                $hasPermission = session('permission_key')['employeeProfile']->is_allowed ?? false;
            @endphp

            @if ($userPosition === 'Human resource manager' || ($userPosition === 'Administrative Manager' && $hasPermission))
                <a href="{{ route('show.employeeprofiles') }}" class="block">
                    <div class="flex items-center space-x-4 p-3 rounded-md cursor-pointer hover:bg-[#FFFFFF50] text-white">
                        <i data-lucide="user" class="w-6 h-6 shrink-0"></i>
                        <span x-show="sidebarOpen" x-transition>Employees Management</span>
                    </div>
                </a>
            @endif

            @if ($userPosition === 'Human resource manager' || ($userPosition === 'Administrative Manager' && $hasPermission))
                <a href="{{ route('show.attendance') }}" class="block">
                    <div class="flex items-center space-x-4 p-3 rounded-md cursor-pointer hover:bg-[#FFFFFF50] text-white">
                        <i data-lucide="clock" class="w-6 h-6 shrink-0"></i>
                        <span x-show="sidebarOpen" x-transition>Manage Attendance and OT</span>
                    </div>
                </a>
            @endif

            @php
                $userPosition = session('user_position');
                $hasPermission = session('permission_key')['evaluationResults']->is_allowed ?? false;
            @endphp

            @if ($userPosition === 'Human resource manager' || ($userPosition === 'Administrative Manager' && $hasPermission))
                <a href="{{ route('show.evaluateservices') }}" class="block">
                    <div class="flex items-center space-x-4 p-3 rounded-md cursor-pointer hover:bg-[#FFFFFF50] text-white">
                        <i data-lucide="file-text" class="w-6 h-6 shrink-0"></i>
                        <span x-show="sidebarOpen" x-transition>Service Requests and Summary</span>
                    </div>
                </a>
            @endif
        </div>
    </div>

    <!-- Main Content -->
    <div class="absolute right-0 top-16 h-screen overflow-y-auto scrollbar-hide transition-all duration-300 ease-in-out p-5 text-black"
         :class="sidebarOpen ? 'ml-64' : 'ml-20'"
         :style="sidebarOpen ? 'width: calc(100% - 16rem);' : 'width: calc(100% - 5rem);'">
        <main class="min-h-screen bg-transparent text-white">
            {{ $slot }}
        </main>
    </div>
</div>


    <script>
        lucide.createIcons();
    </script>
</body>
</html>
