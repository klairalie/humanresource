<x-guest-layout>
    <div class="max-w-2xl mx-auto p-6 bg-white shadow rounded-lg mt-30">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Add Assessment</h1>

        <form action="{{ route('assessments.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Assessment Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                    Assessment Title <span class="text-red-500">*</span>
                </label>
                <select 
                    id="title" 
                    name="title" 
                    class="border border-gray-300 rounded-lg w-full p-2 focus:ring focus:ring-green-300 focus:outline-none" 
                    required
                    onchange="updateTypeAndPosition(this)"
                >
                    <option value="">-- Select Title --</option>
                    <option value="Applicant Assessment Test" data-type="test" data-description="This is an assessment test for applicants based on their applied position.">
                        Applicant Assessment Test
                    </option>
                    <option value="Employee Evaluation" data-type="evaluation" data-description="This is an evaluation form for employees to assess their performance.">
                        Employee Evaluation
                    </option>
                </select>
            </div>

            <!-- Hidden input for type -->
            <input type="hidden" id="type" name="type">

            <!-- Position Dropdown (only required for Applicant Test) -->
            <div id="positionWrapper">
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

    <!-- JS to auto-set type, toggle position, and auto-fill description -->
    <script>
        function updateTypeAndPosition(select) {
            let selected = select.options[select.selectedIndex];
            let type = selected.getAttribute("data-type");
            let defaultDescription = selected.getAttribute("data-description");

            // set hidden input for type
            document.getElementById("type").value = type;

            // toggle position dropdown requirement
            let positionWrapper = document.getElementById("positionWrapper");
            let positionSelect = document.getElementById("position_name");

            if (type === "test") {
                positionWrapper.style.display = "block";
                positionSelect.setAttribute("required", "required");
            } else {
                positionWrapper.style.display = "none";
                positionSelect.removeAttribute("required");
                positionSelect.value = "";
            }

            // auto-fill description (if textarea is empty or matches old default)
            let descField = document.getElementById("description");
            if (!descField.value || descField.value === "This is an assessment test for applicants based on their applied position." || descField.value === "This is an evaluation form for employees to assess their performance.") {
                descField.value = defaultDescription || "";
            }
        }

        // Run once on page load (in case of validation errors)
        document.addEventListener("DOMContentLoaded", function() {
            let titleSelect = document.getElementById("title");
            if (titleSelect.value !== "") {
                updateTypeAndPosition(titleSelect);
            } else {
                document.getElementById("positionWrapper").style.display = "none";
            }
        });
    </script>
</x-guest-layout>
