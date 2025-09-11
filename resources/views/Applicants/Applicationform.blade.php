<x-nav-layout>

    {{-- Validation Errors (for non-JS fallback) --}}
    @if ($errors->any())
        <div class="max-w-3xl mx-auto mt-6">
            <div class="bg-red-100 text-red-700 p-4 rounded-lg">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Success Message --}}
   @if (session('success'))
    <script>
        alert("✅ {{ session('success') }}");
    </script>
@endif


    <!-- Applicant Registration Form -->
    <section class="relative pt-32 pb-20">
        <!-- Decorative Background Elements -->
        <div class="absolute top-10 left-10 w-24 h-24 bg-sky-200 rounded-full mix-blend-multiply filter blur-2xl opacity-40 animate-pulse"></div>
        <div class="absolute bottom-20 right-20 w-32 h-32 bg-emerald-200 rounded-full mix-blend-multiply filter blur-2xl opacity-40 animate-pulse"></div>

        <div class="max-w-4xl mx-auto px-6 relative">
            <div class="bg-white/70 backdrop-blur-md shadow-xl rounded-2xl p-10 border border-gray-200 overflow-hidden relative">

                <!-- Header -->
                <h1 class="text-3xl font-extrabold text-sky-900 mb-8 border-b border-gray-200 pb-4">Applicant Registration Form</h1>

                <form id="applicantForm" action="{{ route('applicants.store') }}" method="POST" enctype="multipart/form-data" class="space-y-10">
                    @csrf

                    <!-- Personal Information -->
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-6">Personal Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div>
                                <label class="block text-sm font-medium text-gray-600">First Name</label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}" class="validate-input mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-amber-400" required>
                                <p class="error-message text-sm text-red-600 mt-1 hidden"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-600">Last Name</label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}" class="validate-input mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-amber-400" required>
                                <p class="error-message text-sm text-red-600 mt-1 hidden"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-600">Contact Number</label>
                                <input type="text" name="contact_number" value="{{ old('contact_number') }}" class="validate-input mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-amber-400" required>
                                <p class="error-message text-sm text-red-600 mt-1 hidden"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-600">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" class="validate-input mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-amber-400" required>
                                <p class="error-message text-sm text-red-600 mt-1 hidden"></p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-600">Address</label>
                                <textarea name="address" rows="2" class="validate-input mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-amber-400" required>{{ old('address') }}</textarea>
                                <p class="error-message text-sm text-red-600 mt-1 hidden"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-600">Date of Birth</label>
                                <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="validate-input mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-amber-400">
                                <p class="error-message text-sm text-red-600 mt-1 hidden"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-600">Emergency Contact</label>
                                <input type="text" name="emergency_contact" value="{{ old('emergency_contact') }}" class="validate-input mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-amber-400">
                                <p class="error-message text-sm text-red-600 mt-1 hidden"></p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-600">Position Applying For</label>
                                <select name="position" class="validate-input mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-amber-400" required>
                                    <option value="" disabled {{ old('position') ? '' : 'selected' }}>-- Select Position --</option>
                                    <option value="Helper" {{ old('position')=='Helper' ? 'selected' : '' }}>Helper</option>
                                    <option value="Assistant Technician" {{ old('position')=='Assistant Technician' ? 'selected' : '' }}>Assistant Technician</option>
                                    <option value="Technician" {{ old('position')=='Technician' ? 'selected' : '' }}>Technician</option>
                                    <option value="Human Resource Manager" {{ old('position')=='Human Resource Manager' ? 'selected' : '' }}>Human Resource Manager</option>
                                    <option value="Administrative Manager" {{ old('position')=='Administrative Manager' ? 'selected' : '' }}>Administrative Manager</option>
                                    <option value="Finance Manager" {{ old('position')=='Finance Manager' ? 'selected' : '' }}>Finance Manager</option>
                                </select>
                                <p class="error-message text-sm text-red-600 mt-1 hidden"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Resume Details -->
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-6">Resume Details</h2>
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
                                    <label class="block text-sm font-medium text-gray-600">{{ $label }}</label>
                                    <textarea name="{{ $field }}" rows="3" class="validate-input mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-amber-400">{{ old($field) }}</textarea>
                                    <p class="error-message text-sm text-red-600 mt-1 hidden"></p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- File Uploads -->
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-6">Uploads</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Good Moral Certificate</label>
                                <input type="file" name="good_moral_file" class="validate-input mt-1 block w-full text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200">
                                <p class="error-message text-sm text-red-600 mt-1 hidden"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Certificate of Employment (COE)</label>
                                <input type="file" name="coe_file" class="validate-input mt-1 block w-full text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200">
                                <p class="error-message text-sm text-red-600 mt-1 hidden"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-end">
                       <button type="submit" class="w-full rounded-lg bg-amber-600 px-4 py-2 font-bold text-white hover:bg-amber-700">
        Submit Application
    </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    {{-- Real-time Validation Script --}}
    <script>
        document.getElementById('applicantForm').addEventListener('submit', function (e) {
    e.preventDefault(); // stop default submission

    // First confirmation
    if (confirm("Are you sure you want to submit it now?")) {
        // Second confirmation
        if (confirm("Are you sure you are providing reliable information?")) {
            this.submit(); // proceed with real submit
        } else {
            alert("❗ Please review your application carefully before submitting.");
        }
    } else {
        alert("✅ You may continue editing your application.");
    }
});
        document.addEventListener("DOMContentLoaded", () => {
            const inputs = document.querySelectorAll(".validate-input");

            inputs.forEach(input => {
                let timer;
                input.addEventListener("keyup", function () {
                    clearTimeout(timer);
                    const field = this.getAttribute("name");
                    const value = this.value;
                    const errorEl = this.parentElement.querySelector(".error-message");

                    timer = setTimeout(() => {
                        fetch("{{ route('validate.applicant') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({ field: field, value: value })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (!data.valid) {
                                errorEl.textContent = data.message;
                                errorEl.classList.remove("hidden");
                            } else {
                                errorEl.textContent = "";
                                errorEl.classList.add("hidden");
                            }
                        });
                    }, 400);
                });

                // For file input validation on change
                input.addEventListener("change", function () {
                    if (this.type === "file") {
                        const errorEl = this.parentElement.querySelector(".error-message");
                        const file = this.files[0];
                        if (file) {
                            const validTypes = ["image/jpeg", "image/png", "application/pdf"];
                            if (!validTypes.includes(file.type)) {
                                errorEl.textContent = "Invalid file type. Only JPG, PNG, or PDF allowed.";
                                errorEl.classList.remove("hidden");
                                this.value = "";
                            } else if (file.size > 2 * 1024 * 1024) {
                                errorEl.textContent = "File too large. Max 2MB allowed.";
                                errorEl.classList.remove("hidden");
                                this.value = "";
                            } else {
                                errorEl.textContent = "";
                                errorEl.classList.add("hidden");
                            }
                        }
                    }
                });
            });
        });
    </script>

</x-nav-layout>
