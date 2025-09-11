<x-guest-layout>
    <div class="min-h-screen p-6">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <h1 class="text-2xl font-bold mb-4 md:mb-0 text-black">List of Applicants</h1>

            <!-- Search Bar -->
            <form method="GET" action="" class="flex items-center space-x-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search applicants..."
                    class="px-4 py-2 border rounded-lg focus:ring focus:ring-blue-300">
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Search
                </button>
            </form>
        </div>

        <!-- Applicants Table -->
        <div class="bg-white shadow p-4 overflow-x-auto">
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-300 text-left text-gray-600">
                        <th class="px-4 py-2">#</th>
                        <th class="px-4 py-2">Full Name</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Contact</th>
                        <th class="px-4 py-2">Position</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applicants as $index => $applicant)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2">{{ $applicant->first_name }}, {{ $applicant->last_name }}</td>
                            <td class="px-4 py-2">{{ $applicant->email }}</td>
                            <td class="px-4 py-2">{{ $applicant->contact_number }}</td>
                            <td class="px-4 py-2">{{ $applicant->position ?? 'N/A' }}</td>
                            <td class="px-4 py-2">
                                <span
                                    class="px-2 py-1 rounded-full text-sm 
                                    {{ $applicant->status == 'Pending'
                                        ? 'bg-yellow-200 text-yellow-800'
                                        : ($applicant->status == 'Reviewed'
                                            ? 'bg-blue-200 text-blue-800'
                                            : 'bg-green-200 text-green-800') }}">
                                    {{ $applicant->status ?? 'Pending' }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                @if (!$applicant->summary)
                                    <form method="POST"
                                        action="{{ route('applicants.summarize', $applicant->applicant_id) }}">
                                        @csrf
                                        <button type="submit"
                                            class="px-3 py-1 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600">
                                            Summarize
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('applicants.summary.show', $applicant->summary->applicant_summary_id) }}"
                                        class="px-3 py-1 bg-green-500 text-white rounded-lg hover:bg-green-600">
                                        View Summary
                                    </a>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-gray-500">No applicants found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $applicants->links() }}
        </div>
    </div>
</x-guest-layout>
