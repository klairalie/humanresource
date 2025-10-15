<x-guest-layout>
    <div class="min-h-screen p-8 text-black">

        <!-- ================= HEADER ================= -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <h1 class="text-3xl font-semibold tracking-tight">Service Evaluation</h1>

            <div class="flex items-center space-x-3 mt-4 md:mt-0">
                <input 
                    type="text" 
                    placeholder="Search by Date, Service Type, or Technician..." 
                    class="w-80 px-3 py-2 border border-gray-400 rounded-lg shadow-sm focus:ring-2 focus:ring-gray-600 focus:outline-none text-black placeholder-gray-500"
                >
                {{-- <a href="{{ route('show.quotationform') }}" 
                   class="px-5 py-2 bg-gray-900 text-white font-medium rounded-lg shadow hover:bg-gray-700 transition">
                    + New Quotation
                </a> --}}
            </div>
        </div>

        <!-- ================= SERVICE TABLES ================= -->
        @php
            $serviceSections = [
                ['title' => 'Cleaning Services', 'items' => $cleaningItems],
                ['title' => 'Repair Services', 'items' => $repairItems],
                ['title' => 'Installation Services', 'items' => $installmentItems],
            ];
        @endphp

        @foreach($serviceSections as $section)
            <div class="mb-12">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold border-l-4 border-gray-700 pl-3">
                        {{ $section['title'] }}
                    </h2>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-300 overflow-hidden">
                    <table class="w-full border-collapse text-sm text-black">
                        <thead>
                            <tr class="bg-gray-100 text-left">
                                <th class="px-5 py-3 font-semibold">Date</th>
                                <th class="px-5 py-3 font-semibold">Service Type</th>
                                <th class="px-5 py-3 font-semibold">Technician</th>
                                <th class="px-5 py-3 font-semibold">Units</th>
                                <th class="px-5 py-3 font-semibold">Status</th>
                                <th class="px-5 py-3 text-center font-semibold">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($section['items'] as $item)
                                @php
                                    $statusClasses = [
                                        'Pending' => 'bg-gray-200 text-gray-800',
                                        'In Progress' => 'bg-gray-300 text-gray-800',
                                        'Completed' => 'bg-gray-400 text-white',
                                        'Rescheduled' => 'bg-gray-300 text-black',
                                    ];
                                @endphp
                                <tr class="border-b hover:bg-gray-50 transition" data-id="{{ $item->item_id }}">
                                    <td class="px-5 py-3">{{ $item->start_date ?? 'N/A' }}</td>
                                    <td class="px-5 py-3">{{ $item->service_type ?? $item->service->service_type ?? 'N/A' }}</td>
                                    <td class="px-5 py-3">{{ $item->technician->full_name ?? 'Unassigned' }}</td>
                                    <td class="px-5 py-3">{{ $item->quantity ?? 0 }}</td>
                                    <td class="px-5 py-3">
                                        <span class="status-badge px-3 py-1 text-xs font-semibold rounded-full {{ $statusClasses[$item->status] ?? 'bg-gray-200 text-gray-800' }}">
                                            {{ $item->status ?? 'Pending' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <div class="action-buttons flex justify-center items-center gap-2">
                                            @if($item->status === 'Completed')
                                                <button 
                                                    class="view-summary-btn px-4 py-1.5 bg-gray-900 text-white rounded-md text-xs font-medium hover:bg-gray-700 transition"
                                                    data-id="{{ $item->item_id }}">
                                                    View Details
                                                </button>

                                            @elseif($item->status === 'Rescheduled')
                                                <button class="status-btn px-3 py-1.5 bg-gray-900 text-white rounded-md text-xs font-medium hover:bg-gray-700 transition" data-status="Completed">✓ Complete</button>
                                                <button class="status-btn px-3 py-1.5 bg-gray-600 text-white rounded-md text-xs font-medium hover:bg-gray-500 transition" data-status="In Progress">▶ In Progress</button>

                                            @elseif($item->status === 'In Progress')
                                                <button class="status-btn px-3 py-1.5 bg-gray-900 text-white rounded-md text-xs font-medium hover:bg-gray-700 transition" data-status="Completed">✓ Complete</button>
                                                <button class="status-btn px-3 py-1.5 bg-gray-200 text-black rounded-md text-xs font-medium hover:bg-gray-300 transition border border-gray-400" data-status="Rescheduled">⟳ Reschedule</button>

                                            @else
                                                <button class="status-btn px-3 py-1.5 bg-gray-900 text-white rounded-md text-xs font-medium hover:bg-gray-700 transition" data-status="Completed">✓ Complete</button>
                                                <button class="status-btn px-3 py-1.5 bg-gray-200 text-black rounded-md text-xs font-medium hover:bg-gray-300 transition border border-gray-400" data-status="Rescheduled">⟳ Reschedule</button>
                                                <button class="status-btn px-3 py-1.5 bg-gray-600 text-white rounded-md text-xs font-medium hover:bg-gray-500 transition" data-status="In Progress">▶ In Progress</button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-gray-500 py-5">
                                        No service reports available.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>

    <!-- ================= MODAL ================= -->
    <div id="summaryModal" 
         class="hidden fixed inset-0 backdrop-blur-sm bg-black/50 flex items-center justify-center z-50 transition-opacity duration-300 ease-in-out">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-8 relative transform scale-95 transition-all duration-300 ease-in-out" id="modalBox">
            <!-- Close Button -->
            <button id="closeModal" 
                    class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 transition">
                ✕
            </button>

            <!-- Modal Header -->
            <div class="mb-6 border-b pb-3">
                <h2 class="text-2xl font-semibold text-gray-800 flex items-center gap-2">
                    <i data-lucide="clipboard-list" class="w-6 h-6 text-gray-700"></i>
                    Service Summary Report
                </h2>
            </div>

            <!-- Modal Content -->
            <div id="summaryContent" class="space-y-3 text-gray-800 text-sm">
                <p class="text-gray-500">Loading details...</p>
            </div>

            <!-- Modal Footer -->
            <div class="mt-8 flex justify-end">
                <button id="closeFooter" 
                        class="px-5 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-700 transition">
                    Close
                </button>
            </div>
        </div>
    </div>

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

            // View Details handler
            document.addEventListener('click', async (e) => {
                if (e.target.classList.contains('view-summary-btn')) {
                    const id = e.target.dataset.id;
                    try {
                        const response = await fetch(`/service/details/${id}`);
                        const result = await response.json();

                        if (result.success && result.data) {
                            const item = result.data;
                            summaryContent.innerHTML = `
                                <div class="grid grid-cols-2 gap-4">
                                    <p><strong>Service Type:</strong> ${item.service_type}</p>
                                    <p><strong>Technician:</strong> ${item.technician_name}</p>
                                    <p><strong>Date:</strong> ${item.start_date}</p>
                                    <p><strong>Units:</strong> ${item.quantity}</p>
                                    <p><strong>Status:</strong> ${item.status}</p>
                                    <p><strong>Remarks:</strong> ${item.remarks ?? 'N/A'}</p>
                                </div>
                            `;
                            openModal();
                        } else {
                            alert('Failed to load service details.');
                        }
                    } catch (error) {
                        console.error(error);
                        alert('Error loading details.');
                    }
                }

                // ================= INSTANT STATUS UPDATE =================
                if (e.target.classList.contains('status-btn')) {
                    const btn = e.target;
                    const newStatus = btn.dataset.status;
                    const row = btn.closest('tr');
                    const id = row.dataset.id;
                    const badge = row.querySelector('.status-badge');
                    const actionDiv = row.querySelector('.action-buttons');

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
                                    ? 'bg-gray-400 text-white'
                                    : newStatus === 'In Progress'
                                    ? 'bg-gray-300 text-gray-800'
                                    : newStatus === 'Rescheduled'
                                    ? 'bg-gray-300 text-black'
                                    : 'bg-gray-200 text-gray-800'
                            }`;

                            // Update buttons dynamically
                            if (newStatus === 'Completed') {
                                actionDiv.innerHTML = `
                                    <button class="view-summary-btn px-4 py-1.5 bg-gray-900 text-white rounded-md text-xs font-medium hover:bg-gray-700 transition" data-id="${id}">
                                        View Details
                                    </button>
                                `;
                            } else if (newStatus === 'In Progress') {
                                actionDiv.innerHTML = `
                                    <button class="status-btn px-3 py-1.5 bg-gray-900 text-white rounded-md text-xs font-medium hover:bg-gray-700 transition" data-status="Completed">✓ Complete</button>
                                    <button class="status-btn px-3 py-1.5 bg-gray-200 text-black rounded-md text-xs font-medium hover:bg-gray-300 transition border border-gray-400" data-status="Rescheduled">⟳ Reschedule</button>
                                `;
                            } else if (newStatus === 'Rescheduled') {
                                actionDiv.innerHTML = `
                                    <button class="status-btn px-3 py-1.5 bg-gray-900 text-white rounded-md text-xs font-medium hover:bg-gray-700 transition" data-status="Completed">✓ Complete</button>
                                    <button class="status-btn px-3 py-1.5 bg-gray-600 text-white rounded-md text-xs font-medium hover:bg-gray-500 transition" data-status="In Progress">▶ In Progress</button>
                                `;
                            } else {
                                actionDiv.innerHTML = `
                                    <button class="status-btn px-3 py-1.5 bg-gray-900 text-white rounded-md text-xs font-medium hover:bg-gray-700 transition" data-status="Completed">✓ Complete</button>
                                    <button class="status-btn px-3 py-1.5 bg-gray-200 text-black rounded-md text-xs font-medium hover:bg-gray-300 transition border border-gray-400" data-status="Rescheduled">⟳ Reschedule</button>
                                    <button class="status-btn px-3 py-1.5 bg-gray-600 text-white rounded-md text-xs font-medium hover:bg-gray-500 transition" data-status="In Progress">▶ In Progress</button>
                                `;
                            }
                        } else {
                            alert('Failed to update status.');
                        }
                    } catch (error) {
                        console.error(error);
                        alert('Error updating status.');
                    }
                }
            });
        });
    </script>
</x-guest-layout>
