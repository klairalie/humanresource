<x-guest-layout>
    <div class="max-w-6xl mx-auto bg-white p-8 rounded-lg shadow-lg border border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 border-b pb-3">Employees with Overtime</h1>
            <a href="{{ route('show.attendance') }}"
               class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg"> x </a>
        </div>

        <!-- Search/Filter -->
        <form method="GET" action="" class="mb-6 flex space-x-3">
            <input type="text" name="search" placeholder="Search by name or date"
                   class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-amber-400 w-full">
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-semibold px-4 py-2 rounded-lg transition">
                Search
            </button>
        </form>

        <!-- Overtime Table -->
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-300 rounded-lg overflow-hidden">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left">Employee ID</th>
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Date</th>
                        <th class="px-4 py-2 text-left">Overtime Hours</th>
                    </tr>
                </thead>
                {{-- <tbody class="text-gray-800">
                    @foreach()
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $ot->employee_id }}</td>
                            <td class="px-4 py-2">{{ $ot->name }}</td>
                            <td class="px-4 py-2">{{ $ot->date }}</td>
                            <td class="px-4 py-2">{{ $ot->hours }}</td>
                        </tr>
                    @endforeach
                </tbody> --}}
            </table>
        </div>
    </div>
</x-guest-layout>
