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
        <div id="club-initial" style="margin-top: 10px;">
            <span class="fa fa-building"></span>: {{Auth::user()->user_club->club_name}}
        </div>

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
                    <a href="{{url('cf-lite/dashboard')}}" style="font-size: 24px;"><span style="font-size: 13px; vertical-align: middle;">❤</span><span style="margin: 0 10px;">@lang('labels.main_menu')</span><span style="font-size: 13px; vertical-align: middle;">❤</span></a>
                </li>
                <li>
                    <a href="{{url('cf-lite/appointment')}}" style="font-size: 24px;"><span style="font-size: 13px; vertical-align: middle;">❤</span><span style="margin: 0 10px;"> @lang('labels.scheduling')</span><span style="font-size: 13px; vertical-align: middle;">❤</span></a>
                </li>
                <li>
                    <a href="{{url('cf-lite/manual-login/choose')}}" style="font-size: 16px;"><span style="font-size: 13px; vertical-align: middle;">❤</span><span style="margin: 0 10px;"> @lang('labels.manual_attendance')</span><span style="font-size: 13px; vertical-align: middle;">❤</span></a>
                </li>
            </ul>
        </div>
        <div>
            <div class="bg-brown-yellow staff-img" style="overflow: hidden;">
                @if(!empty($latest_member_attendance->workout_user->user_memberpersonal->photo))
                    <img class="img-circle" id="attended-member-photo" src="{{asset('images/member_photos/'.$latest_member_attendance->workout_user->user_memberpersonal->photo)}}" style="width: 142px; height: 142px; object-fit: cover;">
                @else
                    <img class="img-circle" id="attended-member-photo" src="{{asset('images/img.png')}}" style="width: 142px; height: 142px; object-fit: cover;">
                @endif
            </div>
            <div class="staff-detail">
                @if(!empty($latest_member_attendance))
                    <div id="attended-member-name">{{$latest_member_attendance->workout_user->name}}</div>
                    <h4 id="attended-member-time">{{\Carbon\Carbon::parse($latest_member_attendance->workout_date)->setTimezone(Session::get('timezone'))->format('H:i T')}}</h4>
                    <div style="display: none">
                        {{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $latest_member_attendance->workout_date, 'UTC')->setTimezone('Asia/Jakarta')}}<br><br>
                        {{\Carbon\Carbon::now()}}
                    </div>
                @else
                    <div>-</div>
                    <h4>-</h4>
                @endif
            </div>
        </div>

        <div class="bg-brown-yellow" style="height: 220px; padding: 5px">
            <div class="box-staff">
                <h4>@lang('labels.attended_member_list')</h4>
                <div>
                    <ul class="list-staff">
                        <script>var members = [];</script>
                        @if(count($member_attendance_list) > 0)
                            @php($the_counter = 0)
                            @php($owe_payment_class = '')
                            @php($currentDate = Carbon\Carbon::now('UTC')->toDateString())


                            @foreach($member_attendance_list as $attend_member)
                                <script>members.push('{{$attend_member->workout_user->id}}')</script>
                                @php($apoBGColor = '')
                                @php($apoDate = '')


                                        @php($apoBGColor = '#eaa6a6')


                                <!--li style="background: {{ $apoBGColor }}"-->
                                <li>
                                    @if($the_counter == 0)
                                        @php($the_list = 'list-active')
                                    @else
                                        @php($the_list = '')
                                    @endif
                                    @if($attend_member->pending_payment->payment_status == 'Unpaid')
                                        @php($paymentDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $attend_member->pending_payment->payment_date)->format('Y-m-d'))

                                        @if($currentDate >= $paymentDate)
                                            @php($owe_payment_class = 'unpaid')

                                            <a role="button" class="attended-member {{$the_list}} {{ $owe_payment_class }}" id="attended-member-{{$attend_member->workout_user->id}}">{{$attend_member->workout_user->name}}
                                            - <span>{{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $attend_member->pending_payment->payment_date)->format('d/M/Y')}}</span>
                                            </a>
                                        @else
                                            @php($owe_payment_class = '')
                                            <a role="button" class="attended-member {{$the_list}} {{ $owe_payment_class }}" id="attended-member-{{$attend_member->workout_user->id}}">{{$attend_member->workout_user->name}}</a>
                                        @endif
                                    @else
                                        @php($owe_payment_class = '')
                                        <a role="button" class="attended-member {{$the_list}} {{ $owe_payment_class }}" id="attended-member-{{$attend_member->workout_user->id}}">{{$attend_member->workout_user->name}}</a>
                                    @endif

                                    @if($attend_member->workout_user->user_appointment && count($attend_member->workout_user->user_appointment) > 1)
                                        <span style="font-style:italic;"> - WM: {{ Carbon\Carbon::createFromFormat('Y-m-d', $attend_member->workout_user->user_appointment[0]->app_date)->format('d/M/Y') }}</span>
                                    @elseif(count($attend_member->workout_user->user_appointment) > 0 )
                                        <span style="font-style:italic;"> - WM: {{ Carbon\Carbon::createFromFormat('Y-m-d', $attend_member->workout_user->user_appointment[0]->app_date)->format('d/M/Y') }}</span>
                                    @endif
                                </li>
                                @php($the_counter++)
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <div id="total-data">
            <div class="col-md-6">
                <h4>WO: {{count($member_attendance_list)}}</h4>
            </div>
            <div class="col-md-6">
                <h4 style="margin-bottom: 5px;" title="@lang('labels.member_total')">@lang('labels.member'): {{$total_member}}</h4>
            </div>
            <div class="col-md-12">
                <h4 style="margin-top: 0; text-align: left; margin-left: 13px;">WO (@lang('labels.other_club')): {{count($other_attendance_list)}}</h4>
            </div>
        </div>

        <div style="margin-top: 50px; color: rebeccapurple;">
            <div class="form-group">
                @if(!empty(Session::get('staff_session')))
                    {!! Form::label('active_staff', trans('labels.active_staff').': '.Session::get('staff_session_name'), ['class' => 'control-label', 'style' => 'font-size: 18px;']) !!}
                @else
                    {!! Form::label('active_staff', trans('labels.active_staff').': '.Auth::user()->name, ['class' => 'control-label', 'style' => 'font-size: 18px;']) !!}
                @endif
            </div>
            <div class="form-group">
                {!! Form::label('staff_session', trans('labels.change_active_staff').': ', ['class' => 'control-label pull-left']) !!}
                {!! Form::select('staff_session', $our_staffs, !empty(Session::get('staff_session')) ? Session::get('staff_session') : Auth::id(), ['class' => 'form-control', 'placeholder' => trans('labels.choose_staff'), 'style' => 'color: rebeccapurple;']) !!}
            </div>
        </div>

        {{--Club Changing start from Club Manager--}}
        @if((\Illuminate\Support\Facades\Auth::user()->role_id <= 4) || (\Illuminate\Support\Facades\Auth::user()->role_id == 8) || (\Illuminate\Support\Facades\Auth::user()->role_id == 10))
            {!! Form::open(['method' => 'POST', 'action' => ['StaffController@manager_class_changing_club', \Illuminate\Support\Facades\Auth::id()], 'id' => 'manager-changing-club-form']) !!}
            <div style="color: rebeccapurple;">
                <div class="form-group">
                    {!! Form::label('active_club', trans('labels.change_active_club').': ', ['class' => 'control-label pull-left', 'title' => trans('labels.you_will_be_forced_to_logout')]) !!}
                    @if((\Illuminate\Support\Facades\Auth::user()->role_id <= 4) || (\Illuminate\Support\Facades\Auth::user()->role_id == 8) || (\Illuminate\Support\Facades\Auth::user()->role_id == 10))
                        {!! Form::select('active_club', $club_access, \Illuminate\Support\Facades\Auth::user()->club_id, ['class' => 'form-control', 'style' => 'color: rebeccapurple;']) !!}
                    {{--@elseif(\Illuminate\Support\Facades\Auth::user()->role_id == 4)--}}
                        {{--<select name="active_club" id="active_club" class="form-control" style="color: rebeccapurple;">--}}
                            {{--@foreach($club_access as $club_available)--}}
                                {{--@if($club_available->id == \Illuminate\Support\Facades\Auth::user()->club_id)--}}
                                    {{--<option value="{{$club_available->id}}" selected="selected">{{$club_available->club_name}}</option>--}}
                                {{--@else--}}
                                    {{--<option value="{{$club_available->id}}">{{$club_available->club_name}}</option>--}}
                                {{--@endif--}}
                            {{--@endforeach--}}
                        {{--</select>--}}
                    @endif
                </div>
            </div>
            {!! Form::close() !!}
        @endif

        <div class="a-close">
            <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();" style="font-size: 32px;"><span style="font-size: 13px; vertical-align: middle;">❤</span><span style="margin: 0 10px;">@lang('labels.system_logout')</span><span style="font-size: 13px; vertical-align: middle;">❤</span></a>
        </div>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
        </form>

        {{--<div id="club-initial">--}}
            {{--<span class="fa fa-building"></span>: {{Auth::user()->user_club->club_name}}--}}
        {{--</div>--}}
    </center>
</div>

<script>
    $(document).ready(function () {
        function addZero(i) {
            if (i < 10) {
                i = "0" + i;
            }
            return i;
        }

        $('.list-staff').on('click', '.attended-member', function () {
            $('.attended-member').removeClass('list-active');
            $(this).addClass('list-active');

            var member_id = $(this).attr('id').split('-').pop();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_mtoken"]').attr('content')
                }
            });

            $.ajax({
                url: '{{url('getmemberpersonal')}}',
                data: {
                    member_id: member_id
                },
                type: 'POST',
                dataType: 'json',
                success: function (result) {
                    if (typeof (result['member'].photo) != 'undefined' && result['member'].photo != null) {
                        $('#attended-member-photo').attr('src', '{{asset('images/member_photos')}}/' + result['member'].photo);
                    } else {
                        $('#attended-member-photo').attr('src', '{{asset('images/img.png')}}');
                    }

                    $('#attended-member-name').text(result['member'].name);

                    var d = new Date(result['member'].workout_date);
                    var h = addZero(d.getHours());
                    var m = addZero(d.getMinutes());
                    var tz = '{{\Carbon\Carbon::now()->setTimezone("Asia/Jakarta")->format('T')}}';
                    $('#attended-member-time').text(h + ':' + m + ' ' + tz);
                },
                error: function (xhr, textStatus, errorThrown) {
                    console.log(JSON.stringify(xhr));
                }
            });
        });

        $('select[name="staff_session"]').on('change', function () {
            var url = "{{url('cf-lite')}}/";

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_mtoken"]').attr('content')
                }
            });

            var formData = {
                staff_id: $(this).val()
            };

            var type = 'POST';
            var my_url = url + 'set-staff-session';

            $.ajax({
                type: type,
                url: my_url,
                data: formData,
                dataType: 'json',
                success: function (data) {
                    window.location.reload();
                },
                error: function (data) {
                    console.log('Error: ', data);
                }
            });
        });

        $('#active_club').on('change', function () {
            if ($(this).val() != '') {
                $('#manager-changing-club-form').submit();
            }
        });

        $('#language-initial').on('click', function () {
            $('#language-box').toggle();
        });
    });
</script>
