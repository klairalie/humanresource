<x-guest-layout>
    <div class="min-h-screen text-black p-8 mb-20">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold flex items-center space-x-2">
                <i data-lucide="calendar-days" class="w-7 h-7 text-black"></i>
                <span>Holiday Calendar</span>
            </h1>

            <!-- Update Holidays Button -->
            <button id="updateHolidaysBtn"
                class="flex items-center space-x-2 bg-white text-black px-4 py-2 rounded-md hover:bg-gray-200 transition duration-200 font-medium shadow-sm">
                <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                <span>Update Holidays</span>
            </button>
        </div>

        <!-- 🗓️ Legend Section -->
        <div class="flex items-center space-x-8 mb-6 text-sm">
            <div class="flex items-center space-x-2">
                <span class="w-4 h-4 bg-blue-500 inline-block rounded-sm border border-white"></span>
                <span>Regular Holiday</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-4 h-4 bg-orange-500 inline-block rounded-sm border border-white"></span>
                <span>Special Non-Working Holiday</span>
            </div>
        </div>

        <!-- Calendar Container -->
        <div id="calendar" class="bg-white text-black p-4 rounded-md shadow-lg border border-gray-300"></div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- ✅ FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

    <!-- ✅ Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            lucide.createIcons(); // initialize icons

            const calendarEl = document.getElementById('calendar');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                height: 'auto',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,listYear'
                },
                events: @json($events),
                dayMaxEventRows: true,
                eventDisplay: 'block',
                eventTextColor: 'black',
            });

            calendar.render();
        });

        // 🔄 Update Holidays Button Logic
        document.getElementById('updateHolidaysBtn').addEventListener('click', function () {
            Swal.fire({
                title: 'Update Holidays?',
                text: "This will refresh or update all holidays for the current year.",
                icon: 'warning',
                background: '#111',
                color: '#fff',
                showCancelButton: true,
                confirmButtonColor: '#fff',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Updating...',
                        text: 'Please wait while holidays are refreshed.',
                        background: '#111',
                        color: '#fff',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    fetch('{{ route('holidays.update') }}')
                        .then(response => response.json())
                        .then(data => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: data.message,
                                background: '#111',
                                color: '#fff',
                                confirmButtonColor: '#fff'
                            }).then(() => location.reload());
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong while updating holidays.',
                                background: '#111',
                                color: '#fff',
                            });
                        });
                }
            });
        });
    </script>
</x-guest-layout>
