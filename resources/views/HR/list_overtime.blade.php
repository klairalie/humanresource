<x-guest-layout>
    <div class="max-w-6xl mx-auto bg-white p-8 rounded-lg shadow-lg border border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 border-b pb-3">Employees with Overtime</h1>
            <a href="{{ route('show.attendance') }}"
               class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg"> x </a>
        </div>

        <!-- Search/Filter -->
<form method="GET" action="" class="mb-10 flex justify-end space-x-5">
    <input type="text" name="search" placeholder="Search by name or date"
           class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-amber-400 w-64 text-black">
    <button type="submit" 
            class="bg-amber-500 hover:bg-amber-600 text-black font-semibold px-4 py-2 rounded-lg transition">
        Search
    </button>
</form>

        <!-- Overtime Table -->
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-300 rounded-lg overflow-hidden">
                <thead class="bg-gray-100 text-black">
                    <tr>
                        <th class="px-4 py-2 text-center">Employee ID</th>
                        <th class="px-4 py-2 text-center">Name</th>
                        <th class="px-4 py-2 text-center">Date</th>
                        <th class="px-4 py-2 text-center">Overtime Hours</th>
                    </tr>
                </thead>
               <tbody class="text-gray-800 text-center">
    @forelse($overtimeRequests as $ot)
        <tr class="border-t">
            <td class="px-4 py-2">{{ $ot->employeeprofiles_id }}</td>
            <td class="px-4 py-2">
                {{ $ot->employeeprofiles?->first_name ?? 'N/A' }}
                {{ $ot->employeeprofiles?->last_name ?? '' }}
            </td>
            <td class="px-4 py-2">{{ $ot->request_date }}</td>
            <td class="px-4 py-2">{{ $ot->overtime_hours }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="4" class="px-4 py-2 text-center text-gray-500">No overtime requests found.</td>
        </tr>
    @endforelse
</tbody>
            </table>
        </div>
    </div>
</x-guest-layout>
