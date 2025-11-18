<x-guest-layout>

    <style>
        /* ===== Fade-in animation for modals ===== */
        .fade-in {
            animation: fadeIn 0.18s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.97); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>

    <div class="p-8 max-w-4xl mx-auto">

        <h1 class="text-4xl font-bold mb-8 text-black tracking-wide text-center">
            Hiring Announcements
        </h1>

        <!-- Add Button (RIGHT aligned) -->
        <div class="flex justify-end mb-4">
            <button 
                class="bg-gray-900 text-white px-5 py-2 rounded shadow hover:bg-gray-700"
                onclick="openCreateModal()"
            >
                + Add Announcement
            </button>
        </div>

        @if(session('success'))
            <div class="mt-4 bg-green-100 text-black px-4 py-2 border border-green-400 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Announcements List -->
        <div class="mt-6 space-y-6">
            @foreach($announcements as $a)
                <div class="border border-gray-300 rounded-lg shadow-md bg-white p-6">

                    <h2 class="text-2xl font-semibold text-black">{{ $a->title }}</h2>

                    <p class="text-black mt-2 leading-relaxed">
                        {{ Str::limit($a->content, 150) }}
                    </p>

                    <p class="text-sm text-gray-700 mt-3">
                        Status: 
                        <span class="font-semibold {{ $a->is_active ? 'text-green-600' : 'text-red-600' }}">
                            {{ $a->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </p>

                    <div class="mt-4 flex gap-6">

                        <!-- Edit -->
                        <button 
                            class="text-blue-700 underline hover:opacity-70"
                            onclick="openEditModal({{ $a->announcement_id }}, '{{ addslashes($a->title) }}', '{{ addslashes($a->content) }}', {{ $a->is_active }})"
                        >
                            Edit
                        </button>

                        <!-- Delete -->
                        <form action="{{ route('announcements.destroy', $a->announcement_id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button 
                                onclick="return confirm('Delete this announcement?')" 
                                class="text-red-700 underline hover:opacity-70"
                            >
                                Delete
                            </button>
                        </form>

                    </div>

                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $announcements->links() }}
        </div>
    </div>

    <!-- ========================================================= -->
    <!--                       CREATE MODAL                         -->
    <!-- ========================================================= -->
    <div id="createModal" 
         class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm hidden 
                z-[9999] flex items-center justify-center p-4">

        <div class="bg-white p-6 rounded-xl shadow-2xl border border-gray-300 
                    w-full max-w-md fade-in">

            <h2 class="text-2xl font-bold mb-5 text-black text-center">
                Create Announcement
            </h2>

            <form action="{{ route('announcements.store') }}" method="POST">
                @csrf

                <label class="block font-semibold text-black">Title</label>
                <input 
                    type="text" 
                    name="title" 
                    class="w-full border border-gray-400 bg-white p-2 rounded mb-4 text-black"
                    required
                >

                <label class="block font-semibold text-black">Content</label>
                <textarea 
                    name="content" 
                    rows="5" 
                    class="w-full border border-gray-400 bg-white p-2 rounded mb-4 text-black"
                    required
                ></textarea>

                <div class="flex items-center mb-4 text-black">
                    <input type="checkbox" name="is_active" checked>
                    <span class="ml-2">Active</span>
                </div>

                <div class="mt-5 flex justify-end gap-3">
                    <button 
                        type="button" 
                        class="px-4 py-2 bg-gray-300 border border-gray-500 rounded text-black hover:bg-gray-400"
                        onclick="closeCreateModal()"
                    >
                        Cancel
                    </button>

                    <button 
                        class="px-4 py-2 bg-gray-900 text-white rounded hover:bg-gray-700"
                    >
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================= -->
    <!--                         EDIT MODAL                         -->
    <!-- ========================================================= -->
    <div id="editModal" 
         class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm hidden 
                z-[9999] flex items-center justify-center p-4">

        <div class="bg-white p-6 rounded-xl shadow-2xl border border-gray-300 
                    w-full max-w-md fade-in">

            <h2 class="text-2xl font-bold mb-5 text-black text-center">
                Edit Announcement
            </h2>

            <form id="editForm" method="POST">
                @csrf @method('PUT')

                <label class="block font-semibold text-black">Title</label>
                <input 
                    id="edit_title" 
                    type="text" 
                    name="title" 
                    class="w-full border border-gray-400 bg-white p-2 rounded mb-4 text-black"
                    required
                >

                <label class="block font-semibold text-black">Content</label>
                <textarea 
                    id="edit_content" 
                    name="content" 
                    rows="5" 
                    class="w-full border border-gray-400 bg-white p-2 rounded mb-4 text-black"
                    required
                ></textarea>

                <div class="flex items-center mb-4 text-black">
                    <input id="edit_is_active" type="checkbox" name="is_active">
                    <span class="ml-2">Active</span>
                </div>

                <div class="mt-5 flex justify-end gap-3">
                    <button 
                        type="button" 
                        class="px-4 py-2 bg-gray-300 border border-gray-500 rounded text-black hover:bg-gray-400"
                        onclick="closeEditModal()"
                    >
                        Cancel
                    </button>

                    <button 
                        class="px-4 py-2 bg-gray-900 text-white rounded hover:bg-gray-700"
                    >
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================= -->
    <!--                           JS                               -->
    <!-- ========================================================= -->
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
            document.getElementById('editForm').action = "/announcements/" + id;

            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>

</x-guest-layout>
