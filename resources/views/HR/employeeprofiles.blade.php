<x-guest-layout>
    <div class="container mx-auto p-10 bg-transparent rounded-2xl shadow-xl max-w-5xl">
        <h1 class="text-3xl font-semibold text-center text-slate-800 mb-10">Add New Employee</h1>

        <form action="{{ route('submit.employeeprofiles')}}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 justify-center ml-25 bg-transparent">
                <!-- LEFT COLUMN -->
                <div class="space-y-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-slate-700">First Name</label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                            class="mt-1 w-64 border border-gray-300 rounded-md shadow-sm px-3 py-1.5 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-slate-700">Last Name</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                            class="mt-1 w-64 border border-gray-300 rounded-md shadow-sm px-3 py-1.5 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-medium text-slate-700">Address</label>
                        <input type="text" name="address" id="address" value="{{ old('address') }}" required
                            class="mt-1 w-64 border border-gray-300 rounded-md shadow-sm px-3 py-1.5 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>

                    <div>
                        <label for="position" class="block text-sm font-medium text-slate-700">Position</label>
                        <input type="text" name="position" id="position" value="{{ old('position') }}" required
                            class="mt-1 w-64 border border-gray-300 rounded-md shadow-sm px-3 py-1.5 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>

                    <div>
                        <label for="contact_info" class="block text-sm font-medium text-slate-700">Contact Info</label>
                        <input type="text" name="contact_info" id="contact_info" value="{{ old('contact_info') }}" required
                            class="mt-1 w-64 border border-gray-300 rounded-md shadow-sm px-3 py-1.5 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>

                    <div>
                        <label for="hire_date" class="block text-sm font-medium text-slate-700">Hire Date</label>
                        <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date') }}" required
                            class="mt-1 w-64 border border-gray-300 rounded-md shadow-sm px-3 py-1.5 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>
                </div>

                <!-- RIGHT COLUMN -->
                <div class="space-y-6">
                    <div>
                        <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
                        <input type="text" name="status" id="status" value="{{ old('status') }}" required
                            class="mt-1 w-64 border border-gray-300 rounded-md shadow-sm px-3 py-1.5 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>

                    <div>
                        <label for="emergency_contact" class="block text-sm font-medium text-slate-700">Emergency Contact</label>
                        <input type="text" name="emergency_contact" id="emergency_contact" value="{{ old('emergency_contact') }}"
                            class="mt-1 w-64 border border-gray-300 rounded-md shadow-sm px-3 py-1.5 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Fingerprint Scans</label>
                        <div class="flex flex-wrap gap-4 mt-2">
                            <canvas id="fingerprintPreview1" width="120" height="120" class="border rounded shadow-sm"></canvas>
                            <canvas id="fingerprintPreview2" width="120" height="120" class="border rounded shadow-sm"></canvas>
                            <canvas id="fingerprintPreview3" width="120" height="120" class="border rounded shadow-sm"></canvas>
                        </div>

                        <input type="hidden" name="fingerprintScan1" id="fingerprintScan1">
                        <input type="hidden" name="fingerprintScan2" id="fingerprintScan2">
                        <input type="hidden" name="fingerprintScan3" id="fingerprintScan3">

                        <p id="fingerprintStatus" class="text-sm text-gray-500 mt-2">Please scan fingerprint (1 of 3)</p>

                        <button type="button" id="scanFingerprint"
                            class="mt-3 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-all">
                            Scan Fingerprint
                        </button>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-center">
                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-md text-md font-medium hover:bg-blue-700 transition">
                    Submit
                </button>
            </div>

            @if($errors->any())
                <div class="mt-6">
                    <ul class="bg-red-100 text-red-700 p-4 rounded-md border border-red-300 space-y-2">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </form>
    </div>
</x-guest-layout>
