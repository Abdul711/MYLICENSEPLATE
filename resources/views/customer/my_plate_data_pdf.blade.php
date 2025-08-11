<!-- resources/views/plates/pdf.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>License Plates</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">License Plates List</h2>
    <table>
        <thead>
            <tr>
                <th>Province</th>
                <th>City</th>
                <th>Plate Number</th>
                   <th>Price</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        @foreach($plates as $plate)
            <tr>
                <td>{{ $plate->region }}</td>
                <td>{{ $plate->city }}</td>
                <td>{{ $plate->plate_number }}</td>
                    <td>{{ $plate->price }}</td>
                <td>{{ ucfirst($plate->status) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
