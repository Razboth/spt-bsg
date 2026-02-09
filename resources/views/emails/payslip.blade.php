<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body>

<div style="max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
    <h2 style="border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 20px;">Slip Gaji</h2>

    <p>Yth. {{ $employee->name }},</p>

    <p>Bersama ini kami sampaikan slip gaji Anda untuk periode {{ numToMonth($month) }} {{ $year }}. Kami harap Anda dapat memeriksa dan memastikan detail yang tertera sudah sesuai.</p>

    <p>Apabila Anda memiliki pertanyaan atau menemukan ketidaksesuaian, harap segera menghubungi bagian HRD.</p>

    <p>Kami menghargai kontribusi dan dedikasi Anda untuk perusahaan. Teruslah memberikan yang terbaik dalam pekerjaan Anda.</p>

    <br>

    <p>Terima kasih,</p>
    <p>Bagian HRD<br>
        {{ $company->name }}</p>
</div>

</body>
</html>
