<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Unpaid Challan Summary</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin:0; padding:0;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td
                            style="background-color: #4CAF50; padding: 20px; color: #ffffff; text-align: center; font-size: 24px;">
                            Unpaid License Plate Summary
                        </td>
                    </tr>

                    <!-- Greeting -->
                    <tr>
                        <td style="padding: 20px; font-size: 16px; color: #333333;">
                            <p>Hello, <strong>{{ $user->name }}</strong>,</p>
                            <p>You have the following unpaid license plate challans:</p>
                        </td>
                    </tr>

                    <!-- Table -->
                    <tr>
                        <td style="padding: 0 20px 20px 20px;">
                            <table width="100%" cellpadding="10" cellspacing="0"
                                style="border-collapse: collapse; font-size: 14px;">
                                <thead>
                                    <tr>
                                         <th
                                            style="background-color: #f0f0f0; border-bottom: 2px solid #ddd; text-align: left;">
                                            Plate Id</th>
                                        <th
                                            style="background-color: #f0f0f0; border-bottom: 2px solid #ddd; text-align: left;">
                                            Plate Number</th>
                                              <th
                                            style="background-color: #f0f0f0; border-bottom: 2px solid #ddd; text-align: left;">
                                            Province </th>
                                               <th
                                            style="background-color: #f0f0f0; border-bottom: 2px solid #ddd; text-align: left;">
                                            City </th>
                                        <th
                                            style="background-color: #f0f0f0; border-bottom: 2px solid #ddd; text-align: left;">
                                            Due Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($plates as $plate)

                                        <tr>
                                            <td style="border-bottom: 1px solid #ddd;">{{ $plate->id }}</td>
                                            <td style="border-bottom: 1px solid #ddd;">{{ $plate->plate_number }}</td>
                                            <td style="border-bottom: 1px solid #ddd;">{{ $plate->region }}</td>
                                            <td style="border-bottom: 1px solid #ddd;">{{ $plate->city }}</td>
                                            <td style="border-bottom: 1px solid #ddd;">
                                                {{ date('d-F-Y', strtotime($plate->created_at . ' +2 months')) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>
 {{-- users:unpaid-challan  --}}
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px; font-size: 14px; color: #555555;">
                            <p>Please pay your challans before the due date to avoid penalties.</p>
                            <p style="margin-top: 10px;">Thank you,<br>{{config('app.name')}}</p>
                        </td>
                    </tr>

                    <tr>
                        <td
                            style="background-color: #f0f0f0; text-align: center; padding: 10px; font-size: 12px; color: #777777;">
                            &copy; {{ date('Y') }} {{ config('app.name') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

{{-- users:unpaid-challan  --}}

</html>
