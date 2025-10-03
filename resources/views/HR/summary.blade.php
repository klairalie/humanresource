<x-guest-layout>
    <style>[x-cloak] { display: none !important; }</style>

    <div x-data="{ showResume: false, showCOE: false, showGoodMoral: false }"
         x-cloak
         class="min-h-screen p-6 flex flex-col items-center">

        <div class="bg-gradient-to-br from-gray-50 to-gray-100 shadow-2xl rounded-2xl p-8 w-full max-w-4xl border border-gray-200 mb-20">
            
            <!-- Header -->
            <div class="border-b border-gray-200 pb-4 mb-6">
                <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight mb-4">
                    Applicant Summary
                </h1> 

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Summary Rating -->
                    <p>Summary Rating: 
                        <span class="px-4 py-1.5 rounded-full text-sm font-semibold shadow-sm
                            {{ $summary->performance_rating == 'High' ? 'bg-green-100 text-green-800 ring-1 ring-green-300 text-lg' : 
                               ($summary->performance_rating == 'Average' ? 'bg-yellow-100 text-yellow-800 ring-1 ring-yellow-300 text-lg' : 'bg-red-100 text-red-800 ring-1 ring-red-300 text-lg') }}">
                            {{ $summary->performance_rating }}
                        </span>
                    </p>

                    <!-- Assessment Test Rating -->
                    <p>Assessment Test Rating: 
                        <span class="px-4 py-1.5 rounded-full text-sm font-semibold shadow-sm
                            {{ $testRating == 'High' ? 'bg-green-100 text-green-800 ring-1 ring-green-300 text-lg' : 
                               ($testRating == 'Average' ? 'bg-yellow-100 text-yellow-800 ring-1 ring-yellow-300 text-lg' : 'bg-red-100 text-red-800 ring-1 ring-red-300 text-lg') }}">
                            {{ $testRating }}
                        </span>
                    </p>

                    <!-- Assessment Total Score -->
                    <p>Assessment Total Score: 
                        <span class="px-4 py-1.5 rounded-full text-sm font-semibold shadow-sm bg-blue-100 text-blue-800 ring-1 ring-blue-300 text-lg">
                            {{ $summary->total_score ?? $testTotalScore }}
                        </span>
                    </p>

                    <!-- Capability Result -->
                    <p>Capability Result: 
                        <span class="px-4 py-1.5 rounded-full text-sm font-semibold shadow-sm bg-purple-100 text-purple-800 ring-1 ring-purple-300 text-lg">
                            {{ $summary->capability_result ?? 'N/A' }}
                        </span>
                    </p>
                </div>
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
<div class="mt-8 flex justify-end gap-3" id="actionButtons">


    {{-- Show Review Document button ONLY if Passed Screening --}}
    @if($summary->applicant->applicant_status === 'Passed Screening')
        <a href="{{ route('review.document', $summary->applicant->applicant_id) }}" 
           class="inline-block px-6 py-2.5 bg-gradient-to-r from-blue-500 to-blue-700 text-white font-semibold rounded-lg shadow hover:from-blue-600 hover:to-blue-800 transition ease-in-out duration-200">
            📑 Review Documents
        </a>
    @endif

    {{-- Show Passed/Failed buttons only if not yet marked --}}
    @if($summary->applicant->applicant_status !== 'Passed Screening' && $summary->applicant->applicant_status !== 'Failed Screening')
        <button type="button" id="passedBtn"
            data-id="{{ $summary->applicant->applicant_id }}"
            class="inline-block px-6 py-2.5 bg-gradient-to-r from-green-500 to-green-700 text-white font-semibold rounded-lg shadow hover:from-green-600 hover:to-green-800 transition ease-in-out duration-200">
            ✅ Passed Screening
        </button>
        
        <button type="button" id="failedBtn"
            data-id="{{ $summary->applicant->applicant_id }}"
            class="inline-block px-6 py-2.5 bg-gradient-to-r from-red-500 to-red-700 text-white font-semibold rounded-lg shadow hover:from-red-600 hover:to-red-800 transition ease-in-out duration-200">
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
            // Reload same page after success
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
            // Reload same page after success
            window.location.reload();
        }
    })
    .catch(err => console.error(err));
});
@endif
</script>

</x-guest-layout>
