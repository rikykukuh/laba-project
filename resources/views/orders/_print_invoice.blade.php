<div class="content">
    <!-- Header Section -->
    <div class="header-section">
        <div class="header-left">
            <img src="{{ asset($config->caminho_img_login) }}" alt="Logo">
            <div class="header-details">
                <strong>PANCALABA</strong><br>
                Jl. Boulevard TB 2/11 Kelapa Gading<br>
                Jakarta Utara, 14240<br>
                Telp: 0812-1904-4164
            </div>
        </div>
        <div class="header-right">
            <p><strong>Buka:</strong> Senin - Jumat 09:00 - 17:00</p>
            <p><strong>Sabtu:</strong> 09:00 - 16:00</p>
            <p><strong>Tutup:</strong> Minggu / Hari Raya</p>
        </div>
    </div>
    <hr>

    <div class="row invoice-info" style="font-size: 14px;">
        <div class="col-sm-4 invoice-col">
            <strong>Detail Pelanggan</strong>
            <address style="margin-bottom: 0px;">
                Nama: <strong>{{ $order->customer->name }}</strong><br>
                Telepon: {{ $order->customer->phone_number }}<br>
                Alamat: {{ $order->customer->address }}<br>
                Kota: {{ $order->customer->city->name ?? '-' }}<br>
            </address>
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
            <b>Detail Order</b><br>
            Cabang: {{ $order->site->name }}<br>
            Tanggal Transaksi: {{ $order->created_at->copy()->timezone('Asia/Jakarta')->format('d-m-Y H:i:s') }} WIB<br>
            @if(!is_null($order->creator))
            Diterima Oleh: {{ $order->creator->name }}<br>
            @endif
            <div class="header-center" style="text-align: left;">
            <b style="font-size: 30px;">{{ $order->number_ticket }}</b>
        </div>
        </div>
        <div class="col-sm-4 invoice-col">
            <div class="header-right" style="text-align: center; font-size: 20px;">
            @if($order->estimate_take_item)
            Estimasi: {{ \Carbon\Carbon::createFromFormat('Y-m-d', $order->estimate_take_item)->format('d-m-Y') }}
            @else
            Estimasi: {{ $order->created_at->copy()->timezone('Asia/Jakarta')->addDays(3)->format('d-m-Y') }}
            @endif
            <br><div style="text-align: center"><b>SELESAI SORE</b></div>
        </div>
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
    <div class="header-section">
        
    </div>
    <!-- Items Table -->
    <div class="table-responsive">
        <table class="table table-sm table-striped table-bordered" cellpadding="0">
            <thead>
            <tr>
                <th class="text-center" style="font-size: 14px;width: 18%;">Kode Bon Item</th>
                <th style="font-size: 14px;width: 50%;">Keterangan</th>
                <th class="text-right" style="font-size: 14px;width: 20%;">Subtotal</th>
            </tr>
            </thead>
            <tbody>
            @foreach($order->orderItems as $orderItem)
                @php
                    $itemSequence = \App\Models\OrderItem::sequenceLabel($loop->iteration);
                    $itemImportCode = $order->itemImportCode($loop->iteration);
                @endphp
                <tr>
                    <td class="text-center" style="font-size: 14px;"><strong>{{ $itemImportCode }}</strong></td>
                    <td style="font-size: 14px;">{{ $orderItem->note }}</td>
                    <td class="text-right" style="font-size: 14px;">Rp{{ number_format($orderItem->bruto, null, ",", ".") }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Totals -->
    <div class="row">
        <div class="col-xs-6">
            <p class="lead" style="margin-bottom: 0px;">Metode Pembayaran:</p>
            @if($order->uang_muka != 0)
            <p style="font-size: 11px;">
                <strong>
                    @if($order->payment)
                        {{ $order->payment->paymentMethod->name }} - {{ $order->payment->paymentMerchant->name ?? "-" }}
                    @else
                        -
                    @endif
                </strong>
            </p>
            @else
            <p></p>
            @endif
            <p style="font-size: 11px;"><b>Catatan:</b> {{ $order->note ?? '-' }}</p>
            <p style="font-size: 11px;"><b>Disclaimer:</b> {{ $config->disclaimer }}</p>
            <p style="margin-top: 10px;font-size: 12px;">
                <b>For:</b> {{ ucwords($type ?? request()->get('type')) }}
            </p>
        </div>
        <div class="col-xs-6">
            <div class="table-responsive">
                <table class="table table-total">
                    <tr>
                        <th style="font-size: 12px;">Total:</th>
                        <td class="text-right" style="font-size: 12px;" >Rp{{ number_format($order->orderItems->sum('bruto'), null, ",", ".") }}</td>
                    </tr>
                    <tr>
                        <th style="font-size: 12px;">DP:</th>
                        <td class="text-right" style="font-size: 12px;" >Rp{{ number_format($order->uang_muka, null, ",", ".") }}</td>
                    </tr>
                    <tr>
                        <th style="font-size: 12px;">Discount:</th>
                        <td class="text-right" style="font-size: 12px;" >Rp{{ number_format($order->discount, null, ",", ".") }}</td>
                    </tr>
                    <tr>
                        <th style="font-size: 12px;">Sisa Pembayaran:</th>
                        <td class="text-right" style="font-size: 12px;">Rp{{ number_format((($order->orderItems->sum('bruto') - $order->uang_muka) - $order->discount), null, ",", ".") }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
