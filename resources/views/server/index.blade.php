@extends('layouts.app')

@section('title', 'Server lists')

@section('content')
    <div class="ui container page-content">

        <div class="text-center head-top">
            <h1 class="ui header">
                <div class="content">
                    Server list
                    <div class="sub header">Here is all the servers using our bot!</div>
                </div>
            </h1>
        </div>

        <div class="ui divider"></div>

        <a href="https://discord.com/oauth2/authorize?client_id=969025841134714901&permissions=8&scope=bot" class="ui button yellow">
            <i class="plus icon"></i>
            Invite GHIDORAH
        </a>

        <a href="https://www.patreon.com/SKFStudios" class="ui button yellow">
            <i class="heart icon" style="color:#DC143C;"></i>
            Patreon
        </a>

        <div class="ui divider"></div>
        </br>
        </br>
        <div class="ui grid container">
            <div class="ui three stackable cards">
                <div class="ui special cards">
                    @foreach ($servers as $server)
                        <div class="ui card">
                            <div class="blurring dimmable image">
                                <div class="ui dimmer">
                                    <div class="content">
                                        <div class="center">
                                            <a class="ui inverted button" href="https://discord.gg/{{ $server['invites'][0] }}">Join server</a>
                                        </div>
                                    </div>
                                </div>
                                <img src="{{ $server['iconURL'] }}">
                            </div>
                            <div class="content">
                                <a href="{{ url('/server/' . $server['id']) }}" class="ui blue image medium label">
                                    {{ $server['name'] }}
                                    <div class="detail">Profile</div>
                                </a>
                                <div class="meta"></div>
                                <div class="description">
                                    {{ $server['name'] }} is a great bean server! Come and say hello to us!
                                </div>
                            </div>
                            <div class="extra content">
                                <a>
                                    <i class="user icon"></i>
                                    {{ $server['memberCount'] }} Members
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        </br>
        <div class="ui divider"></div>

        <div class="ui info message">
            <div class="header">
                    Informations
             </div>
            <p>All the servers are protected by GHIDORAH our bot permit to help server owners to protect and keep a safe community and avoiding threats on servers.</p>
            <p>The bot is free and furry friendly! our bot is still in development and need to build a community around it!</p>
            <p>Hope you'll enjoy our bot!</p>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        $('.special.cards .image').dimmer({
            on: 'hover'
        });
    </script>
@endsection