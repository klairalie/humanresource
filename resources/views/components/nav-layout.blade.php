<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title ?? '3RS Air-Conditioning Solution' }}</title>
  @vite(['resources/css/app.css', 'resources/css/applicant.css', 'resources/js/app.js'])
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-white text-gray-800">

  <!-- Navbar -->
  <nav class="w-full fixed top-0 left-0 z-50 backdrop-blur-md bg-gradient-to-r from-orange-200 via-white to-gray-300 backdrop-blur-md shadow-sm py-3.5 px-6 flex items-center justify-between">
    <!-- Logo -->
    <div class="flex items-left ml-0">
      <img src="{{ asset('storage/3Rs_logo.png') }}" alt="3RS Logo" class="h-10 sm:h-12 w-auto items-left ml-0">
    </div>

    <!-- Nav Links (desktop) -->
    <div class="hidden md:flex items-center space-x-6 text-base sm:text-lg font-medium">
      <a href="/" class="text-gray-700 hover:text-sky-700 hover:underline transition">Home</a>
      <a href="" class="text-gray-700 hover:text-gray-900 hover:underline transition">Login</a>
    </div>

    <!-- Mobile Menu Button -->
    <div class="md:hidden">
      <button id="menu-btn" class="text-gray-700 focus:outline-none focus:ring-2 focus:ring-sky-400 text-xl ml-2">
        ☰
      </button>
    </div>
  </nav>

  <!-- Mobile Menu -->
  <div id="mobile-menu" 
       class="fixed top-0 right-0 h-full w-64 bg-white shadow-lg transform translate-x-full transition-transform duration-300 ease-in-out z-50 md:hidden">
    <!-- Close Button -->
    <div class="flex justify-between items-center px-6 py-4 border-b">
      <span class="font-bold text-lg text-sky-800">Menu</span>
      <button id="close-btn" class="text-gray-600 text-2xl mt-1">&times;</button>
    </div>

    <!-- Links -->
    <div class="flex flex-col space-y-4 px-6 py-6 text-lg">
      <a href="/" class="text-gray-700 hover:text-sky-700 hover:underline transition">Home</a>
      {{-- <a href="{{ route('show.applicationform') }}" class="text-gray-700 hover:text-sky-700 hover:underline transition">Apply</a>
      <a href="" class="text-gray-700 hover:text-emerald-700 hover:underline transition">Book</a> --}}
      <a href="" class="text-gray-700 hover:text-gray-900 hover:underline transition">Login</a>
    </div>
  </div>

  <!-- Page Content -->
  <main class="">
    {{ $slot }}
  </main>

  <!-- Script for Right Sidebar -->
  <script>
    const menuBtn = document.getElementById('menu-btn');
    const closeBtn = document.getElementById('close-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    menuBtn?.addEventListener('click', () => {
      mobileMenu.classList.remove('translate-x-full');
    });

    closeBtn?.addEventListener('click', () => {
      mobileMenu.classList.add('translate-x-full');
    });
  </script>
</body>
</html>
