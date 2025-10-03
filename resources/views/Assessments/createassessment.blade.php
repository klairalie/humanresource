<x-guest-layout>
    <div class="max-w-2xl mx-auto p-6 bg-white shadow rounded-lg mt-30">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Add Assessment</h1>

        <form action="{{ route('assessments.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Assessment Type -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                    Assessment Type <span class="text-red-500">*</span>
                </label>
                <select 
                    id="type" 
                    name="type" 
                    class="border border-gray-300 rounded-lg w-full p-2 focus:ring focus:ring-green-300 focus:outline-none" 
                    required
                >
                    <option value="">-- Select Type --</option>
                    <option value="test">Applicant Assessment Test</option>
                    <option value="evaluation">Employee Evaluation</option>
                </select>
            </div>

            <!-- Position Dropdown (only required for test) -->
            <div>
                <label for="position_name" class="block text-sm font-medium text-gray-700 mb-1">
                    Position <span class="text-red-500">*</span>
                </label>
                <select 
                    id="position_name" 
                    name="position_name" 
                    class="border border-gray-300 rounded-lg w-full p-2 focus:ring focus:ring-green-300 focus:outline-none"
                >
                    <option value="">-- Select Position --</option>
                    <option value="Helper">Helper</option>
                    <option value="Assistant Technician">Assistant Technician</option>
                    <option value="Technician">Technician</option>
                    <option value="Human Resource Manager">Human Resource Manager</option>
                    <option value="Administrative Manager">Administrative Manager</option>
                    <option value="Finance Manager">Finance Manager</option>
                </select>
                <small class="text-gray-500">Required for Applicant Tests, optional for Evaluations</small>
            </div>

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                    Assessment Title <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    class="border border-gray-300 rounded-lg w-full p-2 focus:ring focus:ring-green-300 focus:outline-none" 
                    required
                >
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Description
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4" 
                    class="border border-gray-300 rounded-lg w-full p-2 focus:ring focus:ring-green-300 focus:outline-none"
                ></textarea>
            </div>

            <!-- Buttons -->
            <div class="pt-2 flex gap-3">
                <a href="{{ url()->previous() }}" 
                   class="px-5 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium rounded-lg shadow">
                    ← Back
                </a>
                <button 
                    type="submit" 
                    class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow"
                >
                    Add Assessment
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
