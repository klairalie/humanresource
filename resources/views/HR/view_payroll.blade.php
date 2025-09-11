
<x-guest-layout>
    <div class="min-h-screen p-6">

        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <h1 class="text-2xl font-bold mb-4 md:mb-0 text-black">Payroll Records</h1>

            <form method="GET" action="{{ route('view.payroll') }}" class="flex items-center space-x-4">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by name, ID, or pay period..."
                    class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500 w-64">
            </form>

            <div class="flex space-x-2 mt-2 md:mt-0">
                <a href="{{ route('show.deductionform') }}">
                    <button class="px-4 py-2 font-sans bg-gradient-to-r from-orange-100 via-orange-300 to-orange-200 hover:from-orange-300 hover:to-orange-100 text-black rounded-lg">+ Deduction</button>
                </a>
                <a href="{{ route('view.deductionrecords') }}">
                    <button class="px-4 py-2 font-sans bg-gradient-to-r from-orange-100 via-orange-300 to-orange-200 hover:from-orange-300 hover:to-orange-100 text-black rounded-lg">Manage
                        Deduction</button>
                </a>
            </div>
        </div>

        <!-- Payroll Table -->
        <div class="overflow-x-auto shadow-md rounded-xl mb-10">
            <table class="min-w-full text-left">
                <thead>
                    <tr class="text-black font-semibold bg-white text-center">
                        <th class="px-6 py-4">Employee ID</th>
                        <th class="px-6 py-4">First Name</th>
                        <th class="px-6 py-4">Last Name</th>
                        <th class="px-6 py-4">Position</th>
                        <th class="px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-black">
                    @forelse ($payroll as $payrolls)
                        <tr class=" hover:bg-gray-400 text-center font-semibold">
                            <td class="px-6 py-3">{{ $payrolls->employeeprofiles?->employeeprofiles_id }}</td>
                            <td class="px-6 py-3">{{ $payrolls->employeeprofiles?->first_name ?? 'No Name' }}</td>
                            <td class="px-6 py-3">{{ $payrolls->employeeprofiles?->last_name ?? '' }}</td>
                            <td class="px-6 py-3">{{ $payrolls->employeeprofiles?->position }}</td>
                            <td class="px-6 py-3 space-x-4">
                                <button
                                    onclick="openDetailsModal({{ $payrolls->employeeprofiles?->employeeprofiles_id }})"
                                    class="text-black font-semibold hover:underline text-center">
                                    View Details
                                </button>
                                 

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-6 text-gray-500">No payroll records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">{{ $payroll->links() }}</div>
    </div>

    <!-- ✅ View Details Modal -->
    <div id="payrollModal" class="fixed inset-0 hidden bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-11/12 md:w-3/4 lg:w-2/3 p-6 max-h-[80vh] overflow-y-auto">
            <h2 class="text-xl font-bold mb-4 text-black">Payroll Records</h2>
            <table class="w-full border border-gray-300">
                <thead class="bg-gray-200 text-black font-semibold">
                    <tr>
                        <th class="px-4 py-2">Payroll ID</th>
                        <th class="px-4 py-2">Pay Period</th>
                        <th class="px-4 py-2">Total Days of Work</th>
                        <th class="px-4 py-2">Start</th>
                        <th class="px-4 py-2">End</th>
                        <th class="px-4 py-2">Basic Salary</th>
                        <th class="px-4 py-2">Overtime</th>
                        <th class="px-4 py-2">Deductions</th>
                        <th class="px-4 py-2">Bonuses</th>
                        <th class="px-4 py-2">Status</th>
                    </tr>
                </thead>
                <tbody id="payrollRecordsBody" class="text-black"></tbody>
            </table>
            <div class="mt-4 text-right">
                <button onclick="closeDetailsModal()"
                    class="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600">
                    Close
                </button>
            </div>
        </div>
    </div>


    <div id="updatePayrollModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg relative p-6">
            <!-- Close Button -->
            <button onclick="closeUpdateModal()"
                class="absolute top-3 right-3 text-gray-600 hover:text-black text-xl font-bold">&times;</button>

            <h2 class="text-xl font-bold mb-4 text-black">Update Payroll</h2>

            <div id="updateModalContent" class="space-y-2 text-black">
                <!-- Filled dynamically -->
            </div>

            <div class="flex justify-end mt-6">
                <button id="updatePayrollBtn"
                    class="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600">Update</button>
                <button id="submitPayrollBtn"
                    class="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600">Submit</button>
            </div>
        </div>
    </div>

    <script>
        // ================== VIEW DETAILS ==================
        function openDetailsModal(employeeId) {
            fetch(`/payroll/records/${employeeId}`)
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById("payrollRecordsBody");
                    tbody.innerHTML = "";

                    if (data.length === 0) {
                        tbody.innerHTML =
                            `<tr><td colspan="9" class="text-center py-4">No payroll records found.</td></tr>`;
                    } else {
                        data.forEach(record => {
                            tbody.innerHTML += `
                            <tr class="border-b">
                                <td class="px-4 py-2">${record.payroll_id}</td>
                                <td class="px-4 py-2">${record.pay_period}</td>
                                <td class="px-4 py-2">${record.total_days_of_work}</td>
                                <td class="px-4 py-2">${record.pay_period_start}</td>
                                <td class="px-4 py-2">${record.pay_period_end}</td>
                                <td class="px-4 py-2">${record.basic_salary}</td>
                                <td class="px-4 py-2">${record.overtime_pay}</td>
                                <td class="px-4 py-2">Cash-advance: ${record.deductions}</td>
                                <td class="px-4 py-2">${record.bonuses}</td>
                                <td class="px-4 py-2">${record.status}</td>
                            </tr>
                        `;
                        });
                    }

                    document.getElementById("payrollModal").classList.remove("hidden");
                });
        }

        function closeDetailsModal() {
            document.getElementById("payrollModal").classList.add("hidden");
        }

        // ================== UPDATE PAYROLL ==================
        let currentPayroll = null;

        function openUpdateModal(data) {
            currentPayroll = data;

            let content = `
            <p><strong>Employee ID:</strong> ${data.employeeprofiles?.employeeprofiles_id ?? ''}</p>
            <p><strong>First Name:</strong> ${data.employeeprofiles?.first_name ?? ''}</p>
            <p><strong>Last Name:</strong> ${data.employeeprofiles?.last_name ?? ''}</p>
            <p><strong>Position:</strong> ${data.employeeprofiles?.position ?? ''}</p>
            <p><strong>Total Days of Work:</strong> ${data.total_days_of_work ?? ''}</p>
            <p><strong>Pay Period:</strong> ${data.pay_period ?? ''}</p>
            <p><strong>Basic Salary:</strong> ${data.basic_salary ?? ''}</p>
            <p><strong>Overtime Pay:</strong> ${data.overtime_pay ?? ''}</p>
            <p><strong>Deductions:</strong> ${data.deductions ?? ''}</p>
            <p><strong>Bonuses:</strong> 
                <input type="text" id="bonusInput" value="${data.bonuses ?? ''}" 
                class="border border-gray-300 rounded-md px-2 py-1 w-32" />
            </p>
            <p><strong>Status:</strong> ${data.status ?? ''}</p>
        `;

            document.getElementById('updateModalContent').innerHTML = content;
            document.getElementById('updatePayrollModal').classList.remove('hidden');
        }

        function closeUpdateModal() {
            document.getElementById('updatePayrollModal').classList.add('hidden');
        }

        document.getElementById('submitPayrollBtn').addEventListener('click', function() {
            if (!currentPayroll) return;

            const bonusValue = document.getElementById('bonusInput').value;

            const payload = {
                employeeprofiles_id: currentPayroll.employeeprofiles?.employeeprofiles_id,
                total_days_of_work: currentPayroll.total_days_of_work,
                basic_salary: currentPayroll.basic_salary,
                overtime_pay: currentPayroll.overtime_pay,
                deductions: currentPayroll.deductions,
                bonuses: bonusValue,
                pay_period_start: currentPayroll.pay_period_start,
                pay_period_end: currentPayroll.pay_period_end,
                pay_period: currentPayroll.pay_period
            };

            fetch("{{ route('store.payroll') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    closeUpdateModal();
                    window.location.reload(); // ✅ refresh table
                })
                .catch(err => {
                    console.error(err);
                    alert('Failed to store payroll record.');
                });
        });
    </script>
</x-guest-layout>
