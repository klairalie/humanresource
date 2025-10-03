<x-guest-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Assessment Results</h1>

        @if($results->isEmpty())
            <p class="text-gray-500">No assessment results found.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border">Applicant Name</th>
                            <th class="px-4 py-2 border">Assessment Title</th>
                            <th class="px-4 py-2 border">Ability Score</th>
                            <th class="px-4 py-2 border">Knowledge Score</th>
                            <th class="px-4 py-2 border">Strength Score</th>
                            <th class="px-4 py-2 border">Total Score</th>
                            <th class="px-4 py-2 border">Performance Rating</th>
                            <th class="px-4 py-2 border">Submitted At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $result)
                            <tr class="text-center border-b hover:bg-gray-50">
                                <td class="px-4 py-2 border">{{ $result->applicant->first_name ?? 'N/A' }} , {{ $result->applicant->last_name ?? 'N/A' }}</td>
                                <td class="px-4 py-2 border">{{ $result->assessment->title ?? 'N/A' }}</td>
                                <td class="px-4 py-2 border">{{ $result->ability_score }}</td>
                                <td class="px-4 py-2 border">{{ $result->knowledge_score }}</td>
                                <td class="px-4 py-2 border">{{ $result->strength_score }}</td>
                                <td class="px-4 py-2 border font-semibold">{{ $result->total_score }}</td>
                                <td class="px-4 py-2 border">
                                    @if($result->performance_rating === 'High')
                                        <span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-sm font-semibold">{{ $result->performance_rating }}</span>
                                    @elseif($result->performance_rating === 'Average')
                                        <span class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded-full text-sm font-semibold">{{ $result->performance_rating }}</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-200 text-red-800 rounded-full text-sm font-semibold">{{ $result->performance_rating }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 border">
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
