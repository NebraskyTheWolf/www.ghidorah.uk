@extends('layouts.app')

@section('title', __('stats.factions.ranking'))

@section('content')
    <div class="ui container page-content">

        <div class="text-center head-top">
            <h1 class="ui header">
                <img src="{{ url('/img/logo-min.png') }}" class="ui circular image">
                <div class="content">
                    Fanction ranking
                    <div class="sub header">Fight to be the best!</div>
                </div>
            </h1>
        </div>

        <div class="ui divider"></div>

        <button class="ui button yellow" onclick="$('#details').modal('show')">
                See details of points
        </button>

        <table class="ui basic padded table" id="ranking">
            <thead>
            <tr>
                <th>Positon</th>
                <th>Name</th>
                <th>Game won</th>
                <th>Kill</th>
                <th>Death</th>
                <th>Score</th>
            </tr>
            </thead>
        </table>

    </div>

    <div class="ui modal" id="details">
        <div class="header">Points details</div>
        <div class="content">

            <table class="ui celled table">
                <thead>
                    <tr>
                        <th>Kills</th>
                        <th>Death</th>
                        <th>Coins</th>
                        <th>Claims / Players / Events</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Under <b>250</b> :<br><span style="color:#016936">+0.5 points</span> / Kills</td>
                        <td>Under <b>250</b> :<br><span style="color:#B03060">-0.8 points</span> / Deaths</td>
                        <td>Under <b>25 000</b> :<br><span style="color:#016936">+0.001 points</span> / Dollars</td>
                        <td><b>Claims :</b><br><span style="color:#016936">+1 points</span> / Claims</td>
                    </tr>
                    <tr>
                        <td>Of <b>250</b> to <b>500</b> :<br><span style="color:#016936">+0.8 points</span> / Kills</td>
                        <td>Of <b>250</b> to <b>500</b> :<br><span style="color:#B03060">-1 points</span> / Deaths</td>
                        <td>Of <b>25 000</b> to <b>50 000</b> :<br><span style="color:#016936">+0.002 points</span> / Dollars</td>
                        <td><b>Outposts :</b><br><span style="color:#016936">+350 points</span> / Outposts</td>
                    </tr>
                    <tr>
                        <td>Of <b>500</b> to <b>1 000</b> :<br><span style="color:#016936">+1 points</span> / Kills</td>
                        <td>Of <b>500</b> to <b>1 000</b> :<br><span style="color:#B03060">-1.2 points</span> / Deaths</td>
                        <td>Of <b>50 000</b> to <b>100 000</b> :<br><span style="color:#016936">+0.004 points</span> / Dollars</td>
                        <td><b>Player :</b><br><span style="color:#016936">+5 points</span> / Players</td>
                    </tr>
                    <tr>
                        <td>Above <b>1 000</b> :<br><span style="color:#016936">+1.2 points</span> / Kills</td>
                        <td>Above <b>1 000</b> :<br><span style="color:#B03060">-1.5 points</span> / Deaths</td>
                        <td>Of <b>100 000</b> to <b>250 000</b> :<br><span style="color:#016936">+0.008 points</span> / Dollars</td>
                        <td><b>Koth :</b><br><span style="color:#016936">+250 points</span> / Win</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>Above <b>250 000</b> :<br><span style="color:#016936">+0.016 points</span>/Dollars</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

            <div class="ui divider"></div>

            <h3>Ressources</h3>

            <div class="ui two column grid">
                <div class="column">
                    <table class="ui celled table">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Value</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Foxisium ingot</td>
                            <td><span style="color:#016936">+0.00015 points</span> / Ingot</td>
                        </tr>
                        <tr>
                            <td>Foxisium block</td>
                            <td><span style="color:#016936">+0.00135 points</span> / Block</td>
                        </tr>
                        <tr>
                            <td>Amethyst ingot</td>
                            <td><span style="color:#016936">+0.002 points</span> / Ingot</td>
                        </tr>
                        <tr>
                            <td>Amethyst block</td>
                            <td><span style="color:#016936">+0.018 points</span> / Block</td>
                        </tr>
                        <tr>
                            <td>Titanium ingot</td>
                            <td><span style="color:#016936">+0.003 points</span> / Ingot</td>
                        </tr>
                        <tr>
                            <td>Titanium block</td>
                            <td><span style="color:#016936">+0.027 points</span> / Block</td>
                        </tr>
                        <tr>
                            <td>Obsidian ingot</td>
                            <td><span style="color:#016936">+0.003 points</span> / Ingot</td>
                        </tr>
                        <tr>
                            <td>Obsidian block</td>
                            <td><span style="color:#016936">+0.027 points</span> / Block</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="column">
                    <table class="ui celled table">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Value</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Xerium ingot</td>
                            <td><span style="color:#016936">+3 points</span> / Ingot</td>
                        </tr>
                        <tr>
                            <td>Xerium block</td>
                            <td><span style="color:#016936">+27 points</span> / Block</td>
                        </tr>
                        <tr>
                            <td>TNT</td>
                            <td><span style="color:#016936">+0.01 points</span> / TNT</td>
                        </tr>
                        <tr>
                            <td>Xerium TNT</td>
                            <td><span style="color:#016936">+9 points</span> / TNT</td>
                        </tr>
                        <tr>
                            <td>Enderpearl</td>
                            <td><span style="color:#016936">+0.005 points</span> / Enderpearl</td>
                        </tr>
                        <tr>
                            <td>Golden aaplwe</td>
                            <td><span style="color:#016936">+0.1 points</span> / Aaplwe</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

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
            'serverSide': true,
            'ajax': '{{ env('DATA_SERVER_ENDPOINT') }}/factions',
            'pageLength': 25,
            'lengthChange': false,
            'columns': [
                {"data": "position", "name": "position"},
                {"data": "name", "name": "name"},
                {"data": "claims_count", "name": "claims_count"},
                {"data": "kills_count", "name": "kills_count"},
                {"data": "deaths_count", "name": "deaths_count"},
                {"data": "score", "name": "score"}
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
                        return '<b><a href="{{ url('/stats/faction') }}/' + data + '">' + data + '</a></b>';
                    },
                    "targets": 1
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