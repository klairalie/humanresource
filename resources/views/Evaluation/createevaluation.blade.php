<x-guest-layout>
    <div class="p-6">
        <h1 class="text-xl font-bold mb-4">Add Evaluation Questions</h1>

        <form action="{{ route('Evaluations.store') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
            @csrf

            <!-- Position Dropdown -->
            <label class="block">
                <span>Position</span>
                <select name="position" class="w-full border rounded p-2" required>
                    <option value="">-- Select Position --</option>
                    <option value="Helper">Helper</option>
                    <option value="Assistant Technician">Assistant Technician</option>
                    <option value="Technician">Technician</option>
                    <option value="Human Resource Manager">Human Resource Manager</option>
                    <option value="Administrative Manager">Administrative Manager</option>
                    <option value="Finance Manager">Finance Manager</option>
                </select>
            </label>

            <!-- Evaluation Dropdown -->
            <label class="block mt-4">
                <span>Select Evaluation</span>
                <select name="evaluation_id" class="w-full border rounded p-2" required>
                    <option value="">-- Select Evaluation --</option>
                    @foreach($evaluations as $evaluation)
                        <option value="{{ $evaluation->evaluation_id }}">
                            {{ $evaluation->position_name }} - {{ $evaluation->title }}
                        </option>
                    @endforeach
                </select>
            </label>

            <!-- Upload -->
            <label class="block mt-4">
                <span>Upload Evaluation Questionnaire (Image / DOCX)</span>
                <input type="file" id="evaluationFile" class="w-full border rounded p-2" accept=".jpg,.jpeg,.png,.doc,.docx">
                <small class="text-gray-600">Supported: JPG, PNG, DOC, DOCX</small>
            </label>

            <!-- Auto-Filled Questions Container -->
            <div id="evaluationContainer" class="space-y-6 mt-4"></div>

            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded mb-15">Save Evaluation</button>
        </form>
    </div>

    {{-- Libraries --}}
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.4.2/mammoth.browser.min.js"></script>

    <script>
        const fileInput = document.getElementById('evaluationFile');
        const container = document.getElementById('evaluationContainer');

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
                block.classList.add("p-4", "border", "rounded", "bg-gray-50", "mb-4");

                block.innerHTML = `
                    <label class="block mb-2 font-semibold">Q${i+1}</label>
                    <textarea class="border rounded p-2 w-full" name="questions[${i}][question]">${q.question}</textarea>

                    <label class="block mt-2">Option A
                        <input type="text" class="border rounded p-1 w-full" name="questions[${i}][option_a]" value="${q.options.A || ""}">
                    </label>
                    <label class="block">Option B
                        <input type="text" class="border rounded p-1 w-full" name="questions[${i}][option_b]" value="${q.options.B || ""}">
                    </label>
                    <label class="block">Option C
                        <input type="text" class="border rounded p-1 w-full" name="questions[${i}][option_c]" value="${q.options.C || ""}">
                    </label>
                    <label class="block">Option D
                        <input type="text" class="border rounded p-1 w-full" name="questions[${i}][option_d]" value="${q.options.D || ""}">
                    </label>

                    <label class="block mt-2">Correct Answer
                        <input type="text" class="border rounded p-1 w-full bg-green-100" name="questions[${i}][correct_answer]" value="${q.correct}">
                    </label>
                `;

                container.appendChild(block);
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
