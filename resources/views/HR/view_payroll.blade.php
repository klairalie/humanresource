<x-guest-layout>
<div class="min-h-screen p-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <h1 class="text-2xl font-bold mb-4 md:mb-0 text-black">Payroll Records</h1>
        <form method="GET" action="{{ route('view.payroll') }}" class="flex items-center space-x-4">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search by name, ID, or pay period..."
                class="px-4 py-2 border border-gray-900 focus:outline-none focus:ring-2 focus:ring-amber-500 w-64 text-black">
        </form>
    </div>

    <meta name="csrf-token" content="{{ csrf_token() }}">

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
                    <tr class="hover:bg-gray-100 text-center font-semibold">
                        <td class="px-6 py-3">{{ $payrolls->employeeprofiles?->employeeprofiles_id }}</td>
                        <td class="px-6 py-3">{{ $payrolls->employeeprofiles?->first_name ?? 'No Name' }}</td>
                        <td class="px-6 py-3">{{ $payrolls->employeeprofiles?->last_name ?? '' }}</td>
                        <td class="px-6 py-3">{{ $payrolls->employeeprofiles?->position }}</td>
                        <td class="px-6 py-3 space-x-4">
                            <button type="button"
                                onclick="openDetailsModal('{{ $payrolls->employeeprofiles?->employeeprofiles_id }}')"
                                class="text-amber-600 font-semibold hover:underline">
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

    <div class="mt-6">{{ $payroll->links() }}</div>
</div>

<!-- Payroll Details Modal -->
<div id="payrollModal"
    class="fixed inset-0 hidden bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50">
    
    <div
        class="bg-white shadow-xl w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 p-6 border border-gray-300 transform transition-all duration-300 scale-100 rounded-none">
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-300">
            <h2 id="payrollModalTitle" class="text-2xl font-semibold text-gray-800 flex items-center gap-2">
                Employee Payroll Details
            </h2>
            <button onclick="closeDetailsModal()"
                class="text-gray-500 hover:text-black transition text-3xl font-light leading-none">
                &times;
            </button>
        </div>

        <!-- Table Section -->
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-300 text-sm text-gray-800">
                <thead class="bg-gray-100 uppercase text-xs font-semibold tracking-wider text-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left">Payroll ID</th>
                        <th class="px-4 py-2 text-left">Pay Period</th>
                        <th class="px-4 py-2 text-left">Days Worked</th>
                        <th class="px-4 py-2 text-left">Start</th>
                        <th class="px-4 py-2 text-left">End</th>
                        <th class="px-4 py-2 text-left">Salary Rate</th>
                        <th class="px-4 py-2 text-left">Basic Salary</th>
                        <th class="px-4 py-2 text-left">Overtime Pay</th>
                        <th class="px-4 py-2 text-left">Gross Pay</th>
                        <th class="px-4 py-2 text-left">Deductions</th>
                        <th class="px-4 py-2 text-left">Bonuses</th>
                        <th class="px-4 py-2 text-left">Bonus Amount</th>
                        <th class="px-4 py-2 text-left">Net Pay</th>
                        <th class="px-4 py-2 text-left">Action</th>
                        <th class="px-4 py-2 text-left">Status</th>
                    </tr>
                </thead>
                <tbody id="payrollRecordsBody" class="divide-y divide-gray-200 bg-gray-50">
                    <!-- Filled dynamically -->
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="mt-6 flex justify-end">
            <button onclick="closeDetailsModal()"
                class="px-5 py-2 bg-gray-200 text-gray-700 font-medium hover:bg-gray-300 transition">
                Close
            </button>
        </div>
    </div>
</div>


<script>
async function openDetailsModal(employeeId) {
    try {
        const response = await fetch(`/payroll/records/${employeeId}`);
        if (!response.ok) { alert('No record found or route missing.'); return; }

        const data = await response.json();
        const tbody = document.getElementById("payrollRecordsBody");
        const title = document.getElementById("payrollModalTitle");
        tbody.innerHTML = "";

        if (data.length === 0) {
            title.textContent = "📋 Employee Payroll Details";
            tbody.innerHTML = `<tr><td colspan="15" class="text-center py-4">No payroll records found.</td></tr>`;
        } else {
            const employee = data[0].employeeprofiles;
            const fullName = employee ? `${employee.first_name} ${employee.last_name}` : 'Unknown Employee';
            title.textContent = `📋 Employee Payroll Details — ${fullName}`;

            data.forEach(record => {
                tbody.innerHTML += `
<tr class="hover:bg-gray-100 transition-colors">
    <td class="px-4 py-2 font-medium text-gray-800">${record.payroll_id}</td>
    <td class="px-4 py-2">${record.pay_period}</td>
    <td class="px-4 py-2">${record.total_days_of_work}</td>
    <td class="px-4 py-2">${record.pay_period_start}</td>
    <td class="px-4 py-2">${record.pay_period_end}</td>
    <td class="px-4 py-2">${record.salary_rate}</td>
    <td class="px-4 py-2">${record.basic_salary}</td>
    <td class="px-4 py-2">${record.overtime_pay}</td>
    <td class="px-4 py-2">${record.gross_pay}</td>
    <td class="px-4 py-2">
        SSS: ${record.sss_contribution} <br>
        PhilHealth: ${record.philhealth_contribution} <br>
        Pag-IBIG: ${record.pagibig_contribution} <br>
        Tax: ${record.tax_deduction} <br>
        <strong>Total: ${record.deductions}</strong>
    </td>
    <td class="px-4 py-2">
        <select class="bonus-select border border-gray-300 rounded-md px-2 py-1 text-sm focus:ring-amber-500 focus:border-amber-500"
            data-payroll-id="${record.payroll_id}">
            <option value="">Select Bonus</option>
            <option value="13th Month Pay(Mandatory)" ${record.bonuses === '13th Month Pay(Mandatory)' ? 'selected' : ''}>13th Month Pay(Mandatory)</option>
            <option value="Christmas Bonus" ${record.bonuses === 'Christmas Bonus' ? 'selected' : ''}>Christmas Bonus</option>
        </select>
    </td>
    <td class="px-4 py-2">
        <input type="number" step="0.01"
            class="bonus-amount border border-gray-300 rounded-md px-2 py-1 w-24 text-sm focus:ring-amber-500 focus:border-amber-500"
            value="${record.bonus_amount ?? ''}"
            data-payroll-id="${record.payroll_id}"
            ${record.bonuses ? '' : 'disabled'}>
    </td>
    <td class="px-4 py-2 font-semibold text-green-600">${record.net_pay}</td>
    <td class="px-4 py-2 text-center">
        <button class="save-bonus bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-md text-xs font-medium transition"
            data-payroll-id="${record.payroll_id}">
            Save
        </button>
    </td>
    <td class="px-4 py-2 font-semibold ${record.status === 'Approved' ? 'text-green-600' : 'text-gray-600'}">
        ${record.status}
    </td>
</tr>`;
            });
        }

        document.getElementById("payrollModal").classList.remove("hidden");
        setupBonusEvents();

    } catch (error) {
        console.error(error);
        alert('Error loading payroll details.');
    }
}

function setupBonusEvents() {
    document.querySelectorAll('.bonus-select').forEach(select => {
        select.addEventListener('change', function() {
            const payrollId = this.dataset.payrollId;
            const bonusType = this.value;
            const bonusInput = document.querySelector(`.bonus-amount[data-payroll-id="${payrollId}"]`);
            const row = this.closest('tr');

            bonusInput.disabled = !bonusType;

            if (bonusType === '13th Month Pay(Mandatory)') {
                // Get basic salary from the row
                const basicSalaryCell = row.querySelector('td:nth-child(7)');
                const basicSalary = parseFloat(basicSalaryCell.textContent) || 0;

                // Calculate 13th month pay (basicSalary / 12)
                const bonusAmount = basicSalary / 12;
                bonusInput.value = bonusAmount.toFixed(2);
            } else if (bonusType === 'Christmas Bonus') {
                // You can set a fixed amount or leave empty for manual input
                bonusInput.value = '';
            } else {
                bonusInput.value = '';
            }
        });
    });

    // Save bonus event
    document.querySelectorAll('.save-bonus').forEach(button => {
        button.addEventListener('click', async function() {
            const payrollId = this.dataset.payrollId;
            const bonusSelect = document.querySelector(`.bonus-select[data-payroll-id="${payrollId}"]`);
            const bonusInput = document.querySelector(`.bonus-amount[data-payroll-id="${payrollId}"]`);
            const bonusType = bonusSelect.value;
            const bonusAmount = bonusInput.value;

            if (!bonusType || !bonusAmount || parseFloat(bonusAmount) <= 0) {
                alert('Please select a bonus type and enter a valid bonus amount.');
                return;
            }

            await updatePayrollBonus(payrollId, bonusType, bonusAmount);
        });
    });
}

async function updatePayrollBonus(payrollId, bonusType, bonusAmount) {
    try {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const response = await fetch(`/payroll/store`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token },
            body: new URLSearchParams({
                payroll_id: payrollId,
                bonuses: bonusType,
                bonus_amount: bonusAmount
            })
        });

        const result = await response.json();
        if (result.success) {
            alert('✅ Bonus updated successfully!');
        } else {
            alert('⚠️ Failed to update bonus.');
        }
    } catch (error) {
        console.error(error);
        alert('⚠️ Error updating bonus.');
    }
}

function closeDetailsModal() {
    document.getElementById("payrollModal").classList.add("hidden");
}
</script>
</x-guest-layout>
