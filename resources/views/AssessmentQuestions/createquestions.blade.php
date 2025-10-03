<x-guest-layout>
    <div class="max-w-3xl mx-auto p-8 bg-white shadow rounded-xl mb-20">
        <!-- Header -->
        <div class="flex items-center gap-2 mb-8">
            <i data-lucide="file-question" class="w-6 h-6 text-green-600"></i>
            <h1 class="text-2xl font-bold text-gray-800">
                Add Questions 
                <span class="text-sm text-gray-500">(10 per Level – Auto Assigned)</span>
            </h1>
        </div>

        <form action="{{ route('Questions.store') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
            @csrf

            <!-- Position Dropdown -->
            <div>
                <label class="flex items-center gap-2 font-semibold text-gray-700">
                    <i data-lucide="briefcase" class="w-5 h-5 text-gray-500"></i>
                    Position
                </label>
                <select name="position" class="w-full border rounded-lg p-2 mt-1 focus:ring-2 focus:ring-green-400" required>
                    <option value="">-- Select Position --</option>
                    <option value="Helper">Helper</option>
                    <option value="Assistant Technician">Assistant Technician</option>
                    <option value="Technician">Technician</option>
                    <option value="Human Resource Manager">Human Resource Manager</option>
                    <option value="Administrative Manager">Administrative Manager</option>
                    <option value="Finance Manager">Finance Manager</option>
                </select>
            </div>

            <!-- Assessment Dropdown -->
            <div>
                <label class="flex items-center gap-2 font-semibold text-gray-700">
                    <i data-lucide="clipboard-list" class="w-5 h-5 text-gray-500"></i>
                    Select Assessment
                </label>
                <select name="assessment_id" class="w-full border rounded-lg p-2 mt-1 focus:ring-2 focus:ring-green-400" required>
                    <option value="">-- Select Assessment --</option>
                    @foreach($assessments as $assessment)
                        <option value="{{ $assessment->assessment_id }}">
                            {{ $assessment->position_name }} - {{ $assessment->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Upload -->
            <div>
                <label class="flex items-center gap-2 font-semibold text-gray-700">
                    <i data-lucide="upload" class="w-5 h-5 text-gray-500"></i>
                    Upload Questionnaire
                </label>
                <div class="mt-2 flex items-center gap-3 p-3 border-2 border-dashed rounded-lg hover:border-green-500 cursor-pointer">
                    <i data-lucide="file-up" class="w-6 h-6 text-green-600"></i>
                    <input type="file" id="questionFile" class="flex-1 text-sm" accept=".jpg,.jpeg,.png,.doc,.docx">
                </div>
                <small class="text-gray-500">Supported: JPG, PNG, DOC, DOCX</small>
            </div>

            <!-- Auto-Filled Questions -->
            <div id="questionsContainer" class="space-y-6 mt-6"></div>

            <!-- Buttons -->
            <div class="flex justify-between items-center">
                <a href="{{ url()->previous() }}" 
                   class="flex items-center gap-2 px-5 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg shadow-md transition">
                    ← Back
                </a>
                <button type="submit" 
                        class="flex items-center gap-2 px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow-md transition">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Save Questions
                </button>
            </div>
        </form>
    </div>

    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script>
        lucide.createIcons();
    </script>

    {{-- Libraries --}}
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.4.2/mammoth.browser.min.js"></script>

    <script>
        const fileInput = document.getElementById('questionFile');
        const container = document.getElementById('questionsContainer');

        function processText(rawText) {
            const lines = rawText.split(/\r?\n/).map(l => l.trim()).filter(l => l !== "");
            const questions = [];
            let current = {};

            lines.forEach(line => {
                if (/^\d+\./.test(line)) {
                    if (current.question) questions.push(current);
                    current = { question: line.replace(/^\d+\.\s*/, ""), options: {}, correct: "" };
                } else if (/^A\./.test(line)) {
                    current.options.A = line.replace(/^A\.\s*/, "");
                } else if (/^B\./.test(line)) {
                    current.options.B = line.replace(/^B\.\s*/, "");
                } else if (/^C\./.test(line)) {
                    current.options.C = line.replace(/^C\.\s*/, "");
                } else if (/^D\./.test(line)) {
                    current.options.D = line.replace(/^D\.\s*/, "");
                } else if (/Correct Answer:/i.test(line)) {
                    const match = line.match(/Correct Answer:\s*([A-D])/i);
                    current.correct = match ? match[1].toUpperCase() : "";
                }
            });

            if (current.question) questions.push(current);
            renderQuestions(questions);
        }

        function renderQuestions(questions) {
            container.innerHTML = "";
            questions.forEach((q, i) => {
                const block = document.createElement("div");
                block.classList.add("p-4", "border", "rounded-lg", "bg-gray-50", "shadow-sm");

                block.innerHTML = `
                    <label class="flex items-center gap-2 mb-2 font-semibold text-gray-700">
                        <i data-lucide="help-circle" class="w-5 h-5 text-green-600"></i>
                        Q${i+1}
                    </label>
                    <textarea class="border rounded-lg p-2 w-full focus:ring-2 focus:ring-green-400" name="questions[${i}][question]">${q.question}</textarea>

                    ${['A','B','C','D'].map(opt => `
                        <label class="block mt-2 text-gray-700">Option ${opt}
                            <input type="text" class="border rounded-lg p-1 w-full mt-1 focus:ring-2 focus:ring-green-400" 
                                   name="questions[${i}][option_${opt.toLowerCase()}]" value="${q.options[opt] || ""}">
                        </label>
                    `).join('')}

                    <label class="block mt-3 font-semibold text-gray-700">Correct Answer
                        <input type="text" class="border rounded-lg p-1 w-full mt-1 bg-green-100 focus:ring-2 focus:ring-green-400" 
                               name="questions[${i}][correct_answer]" value="${q.correct}">
                    </label>
                `;

                container.appendChild(block);
                lucide.createIcons(); // refresh icons for new blocks
            });
        }

        fileInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;
            const ext = file.name.split('.').pop().toLowerCase();

            if (['doc', 'docx'].includes(ext)) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    mammoth.extractRawText({ arrayBuffer: event.target.result })
                        .then(result => processText(result.value))
                        .catch(err => {
                            console.error("Mammoth error:", err);
                            alert("Could not read Word file.");
                        });
                };
                reader.readAsArrayBuffer(file);
            } else if (['jpg','jpeg','png'].includes(ext)) {
                const reader = new FileReader();
                reader.onload = function () {
                    Tesseract.recognize(reader.result, 'eng')
                        .then(({ data: { text } }) => processText(text));
                };
                reader.readAsDataURL(file);
            } else {
                alert("Unsupported file type. Use image (JPG/PNG) or DOCX/DOC.");
            }
        });
    </script>
</x-guest-layout>
