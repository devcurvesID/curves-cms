@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <style>
        .home-btn-title {
            font-family: 'Alegreya Sans', sans-serif;
        }

        .grey-bg {
            background-color: #efefef;
        }

        td {
            font-size: 14px;
        }

        @media print {
            #management-button-row {
                display: none;
            }
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @php($date = $_GET['date'])
    @php($membership_target = $_GET['membership_target'])
    @php($in_hold_target = $_GET['in_hold_target'])
    @php($const_total_wo_target = $_GET['const_total_wo_target'])
    @php($wo_avg_target = $_GET['wo_avg_target'])
    @php($wo_weekly_target = $_GET['wo_weekly_target'])
    @php($attrition_target = $_GET['attrition_target'])
    @php($wm_target = $_GET['wm_target'])
    @php($flag_a = $_GET['flag_a'])
    @php($flag_b = $_GET['flag_b'])
    @php($flag_c = $_GET['flag_c'])
    @php($flag_d = $_GET['flag_d'])
    @php($flag_e = $_GET['flag_e'])
    @php($showup_rate_target = $_GET['showup_rate_target'])
    @php($new_sales_target = $_GET['new_sales_target'])
    @php($ad_pp_target = $_GET['ad_pp_target'])
    @php($total_new_sales_weekly = [])
    @php($total_renewal_weekly = [])
    @php($total_assessment_weekly_pp18 = [])
    @php($total_assessment_weekly_pp12 = [])
    @php($total_assessment_weekly_ad12 = [])
    @php($total_assessment_weekly_st12 = [])
    @php($total_assessment_weekly_st6 = [])
    @php($total_assessment_weekly_mm = [])
    @php($total_assessment_weekly_ns = [])
    @php($total_member = 0)
    @php($total_active_member = 0)
    @php($total_attrition_projection = 0)
    @php($total_show_up = 0)
    @php($total_sale = 0)
    @php($total_wm_target = 0)
    @php($total_appointment_from_source = 0)
    @php($total_attending_guest = 0)
    @php($total_new_sales_pp18 = 0)
    @php($total_new_sales_pp12 = 0)
    @php($total_new_sales_ad12 = 0)
    @php($total_new_sales_st12 = 0)
    @php($total_new_sales_st6 = 0)
    @php($total_new_sales_mm = 0)
    @php($total_new_sales = 0)
    @php($total_renewal_pp18 = 0)
    @php($total_renewal_pp12 = 0)
    @php($total_renewal_ad12 = 0)
    @php($total_renewal_st12 = 0)
    @php($total_renewal_st6 = 0)
    @php($total_renewal_mm = 0)
    @php($total_renewal = 0)
    @php($total_assessment_pp18 = 0)
    @php($total_assessment_pp12 = 0)
    @php($total_assessment_ad12 = 0)
    @php($total_assessment_st12 = 0)
    @php($total_assessment_st6 = 0)
    @php($total_assessment_mm = 0)
    @php($total_assessment_ns = 0)
    @php($thirtyday = 0)
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/club-assessment')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">
                        @lang('labels.club_assessment')<br><small>({{\Carbon\Carbon::parse($date)->format('F Y')}})</small>
                    </h2>
                </center>
            </div>
        </div>
        <div class="row">

            <div class="col-md-10 col-md-offset-1">
                <a id="my-btn-print" class="btn btn-primary" style="margin-bottom: 25px;"><i class="fa fa-print"></i> @lang('labels.print')</a>
            </div>

            <div class="col-md-10 col-md-offset-1">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>@lang('labels.total_membership_goal')</th>
                                <th class="text-center">{{$membership_target}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>@lang('labels.TM') - @lang('labels.total_member') @lang('labels.last_month')</td>
                                <td class="text-center">
                                    {{$tm}}
                                    @php($total_member = $tm)
                                </td>
                            </tr>
                            <tr>
                                <td>@lang('labels.TAM') - @lang('labels.total_active_member') @lang('labels.last_month')</td>
                                <td class="text-center">
                                    {{$tam}}
                                    @php($total_active_member = $tam)
                                </td>
                            </tr>
                            <tr>
                                <td>@lang('labels.show_up') % @lang('labels.last_month')</td>
                                <td class="text-center">
                                    @php($total_show_up = round(($show_up_guests / $total_fa) * 100))
                                    {{$total_show_up}}%
                                </td>
                            </tr>
                            <tr>
                                <td>@lang('labels.sales') % @lang('labels.last_month')</td>
                                <td class="text-center">
                                    @php($total_sale = round(($sale_fas / $total_fa) * 100))
                                    {{$total_sale}}%
                                </td>
                            </tr>
                            <tr>
                                <td>@lang('labels.attrition%projection') @lang('labels.last_month')</td>
                                <td class="text-center">
                                    @if(!empty($_GET['attrition_projection']))
                                        @php($total_attrition_projection = $_GET['attrition_projection'])
                                    @endif
                                    {{$total_attrition_projection}}%
                                </td>
                            </tr>
                            <tr>
                                <td>@lang('labels.expexted_completion_of_30_day_challenge_this_month')</td>
                                <td class="text-center">
                                    @if(!empty($fwos))
                                        @php($thirtyday = $fwos)
                                    @endif
                                    {{ceil($thirtyday * 0.8)}}%
                                </td>
                            </tr>
                            <tr>
                                <td>@lang('labels.expexted_completion_of_90_day_challenge_this_month')</td>
                                <td class="text-center">
                                    {{ceil($thirtyday * 0.8 * 0.8)}}%
                                </td>
                            </tr>
                            <tr>
                                <td>@lang('labels.new_sales_needed_this_month')</td>
                                <td class="text-center">{{($membership_target * (1 + $total_attrition_projection)) - $total_member}}</td>
                            </tr>
                            <tr>
                                <td>@lang('labels.appointments_needed_this_month')</td>
                                <td class="text-center">{{round((($membership_target * (1 + $total_attrition_projection)) - $total_member)/($total_show_up * 0.01)/($total_sale * 0.01))}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <br>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">@lang('labels.goal')</th>
                                <th>@lang('labels.workout')/@lang('labels.member_experience')</th>
                                <th class="text-center">@lang('labels.mg')</th>
                                <th class="text-center">1</th>
                                <th class="text-center">2</th>
                                <th class="text-center">3</th>
                                <th class="text-center">4</th>
                                <th class="text-center">5</th>
                                <th class="text-center">@lang('labels.monthly')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td>@lang('labels.TM') - @lang('labels.total_membership')</td>
                                <td></td>
                                <td>{{$member_collective[0]}}</td>
                                <td>{{$member_collective[1]}}</td>
                                <td>{{$member_collective[2]}}</td>
                                <td>{{$member_collective[3]}}</td>
                                <td>
                                    @if(!empty($member_collective[4]))
                                        {{$member_collective[4]}}
                                    @else
                                        0
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($member_collective[4]))
                                        {{$member_collective[4]}}
                                    @else
                                        {{$member_collective[3]}}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>@lang('labels.total_member_in_hold')</td>
                                <td></td>
                                <td>{{$member_hold[0]}}</td>
                                <td>{{$member_hold[1]}}</td>
                                <td>{{$member_hold[2]}}</td>
                                <td>{{$member_hold[3]}}</td>
                                <td>
                                    @if(!empty($member_hold[4]))
                                        {{$member_hold[4]}}
                                    @else
                                        0
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($member_hold[4]))
                                        {{$member_hold[4]}}
                                    @else
                                        {{$member_hold[3]}}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>@lang('labels.free_membership')</td>
                                <td></td>
                                <td>{{$free_membership[0]}}</td>
                                <td>{{$free_membership[1]}}</td>
                                <td>{{$free_membership[2]}}</td>
                                <td>{{$free_membership[3]}}</td>
                                <td>
                                    @if(!empty($free_membership[4]))
                                        {{$free_membership[4]}}
                                    @else
                                        0
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($free_membership[4]))
                                        {{$free_membership[4]}}
                                    @else
                                        {{$free_membership[3]}}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>@lang('labels.TAM') - @lang('labels.total_active_membership')</td>
                                <td></td>
                                @for($i = 0; $i < 5; $i++)
                                    @if($i == 4)
                                        <td>
                                            @if(!empty($member_collective[$i]) || !empty($member_hold[$i]))
                                                {{$member_collective[$i] - $member_hold[$i]}}
                                            @else
                                                {{$member_collective[$i - 1] - $member_hold[$i - 1]}}
                                            @endif
                                        </td>
                                    @else
                                        <td>{{$member_collective[$i] - $member_hold[$i]}}</td>
                                    @endif
                                @endfor
                                <td>
                                    @if(!empty($member_collective[4]) || !empty($member_hold[4]))
                                        {{$member_collective[4] - $member_hold[4]}}
                                    @else
                                        {{$member_collective[3] - $member_hold[3]}}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center">
                                    {{-- in hold target --}}
                                    < {{$in_hold_target}}%
                                </th>
                                <td>@lang('labels.total_member_in_hold') %</td>
                                <td class="text-center">
                                    @if(!empty($member_collective[4]) || !empty($member_hold[4]))
                                        @php($the_in_hold = round($member_hold[4] / $member_collective[4]))
                                    @else
                                        @php($the_in_hold = round($member_hold[3] / $member_collective[3]))
                                    @endif

                                    @if($the_in_hold < $in_hold_target)
                                        <span class="fa fa-star"></span>
                                    @endif
                                </td>
                                @for($i = 0; $i < 5; $i++)
                                    @if($i == 4)
                                        <td>
                                            @if(!empty($member_collective[$i]) || !empty($member_hold[$i]))
                                                {{ round($member_hold[$i] / $member_collective[$i])}}
                                            @else
                                                {{ round($member_hold[$i - 1] / $member_collective[$i - 1])}}
                                            @endif
                                            %
                                        </td>
                                    @else
                                        <td>{{ round($member_hold[$i] / $member_collective[$i])}}%</td>
                                    @endif
                                @endfor
                                <td>
                                    @if(!empty($member_collective[4]) || !empty($member_hold[4]))
                                        {{ round($member_hold[4] / $member_collective[4])}}
                                    @else
                                        {{ round($member_hold[3] / $member_collective[3])}}
                                    @endif
                                    %
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>@lang('labels.total_fwo_member')</td>
                                <td></td>
                                <td>{{round($fwo_weekly[0] / $member_collective[0] * 100)}}%</td>
                                <td>{{round($fwo_weekly[1] / $member_collective[1] * 100)}}%</td>
                                <td>{{round($fwo_weekly[2] / $member_collective[2] * 100)}}%</td>
                                <td>{{round($fwo_weekly[3] / $member_collective[3] * 100)}}%</td>
                                <td>
                                    @if(!empty($fwo_weekly[4]))
                                        {{round($fwo_weekly[4] / $member_collective[4] * 100)}}%
                                    @else
                                        0%
                                    @endif
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>@lang('labels.total_didnt_do_fwo')</td>
                                <td></td>
                                <td>{{round($not_fwo_weekly[0] / $member_collective[0] * 100)}}%</td>
                                <td>{{round($not_fwo_weekly[1] / $member_collective[1] * 100)}}%</td>
                                <td>{{round($not_fwo_weekly[2] / $member_collective[2] * 100)}}%</td>
                                <td>{{round($not_fwo_weekly[3] / $member_collective[3] * 100)}}%</td>
                                <td>
                                    @if(!empty($not_fwo_weekly[4]))
                                        {{round($not_fwo_weekly[4] / $member_collective[4] * 100)}}%
                                    @else
                                        0%
                                    @endif
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>@lang('labels.total_member_finished_30_days_challenge')</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>@lang('labels.total_member_finished_90_days_challenge')</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>

                            {{-- Total Workouts Section --}}
                            <tr>
                                <th class="text-center">
                                    {{-- Total Workout Target --}}
                                    > {{$const_total_wo_target}} x @lang('labels.TAM')
                                </th>
                                <th>@lang('labels.total_workout')</th>
                                <th>
                                    @if(!empty($member_collective[4]) || !empty($member_hold[4]))
                                        @php($the_tam = $member_collective[4] - $member_hold[4])
                                    @else
                                        @php($the_tam = $member_collective[3] - $member_hold[3])
                                    @endif

                                    @if($total_wo_this_month > ($const_total_wo_target * $the_tam))
                                        <span class="fa fa-star"></span>
                                    @endif
                                </th>
                                @for($i = 0; $i < 5; $i++)
                                    @if($i == 4)
                                        <td>
                                            @if(!empty($total_wo[$i]))
                                                {{ $total_wo[$i] }}
                                            @else
                                                0
                                            @endif
                                        </td>
                                    @else
                                        <td>{{ $total_wo[$i] }}</td>
                                    @endif
                                @endfor
                                <td>{{$total_wo_this_month}}</td>
                            </tr>
                            <tr>
                                <th class="text-center">
                                    {{-- Total workout average --}}
                                    > {{$wo_avg_target}}
                                </th>
                                <td>@lang('labels.average_workout')/@lang('labels.active_member')/@lang('labels.week')</td>
                                <th>
                                    @if(!empty($member_collective[4]) || !empty($member_hold[4]))
                                        @php($the_avg_wo = $total_wo_this_month/($member_collective[4] - $member_hold[4])/4)
                                    @else
                                        @php($the_avg_wo = $total_wo_this_month/($member_collective[3] - $member_hold[3])/4)
                                    @endif

                                    @if($the_avg_wo > $wo_avg_target)
                                        <span class="fa fa-star"></span>
                                    @endif
                                </th>
                                @for($i = 0; $i < 5; $i++)
                                    @if($i == 4)
                                        <td>
                                            @if(!empty($total_wo[$i]) && (!empty($member_collective[$i]) || !empty($member_hold[$i])))
                                                {{number_format(($total_wo[$i] / ($member_collective[$i] - $member_hold[$i])), 1)}}
                                            @else
                                                0.0
                                            @endif
                                        </td>
                                    @else
                                        <td>{{number_format(($total_wo[$i] / ($member_collective[$i] - $member_hold[$i])), 1)}}</td>
                                    @endif
                                @endfor
                                <td>
                                    @if(!empty($member_collective[4]) || !empty($member_hold[4]))
                                        {{number_format(($total_wo_this_month/($member_collective[4] - $member_hold[4])/4), 1)}}
                                    @else
                                        {{number_format(($total_wo_this_month/($member_collective[3] - $member_hold[3])/4), 1)}}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center">
                                    {{-- Highest Traffic Day --}}
                                    > {{$wo_weekly_target}}%@lang('labels.TAM')
                                </th>
                                <td>@lang('labels.highest_traffic_day') (@lang('labels.monthly') %)</td>
                                <th>
                                    @if(!empty($member_collective[4]) || !empty($member_hold[4]))
                                        @php($the_wo_weekly = max($total_wo_weekly)/($member_collective[4] - $member_hold[4]))
                                    @else
                                        @php($the_wo_weekly = max($total_wo_weekly)/($member_collective[3] - $member_hold[3]))
                                    @endif

                                    @if($the_wo_weekly > $wo_weekly_target)
                                        <span class="fa fa-star"></span>
                                    @endif
                                </th>
                                @for($i = 0; $i < 5; $i++)
                                    @if($i == 4)
                                        <td>
                                            @if(!empty($total_wo_weekly[$i]))
                                                {{$total_wo_weekly[$i]}}
                                            @else
                                                0
                                            @endif
                                        </td>
                                    @else
                                        <td>{{$total_wo_weekly[$i]}}</td>
                                    @endif
                                @endfor
                                <td>
                                    @if(!empty($member_collective[4]) || !empty($member_hold[4]))
                                        {{round(max($total_wo_weekly)/($member_collective[4] - $member_hold[4]))}}
                                    @else
                                        {{round(max($total_wo_weekly)/($member_collective[3] - $member_hold[3]))}}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>@lang('labels.cancellation')</td>
                                <td></td>
                                @for($i = 0; $i < 5; $i++)
                                    @if($i == 4)
                                        <td>
                                            @if(!empty($total_cancellation_weekly[$i]))
                                                {{$total_cancellation_weekly[$i]}}
                                            @else
                                                0
                                            @endif
                                        </td>
                                    @else
                                        <td>{{$total_cancellation_weekly[$i]}}</td>
                                    @endif
                                @endfor
                                <td>{{$total_cancellation}}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>@lang('labels.cancellation_before_fwo')</td>
                                <td></td>
                                @for($i = 0; $i < 5; $i++)
                                    @if($i == 4)
                                        <td>
                                            @if(!empty($cancellation_bfwo[$i]))
                                                {{$cancellation_bfwo[$i]}}
                                            @else
                                                0
                                            @endif
                                        </td>
                                    @else
                                        <td>{{$cancellation_bfwo[$i]}}</td>
                                    @endif
                                @endfor
                                <td>{{$total_cancellation_bfwo}}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>@lang('labels.cancellation_after_fwo')</td>
                                <td></td>
                                @for($i = 0; $i < 5; $i++)
                                    @if($i == 4)
                                        <td>
                                            @if(!empty($cancellation_afwo[$i]))
                                                {{$cancellation_afwo[$i]}}
                                            @else
                                                0
                                            @endif
                                        </td>
                                    @else
                                        <td>{{$cancellation_afwo[$i]}}</td>
                                    @endif
                                @endfor
                                <td>{{$total_cancellation_afwo}}</td>
                            </tr>
                            <tr>
                                <th class="text-center">< {{$attrition_target}}%</th>
                                <td>@lang('labels.attrition') (@lang('labels.less_than') {{$attrition_target}}% @lang('labels.TM')/m @lang('labels.or') 1% @lang('labels.TM')/@lang('labels.week'))</td>
                                <td class="text-center">
                                    @if(!empty($member_collective[4]))
                                        @php($the_attrition = $total_cancellation * 100 / $member_collective[4])
                                    @else
                                        @php($the_attrition = $total_cancellation * 100 / $member_collective[3])
                                    @endif

                                    @if($the_attrition > $attrition_target)
                                        <span class="fa fa-star"></span>
                                    @endif
                                </td>
                                @for($i = 0; $i < 5; $i++)
                                    @if($i == 4)
                                        <td>
                                            @if(!empty($total_cancellation_weekly[$i]) || !empty($member_collective[$i]))
                                                {{number_format(($total_cancellation_weekly[$i] * 100 / $member_collective[$i]), 1)}}%
                                            @else
                                                0%
                                            @endif
                                        </td>
                                    @else
                                        <td>{{number_format(($total_cancellation_weekly[$i] * 100 / $member_collective[$i]), 1)}}%</td>
                                    @endif
                                @endfor
                                <td>
                                    @if(!empty($member_collective[4]))
                                        {{number_format(($total_cancellation * 100 / $member_collective[4]), 1)}}
                                    @else
                                        {{number_format(($total_cancellation * 100 / $member_collective[3]), 1)}}
                                    @endif
                                    %
                                </td>
                            </tr>

                            {{-- Cancellation during the Contract --}}
                            <tr>
                                <td></td>
                                <th>@lang('labels.cancellation_during_the_contract')</th>
                                <td colspan="7"></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>@lang('labels.pp_18_months')</td>
                                <td></td>
                                @for($i = 0; $i < 5; $i++)
                                    @if($i == 4)
                                        <td>
                                            @if(!empty($cancellation_pp18[$i]))
                                                {{$cancellation_pp18[$i]}}
                                            @else
                                                0
                                            @endif
                                        </td>
                                    @else
                                        <td>{{$cancellation_pp18[$i]}}</td>
                                    @endif
                                @endfor
                                <td>{{$total_cancellation_pp18}}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>@lang('labels.pp_12_months')</td>
                                <td></td>
                                @for($i = 0; $i < 5; $i++)
                                    @if($i == 4)
                                        <td>
                                            @if(!empty($cancellation_pp12[$i]))
                                                {{$cancellation_pp12[$i]}}
                                            @else
                                                0
                                            @endif
                                        </td>
                                    @else
                                        <td>{{$cancellation_pp12[$i]}}</td>
                                    @endif
                                @endfor
                                <td>{{$total_cancellation_pp12}}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>@lang('labels.ad_12_months')</td>
                                <td></td>
                                @for($i = 0; $i < 5; $i++)
                                    @if($i == 4)
                                        <td>
                                            @if(!empty($cancellation_ad12[$i]))
                                                {{$cancellation_ad12[$i]}}
                                            @else
                                                0
                                            @endif
                                        </td>
                                    @else
                                        <td>{{$cancellation_ad12[$i]}}</td>
                                    @endif
                                @endfor
                                <td>{{$total_cancellation_ad12}}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>@lang('labels.st_12_months')</td>
                                <td></td>
                                @for($i = 0; $i < 5; $i++)
                                    @if($i == 4)
                                        <td>
                                            @if(!empty($cancellation_st12[$i]))
                                                {{$cancellation_st12[$i]}}
                                            @else
                                                0
                                            @endif
                                        </td>
                                    @else
                                        <td>{{$cancellation_st12[$i]}}</td>
                                    @endif
                                @endfor
                                <td>{{$total_cancellation_st12}}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>@lang('labels.st_6_months')</td>
                                <td></td>
                                @for($i = 0; $i < 5; $i++)
                                    @if($i == 4)
                                        <td>
                                            @if(!empty($cancellation_st6[$i]))
                                                {{$cancellation_st6[$i]}}
                                            @else
                                                0
                                            @endif
                                        </td>
                                    @else
                                        <td>{{$cancellation_st6[$i]}}</td>
                                    @endif
                                @endfor
                                <td>{{$total_cancellation_st6}}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>@lang('labels.month_to_month')</td>
                                <td></td>
                                @for($i = 0; $i < 5; $i++)
                                    @if($i == 4)
                                        <td>
                                            @if(!empty($cancellation_mm[$i]))
                                                {{$cancellation_mm[$i]}}
                                            @else
                                                0
                                            @endif
                                        </td>
                                    @else
                                        <td>{{$cancellation_mm[$i]}}</td>
                                    @endif
                                @endfor
                                <td>{{$total_cancellation_mm}}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <th>@lang('labels.total_weigh_n_measure')</th>
                                <td colspan="6" class="grey-bg"></td>
                                <td>{{$total_wm}}</td>
                            </tr>
                            <tr>
                                <th class="text-center"> > {{$wm_target}}% @lang('labels.TAM')</th>
                                <th>@lang('labels.weigh_n_measure') %</th>
                                <th class="text-center">
                                    @if(!empty($member_collective[4]) || !empty($member_hold[4]))
                                        @php($the_wm = $total_wm / ($member_collective[4] - $member_hold[4]))
                                    @else
                                        @php($the_wm = $total_wm / ($member_collective[3] - $member_hold[3]))
                                    @endif

                                    @if($the_wm > $wm_target)
                                        <span class="fa fa-star"></span>
                                    @endif
                                </th>
                                <td colspan="5" class="grey-bg"></td>
                                <td>
                                    @if(!empty($member_collective[4]) || !empty($member_hold[4]))
                                        {{round($total_wm / ($member_collective[4] - $member_hold[4]))}}
                                    @else
                                        {{round($total_wm / ($member_collective[3] - $member_hold[3]))}}
                                    @endif
                                    %
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center"> > {{$flag_a}}%</th>
                                <td>A: @lang('labels.member_wo') 12+ @lang('labels.times_per_month')</td>
                                <td class="text-center">
                                    @if(!empty($member_collective[4]) || !empty($member_hold[4]))
                                        @php($the_flag_a = count($wo_flags->where('wo_flag', 'A')) / ($member_collective[4] - $member_hold[4]))
                                    @else
                                        @php($the_flag_a = count($wo_flags->where('wo_flag', 'A')) / ($member_collective[3] - $member_hold[3]))
                                    @endif

                                    @if($the_flag_a > $flag_a)
                                        <span class="fa fa-star"></span>
                                    @endif
                                </td>
                                <td colspan="3" class="grey-bg"></td>
                                <th class="text-center">A</th>
                                <td>{{count($wo_flags->where('wo_flag', 'A'))}}</td>
                                <td>
                                    @if(!empty($member_collective[4]) || !empty($member_hold[4]))
                                        {{round(count($wo_flags->where('wo_flag', 'A')) * 100 / ($member_collective[4] - $member_hold[4]))}}
                                    @else
                                        {{round(count($wo_flags->where('wo_flag', 'A')) * 100 / ($member_collective[3] - $member_hold[3]))}}
                                    @endif
                                    %
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center">{{$flag_b}}%</th>
                                <td>B: @lang('labels.member_wo') 8-11 @lang('labels.times_per_month')</td>
                                <td class="text-center">
                                    @if(!empty($member_collective[4]) || !empty($member_hold[4]))
                                        @php($the_flag_b = count($wo_flags->where('wo_flag', 'B')) / ($member_collective[4] - $member_hold[4]))
                                    @else
                                        @php($the_flag_b = count($wo_flags->where('wo_flag', 'B')) / ($member_collective[3] - $member_hold[3]))
                                    @endif

                                    @if($the_flag_b > $flag_b)
                                        <span class="fa fa-star"></span>
                                    @endif
                                </td>
                                <td colspan="3" class="grey-bg"></td>
                                <th class="text-center">B</th>
                                <td>{{count($wo_flags->where('wo_flag', 'B'))}}</td>
                                <td>
                                    @if(!empty($member_collective[4]) || !empty($member_hold[4]))
                                        {{round(count($wo_flags->where('wo_flag', 'B')) * 100 / ($member_collective[4] - $member_hold[4]))}}
                                    @else
                                        {{round(count($wo_flags->where('wo_flag', 'B')) * 100 / ($member_collective[3] - $member_hold[3]))}}
                                    @endif
                                    %
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center">{{$flag_c}}%</th>
                                <td>C: @lang('labels.member_wo') 4-7 @lang('labels.times_per_month')</td>
                                <td class="text-center"></td>
                                <td colspan="3" class="grey-bg"></td>
                                <th class="text-center">C</th>
                                <td>{{count($wo_flags->where('wo_flag', 'C'))}}</td>
                                <td>
                                    @if(!empty($member_collective[4]) || !empty($member_hold[4]))
                                        {{round(count($wo_flags->where('wo_flag', 'C')) * 100 / ($member_collective[4] - $member_hold[4]))}}
                                    @else
                                        {{round(count($wo_flags->where('wo_flag', 'C')) * 100 / ($member_collective[3] - $member_hold[3]))}}
                                    @endif
                                    %
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center">{{$flag_d}}%</th>
                                <td>D: @lang('labels.member_wo') 1-3 @lang('labels.times_per_month')</td>
                                <td class="text-center"></td>
                                <td colspan="3" class="grey-bg"></td>
                                <th class="text-center">D</th>
                                <td>{{count($wo_flags->where('wo_flag', 'D'))}}</td>
                                <td>
                                    @if(!empty($member_collective[4]) || !empty($member_hold[4]))
                                        {{round(count($wo_flags->where('wo_flag', 'D')) * 100 / ($member_collective[4] - $member_hold[4]))}}
                                    @else
                                        {{round(count($wo_flags->where('wo_flag', 'D')) * 100 / ($member_collective[3] - $member_hold[3]))}}
                                    @endif
                                    %
                                </td>
                            </tr>
                            <tr>
                                <th class="text-center">{{$flag_e}}%</th>
                                <td>E: @lang('labels.member_wo') 0 @lang('labels.times_per_month')</td>
                                <td class="text-center"></td>
                                <td colspan="3" class="grey-bg"></td>
                                <th class="text-center">E</th>
                                <td>{{count($wo_flags->where('wo_flag', 'E'))}}</td>
                                <td>
                                    @if(!empty($member_collective[4]) || !empty($member_hold[4]))
                                        {{round(count($wo_flags->where('wo_flag', 'E')) * 100 / ($member_collective[4] - $member_hold[4]))}}
                                    @else
                                        {{round(count($wo_flags->where('wo_flag', 'E')) * 100 / ($member_collective[3] - $member_hold[3]))}}
                                    @endif
                                    %
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <br>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th colspan="9">@lang('labels.guest_production')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th colspan="2">@lang('labels.appointment')</th>
                                <th class="text-center">@lang('labels.mg')</th>
                                <th class="text-center">1</th>
                                <th class="text-center">2</th>
                                <th class="text-center">3</th>
                                <th class="text-center">4</th>
                                <th class="text-center">5</th>
                                <th class="text-center">@lang('labels.monthly')</th>
                            </tr>
                            @if(count($sources) > 0)
                                @if(count($sources->where('source_type', 'BRs')))
                                    @php($counter = 0)
                                    @foreach($sources->where('source_type', 'BRs') as $br_source)
                                        @php($row_source[$br_source->id] = 0)
                                        <tr>
                                            @if($counter == 0)
                                                <td rowspan="{{count($sources->where('source_type', 'BRs'))}}" class="text-center">BR</td>
                                                @php($counter++)
                                            @endif
                                            <td>{{$br_source->source}}</td>
                                            <td></td>
                                            @for($i = 0; $i < 5; $i++)
                                                <td class="text-center">
                                                    {{$member_from_source[$br_source->id][$i]}}
                                                    @php($row_source[$br_source->id] = $row_source[$br_source->id] + $member_from_source[$br_source->id][$i])
                                                    @php($total_appointment_from_source = $total_appointment_from_source + $member_from_source[$br_source->id][$i])
                                                </td>
                                            @endfor
                                            <td class="text-center">{{$row_source[$br_source->id]}}</td>
                                        </tr>
                                    @endforeach
                                @endif

                                @if(count($sources->where('source_type', 'Branding')))
                                    @php($counter = 0)
                                    @foreach($sources->where('source_type', 'Branding') as $br_source)
                                        @php($row_source[$br_source->id] = 0)
                                        <tr>
                                            @if($counter == 0)
                                                <td rowspan="{{count($sources->where('source_type', 'Branding'))}}" class="text-center">Branding</td>
                                                @php($counter++)
                                            @endif
                                            <td>{{$br_source->source}}</td>
                                            <td></td>
                                            @for($i = 0; $i < 5; $i++)
                                                <td class="text-center">
                                                    {{$member_from_source[$br_source->id][$i]}}
                                                    @php($row_source[$br_source->id] = $row_source[$br_source->id] + $member_from_source[$br_source->id][$i])
                                                    @php($total_appointment_from_source = $total_appointment_from_source + $member_from_source[$br_source->id][$i])
                                                </td>
                                            @endfor
                                            <td class="text-center">{{$row_source[$br_source->id]}}</td>
                                        </tr>
                                    @endforeach
                                @endif

                                @if(count($sources->where('source_type', 'AGP')))
                                    @php($counter = 0)
                                    @foreach($sources->where('source_type', 'AGP') as $br_source)
                                        @php($row_source[$br_source->id] = 0)
                                        <tr>
                                            @if($counter == 0)
                                                <td rowspan="{{count($sources->where('source_type', 'AGP'))}}" class="text-center">AGP</td>
                                                @php($counter++)
                                            @endif
                                            <td>{{$br_source->source}}</td>
                                            <td></td>
                                            @for($i = 0; $i < 5; $i++)
                                                <td class="text-center">
                                                    {{$member_from_source[$br_source->id][$i]}}
                                                    @php($row_source[$br_source->id] = $row_source[$br_source->id] + $member_from_source[$br_source->id][$i])
                                                    @php($total_appointment_from_source = $total_appointment_from_source + $member_from_source[$br_source->id][$i])
                                                </td>
                                            @endfor
                                            <td class="text-center">{{$row_source[$br_source->id]}}</td>
                                        </tr>
                                    @endforeach
                                @endif

                                <tr>
                                    <th class="text-center">{{round((($membership_target * (1 + $total_attrition_projection)) - $total_member)/($total_show_up * 0.01)/($total_sale * 0.01))}}</th>
                                    <th>@lang('labels.total_appointment')</th>
                                    <th></th>
                                    @for($i = 0; $i < 5; $i++)
                                        @php($appointment_weekly[$i] = 0)
                                        <th class="text-center">
                                            @foreach($sources as $source)
                                                @php($appointment_weekly[$i] = $appointment_weekly[$i] + $member_from_source[$source->id][$i])
                                            @endforeach
                                            {{$appointment_weekly[$i]}}
                                        </th>
                                    @endfor
                                    <th class="text-center">{{$total_appointment_from_source}}</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th>@lang('labels.TOG') - @lang('labels.total_attending_guest') (@lang('labels.fa'))</th>
                                    <th></th>
                                    @for($i = 0; $i < 5; $i++)
                                        <th class="text-center">
                                            {{$attending_guest[$i]}}
                                            @php($total_attending_guest = $total_attending_guest + $attending_guest[$i])
                                        </th>
                                    @endfor
                                    <th class="text-center">{{$total_attending_guest}}</th>
                                </tr>
                                <tr>
                                    <th class="text-center"> > {{$showup_rate_target}}%</th>
                                    <th>@lang('labels.show_up_rate')</th>
                                    <th class="text-center">
                                        @if(($total_attending_guest * 100 / $total_appointment_from_source) > $showup_rate_target)
                                            <span class="fa fa-star"></span>
                                        @endif
                                    </th>
                                    @for($i = 0; $i < 5; $i++)
                                        <td class="text-center">
                                            @if($appointment_weekly[$i] == 0)
                                                {{round($attending_guest[$i] / 1) * 100}}%
                                            @else
                                                {{round(($attending_guest[$i] / $appointment_weekly[$i]) * 100)}}%
                                            @endif
                                        </td>
                                    @endfor
                                    <th class="text-center">{{round(($total_attending_guest / $total_appointment_from_source) * 100)}}%</th>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <br>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th colspan="2">@lang('labels.new_sales')</th>
                                <th class="text-center">@lang('labels.mg')</th>
                                <th class="text-center">1</th>
                                <th class="text-center">2</th>
                                <th class="text-center">3</th>
                                <th class="text-center">4</th>
                                <th class="text-center">5</th>
                                <th class="text-center">@lang('labels.monthly')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td>@lang('labels.pp_18_months')</td>
                                <td></td>
                                @for($i = 0; $i < 5; $i++)
                                    <td class="text-center">
                                        {{$new_sales_pp18[$i]}}
                                        @php($total_new_sales_pp18 = $total_new_sales_pp18 + $new_sales_pp18[$i])
                                        @php($total_new_sales_weekly[$i] = $new_sales_pp18[$i])
                                    </td>
                                @endfor
                                <td class="text-center">{{$total_new_sales_pp18}}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>@lang('labels.pp_12_months')</td>
                                <td></td>
                                @for($i = 0; $i < 5; $i++)
                                    <td class="text-center">
                                        {{$new_sales_pp12[$i]}}
                                        @php($total_new_sales_pp12 = $total_new_sales_pp12 + $new_sales_pp12[$i])
                                        @php($total_new_sales_weekly[$i] = $total_new_sales_weekly[$i] + $new_sales_pp12[$i])
                                    </td>
                                @endfor
                                <td class="text-center">{{$total_new_sales_pp12}}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>@lang('labels.ad_12_months')</td>
                                <td></td>
                                @for($i = 0; $i < 5; $i++)
                                    <td class="text-center">
                                        {{$new_sales_ad12[$i]}}
                                        @php($total_new_sales_ad12 = $total_new_sales_ad12 + $new_sales_ad12[$i])
                                        @php($total_new_sales_weekly[$i] = $total_new_sales_weekly[$i] + $new_sales_ad12[$i])
                                    </td>
                                @endfor
                                <td class="text-center">{{$total_new_sales_ad12}}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>@lang('labels.st_12_months')</td>
                                <td></td>
                                @for($i = 0; $i < 5; $i++)
                                    <td class="text-center">
                                        {{$new_sales_st12[$i]}}
                                        @php($total_new_sales_st12 = $total_new_sales_st12 + $new_sales_st12[$i])
                                        @php($total_new_sales_weekly[$i] = $total_new_sales_weekly[$i] + $new_sales_st12[$i])
                                    </td>
                                @endfor
                                <td class="text-center">{{$total_new_sales_st12}}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>@lang('labels.st_6_months')</td>
                                <td></td>
                                @for($i = 0; $i < 5; $i++)
                                    <td class="text-center">
                                        {{$new_sales_st6[$i]}}
                                        @php($total_new_sales_st6 = $total_new_sales_st6 + $new_sales_st6[$i])
                                        @php($total_new_sales_weekly[$i] = $total_new_sales_weekly[$i] + $new_sales_st6[$i])
                                    </td>
                                @endfor
                                <td class="text-center">{{$total_new_sales_st6}}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>@lang('labels.month_to_month')</td>
                                <td></td>
                                @for($i = 0; $i < 5; $i++)
                                    <td class="text-center">
                                        {{$new_sales_mm[$i]}}
                                        @php($total_new_sales_mm = $total_new_sales_mm + $new_sales_mm[$i])
                                        @php($total_new_sales_weekly[$i] = $total_new_sales_weekly[$i] + $new_sales_mm[$i])
                                    </td>
                                @endfor
                                <td class="text-center">{{$total_new_sales_mm}}</td>
                            </tr>
                            <tr>
                                <th class="text-center">{{($membership_target * (1 + $total_attrition_projection)) - $total_member}}</th>
                                <th>@lang('labels.total_sales')</th>
                                <th></th>
                                @for($i = 0; $i < 5; $i++)
                                    <th class="text-center">
                                        {{$total_new_sales_weekly[$i]}}
                                        @php($total_new_sales = $total_new_sales + $total_new_sales_weekly[$i])
                                    </th>
                                @endfor
                                <th class="text-center">{{$total_new_sales}}</th>
                            </tr>
                            <tr>
                                <th class="text-center"> > {{$new_sales_target}}%</th>
                                <th>@lang('labels.sales') %</th>
                                <th class="text-center">
                                    @php($new_sales_gate = 0)
                                    @if($total_attending_guest == 0)
                                        {{--@if(($total_new_sales / $total_attending_guest * 100) > $new_sales_target)--}}
                                            {{--@php($new_sales_gate = 1)--}}
                                        {{--@endif--}}
                                    @else
                                        @if(($total_new_sales * 100) > $new_sales_target)
                                            @php($new_sales_gate = 1)
                                        @endif

                                        @if(($total_new_sales / $total_attending_guest * 100) > $new_sales_target)
                                            @php($new_sales_gate = 1)
                                        @endif
                                    @endif

                                    @if($new_sales_gate == 1)
                                        <span class="fa fa-star"></span>
                                    @endif
                                </th>
                                @for($i = 0; $i < 5; $i++)
                                    <th class="text-center">
                                        @if($attending_guest[$i] == 0)
                                            {{round($total_new_sales_weekly[$i] * 100)}}
                                        @else
                                            {{round($total_new_sales_weekly[$i] / $attending_guest[$i] * 100)}}
                                        @endif
                                        %
                                    </th>
                                @endfor
                                <th class="text-center">
                                    @if($total_attending_guest == 0)
                                        {{round($total_new_sales * 100)}}
                                    @else
                                        {{round($total_new_sales / $total_attending_guest * 100)}}
                                    @endif
                                    %
                                </th>
                            </tr>
                            <tr>
                                <th class="text-center"> > {{$ad_pp_target}} %</th>
                                <th>@lang('labels.AD') + @lang('labels.PP') %</th>
                                <th class="text-center">
                                    @php($ad_pp_gate = 0)
                                    @if($total_new_sales == 0)
                                        @if((($total_new_sales - $total_new_sales_mm) * 100) > $ad_pp_target)
                                            @php($ad_pp_gate = 1)
                                        @endif
                                    @else
                                        @if((($total_new_sales - $total_new_sales_mm) / $total_new_sales * 100) > $ad_pp_target)
                                            @php($ad_pp_gate = 1)
                                        @endif
                                    @endif

                                    @if($ad_pp_gate == 1)
                                        <span class="fa fa-star"></span>
                                    @endif
                                </th>
                                @for($i = 0; $i < 5; $i++)
                                    <th class="text-center">
                                        @if($total_new_sales_weekly[$i] == 0)
                                            {{round(($total_new_sales_weekly[$i] - $new_sales_mm[$i]) * 100)}}
                                        @else
                                            {{round(($total_new_sales_weekly[$i] - $new_sales_mm[$i]) / $total_new_sales_weekly[$i] * 100)}}
                                        @endif
                                        %
                                    </th>
                                @endfor
                                <th class="text-center">
                                    @if($total_new_sales == 0)
                                        {{round(($total_new_sales - $total_new_sales_mm) * 100)}}
                                    @else
                                        {{round(($total_new_sales - $total_new_sales_mm) / $total_new_sales * 100)}}
                                    @endif
                                    %
                                </th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <br>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th colspan="2">@lang('labels.renewal')</th>
                                <th class="text-center">@lang('labels.mg')</th>
                                <th class="text-center">1</th>
                                <th class="text-center">2</th>
                                <th class="text-center">3</th>
                                <th class="text-center">4</th>
                                <th class="text-center">5</th>
                                <th class="text-center">@lang('labels.monthly')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td>@lang('labels.pp_18_months')</td>
                                <td></td>
                                @for($i = 0; $i < 5; $i++)
                                    <td class="text-center">
                                        {{$renewal_pp18[$i]}}
                                        @php($total_renewal_pp18 = $total_renewal_pp18 + $renewal_pp18[$i])
                                        @php($total_renewal_weekly[$i] = $renewal_pp18[$i])
                                    </td>
                                @endfor
                                <td class="text-center">{{$total_renewal_pp18}}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>@lang('labels.pp_12_months')</td>
                                <td></td>
                                @for($i = 0; $i < 5; $i++)
                                    <td class="text-center">
                                        {{$renewal_pp12[$i]}}
                                        @php($total_renewal_pp12 = $total_renewal_pp12 + $renewal_pp12[$i])
                                        @php($total_renewal_weekly[$i] = $total_renewal_weekly[$i] + $renewal_pp12[$i])
                                    </td>
                                @endfor
                                <td class="text-center">{{$total_renewal_pp12}}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>@lang('labels.ad_12_months')</td>
                                <td></td>
                                @for($i = 0; $i < 5; $i++)
                                    <td class="text-center">
                                        {{$renewal_ad12[$i]}}
                                        @php($total_renewal_ad12 = $total_renewal_ad12 + $renewal_ad12[$i])
                                        @php($total_renewal_weekly[$i] = $total_renewal_weekly[$i] + $renewal_ad12[$i])
                                    </td>
                                @endfor
                                <td class="text-center">{{$total_renewal_ad12}}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>@lang('labels.st_12_months')</td>
                                <td></td>
                                @for($i = 0; $i < 5; $i++)
                                    <td class="text-center">
                                        {{$renewal_st12[$i]}}
                                        @php($total_renewal_st12 = $total_renewal_st12 + $renewal_st12[$i])
                                        @php($total_renewal_weekly[$i] = $total_renewal_weekly[$i] + $renewal_st12[$i])
                                    </td>
                                @endfor
                                <td class="text-center">{{$total_renewal_st12}}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>@lang('labels.st_6_months')</td>
                                <td></td>
                                @for($i = 0; $i < 5; $i++)
                                    <td class="text-center">
                                        {{$renewal_st6[$i]}}
                                        @php($total_renewal_st6 = $total_renewal_st6 + $renewal_st6[$i])
                                        @php($total_renewal_weekly[$i] = $total_renewal_weekly[$i] + $renewal_st6[$i])
                                    </td>
                                @endfor
                                <td class="text-center">{{$total_renewal_st6}}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>@lang('labels.month_to_month')</td>
                                <td></td>
                                @for($i = 0; $i < 5; $i++)
                                    <td class="text-center">
                                        {{$renewal_mm[$i]}}
                                        @php($total_renewal_mm = $total_renewal_mm + $renewal_mm[$i])
                                        @php($total_renewal_weekly[$i] = $total_renewal_weekly[$i] + $renewal_mm[$i])
                                    </td>
                                @endfor
                                <td class="text-center">{{$total_renewal_mm}}</td>
                            </tr>
                            <tr>
                                <th></th>
                                <th>@lang('labels.total_renewal')</th>
                                <th></th>
                                @for($i = 0; $i < 5; $i++)
                                    <th class="text-center">
                                        {{$total_renewal_weekly[$i]}}
                                        @php($total_renewal = $total_renewal + $total_renewal_weekly[$i])
                                    </th>
                                @endfor
                                <th class="text-center">{{$total_renewal}}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <br>
                <!-- Management Added 30052018 -->
                {!! Form::open(['method' => 'PATCH', 'action' => ['CAController@set_management', $date], 'id' => 'management-form']) !!}
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th colspan="2">@lang('labels.management')</th>
                            <th class="text-center">@lang('labels.mg')</th>
                            <th class="text-center">1</th>
                            <th class="text-center">2</th>
                            <th class="text-center">3</th>
                            <th class="text-center">4</th>
                            <th class="text-center">5</th>
                            <th class="text-center">@lang('labels.monthly')</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- Weekly -->
                        <tr>
                            <td rowspan="4">@lang('labels.weekly')</td>
                            <td>@lang('labels.weekly_staff_meeting')</td>
                            <td></td>
                            @for($i = 1; $i <= 5; $i++)
                                @php($management_gate = 0)
                                <td class="text-center">
                                    @if(count($managements->where('type', 'wsm')) > 0)
                                        @foreach($managements->where('type', 'wsm') as $wsm)
                                            @if($wsm->code == 'wsm_'.$i)
                                                @php($management_gate = 1)
                                            @endif
                                        @endforeach

                                        @if($management_gate == 1)
                                            {!! Form::checkbox('wsm['.$i.']', 'wsm_'.$i, true) !!}
                                        @else
                                            {!! Form::checkbox('wsm['.$i.']', 'wsm_'.$i, false) !!}
                                        @endif
                                    @else
                                        {!! Form::checkbox('wsm['.$i.']', 'wsm_'.$i, false) !!}
                                    @endif
                                </td>
                            @endfor
                            <td></td>
                        </tr>
                        <tr>
                            <td>@lang('labels.club_analysis_report_completed')</td>
                            <td></td>
                            @for($i = 1; $i <= 5; $i++)
                                @php($management_gate = 0)
                                <td class="text-center">
                                    @if(count($managements->where('type', 'carc')) > 0)
                                        @foreach($managements->where('type', 'carc') as $carc)
                                            @if($carc->code == 'carc_'.$i)
                                                @php($management_gate = 1)
                                            @endif
                                        @endforeach

                                        @if($management_gate == 1)
                                            {!! Form::checkbox('carc['.$i.']', 'carc_'.$i, true) !!}
                                        @else
                                            {!! Form::checkbox('carc['.$i.']', 'carc_'.$i, false) !!}
                                        @endif
                                    @else
                                        {!! Form::checkbox('carc['.$i.']', 'carc_'.$i, false) !!}
                                    @endif
                                </td>
                            @endfor
                            <td></td>
                        </tr>
                        <tr>
                            <td>@lang('labels.ongoing_guest_production_planning_and_meeting')</td>
                            <td></td>
                            @for($i = 1; $i <= 5; $i++)
                                @php($management_gate = 0)
                                <td class="text-center">
                                    @if(count($managements->where('type', 'ogppam')) > 0)
                                        @foreach($managements->where('type', 'ogppam') as $ogppam)
                                            @if($ogppam->code == 'ogppam_'.$i)
                                                @php($management_gate = 1)
                                            @endif
                                        @endforeach

                                        @if($management_gate == 1)
                                            {!! Form::checkbox('ogppam['.$i.']', 'ogppam_'.$i, true) !!}
                                        @else
                                            {!! Form::checkbox('ogppam['.$i.']', 'ogppam_'.$i, false) !!}
                                        @endif
                                    @else
                                        {!! Form::checkbox('ogppam['.$i.']', 'ogppam_'.$i, false) !!}
                                    @endif
                                </td>
                            @endfor
                            <td></td>
                        </tr>
                        <tr>
                            <td>@lang('labels.ongoing_staff_and_management_training_in_the_club')</td>
                            <td></td>
                            @for($i = 1; $i <= 5; $i++)
                                @php($management_gate = 0)
                                <td class="text-center">
                                    @if(count($managements->where('type', 'osamtitc')) > 0)
                                        @foreach($managements->where('type', 'osamtitc') as $osamtitc)
                                            @if($osamtitc->code == 'osamtitc_'.$i)
                                                @php($management_gate = 1)
                                            @endif
                                        @endforeach

                                        @if($management_gate == 1)
                                            {!! Form::checkbox('osamtitc['.$i.']', 'osamtitc_'.$i, true) !!}
                                        @else
                                            {!! Form::checkbox('osamtitc['.$i.']', 'osamtitc_'.$i, false) !!}
                                        @endif
                                    @else
                                        {!! Form::checkbox('osamtitc['.$i.']', 'osamtitc_'.$i, false) !!}
                                    @endif
                                </td>
                            @endfor
                            <td></td>
                        </tr>
                        <!-- Daily -->
                        <tr>
                            <td rowspan="6">@lang('labels.daily')</td>
                            <td>@lang('labels.reporting_projection_sheets_including_no_sales')</td>
                            <td></td>
                            @for($i = 1; $i <= 5; $i++)
                                @php($management_gate = 0)
                                <td class="text-center">
                                    @if(count($managements->where('type', 'rpsins')) > 0)
                                        @foreach($managements->where('type', 'rpsins') as $rpsins)
                                            @if($rpsins->code == 'rpsins_'.$i)
                                                @php($management_gate = 1)
                                            @endif
                                        @endforeach

                                        @if($management_gate == 1)
                                            {!! Form::checkbox('rpsins['.$i.']', 'rpsins_'.$i, true) !!}
                                        @else
                                            {!! Form::checkbox('rpsins['.$i.']', 'rpsins_'.$i, false) !!}
                                        @endif
                                    @else
                                        {!! Form::checkbox('rpsins['.$i.']', 'rpsins_'.$i, false) !!}
                                    @endif
                                </td>
                            @endfor
                            <td></td>
                        </tr>
                        <tr>
                            <td>@lang('labels.follows_travel_transfer_and_cancellation_policies')</td>
                            <td></td>
                            @for($i = 1; $i <= 5; $i++)
                                @php($management_gate = 0)
                                <td class="text-center">
                                    @if(count($managements->where('type', 'fttacp')) > 0)
                                        @foreach($managements->where('type', 'fttacp') as $fttacp)
                                            @if($fttacp->code == 'fttacp_'.$i)
                                                @php($management_gate = 1)
                                            @endif
                                        @endforeach

                                        @if($management_gate == 1)
                                            {!! Form::checkbox('fttacp['.$i.']', 'fttacp_'.$i, true) !!}
                                        @else
                                            {!! Form::checkbox('fttacp['.$i.']', 'fttacp_'.$i, false) !!}
                                        @endif
                                    @else
                                        {!! Form::checkbox('fttacp['.$i.']', 'fttacp_'.$i, false) !!}
                                    @endif
                                </td>
                            @endfor
                            <td></td>
                        </tr>
                        <tr>
                            <td>@lang('labels.no_unauthorized_classes_or_equipment')</td>
                            <td></td>
                            @for($i = 1; $i <= 5; $i++)
                                @php($management_gate = 0)
                                <td class="text-center">
                                    @if(count($managements->where('type', 'nucoe')) > 0)
                                        @foreach($managements->where('type', 'nucoe') as $nucoe)
                                            @if($nucoe->code == 'nucoe_'.$i)
                                                @php($management_gate = 1)
                                            @endif
                                        @endforeach

                                        @if($management_gate == 1)
                                            {!! Form::checkbox('nucoe['.$i.']', 'nucoe_'.$i, true) !!}
                                        @else
                                            {!! Form::checkbox('nucoe['.$i.']', 'nucoe_'.$i, false) !!}
                                        @endif
                                    @else
                                        {!! Form::checkbox('nucoe['.$i.']', 'nucoe_'.$i, false) !!}
                                    @endif
                                </td>
                            @endfor
                            <td></td>
                        </tr>
                        <tr>
                            <td>@lang('labels.no_unauthorized_workout_schemes')</td>
                            <td></td>
                            @for($i = 1; $i <= 5; $i++)
                                @php($management_gate = 0)
                                <td class="text-center">
                                    @if(count($managements->where('type', 'nuws')) > 0)
                                        @foreach($managements->where('type', 'nuws') as $nuws)
                                            @if($nuws->code == 'nuws_'.$i)
                                                @php($management_gate = 1)
                                            @endif
                                        @endforeach

                                        @if($management_gate == 1)
                                            {!! Form::checkbox('nuws['.$i.']', 'nuws_'.$i, true) !!}
                                        @else
                                            {!! Form::checkbox('nuws['.$i.']', 'nuws_'.$i, false) !!}
                                        @endif
                                    @else
                                        {!! Form::checkbox('nuws['.$i.']', 'nuws_'.$i, false) !!}
                                    @endif
                                </td>
                            @endfor
                            <td></td>
                        </tr>
                        <tr>
                            <td>@lang('labels.no_unapproved_supplements_or_products')</td>
                            <td></td>
                            @for($i = 1; $i <= 5; $i++)
                                @php($management_gate = 0)
                                <td class="text-center">
                                    @if(count($managements->where('type', 'nusop')) > 0)
                                        @foreach($managements->where('type', 'nusop') as $nusop)
                                            @if($nusop->code == 'nusop_'.$i)
                                                @php($management_gate = 1)
                                            @endif
                                        @endforeach

                                        @if($management_gate == 1)
                                            {!! Form::checkbox('nusop['.$i.']', 'nusop_'.$i, true) !!}
                                        @else
                                            {!! Form::checkbox('nusop['.$i.']', 'nusop_'.$i, false) !!}
                                        @endif
                                    @else
                                        {!! Form::checkbox('nusop['.$i.']', 'nusop_'.$i, false) !!}
                                    @endif
                                </td>
                            @endfor
                            <td></td>
                        </tr>
                        <tr>
                            <td>@lang('labels.no_unauthorized_advertising_or_logo_usage')</td>
                            <td></td>
                            @for($i = 1; $i <= 5; $i++)
                                @php($management_gate = 0)
                                <td class="text-center">
                                    @if(count($managements->where('type', 'nuaolu')) > 0)
                                        @foreach($managements->where('type', 'nuaolu') as $nuaolu)
                                            @if($nuaolu->code == 'nuaolu_'.$i)
                                                @php($management_gate = 1)
                                            @endif
                                        @endforeach

                                        @if($management_gate == 1)
                                            {!! Form::checkbox('nuaolu['.$i.']', 'nuaolu_'.$i, true) !!}
                                        @else
                                            {!! Form::checkbox('nuaolu['.$i.']', 'nuaolu_'.$i, false) !!}
                                        @endif
                                    @else
                                        {!! Form::checkbox('nuaolu['.$i.']', 'nuaolu_'.$i, false) !!}
                                    @endif
                                </td>
                            @endfor
                            <td></td>
                        </tr>
                        <!-- Monthly -->
                        <tr>
                            <td rowspan="5">@lang('labels.monthly')</td>
                            <td>@lang('labels.manager_team_leader_coaches_attend_corporate_training')</td>
                            <td colspan="6"></td>
                            <td class="text-center">
                                @php($management_gate = 0)
                                @if(count($managements->where('type', 'mtlcact')) > 0)
                                    @foreach($managements->where('type', 'mtlcact') as $mtlcact)
                                        @if($mtlcact->code == 'mtlcact')
                                            @php($management_gate = 1)
                                        @endif
                                    @endforeach

                                    @if($management_gate == 1)
                                        {!! Form::checkbox('mtlcact', 'mtlcact', true) !!}
                                    @else
                                        {!! Form::checkbox('mtlcact', 'mtlcact', false) !!}
                                    @endif
                                @else
                                    {!! Form::checkbox('mtlcact', 'mtlcact', false) !!}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>@lang('labels.pj_ca_report_and_royalty_on_time')</td>
                            <td colspan="6"></td>
                            <td class="text-center">
                                @php($management_gate = 0)
                                @if(count($managements->where('type', 'pcrarot')) > 0)
                                    @foreach($managements->where('type', 'pcrarot') as $pcrarot)
                                        @if($pcrarot->code == 'pcrarot')
                                            @php($management_gate = 1)
                                        @endif
                                    @endforeach

                                    @if($management_gate == 1)
                                        {!! Form::checkbox('pcrarot', 'pcrarot', true) !!}
                                    @else
                                        {!! Form::checkbox('pcrarot', 'pcrarot', false) !!}
                                    @endif
                                @else
                                    {!! Form::checkbox('pcrarot', 'pcrarot', false) !!}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>@lang('labels.action_plan_and_calendar')</td>
                            <td colspan="6"></td>
                            <td class="text-center">
                                @php($management_gate = 0)
                                @if(count($managements->where('type', 'apac')) > 0)
                                    @foreach($managements->where('type', 'apac') as $apac)
                                        @if($apac->code == 'apac')
                                            @php($management_gate = 1)
                                        @endif
                                    @endforeach

                                    @if($management_gate == 1)
                                        {!! Form::checkbox('apac', 'apac', true) !!}
                                    @else
                                        {!! Form::checkbox('apac', 'apac', false) !!}
                                    @endif
                                @else
                                    {!! Form::checkbox('apac', 'apac', false) !!}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>@lang('labels.club_cleanliness_equipment_maintained_rotated')</td>
                            <td colspan="6"></td>
                            <td class="text-center">
                                @php($management_gate = 0)
                                @if(count($managements->where('type', 'ccemr')) > 0)
                                    @foreach($managements->where('type', 'ccemr') as $ccemr)
                                        @if($ccemr->code == 'ccemr')
                                            @php($management_gate = 1)
                                        @endif
                                    @endforeach

                                    @if($management_gate == 1)
                                        {!! Form::checkbox('ccemr', 'ccemr', true) !!}
                                    @else
                                        {!! Form::checkbox('ccemr', 'ccemr', false) !!}
                                    @endif
                                @else
                                    {!! Form::checkbox('ccemr', 'ccemr', false) !!}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>@lang('labels.individual_staff_meeting_season')</td>
                            <td colspan="6"></td>
                            <td class="text-center">
                                @php($management_gate = 0)
                                @if(count($managements->where('type', 'isms')) > 0)
                                    @foreach($managements->where('type', 'isms') as $isms)
                                        @if($isms->code == 'isms')
                                            @php($management_gate = 1)
                                        @endif
                                    @endforeach

                                    @if($management_gate == 1)
                                        {!! Form::checkbox('isms', 'isms', true) !!}
                                    @else
                                        {!! Form::checkbox('isms', 'isms', false) !!}
                                    @endif
                                @else
                                    {!! Form::checkbox('isms', 'isms', false) !!}
                                @endif
                            </td>
                        </tr>
                        <tr id="management-button-row">
                            <td colspan="9">
                                <button type="button" id="submit-management-btn" class="pull-right btn btn-primary">
                                    <span class="fa fa-save"></span> @lang('labels.save')
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                {!! Form::close() !!}
                <br>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th colspan="9">@lang('labels.individual_staff_assessment') (@lang('labels.weekly'))</th>
                            </tr>
                            <tr>
                                <th class="text-center">@lang('labels.name')</th>
                                <th></th>
                                <th class="text-center">1</th>
                                <th class="text-center">2</th>
                                <th class="text-center">3</th>
                                <th class="text-center">4</th>
                                <th class="text-center">5</th>
                                <th class="text-center">@lang('labels.monthly')</th>
                                <th class="text-center">@lang('labels.summary')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($staffs) > 0)
                                @foreach($staffs as $staff)
                                    <tr>
                                        <td rowspan="7" class="text-center">{{$staff->name}}</td>
                                        <td>@lang('labels.pp_18_months')</td>
                                        @for($i = 0; $i < 5; $i++)
                                            <td>
                                                {{$assessment_pp18[$staff->id][$i]}}
                                                @if($i == 0)
                                                    @php($total_assessment_weekly_pp18[$staff->id] = $assessment_pp18[$staff->id][$i])
                                                @else
                                                    @php($total_assessment_weekly_pp18[$staff->id] = $total_assessment_weekly_pp18[$staff->id] + $assessment_pp18[$staff->id][$i])
                                                @endif

                                                @php($total_assessment_pp18 = $total_assessment_pp18 + $assessment_pp18[$staff->id][$i])
                                            </td>
                                        @endfor
                                        <td>{{$total_assessment_weekly_pp18[$staff->id]}}</td>
                                        <td rowspan="6" class="text-center"></td>
                                    </tr>
                                    <tr>
                                        <td>@lang('labels.pp_12_months')</td>
                                        @for($i = 0; $i < 5; $i++)
                                            <td>
                                                {{$assessment_pp12[$staff->id][$i]}}
                                                @if($i == 0)
                                                    @php($total_assessment_weekly_pp12[$staff->id] = $assessment_pp12[$staff->id][$i])
                                                @else
                                                    @php($total_assessment_weekly_pp12[$staff->id] = $total_assessment_weekly_pp12[$staff->id] + $assessment_pp12[$staff->id][$i])
                                                @endif

                                                @php($total_assessment_pp12 = $total_assessment_pp12 + $assessment_pp12[$staff->id][$i])
                                            </td>
                                        @endfor
                                        <td>{{$total_assessment_weekly_pp12[$staff->id]}}</td>
                                    </tr>
                                    <tr>
                                        <td>@lang('labels.ad_12_months')</td>
                                        @for($i = 0; $i < 5; $i++)
                                            <td>
                                                {{$assessment_ad12[$staff->id][$i]}}
                                                @if($i == 0)
                                                    @php($total_assessment_weekly_ad12[$staff->id] = $assessment_ad12[$staff->id][$i])
                                                @else
                                                    @php($total_assessment_weekly_ad12[$staff->id] = $total_assessment_weekly_ad12[$staff->id] + $assessment_ad12[$staff->id][$i])
                                                @endif

                                                @php($total_assessment_ad12 = $total_assessment_ad12 + $assessment_ad12[$staff->id][$i])
                                            </td>
                                        @endfor
                                        <td>{{$total_assessment_weekly_ad12[$staff->id]}}</td>
                                    </tr>
                                    <tr>
                                        <td>@lang('labels.st_12_months')</td>
                                        @for($i = 0; $i < 5; $i++)
                                            <td>
                                                {{$assessment_st12[$staff->id][$i]}}
                                                @if($i == 0)
                                                    @php($total_assessment_weekly_st12[$staff->id] = $assessment_st12[$staff->id][$i])
                                                @else
                                                    @php($total_assessment_weekly_st12[$staff->id] = $total_assessment_weekly_st12[$staff->id] + $assessment_st12[$staff->id][$i])
                                                @endif

                                                @php($total_assessment_st12 = $total_assessment_st12 + $assessment_st12[$staff->id][$i])
                                            </td>
                                        @endfor
                                        <td>{{$total_assessment_weekly_st12[$staff->id]}}</td>
                                    </tr>
                                    <tr>
                                        <td>@lang('labels.st_6_months')</td>
                                        @for($i = 0; $i < 5; $i++)
                                            <td>
                                                {{$assessment_st6[$staff->id][$i]}}
                                                @if($i == 0)
                                                    @php($total_assessment_weekly_st6[$staff->id] = $assessment_st6[$staff->id][$i])
                                                @else
                                                    @php($total_assessment_weekly_st6[$staff->id] = $total_assessment_weekly_st6[$staff->id] + $assessment_st6[$staff->id][$i])
                                                @endif

                                                @php($total_assessment_st6 = $total_assessment_st6 + $assessment_st6[$staff->id][$i])
                                            </td>
                                        @endfor
                                        <td>{{$total_assessment_weekly_st6[$staff->id]}}</td>
                                    </tr>
                                    <tr>
                                        <td>@lang('labels.month_to_month')</td>
                                        @for($i = 0; $i < 5; $i++)
                                            <td>
                                                {{$assessment_mm[$staff->id][$i]}}
                                                @if($i == 0)
                                                    @php($total_assessment_weekly_mm[$staff->id] = $assessment_mm[$staff->id][$i])
                                                @else
                                                    @php($total_assessment_weekly_mm[$staff->id] = $total_assessment_weekly_mm[$staff->id] + $assessment_mm[$staff->id][$i])
                                                @endif

                                                @php($total_assessment_mm = $total_assessment_mm + $assessment_mm[$staff->id][$i])
                                            </td>
                                        @endfor
                                        <td>{{$total_assessment_weekly_mm[$staff->id]}}</td>
                                    </tr>
                                    <tr>
                                        <td>@lang('labels.no_sale')</td>
                                        @for($i = 0; $i < 5; $i++)
                                            <td>
                                                {{$assessment_ns[$staff->id][$i]}}
                                                @if($i == 0)
                                                    @php($total_assessment_weekly_ns[$staff->id] = $assessment_ns[$staff->id][$i])
                                                @else
                                                    @php($total_assessment_weekly_ns[$staff->id] = $total_assessment_weekly_ns[$staff->id] + $assessment_ns[$staff->id][$i])
                                                @endif

                                                @php($total_assessment_ns = $total_assessment_ns + $assessment_ns[$staff->id][$i])
                                            </td>
                                        @endfor
                                        <td>{{$total_assessment_weekly_ns[$staff->id]}}</td>
                                        <td>
                                            @php($sale = $total_assessment_weekly_pp18[$staff->id] + $total_assessment_weekly_pp12[$staff->id] + $total_assessment_weekly_ad12[$staff->id] + $total_assessment_weekly_st12[$staff->id] +
                                            $total_assessment_weekly_st6[$staff->id] + $total_assessment_weekly_mm[$staff->id])

                                            @php($nosale = $total_assessment_weekly_pp18[$staff->id] + $total_assessment_weekly_pp12[$staff->id] + $total_assessment_weekly_ad12[$staff->id] + $total_assessment_weekly_st12[$staff->id] +
                                            $total_assessment_weekly_st6[$staff->id] + $total_assessment_weekly_mm[$staff->id] + $total_assessment_weekly_ns[$staff->id])

                                            @if($nosale == 0)
                                                @php($sales_percent = $sale * 100)
                                            @else
                                                @php($sales_percent = $sale / $nosale * 100)
                                            @endif

                                            @lang('labels.sales'): {{ round($sales_percent) }}%
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            <tr></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('other-script')
    <script>
        $('#my-btn-print').on('click', function () {
            $(this).addClass('hidden');
            $('.left-sidebar').addClass('hidden');
            $('.nav-tabs').addClass('hidden');
            $('#floating-button').addClass('hidden');
            $('#manajemenkeanggotaan').addClass('hidden');
            $('.btn-back-to').addClass('hidden');
            $('.page-title').css({'font-size' : '20px', 'margin-top' : '10px', 'margin-bottom' : '10px'});
            $('.box-menu').css('border', '0');
            $('.table-bordered th').css('font-size', '10px');
            $('.table-bordered td').css('font-size', '10px');
            window.print();
            $(this).removeClass('hidden');
            $('.left-sidebar').removeClass('hidden');
            $('.nav-tabs').removeClass('hidden');
            $('#floating-button').removeClass('hidden');
            $('#manajemenkeanggotaan').removeClass('hidden');
            $('.btn-back-to').removeClass('hidden');
            $('.page-title').css({'font-size' : '50px', 'margin-top' : '50px', 'margin-bottom' : '50px'});
            $('.box-menu').css('border', '');
            $('.table-bordered th').css('font-size', '16px');
            $('.table-bordered td').css('font-size', '16px');
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#submit-management-btn').on('click', function () {
                $('#management-form').submit();
            });
        });
    </script>
@endsection