<x-guest-layout>
    <div class="min-h-screen bg-gray-100 p-6 text-gray-900">
        <div class="bg-white shadow-xl rounded-xl p-10 max-w-5xl mx-auto border border-gray-200">

            <!-- Start Form -->
            <form action="" method="POST">
                @csrf

                <!-- Header -->
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <!-- Logo Placeholder -->
                        <!-- Logo -->
                        <div class="h-16 w-48 flex items-center">
                            <img src="{{ asset('storage/company-logo.png') }}" alt="Company Logo"
                                class="h-full w-auto object-contain">
                        </div>

                        <div class="mt-4 space-y-2 text-sm text-gray-700">
                            <p>Contact Number:
                                <input type="text" name="contact_number"
                                    class="focus:outline-none border-b border-gray-300 ml-1" />
                            </p>
                            <p>Email:
                                <input type="email" name="email"
                                    class="focus:outline-none border-b border-gray-300 ml-1" />
                            </p>
                        </div>
                    </div>
                    <div class="text-right text-sm text-gray-700">
                        <p>Date:
                            <input type="date" name="date"
                                class="focus:outline-none border-b border-gray-300 ml-1" />
                        </p>
                    </div>
                </div>

                <!-- Client Info -->
                <div class="mb-8 text-sm space-y-3 text-gray-700">
                    <p><strong>Client Name:</strong>
                        <input type="text" name="client_name"
                            class="focus:outline-none border-b border-gray-300 ml-2 w-2/3" />
                    </p>
                    <p><strong>Attention:</strong>
                        <input type="text" name="attention"
                            class="focus:outline-none border-b border-gray-300 ml-2 w-2/3" />
                    </p>
                    <p><strong>Subject:</strong>
                        <input type="text" name="subject"
                            class="focus:outline-none border-b border-gray-300 ml-2 w-2/3" />
                    </p>
                </div>

                <!-- Intro -->
                <p class="mb-8 text-md leading-relaxed text-black">
                    Dear Sir/Madam: Please find herewith our proposal of the above subject for the following terms and
                    conditions for your consideration.
                </p>
                <!-- Table -->
                <div class="overflow-x-auto mb-8">
                    <table id="dynamicTable" class="w-full border border-gray-400 text-sm">
                        <thead>
                            <tr class="bg-gray-200 text-gray-800 font-semibold">
                                <th class="border px-3 py-2">ITEM</th>
                                <th class="border px-3 py-2">DESCRIPTION</th>
                                <th class="border px-3 py-2">QTY</th>
                                <th class="border px-3 py-2">UNIT PRICE</th>
                                <th class="border px-3 py-2">TOTAL AMOUNT</th>
                                <th class="border px-3 py-2">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border px-3 py-2 text-center">
                                    <input type="text" name="item"
                                        class="w-full text-center focus:outline-none" />
                                </td>
                                <td class="border px-3 py-2">
                                    <textarea name="description" rows="2" class="w-full resize-none focus:outline-none"></textarea>
                                </td>
                                <td class="border px-3 py-2 text-center">
                                    <input type="number" name="qty"
                                        class="w-full text-center focus:outline-none" />
                                </td>
                                <td class="border px-3 py-2 text-right">
                                    <input type="number" name="unit_price"
                                        class="w-full text-right focus:outline-none" />
                                </td>
                                <td class="border px-3 py-2 text-right">
                                    <input type="number" name="total_amount"
                                        class="w-full text-right focus:outline-none" />
                                </td>
                                <td class="border px-3 py-2 text-center">
                                    <button type="button" onclick="deleteRow(this)"
                                        class="text-red-600 hover:underline">Delete</button>
                                </td>
                            </tr>
                            <tr class="bg-gray-100 font-bold">
                                <td colspan="4" class="border px-3 py-2 text-right">OVERALL TOTAL AMOUNT</td>
                                <td class="border px-3 py-2 text-right">
                                    <input type="number" name="grand_total"
                                        class="w-full text-right focus:outline-none font-bold" />
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Action Buttons -->
                    <div class="mt-3 flex gap-3">
                        <button type="button" onclick="addRow()"
                            class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-4 py-1 rounded shadow hover:from-orange-600 hover:to-red-600">
                            Add Row
                        </button>

                        <button type="button" onclick="mergeCells()"
                            class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-4 py-1 rounded shadow hover:from-orange-600 hover:to-red-600">
                            Merge Selected Cells
                        </button>
                    </div>
                    <br>
                    <br>

                    <!-- Remarks -->
                    <div class="mb-8 text-sm text-gray-700">
                        <p class="text-lg font-serif">Remarks:</p>
                    </div>
                    <div class="mb-6 text-sm text-gray-700 space-y-2">
                        <div><span class="font-semibold">Terms of Payment on Equipment:</span> <input type="text"
                                class="focus:outline-none ml-2" /></div>
                        <div><span class="font-semibold">Terms of Payment on Installation:</span> <input type="text"
                                class="focus:outline-none ml-2" /></div>
                        <div><span class="font-semibold">Warranty:</span> <input type="text"
                                class="focus:outline-none ml-2" /></div>
                        <div><span class="font-semibold">Payable to:</span> <input type="text"
                                class="focus:outline-none ml-2 text-red-600 font-semibold" /></div>
                    </div>

                    <!-- Guarantee -->
                     <div class="mb-8 text-sm text-gray-700">
                        <p class="text-md">EXEPTION: Feeder line and circuit breakers, Painting and civils works, Mechanical and as built plans</p>
                    </div>
                    <div class="mb-8 text-sm text-gray-700">
                        <p class="font-bold">Guarantee: <span class="font-semibold"> We guarantee the equipment supplied by us against
                            mechanical defect and
                            Within a period of one (1) year from the date of completion installation. All defective
                            Parts found within this period shall be replaced or repaired by us free of charge with the
                            Exception of the expandable items: Air filter and charge y-belt which are guaranteed by us
                            For (90) days from completion date, defect and burning out of motors due to negligence of
                            Your operation or due to abnormal voltage fluctuations, for those damaged units caused by
                            fire, lightning, and other natural disaster and riots are not included in this guarantee.</span>
                        </p>
                    </div>

                    <!-- Closing -->
                    <p class="text-sm mb-10 leading-relaxed text-gray-700">
                        We hope that you find our offer in order and we look forward to be of service to you.
                    </p>

                    <!-- Proprietor & Conformity Section -->
                    <div class="flex justify-between items-start mt-12">
                        <!-- Proprietor -->
                        <div class="text-left">
                            <p class="font-bold underline relative inline-block">
                                Adrian N. Genobia
                                <img src="{{ asset('storage/Esig.png') }}" alt="Proprietor Signature"
                                    class="absolute left-6 top-[-35px] h-16 w-auto opacity-90">
                            </p>
                            <p class="text-sm text-gray-600 mt-3 ml-4">Proprietor</p>
                        </div>

                        <!-- Conformed By -->
                        <div class="text-right">
                            <p class="font-semibold mr-10">Conformed by:</p>
                            <input type="text"
                                class="text-center text-gray-600 mt-2 w-48 focus:outline-none border-b border-gray-400 block ml-auto"
                                placeholder="Name & Date" />
                        </div>
                    </div>

                    <!-- Submit button -->
                    <div class="mt-12 flex justify-center">
                        <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white rounded-lg shadow-lg hover:scale-105 transform transition">
                            Submit Quotation
                        </button>
                    </div>
            </form>
            <!-- End Form -->

        </div>
    </div>

    <script>
        function addRow() {
            const table = document.getElementById("dynamicTable").getElementsByTagName('tbody')[0];
            const newRow = table.insertRow(table.rows.length - 1); // insert before the total row

            newRow.innerHTML = `
        <td class="border px-3 py-2 text-center">
            <input type="text" name="item" class="w-full text-center focus:outline-none" />
        </td>
        <td class="border px-3 py-2">
            <textarea name="description" rows="2" class="w-full resize-none focus:outline-none"></textarea>
        </td>
        <td class="border px-3 py-2 text-center">
            <input type="number" name="qty" class="w-full text-center focus:outline-none" />
        </td>
        <td class="border px-3 py-2 text-right">
            <input type="number" name="unit_price" class="w-full text-right focus:outline-none" />
        </td>
        <td class="border px-3 py-2 text-right">
            <input type="number" name="total_amount" class="w-full text-right focus:outline-none" />
        </td>
        <td class="border px-3 py-2 text-center">
            <button type="button" onclick="deleteRow(this)" class="text-red-600 hover:underline">Delete</button>
        </td>
    `;
        }

        function deleteRow(button) {
            const row = button.closest("tr");
            const table = document.getElementById("dynamicTable");
            const totalRow = table.rows[table.rows.length - 1];
            if (row !== totalRow) {
                row.remove();
            } else {
                alert("You cannot delete the total row.");
            }
        }

        function mergeCells() {
            const table = document.getElementById("dynamicTable");
            const selection = window.getSelection();
            if (!selection.rangeCount) return;

            const range = selection.getRangeAt(0);
            const startCell = range.startContainer.closest("td,th");
            const endCell = range.endContainer.closest("td,th");

            if (startCell && endCell && startCell.parentNode === endCell.parentNode) {
                let startIndex = startCell.cellIndex;
                let endIndex = endCell.cellIndex;

                if (startIndex > endIndex)[startIndex, endIndex] = [endIndex, startIndex];

                startCell.colSpan = endIndex - startIndex + 1;

                for (let i = startIndex + 1; i <= endIndex; i++) {
                    startCell.parentNode.deleteCell(startIndex + 1);
                }
            } else {
                alert("Please select cells in the same row to merge.");
            }
        }
    </script>
</x-guest-layout>
