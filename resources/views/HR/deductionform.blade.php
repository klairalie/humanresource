<x-guest-layout>
    <div class="min-h-screen p-6">
        <!-- Header -->
        <h1 class="text-2xl font-bold mb-6 text-black">Add Deduction (Cash Advance)</h1>

        <!-- Deduction Form -->
        <div class="bg-white shadow-md rounded-xl p-6 max-w-2xl">
            <form method="POST" action="{{ route('deductions.store') }}">
                @csrf

                <!-- Employee -->
                <div class="mb-4">
                    <label for="employeeprofiles_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Employee
                    </label>
                    <select name="employeeprofiles_id" id="employeeprofiles_id" 
                        class="w-full border border-gray-300 rounded-md p-2 focus:ring-amber-500 focus:border-amber-500" required>
                        <option value="">-- Select Employee --</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->employeeprofiles_id }}">
                                {{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->employeeprofiles_id }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Payroll ID (hidden or auto-linked later) -->
                <input type="hidden" name="payroll_id" value="{{ $payroll->payroll_id ?? '' }}">

                <!-- Deduction Type (fixed to Cash Advance) -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deduction Type</label>
                    <input type="text" name="deduction_type" value="Cash Advance" readonly
                        class="w-full border border-gray-300 rounded-md p-2 bg-gray-100 text-gray-600">
                </div>

                <!-- Total Amount -->
                <div class="mb-4">
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                        Total Amount (₱)
                    </label>
                    <input type="number" step="0.01" name="amount" id="amount" required
                        class="w-full border border-gray-300 rounded-md p-2 focus:ring-amber-500 focus:border-amber-500">
                </div>

                <!-- Partial Payment -->
                <div class="mb-4">
                    <label for="partial_payment" class="block text-sm font-medium text-gray-700 mb-1">
                        Partial Payment (₱)
                    </label>
                    <input type="number" step="0.01" name="partial_payment" id="partial_payment" 
                        class="w-full border border-gray-300 rounded-md p-2 focus:ring-amber-500 focus:border-amber-500">
                    <p class="text-xs text-gray-500 mt-1">Leave as 0 if no payment yet.</p>
                </div>

                <!-- Date -->
                <div class="mb-4">
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-1">
                        Date
                    </label>
                    <input type="date" name="date" id="date" value="{{ now()->toDateString() }}" required
                        class="w-full border border-gray-300 rounded-md p-2 focus:ring-amber-500 focus:border-amber-500">
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" 
                        class="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600">
                        Save Deduction
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
