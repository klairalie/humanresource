<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Submitted</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

    <div class="max-w-2xl mx-auto mt-20 bg-white shadow-lg rounded-xl p-10 text-center border border-gray-200">
        
        <div class="flex justify-center mb-6">
            <div class="w-16 h-16 bg-green-100 text-green-600 flex items-center justify-center rounded-full shadow">
                ✓
            </div>
        </div>

        <h1 class="text-2xl font-bold text-gray-800 mb-4">Assessment Submitted</h1>

        @if(session('success'))
            <p class="text-gray-600">{{ session('success') }}</p>
        @else
            <p class="text-gray-600">Thank you for completing the assessment. Your answers have been recorded.</p>
        @endif

        <div class="mt-8">
            <p class="text-sm text-gray-400">You may now close this page.</p>
        </div>
    </div>

</body>
</html>
