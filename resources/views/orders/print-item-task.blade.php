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
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
        }

        .print-controls {
            display: none;
        }

        .print-preview-modal {
            display: none;
        }

        .label {
            position: relative;
            width: 80mm;
            height: 40mm;
            padding: 2mm;
            overflow: hidden;
            break-inside: avoid;
            page-break-inside: avoid;
            break-after: page;
            page-break-after: always;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
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

        thead th:nth-child(1),
        thead th:nth-child(2),
        .value-row td:nth-child(1),
        .value-row td:nth-child(2) {
            padding: 0.5mm;
        }

        .value-row {
            height: 5mm;
        }

        .value-row td {
            height: 5mm;
            padding-top: 0.4mm;
            padding-bottom: 0.4mm;
            font-size: 7pt !important;
            font-weight: bold;
            line-height: 1.1;
        }

        .note-row td {
            height: auto;
            padding: 1.5mm;
            text-align: left;
            vertical-align: top;
            font-size: 9pt;
            line-height: 1.2;
        }

        .estimated-done {
            position: absolute;
            right: 3mm;
            bottom: 2.5mm;
            padding-left: 0.7mm;
            background: #fff;
            font-size: 7pt;
            font-weight: normal;
            line-height: 1;
        }

        @media screen {
            html,
            body {
                width: 100%;
                height: auto;
                padding: 14px;
                background: #eee;
            }

            .print-controls {
                display: block;
                width: min(100%, 80mm);
                margin: 0 auto 14px;
                padding: 12px;
                border-radius: 7px;
                background: #fff;
                box-shadow: 0 2px 10px rgba(0, 0, 0, .12);
                font-size: 12px;
                line-height: 1.45;
            }

            .print-controls button,
            .print-controls a,
            .print-controls select,
            .print-preview-actions button {
                display: block;
                width: 100%;
                margin-top: 8px;
                padding: 11px;
                border: 0;
                border-radius: 5px;
                text-align: center;
                text-decoration: none;
                font-weight: bold;
                cursor: pointer;
            }

            .print-controls select {
                padding: 9px;
                border: 1px solid #c7cccf;
                background: #fff;
                font-weight: normal;
            }

            .print-status {
                display: block;
                margin-top: 8px;
                color: #555;
            }

            .print-preview-modal.is-open {
                position: fixed;
                z-index: 9999;
                inset: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 16px;
                background: rgba(0, 0, 0, .65);
            }

            .print-preview-dialog {
                width: min(100%, 430px);
                max-height: calc(100vh - 32px);
                padding: 16px;
                overflow-y: auto;
                border-radius: 8px;
                background: #eee;
                box-shadow: 0 4px 24px rgba(0, 0, 0, .3);
            }

            .print-preview-dialog h3 {
                margin: 0 0 4px;
                font-size: 16px;
            }

            .print-preview-dialog p {
                margin: 0 0 12px;
                font-size: 12px;
            }

            .print-preview-paper {
                display: flex;
                width: 100%;
                aspect-ratio: 2 / 1;
                align-items: center;
                justify-content: center;
                margin-bottom: 10px;
                overflow: hidden;
                background: #fff;
                box-shadow: 0 1px 5px rgba(0, 0, 0, .18);
            }

            .print-preview-paper canvas {
                width: 90%;
                height: 100%;
            }

            .print-preview-actions {
                position: sticky;
                bottom: -16px;
                display: flex;
                gap: 8px;
                margin: 6px -16px -16px;
                padding: 12px 16px 16px;
                background: #eee;
            }

            .print-preview-actions button {
                margin-top: 0;
            }

            .btn-bluetooth {
                color: #fff;
                background: #00796b;
            }

            .btn-default {
                color: #222;
                background: #e4e7e9;
            }

            .label {
                margin: 0 auto 12px;
                background: #fff;
                box-shadow: 0 2px 10px rgba(0, 0, 0, .12);
            }
        }

        @media print {
            html,
            body {
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
            }

            .print-controls,
            .print-preview-modal {
                display: none !important;
            }

            .label {
                width: 100vw;
                height: 100vh;
                margin: 0;
                padding: 1.5mm;
            }

            th {
                font-size: 7pt;
            }

            .value-row td {
                font-size: 7pt !important;
            }

            .note-row td {
                font-size: 11pt;
            }
        }
    </style>
</head>
<body>
    <div class="print-controls">
        <strong>Print Tugas — SM802</strong><br>
        Gunakan Chrome atau Edge, lalu pilih RPP02N/SM802 saat diminta.
        <button id="web-bluetooth-print-button" type="button" class="btn-bluetooth" onclick="openSm802Preview()">Preview &amp; Cetak SM802</button>
        <small id="web-bluetooth-status" class="print-status">Printer belum terhubung.</small>
        <button type="button" class="btn-default" onclick="window.print()">Cetak biasa</button>
    </div>

    <div id="sm802-preview-modal" class="print-preview-modal" role="dialog" aria-modal="true" aria-labelledby="sm802-preview-title">
        <div class="print-preview-dialog">
            <h3 id="sm802-preview-title">Preview Cetak SM802</h3>
            <p>Ukuran kertas 80 × 40 mm. Periksa semua tugas sebelum mencetak.</p>
            <div id="sm802-preview-content"></div>
            <div class="print-preview-actions">
                <button type="button" class="btn-default" onclick="closeSm802Preview()">Batal</button>
                <button type="button" class="btn-bluetooth" onclick="confirmSm802Print()">Cetak Sekarang</button>
            </div>
        </div>
    </div>

    @foreach ($labels as $label)
        <div class="label">
            <table>
                <colgroup>
                    <col style="width: 30%;">
                    <col style="width: 40%;">
                    <col style="width: 30%;">
                </colgroup>
                <thead>
                    <tr>
                        <th>Nomor Bon</th>
                        <th>Kode Bon Item</th>
                        <th>Penerima</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="value-row">
                        <td>{{ $label['item']->order->number_ticket }}</td>
                        <td>{{ $label['itemCode'] }}</td>
                        <td>{{ $label['receiver'] }}</td>
                    </tr>
                    <tr class="note-row">
                        <td colspan="3"><strong>Keterangan:</strong><br>{{ $label['item']->note ?: '-' }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="estimated-done">Est. selesai: {{ $label['estimatedDone'] }}</div>
        </div>
    @endforeach

    @php
        $taskLabelPayload = $labels->map(function ($label) {
            return [
                'ticket' => (string) $label['item']->order->number_ticket,
                'itemCode' => (string) $label['itemCode'],
                'receiver' => (string) $label['receiver'],
                'note' => (string) ($label['item']->note ?: '-'),
                'estimatedDone' => (string) $label['estimatedDone'],
            ];
        })->values()->all();
    @endphp

    <script>
        const taskLabels = @json($taskLabelPayload);
        const sm802ServiceUuids = [
            '0000fee7-0000-1000-8000-00805f9b34fb',
            '0000ff00-0000-1000-8000-00805f9b34fb',
            'e7810a71-73ae-499d-8c15-faa9aef0c3f2',
            '49535343-fe7d-4ae5-8fa9-9fafd205e455'
        ];
        const sm802PreferredChannels = [
            {
                service: '49535343-fe7d-4ae5-8fa9-9fafd205e455',
                characteristics: [
                    '49535343-8841-43f4-a8d4-ecbe34729bb3',
                    '49535343-1e4d-4bd9-ba61-23c647249616'
                ]
            },
            {
                service: '0000ff00-0000-1000-8000-00805f9b34fb',
                characteristics: [
                    '0000ff02-0000-1000-8000-00805f9b34fb',
                    '0000ff01-0000-1000-8000-00805f9b34fb'
                ]
            },
            {
                service: 'e7810a71-73ae-499d-8c15-faa9aef0c3f2',
                characteristics: ['bef8d6c9-9c21-4c9e-b632-bd58c1009f9f']
            },
            {
                service: '0000fee7-0000-1000-8000-00805f9b34fb',
                characteristics: ['0000fec7-0000-1000-8000-00805f9b34fb']
            }
        ];
        let sm802Device = null;
        let sm802WriteCharacteristic = null;

        function printableText(value) {
            return String(value || '')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/\s+/g, ' ')
                .trim();
        }

        function fitFontSize(context, text, maxWidth, initialSize, minimumSize) {
            let size = initialSize;
            while (size > minimumSize) {
                context.font = 'bold ' + size + 'px Arial, sans-serif';
                if (context.measureText(text).width <= maxWidth) break;
                size--;
            }

            return size;
        }

        function drawCellText(context, text, left, right, top, bottom) {
            text = printableText(text);
            const size = fitFontSize(context, text, (right - left) - 14, 20, 13);
            context.font = 'bold ' + size + 'px Arial, sans-serif';
            context.textAlign = 'center';
            context.textBaseline = 'middle';
            context.fillText(text, (left + right) / 2, (top + bottom) / 2);
        }

        function wrapCanvasText(context, text, maxWidth) {
            const words = printableText(text).split(' ');
            const lines = [];
            let currentLine = '';

            words.forEach(function (word) {
                const candidate = currentLine ? currentLine + ' ' + word : word;
                if (context.measureText(candidate).width <= maxWidth) {
                    currentLine = candidate;
                    return;
                }

                if (currentLine) lines.push(currentLine);
                currentLine = word;
            });

            if (currentLine) lines.push(currentLine);
            return lines.length ? lines : ['-'];
        }

        function createTaskCanvas(label) {
            // 80 x 40 mm at 203 DPI. The SM802 printable width is 576 dots
            // (72 mm); the remaining 8 mm becomes the printer's side margins.
            const canvas = document.createElement('canvas');
            canvas.width = 576;
            canvas.height = 320;

            const context = canvas.getContext('2d', { willReadFrequently: true });
            context.fillStyle = '#fff';
            context.fillRect(0, 0, canvas.width, canvas.height);
            context.fillStyle = '#000';
            context.strokeStyle = '#000';
            context.lineWidth = 2;

            const top = 16;
            const bottom = 304;
            const headerBottom = 64;
            const valueBottom = 104;
            const firstColumn = 173;
            const secondColumn = 403;

            context.strokeRect(1, top, canvas.width - 2, bottom - top);
            context.beginPath();
            context.moveTo(0, headerBottom);
            context.lineTo(canvas.width, headerBottom);
            context.moveTo(0, valueBottom);
            context.lineTo(canvas.width, valueBottom);
            context.moveTo(firstColumn, top);
            context.lineTo(firstColumn, valueBottom);
            context.moveTo(secondColumn, top);
            context.lineTo(secondColumn, valueBottom);
            context.stroke();

            drawCellText(context, 'Nomor Bon', 0, firstColumn, top, headerBottom);
            drawCellText(context, 'Kode Bon Item', firstColumn, secondColumn, top, headerBottom);
            drawCellText(context, 'Penerima', secondColumn, canvas.width, top, headerBottom);
            drawCellText(context, label.ticket, 0, firstColumn, headerBottom, valueBottom);
            drawCellText(context, label.itemCode, firstColumn, secondColumn, headerBottom, valueBottom);
            drawCellText(context, label.receiver, secondColumn, canvas.width, headerBottom, valueBottom);

            context.textAlign = 'left';
            context.textBaseline = 'alphabetic';
            context.font = 'bold 22px Arial, sans-serif';
            context.fillText('Keterangan:', 10, 132);

            context.font = '22px Arial, sans-serif';
            const noteLines = wrapCanvasText(context, label.note, canvas.width - 20);
            const maximumNoteLines = 5;
            noteLines.slice(0, maximumNoteLines).forEach(function (line, index) {
                let output = line;
                if (index === maximumNoteLines - 1 && noteLines.length > maximumNoteLines) {
                    output = output.replace(/\s*$/, '') + '...';
                }
                context.fillText(output, 10, 158 + (index * 24));
            });

            context.font = '18px Arial, sans-serif';
            context.textAlign = 'right';
            context.fillText('Est. selesai: ' + printableText(label.estimatedDone), canvas.width - 9, 296);

            return canvas;
        }

        function openSm802Preview() {
            const modal = document.getElementById('sm802-preview-modal');
            const content = document.getElementById('sm802-preview-content');
            content.innerHTML = '';

            taskLabels.forEach(function (label) {
                const paper = document.createElement('div');
                paper.className = 'print-preview-paper';
                paper.appendChild(createTaskCanvas(label));
                content.appendChild(paper);
            });

            modal.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        }

        function closeSm802Preview() {
            document.getElementById('sm802-preview-modal').classList.remove('is-open');
            document.body.style.overflow = '';
        }

        function confirmSm802Print() {
            closeSm802Preview();
            printTasksWithWebBluetooth();
        }

        function appendEscPosRaster(bytes, canvas) {
            const context = canvas.getContext('2d', { willReadFrequently: true });
            const pixels = context.getImageData(0, 0, canvas.width, canvas.height).data;
            const widthBytes = Math.ceil(canvas.width / 8);

            // GS v 0: print raster bitmap in normal density.
            bytes.push(
                29, 118, 48, 0,
                widthBytes & 0xff,
                (widthBytes >> 8) & 0xff,
                canvas.height & 0xff,
                (canvas.height >> 8) & 0xff
            );

            for (let y = 0; y < canvas.height; y++) {
                for (let byteX = 0; byteX < widthBytes; byteX++) {
                    let value = 0;
                    for (let bit = 0; bit < 8; bit++) {
                        const x = (byteX * 8) + bit;
                        if (x >= canvas.width) continue;
                        const offset = ((y * canvas.width) + x) * 4;
                        const luminance = (pixels[offset] * 0.299)
                            + (pixels[offset + 1] * 0.587)
                            + (pixels[offset + 2] * 0.114);
                        if (luminance < 180) value |= (0x80 >> bit);
                    }
                    bytes.push(value);
                }
            }
        }

        function createTaskPrintBytes() {
            const bytes = [27, 64]; // ESC/POS initialize
            taskLabels.forEach(function (label) {
                appendEscPosRaster(bytes, createTaskCanvas(label));
            });

            // Dorong label terakhir sekitar 10 mm melewati print head agar bagian
            // bawah 80 x 40 mm tidak ikut terpotong saat kertas disobek.
            bytes.push(27, 74, 80); // ESC J 80: feed 80 dots at 203 DPI

            return bytes;
        }

        function setWebBluetoothStatus(message, isError) {
            const status = document.getElementById('web-bluetooth-status');
            if (!status) return;
            status.textContent = message;
            status.style.color = isError ? '#b71c1c' : '#555';
        }

        function webBluetoothErrorMessage(error) {
            if (error && error.name === 'NotFoundError') {
                return 'Pemilihan printer dibatalkan atau RPP02N tidak ditemukan.';
            }
            if (error && error.name === 'SecurityError') {
                return 'Bluetooth diblokir browser. Buka halaman melalui HTTPS atau localhost.';
            }
            if (error && error.name === 'NetworkError') {
                return 'Koneksi ke RPP02N terputus. Pastikan printer menyala dan tidak terhubung ke perangkat lain.';
            }

            return (error && error.message) ? error.message : String(error);
        }

        async function findSm802WriteCharacteristic(server) {
            for (const channel of sm802PreferredChannels) {
                try {
                    const service = await server.getPrimaryService(channel.service);
                    for (const uuid of channel.characteristics) {
                        try {
                            const characteristic = await service.getCharacteristic(uuid);
                            if (characteristic.properties.writeWithoutResponse || characteristic.properties.write) {
                                return characteristic;
                            }
                        } catch (error) {
                            // Coba characteristic berikutnya karena firmware SM802 dapat berbeda.
                        }
                    }
                } catch (error) {
                    // Coba service printer berikutnya.
                }
            }

            const services = await server.getPrimaryServices();
            let writeWithResponse = null;

            for (const service of services) {
                const characteristics = await service.getCharacteristics();
                for (const characteristic of characteristics) {
                    if (characteristic.properties.writeWithoutResponse) {
                        return characteristic;
                    }
                    if (!writeWithResponse && characteristic.properties.write) {
                        writeWithResponse = characteristic;
                    }
                }
            }

            if (writeWithResponse) return writeWithResponse;
            throw new Error('Saluran tulis BLE pada printer tidak ditemukan.');
        }

        async function connectSm802() {
            if (sm802Device && sm802Device.gatt.connected && sm802WriteCharacteristic) {
                return sm802WriteCharacteristic;
            }

            setWebBluetoothStatus('Mencari RPP02N/SM802...', false);
            sm802Device = await navigator.bluetooth.requestDevice({
                filters: [
                    { namePrefix: 'RPP' },
                    { namePrefix: 'SM802' }
                ],
                optionalServices: sm802ServiceUuids
            });

            sm802Device.addEventListener('gattserverdisconnected', function () {
                sm802WriteCharacteristic = null;
                setWebBluetoothStatus('Printer terputus. Klik tombol untuk menghubungkan kembali.', true);
            }, { once: true });

            setWebBluetoothStatus('Menghubungkan ke ' + (sm802Device.name || 'SM802') + '...', false);
            const server = await sm802Device.gatt.connect();
            sm802WriteCharacteristic = await findSm802WriteCharacteristic(server);
            setWebBluetoothStatus((sm802Device.name || 'SM802') + ' terhubung.', false);
            return sm802WriteCharacteristic;
        }

        async function writeSm802Bytes(characteristic, bytes) {
            // Ukuran 20 byte aman untuk perangkat BLE dengan MTU standar.
            const chunkSize = 20;
            for (let offset = 0; offset < bytes.length; offset += chunkSize) {
                const chunk = new Uint8Array(bytes.slice(offset, offset + chunkSize));
                if (characteristic.properties.writeWithoutResponse
                    && typeof characteristic.writeValueWithoutResponse === 'function') {
                    await characteristic.writeValueWithoutResponse(chunk);
                } else {
                    await characteristic.writeValueWithResponse(chunk);
                }

                if (offset > 0 && offset % 1000 === 0) {
                    await new Promise(function (resolve) {
                        setTimeout(resolve, 15);
                    });
                }
            }
        }

        async function printTasksWithWebBluetooth() {
            const button = document.getElementById('web-bluetooth-print-button');
            if (!navigator.bluetooth) {
                setWebBluetoothStatus('Web Bluetooth tidak didukung. Gunakan Chrome atau Edge terbaru.', true);
                return;
            }

            button.disabled = true;
            try {
                const characteristic = await connectSm802();
                const bytes = createTaskPrintBytes();
                setWebBluetoothStatus('Mengirim label 80 × 40 mm ke printer...', false);
                await writeSm802Bytes(characteristic, bytes);
                setWebBluetoothStatus('Label 80 × 40 mm berhasil dikirim ke printer.', false);
            } catch (error) {
                setWebBluetoothStatus(webBluetoothErrorMessage(error), true);
                console.error(error);
            } finally {
                button.disabled = false;
            }
        }

        function initializeWebBluetooth() {
            if (!navigator.bluetooth) {
                const button = document.getElementById('web-bluetooth-print-button');
                button.disabled = true;
                setWebBluetoothStatus('Gunakan Chrome atau Edge terbaru untuk mencetak lewat Bluetooth.', true);
                return;
            }

            if (!window.isSecureContext) {
                const button = document.getElementById('web-bluetooth-print-button');
                button.disabled = true;
                setWebBluetoothStatus('Web Bluetooth memerlukan HTTPS atau alamat localhost.', true);
                return;
            }

            if (typeof navigator.bluetooth.getAvailability === 'function') {
                navigator.bluetooth.getAvailability().then(function (available) {
                    if (!available) {
                        setWebBluetoothStatus('Bluetooth Windows sedang mati atau tidak tersedia.', true);
                    }
                });
            }
        }

        window.addEventListener('load', function () {
            initializeWebBluetooth();
        });
    </script>
</body>
</html>
