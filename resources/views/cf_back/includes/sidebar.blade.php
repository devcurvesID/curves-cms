<div class="left_col scroll-view">
    <div class="navbar nav_title" style="border: 0; padding-top: 10px;">
        <a href="{{url('cf-back/dashboard')}}" class="site_title">
            <center>
                <img src="{{asset('images/logo.png')}}" width="90px" height="50px;">
            </center>
        </a>
    </div>

    <div class="clearfix"></div>

    <br/>

    <!-- sidebar menu -->
    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
        <hr style="margin-top: 0px;"/>
        <div class="menu_section">
            <h3>General</h3>
            <ul class="nav side-menu">
                <li><a href="{{url('cf-back/dashboard')}}"><i class="fa fa-tachometer"></i> Dashboard</a></li>
                <li><a href="{{url('cf-back/lead/date')}}"><i
                                class="fa fa-clock-o"></i> @lang('labels.leads_appointment_list')</a></li>
            </ul>
        </div>
        <div class="menu_section">
            <h3>MASTER DATA</h3>
            <ul class="nav side-menu">
                <li><a><i class="fa fa-home"></i> @lang('labels.club') <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li><a href="{{url('cf-back/clubinfo')}}">@lang('labels.list') @lang('labels.club')</a></li>
                        <li><a href="{{url('cf-back/club/service-fee')}}">@lang('labels.club_service_fee')</a></li>
                    </ul>
                </li>
                <li><a><i class="fa fa-user"></i> @lang('labels.lead') <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li><a href="{{url('cf-back/lead')}}">@lang('labels.list') @lang('labels.lead')</a></li>
                    </ul>
                </li>
                <li><a><i class="fa fa-user-secret"></i> @lang('labels.user') <span
                                class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li><a href="{{url('cf-back/staff')}}">@lang('labels.staff')</a></li>
                        <li><a href="{{url('cf-back/member')}}">@lang('labels.member')</a></li>
                        {{--<li><a href="{{url('cf-back/staff/staff-hours')}}">@lang('labels.staff_hour')</a></li>--}}
                    </ul>
                </li>
                <li><a href="{{url('cf-back/role')}}"><i class="fa fa-shield"></i> @lang('labels.role')</a></li>
                <li><a href="{{url('cf-back/member-attribute')}}"><i
                                class="fa fa-bug"></i> @lang('labels.member_attribute')</a></li>

                <li><a><i class="fa fa-globe"></i> @lang('labels.location') <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li><a href="{{url('cf-back/country')}}">@lang('labels.country')</a></li>
                        <li><a href="{{url('cf-back/province')}}">@lang('labels.province')</a></li>
                        <li><a href="{{url('cf-back/city')}}">@lang('labels.city')</a></li>
                    </ul>
                </li>
                <li><a href="{{url('cf-back/tax')}}"><i class="fa fa-money"></i> @lang('labels.tax')</a></li>
            </ul>
        </div>

    </div>
    <!-- /sidebar menu -->

    <!-- /menu footer buttons -->
    <div class="sidebar-footer hidden-small">
        <a data-toggle="tooltip" data-placement="top" title="Settings">
            <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
        </a>
        <a data-toggle="tooltip" data-placement="top" title="FullScreen">
            <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
        </a>
        <a data-toggle="tooltip" data-placement="top" title="Lock">
            <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
        </a>
        <a data-toggle="tooltip" data-placement="top" title="Logout" href="login">
            <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
        </a>
    </div>
    <!-- /menu footer buttons -->
</div>