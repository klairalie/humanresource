<x-guest-layout>
    <div class="min-h-screen p-6 mt-10" 
        x-data="applicantModal()"
        x-cloak
    >
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <h1 class="text-2xl font-bold mb-4 md:mb-0 text-black">List of Applicants</h1>

            <!-- Search Bar -->
            <form method="GET" action="" class="flex items-center space-x-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search applicants..."
                    class="px-4 py-2 border rounded-lg focus:ring focus:ring-blue-300">
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Search
                </button>
            </form>
        </div>

        <!-- Applicants Table -->
        <div class="bg-white shadow p-4 overflow-x-auto">
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-300 text-center text-gray-600">
                        <th class="px-4 py-2">#</th>
                        <th class="px-4 py-2">Full Name</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Contact</th>
                        <th class="px-4 py-2">Position</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applicants as $index => $applicant)
                        <tr class="border-b hover:bg-gray-50 text-center">
                            <td class="px-4 py-2">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2">{{ $applicant->first_name }}, {{ $applicant->last_name }}</td>
                            <td class="px-4 py-2">{{ $applicant->email }}</td>
                            <td class="px-4 py-2">{{ $applicant->contact_number }}</td>
                            <td class="px-4 py-2">{{ $applicant->position ?? 'N/A' }}</td>
                            <td class="px-4 py-2">
                                <span
                                    class="px-2 py-1 rounded-full text-sm 
                                    {{ $applicant->applicant_status == 'Pending'
                                        ? 'bg-yellow-200 text-yellow-800'
                                        : ($applicant->applicant_status == 'On Screening'
                                            ? 'bg-blue-200 text-blue-800'
                                            : 'bg-green-200 text-green-800') }}">
                                    {{ $applicant->applicant_status ?? 'Pending' }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                @if ($applicant->applicant_status === 'Pending')
                                    <button 
                                        @click="openModal({ 
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
                                        class="px-3 py-1 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                                        View Application Form
                                    </button>
                                @endif

                                @if ($applicant->summary && $applicant->applicant_status === 'On Screening')
                                    <a href="{{ route('applicants.summary.show', $applicant->summary->applicant_summary_id) }}"
                                        class="px-3 py-1 bg-green-500 text-white rounded-lg hover:bg-green-600">
                                        Summarize
                                    </a>
                                @endif

                                @if ($applicant->applicant_status === 'Reviewed')
                                    <a href="{{ route('review.document', $applicant->applicant_id) }}"
                                        class="px-3 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                                        View Details
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-gray-500">No applicants found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $applicants->links() }}
        </div>

        <!-- Modal Viewer -->
        <div x-show="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-cloak>
            <div class="bg-white w-full max-w-3xl p-6 rounded-lg shadow-lg overflow-y-auto max-h-[90vh] relative">
                
                <button @click="closeModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">✕</button>

                <h2 class="text-xl font-bold mb-4">Application Form</h2>
                <div class="space-y-3 text-left">
                    <p><strong>Full Name:</strong> <span x-text="applicant.first_name + ' ' + applicant.last_name"></span></p>
                    <p><strong>Email:</strong> <span x-text="applicant.email"></span></p>
                    <p><strong>Contact:</strong> <span x-text="applicant.contact"></span></p>
                    <p><strong>Address:</strong> <span x-text="applicant.address"></span></p>
                    <p><strong>Date of Birth:</strong> <span x-text="applicant.date_of_birth"></span></p>
                    <p><strong>Emergency Contact:</strong> <span x-text="applicant.emergency_contact"></span></p>
                    <p><strong>Position:</strong> <span x-text="applicant.position"></span></p>

                    <hr class="my-2">

                    <p><strong>Career Objective:</strong></p>
                    <p class="whitespace-pre-line" x-text="applicant.career_objective"></p>

                    <p><strong>Work Experience:</strong></p>
                    <p class="whitespace-pre-line" x-text="applicant.work_experience"></p>

                    <p><strong>Education:</strong></p>
                    <p class="whitespace-pre-line" x-text="applicant.education"></p>

                    <p><strong>Skills:</strong></p>
                    <p class="whitespace-pre-line" x-text="applicant.skills"></p>

                    <p><strong>Achievements:</strong></p>
                    <p class="whitespace-pre-line" x-text="applicant.achievements"></p>

                    <p><strong>References:</strong></p>
                    <p class="whitespace-pre-line" x-text="applicant.references"></p>

                    <hr class="my-2">

                    <p><strong>Attachments:</strong></p>
                    <ul class="list-disc pl-6">
                        <li><a :href="'/storage/' + applicant.resume_file" target="_blank" class="text-blue-600 hover:underline">Resume</a></li>
                        <li><a :href="'/storage/' + applicant.good_moral_file" target="_blank" class="text-blue-600 hover:underline">Good Moral</a></li>
                        <li><a :href="'/storage/' + applicant.coe_file" target="_blank" class="text-blue-600 hover:underline">Certificate of Employment</a></li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex flex-col md:flex-row md:items-center md:justify-end space-y-2 md:space-y-0 md:space-x-3">
                    <form :action="`/assessment/${applicant.id}/${selectedAssessment}`" method="POST" class="flex items-center space-x-2">
                        @csrf
                        <label>
                            <span>Select Assessment</span>
                            <select x-model="selectedAssessment" class="border rounded p-1 ml-2" required>
                                <option value="">-- Select Assessment --</option>
                                @foreach($assessments as $assessment)
                                    <option value="{{ $assessment->assessment_id }}">
                                        {{ $assessment->position_name }} - {{ $assessment->title }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 ml-2">
                            Send Assessment
                        </button>
                    </form>

                    <form :action="`/applicants/${applicant.id}/reject`" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Reject
                        </button>
                    </form>

                    <button @click="closeModal" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js -->
    <script>
        function applicantModal() {
            return {
                show: false,
                applicant: {},
                selectedAssessment: '',
                openModal(data) {
                    this.applicant = data;
                    this.show = true;
                    this.selectedAssessment = '';
                },
                closeModal() {
                    this.show = false;
                    this.applicant = {};
                    this.selectedAssessment = '';
                }
            }
        }
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>
</x-guest-layout>
