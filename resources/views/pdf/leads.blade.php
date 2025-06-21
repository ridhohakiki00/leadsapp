<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $report_name }}</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
    </style>
</head>

<body>
    <h3>{{ $report_name }}</h3>
    <table>
        <thead>
            <tr>
                @foreach ($fields as $field)
                    <th>{{ ucfirst(str_replace('_', ' ', $field)) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    @foreach ($fields as $field)
                        <td>{{ $row[$field] }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
