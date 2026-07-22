@extends('layouts.AdminLTE.index')

@section('icon_page', 'dashboard')

@section('title', 'Dashboard ')

@section('menu_pagina')

@section('content')
  <div class="row">
    <!-- <div class="col-lg-3 col-xs-6">
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3>{{ $permissionCount }}</h3>

          <p>Peran</p>
        </div>
        <div class="icon">
          <i class="ion ion-person-stalker"></i>
        </div>
        <a href="{{ route('role') }}" class="small-box-footer">Info lanjut <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div> -->
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-yellow">
        <div class="inner">
          <h3>{{ $activeUserCount }}</h3>

          <p>Admin Terdaftar</p>
        </div>
        <div class="icon">
          <i class="ion ion-person-add"></i>
        </div>
        <a href="{{ route('user') }}" class="small-box-footer">Info lanjut <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>

    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-blue">
        <div class="inner">
          <h3>{{ $totalOrderToday }}</h3>

          <p>Bon Hari ini</p>
        </div>
        <div class="icon">
          <i class="ion ion-clipboard"></i>
        </div>
        <a href="{{ route('orders.index') }}" class="small-box-footer">Info lanjut <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>

    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-blue">
        <div class="inner">
          <h3>{{ $totalPenjualanToday }}</h3>

          <p>Penjualan Hari ini</p>
        </div>
        <div class="icon">
          <i class="ion ion-bag"></i>
        </div>
        <a href="{{ route('order-products.index') }}" class="small-box-footer">Info lanjut <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>

    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-green">
        <div class="inner">
          <h3>{{ $barangMasuk }}</h3>

          <p>Barang masuk Hari ini</p>
        </div>
        <div class="icon">
          <i class="ion ion-settings"></i>
        </div>
        <a href="{{ route('orders.index') }}" class="small-box-footer">Info lanjut <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->
  </div>
  <!-- /.row -->
  <!-- content dashboard -->
  @if ($canViewProductivityCharts)
  <div class="row">
    <div class="col-md-6">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Produktivitas Kasir</h3>
          <p class="text-muted no-margin">Jumlah bon yang dibuat (bon cancel tidak dihitung)</p>
        </div>
        <div class="box-body">
          @if ($cashierProductivity->isEmpty())
            <p class="text-center text-muted" style="padding: 100px 0;">Belum ada data bon.</p>
          @else
            <canvas id="cashier-productivity-chart" style="height: 300px;"></canvas>
            <div id="cashier-productivity-chart-legend" class="productivity-chart-legend"></div>
          @endif
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title">Produktivitas Teknisi</h3>
          <p class="text-muted no-margin">Jumlah barang yang di-assign kepada teknisi</p>
        </div>
        <div class="box-body">
          @if ($technicianProductivity->isEmpty())
            <p class="text-center text-muted" style="padding: 100px 0;">Belum ada assignment teknisi.</p>
          @else
            <canvas id="technician-productivity-chart" style="height: 300px;"></canvas>
            <div id="technician-productivity-chart-legend" class="productivity-chart-legend"></div>
          @endif
        </div>
      </div>
    </div>
  </div>
  @endif
  <!-- /.row -->
@endsection

@section('layout_css')
  <style>
    .productivity-chart-legend ul {
      margin: 15px 0 0;
      padding: 0;
      text-align: center;
    }

    .productivity-chart-legend li {
      display: inline-block;
      margin: 4px 10px;
      list-style: none;
    }

    .productivity-chart-legend li span {
      display: inline-block;
      width: 12px;
      height: 12px;
      margin-right: 5px;
      border-radius: 2px;
      vertical-align: -1px;
    }
  </style>
@endsection

@section('layout_js')
  @if ($canViewProductivityCharts)
  <script>
    $(function () {
      const colors = [
        '#3c8dbc', '#00a65a', '#f39c12', '#dd4b39', '#605ca8',
        '#00c0ef', '#d81b60', '#39cccc', '#ff851b', '#001f3f'
      ];

      function renderProductivityPie(canvasId, rows) {
        const canvas = document.getElementById(canvasId);
        if (!canvas || !rows.length) {
          return;
        }

        const chartData = rows.map(function (row, index) {
          return {
            value: row.total,
            color: colors[index % colors.length],
            highlight: colors[index % colors.length],
            label: row.name + ' (' + row.total + ')'
          };
        });

        const chart = new Chart(canvas.getContext('2d')).Pie(chartData, {
          responsive: true,
          maintainAspectRatio: false,
          segmentShowStroke: true,
          segmentStrokeColor: '#fff',
          segmentStrokeWidth: 2,
          animationSteps: 60,
          legendTemplate: '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<segments.length; i++){%><li><span style="background-color:<%=segments[i].fillColor%>"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>'
        });

        $('#' + canvasId + '-legend').html(chart.generateLegend());
      }

      renderProductivityPie('cashier-productivity-chart', @json($cashierProductivity));
      renderProductivityPie('technician-productivity-chart', @json($technicianProductivity));
    });
  </script>
  @endif
@endsection
