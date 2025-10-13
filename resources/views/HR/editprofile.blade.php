<x-guest-layout>
    <div class="max-w-md mx-auto bg-white p-6 rounded-xl text-black shadow-lg">
        
        <!-- Profile Picture / HR Logo -->
        <div class="flex flex-col items-center mb-6">
            <div class="relative w-28 h-28 flex items-center justify-center rounded-full bg-orange-400 border font-bold text-2xl mb-3">
                HR
            </div>
            <span class="text-sm text-gray-500">Click below to update your profile picture</span>
        </div>

        <!-- Form -->
        <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    value="{{ old('email', $employee->email) }}" 
                    required 
                    class="w-full border border-gray-300 rounded-md p-2"
                >
            </div>

            <!-- Username -->
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input 
                    type="text" 
                    name="username" 
                    id="username" 
                    value="{{ old('username', $admin->username) }}" 
                    required 
                    class="w-full border border-gray-300 rounded-md p-2"
                >
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    placeholder="Enter new password to change"
                    class="w-full border border-gray-300 rounded-md p-2"
                >
                <small class="text-gray-500">Leave blank to keep your current password.</small>
            </div>

            <!-- Submit -->
            <div class="text-center space-x-2">
                <button 
                    type="submit" 
                    class="px-6 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-xl shadow-md transition duration-200"
                >
                    Update Profile
                </button>

                <a href="{{ route('show.dashboard') }}" 
                   class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-black rounded-xl shadow-md transition duration-200">
                   Close
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
