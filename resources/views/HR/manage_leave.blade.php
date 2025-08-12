<x-guest-layout>
    <div class="max-w-6xl mx-auto bg-white p-8 rounded-lg shadow-lg border border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 border-b pb-3">Manage Leave Requests</h1>
            <a href="{{ route('show.attendance') }}"
               class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg"> x </a>
        </div>

        <!-- Leave Requests Table -->
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-300 rounded-lg overflow-hidden">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left">Employee ID</th>
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Leave Type</th>
                        <th class="px-4 py-2 text-left">From</th>
                        <th class="px-4 py-2 text-left">To</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-800">
                    {{-- @foreach($leaveRequests as $leave)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $leave->employee_id }}</td>
                            <td class="px-4 py-2">{{ $leave->name }}</td>
                            <td class="px-4 py-2">{{ $leave->type }}</td>
                            <td class="px-4 py-2">{{ $leave->from }}</td>
                            <td class="px-4 py-2">{{ $leave->to }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded-lg text-sm 
                                    {{ $leave->status == 'Pending' ? 'bg-yellow-200 text-yellow-800' : 
                                       ($leave->status == 'Approved' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800') }}">
                                    {{ $leave->status }}
                                </span>
                                {{ route('leave.approve', $leave->id) }}
                                 {{ route('leave.reject', $leave->id) }}
                            </td> --}}
                            <td class="px-4 py-2 space-x-2">
                                <form action="" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1 rounded-lg text-sm">Approve</button>
                                </form>
                                <form action="" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-gray-400 hover:bg-gray-500 text-white px-3 py-1 rounded-lg text-sm">Reject</button>
                                </form>
                            </td>
                        </tr>
                    {{-- @endforeach --}}
                </tbody>
            </table>
        </div>
    </div>
</x-guest-layout>
