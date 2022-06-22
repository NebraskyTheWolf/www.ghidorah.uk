@extends('layouts.app')

@section('title', $servername . ' leaderboard')

@section('content')
    <div class="ui container page-content">

        <div class="text-center head-top">
            <h1 class="ui header">
                <img src="{{ url($servericon) }}" class="ui circular image">
                <div class="content">
                    {{ $servername }} leaderboards
                    <div class="sub header">Send message and get more xp and perks!</div>
                </div>
            </h1>
        </div>

        <div class="ui divider"></div>

        <button class="ui button yellow" onclick="$('#details').modal('show')">
                See details of points
        </button>

        <div class="ui divider" id="waitingDivider"></div>

        <table class="ui basic padded table" id="ranking">
            <thead>
                <tr>
                    <th>Position</th>
                    <th>Username</th>
                    <th>Level</th>
                    <th>Scores</th>
                </tr>
                <tbody>
                    @foreach ($datatable as $data)
                        <tr>
                            <td>{{ $data->position }}</td>
                            <td>{{ $data->username }}</td>
                            <td>{{ $data->level }}</td>
                            <td>{{ $data->xp }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </thead>
        </table>
    </div>

    <div class="ui modal" id="details">
        <div class="header">Points details</div>
        <div class="content">

            <table class="ui celled table">
                <thead>
                    <tr>
                        <th>Messages</th>
                        <th>Voice Chat</th>
                        <th>Playing games</th>
                        <th>Events</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Under <b>250</b> :<br><span style="color:#016936">+0.5 points</span> / Messages</td>
                        <td></td>
                        <td>Under <b>25 000</b> :<br><span style="color:#016936">+0.001 points</span> / Dollars</td>
                        <td><b>Claims :</b><br><span style="color:#016936">+1 points</span> / Claims</td>
                    </tr>
                    <tr>
                        <td>Of <b>250</b> to <b>500</b> :<br><span style="color:#016936">+0.8 points</span> / Messages</td>
                        <td></td>
                        <td>Of <b>25 000</b> to <b>50 000</b> :<br><span style="color:#016936">+0.002 points</span> / Dollars</td>
                        <td><b>Events :</b><br><span style="color:#016936">+350 points</span> / Events</td>
                    </tr>
                    <tr>
                        <td>Of <b>500</b> to <b>1 000</b> :<br><span style="color:#016936">+1 points</span> / Messages</td>
                        <td></td>
                        <td>Of <b>50 000</b> to <b>100 000</b> :<br><span style="color:#016936">+0.004 points</span> / Dollars</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Above <b>1 000</b> :<br><span style="color:#016936">+1.2 points</span> / Kills</td>
                        <td></td>
                        <td>Of <b>100 000</b> to <b>250 000</b> :<br><span style="color:#016936">+0.008 points</span> / Dollars</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>Above <b>250 000</b> :<br><span style="color:#016936">+0.016 points</span> / Dollars</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('style')
    <link rel="stylesheet" href="{{ url('/css/dataTables.semanticui.min.css') }}">
    <style>
        .table a {
            color: #0f0f10;
        }

        .table a:hover {
            color: #0f0f10;
            text-decoration: underline;
        }

        .head-top {
            margin-top: -6%;
            z-index: 10;
            margin-left: -20px;
            position: relative;
        }
    </style>
@endsection
@section('script')
    <script type="text/javascript" src="{{ url('/js/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('/js/dataTables.semanticui.min.js') }}"></script>
    <script type="text/javascript">
        var round = function (nb) {
            return Math.round(nb * 100) / 100;
        };

        $('#ranking').DataTable({
            'processing': true,
            'serverSide': false,
            'pageLength': 25,
            'lengthChange': false,
            'columns': [
                {"data": "position", "name": "position"},
                {"data": "userID", "name": "userID"},
                {"data": "level", "name": "level"},
                {"data": "score", "name": "score"},
            ],
            "columnDefs": [
                {
                    "render": function (data, type, row) {
                        if (parseInt(data) === 1)
                            return ' <i class="icon trophy"></i>' + data;
                        else
                            return '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' + data;
                    },
                    "targets": 0
                },
                {
                    "render": function (data, type, row) {
                        var details = JSON.parse(row.details);
                        var detailsString = '';
                        detailsString += '<b>Kills:</b> <span style=\'color: #016936\'>+' + round(details.kills) + ' points</span>';
                        detailsString += '<br><b>Deaths:</b> <span style=\'color: #B03060\'>' + round(details.deaths) + ' points</span>';
                        detailsString += '<br><b>Money:</b> <span style=\'color: #016936\'>+' + round(details.money) + ' points</span>';
                        detailsString += '<br><b>Game won:</b> <span style=\'color: #016936\'>+' + round(0) + ' points</span>';
                        detailsString += '<br><b>Game lost:</b> <span style=\'color: #016936\'>+' + round(0) + ' points</span>';
                        detailsString += '<br><b>Players:</b> <span style=\'color: #016936\'>+' + round(0) + ' points</span>';
                        detailsString += '<br><b>Ressources:</b> <span style=\'color: #016936\'>+' + round(details.materials) + ' points</span>';

                        return data + '<div style="float: right" class="ui yellow button" data-placement="right center" data-toggle="popup"\n' +
                            '        data-content="' + detailsString + '">\n' +
                            '        Détails\n' +
                            '</div>';
                    },
                    "targets": 5
                }
            ],
            'createdRow': function (row, data, dataIndex) {
                if (data.position === 1)
                    $(row).css('background-color', 'rgba(255, 215, 0, 0.3)');
                else if (data.position === 2)
                    $(row).css('background-color', 'rgba(192, 192, 192, 0.3)');
                else if (data.position === 3)
                    $(row).css('background-color', 'rgba(205, 127, 50, 0.3)');
            },
            'drawCallback': function (settings, json) {
                $('[data-toggle="popup"]').each(function (k, el) {
                    $(el).popup({
                        html: $(el).attr('data-content'),
                        position: $(el).attr('data-placement')
                    })
                })
            },
            'language': datatableLang
        });
    </script>
@endsection