<x-guest-layout>
    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-2xl font-bold text-red-600 mb-6">⚠ Queue Failure Notifications</h1>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                ✅ {{ session('success') }}
            </div>
        @elseif(session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                ⚠ {{ session('error') }}
            </div>
        @endif

        @if($failedJobs->isEmpty())
            <div class="bg-green-100 text-green-700 p-4 rounded-lg">
                ✅ No failed jobs. Everything is running smoothly.
            </div>
        @else
            <div class="flex justify-end space-x-3 mb-6">
                {{-- Retry all failed jobs --}}
                <form action="{{ route('queue.retryAll') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        🔄 Retry All
                    </button>
                </form>

                {{-- Clear all failed jobs --}}
                <form action="{{ route('queue.clearAll') }}" method="POST" onsubmit="return confirm('Clear ALL failed jobs?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                        🗑 Clear All
                    </button>
                </form>
            </div>

            <div class="space-y-4">
                @foreach($failedJobs as $job)
                    <div class="bg-white shadow rounded-lg p-4 border border-red-300">
                        <p><strong>ID:</strong> {{ $job->id }}</p>
                        <p><strong>Queue:</strong> {{ $job->queue }}</p>
                        <p><strong>Connection:</strong> {{ $job->connection }}</p>
                        <p><strong>Failed At:</strong> {{ $job->failed_at }}</p>

                        <details class="mt-2">
                            <summary class="cursor-pointer text-red-500">Error</summary>
                            <pre class="bg-gray-100 p-2 rounded text-sm overflow-x-auto">{{ $job->exception }}</pre>
                        </details>

                        <div class="flex space-x-3 mt-4">
                            {{-- Retry this job --}}
                            <form action="{{ route('queue.retry', $job->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                                    🔄 Retry
                                </button>
                            </form>

                            {{-- Delete this job --}}
                            <form action="{{ route('queue.delete', $job->id) }}" method="POST" onsubmit="return confirm('Delete this failed job?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-gray-500 text-white px-3 py-1 rounded hover:bg-gray-600">
                                    🗑 Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $failedJobs->links() }}
            </div>
        @endif
    </div>
</x-guest-layout>
