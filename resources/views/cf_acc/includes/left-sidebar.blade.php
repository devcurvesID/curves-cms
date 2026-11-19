<meta name="_mtoken" content="{!! csrf_token() !!}"/>
<style>
    .staff-detail {
        color: rebeccapurple;
    }

    #total-data h4 {
        color: deeppink;
        font-size: 14px;
        font-weight: 600;
    }

    #club-initial {
        text-align: left;
        color: deeppink;
        font-weight: 600;
    }

    #language-initial {
        position: absolute;
        right: 0;
        top: 3px;
    }

    .language-img {
        width: 15px;
        height: 15px;
    }

    #language-box {
        position: absolute;
        right: -20px;
        top: 40px;
        height: 70px;
        width: 35px;
        background-color: #eeefef;
        padding: 10px 10px 0;
        display: none;
        z-index: 1;
    }

    #language-box:before {
        content: '';
        display: block;
        position: absolute;
        left: 0%;
        width: 0;
        height: 0;
        border-style: solid;
        transform: rotate(-90deg);
        top: -16px;
        border-color: transparent transparent transparent #eeefef;
        border-width: 8px;
    }

    #language-box ul {
        list-style: none;
        margin-left: 0;
        margin-bottom: 0;
        padding-left: 0;
    }

    #language-box ul li {
        margin-bottom: 15px;
        font-family: OpenSans-Regular;
        font-size: 12px;
        line-height: 15px;
    }
</style>
<div style="margin: 0px auto">
    <center>
        <div id="language-initial" style="margin-top: 10px;">
            @if ("en" == App::getLocale())
                <a href="javascript:;"  title="@lang('labels.english')">
<svg version="1.1" id="langimg-eng" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" class="language-img"
         viewBox="0 0 20 20" style="enable-background:new 0 0 20 20;" xml:space="preserve">
<style type="text/css">
        .st0{fill:#CD152B;}
        .st1{fill:#26336B;}
</style>
<g id="XMLID_79_">
        <g id="XMLID_90_">
                <path id="XMLID_94_" class="st0" d="M12.6,7.4h1.4l4.3-2.9C18.2,4.2,18,4,17.8,3.7l-5.1,3.4V7.4z"/>
                <path id="XMLID_93_" class="st0" d="M4.3,7.4h1.9l-4.5-3C1.5,4.7,1.3,5,1.2,5.3L4.3,7.4z"/>
                <path id="XMLID_92_" class="st0" d="M15.9,12.7H14l4.3,2.9c0.2-0.3,0.4-0.6,0.5-0.9L15.9,12.7z"/>
                <path id="XMLID_91_" class="st0" d="M7.3,12.7H5.8l-4.2,2.8c0.2,0.3,0.4,0.6,0.6,0.8L7.3,13V12.7z"/>
        </g>
        <g id="XMLID_81_">
                <path id="XMLID_89_" class="st1" d="M7.3,6.4V0.4c-1.9,0.5-3.5,1.6-4.8,3L7.3,6.4z"/>
                <path id="XMLID_88_" class="st1" d="M19.6,12.7H17l2.1,1.4C19.3,13.7,19.5,13.2,19.6,12.7z"/>
                <path id="XMLID_87_" class="st1" d="M12.6,6.4l4.7-3.1c-1.3-1.4-2.9-2.4-4.7-2.9V6.4z"/>
                <path id="XMLID_86_" class="st1" d="M16.9,7.4h2.8c-0.1-0.5-0.3-1-0.5-1.5L16.9,7.4z"/>
                <path id="XMLID_85_" class="st1" d="M7.3,13.7l-4.6,3.1c1.2,1.3,2.8,2.3,4.6,2.8V13.7z"/>
                <path id="XMLID_84_" class="st1" d="M12.6,13.7v6c1.9-0.5,3.5-1.5,4.8-2.9L12.6,13.7z"/>
                <path id="XMLID_83_" class="st1" d="M0.4,7.4h2.6L0.8,6C0.7,6.5,0.5,6.9,0.4,7.4z"/>
                <path id="XMLID_82_" class="st1" d="M3.1,12.7H0.4c0.1,0.5,0.3,1,0.5,1.4L3.1,12.7z"/>
        </g>
        <path id="XMLID_80_" class="st0" d="M20,10c0-0.5,0-1-0.1-1.5h-8.3V0.1C11.1,0,10.5,0,10,0C9.5,0,8.9,0,8.4,0.1v8.3H0.1
                C0,9,0,9.5,0,10c0,0.6,0,1.1,0.1,1.7h8.3v8.2C8.9,20,9.5,20,10,20c0.5,0,1.1,0,1.6-0.1v-8.2h8.3C20,11.1,20,10.6,20,10z"/>
</g>
</svg></a>

            @elseif("id" == App::getLocale())
                <a href="javascript:;" title="@lang('labels.bahasa_indonesia')">
                <svg version="1.1" id="langimg-indo" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"  class="language-img"
                    viewBox="0 0 20 20" style="enable-background:new 0 0 20 20;" xml:space="preserve">
                <style type="text/css">
                    .st0{fill:#E42328;}
                    .st1{fill:#FFFFFF;}
                </style>
                <g id="XMLID_98_">
                    <path id="XMLID_100_" class="st0" d="M20,10c0-5.5-4.5-10-10-10S0,4.5,0,10c0,0,0,0,0,0L20,10C20,10,20,10,20,10z"/>
                    <path id="XMLID_99_"  style="fill:#ffffff" class="st1" d="M0,10c0,5.5,4.5,10,10,10s10-4.5,10-10H0z"/>
                </g>
                </svg>

		        </a>
            @endif
        </div>

        <div id="language-box">
            <ul>
                <li class="change-language" id="id">
                    <a href="{{ route('lang.switch', 'id') }}" title="@lang('labels.bahasa_indonesia')">
                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 20 20" style="enable-background:new 0 0 20 20;" xml:space="preserve">
                            <style type="text/css">
                            .st0{fill:#E42328;}
                            .st1{fill:#FFFFFF;}
                            </style>
                            <g id="XMLID_98_">
                                <path id="XMLID_100_" class="st0" d="M20,10c0-5.5-4.5-10-10-10S0,4.5,0,10c0,0,0,0,0,0L20,10C20,10,20,10,20,10z"/>
                                <path id="XMLID_99_" style="fill:#ffffff" class="st1" d="M0,10c0,5.5,4.5,10,10,10s10-4.5,10-10H0z"/>
                            </g>
                        </svg>
		            </a>
                </li>
                <li class="change-language" id="en"><a href="{{ route('lang.switch', 'en') }}" title="@lang('labels.english')">
			<svg version="1.1" id="langimg-eng" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" class="language-img"
	 viewBox="0 0 20 20" style="enable-background:new 0 0 20 20;" xml:space="preserve">
<style type="text/css">
	.st0{fill:#CD152B;}
	.st1{fill:#26336B;}
</style>
<g id="XMLID_79_">
	<g id="XMLID_90_">
		<path id="XMLID_94_" class="st0" d="M12.6,7.4h1.4l4.3-2.9C18.2,4.2,18,4,17.8,3.7l-5.1,3.4V7.4z"/>
		<path id="XMLID_93_" class="st0" d="M4.3,7.4h1.9l-4.5-3C1.5,4.7,1.3,5,1.2,5.3L4.3,7.4z"/>
		<path id="XMLID_92_" class="st0" d="M15.9,12.7H14l4.3,2.9c0.2-0.3,0.4-0.6,0.5-0.9L15.9,12.7z"/>
		<path id="XMLID_91_" class="st0" d="M7.3,12.7H5.8l-4.2,2.8c0.2,0.3,0.4,0.6,0.6,0.8L7.3,13V12.7z"/>
	</g>
	<g id="XMLID_81_">
		<path id="XMLID_89_" class="st1" d="M7.3,6.4V0.4c-1.9,0.5-3.5,1.6-4.8,3L7.3,6.4z"/>
		<path id="XMLID_88_" class="st1" d="M19.6,12.7H17l2.1,1.4C19.3,13.7,19.5,13.2,19.6,12.7z"/>
		<path id="XMLID_87_" class="st1" d="M12.6,6.4l4.7-3.1c-1.3-1.4-2.9-2.4-4.7-2.9V6.4z"/>
		<path id="XMLID_86_" class="st1" d="M16.9,7.4h2.8c-0.1-0.5-0.3-1-0.5-1.5L16.9,7.4z"/>
		<path id="XMLID_85_" class="st1" d="M7.3,13.7l-4.6,3.1c1.2,1.3,2.8,2.3,4.6,2.8V13.7z"/>
		<path id="XMLID_84_" class="st1" d="M12.6,13.7v6c1.9-0.5,3.5-1.5,4.8-2.9L12.6,13.7z"/>
		<path id="XMLID_83_" class="st1" d="M0.4,7.4h2.6L0.8,6C0.7,6.5,0.5,6.9,0.4,7.4z"/>
		<path id="XMLID_82_" class="st1" d="M3.1,12.7H0.4c0.1,0.5,0.3,1,0.5,1.4L3.1,12.7z"/>
	</g>
	<path id="XMLID_80_" class="st0" d="M20,10c0-0.5,0-1-0.1-1.5h-8.3V0.1C11.1,0,10.5,0,10,0C9.5,0,8.9,0,8.4,0.1v8.3H0.1
		C0,9,0,9.5,0,10c0,0.6,0,1.1,0.1,1.7h8.3v8.2C8.9,20,9.5,20,10,20c0.5,0,1.1,0,1.6-0.1v-8.2h8.3C20,11.1,20,10.6,20,10z"/>
</g>
</svg>
</a>                </li>
            </ul>
        </div>

        <div>
            <ul class="left-menu">
                <li>
                    <a href="{{url('cf-acc/dashboard')}}"><span style="font-size: 13px; vertical-align: middle;">❤</span><span class="glyphicon glyphicon-home" style="margin: 0 10px;"></span><span style="font-size: 13px; vertical-align: middle;">❤</span></a>
                </li>
                <li>
                    <a href="{{url('cf-acc/accounting/chart-of-account')}}" style="font-size: 16px;"><span style="font-size: 13px; vertical-align: middle;">❤</span><span style="margin: 0 10px;">@lang('labels.chart_of_account')</span><span style="font-size: 13px; vertical-align: middle;">❤</span></a>
                </li>
                {{--<li>--}}
                    {{--<a href="{{url('cf-acc/general-ledger')}}" style="font-size: 16px;"><span style="font-size: 13px; vertical-align: middle;">❤</span><span style="margin: 0 10px;"> @lang('labels.general_ledger')</span><span style="font-size: 13px; vertical-align: middle;">❤</span></a>--}}
                {{--</li>--}}
                {{--<li>--}}
                    {{--<a href="{{url('cf-acc/memorial-journal')}}" style="font-size: 16px;"><span style="font-size: 13px; vertical-align: middle;">❤</span><span style="margin: 0 10px;"> @lang('labels.memorial_journal')</span><span style="font-size: 13px; vertical-align: middle;">❤</span></a>--}}
                {{--</li>--}}
                <li>
                    <a href="{{url('cf-acc/ledger')}}" style="font-size: 16px;"><span style="font-size: 13px; vertical-align: middle;">❤</span><span style="margin: 0 10px;"> @lang('labels.ledger') <br> @lang('labels.trial_balance') <br> @lang('labels.income_statement')</span><span style="font-size: 13px; vertical-align: middle;">❤</span></a>
                </li>
                {{--<li>--}}
                    {{--<a href="{{url('cf-acc/cash-flow')}}" style="font-size: 16px;"><span style="font-size: 13px; vertical-align: middle;">❤</span><span style="margin: 0 10px;"> @lang('labels.cash_flow')</span><span style="font-size: 13px; vertical-align: middle;">❤</span></a>--}}
                {{--</li>--}}
                {{--<li>--}}
                    {{--<a href="{{url('cf-acc/bank-flow')}}" style="font-size: 16px;"><span style="font-size: 13px; vertical-align: middle;">❤</span><span style="margin: 0 10px;"> @lang('labels.bank_flow')</span><span style="font-size: 13px; vertical-align: middle;">❤</span></a>--}}
                {{--</li>--}}
                {{--<li>--}}
                    {{--<a href="{{url('cf-acc/purchasing-book')}}" style="font-size: 16px;"><span style="font-size: 13px; vertical-align: middle;">❤</span><span style="margin: 0 10px;"> @lang('labels.purchasing_book')</span><span style="font-size: 13px; vertical-align: middle;">❤</span></a>--}}
                {{--</li>--}}
                <li>
                    <hr style="border-color: rebeccapurple">
                </li>
                <li>
                    <a href="{{url('cf-acc/financial-statement/cashflow-statement')}}" style="font-size: 16px;"><span style="font-size: 13px; vertical-align: middle;">❤</span><span style="margin: 0 10px;"> @lang('labels.cashflow_statement')</span><span style="font-size: 13px; vertical-align: middle;">❤</span></a>
                </li>
                <li>
                    <a href="{{url('cf-acc/financial-statement/balance')}}" style="font-size: 16px;"><span style="font-size: 13px; vertical-align: middle;">❤</span><span style="margin: 0 10px;"> @lang('labels.neraca')</span><span style="font-size: 13px; vertical-align: middle;">❤</span></a>
                </li>
                <li>
                    <a href="{{url('cf-acc/financial-statement/income-statement')}}" style="font-size: 16px;"><span style="font-size: 13px; vertical-align: middle;">❤</span><span style="margin: 0 10px;"> @lang('labels.income_statement')</span><span style="font-size: 13px; vertical-align: middle;">❤</span></a>
                </li>
                <li>
                    <hr style="border-color: rebeccapurple">
                </li>
{{--                <li>--}}
{{--                    <a href="{{url('cf-acc/accounting/cost-revenue')}}" style="font-size: 16px;"><span style="font-size: 13px; vertical-align: middle;">❤</span><span style="margin: 0 10px;"> @lang('labels.master_cost_revenue')</span><span style="font-size: 13px; vertical-align: middle;">❤</span></a>--}}
{{--                </li>--}}
                <li>
                    <a href="{{url('cf-acc/transaction')}}" style="font-size: 16px;"><span style="font-size: 13px; vertical-align: middle;">❤</span><span style="margin: 0 10px;"> @lang('labels.manual_cost_revenue')</span><span style="font-size: 13px; vertical-align: middle;">❤</span></a>
                </li>
                <li>
                    <a href="{{url('cf-acc/accounting/cost-revenue-center')}}" style="font-size: 16px;"><span style="font-size: 13px; vertical-align: middle;">❤</span><span style="margin: 0 10px;"> @lang('labels.cost_revenue_center')</span><span style="font-size: 13px; vertical-align: middle;">❤</span></a>
                </li>
                <li>
                    <a href="{{url('cf-acc/accounting/beginning-balance')}}" style="font-size: 16px;"><span style="font-size: 13px; vertical-align: middle;">❤</span><span style="margin: 0 10px;"> @lang('labels.manual_beginning_balance')</span><span style="font-size: 13px; vertical-align: middle;">❤</span></a>
                </li>
            </ul>
        </div>

        <div class="a-close">
            <a role="button" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();" style="font-size: 32px;"><span style="font-size: 13px; vertical-align: middle;">❤</span><span style="margin: 0 10px;">@lang('labels.system_logout')</span><span style="font-size: 13px; vertical-align: middle;">❤</span></a>
        </div>
        {!! Form::open(['method' => 'POST', 'action' => 'AccountingAccountController@acc_logout', 'id' => 'logout-form', 'style' => 'display:none;']) !!}
        {!! Form::close() !!}
    </center>
</div>

<script>
    $(document).ready(function () {
        $('#language-initial').on('click', function () {
            $('#language-box').toggle();
        });
    });
</script>
