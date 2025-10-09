<x-guest-layout>
    <div class="container mx-auto p-10">
        <h1 class="text-2xl font-bold mb-6">Add Aircon Product</h1>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('aircon-types.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <input type="text" name="name" placeholder="Aircon Name" class="w-full p-2 border rounded" required>
            <input type="text" name="brand" placeholder="Brand" class="w-full p-2 border rounded">
            <input type="text" name="capacity" placeholder="Capacity (e.g., 1.5HP)" class="w-full p-2 border rounded">
            <input type="text" name="model" placeholder="Model" class="w-full p-2 border rounded">
            <input type="text" name="category" placeholder="Category (Split/Window)" class="w-full p-2 border rounded">
            <input type="number" step="0.01" name="base_price" placeholder="Base Price" class="w-full p-2 border rounded">
            <textarea name="description" placeholder="Description" class="w-full p-2 border rounded"></textarea>
            <input type="file" name="image" class="w-full p-2 border rounded">

            <button type="submit" class="bg-purple-500 text-white px-6 py-2 rounded hover:bg-purple-600">
                Add Aircon
            </button>
        </form>
    </div>
</x-guest-layout>
