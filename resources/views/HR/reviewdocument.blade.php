<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Applicant Details</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        [x-cloak] { display: none !important; }
        header, nav, .navbar {
            display: none !important;
        }
    </style>
</head>
<body>
    
<div class="min-h-screen p-6 flex flex-col items-center">

    <div class="bg-white shadow-2xl rounded-2xl p-8 w-full max-w-5xl border border-gray-200 mb-6 print:shadow-none print:rounded-none print:border-0">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6 print:flex-col print:items-start print:justify-start">
            <h1 class="text-3xl font-bold text-black border-b-2 border-gray-300 pb-2">
                Applicant: {{ $applicant->first_name }} {{ $applicant->last_name }}
            </h1>
        </div>

        <!-- Applicant Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 text-black">
            <div>
                <p class="font-medium text-sm">Position</p>
                <p class="text-lg font-semibold">{{ $applicant->position }}</p>
            </div>
            <div>
                <p class="font-medium text-sm">Career Objective</p>
                <p>{{ $applicant->career_objective }}</p>
            </div>
            <div>
                <p class="font-medium text-sm">Skills</p>
                <p>{{ $applicant->skills }}</p>
            </div>
            <div>
                <p class="font-medium text-sm">Achievements</p>
                <p>{{ $applicant->achievements }}</p>
            </div>
            <div>
                <p class="font-medium text-sm">Education Attainment</p>
                <p>{{ $applicant->education }}</p>
            </div>
            <div>
                <p class="font-medium text-sm">Work Experience</p>
                <p>{{ $applicant->work_experience }}</p>
            </div>
            <div>
                <p class="font-bold text-lg">Person Reference</p>
                <p>{{ $applicant->references }}</p>
            </div>
        </div>

      <!-- Uploaded Files with Modal -->
<div class="bg-gray-50 p-5 rounded-xl border border-gray-200 shadow-sm mb-6 print:hidden" 
     x-data="{ showModal: false, modalFile: '' }" 
     x-cloak>
    
    <ul class="space-y-3 text-gray-700">
        {{-- Example buttons if you want to enable again --}}
        {{-- 
        @if($applicant->coe_file)
            <li>
                <button 
                    @click="modalFile='{{ asset('storage/'.$applicant->coe_file) }}'; showModal = true"
                    class="text-blue-600 hover:underline font-medium">
                    📄 Certificate of Employment
                </button>
            </li>
        @else
            <li class="italic text-gray-500">No COE uploaded.</li>
        @endif

        @if($applicant->good_moral_file)
            <li>
                <button 
                    @click="modalFile='{{ asset('storage/'.$applicant->good_moral_file) }}'; showModal = true"
                    class="text-blue-600 hover:underline font-medium">
                    📄 Good Moral Certificate
                </button>
            </li>
        @else
            <li class="italic text-gray-500">No Good Moral uploaded.</li>
        @endif 
        --}}
    </ul>

    <!-- Modal -->
    <div
        x-show="showModal"
        x-transition
        x-cloak
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white rounded-xl shadow-lg w-11/12 md:w-3/4 lg:w-2/3 max-h-[90vh] overflow-auto">
            <div class="flex justify-between items-center p-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold">Document Preview</h3>
                <button @click="showModal = false" class="text-gray-500 hover:text-gray-800 font-bold">&times;</button>
            </div>
            <div class="p-4">
                <!-- Determine if PDF or Image --> 
                <template x-if="modalFile.endsWith('.pdf')" x-cloak>
                    <iframe :src="modalFile" class="w-full h-[70vh]" frameborder="0"></iframe>
                </template>
                <template x-if="!modalFile.endsWith('.pdf')" x-cloak>
                    <img :src="modalFile" class="w-full h-auto rounded-lg" alt="Document Preview">
                </template>
            </div>
        </div>
    </div>
</div>


        <!-- Summary Section -->
        @if($summary)
            <div class="bg-gray-50 p-5 rounded-xl border border-gray-200 shadow-sm mb-6 print:bg-white print:border-0 print:shadow-none text-black">
                <h2 class="text-lg font-semibold mb-3">Applicant Summary</h2>

                 <p class="text-sm font-semibold text-black print:mt-4 mb-3">
                <strong>Performance Rating:</strong> {{ $summary->performance_rating ?? 'N/A' }}
            </p>
                <p><strong>Matched Skills:</strong></p>
                <ul class="list-disc list-inside mb-2">
                    @forelse(json_decode($summary->matched_skills, true) ?? [] as $skill)
                        <li>{{ $skill }}</li>
                    @empty
                        <li class="italic text-gray-500">No matched skills</li>
                    @endforelse
                </ul>
                <p><strong>Matched Objectives:</strong></p>
                <ul class="list-disc list-inside">
                    @forelse(json_decode($summary->matched_career_objective, true) ?? [] as $obj)
                        <li>{{ $obj }}</li>
                    @empty
                        <li class="italic text-gray-500">No matched objectives</li>
                    @endforelse
                </ul>
            </div>
        @endif

        <!-- Buttons Section -->
<!-- Buttons + Schedule Modal in one Alpine scope -->
<div x-data="{ showScheduleModal: false }" class="mt-6 flex flex-col md:flex-row justify-center items-center gap-4 print:hidden">
    
    @if($applicant->applicant_status !== 'Reviewed' && $applicant->applicant_status !== 'Scheduled Interview')
        <!-- Mark as Reviewed -->
        <form action="{{ route('applicant.markReviewed', $applicant->applicant_id) }}" method="POST">
            @csrf
            <button type="submit" 
                class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg shadow hover:bg-green-700 transition">
                Mark as Reviewed
            </button>
        </form>
    @elseif($applicant->applicant_status === 'Reviewed')
        <!-- PASSED Button to open modal -->
        <button 
            @click="showScheduleModal = true"
            class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg shadow hover:bg-green-700 transition">
            PASSED
        </button>
    @elseif($applicant->applicant_status === 'Scheduled Interview')
        <button disabled 
            class="px-6 py-3 bg-gray-400 text-white font-semibold rounded-lg shadow cursor-not-allowed">
            Interview Scheduled
        </button>
    @endif

    <a href="{{ route('show.listapplicants') }}" 
        class="px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg shadow hover:bg-gray-700 transition">
        ← Back to Applicants
    </a>

    <button 
        onclick="window.print()"
        class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-blue-700 transition">
        Print / Save as PDF
    </button>

    <!-- Schedule Interview Modal -->
    <div 
        x-show="showScheduleModal"
        x-cloak
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white rounded-xl shadow-lg w-11/12 md:w-1/2 p-6">
            <h2 class="text-xl font-semibold mb-4">Schedule Interview</h2>

            <form action="{{ route('applicant.scheduleInterview', $applicant->applicant_id) }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium">Interview Date</label>
                    <input type="date" name="interview_date" required 
                        class="w-full border rounded-lg px-3 py-2 mt-1">
                </div>

                <div>
                    <label class="block text-sm font-medium">Interview Time</label>
                    <input type="time" name="interview_time" required 
                        class="w-full border rounded-lg px-3 py-2 mt-1">
                </div>

                <div>
                    <label class="block text-sm font-medium">Location</label>
                    <input type="text" name="location" required placeholder="e.g. HR Office, 3rd Floor"
                        class="w-full border rounded-lg px-3 py-2 mt-1">
                </div>

                <div>
                    <label class="block text-sm font-medium">HR Manager</label>
                    <input type="text" name="hr_manager" required placeholder="Enter HR Manager name"
                        class="w-full border rounded-lg px-3 py-2 mt-1">
                </div>

                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" 
                        @click="showScheduleModal = false"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Save Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



    </div>
</div>

</body>
</html>
