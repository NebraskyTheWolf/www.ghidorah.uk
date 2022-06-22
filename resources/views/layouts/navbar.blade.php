<div class="ui menu navbar">
  <div class="logo">
    <img src="{{ url('/img/logo-min.png') }}" width="60" alt="Logo">
  </div>

  <a onClick="$('.ui.menu.navbar.sidebar').sidebar('toggle')" class="item mobile-menu">
    <i class="sidebar icon"></i>
    Menu
  </a>

  <a href="{{ url('/') }}" class="item"><i class="home icon"></i> @lang('global.navbar.home')</a>
  <a href="https://discord.gg/cnBerxRBEG" class="item"><i class="share icon"></i> @lang('global.navbar.join')</a>

  <a href="{{ url('/shop') }}" class="ui item">
    <i class="shop icon"></i> @lang('global.navbar.shop')
  </a>

  <div class="ui dropdown item">
    <i class="globe icon"></i> @lang('global.navbar.community')
    <i class="dropdown icon"></i>
    <div class="menu">
      <a href="{{ url('/servers') }}" class="item" disabled><i class="user plus icon"></i> Servers</a>
      <a href="{{ url('/rooms') }}" class="item" disabled><i class="video icon"></i> Movie rooms</a>
      <a href="{{ url('/games') }}" class="item" disabled><i class="trophy icon"></i> Games</a>
      <a href="{{ url('/furcons') }}" class="item" disabled><i class="rocket icon"></i> Furcons</a>
    </div>
  </div>

  <div class="ui dropdown item">
    <i class="help icon"></i> @lang('global.navbar.help')
    <i class="dropdown icon"></i>
    <div class="menu">
      <a href="{{ url('/wiki') }}" class="item"><i class="info icon"></i> @lang('global.navbar.wiki')</a>
      <a href="{{ url('/faq') }}" class="item"><i class="help circle icon"></i> @lang('global.navbar.faq')</a>
    </div>
  </div>

  <div class="right menu">
    @if (Auth::check())
      <div class="item">
        <a href="{{ url('/user') }}" class="ui obsifight button"><i class="user icon"></i> {{ Auth::user()->username }}</a>
      </div>
      <div class="item">
        <a href="{{ url('/logout') }}" class="ui button"><i class="sign out icon"></i> @lang('user.logout')</a>
      </div>
    @else
      <div class="item">
        <a href="{{ url('/signup') }}" class="ui obsifight button"><i class="signup icon"></i> @lang('user.signup')</a>
      </div>
      <div class="item">
        <a href="{{ url('/login') }}" class="ui button"><i class="sign in icon"></i> @lang('user.login')</a>
      </div>
    @endif
  </div>
</div>
