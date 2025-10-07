<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluation Questionnaire</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 font-sans text-gray-900">
<div class="min-h-screen flex items-center justify-center py-10 px-4">
    <div class="bg-white w-full max-w-3xl shadow-xl rounded-2xl p-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6 border-b pb-3">
            <h1 class="text-2xl font-bold flex items-center gap-2 text-green-700">
                <i data-lucide="clipboard-check" class="w-6 h-6"></i>
                Evaluation Questionnaire
            </h1>
            <span class="text-sm text-gray-500">Confidential</span>
        </div>

        <!-- Evaluator Info -->
        <div class="mb-6 bg-gray-50 p-4 rounded-lg border">
            <div class="flex items-center gap-2 text-gray-700">
                <i data-lucide="user" class="w-5 h-5 text-green-600"></i>
                <p>
                    Evaluator:
                    <b>{{ $evaluator->first_name }} {{ $evaluator->last_name }}</b>
                    <span class="text-gray-500 text-sm">({{ $evaluator->position }})</span>
                </p>
            </div>
        </div>

        <!-- Form -->
        <form id="evaluationForm" action="{{ route('evaluation.submit', $token) }}" method="POST">
            @csrf

            <!-- Step 1: Select Employee -->
            <div>
                <label class="block font-semibold mb-2 flex items-center gap-2 text-gray-800">
                    <i data-lucide="users" class="w-5 h-5 text-green-600"></i>
                    Select Employee to Evaluate
                </label>

                <select name="evaluatee_id" required
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-500 text-gray-900">
                    <option value="">-- Choose Employee --</option>
                    @foreach($employees as $target)
                        @if($evaluator->employeeprofiles_id !== $target->employeeprofiles_id)
                            @php
                                $canEvaluate = false;
                                if($evaluator->position === 'Helper' && in_array($target->position, ['Technician', 'Assistant Technician'])) $canEvaluate = true;
                                if($evaluator->position === 'Technician' && in_array($target->position, ['Helper', 'Assistant Technician'])) $canEvaluate = true;
                                if($evaluator->position === 'Assistant Technician' && in_array($target->position, ['Helper', 'Technician'])) $canEvaluate = true;

                                $alreadyEvaluated = \App\Models\EvaluationSummary::where('evaluator_id', $evaluator->employeeprofiles_id)
                                    ->where('evaluatee_id', $target->employeeprofiles_id)
                                    ->where('assessment_id', $assessment->assessment_id)
                                    ->exists();
                            @endphp
                            @if($canEvaluate && !$alreadyEvaluated)
                                <option value="{{ $target->employeeprofiles_id }}"
                                        {{ old('evaluatee_id') == $target->employeeprofiles_id ? 'selected' : '' }}>
                                    {{ $target->first_name }} {{ $target->last_name }} ({{ $target->position }})
                                </option>
                            @endif
                        @endif
                    @endforeach
                </select>
                @error('evaluatee_id')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Step 2: Questions -->
            <div class="mt-4">
                <label class="block font-semibold mb-3 flex items-center gap-2 text-gray-800">
                    <i data-lucide="file-text" class="w-5 h-5 text-green-600"></i>
                    Evaluation Questions
                </label>

                <div class="space-y-5">
                    @foreach ($questions as $q)
                        <div class="border rounded-lg p-4 hover:shadow transition bg-gray-50">
                            <label class="block font-medium text-gray-800 mb-2">
                                {{ $q->question }}
                                <span class="text-sm text-gray-500">[{{ $q->category }}]</span>
                            </label>
                            <select name="answers[{{ $q->evaluation_question_id }}]" required
                                    class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-500 text-gray-900">
                                <option value="">-- Select Rating --</option>
                                <option value="1">1 - Poor</option>
                                <option value="2">2 - Fair</option>
                                <option value="3">3 - Good</option>
                                <option value="4">4 - Very Good</option>
                                <option value="5">5 - Excellent</option>
                            </select>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Step 3: Feedback -->
            <div class="mt-4">
                <label class="block font-semibold mb-2 flex items-center gap-2 text-gray-800">
                    <i data-lucide="message-square-text" class="w-5 h-5 text-green-600"></i>
                    Additional Feedback (optional)
                </label>
                <textarea name="feedback" rows="4"
                          class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-500 text-gray-900 placeholder-gray-400"
                          placeholder="Write your feedback here...">{{ old('feedback') }}</textarea>
            </div>

            <!-- Submit -->
            <div class="text-right mt-4">
                <button type="submit"
                        class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md transition flex items-center gap-2 mx-auto">
                    <i data-lucide="send" class="w-5 h-5"></i>
                    Submit Evaluation
                </button>
            </div>
        </form>
    </div>
</div>

<script>
lucide.createIcons();

document.getElementById('evaluationForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const form = this;
    const formData = new FormData(form);

    fetch(form.action, {
        method: form.method,
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(async response => {
        const data = await response.json();

        if (response.ok && data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Thank you!',
                text: data.message,
                confirmButtonColor: '#16a34a'
            }).then(() => {
                // Automatically reload the page after clicking OK
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: data.error || 'Something went wrong. Please try again.'
            });
        }
    })
    .catch(() => {
        Swal.fire({
            icon: 'error',
            title: 'Network Error',
            text: 'Please check your connection and try again.'
        });
    });
});
</script>
</body>
</html>
