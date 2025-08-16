<!DOCTYPE html>
<html>
<head>
    <title>License Plates Export</title>
    <style>
        table { width:100%; border-collapse: collapse; font-size:12px; }
        th, td { border:1px solid #000; padding:5px; text-align:center; }
        th { background:#f2f2f2; }
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
            <td>{{ $plate['Province'] }}</td>
            <td>{{ $plate['City'] }}</td>
            <td>{{ $plate['Plate Number'] }}</td>
            <td>{{ $plate['Price'] }}</td>
            <td>{{ $plate['Status'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
