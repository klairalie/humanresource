<x-guest-layout>
<!-- Top Navigation Buttons -->
<div class="flex justify-end gap-4 mt-10 mb-8 mr-35">
    <a href="{{ route('show.leaverequest') }}" class="flex items-center gap-4 bg-[#2C2C2C] text-white text-sm font-semibold px-10 py-3.5 shadow-md hover:scale-[1.02]">
        <i class="fa-solid fa-calendar-check"></i> Manage Leave Requests
    </a>
    <a href="{{ route('show.overtime') }}" class="flex items-center gap-4 bg-[#2C2C2C] text-white text-sm font-semibold px-10 py-3.5 shadow-md hover:scale-[1.02]">
        <i class="fa-solid fa-clock"></i> Manage Overtime
    </a>
</div>

<div class="max-w-5xl mx-auto bg-white p-15 mt-6 mb-35 shadow-lg border border-gray-200 text-black">
    <h1 class="text-3xl font-extrabold mb-4 tracking-wide text-black flex items-center gap-2">
        Daily Attendance Record
    </h1>

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
            </div>

            <div id="modalContent" class="text-sm overflow-y-auto max-h-80 mt-4 mb-4">
                <p class="text-center py-4 text-gray-500">Loading attendance records...</p>
            </div>

            <div class="text-right">
                <button id="editSelectedBtn" onclick="openEditModal()" disabled
                    class="bg-blue-700 hover:bg-blue-600 text-white px-4 py-2 rounded text-xs font-medium shadow transition">
                    Edit Selected
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-[60]">
        <div class="bg-white w-full max-w-sm p-6 rounded-lg shadow-lg text-black">
            <h3 class="text-lg font-bold mb-3 text-center">Manual Attendance Correction</h3>
            <p class="text-sm mb-4 text-center text-gray-600">Set the selected dates to Present with fixed Time In/Out.</p>

            <form id="editForm">
                <div class="space-y-3 mb-5">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="setPresent" checked>
                        <label for="setPresent" class="text-sm">Mark as Present</label>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 block">Time In:</label>
                        <input type="text" value="07:00 AM" disabled class="border px-2 py-1 w-full text-sm rounded">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 block">Time Out:</label>
                        <input type="text" value="05:00 PM" disabled class="border px-2 py-1 w-full text-sm rounded">
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeEditModal()" class="px-3 py-1 text-sm bg-gray-300 hover:bg-gray-400 rounded">Cancel</button>
                    <button type="submit" class="px-4 py-1 text-sm bg-blue-700 hover:bg-blue-600 text-white rounded">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const employeeAttendanceData = @json($groupedAttendances);
let currentEmployeeId = null;
let currentRecords = [];
let selectedDates = [];

function openModal(employeeId, employeeName) {
    const modal = document.getElementById("attendanceModal");
    const modalBox = document.getElementById("modalBox");
    const employeeNameDiv = document.getElementById("employeeName");
    employeeNameDiv.textContent = employeeName;
    currentEmployeeId = employeeId;
    currentRecords = employeeAttendanceData[String(employeeId)] || [];
    renderTable(currentRecords);
    modal.classList.remove("hidden");
    setTimeout(() => modalBox.classList.remove("scale-95"), 50);
}

function renderTable(records) {
    const modalContent = document.getElementById("modalContent");
    const editButton = document.getElementById("editSelectedBtn");
    selectedDates = [];
    editButton.disabled = true;

    if (!records.length) {
        modalContent.innerHTML = `<p class="text-center py-4 text-gray-500">No records available.</p>`;
        return;
    }

    modalContent.innerHTML = `
        <table class="w-full border text-xs mt-3 mb-3">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-2 py-1"><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
                    <th class="border px-2 py-1">Date</th>
                    <th class="border px-2 py-1">Time In</th>
                    <th class="border px-2 py-1">Time Out</th>
                    <th class="border px-2 py-1">Status</th>
                </tr>
            </thead>
            <tbody>
                ${records.map(r => `
                    <tr class="hover:bg-gray-50">
                        <td class="border px-2 py-1 text-center"><input type="checkbox" class="recordCheckbox" data-date="${r.date}" onchange="updateSelected()"></td>
                        <td class="border px-2 py-1">${r.date}</td>
                        <td class="border px-2 py-1">${r.time_in || '-'}</td>
                        <td class="border px-2 py-1">${r.time_out || '-'}</td>
                        <td class="border px-2 py-1">${r.status}</td>
                    </tr>`).join('')}
            </tbody>
        </table>
    `;
}

function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.recordCheckbox');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
    updateSelected();
}

function updateSelected() {
    selectedDates = Array.from(document.querySelectorAll('.recordCheckbox:checked')).map(cb => cb.dataset.date);
    document.getElementById('editSelectedBtn').disabled = selectedDates.length === 0;
}

function closeModal() {
    const modal = document.getElementById("attendanceModal");
    modal.classList.add("hidden");
}

// Sub Modal
function openEditModal() {
    document.getElementById('editModal').classList.remove('hidden');
}
function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

// Handle Manual Update
document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    if (selectedDates.length === 0) return;

    fetch(`/attendance/manual-update`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            employee_id: currentEmployeeId,
            dates: selectedDates
        })
    }).then(res => res.json()).then(data => {
        alert(data.message);
        closeEditModal();
        closeModal();
        location.reload();
    }).catch(err => console.error(err));
});
</script>
</x-guest-layout>
