<x-guest-layout>
    <div class="min-h-screen p-6">
        <h1 class="text-2xl font-bold mb-6 text-black">Add Deduction (Cash Advance)</h1>

        <div class="bg-white shadow-md rounded-xl p-6 max-w-2xl">
            <form method="POST" action="{{ route('store.deduction') }}">
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
                                {{ $employee->employeeprofiles_id }} - {{ $employee->first_name }} {{ $employee->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>


                <!-- Deduction Type -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deduction Type</label>
                    <input type="text" name="deduction_type" value="Cash Advance" readonly
                        class="w-full border border-gray-300 rounded-md p-2 bg-gray-100 text-gray-600">
                </div>

                <!-- Amount -->
                <div class="mb-4">
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                        Deduction Amount (₱)
                    </label>
                    <input type="number" step="0.01" name="amount" id="amount" required
                        class="w-full border border-gray-300 rounded-md p-2 focus:ring-amber-500 focus:border-amber-500">
                </div>

                <!-- Date -->
                <div class="mb-4">
                    <label for="deduction_date" class="block text-sm font-medium text-gray-700 mb-1">
                        Date
                    </label>
                    <input type="date" name="deduction_date" id="deduction_date" value="{{ now()->toDateString() }}" required
                        class="w-full border border-gray-300 rounded-md p-2 focus:ring-amber-500 focus:border-amber-500">
                </div>

                <!-- Submit -->
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
