<x-guest-layout>
    <div class="max-w-9xl mx-auto bg-white p-8 rounded-lg shadow-lg border border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 border-b pb-3">Manage Leave Requests</h1>
            <a href="{{ route('show.attendance') }}"
               class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg">x</a>
        </div>

        <!-- Leave Requests Table -->
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-300 rounded-lg overflow-hidden">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left">Employee ID</th>
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">From</th>
                        <th class="px-4 py-2 text-left">To</th>
                        <th class="px-4 py-2 text-left">Reason</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-800">
                    @forelse($leaveRequests as $leave)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $leave->employeeprofiles_id }}</td>
                            <td class="px-4 py-2">{{ $leave->first_name }} {{ $leave->last_name }}</td>
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($leave->start_at)->format('M d, Y h:i A') }}</td>
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($leave->end_at)->format('M d, Y h:i A') }}</td>
                            <td class="px-4 py-2">{{ $leave->reason ?? 'N/A' }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded-lg text-sm bg-yellow-200 text-yellow-800 font-medium">
                                    {{ ucfirst($leave->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 space-x-2">
                                <form action="{{ route('leave.approve', $leave->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-lg text-sm">
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('leave.reject', $leave->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-sm">
                                        Reject
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-gray-500">No pending leave requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-guest-layout>
