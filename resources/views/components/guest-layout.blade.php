<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>WELCOME TO HR DASHBOARD</title>
    @vite('resources/css/app.css', 'resources/js/app.js')

    <!-- Alpine.js -->
    <script src="//unpkg.com/alpinejs" defer></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Prevent x-cloak flash -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-black text-white h-full overflow-hidden">

<div x-data="{ expanded: false }" class="h-full w-full flex">

    <!-- Sidebar -->
    <div 
        class="bg-black p-6 shadow-amber-100 flex flex-col space-y-4 fixed top-0 left-0 bottom-0 transition-all duration-300 ease-in-out z-50 scrollbar-hide"
        @mouseenter="expanded = true"
        @mouseleave="expanded = false"
        :class="expanded ? 'w-64' : 'w-20'"
        x-cloak
    >
        <!-- Sidebar Header -->
        <div class="p-6 text-lg text-center font-bold border-b border-gray-700" x-show="expanded" x-cloak>
            <h2>(HR Admin's Name)</h2>
        </div>

        <!-- Sidebar Menu -->
        <div class="flex-1 p-4 space-y-4 text-md">
            <a href="{{ route('show.dashboard') }}" class="block">
                <div class="flex items-center space-x-4 p-3 rounded-md cursor-pointer hover:bg-[#99FF33] bg-[#66CC00] text-black">
                    <i class="fas fa-home text-xl"></i>
                    <span x-show="expanded" class="transition-opacity duration-300" x-cloak>Dashboard</span>
                </div>
            </a>

            <a href="{{ route('show.employeeprofiles') }}" class="block">
                <div class="flex items-center space-x-4 p-3 rounded-md cursor-pointer hover:bg-[#99FF33]">
                    <i class="fas fa-user-tie text-xl"></i>
                    <span x-show="expanded" class="transition-opacity duration-300" x-cloak>Manage Employee Profiles</span>
                </div>
            </a>

            <a href="{{ route('show.attendance') }}" class="block">
                <div class="flex items-center space-x-4 p-3 rounded-md cursor-pointer hover:bg-[#99FF33]">
                    <i class="fas fa-user-clock text-xl"></i>
                    <span x-show="expanded" class="transition-opacity duration-300" x-cloak>Manage Attendance and OT</span>
                </div>
            </a>
             <a href="{{ route('view.payroll') }}" class="block"> 
            <div class="flex items-center space-x-4 p-3 rounded-md cursor-pointer hover:bg-[#99FF33]">
                <i class="fas fa-coins text-xl"></i>
                <span x-show="expanded" class="transition-opacity duration-300" x-cloak>Payroll Data Operation</span>
            </div>
             </a>
            <div class="flex items-center space-x-4 p-3 rounded-md cursor-pointer hover:bg-[#99FF33]">
                <i class="fas fa-file-alt text-xl"></i>
                <span x-show="expanded" class="transition-opacity duration-300" x-cloak>Evaluate Employee Services</span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div 
        class="absolute right-0 top-0 h-screen overflow-y-auto scrollbar-hide transition-all duration-300 ease-in-out p-6 bg-[url('/backgroundmain.png')] bg-cover bg-center"
        :class="expanded ? 'ml-64' : 'ml-20'"
        style="width: calc(100% - 5rem);"
        x-effect="$el.style.width = expanded ? 'calc(100% - 16rem)' : 'calc(100% - 5rem)'"
        x-cloak
    >
        <main class="min-h-screen bg-transparent">
    {{ $slot }}
</main>

</div>
</body>
</html>
