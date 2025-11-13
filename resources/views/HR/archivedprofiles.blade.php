<x-guest-layout>
    <div class="min-h-screen bg-gray-100 p-4 sm:p-6 text-black">

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        {{-- ✅ Show success message after reactivation --}}
        @if (session('success'))
            <script>
                window.addEventListener('load', () => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: '{{ session('success') }}',
                        confirmButtonColor: '#f59e0b',
                        timer: 2500,
                        showConfirmButton: false
                    });
                });
            </script>
        @endif

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <h1 class="text-lg sm:text-xl font-semibold text-gray-800">Archived Employee Profiles</h1>
        </div>

        <!-- Table Wrapper -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table-fixed min-w-full border border-gray-200 text-xs sm:text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="w-14 px-2 py-2 text-left">ID</th>
                            <th class="w-32 px-2 py-2 text-left">Name</th>
                            <th class="w-28 px-2 py-2 text-left">Position</th>
                            <th class="w-28 px-2 py-2 text-left">Contact</th>
                            <th class="w-28 px-2 py-2 text-left">Hire Date</th>
                            <th class="w-40 px-2 py-2 text-left truncate">Reason</th>
                            <th class="w-36 px-2 py-2 text-left">Archived At</th>
                            <th class="w-28 px-2 py-2 text-left">Archived By</th>
                            <th class="w-20 px-2 py-2 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($archives as $arc)
                            <tr class="hover:bg-gray-50">
                                <td class="px-2 py-2 whitespace-nowrap text-gray-700">{{ $arc->archiveprofiles_id }}</td>
                                <td class="px-2 py-2 whitespace-nowrap text-gray-700 truncate">{{ $arc->last_name }}, {{ $arc->first_name }}</td>
                                <td class="px-2 py-2 whitespace-nowrap text-gray-700">{{ $arc->position }}</td>
                                <td class="px-2 py-2 whitespace-nowrap text-gray-700">{{ $arc->contact_number }}</td>
                                <td class="px-2 py-2 whitespace-nowrap text-gray-700">{{ $arc->hire_date }}</td>
                                <td class="px-2 py-2 text-gray-700 truncate max-w-[150px]">{{ $arc->reason }}</td>
                                <td class="px-2 py-2 whitespace-nowrap text-gray-700">
                                    {{ \Carbon\Carbon::parse($arc->archived_at)->format('M d, Y h:i A') }}
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap text-gray-700">{{ $arc->archived_by }}</td>
                                <td class="px-2 py-2 text-center">
                                    @if($arc->status === 'deactivated')
                                        <form action="{{ route('archived.reactivate', $arc->archiveprofiles_id) }}" method="POST" class="reactivate-form">
                                            @csrf
                                            @method('PUT')
                                            @if (in_array(session('user_position'), ['Human resource manager']))
                                            <button type="button" 
                                                    class="px-2 py-1 bg-green-700 text-white rounded-md hover:bg-green-600 transition text-xs reactivate-btn">
                                                Reactivate
                                            </button>
                                            @endif
                                        </form>
                                    @else
                                        <span class="text-gray-500 italic text-xs">Active</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ✅ Pagination Links --}}
            <div class="px-4 py-3 bg-white border-t border-gray-200">
                {{ $archives->links('pagination::tailwind') }}
            </div>
        </div>
    </div>

    {{-- ✅ SweetAlert Confirm Reactivation --}}
    <script>
        document.querySelectorAll('.reactivate-btn').forEach(button => {
            button.addEventListener('click', function (e) {
                const form = this.closest('form');
                Swal.fire({
                    title: 'Confirm Reactivation',
                    text: "Are you sure you want to reactivate this employee profile?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Reactivate',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
</x-guest-layout>
