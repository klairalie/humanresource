<x-guest-layout>
    <div class="max-w-5xl mx-auto p-8">
        <!-- Header -->
        <div class="flex items-center gap-3 mb-8 border-b pb-4">
            <i data-lucide="alert-triangle" class="w-8 h-8 text-red-600"></i>
            <h1 class="text-3xl font-bold text-gray-800">Queue Failure Notifications</h1>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="flex items-center gap-2 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg mb-5">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <span>{{ session('success') }}</span>
            </div>
        @elseif(session('error'))
            <div class="flex items-center gap-2 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-5">
                <i data-lucide="alert-octagon" class="w-5 h-5"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($failedJobs->isEmpty())
            <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 p-5 rounded-lg">
                <i data-lucide="check" class="w-6 h-6"></i>
                <span class="font-medium">No failed jobs — everything is running smoothly.</span>
            </div>
        @else
            <!-- Actions -->
            <div class="flex justify-end gap-3 mb-6">
                <!-- Retry All -->
                <form action="{{ route('queue.retryAll') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-2 bg-blue-600 text-white font-medium px-4 py-2 rounded-lg hover:bg-blue-700 shadow-sm transition">
                        <i data-lucide="rotate-ccw" class="w-5 h-5"></i> Retry All
                    </button>
                </form>

                <!-- Clear All -->
                <form action="{{ route('queue.clearAll') }}" method="POST" onsubmit="return confirm('Clear ALL failed jobs?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="flex items-center gap-2 bg-red-600 text-white font-medium px-4 py-2 rounded-lg hover:bg-red-700 shadow-sm transition">
                        <i data-lucide="trash-2" class="w-5 h-5"></i> Clear All
                    </button>
                </form>
            </div>

            <!-- Failed Jobs List -->
            <div class="space-y-5">
                @foreach($failedJobs as $job)
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <i data-lucide="server-crash" class="w-6 h-6 text-red-600"></i>
                                <h2 class="text-lg font-semibold text-gray-800">Job #{{ $job->id }}</h2>
                            </div>
                            <span class="text-sm text-gray-500">Failed: {{ $job->failed_at }}</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-700">
                            <p><strong>Queue:</strong> {{ $job->queue }}</p>
                            <p><strong>Connection:</strong> {{ $job->connection }}</p>
                        </div>

                        <details class="mt-3 group">
                            <summary class="cursor-pointer text-red-600 flex items-center gap-1 group-hover:underline">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i> View Error Details
                            </summary>
                            <pre class="bg-gray-50 border border-gray-200 mt-2 p-3 rounded-lg text-sm text-gray-800 overflow-x-auto">{{ $job->exception }}</pre>
                        </details>

                        <div class="flex gap-3 mt-4">
                            <!-- Retry -->
                            <form action="{{ route('queue.retry', $job->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="flex items-center gap-2 bg-yellow-500 text-white px-3 py-1.5 rounded-lg hover:bg-yellow-600 shadow-sm transition">
                                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i> Retry
                                </button>
                            </form>

                            <!-- Delete -->
                            <form action="{{ route('queue.delete', $job->id) }}" method="POST" onsubmit="return confirm('Delete this failed job?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="flex items-center gap-2 bg-gray-600 text-white px-3 py-1.5 rounded-lg hover:bg-gray-700 shadow-sm transition">
                                    <i data-lucide="trash" class="w-4 h-4"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $failedJobs->links() }}
            </div>
        @endif
    </div>

    <!-- Load Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</x-guest-layout>
