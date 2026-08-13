<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Print Tugas</title>
    <style>
        @page {
            size: 80mm 40mm;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: 80mm;
            height: 40mm;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
        }

        .label {
            width: 80mm;
            height: 40mm;
            padding: 2mm;
            overflow: hidden;
            break-after: page;
            page-break-after: always;
        }

        .label:last-child {
            break-after: auto;
            page-break-after: auto;
        }

        table {
            width: 100%;
            height: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 0.35mm solid #000;
            padding: 1.2mm;
            text-align: center;
            vertical-align: middle;
            overflow-wrap: anywhere;
        }

        th {
            height: 6mm;
            font-size: 8pt;
            line-height: 1.1;
        }

        .value-row td {
            height: 8mm;
            font-size: 10pt;
            font-weight: bold;
            line-height: 1.1;
        }

        .note-row td {
            height: 20mm;
            padding: 1.5mm;
            text-align: left;
            vertical-align: top;
            font-size: 9pt;
            line-height: 1.2;
        }

        @media screen {
            body {
                background: #eee;
            }

            .label {
                background: #fff;
            }
        }
    </style>
</head>
<body>
    @foreach ($labels as $label)
        <div class="label">
            <table>
                <colgroup>
                    <col style="width: 32%;">
                    <col style="width: 48%;">
                    <col style="width: 20%;">
                </colgroup>
                <thead>
                    <tr>
                        <th>Nomor Bon</th>
                        <th>Kode Bon Item</th>
                        <th>Urutan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="value-row">
                        <td>{{ $label['item']->order->number_ticket }}</td>
                        <td>{{ $label['itemCode'] }}</td>
                        <td>{{ $label['sequence'] }}</td>
                    </tr>
                    <tr class="note-row">
                        <td colspan="3"><strong>Keterangan:</strong><br>{{ $label['item']->note ?: '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endforeach

    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
</body>
</html>
