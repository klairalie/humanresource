<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Thank You</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <div class="bg-white p-8 rounded-2xl shadow-xl text-center max-w-lg">
    <h1 class="text-3xl font-bold text-green-600 mb-4">✅ Thank You!</h1>
    <p class="text-gray-700 mb-6">
      Your evaluation has been successfully submitted.<br>
      Would you like to evaluate another employee?
    </p>

    @if($remainingEvaluatees->count() > 0)
      <form action="{{ route('evaluation.questionnaire', $token) }}" method="GET">
        <button type="submit"
          class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg">
          Yes, Continue Evaluating
        </button>
      </form>
    @endif

    <a href="{{ route('home') }}"
      class="block mt-4 text-gray-500 hover:text-gray-700 text-sm">
      No, Return to Home
    </a>
  </div>

  <script>
    Swal.fire({
      icon: 'success',
      title: 'Thank you!',
      text: 'Your evaluation was submitted successfully.',
      confirmButtonColor: '#16a34a'
    });
  </script>
</body>
</html>
