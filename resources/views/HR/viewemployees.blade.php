<x-guest-layout>



    <div class="min-h-screen bg-transparent p-6">
        <!-- Header Section with Search and Add Button -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <h1 class="text-2xl font-bold mb-4 md:mb-0">Employee Profiles</h1>
            <div class="flex items-center space-x-4">
                <input type="text" placeholder="Search employee..."
                    class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
                <a href="/HR/employeeprofiles"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-full text-xl flex items-center justify-center">
                    +
                </a>
            </div>
        </div>

        <!-- Employee Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($employee as $emp)
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-300">
                    <div class="flex justify-between items-start mb-4">
                        <h4 class="text-xl font-bold text-gray-800">
                            {{ $emp['first_name'] }} <span class="font-medium">{{ $emp['last_name'] }}</span>
                        </h4>

                        <form action="{{ route('show.edit', $emp['employeeprofiles_id']) }}" method="GET">

                            <button type="submit"
                                class="bg-transparent hover:underline text-black px-4 py-2 rounded-md">Edit</button>
                        </form>

                    </div>

                    <div class="space-y-2 text-gray-700 text-sm">
                        <p><span class="font-semibold">Employee ID:</span> {{ $emp['employeeprofiles_id'] }}</p>
                        <p><span class="font-semibold">Address:</span> {{ $emp['address'] }}</p>
                        <p><span class="font-semibold">Position:</span> {{ $emp['position'] }}</p>
                        <p><span class="font-semibold">Contact:</span> {{ $emp['contact_info'] }}</p>
                        <p><span class="font-semibold">Hire Date:</span> {{ $emp['hire_date'] }}</p>
                        <p><span class="font-semibold">Status:</span> {{ $emp['status'] }}</p>
                        <p><span class="font-semibold">Emergency Contact:</span> {{ $emp['emergency_contact'] }}</p>
                        <p class="font-semibold">Fingerprint:</p>
                        <img src="data:image/png;base64,{{ $emp['fingerprint_data'] }}" alt="Fingerprint"
                            class="w-32 h-auto border border-gray-300 rounded">
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-guest-layout>
