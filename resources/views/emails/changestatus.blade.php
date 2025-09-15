<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Application Status Update</title>
</head>
<body>
    <h2>Hello {{ $applicant->first_name }} {{ $applicant->last_name }},</h2>

    <p>Your application status has been updated to: 
       <strong>{{ $applicant->applicant_status }}</strong>.</p>

    <p>Thank you for applying. We’ll keep you updated on the next steps.</p>

    <br>
    <p>Regards,</p>
    <p>- 3RS Airconditioning HR Management</p>
</body>
</html>
