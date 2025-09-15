<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 py-12 px-6">
        <div class="bg-white shadow-xl rounded-2xl w-full max-w-2xl p-8 relative" x-data="{ showModal: false }">

            <!-- Close (X) Button -->
            <a href="{{ route('show.dashboard') }}" 
               class="absolute top-4 right-4 text-gray-500 hover:text-gray-800">
                ✕
            </a>

            <!-- Title -->
            <h2 class="text-2xl font-bold text-sky-800 mb-6 text-center">
                Upload Resume Format
            </h2>

            <!-- Upload Form -->
            <form action="{{ route('resume.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-gray-700 font-medium mb-2">
                        Choose File (.doc, .docx)
                    </label>
                    <input type="file" 
                           name="resume" 
                           accept=".doc,.docx" 
                           class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    @error('resume')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" 
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    Upload
                </button>
            </form>

            <!-- Success Message -->
            @if(session('success'))
                <div class="mt-4 bg-green-100 text-green-700 px-4 py-2 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Resume Format Table -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Current Resume Format</h3>
                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-200 rounded-lg overflow-hidden">
                        <thead class="bg-gray-100 text-gray-700 text-sm">
                            <tr>
                                <th class="px-4 py-2 text-left">File Name</th>
                                <th class="px-4 py-2 text-left">Date Uploaded</th>
                                <th class="px-4 py-2 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @if($resume)
                                <tr class="border-t">
                                    <td class="px-4 py-2">{{ $resume->file_name }}</td>
                                    <td class="px-4 py-2">
                                        {{ \Carbon\Carbon::parse($resume->created_at)->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="px-4 py-2 text-center flex justify-center gap-3">
                                        <!-- View Details (Open Modal) -->
                                        <button type="button" 
                                                class="text-blue-600 hover:underline text-sm"
                                                @click="showModal = true">
                                            View
                                        </button>

                                        <!-- Delete -->
                                        <form action="{{ route('resume.delete', $resume->resume_format_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this resume format?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline text-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="3" class="px-4 py-4 text-center text-gray-500">
                                        No resume format uploaded yet.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Resume Details Modal -->
            <div x-show="showModal" 
                 class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
                 x-cloak>
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 relative">
                    
                    <!-- Close Button -->
                    <button @click="showModal = false" 
                            class="absolute top-3 right-3 text-gray-500 hover:text-gray-800">
                        ✕
                    </button>

                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Resume Details</h3>
                    
                    @if($resume)
                        <div class="space-y-3 text-sm text-gray-700">
                            <p><strong>File Name:</strong> {{ $resume->file_name }}</p>
                            <p><strong>Date Uploaded:</strong> {{ \Carbon\Carbon::parse($resume->created_at)->format('M d, Y h:i A') }}</p>
                            <p><strong>File Path:</strong> {{ $resume->file_path ?? 'N/A' }}</p>
                        </div>
                    @else
                        <div class="text-gray-500 text-sm">
                            No resume uploaded yet.
                        </div>
                    @endif

                    <div class="mt-6 text-right">
                        <button @click="showModal = false" 
                                class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg">
                            Close
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-guest-layout>
