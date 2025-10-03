<x-guest-layout>
    <div class="max-w-5xl mx-auto bg-white rounded-xl shadow-md p-6 mt-10">
        <h2 class="text-xl font-bold mb-6">Recent Activities</h2>

        <table class="min-w-full text-sm border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border">Action</th>
                    <th class="px-4 py-2 border">Date</th>
                    <th class="px-4 py-2 border">Time</th>
                    <th class="px-4 py-2 border">Performed By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="px-4 py-2 border font-bold">{{ $log->action_type }}</td>
                        <td class="px-4 py-2 border">{{ $log->created_at->format('F d, Y') }}</td>
                        <td class="px-4 py-2 border">{{ $log->created_at->format('h:i A') }}</td>
                        <td class="px-4 py-2 border">
                            {{ $log->user?->first_name ?? 'System' }}
                            {{ $log->user?->last_name ?? '' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-center text-gray-500">No activities yet</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>
</x-guest-layout>
