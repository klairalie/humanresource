<x-guest-layout>
    <!-- Alpine x-cloak CSS -->
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <div x-data="{ showResume: false, showCOE: false, showGoodMoral: false }" 
         x-cloak 
         class="min-h-screen p-6 flex flex-col items-center">
        
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 shadow-2xl rounded-2xl p-8 w-full max-w-4xl border border-gray-200 mb-20">
            
            <!-- Header -->
            <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-6">
                <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">
                    Applicant Summary
                </h1> 
                <p>Performance Rating: 
                    <span class="px-4 py-1.5 rounded-full text-sm font-semibold shadow-sm
                        {{ $summary->performance_rating == 'High' ? 'bg-green-100 text-green-800 ring-1 ring-green-300 text-lg' : 
                           ($summary->performance_rating == 'Average' ? 'bg-yellow-100 text-yellow-800 ring-1 ring-yellow-300 text-lg' : 'bg-red-100 text-red-800 ring-1 ring-red-300 text-lg') }}">
                        {{ $summary->performance_rating }}
                    </span>
                </p>
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
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">
                    Matched Objectives ({{ count(json_decode($summary->matched_career_objective, true) ?? []) }})
                </h2>
                <ul class="space-y-2 text-gray-700 list-disc list-inside">
                    @forelse(json_decode($summary->matched_career_objective, true) ?? [] as $obj)
                        <li class="pl-2">{{ $obj }}</li>
                    @empty
                        <li class="italic text-gray-500">No matched objectives found.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Uploaded Files -->
            <div class="bg-gray-50 p-5 rounded-xl border border-gray-200 shadow-sm mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Uploaded Files</h2>
                <ul class="space-y-3 text-gray-700">
                    @if($summary->applicant->resume_file)
                        <li>
                            <button @click="showResume = true"
                                    class="text-blue-600 hover:underline font-medium">
                                📄 View Resume
                            </button>
                        </li>
                    @else
                        <li class="italic text-gray-500">No Resume uploaded.</li>
                    @endif

                    @if($summary->applicant->coe_file)
                        <li>
                            <button @click="showCOE = true"
                                    class="text-blue-600 hover:underline font-medium">
                                📄 Certificate of Employment
                            </button>
                        </li>
                    @else
                        <li class="italic text-gray-500">No COE uploaded.</li>
                    @endif

                    @if($summary->applicant->good_moral_file)
                        <li>
                            <button @click="showGoodMoral = true"
                                    class="text-blue-600 hover:underline font-medium">
                                📄 Good Moral Certificate
                            </button>
                        </li>
                    @else
                        <li class="italic text-gray-500">No Good Moral uploaded.</li>
                    @endif
                </ul>
            </div>

           <!-- Resume Modal -->
<div x-show="showResume" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white w-11/12 md:w-3/4 lg:w-1/2 rounded-2xl shadow-lg p-6 relative">
        <button @click="showResume = false" class="absolute top-3 right-3 text-gray-600 hover:text-black">✖</button>
        <h3 class="text-xl font-semibold mb-4">Applicant Resume</h3>

        <!-- Load the resume viewer -->
        <iframe src="{{ route('applicant.resume.view', $summary->applicant->applicant_id) }}"
                class="w-full h-[500px] rounded-lg border"></iframe>
    </div>
</div>


            <!-- COE Modal -->
            <div x-show="showCOE" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white w-11/12 md:w-3/4 lg:w-1/2 rounded-2xl shadow-lg p-6 relative">
                    <button @click="showCOE = false" class="absolute top-3 right-3 text-gray-600 hover:text-black">✖</button>
                    <h3 class="text-xl font-semibold mb-4">Certificate of Employment</h3>
                    <iframe src="{{ asset('storage/' . $summary->applicant->coe_file) }}" 
                            class="w-full h-[500px] rounded-lg border"></iframe>
                </div>
            </div>

            <!-- Good Moral Modal -->
            <div x-show="showGoodMoral" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white w-11/12 md:w-3/4 lg:w-1/2 rounded-2xl shadow-lg p-6 relative">
                    <button @click="showGoodMoral = false" class="absolute top-3 right-3 text-gray-600 hover:text-black">✖</button>
                    <h3 class="text-xl font-semibold mb-4">Good Moral Certificate</h3>
                    <iframe src="{{ asset('storage/' . $summary->applicant->good_moral_file) }}" 
                            class="w-full h-[500px] rounded-lg border"></iframe>
                </div>
            </div>

            <!-- Back Button -->
            <div class="mt-8 text-right">
                <a href="{{ route('show.listapplicants') }}" 
                   class="inline-block px-6 py-2.5 bg-gradient-to-r from-gray-600 to-gray-800 text-white font-semibold rounded-lg shadow hover:from-gray-700 hover:to-gray-900 transition ease-in-out duration-200">
                    ← Back to Applicants
                </a>
            </div>
        </div>

        <!-- Review Document Button Outside Form -->
        <div class="mt-1 w-full max-w-4xl flex justify-center">
            <a href="{{ route('review.document', $summary->applicant->applicant_id) }}" 
               class="inline-block px-6 py-2.5 bg-gradient-to-r from-orange-200 to-gray-400 text-white font-semibold rounded-lg shadow hover:from-blue-700 hover:to-blue-900 transition ease-in-out duration-200 mb-20">
                Review Document
            </a>
        </div>
    </div>
</x-guest-layout>
