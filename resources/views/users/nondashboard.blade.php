@extends('layouts.app')

@section('title') {{trans('admin.dashboard')}} -@endsection
@php use App\Helper; @endphp

@section('content')

    <?php $pageTitle = "Welcome Frederik 👋"; ?>

    <div class="session-main-wrapper" id="page-dashboard">
        @include("includes.menus.main-creator-menu")




        <div class="session-main-page-wrapper">
            @include("includes.menus.main-creator-navbar")

            <div class="session-main-content-wrapper">
                <div class="dashboard-top">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <div class="timeline">
                                <h6>Hours</h6>
                                <h2>{{$minuteTotals}}</h2>
                                <p>You have completed more then 24 hours in the chosen period.</p>
                            </div>
                            <div class="timeline-chart">
                                <div class="chart">
                                    <div id="chart2" style="width:100%;"></div>
                                </div>
                                <a href="javascript:;">Show Results</a>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <div class="timeline">
                                <h6>Lessons</h6>
                                <h2>{{$lessonTotals}}</h2>
                                <p>You have taken 7 more classes in chosen period that is 20% more then before.</p>
                            </div>
                            <div class="timeline-chart">
                                <div class="chart">
                                    <div id="chart4" style="width:100%;"></div>
                                </div>
                                <a href="javascript:;">Show Results</a>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <div class="timeline">
                                <h6>Growth</h6>
                                <h2>{{$growthRate > 0 ? "+".$growthRate : $growthRate}}%</h2>
                                <p>You started to study more harder and you got excellent marks more often.</p>
                            </div>
                            <div class="timeline-chart">
                                <div class="chart">
                                    <div id="chart3" style="width:100%;"></div>
                                </div>
                                <a href="javascript:;">Show Results</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dashboard-memeber">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="member-one h-100">
                                <div class="title-main">
                                    <h4>Recent mentorships</h4>
                                    {{-- <a href="javascript:;">View All</a> --}}
                                </div>
                                {{-- <div class="member-table recent-table">
                                    <table class="table-striped table-hover">
                                        <tbody>
                                            <tr>
                                                <td><img src="https://app.eduvo.io/public/images/member1.png"></td>
                                                <td>Frances Swann</td>
                                                <td><a href="javascript:;" class="member-btn green">Invite member</a></td>
                                                <td><a href="javascript:;" class="member-btn">Go to membership</a></td>
                                            </tr>
                                            <tr>
                                                <td><img src="https://app.eduvo.io/public/images/member2.png"></td>
                                                <td>Frances Swann</td>
                                                <td><a href="javascript:;" class="member-btn green">Invite member</a></td>
                                                <td><a href="javascript:;" class="member-btn">Go to membership</a></td>
                                            </tr>
                                            <tr>
                                                <td><img src="https://app.eduvo.io/public/images/member3.png"></td>
                                                <td>Frances Swann</td>
                                                <td><a href="javascript:;" class="member-btn green">Invite member</a></td>
                                                <td><a href="javascript:;" class="member-btn">Go to membership</a></td>
                                            </tr>
                                            <tr>
                                                <td><img src="https://app.eduvo.io/public/images/member4.png"></td>
                                                <td>Frances Swann</td>
                                                <td><a href="javascript:;" class="member-btn green">Invite member</a></td>
                                                <td><a href="javascript:;" class="member-btn">Go to membership</a></td>
                                            </tr>
                                            <tr>
                                                <td><img src="https://app.eduvo.io/public/images/member5.png"></td>
                                                <td>Frances Swann</td>
                                                <td><a href="javascript:;" class="member-btn green">Invite member</a></td>
                                                <td><a href="javascript:;" class="member-btn">Go to membership</a></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div> --}}

                                @if(count($mentors) > 0)
                                    <div class="member-table recent-table">
                                        <table class="table-striped table-hover">
                                            <tbody>
                                                @foreach($mentors as $mentor)
                                                <tr>
                                                    <td>
                                                        {{-- <img src="https://app.eduvo.io/public/images/member1.png"> --}}
                                                        <img src="{{Helper::getFile(config('path.avatar').$mentor->avatar)}}" />
                                                    </td>
                                                    <td>{{ $mentor->name }}</td>
                                                    <td><a href="javascript:;" class="member-btn green">Invite member</a></td>
                                                    <td><a href="javascript:;" class="member-btn">Go to membership</a></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="no-upcoming-payments d-flex align-items-center h-100">
                                        <h5 class="text-muted">You have no recent mentorships</h5>
                                    </div>
                                @endif

                                

                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="member-one member-two h-100">
                                <div class="title-main">
                                    <h4>Upcoming payments</h4>
                                </div>
                                {{-- <div class="member-table">
                                    <table class="table-striped table-hover">
                                        <tbody>
                                            <tr>
                                                <td><img src="https://app.eduvo.io/public/images/member1.png"></td>
                                                <td>Frances Swann</td>
                                                <td><a href="javascript:;" class="member-btn perple">Pending</a></td>
                                                <td>$54</td>
                                            </tr>
                                            <tr>
                                                <td><img src="https://app.eduvo.io/public/images/member2.png"></td>
                                                <td>Frances Swann</td>
                                                <td><a href="javascript:;" class="member-btn green">Completed</a></td>
                                                <td>$54</td>
                                            </tr>
                                            <tr>
                                                <td><img src="https://app.eduvo.io/public/images/member3.png"></td>
                                                <td>Frances Swann</td>
                                                <td><a href="javascript:;" class="member-btn perple">Pending</a></td>
                                                <td>$54</td>
                                            </tr>
                                            <tr>
                                                <td><img src="https://app.eduvo.io/public/images/member4.png"></td>
                                                <td>Frances Swann</td>
                                                <td><a href="javascript:;" class="member-btn green">Completed</a></td>
                                                <td>$54</td>
                                            </tr>
                                            <tr>
                                                <td><img src="https://app.eduvo.io/public/images/member5.png"></td>
                                                <td>Frances Swann</td>
                                                <td><a href="javascript:;" class="member-btn perple">Pending</a></td>
                                                <td>$54</td>
                                            </tr>
                                        </tbody>
                                    </table> 
                                </div> --}}

                                <div class="no-upcoming-payments d-flex align-items-center h-100">
                                    <h5 class="text-muted">You have no upcoming payments</h5>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="member-one available-table">
                                <div class="title-main">
                                    <h4>Available Documents</h4>
                                </div>
                                <div class="member-table">
                                    <table class="table-striped table-hover">
                                        <tbody>
                                            <tr>
                                                <td><img src="https://app.eduvo.io/public/images/doc1.png"></td>
                                                <td>Document Name</td>
                                                <td><a href="javascript:;" class="member-btn perple">Download</a></td>
                                            </tr>
                                            <tr>
                                                <td><img src="https://app.eduvo.io/public/images/doc2.png"></td>
                                                <td>Document Name</td>
                                                <td><a href="javascript:;" class="member-btn perple">Download</a></td>
                                            </tr>
                                            <tr>
                                                <td><img src="https://app.eduvo.io/public/images/doc3.png"></td>
                                                <td>Document Name</td>
                                                <td><a href="javascript:;" class="member-btn perple">Download</a></td>
                                            </tr>
                                            <tr>
                                                <td><img src="https://app.eduvo.io/public/images/doc4.png"></td>
                                                <td>Document Name</td>
                                                <td><a href="javascript:;" class="member-btn perple">Download</a></td>
                                            </tr>
                                            <tr>
                                                <td><img src="https://app.eduvo.io/public/images/doc5.png"></td>
                                                <td>Document Name</td>
                                                <td><a href="javascript:;" class="member-btn perple">Download</a></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('javascript')
<script src="https://app.eduvo.io/public/js/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // Charts


    $(function () {
        var options = {
            chart: {
                type: "area"
            },
            dataLabels: {
                enabled: false,
            },
            series: [{
                name: "Minutes",
                //  data: [1, 2, 3, 5, 4, 3, 2, 1]
                data: JSON.parse('{{json_encode($minutes)}}')

            }],
            fill: {
                colors: '#e4c9fe',
                opacity: 0.8,
                type: 'solid',
                gradient: {
                    shade: 'light',
                    shadeIntensity: 0.5,
                    gradientToColors: ['#e4c9fe'], // optional, if not defined - uses the shades of same color in series
                    inverseColors: true,
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 50, 100],
                    colorStops: []
                }
            },
            toolbar: {
                show: false,
            },
            stroke: {
                width: 5,
                curve: 'smooth',
                colors: '#d8bdf2',
            },
            grid: {
                show: false,
                strokeDashArray: 0,
                position: 'back',
            },
            xaxis: {
                axisTicks: {
                show: false,
                },
                axisBorder: {
                show: false,
                },
                labels: {
                show: false,
                },
            },
            markers: {
                colors: '#cfa8f6',
            },
        };

        var chart = new ApexCharts(document.querySelector("#chart2"), options);

        chart.render();

    });

    $(function () {
        var options = {
            chart: {
                type: "area"
            },
            dataLabels: {
                enabled: false,
            },
            series: [{
                name: "Lessions",
                // data: [1, 2, 1, 3, 0, 1, 3, 1, 4, 1]
                data: JSON.parse('{{json_encode($lessons)}}')
            }],
            fill: {
                colors: '#e4c9fe',
                opacity: 0.8,
                type: 'solid',
                gradient: {
                    shade: 'light',
                    shadeIntensity: 0.5,
                    gradientToColors: ['#e4c9fe'], // optional, if not defined - uses the shades of same color in series
                    inverseColors: true,
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 50, 100],
                    colorStops: []
                }
            },
            toolbar: {
                show: false,
            },
            pattern: {
            style: 'verticalLines',
            width: 5,
            height: 5,
            },
            stroke: {
                width: 2,
                curve: 'smooth',
                colors: '#d8bdf2',
            },
            grid: {
                show: false,
                strokeDashArray: 0,
                position: 'back',
            },
            xaxis: {
                axisTicks: {
                show: false,
                },
                axisBorder: {
                show: false,
                },
                labels: {
                show: false,
                },
            },
            markers: {
                colors: '#cfa8f6',
            },
        };

        var chart = new ApexCharts(document.querySelector("#chart4"), options);

        chart.render();

    });

    $(function () {
        var options = {
        series: [
                {
                name: "Minutes",
                data: JSON.parse('{{json_encode($minutes)}}')

                }
            ],
            chart: {
                type: "bar"
            },
            dataLabels: {
                enabled: false,
            },
            fill: {
                colors: '#e4c9fe',
                },
            grid: {
                show: false,
                strokeDashArray: 0,
                position: 'back',
            },
            xaxis: {
                axisTicks: {
                show: false,
                },
                axisBorder: {
                show: false,
                },
                labels: {
                show: false,
                },
            },
            markers: {
                colors: '#cfa8f6',
            },

        };

        var chart = new ApexCharts(document.querySelector("#chart3"), options);

        chart.render();

    });

</script>
    
@endsection