<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Questionnaire</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    
    <div class="max-w-4xl mx-auto mt-10 bg-white shadow-2xl rounded-xl overflow-hidden border border-gray-200">
        
        <!-- Header -->
        <div class="bg-blue-700 text-white p-6 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-blue-700 font-bold text-lg shadow">
                    3RS
                </div>
                <div>
                    <h1 class="text-2xl font-bold">Assessment Questionnaire</h1>
                    <p class="text-sm text-blue-100">Confidential – Please answer honestly</p>
                </div>
            </div>
            <div class="text-right text-sm italic">
                <p>Form ID: {{ strtoupper($token) }}</p>
            </div>
        </div>

        <!-- Body -->
        <div class="p-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('question.store', $token) }}" method="POST">
                @csrf

                @foreach($questions as $index => $question)
                    <div class="mb-8">
                        <div class="flex items-start space-x-3 mb-3">
                            <div class="w-8 h-8 flex items-center justify-center bg-blue-600 text-white font-bold rounded-full shadow">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <p class="font-semibold text-lg text-gray-800">
                                    {{ $question->question }}
                                </p>
                                <span class="text-sm text-gray-500">Difficulty: {{ ucfirst($question->level) }}</span>
                            </div>
                        </div>

                        <div class="ml-11 space-y-3">
                            @foreach(['a', 'b', 'c', 'd'] as $choice)
                                @if($question->{'choice_'.$choice})
                                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-blue-50 transition">
                                        <input type="radio" 
                                            name="question_{{ $question->assessment_question_id }}" 
                                            value="{{ strtoupper($choice) }}" 
                                            class="text-blue-600 focus:ring-blue-500">
                                        <span class="ml-3 text-gray-700">{{ $question->{'choice_'.$choice} }}</span>
                                    </label>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="text-center mt-10">
                    <button type="submit" class="bg-blue-700 text-white px-8 py-3 rounded-lg shadow-md hover:bg-blue-800 transition font-semibold">
                        Submit Assessment
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 border-t text-center text-sm text-gray-500 py-4">
            © {{ date('Y') }} Human Resource Department – All Rights Reserved
        </div>
    </div>

</body>
</html>
