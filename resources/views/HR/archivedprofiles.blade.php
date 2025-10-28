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
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Archived Employee Profiles</h1>
        </div>

        <!-- Table Wrapper -->
        <div class="bg-gray-300s shadow-md overflow-hidden">
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
                                <td class="px-4 sm:px-6 py-3 whitespace-nowrap">{{ $arc->archiveprofiles_id }}</td>
                                <td class="px-4 sm:px-6 py-3 whitespace-nowrap">{{ $arc->last_name }}, {{ $arc->first_name }}</td>
                                <td class="px-4 sm:px-6 py-3 whitespace-nowrap">{{ $arc->position }}</td>
                                <td class="px-4 sm:px-6 py-3 whitespace-nowrap">{{ $arc->contact_number }}</td>
                                <td class="px-4 sm:px-6 py-3 whitespace-nowrap">{{ $arc->hire_date }}</td>
                                <td class="px-4 sm:px-6 py-3">{{ $arc->reason }}</td>
                                <td class="px-4 sm:px-6 py-3 whitespace-nowrap">{{ $arc->archived_at }}</td>
                                <td class="px-4 sm:px-6 py-3 whitespace-nowrap">{{ $arc->archived_by }}</td>
                                <td class="px-4 sm:px-6 py-3 text-center">
                                    @if($arc->status === 'deactivated')
                                        <form action="{{ route('archived.reactivate', $arc->archiveprofiles_id) }}" method="POST" class="reactivate-form">
                                            @csrf
                                            @method('PUT')
                                            @if (in_array(session('user_position'), ['Human resource manager']))
                                            <button type="button" 
                                                    class="px-3 py-1 bg-green-700 text-white rounded-md hover:bg-green-600 transition text-sm reactivate-btn">
                                                Reactivate
                                            </button>
                                            @endif
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
