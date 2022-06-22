@extends('layouts.app')

@section('title', 'Servers partnership')

@section('content')
    <div class="ui container page-content">

        <div class="text-center head-top">
            <h1 class="ui header">
                <img src="{{ url('/img/logo-min.png') }}" class="ui circular image">
                <div class="content">
                     Partnership requirement.
                    <div class="sub header">Here you'll find our requirements for being partner with SKFStudios</div>
                </div>
            </h1>
        </div>

        <div class="ui divider"></div>

        <div class="ui info message">
            <div class="header">
                    Eligible
             </div>
            <p>Your server must have 50+ members.</p>
            <p>The owner are not blacklisted.</p>
            <p>The server are SFW and safe for peoples over 16.</p>
            <p>The server must be moderated.</p>
            <p>The server don't have any abusive staff.</p>
            <p>The server have at least 100+ messages daily</p>
        </div>

        <div class="ui error message">
            <div class="header">
                    Not Eligible
             </div>
            <p>Your server are less than 50 members.</p>
            <p>The server owner are banned/blacklisted from SKFStudios</p>
            <p>The server is NSFW or inappropriate for minor.</p>
            <p>The server is a toxic community.</p>
            <p>The server is not active or dead.</p>
            <p>The server failed the daily messages for 6 weeks.</p>
        </div>

        <div class="ui divider"></div>

        <div class="ui error message">
            <div class="header">
                   Warning
             </div>
            <p>If the server owner leave our discord the partnership will be terminated over <span>24 hours</span>.</p>
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