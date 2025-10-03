<x-guest-layout>
    <div class="max-w-4xl mx-auto mt-10 bg-white rounded-xl shadow-lg p-8 border border-gray-200">
        <!-- Header -->
        <div class="flex items-center gap-2 mb-6 border-b pb-3">
            <i data-lucide="settings" class="w-6 h-6 text-green-600"></i>
            <h1 class="text-2xl font-bold text-gray-800">System Settings</h1>
        </div>

        <!-- Grid Links -->
        <div class="grid gap-4 sm:grid-cols-2">
            <!-- Manage Assessment Questions -->
            <a href="{{ route('view.questions') }}"
               class="flex items-center p-4 bg-gradient-to-r from-blue-100 to-blue-50 rounded-lg shadow hover:shadow-md transition">
                <i data-lucide="help-circle" class="w-6 h-6 text-blue-600 mr-3"></i>
                <span class="font-medium text-gray-700">Manage Assessment Questions</span>
            </a>

            <!-- Manage Assessments -->
            <a href="{{ route('assessments.create') }}"
               class="flex items-center p-4 bg-gradient-to-r from-green-100 to-green-50 rounded-lg shadow hover:shadow-md transition">
                <i data-lucide="list-checks" class="w-6 h-6 text-green-600 mr-3"></i>
                <span class="font-medium text-gray-700">Manage Assessments</span>
            </a>

            <!-- View Assessment Results -->
            <a href="{{ route('assessment.results') }}"
               class="flex items-center p-4 bg-gradient-to-r from-purple-100 to-purple-50 rounded-lg shadow hover:shadow-md transition">
                <i data-lucide="bar-chart-3" class="w-6 h-6 text-purple-600 mr-3"></i>
                <span class="font-medium text-gray-700">View Assessment Results</span>
            </a>

            <!-- Employee Attendance -->
            <a href="{{ route('employee.attendance') }}"
               class="flex items-center p-4 bg-gradient-to-r from-orange-100 to-orange-50 rounded-lg shadow hover:shadow-md transition">
                <i data-lucide="user-check" class="w-6 h-6 text-orange-600 mr-3"></i>
                <span class="font-medium text-gray-700">Employee Attendance</span>
            </a>

            <!-- Edit Profile Info -->
            <a href="{{ route('show.editprofile') }}"
               class="flex items-center p-4 bg-gradient-to-r from-pink-100 to-pink-50 rounded-lg shadow hover:shadow-md transition">
                <i data-lucide="user-cog" class="w-6 h-6 text-pink-600 mr-3"></i>
                <span class="font-medium text-gray-700">Edit Profile Info</span>
            </a>
        </div>
    </div>

    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script>
        lucide.createIcons();
    </script>
</x-guest-layout>
