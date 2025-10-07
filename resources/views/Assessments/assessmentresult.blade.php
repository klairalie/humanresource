<x-guest-layout>
    <div class="bg-transparent font-sans mx-auto p-6 max-w-7xl mt-10 text-bold">
        <h1 class="text-2xl font-bold mb-6 text-gray-900">Assessment Results</h1>

        @if($results->isEmpty())
            <p class="text-gray-600 italic">No assessment results found.</p>
        @else
            <div class="overflow-x-auto bg-white shadow-md rounded-lg">
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
                                <td class="px-6 py-3 border-b">
                                    {{ $result->assessment->title ?? 'N/A' }}
                                </td>
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
    </div>
</x-guest-layout>
