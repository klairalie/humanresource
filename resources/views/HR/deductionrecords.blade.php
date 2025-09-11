<x-guest-layout>
    <div class="container mx-auto p-4 rounded-2xl bg-gradient-to-b from-orange-100 via-white to-gray-200">
        <h1 class="text-2xl font-bold mb-4">Deduction Records</h1>

        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-2 px-4 border-b">Employee Name</th>
                    <th class="py-2 px-4 border-b">Deduction Type</th>
                    <th class="py-2 px-4 border-b">Partial Payment</th>
                    <th class="py-2 px-4 border-b">Partial Payment Date</th>
                    <th class="py-2 px-4 border-b">Amount</th>
                    <th class="py-2 px-4 border-b">Deduction_Date</th>
                    <th class="py-2 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($deductions as $deduction)
                    <tr>
                        <td class="py-2 px-4 border-b text-center">
                            {{ $deduction->employeeprofiles?->first_name }}
                        </td>
                        <td class="py-2 px-4 border-b text-center">
                            {{ $deduction->deduction_type }}
                        </td>

                        <!-- ✅ Inline Update Form for Partial Payment -->
                        <td class="py-2 px-4 border-b text-center">
                            <form action="{{ route('update.deduction', ['deduction_id' => $deduction->deduction_id]) }}" method="POST" class="flex items-center justify-center space-x-2">
                                @csrf
                                @method('PUT')

                                <input type="number" name="partial_payment" value="{{ $deduction->partial_payment }}" 
                                    class="w-24 px-2 py-1 border rounded-md text-center" min="0" step="0.01" required>

                                <button type="submit" class="px-3 py-1 bg-blue-500 text-white text-sm rounded-md hover:bg-blue-600">
                                    Update
                                </button>
                            </form>
                        </td>
                        <td class="py-2 px-4 border-b text-center">{{ $deduction->partialpay_date }}</td>
                        <td class="py-2 px-4 border-b text-center">{{ $deduction->amount }}</td>
                        <td class="py-2 px-4 border-b text-center">{{ $deduction->deduction_date }}</td>

                        <!-- Delete Button -->
                        <td class="py-2 px-4 border-b text-center">
                            <form action="{{ route('delete.deduction', ['deduction_id' => $deduction->deduction_id]) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $deductions->links() }}
        </div>
    </div>
</x-guest-layout>
