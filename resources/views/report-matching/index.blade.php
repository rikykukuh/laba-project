@extends('layouts.AdminLTE.index')

@section('icon_page','exchange')
@section('title','Report Matching')

@section('content')

{{-- FILTER --}}
<div class="box box-info">
    <div class="box-header"><h3 class="box-title">Filter Report Matching</h3></div>
    <div class="box-body">
        <form id="form-filter" class="form-inline">
            <input type="hidden" id="start_date">
            <input type="hidden" id="end_date">

            <div class="form-group">
                <label>Date Range</label>
                <input type="text" id="summary-matching" class="form-control">
            </div>

            <button class="btn btn-primary">
                <i class="fa fa-filter"></i> Filter
            </button>
        </form>
    </div>
</div>

{{-- SUMMARY METHOD --}}
<div class="box box-warning">
    <div class="box-header"><h3 class="box-title">Total per Payment Method</h3></div>
    <div class="box-body">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>#</th>
                <th>Payment Method</th>
                <th>Amount</th>
            </tr>
            </thead>
            <tbody id="summary-method"></tbody>
        </table>
    </div>
</div>

{{-- SUMMARY MERCHANT --}}
<div class="box box-warning">
    <div class="box-header"><h3 class="box-title">Total per Merchant</h3></div>
    <div class="box-body">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>#</th>
                <th>Method</th>
                <th>Merchant</th>
                <th>Amount</th>
            </tr>
            </thead>
            <tbody id="summary-merchant"></tbody>
        </table>
    </div>
</div>

{{-- DATATABLE --}}
<div class="box box-danger">
    <div class="box-header"><h3 class="box-title">Daftar Pembayaran</h3></div>
    <div class="box-body">
        <table id="matching-table" class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>No</th>
                <th>No BON</th>
                <th>Tanggal</th>
                <th>Method</th>
                <th>Merchant</th>
                <th>Nominal</th>
                <th>Status</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(function(){

    $('#summary-matching').daterangepicker({
        locale:{format:'YYYY-MM-DD'}
    });

    const table = $('#matching-table').DataTable({
        processing: true,
        ajax:{
            url:'{{ route("laporan.report-matching-data") }}',
            data: function(d){
                d.start_date = $('#start_date').val();
                d.end_date   = $('#end_date').val();
            },
            dataSrc:function(json){
                renderMethod(json.data_detail);
                renderMerchant(json.data);
                return json.data_list;
            }
        },
        columns:[
            {data:null,render:(d,t,r,m)=>m.row+1},
            {data:'order_number'},
            {data:'created_at',render:d=>new Date(d).toLocaleString('id-ID')},
            {data:'payment_method_name'},
            {data:'payment_merchant_name'},
            {data:'payment_amount',render:d=>rupiah(d)},
            {data:'payment_status'}
        ]
    });

    $('#summary-matching').on('apply.daterangepicker',function(e,p){
        $('#start_date').val(p.startDate.format('YYYY-MM-DD'));
        $('#end_date').val(p.endDate.format('YYYY-MM-DD'));
        table.ajax.reload();
    });

    function renderMethod(data){
        let h='',t=0;
        data.forEach((d,i)=>{
            t+=+d.total_value;
            h+=`<tr>
                <td>${i+1}</td>
                <td>${d.method_name}</td>
                <td>${rupiah(d.total_value)}</td>
            </tr>`;
        });
        h+=`<tr>
            <td colspan="2"><b>Total</b></td>
            <td><b>${rupiah(t)}</b></td>
        </tr>`;
        $('#summary-method').html(h);
    }

    function renderMerchant(data){
        let h='',t=0;
        data.forEach((d,i)=>{
            t+=+d.total_value;
            h+=`<tr>
                <td>${i+1}</td>
                <td>${d.method_name}</td>
                <td>${d.merchant_name}</td>
                <td>${rupiah(d.total_value)}</td>
            </tr>`;
        });
        h+=`<tr>
            <td colspan="3"><b>Total</b></td>
            <td><b>${rupiah(t)}</b></td>
        </tr>`;
        $('#summary-merchant').html(h);
    }

    function rupiah(v){
        return 'Rp '+parseInt(v).toLocaleString('id-ID');
    }
});
</script>
@endsection
