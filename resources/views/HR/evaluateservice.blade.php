<x-guest-layout>
    <div class="min-h-screen p-8 text-black">

        <!-- ================= HEADER ================= -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-10 gap-4">
            <h1 class="text-3xl font-bold tracking-tight">Service Requests and Summary</h1>
            <input 
                type="text" 
                placeholder="Search by Date, Service Type, or Technician..." 
                class="w-full md:w-80 px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-gray-600 focus:outline-none placeholder-gray-500 text-black"
            >
        </div>

        <!-- ================= SERVICE SECTIONS ================= -->
        @php
            $serviceSections = [
                ['title' => 'Cleaning Services', 'items' => $cleaningItems],
                ['title' => 'Repair Services', 'items' => $repairItems],
                ['title' => 'Installation Services', 'items' => $installmentItems],
            ];
        @endphp

        @foreach($serviceSections as $section)
            <section class="mb-16">
                <h2 class="text-2xl font-semibold border-l-4 border-gray-700 pl-3 mb-6">
                    {{ $section['title'] }}
                </h2>

                @if($section['items']->isEmpty())
                    <p class="text-center text-gray-500 py-6 border border-gray-200 rounded-lg bg-gray-50">
                        No service reports available.
                    </p>
                @else
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($section['items'] as $item)
                            @php
                                $statusColors = [
                                    'Pending' => 'bg-gray-200 text-gray-800',
                                    'In Progress' => 'bg-blue-100 text-blue-800',
                                    'Completed' => 'bg-green-500 text-white',
                                    'Rescheduled' => 'bg-yellow-200 text-yellow-800',
                                ];

                                $leadNames = $item->leadTechnicians->map(fn($t) => "{$t->first_name} {$t->last_name}")->implode(', ') ?: 'Unassigned';
                                $assistantNames = $item->assistantTechnicians->map(fn($t) => "{$t->first_name} {$t->last_name}")->implode(', ') ?: 'Unassigned';
                            @endphp

                            <div class="bg-white rounded-2xl shadow-md border border-gray-200 hover:shadow-lg transition flex flex-col justify-between p-6" data-id="{{ $item->item_id }}">
                                
                                <!-- Card Header -->
                                <div>
                                    <div class="flex justify-between items-start mb-3">
                                        <h3 class="text-lg font-semibold text-gray-800">
                                            {{ $item->service_type ?? $item->service->service_type ?? 'N/A' }}
                                        </h3>
                                        <span class="status-badge px-3 py-1 text-xs font-semibold rounded-full {{ $statusColors[$item->status] ?? 'bg-gray-200 text-gray-800' }}">
                                            {{ $item->status ?? 'Pending' }}
                                        </span>
                                    </div>

                                    <!-- Card Details -->
                                    <div class="text-sm text-gray-600 space-y-1">
                                        <p><strong>Date:</strong> {{ $item->start_date ?? 'N/A' }}</p>
                                        <p><strong>Lead Technician:</strong> {{ $leadNames }}</p>
                                        <p><strong>Assistant Technician:</strong> {{ $assistantNames }}</p>
                                        <p><strong>Units:</strong> {{ $item->quantity ?? 0 }}</p>
                                    </div>
                                </div>

                                <!-- Card Actions -->
                                <div class="mt-4 flex justify-end">
                                    @if($item->status === 'Completed')
                                        <button 
                                            class="view-summary-btn inline-flex items-center gap-2 px-3 py-1.5 bg-gray-800 text-white text-xs font-medium rounded-md hover:bg-gray-700 transition"
                                            data-id="{{ $item->item_id }}">
                                            <i data-lucide="file-text" class="w-4 h-4 text-gray-200"></i>
                                            View Details
                                        </button>
                                    @else
                                        <div class="relative">
                                            <button 
                                                class="action-toggle inline-flex items-center gap-1 px-3 py-1.5 bg-gray-800 text-white text-xs font-medium rounded-md hover:bg-gray-700 transition"
                                                type="button">
                                                <i data-lucide="settings" class="w-4 h-4"></i>
                                                Actions
                                                <i data-lucide="chevron-down" class="w-3 h-3 ml-1"></i>
                                            </button>

                                            <div class="action-menu hidden absolute right-0 mt-1 w-44 bg-white border border-gray-200 rounded-lg shadow-lg z-20 text-left animate-fade-in">
                                                <button class="status-btn w-full flex items-center gap-2 px-4 py-2 text-gray-700 text-sm hover:bg-gray-100 transition" data-status="Completed">
                                                    <i data-lucide="check-circle" class="w-4 h-4 text-green-600"></i>
                                                    Mark Completed
                                                </button>
                                                <button class="status-btn w-full flex items-center gap-2 px-4 py-2 text-gray-700 text-sm hover:bg-gray-100 transition" data-status="In Progress">
                                                    <i data-lucide="play-circle" class="w-4 h-4 text-blue-600"></i>
                                                    In Progress
                                                </button>
                                                <button class="status-btn w-full flex items-center gap-2 px-4 py-2 text-gray-700 text-sm hover:bg-gray-100 transition" data-status="Rescheduled">
                                                    <i data-lucide="refresh-cw" class="w-4 h-4 text-yellow-600"></i>
                                                    Reschedule
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $section['items']->links('pagination::tailwind') }}
                    </div>
                @endif
            </section>
        @endforeach
    </div>

    <!-- ================= MODAL ================= -->
    <div id="summaryModal" class="hidden fixed inset-0 backdrop-blur-sm bg-black/50 flex items-center justify-center z-50 transition-opacity duration-300 ease-in-out">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-8 relative transform scale-95 transition-all duration-300 ease-in-out" id="modalBox">
            <button id="closeModal" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 transition">✕</button>

            <div class="mb-6 border-b pb-3">
                <h2 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">
                    <i data-lucide="clipboard-list" class="w-6 h-6 text-gray-700"></i>
                    Service Summary Report
                </h2>
            </div>

            <div id="summaryContent" class="space-y-3 text-gray-800 text-sm">
                <p class="text-gray-500">Loading details...</p>
            </div>

            <div class="mt-8 flex justify-end">
                <button id="closeFooter" class="px-5 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-700 transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- ================= STYLES ================= -->
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.15s ease-in-out;
        }
    </style>

    <!-- ================= SCRIPT ================= -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('summaryModal');
            const modalBox = document.getElementById('modalBox');
            const summaryContent = document.getElementById('summaryContent');
            const closeModalBtns = [document.getElementById('closeModal'), document.getElementById('closeFooter')];

            closeModalBtns.forEach(btn => btn.addEventListener('click', closeModal));

            function openModal() {
                modal.classList.remove('hidden');
                setTimeout(() => modalBox.classList.remove('scale-95'), 50);
            }

            function closeModal() {
                modalBox.classList.add('scale-95');
                setTimeout(() => modal.classList.add('hidden'), 150);
            }

            document.addEventListener('click', async (e) => {
                if (e.target.closest('.action-toggle')) {
                    const btn = e.target.closest('.action-toggle');
                    const menu = btn.nextElementSibling;
                    menu.classList.toggle('hidden');
                    return;
                }

                document.querySelectorAll('.action-menu').forEach(menu => {
                    if (!menu.contains(e.target) && !e.target.closest('.action-toggle')) {
                        menu.classList.add('hidden');
                    }
                });

                // ✅ View Details Modal
                if (e.target.closest('.view-summary-btn')) {
                    const id = e.target.closest('.view-summary-btn').dataset.id;
                    try {
                        const response = await fetch(`/service/details/${id}`);
                        const result = await response.json();
                        if (result.success && result.data) {
                            const item = result.data;
                            summaryContent.innerHTML = `
                                <div class="grid grid-cols-2 gap-4">
                                    <p><strong>Service Type:</strong> ${item.service_type}</p>
                                    <p><strong>Lead Technician:</strong> {{ $leadNames }}</p>
                                    <p><strong>Assistant Technician:</strong> {{ $assistantNames }}</p>
                                    <p><strong>Date:</strong> ${item.start_date}</p>
                                    <p><strong>Units:</strong> ${item.quantity}</p>
                                    <p><strong>Status:</strong> ${item.status}</p>
                                    <p><strong>Remarks:</strong> ${item.remarks ?? 'N/A'}</p>
                                </div>
                            `;
                            openModal();
                        } else alert('Failed to load service details.');
                    } catch (err) {
                        console.error(err);
                        alert('Error loading details.');
                    }
                }

                // ✅ Update Status
                if (e.target.closest('.status-btn')) {
                    const btn = e.target.closest('.status-btn');
                    const newStatus = btn.dataset.status;
                    const card = btn.closest('[data-id]');
                    const id = card.dataset.id;
                    const badge = card.querySelector('.status-badge');

                    try {
                        const res = await fetch(`/service/update-status/${id}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({ status: newStatus }),
                        });
                        const data = await res.json();

                        if (data.success) {
                            badge.textContent = newStatus;
                            badge.className = `status-badge px-3 py-1 text-xs font-semibold rounded-full ${
                                newStatus === 'Completed'
                                    ? 'bg-green-500 text-white'
                                    : newStatus === 'In Progress'
                                    ? 'bg-blue-100 text-blue-800'
                                    : newStatus === 'Rescheduled'
                                    ? 'bg-yellow-200 text-yellow-800'
                                    : 'bg-gray-200 text-gray-800'
                            }`;

                            const actionDiv = card.querySelector('.mt-4');
                            actionDiv.innerHTML = `
                                <button 
                                    class="view-summary-btn inline-flex items-center gap-2 px-3 py-1.5 bg-gray-800 text-white text-xs font-medium rounded-md hover:bg-gray-700 transition"
                                    data-id="${id}">
                                    <i data-lucide="file-text" class="w-4 h-4 text-gray-200"></i>
                                    View Details
                                </button>
                            `;
                        } else alert('Failed to update status.');
                    } catch (err) {
                        console.error(err);
                        alert('Error updating status.');
                    }
                }
            });
        });
    </script>
</x-guest-layout>
