<x-guest-layout>
    <div class="bg-transparent font-sans mx-auto p-6 max-w-7xl mt-10 text-bold">
        <h1 class="text-2xl font-bold mb-6 text-gray-900">Assessment Results</h1>

        @if($results->isEmpty())
            <p class="text-gray-600 italic">No assessment results found.</p>
        @else
            <div class="overflow-x-auto bg-white shadow-md rounded-lg mb-10">
                <table class="min-w-full border-collapse">
                    <thead class="bg-gray-100 text-gray-800 text-sm uppercase tracking-wide">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold border-b">Applicant Name</th>
                            <th class="px-6 py-3 text-left font-semibold border-b">Assessment Title</th>
                            <th class="px-6 py-3 text-center font-semibold border-b">Ability Score</th>
                            <th class="px-6 py-3 text-center font-semibold border-b">Knowledge Score</th>
                            <th class="px-6 py-3 text-center font-semibold border-b">Strength Score</th>
                            <th class="px-6 py-3 text-center font-semibold border-b">Total Score</th>
                            <th class="px-6 py-3 text-center font-semibold border-b">Performance Rating</th>
                            <th class="px-6 py-3 text-center font-semibold border-b">Submitted At</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @foreach($results as $result)
                            <tr class="hover:bg-gray-50 even:bg-gray-50/50">
                                <td class="px-6 py-3 border-b">
                                    {{ $result->applicant->first_name ?? 'N/A' }} {{ $result->applicant->last_name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-3 border-b">{{ $result->assessment->title ?? 'N/A' }}</td>
                                <td class="px-6 py-3 text-center border-b">{{ $result->ability_score }}</td>
                                <td class="px-6 py-3 text-center border-b">{{ $result->knowledge_score }}</td>
                                <td class="px-6 py-3 text-center border-b">{{ $result->strength_score }}</td>
                                <td class="px-6 py-3 text-center border-b font-bold text-gray-900">{{ $result->total_score }}</td>
                                <td class="px-6 py-3 text-center border-b">
                                    @if($result->performance_rating === 'High')
                                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                            {{ $result->performance_rating }}
                                        </span>
                                    @elseif($result->performance_rating === 'Average')
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">
                                            {{ $result->performance_rating }}
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">
                                            {{ $result->performance_rating }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-center border-b text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($result->submitted_at)->timezone('Asia/Manila')->format('Y-m-d H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- ✅ Evaluation Summary Section --}}
        {{-- ✅ Evaluation Summary Section --}}
<h1 class="text-2xl font-bold mb-6 text-gray-900">Evaluation Summary</h1>

@if($summaries->isEmpty())
    <p class="text-gray-600 italic">No evaluation summaries found.</p>
@else
    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
        <table class="min-w-full border-collapse">
            <thead class="bg-gray-100 text-gray-800 text-sm uppercase tracking-wide">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold border-b">Evaluator</th>
                    <th class="px-6 py-3 text-left font-semibold border-b">Evaluatee</th>
                    <th class="px-6 py-3 text-left font-semibold border-b">Assessment</th>
                    <th class="px-6 py-3 text-center font-semibold border-b">Total Score</th>
                    <th class="px-6 py-3 text-left font-semibold border-b">Category Scores</th>
                    <th class="px-6 py-3 text-left font-semibold border-b">Feedback</th>
                    <th class="px-6 py-3 text-center font-semibold border-b">Created At</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @foreach($summaries as $summary)
                    <tr class="hover:bg-gray-50 even:bg-gray-50/50">
                        <td class="px-6 py-3 border-b">{{ $summary->evaluator->first_name ?? 'N/A' }} {{ $summary->evaluator->last_name ?? '' }}</td>
                        <td class="px-6 py-3 border-b">{{ $summary->evaluatee->first_name ?? 'N/A' }} {{ $summary->evaluatee->last_name ?? '' }}</td>
                        <td class="px-6 py-3 border-b">{{ $summary->assessment->title ?? 'N/A' }}</td>
                        <td class="px-6 py-3 text-center border-b font-bold text-gray-900">{{ $summary->total_score ?? '0' }}</td>
                        <td class="px-6 py-3 border-b text-sm">
                            @if($summary->category_scores)
                                @foreach(json_decode($summary->category_scores, true) as $category => $score)
                                    <div><strong>{{ ucfirst($category) }}:</strong> {{ $score }}</div>
                                @endforeach
                            @else
                                <span class="italic text-gray-500">No category scores</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 border-b text-sm text-gray-700">{{ $summary->feedback ?? 'No feedback' }}</td>
                        <td class="px-6 py-3 text-center border-b text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($summary->created_at)->timezone('Asia/Manila')->format('Y-m-d H:i') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ✅ Pagination Controls --}}
    <div class="mt-10 mb-10">
        {{ $summaries->links('pagination::tailwind') }}
    </div>
@endif
iv>
</x-guest-layout>
