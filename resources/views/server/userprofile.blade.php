@extends('layouts.app')

@section('title', $username)

@section('content')
    <div class="ui container page-content">
        <h1 class="ui center aligned header">
            <img src="{{ url($iconURL) }}" class="ui rounded staff image" alt="Vakea">
            <div class="content">
                @if (isset($serverId))
                    <a href="{{ url('/server/' . $serverId) }}" class="ui blue image medium label">
                        {{ $serverName }}
                        @if ($serverOwnerId === $userDID)
                            <div class="detail">Owner</div>
                        @endif
                        @if ($serverOwnerId !== $userDID)
                            <div class="detail">Member</div>
                        @endif
                    </a>
                @endif
                {{ $username }}
                <div class="sub header" style="margin-top:5px;"><i class="Europe/London flag"></i></div>
            </div>
        </h1>
        <div class="ui divider"></div>
            <div class="ui stackable grid" style="position:relative;">

                <div class="ui eight wide column">
                    <h2 class="ui header">
                        @lang('stats.users.infos.title')
                        <div class="sub header">@lang('stats.users.infos.subtitle')</div>
                    </h2>
                    <br>

                    <div class="ui four small statistics">
                        <div class="statistic">
                            <div class="value">
                                {{ $userLevel }}
                            </div>
                            <div class="label">
                                Level
                            </div>
                        </div>
                        <div class="statistic">
                            <div class="value">
                                {{ $userXP }}
                            </div>
                            <div class="label">
                                Scores
                            </div>
                        </div>
                        <div class="statistic">
                            <div class="value">
                                {{ $rankName }}
                            </div>
                            <div class="label">
                                Rank
                            </div>
                        </div>
                        <div class="statistic">
                            <div class="value">
                                #{{ $rankPosition }}
                            </div>
                            <div class="label">
                                Position
                            </div>
                        </div>
                    </div>
                    <br>

                    <div class="ui indicating progress" id="levels">
                        <div class="bar">
                            <div class="progress"></div>
                        </div>
                        <div class="label">Level</div>
                    </div>
                </div>
                <div class="ui vertical divider"></div>
                <div class="ui eight wide column">
                    <h2 class="ui header">
                        Achievements
                        <div class="sub header">@lang('stats.success.subtitle', ['number' => env('APP_VERSION_COUNT')])</div>
                    </h2>

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
@endsection
@section('style')
    <style media="screen">
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
            right: calc(100% - 80%);
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
    <script type="text/javascript">
        $('#levels').progress({
            total: '{{ $requiredXp }}',
            value: '{{ $userXP }}',
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
@endsection
