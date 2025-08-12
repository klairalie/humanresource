<x-guest-layout>
    <div class="max-w-4xl mx-auto mt-8 p-10 bg-[#fdf8f5] rounded-2xl shadow-lg text-[#4a3c31] relative">

        <!-- X Button -->
        <a href="{{ route('view.payroll') }}" 
           class="absolute top-4 right-4 text-[#a0522d] hover:text-[#7b3f20] text-2xl font-bold">
            &times;
        </a>

        <h1 class="text-center mb-6 font-bold text-2xl tracking-wide">
            Add New Payroll Record
        </h1>

        {{-- Error alerts --}}
        @if ($errors->any())
            <script>
                alert("{{ implode('\n', $errors->all()) }}");
            </script>
        @endif

        @if (session('success'))
            <div class="bg-[#fbeec1] text-[#5f4b32] p-4 rounded-lg mb-6 border border-[#e2c275]">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('create.payroll') }}" 
              class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @csrf

            {{-- Employee ID --}}
            <div>
                <label for="employeeprofiles_id" class="font-semibold text-sm">Employee Profile ID</label>
                <input type="number" name="employeeprofiles_id_display"
                    value="{{ $latestEmployee->employeeprofiles_id ?? '' }}" readonly
                    class="w-full p-2 rounded-lg border border-[#c4b7a6] text-gray-700 bg-[#f4ede7]">
                <input type="hidden" name="employeeprofiles_id"
                    value="{{ $latestEmployee->employeeprofiles_id ?? '' }}">
            </div>

            {{-- Employee Name --}}
            <div>
                <label class="font-semibold text-sm">Employee Name</label>
                <input type="text" name="employee_name_display"
                    value="{{ ($latestEmployee->first_name ?? '') . ' ' . ($latestEmployee->last_name ?? '') }}" readonly
                    class="w-full p-2 rounded-lg border border-[#c4b7a6] text-gray-700 bg-[#f4ede7]">
                <input type="hidden" name="employee_name"
                    value="{{ ($latestEmployee->first_name ?? '') . ' ' . ($latestEmployee->last_name ?? '') }}">
            </div>

            {{-- Pay Period Start --}}
            <div>
                <label for="pay_period_start" class="font-semibold text-sm">Pay Period Start</label>
                <input type="date" name="pay_period_start" id="pay_period_start"
                    value="{{ $payPeriod['pay_period_start'] ?? '' }}"
                    class="w-full p-2 rounded-lg border border-[#c4b7a6] bg-[#f4ede7] text-gray-700">
            </div>

            {{-- Pay Period End --}}
            <div>
                <label for="pay_period_end" class="font-semibold text-sm">Pay Period End</label>
                <input type="date" name="pay_period_end" id="pay_period_end"
                    value="{{ $payPeriod['pay_period_end'] ?? '' }}"
                    class="w-full p-2 rounded-lg border border-[#c4b7a6] bg-[#f4ede7] text-gray-700">
            </div>

            {{-- Basic Salary --}}
            <div>
                <label for="basic_salary" class="font-semibold text-sm">Basic Salary</label>
                <input type="number" step="0.01" name="basic_salary" id="basic_salary"
                    value="{{ old('basic_salary') }}" required
                    class="w-full p-2 rounded-lg border border-[#c4b7a6]">
            </div>

            {{-- Pay Period --}}
            <div>
                <label for="pay_period" class="font-semibold text-sm">Pay Period</label>
                <select name="pay_period" id="pay_period"
                    class="w-full p-2 rounded-lg border border-[#c4b7a6] bg-white text-gray-700">
                    @php
                        $currentYear = date('Y');
                        $periods = [];
                        for ($month = 1; $month <= 12; $month++) {
                            $monthName = date('F', mktime(0, 0, 0, $month, 1));
                            $periods[] = [
                                'label' => "$monthName 1–15, $currentYear",
                                'start' => date('Y-m-d', strtotime("$currentYear-$month-01")),
                                'end' => date('Y-m-d', strtotime("$currentYear-$month-15")),
                            ];
                            $lastDay = date('t', strtotime("$currentYear-$month-01"));
                            $periods[] = [
                                'label' => "$monthName 16–$lastDay, $currentYear",
                                'start' => date('Y-m-d', strtotime("$currentYear-$month-16")),
                                'end' => date('Y-m-d', strtotime("$currentYear-$month-$lastDay")),
                            ];
                        }
                    @endphp
                    @foreach ($periods as $p)
                        <option value="{{ $p['start'] }}|{{ $p['end'] }}"
                            @if (isset($payPeriod) && $p['start'] == $payPeriod['pay_period_start']) selected @endif>
                            {{ $p['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Submit Button --}}
            <div class="col-span-1 md:col-span-2 text-center">
                <button type="submit"
                    class="mt-4 px-6 py-3 rounded-xl bg-[#a67c52] text-[#fdf8f5] font-bold text-lg shadow-md hover:bg-[#8c6644] transition">
                    Create Payroll
                </button>
            </div>
        </form>
    </div>

    {{-- Hidden fields for JS --}}
    <input type="hidden" name="pay_period_start" id="pay_period_start_hidden" value="">
    <input type="hidden" name="pay_period_end" id="pay_period_end_hidden" value="">

    <script>
        const select = document.getElementById('pay_period');
        const startInput = document.getElementById('pay_period_start_hidden');
        const endInput = document.getElementById('pay_period_end_hidden');

        function updateDates() {
            const [start, end] = select.value.split('|');
            startInput.value = start;
            endInput.value = end;
        }

        select.addEventListener('change', updateDates);
        updateDates();
    </script>
</x-guest-layout>
