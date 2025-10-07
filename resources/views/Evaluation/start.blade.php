{{-- THE START OF EMPLOYEE EVALUATION BUT I DONT KNOW WHERE AND WHEN IS USED --}}

<x-guest-layout>
    <div class="max-w-3xl mx-auto p-8 bg-white shadow rounded-xl mb-20">
        <h1 class="text-2xl font-bold text-black mb-6">Employee Evaluation</h1>
        <p class="text-black mb-4">Hello, {{ $employee->first_name }}. Please answer the following questions:</p>

        <form action="{{ route('evaluation.submit') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="token" value="{{ $tokenRecord->token }}">
            <input type="hidden" name="employee_id" value="{{ $employee->employee_id }}">
            <input type="hidden" name="assessment_id" value="{{ $tokenRecord->assessment_id }}">

            @foreach($questions as $idx => $q)
                <div class="p-4 border rounded-lg bg-gray-50 shadow-sm">
                    <label class="font-semibold text-black mb-2 block">Q{{ $idx+1 }} ({{ $q->category }}):</label>
                    <textarea name="answers[{{ $q->id }}]" 
                              class="border rounded-lg p-2 w-full focus:ring-2 focus:ring-green-400 text-black"
                              required></textarea>
                </div>
            @endforeach

            <button type="submit" 
                    class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow-md">
                Submit Evaluation
            </button>
        </form>
    </div>
</x-guest-layout>
