<x-guest-layout>


<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-bold text-red-600 mb-6">⚠ Queue Failure Notifications</h1>

    @if($failedJobs->isEmpty())
        <div class="bg-green-100 text-green-700 p-4 rounded-lg">
            ✅ No failed jobs. Everything is running smoothly.
        </div>
    @else
        <div class="space-y-4">
            @foreach($failedJobs as $job)
                <div class="bg-white shadow rounded-lg p-4 border border-red-300">
                    <p><strong>Queue:</strong> {{ $job->queue }}</p>
                    <p><strong>Connection:</strong> {{ $job->connection }}</p>
                    <p><strong>Failed At:</strong> {{ $job->failed_at }}</p>
                    <details class="mt-2">
                        <summary class="cursor-pointer text-red-500">Error</summary>
                        <pre class="bg-gray-100 p-2 rounded text-sm">{{ $job->exception }}</pre>
                    </details>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $failedJobs->links() }}
        </div>
    @endif
</div>

</x-guest-layout>