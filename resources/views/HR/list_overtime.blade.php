<x-guest-layout>
    <div class="max-w-6xl mx-auto bg-white p-8 rounded-lg shadow-lg border border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 border-b pb-3">Manage Overtime Requests</h1>
            <a href="{{ route('show.attendance') }}"
               class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg">x</a>
        </div>

        <!-- Flash messages -->
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Search bar -->
        <form method="GET" action="{{ route('show.overtime') }}" class="mb-6 flex justify-end space-x-4">
            <input type="text" name="search" placeholder="Search by name or date"
                class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-amber-400 w-64 text-black"
                value="{{ request('search') }}">
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
                        <th class="px-4 py-2 text-left">Employee ID</th>
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Hours</th>
                        <th class="px-4 py-2 text-left">Amount</th>
                        <th class="px-4 py-2 text-left">Reason</th>
                        <th class="px-4 py-2 text-left">Filed Date</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-800">
                    @forelse($overtimeRequests as $ot)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $ot->employeeprofiles_id }}</td>
                            <td class="px-4 py-2">{{ $ot->first_name }} {{ $ot->last_name }}</td>
                            <td class="px-4 py-2">{{ number_format($ot->hours, 2) }}</td>
                            <td class="px-4 py-2">₱{{ number_format($ot->amount, 2) }}</td>
                            <td class="px-4 py-2">{{ $ot->reason ?? 'N/A' }}</td>
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($ot->filed_date)->format('M d, Y h:i A') }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded-lg text-sm bg-yellow-200 text-yellow-800 font-medium">
                                    {{ ucfirst($ot->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 space-x-2">
                                <form action="{{ route('overtime.approve', $ot->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-lg text-sm">
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('overtime.reject', $ot->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-sm">
                                        Reject
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-gray-500">No pending overtime requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-guest-layout>
