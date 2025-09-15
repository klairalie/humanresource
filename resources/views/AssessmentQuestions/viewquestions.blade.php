<x-guest-layout>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Assessment Questions</h1>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('Questions.create') }}"
               class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow">
                + Add Question
            </a>

            <form action="{{ route('Questions.destroyAll') }}" method="POST"
                onsubmit="return confirm('Are you sure you want to delete ALL questions? This cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg shadow">
                    Delete All Questions
                </button>
            </form>
        </div>

        {{-- Success Alert --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-2 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        {{-- Filter Bar --}}
        <div class="mb-6 flex gap-3">
            <label for="positionFilter" class="font-medium text-gray-700">Filter by Position:</label>
            <select id="positionFilter"
                class="border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                <option value="all">All Positions</option>
                @foreach ($positions as $pos)
                    <option value="pos-{{ Str::slug($pos) }}">{{ $pos }}</option>
                @endforeach
            </select>
        </div>

        {{-- Position Tables --}}
        @foreach ($positions as $pos)
            <div class="position-table mb-10" id="pos-{{ Str::slug($pos) }}">
                <h2 class="text-xl font-semibold mb-3 text-gray-800">Position: {{ $pos }}</h2>

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
                                    <td class="px-6 py-4">{{ $q->question }}</td>
                                    <td class="px-6 py-4">{{ $q->correct_answer }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <button type="button"
                                            class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded-md shadow edit-btn"
                                            data-id="{{ $q->assessment_question_id }}"
                                            data-question="{{ $q->question }}"
                                            data-answer="{{ $q->correct_answer }}">
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

                {{-- Pagination per position --}}
                <div class="mt-10">
                    {{ $allQuestions[$pos]->links() }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- Edit Modal --}}
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
            <h2 class="text-xl font-bold mb-4 text-gray-800">Edit Question</h2>

            {{-- The action will be set dynamically in JS --}}
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="editQuestion" class="block text-sm font-medium text-gray-700">Question</label>
                    <textarea id="editQuestion" name="question" rows="3"
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200"
                        required></textarea>
                </div>

                <div class="mb-4">
                    <label for="editAnswer" class="block text-sm font-medium text-gray-700">Correct Answer</label>
                    <input type="text" id="editAnswer" name="correct_answer"
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200"
                        required>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" id="closeModal"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

<script>
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

                // Generate correct route dynamically
                editForm.action = "{{ route('Questions.update', ['assessmentQuestion' => '__ID__']) }}"
                    .replace('__ID__', id);

                editModal.classList.remove("hidden");
                editModal.classList.add("flex");
            });
        });

        closeModal.addEventListener("click", function () {
            editModal.classList.add("hidden");
            editModal.classList.remove("flex");
        });
    });
</script>


</x-guest-layout>
