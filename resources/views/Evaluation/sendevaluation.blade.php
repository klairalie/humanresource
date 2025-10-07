<x-guest-layout>
    <div class="min-h-screen p-6 mt-10 text-black">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Send Evaluations</h1>

            <!-- Search bar -->
            <form method="GET" action="{{ url()->current() }}" class="flex items-center space-x-2">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search employees..."
                       class="px-4 py-2 border rounded-lg focus:ring focus:ring-blue-300 text-black">
                <button type="submit"
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Search
                </button>
            </form>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 border border-green-300 rounded">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-700 border border-red-300 rounded">
                {{ session('error') }}
            </div>
        @endif

        @php
            // Define allowed mapping: who can evaluate whom
            $allowedMapping = [
                'Helper' => ['Technician', 'Assistant Technician'],
                'Technician' => ['Helper', 'Assistant Technician'],
                'Assistant Technician' => ['Technician', 'Helper'],
            ];

            // Group employees by position
            $groupedEmployees = $employees->groupBy('position');
        @endphp

        @forelse($groupedEmployees as $position => $group)
            @php
                // Determine allowed assessment positions for this group
                $allowedPositions = $allowedMapping[$position] ?? [];

                // Filter assessments only to allowed positions
                $positionAssessments = $assessments->filter(function($a) use ($allowedPositions) {
                    return in_array($a->position_name, $allowedPositions);
                });
            @endphp

            <div class="bg-white shadow p-4 mb-8 rounded-lg">
                <h2 class="text-xl font-semibold mb-4 border-b pb-2">
                    {{ $position ?? 'Unassigned Position' }}
                </h2>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-200 text-black text-center">
                                <th class="px-4 py-2">#</th>
                                <th class="px-4 py-2">Full Name</th>
                                <th class="px-4 py-2">Email</th>
                                <th class="px-4 py-2">Position</th>
                                <th class="px-4 py-2">Assessment</th>
                                <th class="px-4 py-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($group as $employee)
                                @php
                                    $fullname = trim($employee->first_name . ' ' . $employee->last_name);
                                @endphp
                                <tr class="border-b hover:bg-gray-50 text-center">
                                    <td class="px-4 py-2">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-2">{{ $fullname }}</td>
                                    <td class="px-4 py-2">{{ $employee->email }}</td>
                                    <td class="px-4 py-2">{{ $employee->position ?? 'N/A' }}</td>
                                    <td class="px-4 py-2">
                                        <form action="{{ route('evaluation.send') }}"
                                              method="POST"
                                              class="flex items-center justify-center space-x-2"
                                              onsubmit="return confirm('Send evaluation link to {{ $fullname }}?')">
                                            @csrf

                                            <!-- Evaluator (receiver of email) -->
                                            <input type="hidden" name="evaluator_id" value="{{ $employee->employeeprofiles_id }}">

                                            <!-- Optional evaluatee (left empty, chosen later) -->
                                            <input type="hidden" name="evaluatee_id" value="">

                                            <select name="assessment_id" class="border rounded p-1 text-black" required>
    <option value="">-- Select Assessment --</option>
    @forelse($positionAssessments->filter(fn($a) => str_contains(strtolower($a->title), 'employee evaluation')) as $assess)
        <option value="{{ $assess->assessment_id }}">
            {{ $assess->position_name }} - {{ $assess->title }}
        </option>
    @empty
        <option disabled>No Employee Evaluation available</option>
    @endforelse
</select>

                                            <button type="submit"
                                                    class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 flex items-center gap-1">
                                                <i data-lucide="send" class="w-4 h-4"></i>
                                                Send
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-gray-500">
                                        No employees found for this position.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500">No employees available for evaluation.</p>
        @endforelse
    </div>

    @push('scripts')
        <script>
            lucide.createIcons();
        </script>
    @endpush
</x-guest-layout>
