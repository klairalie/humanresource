<x-guest-layout>
    <div class="max-w-md mx-auto bg-white p-6 rounded-xl shadow-lg">
        
        <!-- Profile Picture Preview -->
        <div class="flex flex-col items-center mb-6">
            <img 
                id="profilePreview" 
                src="https://via.placeholder.com/120?text=Profile+Pic" 
                alt="Profile Picture" 
                class="w-28 h-28 rounded-full border-4 border-cyan-400 shadow-md object-cover mb-3"
            >
            <span class="text-sm text-gray-500">Click below to update your profile picture</span>
        </div>

        <!-- Form -->
        <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Profile Picture -->
            <div>
                <label for="profile_picture" class="block text-sm font-medium text-gray-700 mb-1">
                    Profile Picture
                </label>
                <input 
                    type="file" 
                    name="profile_picture" 
                    id="profile_picture" 
                    accept="image/*"
                    class="w-full border border-gray-300 rounded-md p-2"
                    onchange="previewImage(event)"
                >
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email
                </label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    value="" 
                    required 
                    class="w-full border border-gray-300 rounded-md p-2"
                >
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Password
                </label>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    required 
                    class="w-full border border-gray-300 rounded-md p-2"
                >
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                    Confirm Password
                </label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    id="password_confirmation" 
                    required 
                    class="w-full border border-gray-300 rounded-md p-2"
                >
            </div>

            <!-- Submit -->
            <div class="text-center">
                <button 
                    type="submit" 
                    class="px-6 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-xl shadow-md transition duration-200"
                >
                    Update Profile
                </button>

                <a href="{{ route('show.dashboard') }}" class="px-6 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-xl shadow-md transition duration-200"> Close</a>
            </div>
        </form>
    </div>

    <!-- Live Preview Script -->
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function(){
                document.getElementById('profilePreview').src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</x-guest-layout>
