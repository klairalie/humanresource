<x-guest-layout>
    <div class="max-w-5xl mx-auto bg-white rounded-xl shadow-md p-6 mt-10 mb-25">
        <h2 class="text-2xl font-bold mb-6 text-black">Recent Activities</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border border-gray-200 divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-black font-semibold uppercase tracking-wider border-b">Action</th>
                        <th class="px-6 py-3 text-left text-black font-semibold uppercase tracking-wider border-b">Date</th>
                        <th class="px-6 py-3 text-left text-black font-semibold uppercase tracking-wider border-b">Time</th>
                        <th class="px-6 py-3 text-left text-black font-semibold uppercase tracking-wider border-b">Performed By</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-black font-medium">{{ $log->action_type }}</td>
                            <td class="px-6 py-4 text-black">{{ $log->created_at->format('F d, Y') }}</td>
                            <td class="px-6 py-4 text-black">{{ $log->created_at->format('h:i A') }}</td>
                            <td class="px-6 py-4 text-black">
                                @if($log->employeeprofiles)
                                    {{ $log->employeeprofiles->first_name }} {{ $log->employeeprofiles->last_name }}
                                @elseif($log->applicant)
                                    {{ $log->applicant->first_name }} {{ $log->applicant->last_name }}
                                @else
                                    System
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                No activities yet
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-10 mb-10">
            {{ $logs->links() }}
        </div>
    </div>
</x-guest-layout>
