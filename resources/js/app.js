import './bootstrap';

 // Simple interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            // Menu item click handler
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => {
                item.addEventListener('click', function() {
                    menuItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // Notification click handler
            const notification = document.querySelector('.notification');
            notification.addEventListener('click', function() {
                alert('You have 3 new notifications');
            });

            // User profile dropdown (simplified)
            const userProfile = document.querySelector('.user-profile');
            userProfile.addEventListener('click', function() {
                alert('User profile menu would open here');
            });
        });

        //employee


  let scanCount = 0;

document.getElementById('scanFingerprint').addEventListener('click', async () => {
    if (scanCount >= 3) {
        alert("All 3 scans are already captured.");
        return;
    }

    document.getElementById('fingerprintStatus').textContent =
        `Scanning fingerprint (${scanCount + 1} of 3)...`;

    try {
        // Replace with your actual scanner SDK/API/WebUSB/WebSocket call
        const imageBase64 = await getFingerprintImageFromScanner();

        // Draw image on correct preview slot
        const canvas = document.getElementById(`fingerprintPreview${scanCount + 1}`);
        const ctx = canvas.getContext('2d');
        const img = new Image();
        img.onload = () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        };
        img.src = `data:image/png;base64,${imageBase64}`;

        // Store image data in corresponding hidden input
        document.getElementById(`fingerprintScan${scanCount + 1}`).value = imageBase64;

        scanCount++;

        // Update status
        if (scanCount < 3) {
            document.getElementById('fingerprintStatus').textContent =
                `Scan successful ✅ — Please scan again (${scanCount + 1} of 3)`;
        } else {
            document.getElementById('fingerprintStatus').textContent = "All 3 scans complete ✅";
            document.getElementById('scanFingerprint').disabled = true;
            document.getElementById('scanFingerprint').classList.add('opacity-50', 'cursor-not-allowed');
        }

    } catch (err) {
        console.error(err);
        document.getElementById('fingerprintStatus').textContent = "Error scanning fingerprint ❌";
    }
});

// Mock function for demo — replace with actual device API call
async function getFingerprintImageFromScanner() {
    return new Promise(resolve => {
        setTimeout(() => {
            // Dummy base64 image — replace with actual scanned fingerprint image
            resolve("iVBORw0KGgoAAAANSUhEUgAAAAEAAAAB..."); 
        }, 1000);
    });
}


//payroll


    function openPayrollModal(employeeId) {
        document.getElementById('modal_employee_id').value = employeeId;

        // Fetch attendance and prefill fields via AJAX
        fetch(`/payroll-data/${employeeId}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('total_days').value = data.total_days_of_work;
                document.getElementById('pay_period').value = data.pay_period;
                document.getElementById('basic_salary').value = data.basic_salary;
                document.getElementById('ot_hours').value = 0;
                document.getElementById('overtime_pay').value = 0;
                document.getElementById('deductions').value = 0;
                document.getElementById('net_pay').value = data.basic_salary;
            });

        document.getElementById('payrollModal').classList.remove('hidden');
    }

    function closePayrollModal() {
        document.getElementById('payrollModal').classList.add('hidden');
    }

    // Live calculation
    document.getElementById('ot_hours').addEventListener('input', function() {
        let hours = parseFloat(this.value) || 0;
        let overtimePay = hours * 200;
        document.getElementById('overtime_pay').value = overtimePay;

        let basic = parseFloat(document.getElementById('basic_salary').value) || 0;
        let deductions = parseFloat(document.getElementById('deductions').value) || 0;
        document.getElementById('net_pay').value = (basic + overtimePay) - deductions;
    });

    document.getElementById('deductions').addEventListener('input', function() {
        let overtimePay = parseFloat(document.getElementById('overtime_pay').value) || 0;
        let basic = parseFloat(document.getElementById('basic_salary').value) || 0;
        let deductions = parseFloat(this.value) || 0;
        document.getElementById('net_pay').value = (basic + overtimePay) - deductions;
    });