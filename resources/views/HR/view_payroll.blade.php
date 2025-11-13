<x-guest-layout>
<div class="min-h-screen p-6 text-white">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <h1 class="text-3xl font-extrabold mb-4 md:mb-0 tracking-wide text-black flex items-center gap-2">
            <i data-lucide="file-text" class="w-6 h-6 text-black"></i> Payroll Records
        </h1>

        <div class="flex items-center space-x-4">
            <form method="GET" action="{{ route('view.payroll') }}" class="flex items-center space-x-2">
                <div class="relative">
                    <i data-lucide="search" class="absolute left-2 top-2.5 w-4 h-4 text-black"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search name, ID, or pay period..."
                        class="pl-8 pr-4 py-2 border border-gray-400 focus:ring-2 focus:ring-gray-300 focus:border-gray-300 w-64 text-black bg-gray-100 shadow-sm transition">
                </div>
            </form>

            <button type="button" onclick="openManageBonusModal()"
                class="px-4 py-2 bg-gray-700 text-white font-semibold hover:bg-gray-800 transition-all shadow-md flex items-center gap-2">
                <i data-lucide="settings" class="w-5 h-5 text-white"></i> Manage Bonus
            </button>
        </div>
    </div>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Payroll Table -->
    <div class="overflow-x-auto shadow-lg border border-gray-400 bg-gray-600 text-white">
        <table class="min-w-full text-left">
            <thead>
                <tr class="bg-gray-700 text-white uppercase text-sm font-bold text-center">
                    <th class="px-6 py-4">Employee ID</th>
                    <th class="px-6 py-4">First Name</th>
                    <th class="px-6 py-4">Last Name</th>
                    <th class="px-6 py-4">Position</th>
                    <th class="px-6 py-4">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-400 text-center">
                @forelse ($payroll as $payrolls)
                    <tr class="bg-gray-300 text-black hover:bg-gray-400 transition font-medium">
                        <td class="px-6 py-3">{{ $payrolls->employeeprofiles?->employeeprofiles_id }}</td>
                        <td class="px-6 py-3">{{ $payrolls->employeeprofiles?->first_name ?? 'No Name' }}</td>
                        <td class="px-6 py-3">{{ $payrolls->employeeprofiles?->last_name ?? '' }}</td>
                        <td class="px-6 py-3">{{ $payrolls->employeeprofiles?->position }}</td>
                        <td class="px-6 py-3">
                            <button type="button"
                                onclick="openDetailsModal('{{ $payrolls->employeeprofiles?->employeeprofiles_id }}')"
                                class="text-black hover:text-gray-800 hover:underline font-semibold transition flex items-center justify-center gap-1">
                                <i data-lucide="eye" class="w-4 h-4 text-black"></i> View Details
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr class="bg-gray-300 text-black">
                        <td colspan="5" class="text-center py-6">No payroll records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $payroll->links() }}</div>
</div>


<!-- ✅ TOAST ALERT SYSTEM -->
<div id="toastContainer" class="fixed top-5 right-5 space-y-3 z-[100]"></div>

<!-- ==================== PAYROLL DETAILS MODAL ==================== -->
<div id="payrollModal"
    class="fixed inset-0 hidden bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center z-50 transition">
    <div class="bg-gray-600 text-white shadow-2xl w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 p-6 border border-gray-400 transform transition-all duration-300 scale-100">

        <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-400">
            <h2 id="payrollModalTitle" class="text-2xl font-bold flex items-center gap-2">
                <i data-lucide="clipboard-list" class="w-6 h-6 text-white"></i> Employee Payroll Details
            </h2>
            <button onclick="closeDetailsModal()" class="text-white hover:text-gray-300 text-3xl font-light leading-none">&times;</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border border-gray-400 text-sm">
                <thead class="bg-gray-700 uppercase text-xs font-semibold tracking-wider text-white">
                    <tr>
                        <th class="px-4 py-2 text-left">Payroll ID</th>
                        <th class="px-4 py-2 text-left">Pay Period</th>
                        <th class="px-4 py-2 text-left">Days Worked</th>
                        <th class="px-4 py-2 text-left">Start</th>
                        <th class="px-4 py-2 text-left">End</th>
                        <th class="px-4 py-2 text-left">Salary Rate</th>
                        <th class="px-4 py-2 text-left">Basic Salary</th>
                        <th class="px-4 py-2 text-left">Basic Salary Tax</th>
                        <th class="px-4 py-2 text-left">Overtime Pay</th>
                        <th class="px-4 py-2 text-left">Deductions</th>
                        <th class="px-4 py-2 text-left">Bonuses</th>
                        <th class="px-4 py-2 text-left">Bonus Amount</th>
                        <th class="px-4 py-2 text-left">Gross Pay</th>
                        <th class="px-4 py-2 text-left">Net Pay</th>
                        <th class="px-4 py-2 text-left">Status</th>
                    </tr>
                </thead>
                <tbody id="payrollRecordsBody" class="divide-y divide-gray-500 bg-gray-200"></tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-end">
            <button onclick="closeDetailsModal()"
                class="px-5 py-2 bg-gray-700 text-white font-medium hover:bg-gray-800 transition">
                Close
            </button>
        </div>
    </div>
</div>

<!-- ==================== MANAGE BONUS MODAL ==================== -->
<div id="manageBonusModal"
    class="fixed inset-0 hidden bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-gray-600 text-white shadow-2xl w-96 p-6 border border-gray-400 transform transition-all duration-300">
        <div class="flex justify-between items-center border-b border-gray-400 pb-2 mb-4">
            <h2 class="text-xl font-semibold flex items-center gap-2">
                <i data-lucide="gift" class="w-5 h-5 text-white"></i> Manage Bonus
            </h2>
            <button onclick="closeManageBonusModal()" class="text-white hover:text-gray-300 text-2xl">&times;</button>
        </div>

        <form id="manageBonusForm" class="space-y-4">
            <div>
                <label for="bonusType" class="block text-sm font-medium text-white mb-1">Select Bonus Type</label>
                <select id="bonusType" name="bonusType"
                    class="w-full border border-gray-400 px-3 py-2 focus:ring-gray-300 focus:border-gray-300 text-black bg-gray-100"
                    onchange="toggleBonusAmount()">
                    <option value="">-- Select Bonus --</option>
                    <option value="Holiday (regular)">Holiday (regular)</option>
                    <option value="Holiday (special)">Holiday (special)</option>
                    <option value="13th Month Pay (Mandatory)">13th Month Pay (Mandatory)</option>
                    <option value="Christmas Bonus">Christmas Bonus</option>
                </select>
            </div>

            <div id="bonusAmountContainer">
                <label for="bonusAmount" class="block text-sm font-medium text-white mb-1">Bonus Amount</label>
                <input type="number" step="0.01" id="bonusAmount" name="bonusAmount"
                    class="w-full border border-gray-400 px-3 py-2 focus:ring-gray-300 focus:border-gray-300 text-black bg-gray-100"
                    placeholder="Enter bonus amount">
            </div>

            <div class="flex justify-end space-x-2 mt-6">
                <button type="button" onclick="closeManageBonusModal()"
                    class="px-4 py-2 bg-gray-700 text-white font-medium hover:bg-gray-800 transition">
                    Cancel
                </button>
                <button type="button" onclick="confirmApplyBonus()"
                    class="px-4 py-2 bg-gray-400 text-black font-semibold hover:bg-gray-300 transition">
                    Apply
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==================== CONFIRM MODAL ==================== -->
<div id="confirmModal"
    class="fixed inset-0 hidden bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center z-[60]">
    <div class="bg-gray-600 text-white shadow-2xl p-6 w-96 text-center border border-gray-400">
        <h3 class="text-lg font-semibold mb-3">Confirm Action</h3>
        <p id="confirmMessage" class="text-gray-200 mb-6"></p>
        <div class="flex justify-center gap-3">
            <button onclick="closeConfirmModal()" class="px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white">Cancel</button>
            <button id="confirmApplyBtn" class="px-4 py-2 bg-gray-400 text-black hover:bg-gray-300">Yes, Apply</button>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    lucide.createIcons();
});

function openDetailsModal(id) {
    fetch(`/payroll/records/${id}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById("payrollRecordsBody");
            const title = document.getElementById("payrollModalTitle");
            tbody.innerHTML = "";

            if (data.length === 0) {
                title.textContent = "Employee Payroll Details";
                tbody.innerHTML = `
                    <tr>
                        <td colspan="14" class="text-center py-4 text-gray-600 flex items-center justify-center gap-2">
                            <i data-lucide="alert-octagon" class="w-5 h-5 text-white"></i>
                            <span class="text-black">No records found.</span>
                        </td>
                    </tr>`;
            } else {
                const emp = data[0].employeeprofiles;
                title.textContent = `Employee Payroll — ${emp?.first_name ?? ''} ${emp?.last_name ?? ''}`;
                data.forEach(r => {
                    tbody.innerHTML += `
                    <tr class="hover:bg-gray-200 text-black">
                        <td class="px-4 py-2">${r.payroll_id}</td>
                        <td class="px-4 py-2">${r.pay_period}</td>
                        <td class="px-4 py-2">${r.total_days_of_work}</td>
                        <td class="px-4 py-2">${r.pay_period_start}</td>
                        <td class="px-4 py-2">${r.pay_period_end}</td>
                        <td class="px-4 py-2">${r.salary_rate}</td>
                        <td class="px-4 py-2">${r.basic_salary}</td>
                        <td class="px-4 py-2">${r.basic_salary_tax}</td>
                        <td class="px-4 py-2">${r.overtime_pay}</td>
                       <td class="px-4 py-2 text-left">
    SSS: ${r.sss_contribution}<br>
    PhilHealth: ${r.philhealth_contribution}<br>
    Pag-IBIG: ${r.pagibig_contribution}<br>
    Tax: ${r.tax_deduction}<br>
    Cash Advance: ${r.cash_advance ?? 0}<br>
    <strong>Total: ${r.deductions}</strong>
</td>

                        <td class="px-4 py-2">${r.bonuses ?? '—'}</td>
                        <td class="px-4 py-2">${r.bonus_amount ?? '0.00'}</td>
                        <td class="px-4 py-2">${r.gross_pay}</td>
                        <td class="px-4 py-2 font-semibold text-green-600">${r.net_pay}</td>
                        <td class="px-4 py-2 font-semibold">${r.status}</td>
                    </tr>`;
                });
            }
            document.getElementById("payrollModal").classList.remove("hidden");
            lucide.createIcons();
        })
        .catch(() => showToast("Error loading payroll details.", "error"));
}

function closeDetailsModal() { document.getElementById("payrollModal").classList.add("hidden"); }
function openManageBonusModal() { document.getElementById("manageBonusModal").classList.remove("hidden"); }
function closeManageBonusModal() { document.getElementById("manageBonusModal").classList.add("hidden"); }
function closeConfirmModal() { document.getElementById("confirmModal").classList.add("hidden"); }

function toggleBonusAmount() {
    const type = document.getElementById("bonusType").value;
    const container = document.getElementById("bonusAmountContainer");
    container.style.display = (type === "Christmas Bonus") ? "block" : "none";
}

function confirmApplyBonus() {
    const bonusType = document.getElementById("bonusType").value;
    const bonusAmount = document.getElementById("bonusAmount").value;
    if (!bonusType) return showToast("Please select a bonus type.", "warning");

    const msg = bonusAmount
        ? `Apply ${bonusType} bonus (₱${parseFloat(bonusAmount).toFixed(2)}) to all present employees?`
        : `Apply ${bonusType} bonus (auto-computed) to all present employees?`;

    document.getElementById("confirmMessage").textContent = msg;
    document.getElementById("confirmModal").classList.remove("hidden");

    document.getElementById("confirmApplyBtn").onclick = () => {
        closeConfirmModal();
        applyBonusToPresentEmployees();
    };
}

async function applyBonusToPresentEmployees() {
    const bonusType = document.getElementById('bonusType').value;
    const bonusAmountInput = document.getElementById('bonusAmount').value;
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const bonusAmount = bonusAmountInput ? parseFloat(bonusAmountInput) : null;

    try {
        const res = await fetch('/payroll/apply-bonus', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token, 'Content-Type': 'application/json' },
            body: JSON.stringify({ bonus_type: bonusType, bonus_amount: bonusAmount })
        });
        const result = await res.json();
        if (result.success) {
            showToast(result.message, "success");
            closeManageBonusModal();
        } else if (result.message?.includes("no Present employees")) {
            showToast(`Can't apply bonus (${bonusType}) — no Present employees.`, "error");
        } else {
            showToast(result.message, "warning");
        }
    } catch (err) {
        console.error(err);
        showToast("Error applying bonus.", "error");
    }
}

function showToast(message, type = "info") {
    const container = document.getElementById("toastContainer") || (() => {
        const el = document.createElement("div");
        el.id = "toastContainer";
        el.className = "fixed top-5 right-5 z-50 flex flex-col gap-3";
        document.body.appendChild(el);
        return el;
    })();

    // All same black-white scheme
    const colors = {
        success: "bg-black text-white border-gray-700",
        error: "bg-black text-white border-gray-700",
        warning: "bg-black text-white border-gray-700",
        info: "bg-black text-white border-gray-700"
    };

    const icons = {
        success: "check-circle",
        error: "alert-octagon",
        warning: "alert-triangle",
        info: "info"
    };

    const toast = document.createElement("div");
    toast.className = `flex items-center gap-2 px-4 py-2 rounded-lg border shadow-md animate-slide-in ${colors[type]}`;
    toast.innerHTML = `<i data-lucide="${icons[type]}" class="w-5 h-5 text-white"></i> <span class="font-medium">${message}</span>`;
    
    container.appendChild(toast);
    lucide.createIcons();

    setTimeout(() => {
        toast.classList.add("opacity-0", "translate-x-full", "transition-all", "duration-500");
        setTimeout(() => toast.remove(), 500);
    }, 4000);
}
</script>

<style>
@keyframes slide-in {
  from { opacity: 0; transform: translateX(100%); }
  to { opacity: 1; transform: translateX(0); }
}
.animate-slide-in {
  animation: slide-in 0.4s ease forwards;
}
</style>


</x-guest-layout>
