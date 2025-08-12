<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Number Plate Challan</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background-color: #f4f7fa;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .email-wrapper {
            max-width: 700px;
            margin: auto;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #2a9d8f, #264653);
            padding: 20px;
            color: white;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
        }
        .content {
            padding: 25px;
        }
        .plate-info {
            background: #f8fafc;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
        }
        .plate-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .plate-info th {
            text-align: left;
            padding: 8px;
            background: #e9ecef;
            border-radius: 4px;
        }
        .plate-info td {
            padding: 8px;
        }
        .cta {
            display: inline-block;
            padding: 12px 20px;
            background: #2a9d8f;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin-top: 15px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            padding: 15px;
            background: #f1f5f9;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="header">
            <h1>Number Plate Challan</h1>
            <p>Due Date: <strong>{{ \Carbon\Carbon::parse($dueDate)->format('d M Y') }}</strong></p>
        </div>
        <div class="content">
            <p>Dear {{ $user->name }},</p>
            <p>Thank you for registering your number plate. Please find the details below. Link is given to download challan .</p>
     <a href="{{ asset('challans/challan_' . $plate->id . '.pdf') }}" 
                   style="background: #27ae60; color: white; padding: 12px 20px; text-decoration: none; border-radius: 6px; font-weight: bold;" download>
                    📄 Download Challan
                </a>
            <div class="plate-info">
                <table>
                    <tr>
                        <th>Plate Number</th>
                        <td>{{ $plate->plate_number }}</td>
                    </tr>
                    <tr>
                        <th>Province</th>
                        <td>{{ $plate->region }}</td>
                    </tr>
                    <tr>
                        <th>City</th>
                        <td>{{ $plate->city }}</td>
                    </tr>
                    <tr>
                        <th>Price</th>
                        <td>Rs {{ number_format($plate->price, 2) }}</td>
                    </tr>
                </table>
            </div>
            <p>Your Plate Look Like Below</p>
              @include("emails.plates",["plate"=>$plate,"provinceLogo"=>$provinceLogo]);
            {{-- <a href="#" class="cta">Download PDF Challan</a> --}}
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{config('app.name')}}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
