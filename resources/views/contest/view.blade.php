@extends('layouts.app')

@section('title', __('sanction.contest.view', ['user' => $contest->user->username]))

@section('content')
    <div class="ui container page-content">
        <div class="ui grid contest-body">

            <div class="thirteen wide column">
                <div class="ui raised card" style="width: 100%">

                    @if ($sanction['is_deleted'])
                        <div class="ui active inverted dimmer">
                            <div class="ui large text loader no-loader">@lang('sanction.expired')</div>
                        </div>
                    @elseif ($contest->status === 'CLOSED')
                        <div class="ui active inverted dimmer">
                            <div class="ui large text loader no-loader">@lang('sanction.contest.closed')</div>
                        </div>
                    @endif

                    <div class="content">
                        <div class="ui items">
                            <div class="item">
                                <div class="ui tiny image mobile-hide">
                                    <img src="https://minotar.net/avatar/{{ $contest->user->username }}">
                                </div>
                                <div class="content">
                                    @if ($sanction['expiration_date'] == 'PERMANENT')
                                        <span class="ui red right ribbon label">@lang('sanction.permanent')</span>
                                    @else
                                        <span class="ui orange right ribbon label formatSeconds">{{ $sanction['expiration_date'] }}</span>
                                    @endif
                                    <div class="clearfix"></div>
                                    <span class="header"
                                          style="position: absolute;top: 20px;">{{ $contest->user->username }}</span>
                                    <div class="meta">
                                        <span>
                                            {{ $contest->sanction_type == 'ban' ? 'Banned' : 'Muted'}}&nbsp;
                                            <span>{{ $sanction['creation_date']->diffForHumans() }}</span>
                                        </span>
                                    </div>
                                    <div class="ui divider"></div>
                                    <div class="description">
                                        <p>
                                            <b>@lang('sanction.reason'): </b>
                                            &laquo; <em>{{ $sanction['reason'] }}</em> &raquo;
                                        </p>
                                        <p>
                                            <b>@lang('sanction.staff'): </b>
                                            {{ $sanction['punisher_uuid'] == '00000000000000000000000000000000' ? "FoxGuard" : $sanction['punisher_uuid'] }}
                                        </p>
                                        <p>
                                            <b>@lang('sanction.contest.content'): </b>
                                            &laquo; <em>{!! nl2br(htmlentities($contest->reason)) !!}</em> &raquo;
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="extra content">
                        <div class="right floated author">
                            @lang('sanction.contest.date_string', ['date' => $contest->created_at->diffForHumans()])
                        </div>
                    </div>
                </div>

                <div class="ui segment">
                    <div class="ui comments" style="max-width: none;">
                        <h3 class="ui dividing header">@lang('sanction.contest.comments')</h3>
                        @foreach ($actions as $action)
                            @if ($action->type == 'comment')
                                <div class="comment">
                                    <a class="avatar">
                                        <img style="height: 35px" src="https://minotar.net/avatar/{{ $action->data->user->username }}">
                                    </a>
                                    <div class="content">
                                        <a class="author">{{ $action->data->user->username }}</a>
                                        <div class="metadata">
                                            <span class="date">{{ $action->data->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="text">
                                            {!! nl2br(htmlentities($action->data->content)) !!}
                                        </div>
                                    </div>
                                </div>
                            @else
                                <h4 class="ui horizontal divider header">
                                    @if ($action->data->action == 'CLOSE')
                                        <i class="remove icon"></i>
                                        @lang('sanction.contest.action.close', ['user' => $action->data->user->username])
                                    @elseif ($action->data->action == 'UNBAN')
                                        <i class="minus icon"></i>
                                        @lang('sanction.contest.action.unban', ['user' => $action->data->user->username])
                                    @elseif ($action->data->action == 'REDUCE')
                                        <i class="checkmark icon"></i>
                                        @lang('sanction.contest.action.reduce', ['user' => $action->data->user->username])
                                    @endif
                                </h4>
                            @endif
                        @endforeach
                        <div id="comment"></div>
                        @if ($sanction['is_deleted'] && $contest->status == 'PENDING' && Auth::user())
                            <form method="post" action="{{ url('/sanctions/contest/' . $contest->id . '/comment') }}" class="ui reply form" data-ajax data-ajax-custom-callback="addComment">
                                <div class="ajax-message" style="margin-bottom:5px;"></div>
                                <div class="field">
                                    <textarea name="content"></textarea>
                                </div>
                                <div style="float:right;">
                                    <button type="submit" class="ui blue labeled submit icon button">
                                        <i class="icon edit"></i> @lang('sanction.contest.comment')
                                    </button>
                                </div>
                                <br><br>
                            </form>
                        @endif
                    </div>
                </div>

            </div>

            @ability('', 'sanction-contest-close,sanction-contest-edit')
                @if (Auth::user())
                    <div class="column">
                        <div class="ui rail">
                            <div class="ui sticky">
                                <div class="ui segment" style="margin-top:15px">
                                    <button class="fluid ui red button close{{ $contest->status == 'CLOSED' ? ' disabled' : ''}}"
                                            data-tooltip="@lang('sanction.contest.action.close.text')"
                                            data-inverted>
                                        <i class="icon remove"></i>&nbsp;<span class="mobile-hidden">@lang('sanction.contest.action.close.btn')</span>
                                    </button>
                                    <div class="ui divider"></div>
                                    <button class="fluid ui green button unban{{ $sanction['is_deleted'] && $contest->status != 'CLOSED' ? '' : ' disabled' }}"
                                            data-tooltip="@lang('sanction.contest.action.unban.text')" data-inverted>
                                        <i class="icon minus"></i>&nbsp;<span class="mobile-hidden">@lang('sanction.contest.action.unban.btn')</span>
                                    </button>
                                    <div class="ui divider"></div>
                                    <button class="fluid ui teal button reduce{{ $sanction['is_deleted'] && $contest->status != 'CLOSED' ? '' : ' disabled' }}"
                                            data-tooltip="@lang('sanction.contest.action.reduce.text')" data-inverted>
                                        <i class="icon checkmark"></i>&nbsp;<span class="mobile-hidden">@lang('sanction.contest.action.reduce.btn')</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endability

        </div>
    </div>
    <div class="ui small modal" id="reduceModal">
        <i class="close icon"></i>
        <div class="header">
            @lang('sanction.contest.action.reduce.modal.title')
        </div>
        <div class="content">
            <form class="ui form" id="reduce">

                <div class="ajax-message" style="margin-bottom:5px;"></div>

                <div class="field">
                    <label>@lang('sanction.contest.action.reduce.modal.field')</label>
                    <div class="ui calendar" id="calendar">
                        <div class="ui input left icon">
                            <i class="calendar icon"></i>
                            <input type="text" name="end_date" placeholder="Format: YYYY-MM-DD HH:MM:SS">
                        </div>
                    </div>
                </div>

            </form>
        </div>
        <div class="actions">
            <div class="ui black deny button">
                @lang('form.cancel')
            </div>
            <button type="button" id="reduceBtn" class="ui positive right labeled icon button">
                @lang('form.valid')
                <i class="checkmark icon"></i>
            </button>
        </div>
    </div>
@endsection
@section('style')
    <style>
        .no-loader:after,
        .no-loader:before {
            display: none;
        }
        .no-loader {
            padding-top: 0!important;
        }
    </style>
@endsection
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/locale/fr.js"></script>

    <link href="{{ url('/css/calendar.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ url('/js/calendar.min.js') }}"></script>

    <script type="text/javascript">
        moment().locale('fr')
        $('.formatSeconds').each(function () {
            $(this).html(formatSeconds($(this).html()))
        })
        $('.ui.sticky').sticky({
            context: '.contest-body'
        })
        // CLOSE
        $('.close').on('click', function (e) {
            $.ajax({
                method: 'DELETE',
                url: '{{ url('/sanctions/contest') }}/{{ $contest->id }}',
                success: function () {
                    window.location = '?'
                }
            })
        })
        // UNBAN
        $('.unban').on('click', function (e) {
            $.ajax({
                method: 'PUT',
                url: '{{ url('/sanctions/contest') }}/{{ $contest->id }}',
                data: {
                    type: 'UNBAN',
                },
                success: function () {
                    window.location = '?'
                }
            })
        })
        // REDUCE
        $('.reduce').on('click', function (e) {
            e.preventDefault()
            // show modal
            $('#reduceModal').modal('show')
            $('#calendar').calendar({
                ampm: false,
                text: {
                    days: [@lang('global.days.initials')],
                    months: [@lang('global.months')],
                    monthsShort: [@lang('global.months.mini')],
                    today: "@lang('global.today')",
                    now: "@lang('global.now')"
                },
                formatter: {
                    date: function (date, settings) {
                        if (!date) return ''
                        var day = date.getDate()
                        var month = date.getMonth() + 1
                        var year = date.getFullYear()
                        if (month.toString().length === 1)
                            month = '0' + month
                        if (day.toString().length === 1)
                            day = '0' + day
                        return year + '-' + month + '-' + day
                    },
                    time: function (date, settings) {
                        if (!date) return ''
                        var hour = date.getHours()
                        var minute = date.getMinutes()
                        if (hour.toString().length === 1)
                            hour = '0' + hour
                        if (minute.toString().length === 1)
                            minute = '0' + minute
                        return hour + ':' + minute + ':' + '00'
                    }
                }
            })
            // submit form
            $('#reduceBtn').on('click', function (e) {
                e.preventDefault()
                var btn = $(this)
                var form = $('form#reduce')

                // disable
                btn.attr('disabled', true)
                form.find('input').attr('disabled', true)
                form.removeClass('error success')

                // util
                function displayAlert(type, msg) {
                    var div = form.find('.ajax-message').hide()
                    if (msg) {
                        if (type)
                            div.html('<div class="ui success message"><div class="header">@lang('form.success.title')</div><p>' + msg + '</p></div>')
                        else
                            div.html('<div class="ui error message"><div class="header">@lang('form.error.title')</div><p>' + msg + '</p></div>')
                        div.fadeIn(150)
                    }
                }

                $.ajax({
                    method: 'PUT',
                    url: '{{ url('/sanctions/contest') }}/{{ $contest->id }}',
                    data: {
                        type: 'REDUCE',
                        end_date: form.find('input[name="end_date"]').val(),
                    },
                    success: function (data) {
                        if (!data.status) {
                            displayAlert(false, data.error)
                            form.addClass('error')
                            btn.attr('disabled', false)
                            form.find('input').attr('disabled', false)
                            return
                        }

                        displayAlert(true, '@lang('sanction.contest.action.reduce.success')')
                        form.addClass('success')
                        window.location = '?'
                    }
                }).fail(function (data) {
                    // display error
                    switch (data.status) {
                        case 400:
                            displayAlert(false, '@lang('form.error.fields')')
                            form.addClass('error')
                            break;
                        default:
                            displayAlert(false, '@lang('form.error.internal')')
                            form.addClass('error')
                    }
                    // enable form
                    btn.attr('disabled', false)
                    form.find('input').attr('disabled', false)
                })
            })
        })
        // ADD COMMENT
        function addComment(data, response, form) {
            var comment = ''
            comment += '<div class="comment">'
                comment += '<a class="avatar">'
                    comment += '<img style="height:35px;" src="https://minotar.net/avatar/{{ Auth::user() ? Auth::user()->username : '' }}">'
                comment += '</a>'
                comment += '<div class="content">'
                    comment += '<a class="author">{{ Auth::user() ? Auth::user()->username : '' }}</a>'
                    comment += '<div class="metadata">'
                        comment += '<span class="date moment">' + moment().fromNow() + '</span>'
                    comment += '</div>'
                    comment += '<div class="text">'
                        comment += form.find('textarea').val()
                    comment += '</div>'
                comment += '</div>'
            comment += '</div>'
            $('#comment').append(comment)
            // hide form
            form.slideUp(150)
        }

        function formatSeconds (s, brut) {
            var fm = [
                Math.floor(s / 60 / 60 / 24), // DAYS
                Math.floor(s / 60 / 60) % 24, // HOURS
                Math.floor(s / 60) % 60, // MINUTES
                s % 60 // SECONDS
            ]
            var result = $.map(fm, function (v, i) {
                return v
            })

            if (brut)
                return result

            // formatting to string
            var durationFormatted = ''

            if (result[0] > 0)
                durationFormatted += result[0] + ' @lang('global.days') '
            if (result[1] > 0)
                durationFormatted += result[1] + ' @lang('global.hours') '
            if (result[2] > 0)
                durationFormatted += result[2] + ' @lang('global.minutes') '
            if (result[3] > 0)
                durationFormatted += result[3] + ' @lang('global.seconds') '

            return durationFormatted.slice(0, -1)
        }
    </script>
@endsection