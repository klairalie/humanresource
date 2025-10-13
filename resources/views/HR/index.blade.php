<x-nav-layout>
  <section 
    x-data="{ openModal: false }"
    class="relative bg-gradient-to-r from-gray-300 to-orange-200 backdrop-blur-md min-h-screen flex flex-col items-center">

    <!-- Hero Section -->
    <div class="relative w-full max-w-7xl mx-auto text-center pt-24 sm:pt-32 md:pt-40 pb-16 px-4 sm:px-6 lg:px-8">
      <!-- Background Image -->
      <div class="absolute inset-0 flex justify-center items-center overflow-hidden opacity-20">
        <img src="https://images.unsplash.com/photo-1603791452906-bd0e7c88c3f0?auto=format&fit=crop&w=1600&q=80"
             class="w-full h-full object-cover">
      </div>

      <!-- Content -->
      <div class="relative z-10">
        <h1 class="text-2xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-sky-900 mb-6 tracking-tight">
          3RS Air-Conditioning Solution
        </h1>
        <p class="text-sm sm:text-base md:text-lg lg:text-xl text-black max-w-2xl mx-auto leading-relaxed mb-10">
          Your trusted partner in air-conditioning installation, cleaning, and maintenance.  
          Seamlessly manage services, applications, and operations in one reliable system.
        </p>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row flex-wrap justify-center gap-4 mt-8">
          <!-- Explore Services -->
          <a href="#services" 
             class="flex items-center justify-center px-5 py-3 bg-sky-600 text-white font-semibold rounded-full shadow-md hover:bg-sky-700 transition w-full sm:w-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            Explore Services
          </a>

          <!-- Book Service -->
          <a href="{{ route('show.bookingindex') }}" 
             class="flex items-center justify-center px-5 py-3 bg-emerald-600 text-white font-semibold rounded-full shadow-md hover:bg-emerald-700 transition w-full sm:w-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.104-.896-2-2-2s-2 .896-2 2 .896 2 2 2 2-.896 2-2zM19 11c0-1.104-.896-2-2-2s-2 .896-2 2 .896 2 2 2 2-.896 2-2zM12 17c-1.104 0-2 .896-2 2h8c0-1.104-.896-2-2-2H12z" />
            </svg>
            Book Service Now
          </a>

          <!-- Contact -->
          <a href="#contact" 
             class="flex items-center justify-center px-5 py-3 bg-gray-100 text-sky-800 font-semibold rounded-full shadow-md hover:bg-gray-200 transition w-full sm:w-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10a9 9 0 11-18 0 9 9 0 0118 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Contact Us
          </a>

          <!-- Join Team -->
          <button 
             @click="openModal = true"
             class="flex items-center justify-center px-5 py-3 bg-gray-100 text-sky-800 font-semibold rounded-full shadow-md hover:bg-gray-200 transition w-full sm:w-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Join Our Team
          </button>
        </div>
      </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="relative w-full max-w-7xl mx-auto py-12 px-4 sm:px-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 lg:gap-8">

      <!-- Installation -->
      <a href="#installation" class="block bg-white shadow-lg rounded-2xl p-6 sm:p-8 text-center hover:shadow-xl transition transform hover:-translate-y-2">
        <div class="flex justify-center mb-4">
          <svg class="w-10 h-10 sm:w-12 sm:h-12 text-sky-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7" />
          </svg>
        </div>
        <h3 class="text-base sm:text-lg md:text-xl font-bold text-sky-800 mb-2">Installation</h3>
        <p class="text-gray-600 text-sm sm:text-base">Professional AC installation ensuring safe, reliable, and efficient performance.</p>
      </a>

      <!-- Repair -->
      <a href="#repair" class="block bg-sky-600 shadow-lg rounded-2xl p-6 sm:p-8 text-center text-white hover:shadow-xl transition transform hover:-translate-y-2">
        <div class="flex justify-center mb-4">
          <svg class="w-10 h-10 sm:w-12 sm:h-12 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m-3-3v6m-9 4h18M5 21h14" />
          </svg>
        </div>
        <h3 class="text-base sm:text-lg md:text-xl font-bold mb-2">Repair</h3>
        <p class="text-sm sm:text-base">Fast and dependable AC repair services to restore comfort and efficiency.</p>
      </a>

      <!-- Cleaning -->
      <a href="#cleaning" class="block bg-white shadow-lg rounded-2xl p-6 sm:p-8 text-center hover:shadow-xl transition transform hover:-translate-y-2">
        <div class="flex justify-center mb-4">
          <svg class="w-10 h-10 sm:w-12 sm:h-12 text-sky-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M5 11h14M7 15h10M9 19h6" />
          </svg>
        </div>
        <h3 class="text-base sm:text-lg md:text-xl font-bold text-sky-800 mb-2">Cleaning</h3>
        <p class="text-gray-600 text-sm sm:text-base">Comprehensive AC cleaning to improve airflow, efficiency, and air quality.</p>
      </a>

      <!-- Buy & Sell -->
      <a href="#buy-sell" class="block bg-white shadow-lg rounded-2xl p-6 sm:p-8 text-center hover:shadow-xl transition transform hover:-translate-y-2">
        <div class="flex justify-center mb-4">
          <svg class="w-10 h-10 sm:w-12 sm:h-12 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .843-3 1.88v4.24c0 1.037 1.343 1.88 3 1.88s3-.843 3-1.88V9.88c0-1.037-1.343-1.88-3-1.88z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12h.01M18 12h.01M12 18h.01M12 6h.01" />
          </svg>
        </div>
        <h3 class="text-base sm:text-lg md:text-xl font-bold text-emerald-700 mb-2">Buy & Sell Units</h3>
        <p class="text-gray-600 text-sm sm:text-base">Affordable new and second-hand AC units with trusted quality assurance.</p>
      </a>
    </div>

    <!-- Showcase Section (Carousel) -->
    <div id="services" 
         x-data="{ slide: 0, total: 3 }" 
         x-init="setInterval(() => { slide = (slide + 1) % total }, 4000)" 
         class="relative w-full max-w-7xl mx-auto py-12 sm:py-16 px-4 sm:px-6 lg:px-8">

      <div class="overflow-hidden">
        <div class="flex transition-transform duration-500"
             :style="`transform: translateX(-${slide * 100}%)`">

          <!-- Slide 1 -->
          <div class="flex-shrink-0 w-full sm:w-1/2 md:w-1/3 p-2 sm:p-4">
            <a href="#aircon-units" class="block bg-white rounded-2xl sm:rounded-3xl shadow-xl overflow-hidden hover:scale-105 transition-transform duration-500">
              <img src="https://images.unsplash.com/photo-1590487985482-f29b8f04f8f3?auto=format&fit=crop&w=800&q=80" 
                   class="w-full h-48 sm:h-60 md:h-72 object-cover">
              <div class="p-4 sm:p-6 text-center">
                <h3 class="text-base sm:text-lg md:text-xl font-bold text-sky-800 mb-2">Aircon Units</h3>
                <p class="text-gray-600 text-xs sm:text-sm md:text-base">Durable, energy-efficient cooling systems for every space.</p>
              </div>
            </a>
          </div>

          <!-- Slide 2 -->
          <div class="flex-shrink-0 w-full sm:w-1/2 md:w-1/3 p-2 sm:p-4">
            <a href="#parts-accessories" class="block bg-white rounded-2xl sm:rounded-3xl shadow-xl overflow-hidden hover:scale-105 transition-transform duration-500">
              <img src="https://images.unsplash.com/photo-1612832021630-9db8c2b2d4d5?auto=format&fit=crop&w=800&q=80" 
                   class="w-full h-48 sm:h-60 md:h-72 object-cover">
              <div class="p-4 sm:p-6 text-center">
                <h3 class="text-base sm:text-lg md:text-xl font-bold text-emerald-700 mb-2">Parts & Accessories</h3>
                <p class="text-gray-600 text-xs sm:text-sm md:text-base">Quality replacement parts and add-ons to extend system life.</p>
              </div>
            </a>
          </div>

          <!-- Slide 3 -->
          <div class="flex-shrink-0 w-full sm:w-1/2 md:w-1/3 p-2 sm:p-4">
            <a href="#maintenance" class="block bg-white rounded-2xl sm:rounded-3xl shadow-xl overflow-hidden hover:scale-105 transition-transform duration-500">
              <img src="https://images.unsplash.com/photo-1581090700227-623dcf12e4b0?auto=format&fit=crop&w=800&q=80" 
                   class="w-full h-48 sm:h-60 md:h-72 object-cover">
              <div class="p-4 sm:p-6 text-center">
                <h3 class="text-base sm:text-lg md:text-xl font-bold text-sky-800 mb-2">Maintenance Services</h3>
                <p class="text-gray-600 text-xs sm:text-sm md:text-base">Expert cleaning, installation, and regular system check-ups.</p>
              </div>
            </a>
          </div>

        </div>
      </div>

      <!-- Dots -->
      <div class="flex justify-center mt-4 sm:mt-6 space-x-2">
        <template x-for="i in total" :key="i">
          <button @click="slide = i - 1" 
                  :class="slide === i-1 ? 'bg-sky-800' : 'bg-gray-300'"
                  class="w-3 h-3 sm:w-4 sm:h-4 rounded-full transition"></button>
        </template>
      </div>
    </div>

    <!-- Application Modal -->
    <div 
      x-show="openModal"
      class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-100 z-50 p-4 " 
      style="display: none;">
      <div class="bg-white rounded-2xl shadow-lg max-w-lg w-full p-6 sm:p-8 text-center mt-0 mb-120">
        <h2 class="text-lg sm:text-xl font-bold text-sky-900 mb-4">Before You Apply</h2>
        <p class="text-black mb-6 text-xl font-semibold">
          To ensure your application is accepted, please download our official resume format 
          and use it when submitting your application.
        </p>

        <a href="{{ route('resume.download') }}" 
           onclick="downloadAndRedirect(event)"
           class="inline-block px-5 py-3 bg-sky-600 text-white font-semibold rounded-full shadow hover:bg-sky-700 transition w-full sm:w-auto">
          Download Resume Format
        </a>

        <button 
          @click="openModal = false"
          class="mt-6 block mx-auto px-4 py-2 bg-gray-200 text-gray-800 rounded-full hover:bg-gray-300 transition">
          Close
        </button>
      </div>
    </div>

  </section>

  <script>
    function downloadAndRedirect(event) {
        event.preventDefault();
        const link = document.createElement('a');
        link.href = "{{ route('resume.download') }}";
        link.download = '';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        setTimeout(() => {
            window.location.href = "{{ route('show.applicationform') }}"; 
        }, 1000);
    }
  </script>
</x-nav-layout>
