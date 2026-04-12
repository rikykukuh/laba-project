@extends('layouts.AdminLTE.index')

@section('icon_page', 'shopping-basket')

@section('title', 'Report Payment')

@section('menu_pagina')
    <li role="presentation">
        <a href="{{ route('order-products.create') }}" class="link_menu_page">
            <i class="fa fa-plus"></i> Tambah Penjualan
        </a><li role="presentation">
        <a href="{{ route('orders.create') }}" class="link_menu_page">
            <i class="fa fa-plus"></i> Tambah Reparasi
        </a>
    </li>
@endsection

@section('content')
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Form Filter Report Payment</h3>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body">
            <form class="form-inline" id="form-filter">
                <input type="hidden" class="form-control" name="start_date" id="start_date" value="">
                <input type="hidden" class="form-control" name="end_date" id="end_date" value="">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Date range:</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right" id="summary-payment">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Amount (Order):</label>
                            <select class="form-control" id="amount-order">
                                <option value="highest">Highest</option>
                                <option value="lowest">Lowest</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin: 15px auto;">
                        <div class="form-group">
                            <button type="submit" class="btn btn-sm bg-navy">
                                <i class="fa fa-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">Total Pembayaran Per payment method</h3>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="table-summary-payment-detail" class="table table-condensed table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Payment Method</th>
                                <th class="text-center">Amount</th>
                            </tr>
                            </thead>
                            <tbody id="table-summary-payment-detail-body">
                            <!-- Data will be loaded here via Ajax -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">Total Pembayaran Per payment merchant</h3>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="table-summary-payment" class="table table-condensed table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Payment Method</th>
                                <th class="text-center">Merchant Name</th>
                                <th class="text-center">Amount</th>
                            </tr>
                            </thead>
                            <tbody id="table-summary-payment-body">
                            <!-- Data will be loaded here via Ajax -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
{{--        @if ($payments->hasPages())--}}
{{--            <div class="box-footer with-border">--}}
{{--                {{ $payments->links() }}--}}
{{--            </div>--}}
{{--        @endif--}}
        <div class="box-footer">
            <span id="spinner" style="display: none; margin-left: 10px;">
                <i class="fa fa-spinner fa-spin"></i> Loading...
            </span>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Daftar Pembayaran</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table id="payments-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Nomor</th>
                                        <th>Nomer BON</th>
                                        <th>Tanggal Pembayaran</th>
                                        <th>Payment Method</th>
                                        <th>Payment Merchant</th>
                                        <th>Nominal Pemabayaran</th>
                                        <th>Status Bon</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="col-md-6">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Bar Chart</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div> -->
    </div>
@endsection

@section('scripts')
<!-- jQuery -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<!-- Buttons Extension -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script>
        let pieChartInstance;
        let barChartInstance;

        $(function() {
            // Initialize date range picker
            $('#summary-payment').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });

            // Auto-submit form when date range is applied
            $('#summary-payment').on('apply.daterangepicker', function(ev, picker) {
                $('#start_date').val(picker.startDate.format('YYYY-MM-DD'));
                $('#end_date').val(picker.endDate.format('YYYY-MM-DD'));

                // Submit the form with Ajax
                fetchPayments();
            });

            $('#amount-order').on('change', function() {
                $('#form-filter').submit();
            });

            // Handle form submit for filtering
            $('#form-filter').on('submit', function(e) {
                e.preventDefault();
                fetchPayments();
            });

            // Function to fetch and load payment data
            function fetchPayments() {
                // Hancurkan instance DataTable sebelumnya jika ada
                if ($.fn.DataTable.isDataTable('#payments-table')) {
                    $('#payments-table').DataTable().clear().destroy();
                }

                const table = $('#payments-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route("laporan.ringkasan-pembayaran") }}', // Sesuaikan dengan route Anda
                        type: 'GET',
                        data: {
                            start_date: $('#start_date').val(),
                            end_date: $('#end_date').val(),
                            amount_order: $('#amount-order').val()
                        },
                        dataSrc: function (json) {
                            console.log('Respons JSON:', json); // Melihat seluruh respons JSON
                            console.log('Data di list_data:', json.data_list); // Melihat isi properti list_data
                            return json.data_list; // Pastikan DataTables membaca data dari list_data
                        },
                    },
                    columns: [
                        { 
                            data: null, 
                            name: 'no', 
                            render: function(data, type, row, meta) {
                                return meta.row + 1; // For row numbering
                            }
                        },
                        { data: 'order_number', name: 'order_number' },
                        // { data: 'created_at', name: 'created_at' },
                        { 
                            data: 'created_at', 
                            name: 'created_at',
                            render: function (data) {
                                // Ubah format ISO ke Hari-Bulan-Tahun Jam:Menit:Detik
                                const date = new Date(data);
                                const formattedDate = `${date.getDate().toString().padStart(2, '0')}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getFullYear()} ${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}:${date.getSeconds().toString().padStart(2, '0')}`;
                                return formattedDate;
                            }
                        },
                        { data: 'payment_method_name', name: 'payment_method_name' },
                        { data: 'payment_merchant_name', name: 'payment_merchant_name' },
                        { 
                            data: 'payment_amount', 
                            name: 'payment_amount',
                            render: function(data, type, row) {
                                if (row.order_status === 'CANCEL') {
                                    return formatRupiah(0); // atau 'Rp 0'
                                }
                                return formatRupiah(data);
                            }
                        },
                        { data: 'order_status', name: 'order_status' },
                    ],
                    dom: 'Bfrtip', // Tambahkan DOM untuk Buttons
                    buttons: [
                        {
                            extend: 'print',
                            text: 'Print Table',
                            title: function () {
                                    // Set title dengan tanggal
                                    let startDate = $('#start_date').val();
                                    let endDate = $('#end_date').val();

                                    if (!startDate || !endDate) {
                                        const today = new Date();
                                        const defaultStartDate = `${today.getFullYear()}-${(today.getMonth() + 1)
                                            .toString()
                                            .padStart(2, '0')}-${today
                                            .getDate()
                                            .toString()
                                            .padStart(2, '0')} 00:00:00`;
                                        const defaultEndDate = `${today.getFullYear()}-${(today.getMonth() + 1)
                                            .toString()
                                            .padStart(2, '0')}-${today
                                            .getDate()
                                            .toString()
                                            .padStart(2, '0')} 23:59:59`;

                                        startDate = startDate || defaultStartDate;
                                        endDate = endDate || defaultEndDate;
                                    }

                                    return `Laporan Uang Masuk ${startDate} - ${endDate}`;},
                            customize: function (win) {
                                $(win.document.body).css('font-size', '10pt');
                                $(win.document.body).css('padding', '10pt');
                                $(win.document.body)
                                    .find('h1')
                                    .css('font-size', '15pt') // Mengatur ukuran font menjadi 15pt
                                    .css('text-align', 'center');
                                $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');

                                $(win.document.body).prepend('<style>@media print { body { font-size: 12pt; margin: 10px; } table { width: 100%; font-size: 10pt; } }</style>');
                                    // Add interval to check if the print window is closed
                                var interval = setInterval(function() {
                                    // Check if the print window has been closed
                                    if (win.document.hidden) {
                                        clearInterval(interval);
                                        win.close(); // Close the print window after printing is done
                                    }
                                }, 500); // Check every 500ms
                                win.focus();  // Ensure print window is brought to the foreground

                                // Trigger print dialog
                                win.print();
                            },
                            action: function (e, dt, button, config) {
                                const previousPaging = dt.settings()[0]._iDisplayLength; // Simpan pengaturan paging sebelumnya

                                // Nonaktifkan paging dan tunggu hingga data selesai dimuat
                                dt.page.len(-1).draw(false).one('draw', function () {
                                    // Lakukan print setelah data selesai dimuat
                                    $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);

                                    // Kembalikan pengaturan paging ke sebelumnya
                                    dt.page.len(previousPaging).draw(false);
                                });
                            },
                        }                                                            
                    ]                                   
                });
                // Show spinner
                $('#spinner').show();

                $.ajax({
                    url: '{{ route("laporan.ringkasan-pembayaran") }}',
                    type: 'GET',
                    data: {
                        start_date: $('#start_date').val(),
                        end_date: $('#end_date').val(),
                        amount_order: $('#amount-order').val()
                    },
                    success: function(response) {
                        // Get the selected amount order
                        const amountOrder = $('#amount-order').val();

                        // Sort data based on amount order selection
                        response.data.sort((a, b) => {
                            return amountOrder === 'highest' ? b.total_value - a.total_value : a.total_value - b.total_value;
                        });

                        // Clear the table body
                        $('#table-summary-payment-body').empty();

                        // Check if there is data in the response
                        let total_amount = 0;
                        if (response.data.length > 0) {
                            // Populate table rows
                            $.each(response.data, function(index, payment) {
                                $('#table-summary-payment-body').append(`
                                    <tr>
                                        <th class="text-center">${index + 1}</th>
                                        <td class="text-center">${payment.method_name}</td>
                                        <td class="text-center">${payment.merchant_name}</td>
                                        <td class="text-center">Rp. ${parseFloat(payment.total_value).toLocaleString('id-ID', { minimumFractionDigits: 2 })}</td>
                                    </tr>
                                `);
                                total_amount = total_amount + parseFloat(payment.total_value);
                            });
                            $('#table-summary-payment-body').append(`
                                    <tr style="background-color: #f0f0f0;">
                                        <td class="text-center" colspan="3">Total</td>
                                        <td class="text-center">Rp. ${parseFloat(total_amount).toLocaleString('id-ID', { minimumFractionDigits: 2 })}</td>
                                    </tr>
                                `);
                            
                        } else {
                            // Show a message if no data is found
                            $('#table-summary-payment-body').append(`
                                <tr>
                                    <td colspan="4" class="text-center">No data available.</td>
                                </tr>
                            `);
                        }

                        // Update the charts with the new data
                        fetchChartData();
                    },
                    error: function() {
                        alert('Failed to load data. Please try again.');
                    },
                    complete: function() {
                        // Hide spinner after request completes
                        $('#spinner').hide();
                    }
                });

                $.ajax({
                    url: '{{ route("laporan.ringkasan-pembayaran") }}',
                    type: 'GET',
                    data: {
                        start_date: $('#start_date').val(),
                        end_date: $('#end_date').val(),
                        amount_order: $('#amount-order').val()
                    },
                    success: function(response) {
                        // Get the selected amount order
                        const amountOrder = $('#amount-order').val();
                        console.log(response); 
                        // Sort data based on amount order selection
                        response.data_detail.sort((a, b) => {
                            return amountOrder === 'highest' ? b.total_value - a.total_value : a.total_value - b.total_value;
                        });

                        // Clear the table body
                        $('#table-summary-payment-detail-body').empty();
                        let total_amount = 0;
                        // Check if there is data in the response
                        if (response.data_detail.length > 0) {
                            // Populate table rows
                            $.each(response.data_detail, function(index, payment) {
                                $('#table-summary-payment-detail-body').append(`
                                    <tr>
                                        <th class="text-center">${index + 1}</th>
                                        <td class="text-center">${payment.method_name}</td>
                                        <td class="text-center">Rp. ${parseFloat(payment.total_value).toLocaleString('id-ID', { minimumFractionDigits: 2 })}</td>
                                    </tr>
                                `);
                                total_amount = total_amount + parseFloat(payment.total_value);
                            });
                            $('#table-summary-payment-detail-body').append(`
                                    <tr style="background-color: #f0f0f0;">
                                        <td></td>
                                        <td class="text-center">Total</td>
                                        <td class="text-center">Rp. ${parseFloat(total_amount).toLocaleString('id-ID', { minimumFractionDigits: 2 })}</td>
                                    </tr>
                                `);
                        } else {
                            // Show a message if no data is found
                            $('#table-summary-payment-detail-body').append(`
                                <tr>
                                    <td colspan="4" class="text-center">No data available.</td>
                                </tr>
                            `);
                        }

                        // Update the charts with the new data
                        fetchChartData();
                    },
                    error: function() {
                        alert('Failed to load data. Please try again.');
                    },
                    complete: function() {
                        // Hide spinner after request completes
                        $('#spinner').hide();
                    }
                });
            }

            // Function to fetch and load chart data
            function fetchChartData() {
                // Show spinner
                $('#spinner').show();

                $.ajax({
                    url: '{{ route("laporan.ringkasan-pembayaran") }}',
                    type: 'GET',
                    data: {
                        start_date: $('#start_date').val(),
                        end_date: $('#end_date').val(),
                        amount_order: $('#amount-order').val()
                    },
                    success: function(response) {
                        // Get the selected amount order
                        const amountOrder = $('#amount-order').val();

                        // Sort data based on amount order selection
                        response.data.sort((a, b) => {
                            return amountOrder === 'highest' ? b.total_value - a.total_value : a.total_value - b.total_value;
                        });

                        // Strukturkan data untuk pie chart
                        let PieData = response.data.map((item, index) => ({
                            value: item.total_value,
                            color: getColor(index),
                            highlight: getColor(index),
                            label: `${item.merchant_method}`
                        }));

                        // Strukturkan data untuk bar chart
                        let labels = response.data.map(item => item.merchant_method);
                        let data = response.data.map(item => item.total_value);

                        // // Buat Pie Chart
                        // createPieChart(PieData);

                        // // Buat Bar Chart
                        // createBarChart(labels, data);
                    },
                    complete: function() {
                        // Hide spinner after chart data is loaded
                        $('#spinner').hide();
                    }
                });
            }

            function getColor(index) {
                // Tentukan warna yang berbeda untuk setiap item
                const colors = ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'];
                return colors[index % colors.length];
            }

            function createPieChart(PieData) {
                let pieChartCanvas = $('#pieChart').get(0).getContext('2d');

                // Destroy previous pie chart instance if it exists
                if (pieChartInstance) {
                    pieChartInstance.destroy();
                }

                pieChartInstance = new Chart(pieChartCanvas).Doughnut(PieData, {
                    segmentShowStroke: true,
                    segmentStrokeColor: '#fff',
                    segmentStrokeWidth: 2,
                    percentageInnerCutout: 50, // This is 0 for Pie charts
                    animationSteps: 100,
                    animationEasing: 'easeOutBounce',
                    animateRotate: true,
                    animateScale: false,
                    responsive: true,
                    maintainAspectRatio: true,
                    tooltipTemplate: "<%= label %>: Rp. <%= new Intl.NumberFormat('id-ID').format(value) %>",
                    legendTemplate: '<ul class="<%=name.toLowerCase()%>-legend"><% for (let i=0; i<segments.length; i++){%><li><span style="background-color:<%=segments[i].fillColor%>"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>'
                });
            }

            function createBarChart(labels, data) {
                let barChartCanvas = $('#barChart').get(0).getContext('2d');

                // Destroy previous bar chart instance if it exists
                if (barChartInstance) {
                    barChartInstance.destroy();
                }

                barChartInstance = new Chart(barChartCanvas).Bar({
                    labels: labels,
                    datasets: [
                        {
                            label: "Total Value",
                            fillColor: "#00a65a",
                            strokeColor: "#00a65a",
                            pointColor: "#00a65a",
                            data: data
                        }
                    ]
                }, {
                    scaleBeginAtZero: true,
                    scaleShowGridLines: true,
                    scaleGridLineColor: 'rgba(0,0,0,.05)',
                    scaleGridLineWidth: 1,
                    scaleShowHorizontalLines: true,
                    scaleShowVerticalLines: true,
                    barShowStroke: true,
                    barStrokeWidth: 2,
                    barValueSpacing: 5,
                    barDatasetSpacing: 1,
                    responsive: true,
                    maintainAspectRatio: true,
                    tooltipTemplate: "<%= label %>: Rp. <%= new Intl.NumberFormat('id-ID').format(value) %>",
                    legendTemplate: '<ul class="<%=name.toLowerCase()%>-legend"><% for (let i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].fillColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>'
                });
            }

            // Load data on page load
            fetchPayments();
        });


        function formatRupiah(amount) {
            return 'Rp ' + parseInt(amount).toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }
    </script>
@endsection
