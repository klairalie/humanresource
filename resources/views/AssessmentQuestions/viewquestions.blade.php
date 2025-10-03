<x-guest-layout>
    <div class="p-6">
        <!-- Page Title -->
        <div class="flex items-center gap-2 mb-6">
            <i data-lucide="file-question" class="w-6 h-6 text-green-600"></i>
            <h1 class="text-2xl font-bold text-gray-800">Assessment Questions</h1>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('Questions.create') }}"
               class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow transition">
                <i data-lucide="plus-circle" class="w-5 h-5"></i>
                Add Question
            </a>

            <form action="{{ route('Questions.destroyAll') }}" method="POST"
                onsubmit="return confirm('Are you sure you want to delete ALL questions? This cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg shadow transition">
                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                    Delete All
                </button>
            </form>
        </div>

        {{-- Success Alert --}}
        @if (session('success'))
            <div class="flex items-center gap-2 bg-green-100 border border-green-300 text-green-700 px-4 py-2 rounded-lg mb-6">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Filter Bar --}}
        <div class="mb-6 flex items-center gap-3">
            <label for="positionFilter" class="flex items-center gap-2 font-medium text-gray-700">
                <i data-lucide="filter" class="w-5 h-5 text-gray-500"></i>
                Filter by Position:
            </label>
            <select id="positionFilter"
                class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-300">
                <option value="all">All Positions</option>
                @foreach ($positions as $pos)
                    <option value="pos-{{ Str::slug($pos) }}">{{ $pos }}</option>
                @endforeach
            </select>
        </div>

        {{-- Position Tables --}}
        @foreach ($positions as $pos)
            <div class="position-table mb-10" id="pos-{{ Str::slug($pos) }}">
                <div class="flex items-center gap-2 mb-3">
                    <i data-lucide="briefcase" class="w-5 h-5 text-gray-600"></i>
                    <h2 class="text-xl font-semibold text-gray-800">Position: {{ $pos }}</h2>
                </div>

                <div class="overflow-x-auto bg-white shadow-md rounded-lg">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-center text-gray-700 uppercase text-sm font-semibold">
                                <th class="px-6 py-3">Level</th>
                                <th class="px-6 py-3">Question</th>
                                <th class="px-6 py-3">Correct Answer</th>
                                <th class="px-6 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-800 text-sm text-center">
                            @forelse($allQuestions[$pos] as $q)
                                <tr class="border-b hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 capitalize">{{ $q->level }}</td>
                                    <td class="px-6 py-4 text-left">{{ $q->question }}</td>
                                    <td class="px-6 py-4">{{ $q->correct_answer }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <button type="button"
                                            class="flex items-center gap-1 px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded-md shadow edit-btn transition"
                                            data-id="{{ $q->assessment_question_id }}"
                                            data-question="{{ $q->question }}"
                                            data-answer="{{ $q->correct_answer }}">
                                            <i data-lucide="pencil" class="w-4 h-4"></i>
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-6 text-gray-500">
                                        No questions found for {{ $pos }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $allQuestions[$pos]->links() }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- Edit Modal --}}
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6 relative">
            <div class="flex items-center gap-2 mb-4">
                <i data-lucide="pencil" class="w-6 h-6 text-blue-600"></i>
                <h2 class="text-xl font-bold text-gray-800">Edit Question</h2>
            </div>

            {{-- The action will be set dynamically in JS --}}
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="editQuestion" class="block text-sm font-medium text-gray-700">Question</label>
                    <textarea id="editQuestion" name="question" rows="3"
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-300"
                        required></textarea>
                </div>

                <div class="mb-4">
                    <label for="editAnswer" class="block text-sm font-medium text-gray-700">Correct Answer</label>
                    <input type="text" id="editAnswer" name="correct_answer"
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-300"
                        required>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" id="closeModal"
                        class="flex items-center gap-2 px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg transition">
                        <i data-lucide="x" class="w-4 h-4"></i>
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script>
        lucide.createIcons();

        document.addEventListener("DOMContentLoaded", function () {
            const editButtons = document.querySelectorAll(".edit-btn");
            const editModal = document.getElementById("editModal");
            const closeModal = document.getElementById("closeModal");
            const editForm = document.getElementById("editForm");
            const questionInput = document.getElementById("editQuestion");
            const answerInput = document.getElementById("editAnswer");

            editButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const id = this.dataset.id;
                    const question = this.dataset.question;
                    const answer = this.dataset.answer;

                    questionInput.value = question;
                    answerInput.value = answer;

                    editForm.action = "{{ route('Questions.update', ['assessmentQuestion' => '__ID__']) }}"
                        .replace('__ID__', id);

                    editModal.classList.remove("hidden");
                    editModal.classList.add("flex");
                    lucide.createIcons();
                });
            });

            closeModal.addEventListener("click", function () {
                editModal.classList.add("hidden");
                editModal.classList.remove("flex");
            });
        });
    </script>
</x-guest-layout>
