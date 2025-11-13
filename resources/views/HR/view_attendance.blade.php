<x-guest-layout>
<!-- Top Navigation Buttons (Right Aligned) -->
<div class="flex justify-end gap-4 mt-10 mb-8 mr-35">
    <a href="{{ route('show.leaverequest') }}"
        class="flex items-center gap-4 bg-[#2C2C2C] text-white text-sm font-semibold px-10 py-3.5 shadow-md transition-all duration-200 ease-in-out hover:scale-[1.02]">
        <i class="fa-solid fa-calendar-check text-white"></i>
        <span>Manage Leave Requests</span>
    </a>

    <a href="{{ route('show.overtime') }}"
        class="flex items-center gap-4 bg-[#2C2C2C] text-white text-sm font-semibold px-10 py-3.5 shadow-md transition-all duration-200 ease-in-out hover:scale-[1.02]">
        <i class="fa-solid fa-clock text-white"></i>
        <span>Manage Overtime</span>
    </a>
</div>


    <!-- Main Container -->
    <div class="max-w-5xl mx-auto bg-white p-15 mt-6 mb-35 shadow-lg border border-gray-200 text-black">
        <h1 class="text-3xl font-extrabold mb-4 md:mb-3 tracking-wide text-black flex items-center gap-2">Daily Attendance Record</h1>

        <!-- Employee Table -->
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
                            <button
                                type="button"
                                onclick='openModal({{ $employee->employeeprofiles_id }}, {!! json_encode($employee->first_name . " " . $employee->last_name) !!})'
                                class="inline-flex items-center gap-1 bg-gray-700 hover:bg-gray-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium shadow transition duration-200 ease-in-out hover:scale-105">
                                <i class="fa-solid fa-eye text-white"></i>
                                View Details
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-4 text-gray-600">No employees found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

       <div class="mt-6 mb-10">
    {{ $employees->appends(request()->query())->links() }}
</div>


    <!-- Modal -->
    <div id="attendanceModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-xl text-black transform scale-95 transition-all my-10"
            id="modalBox">
            <div class="flex justify-between items-center border-b pb-2 mb-3">
                <h2 id="modalTitle" class="text-lg font-bold text-gray-800">Attendance Details</h2>
                <button onclick="closeModal()" class="text-gray-600 text-2xl font-bold hover:text-gray-800">&times;</button>
            </div>

            <div id="employeeName" class="font-semibold mb-3 text-center text-blue-700"></div>

            <!-- Date Filters -->
            <div class="flex gap-3 mb-4 justify-center text-sm">
                <div>
                    <label for="fromDate" class="block text-gray-600 text-xs font-semibold">From:</label>
                    <input type="date" id="fromDate" class="border border-gray-300 px-2 py-1 rounded text-black focus:ring focus:ring-blue-200">
                </div>
                <div>
                    <label for="toDate" class="block text-gray-600 text-xs font-semibold">To:</label>
                    <input type="date" id="toDate" class="border border-gray-300 px-2 py-1 rounded text-black focus:ring focus:ring-blue-200">
                </div>
            </div>

            <!-- Content -->
            <div id="modalContent" class="text-sm overflow-y-auto max-h-80 mt-4 mb-4">
                <p class="text-center py-4 text-gray-500">Loading attendance records...</p>
            </div>
        </div>
    </div>

    <!-- JavaScript (unchanged) -->
    <script>
        const employeeAttendanceData = @json($groupedAttendances);
        console.log("Loaded attendance data:", employeeAttendanceData);

        let currentEmployeeId = null;
        let currentRecords = [];

        function openModal(employeeId, employeeName) {
            const modal = document.getElementById("attendanceModal");
            const modalBox = document.getElementById("modalBox");
            const modalTitle = document.getElementById("modalTitle");
            const modalContent = document.getElementById("modalContent");
            const employeeNameDiv = document.getElementById("employeeName");

            modalTitle.textContent = `${employeeName}'s Attendance`;
            employeeNameDiv.textContent = employeeName;

            currentEmployeeId = employeeId;
            currentRecords = employeeAttendanceData[String(employeeId)] || [];

            if (!currentRecords.length) {
                modalContent.innerHTML = `<p class="text-center py-4 text-gray-500">No attendance records available for this employee.</p>`;
            } else {
                renderTable(currentRecords);
            }

            const fromDate = document.getElementById("fromDate");
            const toDate = document.getElementById("toDate");
            fromDate.value = "";
            toDate.value = "";
            fromDate.onchange = filterByDate;
            toDate.onchange = filterByDate;

            modal.classList.remove("hidden");
            setTimeout(() => modalBox.classList.remove("scale-95"), 50);
        }

        function renderTable(records) {
            const modalContent = document.getElementById("modalContent");
            if (!records.length) {
                modalContent.innerHTML = `<p class="text-center py-4 text-gray-500">No attendance records found for the selected date range.</p>`;
                return;
            }

            modalContent.innerHTML = `
                <table class="w-full border text-xs text-black mt-3 mb-3 rounded">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-2 py-1 font-semibold text-gray-700">Date</th>
                            <th class="border px-2 py-1 font-semibold text-gray-700">Time In</th>
                            <th class="border px-2 py-1 font-semibold text-gray-700">Time Out</th>
                            <th class="border px-2 py-1 font-semibold text-gray-700">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${records.map(r => `
                            <tr class="hover:bg-gray-50">
                                <td class="border px-2 py-1">${r.date}</td>
                                <td class="border px-2 py-1">${r.time_in || '-'}</td>
                                <td class="border px-2 py-1">${r.time_out || '-'}</td>
                                <td class="border px-2 py-1">${r.status}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        }

        function filterByDate() {
            const fromDate = document.getElementById("fromDate").value;
            const toDate = document.getElementById("toDate").value;
            if (!fromDate || !toDate) return;

            const from = new Date(fromDate);
            const to = new Date(toDate);
            to.setHours(23, 59, 59, 999);

            const filtered = currentRecords.filter(r => {
                const recordDate = new Date(r.date.replace(" ", "T"));
                return recordDate >= from && recordDate <= to;
            });

            renderTable(filtered);
        }

        function closeModal() {
            const modal = document.getElementById("attendanceModal");
            const modalBox = document.getElementById("modalBox");
            modalBox.classList.add("scale-95");
            setTimeout(() => modal.classList.add("hidden"), 150);
        }

        document.getElementById("attendanceModal").addEventListener("click", e => {
            if (e.target.id === "attendanceModal") closeModal();
        });
    </script>
</x-guest-layout>
