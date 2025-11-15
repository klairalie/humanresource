<x-guest-layout>
    <div class="p-6 max-w-5xl mx-auto">

        <h1 class="text-2xl font-bold mb-4">Hiring Announcements</h1>

        <!-- Button Open Create Modal -->
        <button 
            class="bg-blue-600 text-white px-4 py-2 rounded" 
            onclick="openCreateModal()"
        >
            + Add Announcement
        </button>

        @if(session('success'))
            <div class="mt-4 bg-green-100 text-green-700 px-4 py-2 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Announcements List -->
        <div class="mt-6">
            @foreach($announcements as $a)
                <div class="border p-4 mb-4 rounded">
                    <h2 class="text-xl font-semibold">{{ $a->title }}</h2>
                    <p class="text-gray-700">{{ Str::limit($a->content, 150) }}</p>

                    <p class="text-sm text-gray-500 mt-2">
                        Status: <strong>{{ $a->is_active ? 'Active' : 'Inactive' }}</strong>
                    </p>

                    <div class="mt-3 flex gap-3">
                        <!-- Edit -->
                        <button 
                            class="text-green-600"
                            onclick="openEditModal({{ $a->announcement_id }}, '{{ addslashes($a->title) }}', '{{ addslashes($a->content) }}', {{ $a->is_active }})"
                        >
                            Edit
                        </button>

                        <!-- Delete -->
                        <form action="{{ route('announcements.destroy', $a->announcement_id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Delete this announcement?')" class="text-red-600">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{ $announcements->links() }}
    </div>

    <!-- CREATE MODAL -->
    <div id="createModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center">
        <div class="bg-white p-6 rounded w-96">
            <h2 class="text-xl font-bold mb-4">Create Announcement</h2>

            <form action="{{ route('announcements.store') }}" method="POST">
                @csrf
                <label class="block font-semibold">Title</label>
                <input type="text" name="title" class="w-full border p-2 rounded mb-3" required>

                <label class="block font-semibold">Content</label>
                <textarea name="content" rows="5" class="w-full border p-2 rounded mb-3" required></textarea>

                <label class="inline-flex items-center mb-3">
                    <input type="checkbox" name="is_active" checked>
                    <span class="ml-2">Active</span>
                </label>

                <div class="mt-4 flex justify-end gap-3">
                    <button 
                        type="button" 
                        class="px-3 py-2 bg-gray-300 rounded"
                        onclick="closeCreateModal()"
                    >
                        Cancel
                    </button>

                    <button class="px-3 py-2 bg-blue-600 text-white rounded">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center">
        <div class="bg-white p-6 rounded w-96">
            <h2 class="text-xl font-bold mb-4">Edit Announcement</h2>

            <form id="editForm" method="POST">
                @csrf
                @method('PUT')

                <label class="block font-semibold">Title</label>
                <input id="edit_title" type="text" name="title" class="w-full border p-2 rounded mb-3" required>

                <label class="block font-semibold">Content</label>
                <textarea id="edit_content" name="content" rows="5" class="w-full border p-2 rounded mb-3" required></textarea>

                <label class="inline-flex items-center mb-3">
                    <input id="edit_is_active" type="checkbox" name="is_active">
                    <span class="ml-2">Active</span>
                </label>

                <div class="mt-4 flex justify-end gap-3">
                    <button 
                        type="button" 
                        class="px-3 py-2 bg-gray-300 rounded"
                        onclick="closeEditModal()"
                    >
                        Cancel
                    </button>

                    <button class="px-3 py-2 bg-green-600 text-white rounded">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script>
        function openCreateModal() {
            document.getElementById('createModal').classList.remove('hidden');
        }
        function closeCreateModal() {
            document.getElementById('createModal').classList.add('hidden');
        }

        function openEditModal(id, title, content, is_active) {
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_content').value = content;
            document.getElementById('edit_is_active').checked = is_active == 1;

            document.getElementById('editForm').action =
                "/announcements/" + id;

            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>

</x-guest-layout>
