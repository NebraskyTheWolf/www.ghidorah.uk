@extends('layouts.app')

@section('title', __('join.title'))

@section('content')
  <div class="ui container page-content">
    <div class="text-center">
      <img src="{{ url('/img/logo-banner.png') }}" height="170" alt="Logo">
    </div>

  </div>
  <div class="white-block">
      <div class="ui container text-center">
        <h2 class="ui header">
          <img src="{{ url('/img/logo-min.png') }}" class="ui circular image">
          <div class="content">
            Discord
            <div class="sub header">@lang('join.step.four.subtitle')</div>
          </div>
        </h2>
        <a class="ui primary labeled icon big button" href="https://discord.gg/DuEJMqxcfY" style="background-color: #7289da;border-color:#7289da;">
          <i class="discord icon">
            <img src="{{ url('/img/discord.png') }}" style="width: 32px;margin-top: 5px;" alt="">
          </i> @lang('join.discord')
        </a>
      </div>
  </div>
  
@endsection
@section('style')
  <style media="screen">
    .ui.button>.icon:not(.button) {
      height: auto;
    }
    .ui.circular.button>.icon {
      width: auto;
    }
    .ui.circular.button i.icon {
      font-size: 3em;
    }
    .ui.circular.button {
      height: 120px!important;
      padding: 45px 10px!important;
      width: 120px;
    }
  </style>
@endsection
