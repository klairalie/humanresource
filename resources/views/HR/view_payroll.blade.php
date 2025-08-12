<x-guest-layout>
    <div class="min-h-screen bg-transparent p-6">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <h1 class="text-2xl font-bold mb-4 md:mb-0 text-black">Payroll Records</h1>
            <div class="flex items-center space-x-4">
                <input type="text" placeholder="Search employee..."
                    class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500 w-64">
                     <a href="{{ route('show.payrollform') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-full text-xl flex items-center justify-center">
                    +
                </a>
            </div>
        </div>

        <!-- Payroll Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($payroll as $payrolls)
                <div class="bg-gradient-to-br from-gray-50 via-white to-gray-100 rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition duration-300 transform hover:-translate-y-1">
                    
                    <!-- Card Header -->
                    <div class="flex justify-between items-start mb-4">
                        <h4 class="text-xl font-bold text-gray-900 tracking-wide">
                            {{ $payrolls->employeeprofiles?->first_name ?? 'No Name' }}
                            <span class="font-medium text-gray-700">
                                {{ $payrolls->employeeprofiles?->last_name ?? '' }}
                            </span>
                        </h4>

                        <form action="{{ route('payroll.compute', $payrolls->employeeprofiles?->employeeprofiles_id) }}" method="GET">
                            <button type="submit"
                                class="bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-semibold px-4 py-2 rounded-lg shadow-md transition duration-300">
                                Compute
                            </button>
                        </form>
                    </div>

                    <!-- Payroll Details -->
                    <div class="space-y-2 text-gray-800 text-sm leading-relaxed">
                        <p><span class="font-semibold text-amber-700">Employee ID:</span> {{ $payrolls->employeeprofiles?->employeeprofiles_id }}</p>
                        <p><span class="font-semibold text-amber-700">Position:</span> {{ $payrolls->employeeprofiles?->position }}</p>
                        <p><span class="font-semibold text-amber-700">Total Days of Work:</span> {{ $payrolls->total_days_of_work }}</p>
                        <p><span class="font-semibold text-amber-700">Pay Period:</span> {{ $payrolls->pay_period }}</p>
                        <p><span class="font-semibold text-amber-700">Basic Salary:</span> {{ $payrolls->basic_salary }}</p>
                        <p><span class="font-semibold text-amber-700">OT Hours:</span> {{ $payrolls->overtime_hours }}</p>
                        <p><span class="font-semibold text-amber-700">Bonuses:</span> {{ $payrolls->bonuses }}</p>
                        <p><span class="font-semibold text-amber-700">Deductions:</span> {{ $payrolls->deductions }}</p>
                        <p class="pt-2 border-t border-gray-200 text-lg font-bold text-orange-800">
                            Net Pay: {{ $payrolls->net_pay }}
                        </p>
                    </div>

                </div>
            @endforeach
        </div>
    </div>
</x-guest-layout>
