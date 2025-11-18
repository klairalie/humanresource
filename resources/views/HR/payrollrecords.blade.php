<x-guest-layout>
    <div class="p-6">

        <!-- Search bar -->
        <form method="GET" action="{{ route('payroll.records') }}" class="mb-6">
            <input 
                type="text" 
                name="search" 
                value="{{ $search ?? '' }}" 
                placeholder="Search employee..." 
                class="w-full sm:w-1/2 border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-400"
            />
        </form>

        <!-- Employee Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            @forelse ($employees as $employee)

                @php
                    $records = $employee->payrolls;
                    $latest = $records->first(); // already sorted desc in controller
                @endphp

                <!-- Employee Card -->
                <div class="bg-white shadow-md rounded-xl border border-gray-200 flex flex-col h-full relative p-4"
                     x-data="{ open: false }" x-init="open = false">

                    <!-- Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <span class="font-semibold text-gray-800 text-lg">
                                {{ $employee->first_name }} {{ $employee->last_name }}
                            </span>
                            <span class="text-sm text-gray-500">
                                ID: {{ $employee->employeeprofiles_id }}
                            </span>
                        </div>

                        <button @click="open = true" 
                                class="text-sm text-cyan-600 hover:underline">
                            View All Records
                        </button>
                    </div>

                    <!-- Latest Payroll Summary -->
                    @if ($latest)
                        <div class="grid grid-cols-2 gap-2 text-sm text-gray-700 mt-auto">
                            <div><span class="font-semibold">Gross:</span> ₱{{ number_format($latest->gross_pay,2) }}</div>
                            <div><span class="font-semibold">Deductions:</span> ₱{{ number_format($latest->deductions,2) }}</div>
                            <div><span class="font-semibold">Bonuses:</span> {{ $latest->bonuses }}</div>
                            <div><span class="font-semibold">Net Pay:</span> ₱{{ number_format($latest->net_pay,2) }}</div>
                            <div class="col-span-2">
                                <span class="font-semibold">Status:</span> 
                                <span class="{{ $latest->status === 'Pending' ? 'text-yellow-600' : 'text-green-600' }}">
                                    {{ $latest->status }}
                                </span>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 text-sm mt-4">No payroll records yet.</p>
                    @endif

                    <!-- Modal: All Records -->
                    <div x-show="open"
                         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                         x-transition.opacity>

                        <div class="bg-white w-full rounded-xl shadow-lg p-6 relative text-center"
                             @click.away="open = false">

                            <!-- Modal Header -->
                            <div class="flex justify-center items-center mb-4 relative">
                                <h3 class="font-semibold text-lg text-gray-800 mx-auto">
                                    Payroll Records - {{ $employee->first_name }} {{ $employee->last_name }}
                                </h3>

                                <button @click="open = false" 
                                        class="absolute right-0 top-0 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
                            </div>

                            <!-- === FILTERS === -->
                            <form method="GET" class="flex flex-wrap gap-3 justify-center mb-4">

                                <select name="month" class="border rounded px-3 py-1 text-black">
                                    <option value="">Filter by Month</option>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}">{{ date("F", mktime(0,0,0,$m,1)) }}</option>
                                    @endfor
                                </select>

                                <input type="text" 
                                       name="period"
                                       placeholder="Pay Period (e.g. 1-15)"
                                       class="border rounded px-3 py-1 text-black" />

                                <button class="px-4 py-1 bg-cyan-600 text-black rounded hover:bg-cyan-700">
                                    Apply
                                </button>
                            </form>

                            <!-- === EXPORT BUTTONS === -->
                            <div class="flex flex-wrap gap-3 justify-center mb-4">

                                <!-- Excel -->
                                <a href="{{ route('payroll.export.excel', $employee->employeeprofiles_id) }}?{{ http_build_query(request()->all()) }}"
                                   class="px-4 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                                    Export Excel
                                </a>

                                <!-- PDF -->
                                <a href="{{ route('payroll.export.pdf', $employee->employeeprofiles_id) }}?{{ http_build_query(request()->all()) }}"
                                   class="px-4 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                    Save PDF
                                </a>

                                <!-- Print -->
                                <a href="{{ route('payroll.print', $employee->employeeprofiles_id) }}?{{ http_build_query(request()->all()) }}"
                                   target="_blank"
                                   class="px-4 py-1 bg-gray-700 text-white rounded hover:bg-gray-800">
                                    Print
                                </a>

                            </div>

                            <!-- === TABLE === -->
                            <div class="overflow-x-auto overflow-y-auto max-h-96 border rounded-lg">
                                <table class="w-full text-sm text-gray-700 border-collapse">
                                    <thead class="bg-gray-50 sticky top-0 z-10 text-center">
                                        <tr>
                                            <th class="px-3 py-2 border-b">Period</th>
                                            <th class="px-3 py-2 border-b">Total Days of Work</th>
                                            <th class="px-3 py-2 border-b">Basic Salary</th>
                                            <th class="px-3 py-2 border-b">OT Pay</th>
                                            <th class="px-3 py-2 border-b">Gross Pay</th>
                                            <th class="px-3 py-2 border-b">Tax</th>
                                            <th class="px-3 py-2 border-b">SSS</th>
                                            <th class="px-3 py-2 border-b">PhilHealth</th>
                                            <th class="px-3 py-2 border-b">Pag-IBIG</th>
                                            <th class="px-3 py-2 border-b">Cash Advance</th>
                                            <th class="px-3 py-2 border-b">Total Deductions</th>
                                            <th class="px-3 py-2 border-b">Bonus Amount</th>
                                            <th class="px-3 py-2 border-b">Net Pay</th>
                                            <th class="px-3 py-2 border-b">Status</th>
                                        </tr>
                                    </thead>

                                    <tbody class="text-center">
                                        @foreach($records as $record)
                                        <tr class="hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50' : '' }}">
                                            <td class="px-3 py-1 border-b">{{ $record->pay_period }}</td>
                                            <td class="px-3 py-1 border-b">{{ $record->total_days_of_work }}</td>
                                            <td class="px-3 py-1 border-b">₱{{ number_format($record->basic_salary,2) }}</td>
                                            <td class="px-3 py-1 border-b">₱{{ number_format($record->overtime_pay,2) }}</td>
                                            <td class="px-3 py-1 border-b">₱{{ number_format($record->gross_pay,2) }}</td>
                                            <td class="px-3 py-1 border-b">₱{{ number_format($record->tax_deduction,2) }}</td>
                                            <td class="px-3 py-1 border-b">₱{{ number_format($record->sss_contribution,2) }}</td>
                                            <td class="px-3 py-1 border-b">₱{{ number_format($record->philhealth_contribution,2) }}</td>
                                            <td class="px-3 py-1 border-b">₱{{ number_format($record->pagibig_contribution,2) }}</td>
                                            <td class="px-3 py-1 border-b">₱{{ number_format($record->cash_advance,2) }}</td>
                                            <td class="px-3 py-1 border-b">₱{{ number_format($record->deductions,2) }}</td>
                                            <td class="px-3 py-1 border-b">₱{{ number_format($record->bonus_amount,2) }}</td>
                                            <td class="px-3 py-1 border-b">₱{{ number_format($record->net_pay,2) }}</td>
                                            <td class="px-3 py-1 border-b">
                                                <span class="{{ $record->status === 'Pending' ? 'text-yellow-600' : 'text-green-600' }}">
                                                    {{ $record->status }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>

                                </table>
                            </div>

                            <!-- Close -->
                            <div class="mt-4 text-center">
                                <button @click="open = false"
                                        class="px-6 py-2 bg-cyan-600 text-white rounded hover:bg-cyan-700">
                                    Close
                                </button>
                            </div>

                        </div>
                    </div>

                </div>
            @empty
                <div class="col-span-full text-center text-gray-500 py-10">
                    No payroll records found.
                </div>
            @endforelse

        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $employees->links() }}
        </div>

    </div>
</x-guest-layout>
