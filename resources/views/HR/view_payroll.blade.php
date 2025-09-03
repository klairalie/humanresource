<x-guest-layout>
    <div class="min-h-screen p-6">

        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <h1 class="text-2xl font-bold mb-4 md:mb-0 text-black">Payroll Records</h1>
            <form method="GET" action="{{ route('view.payroll') }}" class="flex items-center space-x-4">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by name, ID, or month..."
                    class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500 w-64">
                   
            </form>
             <a href="{{ route('show.deductionform') }}">
                <button class="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600"> + Deduction </button>
            </a>
        </div>

        <!-- Payroll Table -->
        <div class="overflow-x-auto bg-white shadow-md rounded-xl mb-10">
            <table class="min-w-full text-left mb-10">
                <thead>
                    <tr class="text-black font-semibold bg-gray-200">
                        <th class="px-6 py-4">Employee ID</th>
                        <th class="px-6 py-4">First Name</th>
                        <th class="px-6 py-4">Last Name</th>
                        <th class="px-6 py-4">Position</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody class="text-black">
                    @foreach ($payroll as $payrolls)
                        <tr class="hover:bg-gray-100 transition even:bg-gray-50">
                            <td class="px-6 py-3">
                                {{ $payrolls->employeeprofiles?->employeeprofiles_id }}
                            </td>
                            <td class="px-6 py-3">
                                {{ $payrolls->employeeprofiles?->first_name ?? 'No Name' }}
                            </td>
                            <td class="px-6 py-3">
                                {{ $payrolls->employeeprofiles?->last_name ?? '' }}
                            </td>
                            <td class="px-6 py-3">
                                {{ $payrolls->employeeprofiles?->position }}
                            </td>
                            <td class="px-6 py-3">
                                <button 
                                    onclick="openModal({{ $payrolls->toJson() }})"
                                    class="text-black font-semibold hover:underline">
                                    View Details
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6 mb-15">
            {{ $payroll->links() }}
        </div>
    </div>

    <!-- Modal -->
    <div id="payrollModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg relative p-6">
            <!-- Close Button -->
            <button onclick="closeModal()" class="absolute top-3 right-3 text-gray-600 hover:text-black text-xl font-bold">&times;</button>
            
            <h2 class="text-xl font-bold mb-4 text-black">Payroll Details</h2>

            <div id="modalContent" class="space-y-2 text-black">
                <!-- Dynamic details will be filled here -->
            </div>

            <div class="flex justify-end mt-6 space-x-3">
                <button class="px-4 py-2 bg-gray-300 text-black rounded-lg hover:bg-gray-400">Edit</button>
                <button class="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600">Submit</button>
            </div>
        </div>
    </div>

    <!-- Script -->
    <script>
        function openModal(data) {
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
                <p><strong>Bonuses:</strong> ${data.bonuses ?? ''}</p>
                <p><strong>Status:</strong> ${data.status ?? ''}</p>
            `;
            document.getElementById('modalContent').innerHTML = content;
            document.getElementById('payrollModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('payrollModal').classList.add('hidden');
        }
    </script>
</x-guest-layout>
