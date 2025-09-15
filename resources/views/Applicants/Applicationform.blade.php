<x-nav-layout>

   
    {{-- Validation Errors (for non-JS fallback) --}}
    @if ($errors->any())
        <script>
            @foreach ($errors->all() as $error)
                alert("{{ $error }}");
            @endforeach
        </script>
    @endif

    {{-- Success Modal --}}
    @if (session('success'))
        <div id="successModal" class="modal-backdrop">
            <div class="modal-card">
                <h2 class="text-2xl font-bold text-green-700 mb-3">✅ {{ session('success') }}</h2>
                <p class="text-gray-700 mb-6">
                    Please always check your Gmail for the status of your application. Thank you!
                </p>
                <button onclick="document.getElementById('successModal').remove()" 
                    class="px-6 py-2 bg-amber-600 text-white rounded-lg font-semibold hover:bg-amber-700">
                    Close
                </button>
            </div>
        </div>
    @endif

    <!-- Applicant Registration Form -->
    <section class="relative pt-32 pb-20 bg-gradient-to-r from-gray-300 to-orange-200 backdrop-blur-md">

        <!-- Instructions -->
        <div class="instructions max-w-4xl mx-auto px-6 mb-10">
            <div class="relative bg-white/80 backdrop-blur-lg shadow-lg rounded-2xl p-8 sm:p-10 border border-gray-200">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-sky-500 to-emerald-400 rounded-t-2xl"></div>
                <h1 class="text-xl sm:text-2xl font-bold text-sky-900 mb-3">Upload Your Resume</h1>
                <p class="text-gray-700 text-sm sm:text-base leading-relaxed">
                    Please upload your <span class="font-semibold text-sky-700">resume file (.doc, .docx)</span> 
                    so our system can scan and process it correctly. Make sure your resume is updated before submitting. Thank You!
                </p>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-6 relative">
            <div class="form-card">

                <!-- Header -->
                <h1 class="form-header">Applicant Registration Form</h1>

                <form id="applicantForm" action="{{ route('applicants.store') }}" method="POST" enctype="multipart/form-data" class="space-y-10">
                    @csrf

                    <!-- Resume Upload -->
                    <div>
                        <label class="input-label text-black text-lg ml-2 mb-2">Upload Resume here</label>
                        <input type="file" id="resumeFile" name="resume_file" accept=".doc,.docx,.jpg,.jpeg,.png" class="validate-input file-input">
                        <p class="error-message text-red-600 hidden"></p>

                        <!-- Spinner -->
                        <div id="resumeSpinner" class="hidden mt-2 flex items-center text-amber-700">
                            <svg class="animate-spin h-5 w-5 mr-2 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span id="resumeSpinnerText">Scanning resume...</span>
                        </div>
                    </div>

                    <!-- Personal Info -->
                    <div>
                        <h1 class="section-title">Personal Information</h1>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="input-label">First Name</label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}" class="validate-input input-field" required>
                            </div>
                            <div>
                                <label class="input-label">Last Name</label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}" class="validate-input input-field" required>
                            </div>
                            <div>
                                <label class="input-label">Contact Number</label>
                                <input type="text" name="contact_number" value="{{ old('contact_number') }}" class="validate-input input-field" required>
                            </div>
                            <div>
                                <label class="input-label">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" class="validate-input input-field" required>
                            </div>
                            <div class="md:col-span-2">
                                <label class="input-label">Address</label>
                                <textarea name="address" rows="2" class="validate-input input-field" required>{{ old('address') }}</textarea>
                            </div>
                            <div>
                                <label class="input-label">Date of Birth</label>
                                <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="validate-input input-field">
                            </div>
                            <div>
                                <label class="input-label">Emergency Contact</label>
                                <input type="text" name="emergency_contact" value="{{ old('emergency_contact') }}" class="validate-input input-field">
                            </div>
                            <div class="md:col-span-2">
                                <label class="input-label">Position Applying For</label>
                                <select name="position" class="validate-input input-field" required>
                                    <option value="" disabled {{ old('position') ? '' : 'selected' }}>-- Select Position --</option>
                                    <option value="Helper" {{ old('position')=='Helper' ? 'selected' : '' }}>Helper</option>
                                    <option value="Assistant Technician" {{ old('position')=='Assistant Technician' ? 'selected' : '' }}>Assistant Technician</option>
                                    <option value="Technician" {{ old('position')=='Technician' ? 'selected' : '' }}>Technician</option>
                                    <option value="Human Resource Manager" {{ old('position')=='Human Resource Manager' ? 'selected' : '' }}>Human Resource Manager</option>
                                    <option value="Administrative Manager" {{ old('position')=='Administrative Manager' ? 'selected' : '' }}>Administrative Manager</option>
                                    <option value="Finance Manager" {{ old('position')=='Finance Manager' ? 'selected' : '' }}>Finance Manager</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Resume Details -->
                    <div>
                        <h2 class="section-title">Resume Details</h2>
                        <div class="space-y-4">
                            @foreach ([
                                'career_objective' => 'Career Objective',
                                'work_experience' => 'Work Experience',
                                'education' => 'Education',
                                'skills' => 'Skills',
                                'achievements' => 'Achievements / Projects',
                                'references' => 'References'
                            ] as $field => $label)
                                <div>
                                    <label class="input-label">{{ $label }}</label>
                                    <textarea name="{{ $field }}" rows="3" class="validate-input input-field text-justify p-3 leading-relaxed">{{ old($field) }}</textarea>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- File Uploads -->
                    <div>
                        <h2 class="section-title">Uploads</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="input-label">Good Moral Certificate</label>
                                <input type="file" name="good_moral_file" class="validate-input file-input">
                            </div>
                            <div>
                                <label class="input-label">Certificate of Employment (COE)</label>
                                <input type="file" name="coe_file" class="validate-input file-input">
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-end">
                        <button type="submit" class="submit-btn">Submit Application</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

<!-- Review Modal -->
<div id="reviewModal" 
     class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center">
  <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-8 text-center">
      
      <h2 class="text-xl font-semibold text-red-700 mb-4">Please Review</h2>
      
      <p class="text-black mb-6 leading-relaxed text-lg">
          Please review your information and edit it if there are any mismatches. 
          Thank you for your attention.
      </p>
      
      <button onclick="document.getElementById('reviewModal').classList.add('hidden')" 
          class="px-6 py-2 bg-orange-300 text-black rounded-md text-sm font-medium hover:bg-gray-200 transition">
          Okay
      </button>
  </div>
</div>



   <script>
function runValidation(input) {
    const field = input.name;
    const value = input.value;

    if (!value) return; // skip empty fields

    fetch("{{ route('validate.applicant') }}", { 
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ field, value })
    })
    .then(res => res.json())
    .then(data => {
        let errorEl = input.parentElement.querySelector(".error-message");
        if (!errorEl) {
            errorEl = document.createElement("p");
            errorEl.className = "error-message text-sm mt-1";
            input.parentElement.appendChild(errorEl);
        }

        if (data.valid) {
            errorEl.textContent = "✔ Looks good";
            errorEl.classList.add("text-green-600");
            errorEl.classList.remove("text-red-600");
        } else {
            errorEl.textContent = data.message;
            errorEl.classList.add("text-red-600");
            errorEl.classList.remove("text-green-600");
        }
    })
    .catch(err => console.error("Validation error:", err));
}

// Attach validation on blur & typing
document.querySelectorAll('.validate-input').forEach(input => {
    input.addEventListener('blur', () => runValidation(input));
    input.addEventListener('input', () => runValidation(input));
});

// Also listen for OCR autofill: trigger validation whenever a field changes value
const observer = new MutationObserver(() => {
    document.querySelectorAll('.validate-input').forEach(input => {
        if (input.value && !input.dataset.validated) {
            runValidation(input);
            input.dataset.validated = "true"; // prevent infinite loop
        }
    });
});

// Watch for OCR changes in the form
observer.observe(document.getElementById('applicantForm'), {
    childList: false,
    subtree: true,
    attributes: true,
    attributeFilter: ['value']
});

document.getElementById('applicantForm').addEventListener('submit', function (e) {
    e.preventDefault();
    if (confirm("Are you sure you want to submit it now?")) {
        if (confirm("Are you sure you are providing reliable information?")) {
            this.submit();
        } else {
            alert("❗ Please review your application carefully before submitting.");
        }
    } else {
        alert("✅ You may continue editing your application.");
    }
});
</script>




    {{-- OCR + Resume Auto-fill (patched) --}}
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.5.1/mammoth.browser.min.js"></script>

    <script type="module">
        const spinner = document.getElementById("resumeSpinner");
        const spinnerText = document.getElementById("resumeSpinnerText");

        function showSpinner(msg = "Scanning resume...") { spinnerText.textContent = msg; spinner.classList.remove("hidden"); }
        function hideSpinner() { spinner.classList.add("hidden"); }

        function setIfEmpty(name, value) {
            if (!value && value !== 0) return;
            const el = document.querySelector(`[name='${name}']`);
            if (!el) return;
            if (el.value && el.value.toString().trim() !== "") return;
            el.value = value.toString().trim();
        }

        function toISODate(dateStr) {
            const d = new Date(dateStr);
            if (!isNaN(d)) return d.toISOString().slice(0,10);
            const m = dateStr.match(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/);
            if (m) {
                const day = parseInt(m[1], 10), month = parseInt(m[2], 10) - 1, year = parseInt(m[3], 10);
                const d2 = new Date(year, month, day);
                if (!isNaN(d2)) return d2.toISOString().slice(0,10);
            }
            return null;
        }

        document.getElementById("resumeFile").addEventListener("change", async function () {
            const file = this.files[0];
            if (!file) return;

            const errorEl = this.parentElement.querySelector(".error-message");
            if (errorEl) { errorEl.textContent = ""; errorEl.classList.remove("hidden"); }

            showSpinner("Scanning resume...");

            try {
                let rawText = "";

                if (file.type.startsWith("image/")) {
                    const reader = new FileReader();
                    reader.onload = async (e) => {
                        const result = await Tesseract.recognize(e.target.result, 'eng');
                        rawText = result.data.text || "";
                        processExtraction(rawText);
                        hideSpinner();
                    };
                    reader.readAsDataURL(file);
                    return;
                }

                if (file.type === "application/pdf") {
                    const reader = new FileReader();
                    reader.onload = async (e) => {
                        const pdfjsLib = await import("https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.mjs");
                        const pdf = await pdfjsLib.getDocument({ data: e.target.result }).promise;
                        const pages = Array.from({ length: pdf.numPages }, (_, i) => i + 1);
                        const pageTexts = await Promise.all(pages.map(async pnum => {
                            const page = await pdf.getPage(pnum);
                            const content = await page.getTextContent();
                            return content.items.map(item => item.str).join(" ");
                        }));
                        rawText = pageTexts.join("\n\n");
                        processExtraction(rawText);
                        hideSpinner();
                    };
                    reader.readAsArrayBuffer(file);
                    return;
                }

                if (file.type === "application/vnd.openxmlformats-officedocument.wordprocessingml.document") {
                    const reader = new FileReader();
                    reader.onload = async (e) => {
                        const result = await mammoth.extractRawText({ arrayBuffer: e.target.result });
                        rawText = result.value || "";
                        processExtraction(rawText);
                        hideSpinner();
                    };
                    reader.readAsArrayBuffer(file);
                    return;
                }

                if (errorEl) errorEl.textContent = "❌ Unsupported file type.";
                hideSpinner();
            } catch (err) {
                console.error(err);
                if (errorEl) errorEl.textContent = "❌ Failed to scan resume.";
                hideSpinner();
            }
        });

        function processExtraction(rawText) {
    const extracted = parseResumeText(rawText || "");
    if (extracted.first_name) setIfEmpty("first_name", extracted.first_name);
    if (extracted.last_name) setIfEmpty("last_name", extracted.last_name);
    if (extracted.email) setIfEmpty("email", extracted.email.toLowerCase());
    if (extracted.contact_number) setIfEmpty("contact_number", extracted.contact_number);
    if (extracted.date_of_birth) setIfEmpty("date_of_birth", extracted.date_of_birth);
    if (extracted.address) setIfEmpty("address", extracted.address);
    if (extracted.emergency_contact) setIfEmpty("emergency_contact", extracted.emergency_contact);
    if (extracted.position) setIfEmpty("position", extracted.position);
    if (extracted.work_experience) setIfEmpty("work_experience", extracted.work_experience);
    if (extracted.education) setIfEmpty("education", extracted.education);
    if (extracted.skills) setIfEmpty("skills", extracted.skills);
    if (extracted.career_objective) setIfEmpty("career_objective", extracted.career_objective);
    if (extracted.achievements) setIfEmpty("achievements", extracted.achievements);
    if (extracted.references) setIfEmpty("references", extracted.references);

    // ✅ Show modal after auto-fill
    const modal = document.getElementById("reviewModal");
    if (modal) modal.classList.remove("hidden");
}

        function parseResumeText(rawText) {
            let text = rawText.replace(/\r/g, "\n").replace(/\t/g, " ").replace(/\u00A0/g, " ");
            text = text.replace(/[|·•·]/g, "\n");
            text = text.replace(/\n{2,}/g, "\n").trim();

            const result = {
                first_name: null,
                last_name: null,
                email: null,
                contact_number: null,
                emergency_contact: null,
                date_of_birth: null,
                address: null,
                position: null,
                career_objective: null,
                work_experience: null,
                education: null,
                skills: null,
                achievements: null,
                references: null
            };

            // --- PATCH: explicit labeled fields ---
            const nameMatch = text.match(/First Name:\s*([^\n]+?)\s+Last Name:\s*([^\n]+)/i);
            if (nameMatch) {
                result.first_name = nameMatch[1].trim();
                result.last_name = nameMatch[2].trim();
            }
            const addrMatch = text.match(/Address:\s*(.+)/i);
            if (addrMatch) result.address = addrMatch[1].trim();
            const emergMatch = text.match(/Emergency Contact:\s*(.+)/i);
            if (emergMatch) result.emergency_contact = emergMatch[1].trim();
            const posMatch = text.match(/Position Applied For\s*\n?(.+)/i);
            if (posMatch) result.position = posMatch[1].trim();
            // --- END PATCH ---

            // Email
            const emailRx = /\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}\b/i;
            const emailMatch = text.match(emailRx);
            if (emailMatch) result.email = emailMatch[0];

            // Phone (first candidate kept as main)
            const phoneCandidates = Array.from(text.matchAll(/(\+?\d[\d\-\s().]{7,}\d)/g)).map(m => m[0]);
            if (phoneCandidates.length) result.contact_number = phoneCandidates[0].trim();

            // DOB
            const dob1 = text.match(/\b(?:Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sep(?:t(?:ember)?)|Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?)\s+\d{1,2},\s+\d{4}\b/i);
            const dob2 = text.match(/\b\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4}\b/);
            const dobMatch = dob1 || dob2;
                       if (dobMatch) {
                const iso = toISODate(dobMatch[0]);
                if (iso) result.date_of_birth = iso;
            }

            // --- section parsing, fallback logic continues here ---
            const sectionDefs = [
                { key: "career_objective", regex: /(?:^|\n)\s*(objective|career objective|profile|summary)\b/i },
                { key: "skills", regex: /(?:^|\n)\s*(skills|technical skills|core competencies|competenc(?:y|ies))\b/i },
                { key: "work_experience", regex: /(?:^|\n)\s*(work experience|experience|employment history|professional experience)\b/i },
                { key: "education", regex: /(?:^|\n)\s*(education|academic background)\b/i },
                { key: "achievements", regex: /(?:^|\n)\s*(achievements|projects|accomplishments|certifications)\b/i },
                { key: "references", regex: /(?:^|\n)\s*(references|referees)\b/i }
            ];

            const lines = text.split("\n").map(l => l.trim()).filter(Boolean);

            const headings = [];
            for (let i = 0; i < lines.length; i++) {
                const line = lines[i];
                for (const def of sectionDefs) {
                    if (def.regex.test(line)) {
                        const inline = line.split(/[:\-]\s*/).slice(1).join(':').trim();
                        headings.push({ key: def.key, index: i, inline: inline || null });
                        break;
                    }
                }
            }

            function isHeadingLine(line) {
                return sectionDefs.some(d => d.regex.test(line));
            }

            if (headings.length > 0) {
                headings.sort((a, b) => a.index - b.index);
                for (let h = 0; h < headings.length; h++) {
                    const hi = headings[h];
                    const start = hi.index;
                    const end = (h + 1 < headings.length) ? headings[h + 1].index : lines.length;
                    const blockLines = [];
                    if (hi.inline) blockLines.push(hi.inline);
                    for (let k = start + 1; k < end; k++) {
                        if (isHeadingLine(lines[k])) break;
                        blockLines.push(lines[k]);
                    }
                    const rawBlock = blockLines.join("\n").trim();
                    if (!rawBlock) continue;
                    switch (hi.key) {
                        case "career_objective":
                            result.career_objective = rawBlock;
                            break;
                        case "skills":
                            result.skills = rawBlock;
                            break;
                        case "work_experience":
                            result.work_experience = rawBlock;
                            break;
                        case "education":
                            result.education = rawBlock;
                            break;
                        case "achievements":
                            result.achievements = rawBlock;
                            break;
                        case "references":
                            if (!/available upon request/i.test(rawBlock)) {
                                result.references = rawBlock;
                            }
                            break;
                    }
                }
            }

            return result;
        }
    </script>
</x-nav-layout>


