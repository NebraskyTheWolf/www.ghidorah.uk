@extends('layouts.app')

@section('title', $discordUser['user']['username'])

@section('content')
    <div class="ui container page-content">
        <h1 class="ui center aligned header">
            <img src="{{ $discordUser['user']['avatar'] }}" class="ui rounded staff image" alt="Vakea">
            <div class="content">
                @if (isset($user->faction->name))
                    <a href="{{ url('/stats/faction/' . $user->faction->name) }}" class="ui blue image medium label">
                        {{ $user->faction->name }}
                        <div class="detail">{{ __('stats.factions.role.' . $user->faction->role)  }}</div>
                    </a>
                @endif
                {{ $discordUser['user']['username'] }}
                <div class="sub header" style="margin-top:5px;"><i class="Europe/London flag"></i>
                    @lang('stats.users.register.date', ['date' => $discordUser['registeredAt']])
                </div>
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
                            {{ $user->stats->online->total_time }}
                        </div>
                        <div class="label">
                            @lang('stats.users.logtime')
                        </div>
                    </div>
                    <div class="statistic">
                        <div class="value">
                            {{ $user->stats->stats->kills + $user->stats->stats->deaths }}
                        </div>
                        <div class="label">
                            @lang('stats.users.fights')
                        </div>
                    </div>
                    <div class="statistic">
                        <div class="value">
                            {{ $user->stats->stats->kills }}
                        </div>
                        <div class="label">
                            @lang('stats.users.kills')
                        </div>
                    </div>
                    <div class="statistic">
                        <div class="value">
                            {{ $user->stats->stats->deaths }}
                        </div>
                        <div class="label">
                            @lang('stats.users.deaths')
                        </div>
                    </div>
                </div>
                <br>
                <div class="ui two small statistics">
                    <div class="statistic">
                        <div class="value">
                            {{ number_format($user->stats->stats->blocks->placed, 0, ',', '.') }}
                        </div>
                        <div class="label">
                            @lang('stats.users.blocks.placed')
                        </div>
                    </div>
                    <div class="statistic">
                        <div class="value">
                            {{ number_format($user->stats->stats->blocks->broken, 0, ',', '.') }}
                        </div>
                        <div class="label">
                            @lang('stats.users.blocks.broken')
                        </div>
                    </div>
                </div>

                <div class="ui divider"></div>

                @for($i = 5; $i <= 8; $i++)
                    <span class="ui {{ in_array($i, $user->stats->versions) ? 'green' : 'red' }} label">
                        <i class="remove icon"></i>
                        @lang('stats.users.versions', ['number' => $i])
                    </span>
                @endfor

                <div class="ui divider"></div>

                <span class="ui {{ $user->stats->cape ? 'blue' : 'grey disabled' }} label">
                  <i class="{{ $user->stats->cape ? 'check' : 'remove' }} icon"></i>
                    @lang('stats.users.cape')
                </span>
                <span class="ui {{ $user->stats->skin ? 'blue' : 'grey disabled' }} label">
                  <i class="{{ $user->stats->skin ? 'check' : 'remove' }} icon"></i>
                    @lang('stats.users.skin')
                </span>

                @if (is_object($user->stats->online->last_connection))
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>@lang('stats.users.last_connection', ['date' => $user->stats->online->last_connection])</em>
                @endif

            </div>
            <div class="ui vertical divider"></div>
            <div class="ui eight wide column">
                <h2 class="ui header">
                    Achievements
                    <div class="sub header">@lang('stats.success.subtitle', ['number' => env('APP_VERSION_COUNT')])</div>
                </h2>

                

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
