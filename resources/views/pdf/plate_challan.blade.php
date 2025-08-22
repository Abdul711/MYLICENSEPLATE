<!DOCTYPE html>
<html>

<head>
    <title>Challan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h2, h3, h4 {
            margin: 5px 0;
        }

        .copies-container {
            display: flex;
            flex-wrap: wrap; /* wrap columns if screen too small */
            justify-content: space-between;
        }

        .column {
            flex: 0 0 32%; /* 3 columns, adjust to 32% for spacing */
            box-sizing: border-box;
            margin-bottom: 10px;
            page-break-inside: avoid; /* prevent breaking PDF page */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* prevent table expanding */
            word-wrap: break-word;
        }

        td, th {
            border: 1px solid black;
            padding: 5px;
            text-align: center;
            font-size: 12px;
        }

        .logo {
            width: 80px;
            margin-top: 10px;
        }

        .banks-table {
            max-height: 150px; /* adjust as needed */
            overflow-y: auto; /* scroll if too many banks */
            display: block;
        }
    </style>
</head>

<body>
    <h2 style="text-align: center;">License Plate Payment Challan</h2>
    <h3 style="text-align: center;">Government Of {{ Str::ucfirst($plate->region) }}</h3>
    <div style="text-align: center;">
        <img src="{{ $provinceLogo }}" class="logo">
    </div>
    <h3 style="text-align: center;">Due Date: {{ $dueDate }}</h3>
    <h3 style="text-align: center;">Creator Name: {{ $user->name }}</h3>
    <h3 style="text-align: center;">Creator Email: {{ $user->email }}</h3>
    <h3 style="text-align: center;">Creator Number: {{ $user->mobile }}</h3>
    <h3 style="text-align: center;">Payment Method: {{ $paymentMethod }}</h3>
    <h3 style="text-align: center;">Invoice Number: {{ $invoiceNumber }}</h3>
    <h3 style="text-align: right;">
        Invoice Creation Date: <strong>{{ \Carbon\Carbon::now()->format('d M, Y') }}</strong>
    </h3>
    <h3 style="text-align: left;">
        Invoice Creation Time: <strong>{{ \Carbon\Carbon::now()->format('h:i A') }}</strong>
    </h3>

    <div class="copies-container">
        @foreach (['Bank Copy', 'Government Copy', 'User Copy'] as $copyType)
            <div class="column">
                <h3 style="text-align: center;">{{ $copyType }}</h3>
                <table>
                    <tr>
                        <td>Plate Number</td>
                        <td>{{ $plate->plate_number }}</td>
                    </tr>
                    <tr>
                        <td>Province</td>
                        <td>{{ $plate->region }}</td>
                    </tr>
                    <tr>
                        <td>City</td>
                        <td>{{ $plate->city }}</td>
                    </tr>
                    <tr>
                        <td>Price</td>
                        <td>{{ number_format($plate->price) }}</td>
                    </tr>
                    <tr>
                        <td>Due Date</td>
                        <td>{{ $dueDate }}</td>
                    </tr>
                    <tr>
                        <td>Penalty</td>
                        <td>{{ $LatePaymentPenalty }}</td>
                    </tr>
                    <tr>
                        <td>After Due Date</td>
                        <td>{{ number_format($plate->price + $LatePaymentPenalty) }}</td>
                    </tr>
                </table>

                <h4 style="text-align: center;">Banks List</h4>
                <div class="banks-table">
                    <table>
                        @foreach ($banks as $bank)
                            <tr>
                                <td>{{ $bank['name'] }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        @endforeach
    </div>
</body>

</html>
