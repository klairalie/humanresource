<x-guest-layout>
    <div class="min-h-screen bg-gray-100 p-4 sm:p-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Archived Employee Profiles</h1>
            <a href="{{ route('archived.logout') }}" 
               class="px-4 py-2 bg-red-500 text-white rounded-md shadow hover:bg-red-600 transition text-center">
                Logout
            </a>
        </div>

        <!-- Table Wrapper (scrollable on small screens) -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto border border-gray-200 text-sm sm:text-base">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left">ID</th>
                            <th class="px-4 sm:px-6 py-3 text-left">Name</th>
                            <th class="px-4 sm:px-6 py-3 text-left">Position</th>
                            <th class="px-4 sm:px-6 py-3 text-left">Contact</th>
                            <th class="px-4 sm:px-6 py-3 text-left">Hire Date</th>
                            <th class="px-4 sm:px-6 py-3 text-left">Reason</th>
                            <th class="px-4 sm:px-6 py-3 text-left">Archived At</th>
                            <th class="px-4 sm:px-6 py-3 text-left">Archived By</th>
                            <th class="px-4 sm:px-6 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($archives as $arc)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 sm:px-6 py-3 whitespace-nowrap">{{ $arc->archiveprofile_id }}</td>
                                <td class="px-4 sm:px-6 py-3 whitespace-nowrap">{{ $arc->last_name }}, {{ $arc->first_name }}</td>
                                <td class="px-4 sm:px-6 py-3 whitespace-nowrap">{{ $arc->position }}</td>
                                <td class="px-4 sm:px-6 py-3 whitespace-nowrap">{{ $arc->contact_info }}</td>
                                <td class="px-4 sm:px-6 py-3 whitespace-nowrap">{{ $arc->hire_date }}</td>
                                <td class="px-4 sm:px-6 py-3">{{ $arc->reason }}</td>
                                <td class="px-4 sm:px-6 py-3 whitespace-nowrap">{{ $arc->archived_at }}</td>
                                <td class="px-4 sm:px-6 py-3 whitespace-nowrap">{{ $arc->archived_by }}</td>
                                <td class="px-4 sm:px-6 py-3 text-center">
                                    @if($arc->status === 'deactivated')
                                        <form action="{{ route('archived.reactivate', $arc->archiveprofile_id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" 
                                                    class="px-3 py-1 bg-green-500 text-white rounded-md hover:bg-green-600 transition text-sm">
                                                Reactivate
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-500 italic">Active</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-guest-layout>
