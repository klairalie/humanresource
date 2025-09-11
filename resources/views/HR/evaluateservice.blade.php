<x-guest-layout>
    <div class="min-h-screen p-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Service Evaluation</h1>
            
            <!-- Search + Quotation Button -->
            <div class="flex items-center space-x-2 mt-4 md:mt-0">
                <input 
                    type="text" 
                    placeholder="Search by Date, Service Type, or Technician..." 
                    class="w-72 px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-1 focus:ring-gray-400 focus:outline-none"
                >
                
                <!-- Quotation Button -->
                <a href="{{ route('show.quotationform') }}" 
                   class="px-4 py-2 bg-gray-800 text-white rounded-lg shadow hover:bg-gray-700 transition">
                    + Quotation
                </a>
            </div>
        </div>

        <!-- Reports Table -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-200 text-left text-gray-700">
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Service Type</th>
                        <th class="px-4 py-3">Technician</th>
                        <th class="px-4 py-3">Units</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Example row -->
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3">2025-08-23</td>
                        <td class="px-4 py-3">Installation</td>
                        <td class="px-4 py-3">John Doe</td>
                        <td class="px-4 py-3">5</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded bg-gray-300 text-gray-800">Ongoing</span>
                        </td>
                        <td class="px-4 py-3 text-center space-x-2">
                            <button class="px-3 py-1 bg-gray-700 text-white rounded hover:bg-gray-600 text-sm">Complete</button>
                            <button class="px-3 py-1 bg-amber-600 text-white rounded hover:bg-amber-500 text-sm">Reschedule</button>
                        </td>
                    </tr>
                    <!-- Repeat rows dynamically with foreach from DB -->
                </tbody>
            </table>
        </div>

        <!-- Summary Section -->
        <div class="mt-6 bg-white rounded-xl shadow p-4">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Summary Evaluation Report</h2>
            <ul class="text-gray-600 text-sm space-y-1">
                <li><strong>Total Units:</strong> 50</li>
                <li><strong>Service Types:</strong> Installation, Maintenance</li>
                <li><strong>Technicians:</strong> John Doe, Jane Smith</li>
                <li><strong>Date Range:</strong> Aug 1 – Aug 23, 2025</li>
            </ul>
        </div>

    </div>
</x-guest-layout>
