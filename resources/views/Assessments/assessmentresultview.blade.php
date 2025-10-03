<x-guest-layout>
    <div class="max-w-4xl mx-auto py-8">
        <!-- Page Title -->
        <h2 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">
            Assessment Result
        </h2>

        <!-- Card -->
        <div class="bg-white shadow-lg rounded-xl p-6">
            <!-- Applicant Info -->
            <div class="mb-6">
                <h4 class="text-2xl font-semibold text-gray-900">
                    {{ $result->first_name }} {{ $result->last_name }}
                </h4>
                <p class="text-gray-700"><strong>Position:</strong> {{ $result->position_name }}</p>
                <p class="text-gray-700"><strong>Assessment Title:</strong> {{ $result->title }}</p>
            </div>

            <!-- Results Table -->
            <div class="overflow-hidden rounded-lg border border-gray-200">
                <table class="table-auto w-full">
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left bg-gray-50 font-semibold text-gray-700">Ability Score</th>
                            <td class="px-4 py-3">{{ $result->ability_score }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-3 text-left bg-gray-50 font-semibold text-gray-700">Knowledge Score</th>
                            <td class="px-4 py-3">{{ $result->knowledge_score }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-3 text-left bg-gray-50 font-semibold text-gray-700">Strength Score</th>
                            <td class="px-4 py-3">{{ $result->strength_score }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-3 text-left bg-gray-50 font-semibold text-gray-700">Total Score</th>
                            <td class="px-4 py-3 font-bold text-gray-900">{{ $result->total_score }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-3 text-left bg-gray-50 font-semibold text-gray-700">Performance Rating</th>
                            <td class="px-4 py-3 text-blue-600 font-semibold">
                                {{ $result->performance_rating }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex items-center gap-3">
                <a href="{{ url()->previous() }}" 
                   class="px-5 py-2.5 bg-gray-600 text-white text-sm font-medium rounded-lg shadow hover:bg-gray-700 transition">
                   Back
                </a>
                <form action="{{ route('applicant.markScreening', $result->applicant_id) }}" method="POST">
    @csrf
    <button type="submit" 
        class="px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg shadow hover:bg-blue-700 transition">
        Done Viewing
    </button>
</form>

            </div>
        </div>
    </div>
</x-guest-layout>
