<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>Curves ID - @yield('title')</title>

<!-- Jquery -->
<script src="{{asset('js/jquery-3_2_1.min.js')}}"></script>
<!-- Bootstrap -->
<link href="{{asset('bootstrap-3_3_7/css/bootstrap.min.css')}}" rel="stylesheet">
<script src="{{asset('bootstrap-3_3_7/js/bootstrap.min.js')}}"></script>
<!-- Font Awesome -->
<link href="{{asset('font-awesome\css\font-awesome.min.css')}}" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Alegreya+Sans" rel="stylesheet">

<link href="{{asset('custom-css/fab.css')}}" rel="stylesheet">

<style>
    body{
        font-family: 'Alegreya Sans', sans-serif;
    }
    .page-title{
        margin-top: 50px;
        margin-bottom: 50px;
        color: #414042;
        font-size: 50px;
    }
    .no_data_found{
        color: white;
    }

    /*==============Left Sidebar==============*/
    .left-menu{
        list-style: none;
        padding-left: 0px;
    }
    .left-menu li{
        padding: 10px;
    }
    .left-menu li a, .a-close a{
        font-size: 16px;
        font-weight: bold;
        color: rebeccapurple;
        cursor: pointer;
    }
    .left-menu li a:hover, .a-close a:hover{
        text-decoration: none;
        text-transform: none;
        color: deeppink;
    }
    .staff-img{
        width: 150px;
        height: 150px;
        border-radius: 50%;
        padding: 4px;
    }
    .staff-img div{
        width: 142px;
        height: 142px;
        border: 5px solid #f2e5f2;
        border-radius: 50%;
        background-color: purple;
    }
    .staff-detail div{
        padding-top: 5px;
        padding-bottom: 5px;
        border-bottom: 2px solid purple;
        font-weight: bold;
        height: 33px;
        overflow: hidden;
        font-size: 20px;
        width: 150px;
    }
    .staff-detail h4{
        font-weight: bold;
    }
    .list-staff{
        padding-left: 5px;
        list-style: none;
    }
    .list-staff li{
        text-align: left;
        padding: 0px;
    }
    .list-staff li a{
        color: black;
        font-weight: bold;
    }
    /*.list-staff li:first-child a, */.list-staff li a.list-active{
        color: deeppink;
    }
    .list-staff li:hover a{
        color: deeppink;
        cursor: pointer;
        text-decoration: none;
        text-transform: none;
    }
    .box-staff{
        height: 210px;
        background-color: pink;
        padding: 5px;
    }
    .box-staff h4{
        color: deeppink;
        font-weight: bold;
        padding-bottom: 5px;
        border-bottom: 2px solid purple;
    }
    .box-staff div{
        height: 150px;
        overflow-y: scroll;
    }
    .bg-brown-yellow{
        background: linear-gradient(-90deg, #800000 0%,#ffeb7f 35%, #ffffff 40%, #ffeb7f 45%, #800000 100%);
    }
    .a-close{
        padding: 20px;
    }

    /*==============Left Sidebar==============*/


    .nav-tabs{
        border: 0px;
    }

    .nav-tabs li{
        width: 50%;
        text-align: center;
    }
    .nav-tabs li.right-bar:not(.active) a{
        margin: 0px;
        background: linear-gradient(to right, #ffffff 0%, #ffeb7f 10%, #800000 100%);
        border: 0px;
        border-radius: 0px;
    }
    .nav-tabs li.left-bar:not(.active) a{
        margin: 0px;
        background: linear-gradient(to left, #ffffff 0%, #ffeb7f 10%, #800000 100%);
        border: 0px;
        border-radius: 0px;
    }

    .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover{
        background: transparent;
    }

    .nav-tabs li a{
        font-size: 32px;
        color: rebeccapurple;
    }

    .nav-tabs li a:hover{
        cursor: pointer !important;
        color: deeppink !important;
    }

    .nav-tabs li.active a{
        background: transparent;
        color: deeppink !important;
    }

    .box-menu{
        /*height: 625px;*/
        background: transparent;
        border: 10px solid transparent;
        -moz-border-image: -moz-linear-gradient(top, #3acfd5 0%, #3a4ed5 100%);
        -webkit-border-image: -webkit-linear-gradient(top, #3acfd5 0%, #3a4ed5 100%);
        border-image: linear-gradient(-90deg, #800000 0%,#ffeb7f 45%, #ffffff 50%, #ffeb7f 55%, #800000 100%);
        border-image-slice: 1;
    }

    .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover{
        border: 0px;
    }

    .box-staff ::-webkit-scrollbar {
        width: 12px;
    }

    .box-staff ::-webkit-scrollbar-track {
        /*-webkit-box-shadow: inset 6px 6px 6px deeppink;*/
        border-radius: 10px;
        background: deeppink;
    }

    .box-staff ::-webkit-scrollbar-thumb {
        border-radius: 10px;
        background: #414042;
        /*-webkit-box-shadow: inset 0px 0px 6px black;*/
    }

    .container-staff{
        padding-left: 15px;
        padding-right: 15px;
    }
</style>
