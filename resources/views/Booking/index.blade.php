<x-nav-layout>
<section class="container mx-auto p-6 md:py-12">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
      Endorsed Air-Conditioning Units
    </h1>
    <p class="text-sm text-gray-500">Browse types we resell — click <em>Quick View</em> or Request Quote.</p>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
    {{-- Dynamic loop: pass $aircons from controller
    @foreach($aircons as $unit)
      <article
        class="bg-white rounded-2xl p-5 shadow hover:shadow-lg transform hover:-translate-y-2 duration-300"
        role="article"
        aria-labelledby="unit-{{ $unit->id }}-title">

        <div class="flex justify-between items-start mb-3">
          <span class="inline-block text-xs font-medium uppercase bg-gray-100 px-3 py-1 rounded-full text-gray-600">
            {{ $unit->category ?? 'Split' }}
          </span>
          <span class="text-xs text-gray-500">{{ $unit->energy_rating ?? '3-star' }}</span>
        </div>

        <img
          src="{{ $unit->image_url ?? asset('images/aircon-placeholder.png') }}"
          alt="{{ $unit->brand }} {{ $unit->model }}"
          class="mx-auto h-40 md:h-44 object-contain">

        <h3 id="unit-{{ $unit->id }}-title" class="text-lg font-semibold mt-4 text-gray-800">
          {{ $unit->brand }} <span class="font-normal"> {{ $unit->model }}</span>
        </h3>

        <p class="text-sm text-gray-600 mt-1">
          Capacity: <span class="font-medium">{{ $unit->capacity ?? '9,000–24,000' }} BTU</span>
          • Warranty: {{ $unit->warranty ?? '1 yr parts' }}
        </p>

        <p class="text-sm text-gray-700 mt-3 line-clamp-3">
          {{ Str::limit($unit->short_description ?? 'Efficient cooling, inverter options, and warranty-backed service.', 120) }}
        </p>

        <div class="mt-4 flex gap-2">
          <button
            type="button"
            class="js-quickview flex-1 py-2 rounded-md border border-gray-200 text-sm font-medium hover:bg-gray-50"
            data-unit='@json($unit)'>
            Quick View
          </button>

          <a
            href="{{ route('booking.create', ['type' => $unit->slug ?? $unit->id]) }}"
            class="flex-1 py-2 rounded-md bg-purple-600 text-white text-center text-sm font-semibold hover:bg-purple-700">
            Request Quote
          </a>
        </div>

        <div class="mt-3 flex items-center justify-between">
          <span class="text-lg font-bold text-gray-800">
            {{ isset($unit->price) ? '₱' . number_format($unit->price) : 'Price on request' }}
          </span>
          <a href="{{ route('products.show', $unit->slug ?? $unit->id) }}" class="text-xs text-indigo-600 hover:underline">View details</a>
        </div>
      </article>
    @endforeach --}}

    {{-- Optionally: a static fallback card if no $aircons provided --}}
    @unless(count($aircons ?? []) > 0)
      <div class="bg-white rounded-2xl p-6 shadow text-center">
        <img src="{{ asset('images/aircon-placeholder.png') }}" alt="Sample split type" class="mx-auto h-40 object-contain">
        <h3 class="mt-4 font-semibold">Split (Inverter) — Sample</h3>
        <p class="text-sm text-gray-600 mt-2">9,000 - 18,000 BTU • Energy Efficient • 1-yr warranty</p>
        <div class="mt-4 flex gap-2">
          <button class="py-2 rounded-md border text-sm w-1/2">Quick View</button>
          <a class="py-2 rounded-md bg-purple-600 text-white w-1/2 text-sm text-center">Request Quote</a>
        </div>
      </div>
    @endunless
  </div>
</section>

{{-- Quick View Modal --}}
<div id="unitModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4" aria-hidden="true">
  <div class="bg-white rounded-2xl max-w-3xl w-full shadow-lg overflow-auto">
    <div class="flex justify-between items-start p-5 border-b">
      <h2 id="modalTitle" class="text-xl font-semibold text-gray-800">Title</h2>
      <button id="modalClose" aria-label="Close modal" class="text-gray-500 hover:text-gray-800">✕</button>
    </div>

    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
      <img id="modalImage" src="" alt="" class="col-span-1 h-44 object-contain mx-auto" />

      <div class="md:col-span-2">
        <p id="modalCategory" class="text-sm text-gray-500 mb-2"></p>
        <div id="modalPrice" class="text-2xl font-bold text-gray-800 mb-3"></div>

        <ul id="modalSpecs" class="text-sm text-gray-700 space-y-1 mb-4">
          <!-- populated by JS -->
        </ul>

        <p id="modalDescription" class="text-sm text-gray-600 mb-4"></p>

        <div class="flex gap-3">
          <a id="modalQuote" href="#" class="px-4 py-2 rounded-md bg-purple-600 text-white text-sm font-semibold hover:bg-purple-700">Request Quote</a>
          <a id="modalProduct" href="#" class="px-4 py-2 rounded-md border text-sm hover:bg-gray-50">View product</a>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Minimal JS to populate and toggle modal --}}
<script>
  document.addEventListener('click', function (e) {
    // Open Quick View
    const quick = e.target.closest('.js-quickview');
    if (quick) {
      try {
        const unit = JSON.parse(quick.getAttribute('data-unit'));
        // populate modal fields
        document.getElementById('modalTitle').textContent = (unit.brand || '') + ' ' + (unit.model || '');
        document.getElementById('modalImage').src = unit.image_url || '/images/aircon-placeholder.png';
        document.getElementById('modalImage').alt = (unit.brand || '') + ' ' + (unit.model || '');
        document.getElementById('modalCategory').textContent = (unit.category || 'Split') + ' • ' + (unit.capacity || '—') + ' BTU';
        document.getElementById('modalPrice').textContent = unit.price ? '₱' + Number(unit.price).toLocaleString() : 'Price on request';

        const specs = [
          'Type: ' + (unit.type || 'Split'),
          'Inverter: ' + ((unit.inverter) ? 'Yes' : 'Available'),
          'Warranty: ' + (unit.warranty || '—'),
          'Energy rating: ' + (unit.energy_rating || '—'),
        ];
        document.getElementById('modalSpecs').innerHTML = specs.map(s => '<li>'+s+'</li>').join('');

        document.getElementById('modalDescription').textContent = unit.description || unit.short_description || '';

        // buttons
        document.getElementById('modalQuote').href = '/booking/create?unit=' + encodeURIComponent(unit.slug || unit.id);
        document.getElementById('modalProduct').href = '/products/' + encodeURIComponent(unit.slug || unit.id);

        document.getElementById('unitModal').classList.remove('hidden');
        document.getElementById('unitModal').querySelector('div').focus();
      } catch (err) {
        console.error('Invalid unit JSON', err);
      }
      return;
    }

    // Close modal when clicking close or backdrop
    if (e.target.id === 'modalClose' || e.target.id === 'unitModal') {
      document.getElementById('unitModal').classList.add('hidden');
    }
  });

  // close on ESC
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') document.getElementById('unitModal').classList.add('hidden');
  });
</script>
</x-nav-layout>
