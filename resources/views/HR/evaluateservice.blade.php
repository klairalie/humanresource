<x-guest-layout>
    <div class="min-h-screen p-8 text-black">

        <!-- ================= HEADER ================= -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-20 gap-4 mt-20">
            <h1 class="text-3xl font-extrabold mb-4 md:mb-0 tracking-wide text-black flex items-center gap-2">
                Service Requests and Summary
            </h1>

            <!-- 🔹 Dropdown Filter -->
            <select 
                id="sectionFilter"
                class="w-full md:w-80 px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-gray-600 focus:outline-none text-black bg-white"
            >
                <option value="all">All Services</option>
                <option value="cleaning">Cleaning Services</option>
                <option value="repair">Repair Services</option>
                <option value="installation">Installation Services</option>
                <option value="maintenance">Maintenance Services</option>
            </select>
        </div>

        <!-- ================= SERVICE SECTIONS ================= -->
        @php
            $serviceSections = [
                ['title' => 'Cleaning Services', 'items' => $cleaningItems, 'id' => 'cleaning'],
                ['title' => 'Repair Services', 'items' => $repairItems, 'id' => 'repair'],
                ['title' => 'Installation Services', 'items' => $installmentItems, 'id' => 'installation'],
                ['title' => 'Maintenance Services', 'items' => $maintenanceItems ?? collect(), 'id' => 'maintenance'],
            ];
        @endphp

        @foreach($serviceSections as $section)
            <section id="{{ $section['id'] }}" class="service-section mb-16">
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
                                $isUnassigned = ($leadNames === 'Unassigned' && $assistantNames === 'Unassigned');
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

                                    @elseif($item->status === 'Rescheduled')
                                        <button 
                                            class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-400 text-white text-xs font-medium rounded-md cursor-not-allowed opacity-70"
                                            disabled>
                                            <i data-lucide="settings" class="w-4 h-4"></i>
                                            Actions Disabled
                                        </button>

                                    @elseif($isUnassigned)
                                        <button 
                                            class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-400 text-white text-xs font-medium rounded-md cursor-not-allowed opacity-70"
                                            disabled>
                                            <i data-lucide="user-x" class="w-4 h-4"></i>
                                            No Technicians Assigned
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

            <div class="mt-8 flex justify-end gap-2">
                <!-- <button id="exportSummaryBtn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-500 transition text-sm">Export Excel</button> -->
                <button id="printSummaryBtn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-500 transition text-sm">Print</button>
                <button id="closeFooter" class="px-5 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-700 transition">Close</button>
            </div>
        </div>
    </div>

    <!-- ================= STYLES ================= -->
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeIn 0.15s ease-in-out; }
    </style>

    <!-- ================= SWEETALERT2 ================= -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- ================= SCRIPT ================= -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('summaryModal');
            const modalBox = document.getElementById('modalBox');
            const summaryContent = document.getElementById('summaryContent');
            const closeModalBtns = [document.getElementById('closeModal'), document.getElementById('closeFooter')];
            const filterDropdown = document.getElementById('sectionFilter');
            const exportBtn = document.getElementById('exportSummaryBtn');
            const printBtn = document.getElementById('printSummaryBtn');
            let currentSummaryData = null;

            // 🔹 Close modal
            closeModalBtns.forEach(btn => btn.addEventListener('click', closeModal));

            function openModal() {
                modal.classList.remove('hidden');
                setTimeout(() => modalBox.classList.remove('scale-95'), 50);
            }

            function closeModal() {
                modalBox.classList.add('scale-95');
                setTimeout(() => modal.classList.add('hidden'), 150);
            }

            // 🔹 Filter Sections by Dropdown
            filterDropdown.addEventListener('change', () => {
                const selected = filterDropdown.value;
                const sections = document.querySelectorAll('.service-section');

                sections.forEach(section => {
                    if (selected === 'all') {
                        section.style.display = 'block';
                    } else if (section.id === selected) {
                        section.style.display = 'block';
                        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    } else {
                        section.style.display = 'none';
                    }
                });
            });

            // 🔹 Card Action Events
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

                // View Details
                if (e.target.closest('.view-summary-btn')) {
                    const id = e.target.closest('.view-summary-btn').dataset.id;
                    try {
                        const response = await fetch(`/service/details/${id}`);
                        const result = await response.json();
                        if (result.success && result.data) {
                            const item = result.data;
                            currentSummaryData = item;
                            summaryContent.innerHTML = `
                                <div class="grid grid-cols-2 gap-4">
                                    <p><strong>Customer:</strong> ${item.customer}</p>
                                    <p><strong>Business Name:</strong> ${item.business_name ?? 'N/A'}</p>
                                    <p><strong>Service Type:</strong> ${item.service_type}</p>
                                    <p><strong>Lead Technician:</strong> ${item.lead_technician}</p>
                                    <p><strong>Assistant Technician:</strong> ${item.assistant_technician}</p>
                                    <p><strong>Date:</strong> ${item.start_date}</p>
                                    <p><strong>Units:</strong> ${item.quantity}</p>
                                    <p><strong>Status:</strong> ${item.status}</p>
                                    <p><strong>Remarks:</strong> ${item.remarks ?? 'N/A'}</p>
                                    <p><strong>Order Total:</strong> ${Number(item.order_total ?? 0).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}</p>
                                </div>
                            `;
                            openModal();
                        } else {
                            Swal.fire('Error', 'Failed to load service details.', 'error');
                        }
                    } catch (err) {
                        console.error(err);
                        Swal.fire('Error', 'An unexpected error occurred.', 'error');
                    }
                }

                // Update Status
                if (e.target.closest('.status-btn')) {
                    const btn = e.target.closest('.status-btn');
                    const newStatus = btn.dataset.status;
                    const card = btn.closest('[data-id]');
                    const id = card.dataset.id;
                    const badge = card.querySelector('.status-badge');

                    Swal.fire({
                        title: `Are you sure?`,
                        text: `Change status to "${newStatus}"?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, update it',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#111827',
                        cancelButtonColor: '#6B7280',
                        reverseButtons: true,
                    }).then(async (result) => {
                        if (result.isConfirmed) {
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

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Updated!',
                                        text: `Status changed to "${newStatus}" successfully.`,
                                        timer: 2000,
                                        showConfirmButton: false,
                                    });
                                } else {
                                    Swal.fire('Failed!', data.message || 'Failed to update status.', 'error');
                                }
                            } catch (err) {
                                console.error(err);
                                Swal.fire('Error!', 'An error occurred while updating status.', 'error');
                            }
                        }
                    });
                }
            });

            function exportSummaryCSV() {
                if (!currentSummaryData) return;
                const h = ['Customer','Business Name','Service Type','Lead Technician','Assistant Technician','Date','Units','Status','Remarks','Order Total'];
                const d = currentSummaryData;
                const row = [
                    d.customer ?? '',
                    d.business_name ?? '',
                    d.service_type ?? '',
                    d.lead_technician ?? '',
                    d.assistant_technician ?? '',
                    d.start_date ?? '',
                    d.quantity ?? '',
                    d.status ?? '',
                    d.remarks ?? '',
                    d.order_total ?? ''
                ];
                const esc = (s) => {
                    const t = String(s == null ? '' : s);
                    const needQ = /[",\n]/.test(t);
                    const e = t.replace(/"/g, '""');
                    return needQ ? '"' + e + '"' : e;
                };
                const csv = [h.map(esc).join(','), row.map(esc).join(',')].join('\n');
                const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'service_summary.csv';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }

            function printSummary() {
                if (!currentSummaryData) return;
                const d = currentSummaryData;
                const currency = (n) => Number(n ?? 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                const styles = `
                  <style>
                    @page { size: A4; margin: 18mm; }
                    :root { --text:#111827; --muted:#6b7280; --border:#e5e7eb; }
                    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji","Segoe UI Emoji"; color: var(--text); }
                    .report { max-width: 800px; margin: 0 auto; }
                    .header { display:flex; align-items:center; gap:16px; padding-bottom:12px; border-bottom:1px solid var(--border); margin-bottom:16px; }
                    .brand { font-size: 22px; font-weight: 800; letter-spacing: .3px; }
                    .sub { color: var(--muted); font-size: 12px; }
                    .section { margin-top: 16px; }
                    .grid { display:grid; grid-template-columns: 1fr 1fr; gap: 10px 24px; }
                    .item { display:flex; gap: 8px; }
                    .label { width: 160px; font-weight: 600; color: var(--muted); }
                    .value { flex: 1; }
                    .card { border: 1px solid var(--border); border-radius: 8px; padding: 16px; }
                    .remarks { white-space: pre-wrap; }
                    @media print { .card { box-shadow: none; } }
                  </style>`;
                const html = `
                  <html>
                    <head><title>Service Report</title>${styles}</head>
                    <body>
                      <div class="report">
                        <div class="header">
                          <img src="{{ url('/3Rs_logo.png') }}" alt="company logo" class="h-10 w-auto" style="height:40px;width:auto;" />
                          <div>
                            <div class="brand">Service Report</div>
                            <div class="sub">Generated ${new Date().toLocaleString()}</div>
                          </div>
                        </div>

                        <div class="card section">
                          <div class="grid">
                            <div class="item"><div class="label">Customer</div><div class="value">${d.customer ?? ''}</div></div>
                            <div class="item"><div class="label">Business Name</div><div class="value">${d.business_name ?? 'N/A'}</div></div>
                            <div class="item"><div class="label">Service Type</div><div class="value">${d.service_type ?? ''}</div></div>
                            <div class="item"><div class="label">Date</div><div class="value">${d.start_date ?? ''}</div></div>
                            <div class="item"><div class="label">Lead Technician</div><div class="value">${d.lead_technician ?? ''}</div></div>
                            <div class="item"><div class="label">Assistant Technician</div><div class="value">${d.assistant_technician ?? ''}</div></div>
                            <div class="item"><div class="label">Units</div><div class="value">${d.quantity ?? ''}</div></div>
                            <div class="item"><div class="label">Status</div><div class="value">${d.status ?? ''}</div></div>
                            <div class="item"><div class="label">Order Total</div><div class="value">${currency(d.order_total)}</div></div>
                          </div>
                          <div class="section">
                            <div class="label" style="width:auto;margin-bottom:6px;">Remarks</div>
                            <div class="remarks">${d.remarks ?? 'N/A'}</div>
                          </div>
                        </div>
                      </div>
                    </body>
                  </html>`;
                const w = window.open('', '_blank');
                w.document.open();
                w.document.write(html);
                w.document.close();
                w.focus();
                w.print();
            }

            if (exportBtn) exportBtn.addEventListener('click', exportSummaryCSV);
            if (printBtn) printBtn.addEventListener('click', printSummary);
        });
    </script>
</x-guest-layout>
