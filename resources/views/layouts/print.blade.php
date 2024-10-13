<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title')</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('assets/adminlte/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/adminlte/bower_components/font-awesome/css/font-awesome.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('assets/adminlte/bower_components/Ionicons/css/ionicons.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/adminlte/dist/css/AdminLTE.min.css') }}">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    @yield('style')
</head>
 <body>
<div class="wrapper">
    <!-- Main content -->
    <div class="invoice">
        @yield('content')
    </div>
    <!-- /.content -->
</div>
<!-- ./wrapper -->
<script type="text/javascript">
    const printContents = document.querySelector('.invoice').innerHTML;
    const originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;

    window.print();

    if (navigator.userAgent.match(/Mobi/)) {
        // Ini buat cek kalo lagi di mobile browser
        var interval = setInterval(function() {
            // Cek apakah window udah ketutup (print selesai)
            if (document.hidden) {
                clearInterval(interval);
                window.close();
            }
        }, 500); // Cek setiap 500ms
    } else {
        // Buat desktop bisa pake onafterprint
        window.onafterprint = function() {
            window.close();
        };
    }

    document.body.innerHTML = originalContents;

    document.title = @yield('title');
</script>
@yield('script')
</body>
</html>
