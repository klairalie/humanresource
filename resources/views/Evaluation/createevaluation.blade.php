{{-- CREATE EVALUATION QUESTIONS --}}

<x-guest-layout>
    <div class="max-w-3xl mx-auto p-8 bg-white shadow rounded-xl mb-20">
        <!-- Header -->
        <div class="flex items-center gap-2 mb-8">
            <i data-lucide="file-question" class="w-6 h-6 text-green-600"></i>
            <h1 class="text-2xl font-bold text-black">
                Add Evaluation Questions
                <span class="text-sm text-black">
                    (Work Performance & Skills / Teamwork & Collaboration / Professional Behavior / Safety & Responsibility — 10 each)
                </span>
            </h1>
        </div>
        
        <form action="{{ route('evaluation.store') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
            @csrf

            <!-- Position -->
            <div>
                <label class="flex items-center gap-2 font-semibold text-black">
                    <i data-lucide="briefcase" class="w-5 h-5 text-black"></i>
                    Position
                </label>
                <select name="position"
                        class="w-full border rounded-lg p-2 mt-1 focus:ring-2 focus:ring-green-400 text-black"
                        required>
                    <option value="">-- Select Position --</option>
                    <option value="Helper">Helper</option>
                    <option value="Assistant Technician">Assistant Technician</option>
                    <option value="Technician">Technician</option>
                    <option value="Human Resource Manager">Human Resource Manager</option>
                    <option value="Administrative Manager">Administrative Manager</option>
                    <option value="Finance Manager">Finance Manager</option>
                </select>
            </div>

            <!-- Assessment -->
            <div>
                <label class="flex items-center gap-2 font-semibold text-black">
                    <i data-lucide="clipboard-list" class="w-5 h-5 text-black"></i>
                    Select Employee Evaluation
                </label>
                <select name="assessment_id"
                        class="w-full border rounded-lg p-2 mt-1 focus:ring-2 focus:ring-green-400 text-black"
                        required>
                    <option value="">-- Select Employee Evaluation --</option>
                    @foreach($assessments as $assessment)
                        @if($assessment->type === 'evaluation')
                            <option value="{{ $assessment->assessment_id }}">
                                {{ $assessment->position_name }} - {{ $assessment->title }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <!-- Upload DOC/DOCX -->
            <div>
                <label class="flex items-center gap-2 font-semibold text-black">
                    <i data-lucide="upload" class="w-5 h-5 text-black"></i>
                    Upload Evaluation File (DOC / DOCX)
                </label>
                <div class="mt-2 flex items-center gap-3 p-3 border-2 border-dashed rounded-lg hover:border-green-500 cursor-pointer">
                    <i data-lucide="file-up" class="w-6 h-6 text-green-600"></i>
                    <input type="file" id="questionFile" name="questionFile"
                           class="flex-1 text-sm text-black" accept=".doc,.docx" required>
                </div>
                <small class="text-black">
                    Supported: DOC, DOCX — file must contain tables under:
                    Work Performance & Skills, Teamwork & Collaboration, Professional Behavior, Safety & Responsibility.
                    Each will be normalized to 10 items.
                </small>
            </div>

            <!-- Auto-Filled Questions -->
            <div id="questionsContainer" class="space-y-6 mt-6"></div>

            <!-- Buttons -->
            <div class="flex justify-between items-center">
                <a href="{{ route('evaluation.view') }}"
                   class="flex items-center gap-2 px-5 py-2 bg-gray-300 hover:bg-gray-400 text-black rounded-lg shadow-md transition">
                    ← Back
                </a>
                <button type="submit"
                        class="flex items-center gap-2 px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow-md transition">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Save Evaluation
                </button>
            </div>
        </form>
    </div>

    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script>lucide.createIcons();</script>

    {{-- Mammoth for DOC/DOCX --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.4.2/mammoth.browser.min.js"></script>

    {{-- Script to handle DOCX upload & extract --}}
    <script>
        const categoryOrder = [
            'Work Performance & Skills',
            'Teamwork & Collaboration',
            'Professional Behavior',
            'Safety & Responsibility'
        ];

        document.getElementById('questionFile').addEventListener('change', handleFileUpload);

        function handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(event) {
                mammoth.extractRawText({arrayBuffer: event.target.result})
                    .then(displayQuestions)
                    .catch(err => console.error("Mammoth error:", err));
            };
            reader.readAsArrayBuffer(file);
        }

        function displayQuestions(result) {
            const text = result.value;
            const lines = text.split("\n").map(l => l.trim()).filter(l => l !== "");
            const questionsContainer = document.getElementById("questionsContainer");
            questionsContainer.innerHTML = "";

            let currentCategory = null;
            let counters = {};
            let counter = 0; // global counter for correct question indexing

            lines.forEach(line => {
                if (categoryOrder.includes(line)) {
                    currentCategory = line;
                    counters[currentCategory] = 0;

                    const categoryHeader = document.createElement("h2");
                    categoryHeader.textContent = currentCategory;
                    categoryHeader.className = "text-lg font-bold text-green-700 mt-6";
                    questionsContainer.appendChild(categoryHeader);
                } else if (currentCategory && counters[currentCategory] < 10) {
                    counters[currentCategory]++;

                    const wrapper = document.createElement("div");
                    wrapper.className = "flex items-start gap-2 mt-2";

                    wrapper.innerHTML = `
                        <span class="font-semibold text-black">${counters[currentCategory]}.</span>
                        <input type="text" name="questions[${counter}][question]"
                               value="${line}" class="flex-1 border p-2 rounded text-black" required>
                        <input type="hidden" name="questions[${counter}][category]" value="${currentCategory}">
                    `;

                    questionsContainer.appendChild(wrapper);
                    counter++;
                }
            });
        }
    </script>
</x-guest-layout>
