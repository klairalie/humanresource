<x-guest-layout>
    <div class="max-w-7xl mx-auto mt-10 px-6">
        <!-- Header -->
        <div class="flex items-center gap-3 mb-10 border-b pb-4">
            <i data-lucide="settings" class="w-8 h-8 text-green-600"></i>
            <h1 class="text-3xl font-bold text-gray-800">System Settings</h1>
        </div>

        <!-- Grid Cards -->
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Manage Assessment Questions -->
            <a href="{{ route('view.questions') }}"
               class="block p-6 bg-gradient-to-br from-blue-50 to-white rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-1">
                <div class="flex items-center mb-3">
                    <i data-lucide="help-circle" class="w-7 h-7 text-blue-600 mr-2"></i>
                    <h2 class="font-semibold text-gray-800">Manage Assessment Questions</h2>
                </div>
                <p class="text-sm text-gray-500">Create, edit, and organize assessment questions.</p>
            </a>

            <!-- Manage Evaluation Questions -->
            <a href="{{ route('evaluation.view') }}"
               class="block p-6 bg-gradient-to-br from-blue-50 to-white rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-1">
                <div class="flex items-center mb-3">
                    <i data-lucide="help-circle" class="w-7 h-7 text-blue-600 mr-2"></i>
                    <h2 class="font-semibold text-gray-800">Manage Evaluation Questions</h2>
                </div>
                <p class="text-sm text-gray-500">Add and maintain evaluation question sets.</p>
            </a>

            <!-- Manage Assessments -->
            <a href="{{ route('assessments.create') }}"
               class="block p-6 bg-gradient-to-br from-green-50 to-white rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-1">
                <div class="flex items-center mb-3">
                    <i data-lucide="list-checks" class="w-7 h-7 text-green-600 mr-2"></i>
                    <h2 class="font-semibold text-gray-800">Manage Assessments</h2>
                </div>
                <p class="text-sm text-gray-500">Build and assign assessments to employees.</p>
            </a>

            <!-- View Assessment Results -->
            <a href="{{ route('assessment.results') }}"
               class="block p-6 bg-gradient-to-br from-purple-50 to-white rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-1">
                <div class="flex items-center mb-3">
                    <i data-lucide="bar-chart-3" class="w-7 h-7 text-purple-600 mr-2"></i>
                    <h2 class="font-semibold text-gray-800">View Assessment Results</h2>
                </div>
                <p class="text-sm text-gray-500">Analyze and review assessment scores.</p>
            </a>

            <!-- Employee Attendance -->
            <a href="{{ route('employee.attendance') }}"
               class="block p-6 bg-gradient-to-br from-orange-50 to-white rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-1">
                <div class="flex items-center mb-3">
                    <i data-lucide="user-check" class="w-7 h-7 text-orange-600 mr-2"></i>
                    <h2 class="font-semibold text-gray-800">Employee Attendance</h2>
                </div>
                <p class="text-sm text-gray-500">Track employee presence and schedules.</p>
            </a>

            <!-- Edit Profile Info -->
            <a href="{{ route('show.editprofile') }}"
               class="block p-6 bg-gradient-to-br from-pink-50 to-white rounded-xl shadow-md hover:shadow-lg transition transform hover:-translate-y-1">
                <div class="flex items-center mb-3">
                    <i data-lucide="user-cog" class="w-7 h-7 text-pink-600 mr-2"></i>
                    <h2 class="font-semibold text-gray-800">Edit Profile Info</h2>
                </div>
                <p class="text-sm text-gray-500">Update your profile and account information.</p>
            </a>
        </div>
    </div>

    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script>
        lucide.createIcons();
    </script>
</x-guest-layout>
