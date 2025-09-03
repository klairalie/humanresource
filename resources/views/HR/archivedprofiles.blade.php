<x-guest-layout>
    <div class="min-h-screen bg-gray-100 p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Archived Employee Profiles</h1>
            <a href="{{ route('archived.logout') }}" 
               class="px-4 py-2 bg-red-500 text-white rounded-md shadow hover:bg-red-600 transition">
                Logout
            </a>
        </div>

        <!-- Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full table-auto border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left">ID</th>
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Position</th>
                        <th class="px-6 py-3 text-left">Contact</th>
                        <th class="px-6 py-3 text-left">Hire Date</th>
                        <th class="px-6 py-3 text-left">Reason</th>
                        <th class="px-6 py-3 text-left">Archived At</th>
                        <th class="px-6 py-3 text-left">Archived By</th>
                        <th class="px-6 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($archives as $arc)
                        <tr>
                            <td class="px-6 py-3">{{ $arc->archiveprofile_id }}</td>
                            <td class="px-6 py-3">{{ $arc->last_name }}, {{ $arc->first_name }}</td>
                            <td class="px-6 py-3">{{ $arc->position }}</td>
                            <td class="px-6 py-3">{{ $arc->contact_info }}</td>
                            <td class="px-6 py-3">{{ $arc->hire_date }}</td>
                            <td class="px-6 py-3">{{ $arc->reason }}</td>
                            <td class="px-6 py-3">{{ $arc->archived_at }}</td>
                            <td class="px-6 py-3">{{ $arc->archived_by }}</td>
                            <td class="px-6 py-3 text-center">
                                @if($arc->status === 'deactivated')
                                    <form action="{{ route('archived.reactivate', $arc->archiveprofile_id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" 
                                                class="px-3 py-1 bg-green-500 text-white rounded-md hover:bg-green-600 transition">
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
</x-guest-layout>
