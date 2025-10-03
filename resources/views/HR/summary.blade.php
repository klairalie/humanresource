<x-guest-layout>
    <style>[x-cloak] { display: none !important; }</style>

    <div x-data="{ showResume: false, showCOE: false, showGoodMoral: false }"
         x-cloak
         class="min-h-screen p-8 flex flex-col items-center bg-gray-100">

        <div class="bg-white shadow-2xl rounded-2xl p-8 w-full max-w-5xl border border-gray-200 mb-20">
            
            <!-- Header -->
            <div class="border-b border-gray-200 pb-5 mb-8">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                    Applicant Summary
                </h1> 
            </div>

            <!-- Ratings Summary -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                <!-- Summary Rating -->
                <div class="flex flex-col items-start bg-gray-50 p-4 rounded-xl border">
                    <p class="text-sm text-gray-600 mb-1">Summary Rating</p>
                    <span class="px-4 py-1.5 rounded-full font-semibold text-base shadow-sm
                        {{ $summary->performance_rating == 'High' ? 'bg-green-100 text-green-800 ring-1 ring-green-300' : 
                           ($summary->performance_rating == 'Average' ? 'bg-yellow-100 text-yellow-800 ring-1 ring-yellow-300' : 'bg-red-100 text-red-800 ring-1 ring-red-300') }}">
                        {{ $summary->performance_rating }}
                    </span>
                </div>

                <!-- Assessment Test Rating -->
                <div class="flex flex-col items-start bg-gray-50 p-4 rounded-xl border">
                    <p class="text-sm text-gray-600 mb-1">Assessment Test Rating</p>
                    <span class="px-4 py-1.5 rounded-full font-semibold text-base shadow-sm
                        {{ $testRating == 'High' ? 'bg-green-100 text-green-800 ring-1 ring-green-300' : 
                           ($testRating == 'Average' ? 'bg-yellow-100 text-yellow-800 ring-1 ring-yellow-300' : 'bg-red-100 text-red-800 ring-1 ring-red-300') }}">
                        {{ $testRating }}
                    </span>
                </div>

                <!-- Assessment Total Score -->
                <div class="flex flex-col items-start bg-gray-50 p-4 rounded-xl border">
                    <p class="text-sm text-gray-600 mb-1">Total Score</p>
                    <span class="px-4 py-1.5 rounded-full font-semibold text-base shadow-sm bg-blue-100 text-blue-800 ring-1 ring-blue-300">
                        {{ $summary->total_score ?? $testTotalScore }}
                    </span>
                </div>

                <!-- Capability Result -->
                <div class="flex flex-col items-start bg-gray-50 p-4 rounded-xl border">
                    <p class="text-sm text-gray-600 mb-1">Capability Result</p>
                    <span class="px-4 py-1.5 rounded-full font-semibold text-base shadow-sm bg-purple-100 text-purple-800 ring-1 ring-purple-300">
                        {{ $summary->capability_result ?? 'N/A' }}
                    </span>
                </div>
            </div>

            <!-- Applicant Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                <div>
                    <p class="text-sm text-gray-500">Applicant</p>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ $summary->applicant->first_name }} {{ $summary->applicant->last_name }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Position</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $summary->position }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Career Objective</p>
                    <p class="text-gray-800">{{ $summary->career_objective }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Achievements</p>
                    <p class="text-gray-800">{{ $summary->achievements }}</p>
                </div>
            </div>

            <!-- Skills -->
            <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 shadow-sm mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">Skills</h2>
                <p class="text-gray-700">{{ $summary->skills }}</p>
            </div>

            <!-- Matched Skills -->
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">
                    Matched Skills ({{ count(json_decode($summary->matched_skills, true) ?? []) }})
                </h2>
                <ul class="space-y-2 text-gray-700 list-disc list-inside">
                    @forelse(json_decode($summary->matched_skills, true) ?? [] as $skill)
                        <li>{{ $skill }}</li>
                    @empty
                        <li class="italic text-gray-500">No matched skills found.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Matched Objectives -->
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">
                    Matched Objectives ({{ count(json_decode($summary->matched_career_objective, true) ?? []) }})
                </h2>
                <ul class="space-y-2 text-gray-700 list-disc list-inside">
                    @forelse(json_decode($summary->matched_career_objective, true) ?? [] as $obj)
                        <li>{{ $obj }}</li>
                    @empty
                        <li class="italic text-gray-500">No matched objectives found.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Uploaded Files -->
            <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 shadow-sm mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">Uploaded Files</h2>
                <ul class="space-y-3 text-gray-700">
                    @if($summary->applicant->resume_file)
                        <li>
                            <button @click="showResume = true" class="text-blue-600 hover:underline font-medium">📄 View Resume</button>
                        </li>
                    @else
                        <li class="italic text-gray-500">No Resume uploaded.</li>
                    @endif

                    @if($summary->applicant->coe_file)
                        <li>
                            <button @click="showCOE = true" class="text-blue-600 hover:underline font-medium">📄 Certificate of Employment</button>
                        </li>
                    @else
                        <li class="italic text-gray-500">No COE uploaded.</li>
                    @endif

                    @if($summary->applicant->good_moral_file)
                        <li>
                            <button @click="showGoodMoral = true" class="text-blue-600 hover:underline font-medium">📄 Good Moral Certificate</button>
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

            <!-- Action Buttons -->
            <div class="mt-8 flex justify-end gap-4" id="actionButtons">
                @if($summary->applicant->applicant_status === 'Passed Screening')
                    <a href="{{ route('review.document', $summary->applicant->applicant_id) }}" 
                       class="inline-block px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-700 text-white font-semibold rounded-lg shadow hover:from-blue-600 hover:to-blue-800 transition">
                        📑 Review Documents
                    </a>
                @endif

                @if($summary->applicant->applicant_status !== 'Passed Screening' && $summary->applicant->applicant_status !== 'Failed Screening')
                    <button type="button" id="passedBtn"
                        data-id="{{ $summary->applicant->applicant_id }}"
                        class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-700 text-white font-semibold rounded-lg shadow hover:from-green-600 hover:to-green-800 transition">
                        ✅ Passed Screening
                    </button>
                    
                    <button type="button" id="failedBtn"
                        data-id="{{ $summary->applicant->applicant_id }}"
                        class="px-6 py-3 bg-gradient-to-r from-red-500 to-red-700 text-white font-semibold rounded-lg shadow hover:from-red-600 hover:to-red-800 transition">
                        ❌ Failed Screening
                    </button>
                @endif
            </div>

            <!-- Success & Failure Alerts -->
            <div id="successMessage" class="hidden mt-4 text-green-700 font-semibold">
                🎉 Applicant marked as Passed Screening! Notification sent.
            </div>

            <div id="failureMessage" class="hidden mt-4 text-red-700 font-semibold">
                ❌ Applicant marked as Failed Screening. Apology email sent.
            </div>
        </div>
    </div>

    <script>
    @if($summary->applicant->applicant_status !== 'Passed Screening' && $summary->applicant->applicant_status !== 'Failed Screening')
    document.getElementById("passedBtn").addEventListener("click", function() {
        let applicantId = this.dataset.id;

        fetch(`/applicants/${applicantId}/pass`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        })
        .catch(err => console.error(err));
    });

    document.getElementById("failedBtn").addEventListener("click", function() {
        let applicantId = this.dataset.id;

        fetch(`/applicants/${applicantId}/fail`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        })
        .catch(err => console.error(err));
    });
    @endif
    </script>
</x-guest-layout>
