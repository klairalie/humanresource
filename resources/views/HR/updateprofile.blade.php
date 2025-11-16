<x-guest-layout>
    <div class="max-w-5xl mx-auto bg-white p-10 rounded-xl shadow-xl border border-gray-200 mb-20">
        <h1 class="text-3xl font-bold text-black mb-8 border-b pb-4">Edit Employee Profile</h1>

        {{-- Error Handling --}}
        @if ($errors->any())
            <script>
                @foreach ($errors->all() as $error)
                    alert("{{ $error }}");
                @endforeach
            </script>
        @endif

        SweetAlert Success Message
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
                document.getElementById('position').addEventListener('change', function() {
                    const salary = this.options[this.selectedIndex].dataset.salary;
                    document.getElementById('salary_rate').value = salary || '';
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
            <div>
<label class="block text-sm font-semibold text-black mb-2">Face Descriptor</label>
                <input type="text" name="face_descriptor" value="{{ old('face_descriptor', $employee->face_descriptor) }} "
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-amber-500 text-black" readonly>

                
            </div>
            <!-- Buttons -->
            <div class="flex justify-end space-x-3 mt-6 col-span-2 mb-5">
                <a href="{{ route('show.employeeprofiles') }}"
                   class="px-6 py-2 bg-gray-300 text-black rounded-lg hover:bg-gray-400 transition font-semibold">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-amber-500 text-black font-semibold rounded-lg hover:bg-amber-600 transition">
                    Save Changes
                </button>
            </div>
            <input type="hidden" id="face_descriptor" name="face_descriptor" value="{{ old('face_descriptor', $employee->face_descriptor) }}">
        </form>
       <div id="camera-wrapper" style="position:relative; width:640px; height:480px; margin:auto;">

    <!-- VIDEO -->
    <video id="video" width="640" height="480" autoplay muted playsinline
        style="position:absolute; top:0; left:0; background:black;">
    </video>

    <!-- CANVAS OVERLAY -->
    <canvas id="overlay" width="640" height="480"
        style="position:absolute; top:0; left:0;">
    </canvas>

    <!-- STATUS -->
    <div id="status-div"
        style="position:absolute; top:10px; left:10px;
               background:rgba(0,0,0,0.7); color:white; padding:10px;
               font-family:Arial; font-size:14px; z-index:100;">
        Camera inactive. Click Register Face.
    </div>

    <!-- REGISTER BUTTON -->
    <button id="register-btn"
        style="position:absolute; bottom:10px; left:10px;
               padding:10px 20px; background:#007bff; color:white; border:none;
               font-size:16px; cursor:pointer; z-index:100;">
        Register Face
    </button>

</div>


    </div>

   
</x-guest-layout>
