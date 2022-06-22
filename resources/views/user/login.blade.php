@extends('layouts.app')

@section('title', __('user.login'))

@section('content')
  <div class="ui container page-content">

    <div class="ui two column middle aligned very relaxed stackable grid" id="login-content">
      <div class="column">
        <h1 class="ui header">
          <i class="sign in icon"></i>
          <div class="content">
            @lang('user.login')
          </div>
        </h1>
        <div class="ui divider"></div>
        <form method="post" action="{{ url('/login') }}" data-ajax data-ajax-custom-callback="afterLogin" class="ui form">
          <div class="field">
            <label>@lang('user.field.username')</label>
            <div class="ui left icon input">
              <input type="text" name="username" placeholder="Vakea">
              <i class="user icon"></i>
            </div>
          </div>
          <div class="field">
            <label>@lang('user.field.password') <small><em><a href="#" onClick="$('.ui.modal#forgotPassword').modal({blurring: true}).modal('show')">@lang('user.password.forgot')</a></em></small></label>
            <div class="ui left icon input">
              <input type="password" name="password" placeholder="*********">
              <i class="lock icon"></i>
            </div>
          </div>
          <div class="field">
            <div class="ui checkbox">
              <input type="checkbox" tabindex="0" name="remember_me" class="hidden">
              <label>@lang('user.field.remember_me')</label>
            </div>
          </div>
          <button type="submit" class="ui blue submit button">@lang('user.login')</button>
        </form>
      </div>
      <div class="ui vertical divider">
        @lang('global.or')
      </div>
      <div class="center aligned column">
        <a href="{{ url('/signup') }}" class="ui big green labeled icon button">
          <i class="signup icon"></i>
          @lang('user.signup')
        </a>
      </div>
    </div>
    <div id="two-factor-auth-content" style="display:none;">
      <div class="ui centered text-center column">
        <h1 class="ui center aligned header">
          <i class="sign in icon"></i>
          <div class="content">
            @lang('user.login.two_factor_auth')
          </div>
        </h1>
        <form method="post" action="{{ url('/login/two-factor-auth') }}" data-ajax data-ajax-custom-callback="afterLogin" class="ui form">
          <div class="field">
            <label>@lang('user.field.two_factor_auth_code')</label>
            <div class="ui left icon input" id="code">
              <input type="text" name="code" placeholder="@lang('user.field.two_factor_auth_code.placeholder')">
              <i class="lock icon"></i>
            </div>
          </div>
          <button type="submit" class="ui blue submit button">@lang('user.login')</button>
        </form>
      </div>
    </div>

  </div>

  <div class="ui modal" id="forgotPassword">
    <i class="close icon"></i>
    <div class="header">
      @lang('user.password.forgot')
    </div>
    <div class="content">
      <form action="{{ url('/user/password/forgot') }}" method="post" data-ajax>
      <div class="ui form">
        <h4 class="ui dividing header">@lang('user.password.forgot.subtitle')</h4>
        <div class="field">
          <label>@lang('user.field.email')</label>
          <input type="text" name="email">
        </div>
      </div>
    </div>
    <div class="actions">
      <button type="submit" class="ui green button">@lang('user.password.forgot.send')</button>
      </form>
    </div>
  </div>
@endsection
@section('style')
  <style media="screen">
    .ui.grid>.column+.divider, .ui.grid>.row>.column+.divider {
      left: 50%;
    }
    .grid {
      position: relative;
    }
    .page-content {
      padding-top: 30px;
      padding-bottom: 30px;
    }
    .input#code {
      width: 520px;
    }
    h1.center.aligned i,
    h1.center.aligned .content {
      display: inline-block!important;
    }
  </style>
@endsection
@section('script')
  <script type="text/javascript">
    function afterLogin(req, res) {
      var redirect = function () {
        if (from = getURLParameter('from'))
          document.location = from
        else
          document.location = '{{ url('/') }}'
      }

      if (!res.twoFactorAuth)
        return redirect()
      $('#login-content').slideUp(150)
      $('#two-factor-auth-content').slideDown(150)
    }
  </script>
@endsection
