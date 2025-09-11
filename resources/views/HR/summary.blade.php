<x-guest-layout>
    <div class="min-h-screen p-6 flex items-center justify-center">
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 shadow-2xl rounded-2xl p-8 w-full max-w-4xl border border-gray-200">
            
            <!-- Header -->
            <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-6">
                <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">
                    Applicant Summary
                </h1> 
                    <p>Performance Rating: <span class="px-4 py-1.5 rounded-full text-sm font-semibold shadow-sm
                    {{ $summary->performance_rating == 'High' ? 'bg-green-100 text-green-800 ring-1 ring-green-300 text-lg' : 
                       ($summary->performance_rating == 'Average' ? 'bg-yellow-100 text-yellow-800 ring-1 ring-yellow-300 text-lg' : 'bg-red-100 text-red-800 ring-1 ring-red-300 text-lg') }}">
                    {{ $summary->performance_rating }}
                </span></p>
            </div>

            <!-- Applicant Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Applicant</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ $summary->applicant->first_name }} {{ $summary->applicant->last_name }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm font-medium">Position</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $summary->position }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm font-medium">Career Objective</p>
                    <p class="text-gray-800">{{ $summary->career_objective }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm font-medium">Achievements</p>
                    <p class="text-gray-800">{{ $summary->achievements }}</p>
                </div>
            </div>

            <!-- Skills -->
            <div class="bg-gray-50 p-5 rounded-xl border border-gray-200 shadow-sm mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Skills</h2>
                <p class="text-gray-700">{{ $summary->skills }}</p>
            </div>

            <!-- Matched Skills -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">
                    Matched Skills ({{ count(json_decode($summary->matched_skills, true) ?? []) }})
                </h2>
                <ul class="space-y-2 text-gray-700 list-disc list-inside">
                    @forelse(json_decode($summary->matched_skills, true) ?? [] as $skill)
                        <li class="pl-2">{{ $skill }}</li>
                    @empty
                        <li class="italic text-gray-500">No matched skills found.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Matched Objectives -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">
                    Matched Objectives ({{ count(json_decode($summary->matched_objectives, true) ?? []) }})
                </h2>
                <ul class="space-y-2 text-gray-700 list-disc list-inside">
                    @forelse(json_decode($summary->matched_career_objective, true) ?? [] as $obj)
                        <li class="pl-2">{{ $obj }}</li>
                    @empty
                        <li class="italic text-gray-500">No matched objectives found.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Back Button -->
            <div class="mt-8 text-center">
                <a href="{{ route('show.listapplicants') }}" 
                   class="inline-block px-6 py-2.5 bg-gradient-to-r from-gray-600 to-gray-800 text-white font-semibold rounded-lg shadow hover:from-gray-700 hover:to-gray-900 transition ease-in-out duration-200">
                    ← Back to Applicants
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
