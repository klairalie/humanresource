<x-guest-layout>
    <div class="min-h-screen bg-transparent p-6">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <h1 class="text-2xl font-bold mb-4 md:mb-0">Employee Profiles</h1>
            <div class="flex items-center space-x-4">

                <!-- Add Employee -->
                <a href="{{ route('view.payroll') }}"
                    class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-full text-md flex items-center justify-center">
                    Payroll Operation
                </a>

                <!-- View Archived -->
                <a href="{{ route('archived.profiles') }}"
                    class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-full text-md flex items-center justify-center">
                    View Archived Profiles
                </a>
            </div>
        </div>

        <!-- Employee Table -->
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
            
            <!-- Search & Filter inside table container -->
            <div class="p-4 border-b flex items-center space-x-4">
                <form method="GET" action="" class="flex space-x-2 w-full">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Search employee..."
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 w-64">

                    <select name="position" onchange="this.form.submit()"
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400">
                        <option value="">All Positions</option>
                        <option value="Administrative Manager"
                            {{ request('position') == 'Administrative Manager' ? 'selected' : '' }}>Administrative Manager</option>
                        <option value="Human Resource Manager"
                            {{ request('position') == 'Human Resource Manager' ? 'selected' : '' }}>Human Resource Manager</option>
                        <option value="Finance Manager"
                            {{ request('position') == 'Finance Manager' ? 'selected' : '' }}>Finance Manager</option>
                        <option value="Technician" {{ request('position') == 'Technician' ? 'selected' : '' }}>Technician</option>
                        <option value="Helper" {{ request('position') == 'Helper' ? 'selected' : '' }}>Helper</option>
                        <option value="Assistant Technician"
                            {{ request('position') == 'Assistant Technician' ? 'selected' : '' }}>Assistant Technician</option>
                    </select>
                </form>
            </div>

            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2 text-left font-semibold text-sm uppercase tracking-wide">ID No.</th>
                        <th class="px-4 py-2 text-left font-semibold text-sm uppercase tracking-wide">Employee</th>
                        <th class="px-4 py-2 text-right font-semibold text-sm uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($employee as $emp)
                        <tr x-data="{ open: false, deactivateOpen: false }" class="hover:bg-gray-50 transition">
                            <td class="px-4 py-2">{{ $emp['employeeprofiles_id'] }}</td>
                            <td class="px-4 py-2 text-md">{{ $emp->last_name }}, {{ $emp->first_name }}</td>
                            <td class="px-4 py-2 text-right">
                                <button @click="open = true" class="text-gray-800 font-medium hover:underline">
                                    View Details
                                </button>
                          <!-- Main Modal -->
                                <div x-show="open"
                                    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
                                    x-transition @click.self="open = false" x-cloak>
                                    <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-sm relative">

                                        <!-- Header -->
                                        <h2 class="text-xl font-bold mb-4 border-b pb-2">
                                            ID No. {{ $emp['employeeprofiles_id'] }} &nbsp;&nbsp;
                                            {{ $emp->last_name }}, {{ $emp->first_name }}
                                        </h2>

                                        <!-- Details Grid -->
                                        <dl class="grid grid-cols-3 gap-y-3 gap-x-4 leading-relaxed text-sm">
                                            <dt class="font-semibold">Address:</dt>
                                            <dd class="col-span-2">{{ $emp['address'] }}</dd>

                                            <dt class="font-semibold">Position:</dt>
                                            <dd class="col-span-2">{{ $emp['position'] }}</dd>

                                            <dt class="font-semibold">Contact:</dt>
                                            <dd class="col-span-2">{{ $emp['contact_info'] }}</dd>

                                            <dt class="font-semibold">Hire Date:</dt>
                                            <dd class="col-span-2">{{ $emp['hire_date'] }}</dd>

                                            <dt class="font-semibold">Status:</dt>
                                            <dd class="col-span-2">{{ $emp['status'] }}</dd>

                                            <dt class="font-semibold">Emergency Contact:</dt>
                                            <dd class="col-span-2">{{ $emp['emergency_contact'] }}</dd>
                                        </dl>

                                        <!-- Fingerprint Section -->
                                        <div class="mt-6">
                                            <p class="font-semibold mb-2">Fingerprint:</p>
                                            <img src="data:image/png;base64,{{ $emp['fingerprint_data'] }}"
                                                alt="Fingerprint"
                                                class="w-24 h-auto border border-gray-300 rounded-md shadow-sm">
                                        </div>

                                        <!-- Footer -->
                                        <div class="mt-6 flex justify-end space-x-3">
                                            <!-- Edit Button -->
                                            <a href="{{ route('show.edit', $emp->employeeprofiles_id) }}"
                                                class="bg-gray-700 hover:bg-gray-900 text-white px-4 py-2 rounded-lg font-medium transition text-sm">
                                                Edit
                                            </a>

                                            <!-- Deactivate Button (triggers reason modal) -->
                                            <button @click="deactivateOpen = true"
                                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition text-sm">
                                                Deactivate
                                            </button>

                                            <!-- Close Button -->
                                            <button @click="open = false"
                                                class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition text-sm">
                                                Close
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Reason Modal -->
                                <div x-show="deactivateOpen"
                                    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
                                    x-transition @click.self="deactivateOpen = false" x-cloak>
                                    <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-sm relative">
                                        <h2 class="text-xl font-bold mb-4 border-b pb-2">
                                            Deactivate {{ $emp->last_name }}, {{ $emp->first_name }}
                                        </h2>

                                        <form method="POST"
                                            action="{{ route('employee.deactivate', $emp->employeeprofiles_id) }}">
                                            @csrf
                                            <div class="mb-4">
                                                <label for="reason" class="block text-sm font-medium">
                                                    Reason for Deactivation
                                                </label>
                                                <textarea name="reason" id="reason" rows="3" required
                                                    class="mt-1 px-3 py-2 border border-gray-300 rounded-md w-full focus:ring-2 focus:ring-red-500"></textarea>
                                            </div>

                                            <div class="flex justify-end space-x-3">
                                                <button type="submit"
                                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium text-sm">
                                                    Confirm Deactivate
                                                </button>
                                                <button type="button" @click="deactivateOpen = false"
                                                    class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium text-sm">
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6 mb-10">
            {{ $employee->appends(request()->query())->links() }}
        </div>
    </div>

    <!-- Alpine.js -->
    <script src="//unpkg.com/alpinejs" defer></script>
</x-guest-layout>
