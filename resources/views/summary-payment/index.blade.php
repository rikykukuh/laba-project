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
            <h3 class="box-title">Report Payment</h3>

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
        <div class="col-md-6">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Donut Chart</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
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
        </div>
    </div>
@endsection

@section('scripts')
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
                            });
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

                        // Buat Pie Chart
                        createPieChart(PieData);

                        // Buat Bar Chart
                        createBarChart(labels, data);
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
    </script>
@endsection
