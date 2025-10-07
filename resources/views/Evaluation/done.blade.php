<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Evaluation Complete</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <div class="bg-white p-8 rounded-2xl shadow-xl text-center max-w-lg">
    <h1 class="text-3xl font-bold text-green-600 mb-4">🎉 Evaluation Complete!</h1>
    <p class="text-gray-700 mb-6">
      Great job, <strong>{{ $evaluator->first_name }}</strong>!<br>
      You’ve completed all your assigned evaluations.
    </p>
    <a href="{{ route('home') }}"
      class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg">
      Back to Home
    </a>
  </div>
</body>
</html>
