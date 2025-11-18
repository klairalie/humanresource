<x-guest-layout>
<!-- Top Navigation Buttons -->
<div class="flex justify-end gap-4 mt-20 mb-8 mr-35">
    <a href="{{ route('show.leaverequest') }}" class="flex items-center gap-4 bg-[#2C2C2C] text-white text-sm font-semibold px-10 py-3.5 shadow-md hover:scale-[1.02]">
        <i class="fa-solid fa-calendar-check"></i> Manage Leave Requests
    </a>
    <a href="{{ route('show.overtime') }}" class="flex items-center gap-4 bg-[#2C2C2C] text-white text-sm font-semibold px-10 py-3.5 shadow-md hover:scale-[1.02]">
        <i class="fa-solid fa-clock"></i> Manage Overtime
    </a>
</div>

<div class="max-w-5xl mx-auto bg-white p-15 mt-6 mb-35 shadow-lg border border-gray-200 text-black">
    <div class="flex items-end justify-between mb-4">
        <h1 class="text-3xl font-extrabold tracking-wide text-black flex items-center gap-2">Daily Attendance Record</h1>
        <div class="flex gap-3 items-end text-sm">
            <div>
                <label for="globalFromDate" class="block text-gray-600 text-xs font-semibold">From:</label>
                <input type="date" id="globalFromDate" class="border border-gray-300 px-2 py-1 rounded text-black">
            </div>
            <div>
                <label for="globalToDate" class="block text-gray-600 text-xs font-semibold">To:</label>
                <input type="date" id="globalToDate" class="border border-gray-300 px-2 py-1 rounded text-black">
            </div>
            <div class="flex items-end gap-2">
                <button id="globalExportExcelBtn" type="button" class="bg-green-600 hover:bg-green-500 text-white px-3 py-1.5 rounded text-xs font-medium shadow">Export Excel</button>
                <button id="globalExportPdfBtn" type="button" class="bg-red-600 hover:bg-red-500 text-white px-3 py-1.5 rounded text-xs font-medium shadow">Print</button>
            </div>
        </div>
    </div>

    <table class="w-full border border-gray-200 text-sm rounded-lg overflow-hidden">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border text-left font-semibold text-gray-700">Employee Name</th>
                <th class="px-4 py-2 border text-left font-semibold text-gray-700">Position</th>
                <th class="px-4 py-2 border text-center font-semibold text-gray-700">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employees as $employee)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-2 border">{{ $employee->first_name }} {{ $employee->last_name }}</td>
                <td class="px-4 py-2 border">{{ $employee->position }}</td>
                <td class="px-4 py-2 border text-center">
                    <button type="button"
                        onclick='openModal({{ $employee->employeeprofiles_id }}, {!! json_encode($employee->first_name . " " . $employee->last_name) !!})'
                        class="inline-flex items-center gap-1 bg-gray-700 hover:bg-gray-600 text-white px-3 py-1.5 rounded text-xs font-medium shadow hover:scale-105">
                        <i class="fa-solid fa-eye"></i> View Details
                    </button>
                </td>
            </tr>
            @empty
            <tr><td colspan="3" class="text-center py-4 text-gray-600">No employees found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-6 mb-10">{{ $employees->appends(request()->query())->links() }}</div>

    <!-- Attendance Modal -->
    <div id="attendanceModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white w-full max-w-2xl p-6 rounded-lg shadow-xl text-black transform scale-95 transition-all my-10" id="modalBox">
            <div class="flex justify-between items-center border-b pb-2 mb-3">
                <h2 id="modalTitle" class="text-lg font-bold text-gray-800">Attendance Details</h2>
                <button onclick="closeModal()" class="text-gray-600 text-2xl font-bold hover:text-gray-800">&times;</button>
            </div>

            <div id="employeeName" class="font-semibold mb-3 text-center text-blue-700"></div>

            <div class="flex gap-3 mb-4 justify-center text-sm">
                <div>
                    <label for="fromDate" class="block text-gray-600 text-xs font-semibold">From:</label>
                    <input type="date" id="fromDate" class="border border-gray-300 px-2 py-1 rounded text-black">
                </div>
                <div>
                    <label for="toDate" class="block text-gray-600 text-xs font-semibold">To:</label>
                    <input type="date" id="toDate" class="border border-gray-300 px-2 py-1 rounded text-black">
                </div>
                <div class="flex items-end gap-2">
                    <button id="exportExcelBtn" type="button" class="bg-green-600 hover:bg-green-500 text-white px-3 py-1.5 rounded text-xs font-medium shadow">Export Excel</button>
                    <button id="exportPdfBtn" type="button" class="bg-red-600 hover:bg-red-500 text-white px-3 py-1.5 rounded text-xs font-medium shadow">Export PDF</button>
                    <button id="editSelectionBtn" type="button" class="bg-blue-600 hover:bg-blue-500 text-white px-3 py-1.5 rounded text-xs font-medium shadow hidden">Edit Selection</button>
                </div>
            </div>

            <div id="modalContent" class="text-sm overflow-y-auto max-h-80 mt-4 mb-4">
                <p class="text-center py-4 text-gray-500">Loading attendance records...</p>
            </div>

            
        </div>
    </div>

    <!-- Edit Selection Modal -->
    <div id="editSelectionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white w-full max-w-xl p-6 rounded-lg shadow-xl text-black transform scale-95 transition-all my-10" id="editSelectionBox">
            <div class="flex justify-between items-center border-b pb-2 mb-3">
                <h2 class="text-lg font-bold text-gray-800">Edit Selected Incomplete Records</h2>
                <button onclick="closeEditSelectionModal()" class="text-gray-600 text-2xl font-bold hover:text-gray-800">&times;</button>
            </div>
            <div class="text-sm">
                <div class="flex flex-wrap items-end gap-3 mb-3">
                    <div>
                        <label class="block text-xs text-gray-600 font-semibold" for="statusSelect">Status</label>
                        <select id="statusSelect" class="border border-gray-300 px-2 py-1 rounded text-black text-xs min-w-40"></select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 font-semibold" for="timeOutInput">Time Out</label>
                        <input id="timeOutInput" type="time" class="border border-gray-300 px-2 py-1 rounded text-black text-xs" step="60">
                    </div>
                    <div>
                        <button type="button" class="bg-blue-600 hover:bg-blue-500 text-white px-3 py-1.5 rounded text-xs font-medium shadow" onclick="applyEditSelection()">Apply</button>
                    </div>
                </div>
                <table class="w-full border text-xs">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-2 py-1">Date</th>
                            <th class="border px-2 py-1">Time In</th>
                            <th class="border px-2 py-1">Time Out</th>
                            <th class="border px-2 py-1">Status</th>
                        </tr>
                    </thead>
                    <tbody id="editSelectionTbody"></tbody>
                </table>
            </div>
            <div class="mt-4 flex justify-end gap-2">
                <button type="button" class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 rounded text-xs" onclick="closeEditSelectionModal()">Close</button>
            </div>
        </div>
    </div>

</div>

<script>
const employeeAttendanceData = @json($groupedAttendances);
const employeeNames = @json($employees->getCollection()->mapWithKeys(function($e){ return [$e->employeeprofiles_id => $e->first_name.' '.$e->last_name]; }));
let currentEmployeeId = null;
let currentRecords = [];
let currentEmployeeName = '';
let selectedIds = new Set();
let cachedStatuses = null;

function openModal(employeeId, employeeName) {
    const modal = document.getElementById("attendanceModal");
    const modalBox = document.getElementById("modalBox");
    const employeeNameDiv = document.getElementById("employeeName");
    employeeNameDiv.textContent = employeeName;
    currentEmployeeName = employeeName;
    currentEmployeeId = employeeId;
    selectedIds = new Set();
    currentRecords = employeeAttendanceData[String(employeeId)] || [];
    renderTable(getFilteredRecords());
    modal.classList.remove("hidden");
    setTimeout(() => modalBox.classList.remove("scale-95"), 50);
}

function renderTable(records) {
    const modalContent = document.getElementById("modalContent");

    if (!records.length) {
        modalContent.innerHTML = `<p class="text-center py-4 text-gray-500">No records available.</p>`;
        return;
    }

    modalContent.innerHTML = `
        <table class="w-full border text-xs mt-3 mb-3">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-2 py-1">Select</th>
                    <th class="border px-2 py-1">Date</th>
                    <th class="border px-2 py-1">Time In</th>
                    <th class="border px-2 py-1">Time Out</th>
                    <th class="border px-2 py-1">Status</th>
                </tr>
            </thead>
            <tbody>
                ${records.map(r => `
                    <tr class="hover:bg-gray-50">
                        <td class="border px-2 py-1 text-center">
                            ${r.status === 'Incomplete' ? `<input type="checkbox" class="incomplete-select" data-id="${r.id}" />` : `<input type="checkbox" disabled />`}
                        </td>
                        <td class="border px-2 py-1">${r.date}</td>
                        <td class="border px-2 py-1">${r.time_in || '-'}</td>
                        <td class="border px-2 py-1">${r.time_out || '-'}</td>
                        <td class="border px-2 py-1">${r.status}</td>
                    </tr>`).join('')}
            </tbody>
        </table>
    `;

    // Restore checked state for already selected rows after re-render
    document.querySelectorAll('#modalContent .incomplete-select').forEach(cb => {
        const id = cb.getAttribute('data-id');
        if (selectedIds.has(id)) cb.checked = true;
        cb.addEventListener('change', (e) => {
            const id = e.target.getAttribute('data-id');
            if (e.target.checked) {
                selectedIds.add(id);
            } else {
                selectedIds.delete(id);
            }
            updateEditSelectionButton();
        });
    });

    updateEditSelectionButton();
}

function closeModal() {
    const modal = document.getElementById("attendanceModal");
    modal.classList.add("hidden");
}


function parseDisplayDate(s) {
    const parts = s.split(' ');
    const months = {Jan:0,Feb:1,Mar:2,Apr:3,May:4,Jun:5,Jul:6,Aug:7,Sep:8,Oct:9,Nov:10,Dec:11};
    const day = parseInt(parts[1].replace(',', ''), 10);
    const year = parseInt(parts[2], 10);
    return new Date(year, months[parts[0]], day);
}

function parseYMD(s) {
    const a = s.split('-');
    return new Date(parseInt(a[0],10), parseInt(a[1],10)-1, parseInt(a[2],10));
}

function getFilteredRecords() {
    const fVal = document.getElementById('fromDate').value;
    const tVal = document.getElementById('toDate').value;
    const from = fVal ? parseYMD(fVal) : null;
    const to = tVal ? parseYMD(tVal) : null;
    return currentRecords.filter(function(r){
        const d = parseDisplayDate(r.date);
        if (from && d < from) return false;
        if (to && d > to) return false;
        return true;
    });
}

function updateEditSelectionButton() {
    const btn = document.getElementById('editSelectionBtn');
    const recs = getFilteredRecords();
    const hasIncomplete = recs.some(r => r.status === 'Incomplete');
    if (!btn) return;
    if (hasIncomplete) {
        btn.classList.remove('hidden');
        const disabled = selectedIds.size === 0;
        btn.disabled = disabled;
        if (disabled) {
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            btn.classList.remove('hover:bg-blue-500');
        } else {
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
            btn.classList.add('hover:bg-blue-500');
        }
    } else {
        btn.classList.add('hidden');
    }
}

function openEditSelectionModal() {
    const modal = document.getElementById('editSelectionModal');
    const box = document.getElementById('editSelectionBox');
    const tbody = document.getElementById('editSelectionTbody');
    const recs = getFilteredRecords().filter(r => r.status === 'Incomplete' && selectedIds.has(String(r.id)));
    if (!recs.length) return;
    tbody.innerHTML = recs.map(r => `
        <tr>
            <td class="border px-2 py-1">${r.date}</td>
            <td class="border px-2 py-1">${r.time_in || '-'}</td>
            <td class="border px-2 py-1">${r.time_out || '-'}</td>
            <td class="border px-2 py-1 text-red-600 font-semibold">${r.status}</td>
        </tr>
    `).join('');
    loadStatuses();
    modal.classList.remove('hidden');
    setTimeout(() => box.classList.remove('scale-95'), 50);
}

function closeEditSelectionModal() {
    const modal = document.getElementById('editSelectionModal');
    modal.classList.add('hidden');
}

async function loadStatuses() {
    if (!cachedStatuses) {
        try {
            const res = await fetch('/attendance/statuses');
            const data = await res.json();
            cachedStatuses = data.success ? data.statuses : [];
        } catch (e) {
            cachedStatuses = [];
        }
    }

    const sel = document.getElementById('statusSelect');
    if (!sel) return;

    sel.innerHTML = '';

    (cachedStatuses || []).forEach((s, idx) => {
        const opt = document.createElement('option');
        opt.value = s;
        opt.textContent = s;
        sel.appendChild(opt);
        // Automatically select the first option only
        if (idx === 0) sel.selectedIndex = 0;
    });
}



async function applyEditSelection() {
    const status = document.getElementById('statusSelect').value; // <-- current selected value
    const timeOut = document.getElementById('timeOutInput').value;
    if (!timeOut) return;

    const ids = Array.from(selectedIds).map(id => parseInt(id, 10));
    if (!ids.length) return;

    try {
        const res = await fetch('/attendance/admin-update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ attendance_ids: ids, time_out: timeOut, status })
        });
        const data = await res.json();
        if (data && data.success) {
            const updates = data.updates || {};
            currentRecords = currentRecords.map(r => {
                const u = updates[String(r.id)] || updates[r.id];
                if (u) {
                    r.time_out = u.time_out_display || r.time_out;
                    r.status = u.status || r.status;
                }
                return r;
            });
            selectedIds.clear();
            closeEditSelectionModal();
            renderTable(getFilteredRecords());
        }
    } catch (e) {
        console.error(e);
    }
}

function downloadBlob(content, filename, type) {
    const blob = new Blob([content], { type });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

function exportExcel() {
    const recs = getFilteredRecords();
    const header = ['Date','Time In','Time Out','Status'];
    const rows = recs.map(function(r){ return [r.date, r.time_in || '-', r.time_out || '-', r.status || '-']; });
    const csv = [header].concat(rows).map(function(row){
        return row.map(function(v){
            const s = String(v == null ? '' : v);
            const needQ = /[",\n]/.test(s);
            const esc = s.replace(/"/g, '""');
            return needQ ? '"' + esc + '"' : esc;
        }).join(',');
    }).join('\n');
    const f = document.getElementById('fromDate').value || 'all';
    const t = document.getElementById('toDate').value || 'all';
    const name = (currentEmployeeName || 'employee').replace(/[^a-z0-9]+/gi,'_');
    downloadBlob('\ufeff' + csv, name + '_attendance_' + f + '_to_' + t + '.csv', 'text/csv;charset=utf-8;');
}

function exportPdf() {
    const recs = getFilteredRecords();
    const f = document.getElementById('fromDate').value || '';
    const t = document.getElementById('toDate').value || '';
    const w = window.open('', '_blank');
    const title = 'Attendance';
    const name = currentEmployeeName || '';
    const range = (f || t) ? ('(' + (f || '...') + ' to ' + (t || '...') + ')') : '';
    const styles = '<style>body{font-family:Arial,sans-serif;color:#000;padding:16px;}h2{margin:0 0 8px 0;}table{width:100%;border-collapse:collapse;font-size:12px;}th,td{border:1px solid #000;padding:6px;text-align:left;}th{background:#eee}</style>';
    const rowsHtml = recs.map(function(r){
        return '<tr><td>' + r.date + '</td><td>' + (r.time_in || '-') + '</td><td>' + (r.time_out || '-') + '</td><td>' + (r.status || '-') + '</td></tr>';
    }).join('');
    const emptyHtml = '<tr><td colspan="4">No records</td></tr>';
    const html = '<html><head><title>' + title + '</title>' + styles + '</head><body><h2>' + name + ' Attendance ' + range + '</h2><table><thead><tr><th>Date</th><th>Time In</th><th>Time Out</th><th>Status</th></tr></thead><tbody>' + (rowsHtml || emptyHtml) + '</tbody></table></body></html>';
    w.document.open();
    w.document.write(html);
    w.document.close();
    w.focus();
    w.print();
}

document.getElementById('exportExcelBtn').addEventListener('click', exportExcel);
document.getElementById('exportPdfBtn').addEventListener('click', exportPdf);
document.getElementById('editSelectionBtn').addEventListener('click', openEditSelectionModal);

document.getElementById('fromDate').addEventListener('change', () => {
    renderTable(getFilteredRecords());
});
document.getElementById('toDate').addEventListener('change', () => {
    renderTable(getFilteredRecords());
});

// Global export: all employees on page within date range
function globalGetFilteredRows() {
    const fVal = document.getElementById('globalFromDate').value;
    const tVal = document.getElementById('globalToDate').value;
    const from = fVal ? parseYMD(fVal) : null;
    const to = tVal ? parseYMD(tVal) : null;
    const rows = [];
    Object.keys(employeeAttendanceData).forEach(function(empId){
        const name = employeeNames[empId] || ('ID:' + empId);
        const recs = employeeAttendanceData[empId] || [];
        recs.forEach(function(r){
            const d = parseDisplayDate(r.date);
            if (from && d < from) return;
            if (to && d > to) return;
            rows.push([name, r.date, r.time_in || '-', r.time_out || '-', r.status || '-']);
        });
    });
    return rows;
}

function globalExportExcel() {
    const header = ['Employee','Date','Time In','Time Out','Status'];
    const rows = globalGetFilteredRows();
    const csv = [header].concat(rows).map(function(row){
        return row.map(function(v){
            const s = String(v == null ? '' : v);
            const needQ = /[",\n]/.test(s);
            const esc = s.replace(/"/g, '""');
            return needQ ? '"' + esc + '"' : esc;
        }).join(',');
    }).join('\n');
    const f = document.getElementById('globalFromDate').value || 'all';
    const t = document.getElementById('globalToDate').value || 'all';
    downloadBlob('\ufeff' + csv, 'attendance_' + f + '_to_' + t + '.csv', 'text/csv;charset=utf-8;');
}

function globalExportPdf() {
    const rows = globalGetFilteredRows();
    const f = document.getElementById('globalFromDate').value || '';
    const t = document.getElementById('globalToDate').value || '';
    const w = window.open('', '_blank');
    const title = 'Attendance';
    const range = (f || t) ? ('(' + (f || '...') + ' to ' + (t || '...') + ')') : '';
    const styles = '<style>body{font-family:Arial,sans-serif;color:#000;padding:16px;}h2{margin:0 0 8px 0;}table{width:100%;border-collapse:collapse;font-size:12px;}th,td{border:1px solid #000;padding:6px;text-align:left;}th{background:#eee}</style>';
    const bodyRows = rows.map(function(r){
        return '<tr><td>' + r[0] + '</td><td>' + r[1] + '</td><td>' + r[2] + '</td><td>' + r[3] + '</td><td>' + r[4] + '</td></tr>';
    }).join('');
    const emptyHtml = '<tr><td colspan="5">No records</td></tr>';
    const html = '<html><head><title>' + title + '</title>' + styles + '</head><body><h2>Daily Attendance ' + range + '</h2><table><thead><tr><th>Employee</th><th>Date</th><th>Time In</th><th>Time Out</th><th>Status</th></tr></thead><tbody>' + (bodyRows || emptyHtml) + '</tbody></table></body></html>';
    w.document.open();
    w.document.write(html);
    w.document.close();
    w.focus();
    w.print();
}

document.getElementById('globalExportExcelBtn').addEventListener('click', globalExportExcel);
document.getElementById('globalExportPdfBtn').addEventListener('click', globalExportPdf);
</script>
</x-guest-layout>
