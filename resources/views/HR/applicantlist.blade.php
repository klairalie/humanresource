<x-guest-layout>
    <div class="min-h-screen p-6 mt-10 text-black" x-data="applicantModal()" x-cloak>
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <h1 class="text-2xl font-bold mb-4 md:mb-0">List of Applicants</h1>

            <!-- Search Bar -->
            <form method="GET" action="" class="flex items-center space-x-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search applicants..."
                    class="px-4 py-2 border rounded-lg focus:ring focus:ring-blue-300 text-black">
                <button type="submit" class="px-4 py-2 bg-blue-500 text-black rounded-lg hover:bg-blue-600">
                    Search
                </button>
            </form>
        </div>

        <!-- Applicants Table -->
        <div class="bg-white shadow p-4 overflow-x-auto">
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-300 text-center text-black">
                        <th class="px-4 py-2">#</th>
                        <th class="px-4 py-2">Full Name</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Position</th>
                        <th class="px-4 py-2 w-56">Status</th>
                        <th class="px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applicants as $index => $applicant)
                        <tr class="border-b hover:bg-gray-50 text-center">
                            <td class="px-4 py-2">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2">{{ $applicant->first_name }}, {{ $applicant->last_name }}</td>
                            <td class="px-4 py-2">{{ $applicant->email }}</td>
                            <td class="px-4 py-2">{{ $applicant->position ?? 'N/A' }}</td>

                            <!-- Status Column -->
                            @php
                                $interview = \DB::table('interviews')
                                    ->where('applicant_id', $applicant->applicant_id)
                                    ->latest('created_at')
                                    ->first();

                                $statusClasses = [
                                    'Pending'             => 'bg-gray-200 text-black',
                                    'On Screening'        => 'bg-blue-100 text-black',
                                    'Passed Screening'    => 'bg-yellow-100 text-black',
                                    'Reviewed'            => 'bg-indigo-100 text-black',
                                    'Scheduled Interview' => 'bg-purple-100 text-black',
                                    'Failed Screening'    => 'bg-red-100 text-black',
                                    'Done'                => 'bg-green-600 text-black',
                                    'Unattended'          => 'bg-gray-300 text-black',
                                    'Hired'               => 'bg-green-700 text-black',
                                    'Rejected'            => 'bg-red-700 text-black',
                                ];

                                $status = $applicant->applicant_status ?? 'Pending';

                                $hasAssessment = \DB::table('assessment_results')
                                    ->where('applicant_id', $applicant->applicant_id)
                                    ->exists();

                                if ($status === 'On Screening' && $hasAssessment) {
                                    $status = 'Done Taking Assessment';
                                    $classApplicant = 'bg-green-100 text-black';
                                } else {
                                    $classApplicant = $statusClasses[$status] ?? 'bg-gray-200 text-black';
                                }

                                $interviewStatus = ($status !== 'Hired' && $status !== 'Rejected') ? ($interview->status ?? null) : null;
                                $classInterview = $interviewStatus ? ($statusClasses[$interviewStatus] ?? 'bg-gray-200 text-black') : null;
                            @endphp

                            <td class="px-4 py-2 w-56">
                                <span class="px-2 py-1 rounded-full text-sm block mb-1 {{ $classApplicant }}">
                                    {{ $status }}
                                </span>
                                @if ($interviewStatus)
                                    <span class="px-2 py-1 rounded-full text-sm block {{ $classInterview }}">
                                        {{ $interviewStatus }}
                                    </span>
                                @endif
                            </td>

                            <!-- Actions Column -->
                            <td class="px-4 py-2">
                                <div class="flex flex-wrap justify-center gap-2">
                                    @php
                                        $hasAssessment = \DB::table('assessment_results')
                                            ->where('applicant_id', $applicant->applicant_id)
                                            ->exists();
                                    @endphp

                                    @if ($status === 'Hired')
                                        <span class="italic">No actions</span>
                                    @elseif ($status === 'Rejected' || $interviewStatus === 'Unattended' || $applicant->applicant_status === 'Failed Screening')
                                        <form action="{{ route('applicants.delete', $applicant->applicant_id) }}" method="POST" onsubmit="return confirm('Delete this applicant?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 bg-red-600 text-black rounded hover:bg-red-700">
                                                Delete
                                            </button>
                                        </form>
                                    @elseif ($interviewStatus === 'Done')
                                        <form action="{{ route('applicants.finalDecision', ['applicant' => $applicant->applicant_id, 'status' => 'Hired']) }}" method="POST" onsubmit="return confirm('Mark applicant as Hired?')">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="px-3 py-1 bg-green-600 text-black rounded hover:bg-green-700">
                                                Hired
                                            </button>
                                        </form>
                                        <form action="{{ route('applicants.finalDecision', ['applicant' => $applicant->applicant_id, 'status' => 'Rejected']) }}" method="POST" onsubmit="return confirm('Reject this applicant?')">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="px-3 py-1 bg-red-600 text-black rounded hover:bg-red-700">
                                                Rejected
                                            </button>
                                        </form>
                                    @else
                                        @if ($applicant->applicant_status === 'On Screening' && $hasAssessment)
                                            <form action="{{ route('applicants.summarize', $applicant->applicant_id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-teal-600 text-black rounded hover:bg-teal-700">
                                                    Summarize
                                                </button>
                                            </form>
                                        @elseif ($applicant->applicant_status === 'Passed Screening')
                                            <a href="{{ route('review.document', $applicant->applicant_id) }}" class="px-3 py-1 bg-blue-600 text-black rounded hover:bg-blue-700">
                                                Review Document
                                            </a>
                                        @elseif ($applicant->applicant_status === 'Reviewed')
                                            <a href="{{ route('review.document', $applicant->applicant_id) }}" class="px-3 py-1 bg-blue-600 text-black rounded hover:bg-blue-700">
                                                View Details
                                            </a>
                                        @elseif ($applicant->applicant_status === 'Scheduled Interview')
                                            <form action="{{ route('interviews.updateStatus', ['applicant' => $applicant->applicant_id, 'status' => 'Done']) }}" method="POST" onsubmit="return confirm('Mark this interview as Done?')">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="px-3 py-1 bg-green-600 text-black rounded hover:bg-green-700">
                                                    Done
                                                </button>
                                            </form>
                                            <form action="{{ route('interviews.updateStatus', ['applicant' => $applicant->applicant_id, 'status' => 'Unattended']) }}" method="POST" onsubmit="return confirm('Mark this interview as Unattended? Email will be sent.')">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="px-3 py-1 bg-red-600 text-black rounded hover:bg-red-700">
                                                    Unattended
                                                </button>
                                            </form>
                                        @elseif ($applicant->applicant_status === 'Pending')
                                            <button @click="openModal({ 
                                                id: '{{ $applicant->applicant_id }}',
                                                first_name: '{{ $applicant->first_name }}',
                                                last_name: '{{ $applicant->last_name }}',
                                                contact: '{{ $applicant->contact_number }}',
                                                email: '{{ $applicant->email }}',
                                                address: '{{ $applicant->address }}',
                                                date_of_birth: '{{ $applicant->date_of_birth }}',
                                                emergency_contact: '{{ $applicant->emergency_contact }}',
                                                position: '{{ $applicant->position }}',
                                                career_objective: `{{ $applicant->career_objective }}`,
                                                work_experience: `{{ $applicant->work_experience }}`,
                                                education: `{{ $applicant->education }}`,
                                                skills: `{{ $applicant->skills }}`,
                                                achievements: `{{ $applicant->achievements }}`,
                                                references: `{{ $applicant->references }}`,
                                                good_moral_file: '{{ $applicant->good_moral_file }}',
                                                coe_file: '{{ $applicant->coe_file }}',
                                                resume_file: '{{ $applicant->resume_file }}',
                                                status: '{{ $applicant->applicant_status }}'
                                            })"
                                            class="px-3 py-1 bg-gray-500 text-black rounded-lg hover:bg-gray-600">
                                                View Application Form
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">No applicants found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Modal Viewer -->
        <div x-show="show"
             @keydown.escape.window="closeModal()"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-start sm:items-center justify-center z-50 p-4"
             x-cloak>
            <div class="bg-white w-full max-w-3xl p-6 rounded-lg shadow-lg overflow-y-auto max-h-[100vh] relative text-black">

                <button @click="closeModal" class="absolute top-3 right-3 hover:text-gray-700">✕</button>

                <h2 class="text-xl font-bold mb-4">Application Form</h2>
                <div class="space-y-3 text-left">
                    <p class="mb-2"><strong>Full Name:</strong> <span x-text="applicant.first_name + ' ' + applicant.last_name"></span></p>
                    <p class="mb-2"><strong>Email:</strong> <span x-text="applicant.email"></span></p>
                    <p class="mb-2"><strong>Address:</strong> <span x-text="applicant.address"></span></p>
                    <p class="mb-2"><strong>Date of Birth:</strong> <span x-text="applicant.date_of_birth"></span></p>
                    <p class="mb-2"><strong>Emergency Contact:</strong> <span x-text="applicant.emergency_contact"></span></p>
                    <p class="mb-2"><strong>Position:</strong> <span x-text="applicant.position"></span></p>

                    <hr class="my-2">

                    <p class="mb-2"><strong>Career Objective:</strong></p>
                    <p class="whitespace-pre-line mb-2" x-text="applicant.career_objective"></p>

                    <p class="mb-2"><strong>Work Experience:</strong></p>
                    <p class="whitespace-pre-line mb-2" x-text="applicant.work_experience"></p>

                    <p class="mb-2"><strong>Education:</strong></p>
                    <p class="whitespace-pre-line mb-2" x-text="applicant.education"></p>

                    <p class="mb-2"><strong>Skills:</strong></p>
                    <p class="whitespace-pre-line mb-2" x-text="applicant.skills"></p>

                    <p class="mb-2"><strong>Achievements:</strong></p>
                    <p class="whitespace-pre-line mb-2" x-text="applicant.achievements"></p>

                    <p class="mb-2"><strong>References:</strong></p>
                    <p class="whitespace-pre-line mb-2" x-text="applicant.references"></p>

                    <hr class="my-2">

                    <p class="mb-2"><strong>Attachments:</strong></p>
                    <ul class="list-disc pl-6">
                        <li class="mb-1"><a :href="'/storage/' + applicant.resume_file" target="_blank" class="text-black hover:underline">Resume</a></li>
                        <li class="mb-1"><a :href="'/storage/' + applicant.good_moral_file" target="_blank" class="text-black hover:underline">Good Moral</a></li>
                        <li class="mb-1"><a :href="'/storage/' + applicant.coe_file" target="_blank" class="text-black hover:underline">Certificate of Employment</a></li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex flex-col md:flex-row md:items-center md:justify-end space-y-2 md:space-y-0 md:space-x-3">
                    <form :action="`/assessment/${applicant.id}/${selectedAssessment}`" method="POST" class="flex items-center space-x-2">
                        @csrf
                        <label>
                            <span>Select Assessment</span>
                            <select x-model="selectedAssessment" class="border rounded p-1 ml-2 w-sm text-black" required>
                                <option value="">-- Select Assessment --</option>
                                @foreach ($assessments as $assessment)
                                    <option value="{{ $assessment->assessment_id }}">{{ $assessment->position_name }} - {{ $assessment->title }}</option>
                                @endforeach
                            </select>
                        </label>
                        <button type="submit" class="px-3 py-1 bg-green-600 text-black rounded hover:bg-green-700 ml-2 mt-5">Send </button>
                    </form>

                       <form :action="`/applicants/${applicant.id}/finalDecision/Rejected`" method="POST" onsubmit="return confirm('Reject this applicant?')">
    @csrf
    @method('PUT')
    <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 mt-5">
        Rejected
    </button>
</form>


                    <button @click="closeModal" class="px-3 py-1 bg-gray-600 text-black rounded hover:bg-gray-700 mt-5">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js -->
    <script>
        // assessments list to be used for auto-detect
        window._assessments = {!! $assessments
            ->map(function($a){
                return [
                    'id' => $a->assessment_id,
                    'position' => $a->position_name ?? '',
                    'title' => $a->title ?? ''
                ];
            })->toJson() !!};

        function applicantModal() {
            return {
                show: false,
                applicant: {},
                selectedAssessment: '',

                openModal(data) {
                    this.applicant = data;
                    this.show = true;
                    this.selectedAssessment = '';

                    try {
                        const list = window._assessments || [];
                        const pos = (data.position || '').toString().toLowerCase().trim();

                        let found = null;

                        if (pos) {
                            // 1) exact match
                            found = list.find(a => (a.position || '').toLowerCase().trim() === pos);

                            // 2) includes
                            if (!found) {
                                found = list.find(a =>
                                    (a.position || '').toLowerCase().includes(pos) ||
                                    (a.title || '').toLowerCase().includes(pos)
                                );
                            }

                            // 3) word-by-word
                            if (!found) {
                                const words = pos.split(/\s+/).filter(Boolean);
                                found = list.find(a =>
                                    words.some(w =>
                                        (a.position || '').toLowerCase().includes(w) ||
                                        (a.title || '').toLowerCase().includes(w)
                                    )
                                );
                            }
                        }

                        // 4) fallback
                        if (!found && list.length) {
                            found = list.find(a => (a.position || '').length) || list[0];
                        }

                        if (found) {
                            this.selectedAssessment = found.id;
                        }
                    } catch (e) {
                        console.error('Auto-detect assessment failed:', e);
                    }

                    document.documentElement.classList.add('overflow-hidden');
                },

                closeModal() {
                    this.show = false;
                    this.applicant = {};
                    this.selectedAssessment = '';
                    document.documentElement.classList.remove('overflow-hidden');
                }
            }
        }
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>
</x-guest-layout>
