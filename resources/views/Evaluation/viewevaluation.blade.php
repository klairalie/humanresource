<x-guest-layout>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Evaluation Questions</h1>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('Evaluations.create') }}" 
               class="px-4 py-2 bg-green-600 text-white rounded shadow hover:bg-green-700">
                Add New Evaluation
            </a>
        </div>

        {{-- Evaluation Questions Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-4 py-2 text-left text-gray-600">#</th>
                        <th class="px-4 py-2 text-left text-gray-600">Position</th>
                        <th class="px-4 py-2 text-left text-gray-600">Evaluation Title</th>
                        <th class="px-4 py-2 text-left text-gray-600">Question</th>
                        <th class="px-4 py-2 text-left text-gray-600">Options</th>
                        <th class="px-4 py-2 text-left text-gray-600">Correct Answer</th>
                        <th class="px-4 py-2 text-center text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($evaluationQuestions as $index => $question)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $index + 1 }}</td>
                            <td class="px-4 py-2">{{ $question->evaluation->position_name }}</td>
                            <td class="px-4 py-2">{{ $question->evaluation->title }}</td>
                            <td class="px-4 py-2">{{ $question->question }}</td>
                            <td class="px-4 py-2">
                                <ul class="list-disc pl-4">
                                    <li>A. {{ $question->option_a }}</li>
                                    <li>B. {{ $question->option_b }}</li>
                                    <li>C. {{ $question->option_c }}</li>
                                    <li>D. {{ $question->option_d }}</li>
                                </ul>
                            </td>
                            <td class="px-4 py-2 font-semibold text-green-600">
                                {{ $question->correct_answer }}
                            </td>
                            <td class="px-4 py-2 text-center">
                                <a href="{{ route('Evaluations.edit', $question->id) }}" 
                                   class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">Edit</a>
                                <form action="{{ route('Evaluations.destroy', $question->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                        class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600"
                                        onclick="return confirm('Are you sure you want to delete this question?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-4 text-center text-gray-500">No evaluation questions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-guest-layout>
