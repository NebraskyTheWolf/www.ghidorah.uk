@extends('layouts.app')

@section('title', $serverName)

@section('content')
    <div class="ui container page-content">
        <div class="text-center head-top">
            <h1 class="ui header">
                <img src="{{ url($serverIcon) }}" class="ui circular image">
                <div class="content">
                    <a href="{{ url('/server/' . $serverId . '/' .  $serverOwnerId . '/profile') }}" class="ui blue image medium label">
                        {{ $serverOwner }}
                        <div class="detail">Owner</div>
                    </a>
                    <div class="sub header">{{ $serverName }} Server Profile</div>
                </div>
            </h1>
        </div>

        <div class="ui divider"></div>

        <a href="{{ url('/server/' . $serverId . '/leaderboards') }}" class="ui button blue">
            <i class="chart area icon"></i>
            Leaderboard
        </a>

        <button class="ui button blue" onclick="$('#rules').modal('show')">
            <i class="exclamation triangle icon" style="color:#FF6347;"></i>  
            Rules
        </button>

        <div class="ui divider"></div>

        <div class="ui stackable grid" style="position:relative;">

            <div class="ui eight wide column">
                <h2 class="ui header">
                    Server informations
                    <div class="sub header">Here you will find all the public informations.</div>
                </h2>
                <br>

                <div class="ui two small statistics"></div>
                <div class="ui four small statistics">
                    <div class="statistic">
                        <div class="value">
                            {{ $serverMemberCount }}
                        </div>
                        <div class="label">
                            Members
                        </div>
                    </div>
                    <div class="statistic">
                        <div class="value">
                            {{ count($serverEmojis) }}
                        </div>
                        <div class="label">
                            Emojis
                        </div>
                    </div>
                    <div class="statistic">
                        <div class="value">
                            {{ count($serverStickers) }}
                        </div>
                        <div class="label">
                            Stickers
                        </div>
                    </div>
                    <div class="statistic">
                        <div class="value">
                            {{ $serverPremiumCount }}
                        </div>
                        <div class="label">
                            Server boosts
                        </div>
                    </div>
                </div>
                <br>
                <div class="ui indicating progress" id="power">
                    <div class="bar">
                        <div class="progress"></div>
                    </div>
                    <div class="label">Boost</div>
                </div>
            </div>
            <div class="ui vertical divider"></div>
            <div class="ui eight wide column">
                <h2 class="ui header">
                    Members
                    <div class="sub header">All the members in the server</div>
                </h2>
                <br>

                @foreach ($serverMembers as $member)
                    <a href="{{ url('/server/' . $serverId . '/' . $member->id . '/profile') }}">
                        <img src="{{ $member->iconURL }}"
                             class="ui rounded member image" alt="{{ $member->username }}" data-toggle="popup"
                             data-variation="inverted" data-placement="top center"
                             data-content="{{ $member->username }}">
                    </a>
                @endforeach
            </div>

        </div>
        <div class="ui divider"></div>
        <div class="ui stackable grid" style="position:relative;">
            <div class="ui eight wide column">
                <h2 class="ui header">
                    Server Achievements
                    <div class="sub header">@lang('stats.success.subtitle', ['number' => env('APP_VERSION_COUNT')])</div>
                </h2>
                <br>
                <div class="ui icon message">
                    <i class="database icon"></i>
                    <div class="content">
                        <div class="header">
                            @lang('stats.graph.no_data.title')
                        </div>
                        <p>@lang('stats.graph.no_data.subtitle')</p>
                    </div>
                </div>
            </div>
            <div class="ui vertical divider"></div>
            <div class="ui eight wide column">
                <h2 class="ui header">
                    Messages
                    <div class="sub header">Average of message sent in the server</div>
                </h2>
                <br>
                <div class="ui icon message">
                    <i class="database icon"></i>
                    <div class="content">
                        <div class="header">
                            @lang('stats.graph.no_data.title')
                        </div>
                        <p>@lang('stats.graph.no_data.subtitle')</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui modal" id="rules">
        <div class="header">{{ $serverName }} - RULES</div>
        <div class="content">
        <div class="ui info message">
                    <div class="header">
                        @lang('global.info')
                    </div>
                    <p>The rules of {{ $serverName }} are strict and non-negotiable.</p>
                    <p>If you are breaking one of our rules you'll be banned or kicked.</p> 
                    <p>Please do not complains of any of this rules.</p>
                </div>
                <form class="ui form" method="post" action="{{ url('/verify/step/three') }}" data-ajax
                      data-ajax-custom-callback="afterStepThree">

                    @if ($serverRules)
                        <!-- code -->
                    @else
                        <div class="ui icon message">
                            <i class="database icon"></i>
                            <div class="content">
                                <div class="header">
                                    Rules not set!
                                </div>
                                <p>It's look like this server didn't set their rules on the website.</p>
                            </div>
                        </div>
                    @endif
                </form>
        </div>
    </div>
@endsection
@section('style')
    <style media="screen">
        .stats-down, .stats-up {
            font-size: 15px;
            position: absolute;
            margin-left: 5px;
        }

        .stats-up {
            color: #30b535;
        }

        .stats-down {
            color: #b53124;
        }

        img.member.image {
            background-color: #bdc3c7;
            border: 2px solid #c0392b;
            margin-right: 5px;
            margin-bottom: 5px;
            display: inline-block;
        }

        .ui.grid > .column + .divider, .ui.grid > .row > .column + .divider {
            left: 50%;
        }

        .ui.vertical.divider:after, .ui.vertical.divider:before {
            height: 100%;
        }

        .label {
            margin-top: 5px !important;
        }

        .achievement.active {
            color: #fff !important;
        }

        .achievement.active.label {
            position: relative;
            background: transparent !important;
        }

        .achievement.active.label:before {
            background: #767676;
            content: '';
            z-index: -2;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            border-radius: .28571429rem;
        }

        .achievement.active.label:after {
            background: #2185D0;
            content: '';
            z-index: -1;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: calc(100% - 0%);
            border-radius: .28571429rem;
        }

        @for ($i = 0; $i <= 100; $i += 0.1)
            .achievement.active.label.p{{ str_replace('.', '-', round($i, 1)) }}:after {
                right: calc(100% - {{ round($i, 1) }}%);
            }
        @endfor
    </style>
@endsection
@section('script')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script type="text/javascript">
        $('#power').progress({
            total: '14',
            value: '{{ $serverPremiumCount }}',
            text: {
                percent: '{value}/{total}'
            }
        })
        $(document).ready(function () {
            $('[data-toggle="popup"]').each(function (k, el) {
                $(el).popup({
                    html: $(el).attr('data-content'),
                    position: $(el).attr('data-placement'),
                    variation: $(el).attr('data-variation')
                })
            })
        })
    </script>
    <script type="text/javascript">
    </script>
@endsection
