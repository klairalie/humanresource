<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>WELCOME TO HR DASHBOARD</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

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

    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;600&display=swap" rel="stylesheet">
</head>

<body class="text-black h-full overflow-hidden" x-data="{ sidebarOpen: false }">

    <!-- Navbar -->
    <nav
        class="fixed top-0 left-0 right-0 z-40 bg-gradient-to-r from-orange-200 via-white to-gray-300 backdrop-blur-md px-6 py-3 flex justify-between items-center">
        <!-- Left -->
        <a href="{{ route('show.dashboard') }}">
        <div>
            <img src="{{ url('/3Rs_logo.png') }}" alt="company logo" class="h-10 w-auto">
        </div>
        </a>
        <!-- Right -->
        <div class="flex items-center space-x-6">
            <!-- Notification Bell -->
            <a href="{{ route('queue.failures') }}" class="relative">
                <i data-lucide="bell" class="w-6 h-6"></i>
                @if($failedCount > 0)
                    <span
                        class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-2 py-0.5">
                        {{ $failedCount }}
                    </span>
                @endif
            </a>

            <!-- Profile dropdown -->
            <div x-data="{ open: false }" class="relative">
                <div @click="open = !open" class="flex items-center space-x-2 cursor-pointer select-none">
                    <!-- Profile Picture with Active Dot -->
                    <div class="relative w-12 h-10 flex items-center justify-center rounded-full bg-orange-400 border text-white font-bold text-sm">
    HR
    <!-- Active Green Dot -->
    <span
        class="absolute bottom-0 right-0 block w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
</div>


                    <span class="font-medium">
                        {{ session('user_email') ?? 'user@example.com' }} 
                        
                    </span>
                    <i data-lucide="chevron-down" class="w-5 h-5 text-gray-500"></i>
                </div>

                <!-- Dropdown Menu -->
                <div x-show="open" x-cloak @click.away="open = false" x-transition
                    class="absolute right-0 mt-2 w-44 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                    <a href="{{ route('settings.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <i data-lucide="settings" class="w-5 h-5 inline mr-2 text-gray-500"></i> Settings
                    </a>
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

    <div class="h-full w-full flex pt-15 bg-[url('/logo_3RS.jpg')] bg-cover bg-center">

        <!-- Sidebar -->
        <div x-cloak @mouseenter="sidebarOpen = true" @mouseleave="sidebarOpen = false"
            class="group bg-gradient-to-b from-[#f89b5fBE] to-[#f2bcbcBE] p-4 shadow-md flex flex-col space-y-4 fixed top-14.5 left-0 bottom-0 z-30 transition-all duration-300 ease-in-out"
            :class="sidebarOpen ? 'w-64' : 'w-20'">
            <div class="flex-1 p-2 space-y-9 mt-10">
                <a href="{{ route('show.dashboard') }}" class="block">
    <div
        class="flex items-center rounded-md cursor-pointer bg-orange-200 hover:bg-orange-300 text-black
               transition-all duration-200"
        :class="sidebarOpen ? 'p-4 space-x-4 justify-start' : 'py-3 px-6 justify-center'">
        <i data-lucide="home" class="w-6 h-6 shrink-0"></i>
        <span x-show="sidebarOpen" x-transition>Dashboard</span>
    </div>
</a>


<a href="{{ route('show.listapplicants') }}" class="block">
    <div
        class="flex items-center space-x-4 p-3 rounded-md cursor-pointer hover:bg-orange-200 text-black">
        <i data-lucide="id-card" class="w-6 h-6 shrink-0"></i>
        <span x-show="sidebarOpen" x-transition>Manage Applicants</span>
    </div>
</a>

<a href="{{ route('show.employeeprofiles') }}" class="block">
    <div
        class="flex items-center space-x-4 p-3 rounded-md cursor-pointer hover:bg-orange-200 text-black">
        <i data-lucide="user" class="w-6 h-6 shrink-0"></i>
        <span x-show="sidebarOpen" x-transition>Manage Employee Profiles</span>
    </div>
</a>

<a href="{{ route('show.attendance') }}" class="block">
    <div
        class="flex items-center space-x-4 p-3 rounded-md cursor-pointer hover:bg-orange-200 text-black">
        <i data-lucide="clock" class="w-6 h-6 shrink-0"></i>
        <span x-show="sidebarOpen" x-transition>Manage Attendance and OT</span>
    </div>
</a>

<a href="{{ route('show.evaluateservices') }}" class="block">
    <div
        class="flex items-center space-x-4 p-3 rounded-md cursor-pointer hover:bg-orange-200 text-black">
        <i data-lucide="file-text" class="w-6 h-6 shrink-0"></i>
        <span x-show="sidebarOpen" x-transition>Evaluate Employee Services</span>
    </div>
</a>

            </div>
            <a href="http://Finance.test" class="block">
    <div
        class="flex items-center space-x-4 p-3 rounded-md cursor-pointer hover:bg-orange-200 text-black">
        <i data-lucide="file-text" class="w-6 h-6 shrink-0"></i>
        <span x-show="sidebarOpen" x-transition>Evaluate Employee Services</span>
    </div>
</a>
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
