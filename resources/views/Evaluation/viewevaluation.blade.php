<x-guest-layout>
    <div class="max-w-6xl mx-auto p-10 bg-white shadow-lg rounded-2xl mt-12 mb-20">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                <i data-lucide="file-text" class="w-7 h-7 text-green-600"></i>
                Evaluation Questions
            </h1>
            <div class="flex gap-3">
                <a href="{{ route('evaluation.create') }}"
                   class="flex items-center gap-2 text-black hover:text-green-600 transition font-medium">
                    <i data-lucide="file-plus" class="w-5 h-5"></i> Add Question
                </a>
            </div>

            <a href="{{ route('evaluation.send') }}"
                   class="flex items-center gap-2 px-5 py-2 bg-gray-300 hover:bg-gray-400 text-black rounded-lg shadow-md transition">
                    send
                </a>
        </div>

        <!-- Bulk Delete & Table Form -->
        <form action="{{ route('evaluation.bulkDelete') }}" method="POST" id="bulkDeleteForm">
            @csrf
            @method('DELETE')

            <!-- Select All + Delete Button -->
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" id="selectAll" class="cursor-pointer">
                    <label for="selectAll" class="cursor-pointer">Select All</label>
                </div>
                <button type="submit"
                        class="text-black hover:text-red-600 transition"
                        onclick="return confirm('Are you sure you want to delete the selected questions?');">
                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300 rounded-lg shadow-sm">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 text-sm uppercase tracking-wide">
                            <th class="border px-6 py-3 text-center w-12"></th>
                            <th class="border px-6 py-3 text-left w-12">#</th>
                            <th class="border px-6 py-3 text-left w-2/5">Question</th>
                            <th class="border px-6 py-3 text-left w-1/5">Category</th>
                            <th class="border px-6 py-3 text-left w-1/5">Position</th>
                            <th class="border px-6 py-3 text-center w-32">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-800">
                        @forelse($questions as $idx => $q)
                            <tr class="hover:bg-gray-50">
                                <td class="border px-6 py-3 text-center">
                                    <input type="checkbox" name="ids[]" value="{{ $q->evaluation_question_id }}" class="rowCheckbox cursor-pointer">
                                </td>
                                <td class="border px-6 py-3">{{ $idx + 1 }}</td>
                                <td class="border px-6 py-3">{{ $q->question }}</td>
                                <td class="border px-6 py-3">{{ $q->category }}</td>
                                <td class="border px-6 py-3">{{ $q->position }}</td>
                                <td class="border px-6 py-3 text-center">
                                    <button type="button"
                                            class="text-blue-600 hover:underline font-medium"
                                            onclick="openEditModal({{ $q->evaluation_question_id }}, '{{ $q->question }}', '{{ $q->category }}', '{{ $q->position }}')">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="border px-6 py-6 text-center text-gray-500">
                                    No evaluation questions found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        {{-- Pagination --}}
        <div class="mt-10">
            {{ $questions->links() }}
        </div>

        {{-- Edit Modal --}}
        <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
            <div class="bg-white w-full max-w-lg p-6 rounded-lg shadow-lg">
                <h2 class="text-xl font-bold mb-4 text-gray-800">Edit Evaluation Question</h2>

                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editId" name="id">

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Question</label>
                        <input type="text" id="editQuestion" name="question"
                               class="w-full border rounded-lg p-2 text-black" required>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeEditModal()"
                                class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">Cancel</button>
                        <button type="submit"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Save</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Scripts --}}
        <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
        <script>
            lucide.createIcons();

            // Select/Deselect all checkboxes
            document.getElementById('selectAll')?.addEventListener('change', function(e) {
                document.querySelectorAll('.rowCheckbox').forEach(cb => cb.checked = e.target.checked);
            });

            // Open/Close edit modal
            function openEditModal(id, question, category, position) {
                document.getElementById('editModal').classList.remove('hidden');
                document.getElementById('editId').value = id;
                document.getElementById('editQuestion').value = question;
                document.getElementById('editForm').action = `/evaluation/${id}`;
            }

            function closeEditModal() {
                document.getElementById('editModal').classList.add('hidden');
            }

            // AJAX submit for edit
            document.getElementById('editForm').addEventListener('submit', function(e) {
                e.preventDefault();
                let form = this;
                let formData = new FormData(form);
                formData.append('_method', 'PUT'); // ensure PUT method
                let url = form.action;

                fetch(url, {
                    method: "POST",
                    body: formData,
                    headers: { "X-Requested-With": "XMLHttpRequest" }
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) location.reload();
                    else alert('Update failed!');
                })
                .catch(err => console.error(err));
            });
        </script>
    </div>
</x-guest-layout>
