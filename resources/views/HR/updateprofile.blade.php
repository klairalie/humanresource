<x-guest-layout>
    <div class="max-w-5xl mx-auto bg-white p-8 md:p-10 rounded-xl shadow-xl border border-gray-200 mb-20">
        <h1 class="text-3xl font-bold text-black mb-6 border-b pb-4">Edit Employee Profile</h1>

        {{-- Error Handling --}}
        @if ($errors->any())
            <script>
                @foreach ($errors->all() as $error)
                    alert("{{ $error }}");
                @endforeach
            </script>
        @endif

        {{-- SweetAlert Success Message --}}
        @if (session('success'))
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: '{{ session('success') }}',
                        confirmButtonColor: '#f59e0b',
                        timer: 2500,
                        showConfirmButton: false
                    });
                });
            </script>
        @endif

        <form action="{{ route('update.profile', $employee->employeeprofiles_id) }}" method="POST"
              class="grid grid-cols-2 gap-6">
            @csrf
            @method('PUT')

            <!-- First Name -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">First Name</label>
                <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-amber-500 text-black">
            </div>

            <!-- Last Name -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Last Name</label>
                <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-amber-500 text-black">
            </div>

            <!-- Address -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Address</label>
                <input type="text" name="address" value="{{ old('address', $employee->address) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-amber-500 text-black">
            </div>

            <!-- Position -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Position</label>
                <select name="position" id="position"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-amber-500 text-black">
                    <option value="">-- Select Position --</option>
                    @foreach ($salaries as $salary)
                        <option value="{{ $salary->position }}"
                            {{ old('position', $employee->position ?? '') == $salary->position ? 'selected' : '' }}
                            data-salary="{{ $salary->salary_rate }}">
                            {{ $salary->position }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Salary Rate -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Salary Rate</label>
                <input type="text" id="salary_rate"
                       value="{{ $employee->salary_rates?->salary_rate ?? '' }}"
                       readonly
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-gray-100 focus:ring-amber-500 text-black">
            </div>

            <!-- Update salary automatically when position changes -->
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const positionEl = document.getElementById('position');
                    if (positionEl) {
                        positionEl.addEventListener('change', function () {
                            const salary = this.options[this.selectedIndex].dataset.salary;
                            document.getElementById('salary_rate').value = salary || '';
                        });
                    }
                });
            </script>

            <!-- Contact Number -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Contact Number</label>
                <input type="text" name="contact_number" value="{{ old('contact_number', $employee->contact_number) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-amber-500 text-black">
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Status</label>
                <input type="text" name="status" value="{{ old('status', $employee->status) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-amber-500 text-black">
            </div>

            <!-- Emergency Contact -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Emergency Contact</label>
                <input type="text" name="emergency_contact" value="{{ old('emergency_contact', $employee->emergency_contact) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-amber-500 text-black">
            </div>

            <!-- Hire Date -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Hire Date</label>
                <input type="date" name="hire_date" value="{{ old('hire_date', $employee->hire_date) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-amber-500 text-black">
            </div>

            <!-- Face Descriptor (read-only visible field for debugging) -->
            <div>
                <label class="block text-sm font-semibold text-black mb-2">Face Descriptor</label>
                <input type="text" name="face_descriptor_display"
                       value="{{ old('face_descriptor', $employee->face_descriptor) }} "
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-amber-500 text-black" readonly>
            </div>

            <!-- Buttons (form) -->
            <div class="flex justify-end space-x-3 mt-6 col-span-2 mb-5">
                <a href="{{ route('show.employeeprofiles') }}"
                   class="px-6 py-2 bg-gray-300 text-black rounded-lg hover:bg-gray-400 transition font-semibold">
                    Cancel
                </a>
                <button type="submit" id="save-btn"
                        class="px-6 py-2 bg-amber-500 text-black font-semibold rounded-lg hover:bg-amber-600 transition">
                    Save Changes
                </button>
            </div>

            <input type="hidden" id="face_descriptor" name="face_descriptor" value="{{ old('face_descriptor', $employee->face_descriptor) }}">
        </form>

        {{-- CAMERA MODULE --}}
        <div class="mt-6 bg-gray-50 border border-gray-200 rounded-md p-4 md:p-6 max-w-3xl mx-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-black">Face Registration</h2>
                <div class="text-sm text-gray-600">Follow the steps to register the face</div>
            </div>

            <div id="camera-wrapper" class="relative w-full max-w-[640px] mx-auto aspect-video bg-black rounded-md overflow-hidden">
                <!-- VIDEO -->
                <video id="video" width="640" height="480" autoplay muted playsinline
                       class="absolute inset-0 w-full h-full object-cover bg-black"></video>

                <!-- CANVAS OVERLAY -->
                <canvas id="overlay" width="640" height="480"
                        class="absolute inset-0 w-full h-full pointer-events-none"></canvas>

                <!-- STATUS -->
                <div id="status-div"
                     class="absolute top-3 left-3 bg-black bg-opacity-60 text-white px-3 py-1 rounded text-sm z-20">
                    Camera inactive. Click Turn On Camera.
                </div>

                <!-- CONTROL BUTTONS (bottom-left) -->
                <div class="absolute bottom-3 left-3 z-30 flex items-center gap-2">
                    <button id="turnon-btn" type="button"
                            class="px-3 py-1 bg-emerald-500 text-white rounded hover:bg-emerald-600 text-sm">
                        Turn On Camera
                    </button>

                    <button id="register-btn" type="button" style="display:none;"
                            class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                        Register Face
                    </button>

                    <button id="retake-btn" type="button" style="display:none;"
                            class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm">
                        Retake Face
                    </button>
                </div>
            </div>

            {{-- Preview / Descriptor area (generated by JS) --}}
            <div id="descriptor-preview-container" class="mt-4"></div>
        </div>
    </div>

   
</x-guest-layout>
