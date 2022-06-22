<div class="ui inverted vertical footer segment">
  <div class="ui container">
    <div class="ui stackable inverted divided equal height stackable grid">
      <div class="three wide column text-center">
        <h4 class="ui inverted header">@lang('global.footer.title.info')</h4>
        <div class="ui inverted link list">
          <a href="http://forum.ghidorah.eu/forums/bugs-site-and-shop.104/" class="item">@lang('global.footer.report-bug')</a>
          <a href="http://forum.ghidorah.eu/misc/contact" class="item">@lang('global.footer.contact')</a>
          <a href="{{ url('/join') }}" class="item">@lang('global.footer.join')</a>
          <a href="{{ url('/shop') }}" class="item">@lang('global.footer.shop')</a>
        </div>
      </div>
      <div class="three wide column text-center">
        <h4 class="ui inverted header">@lang('global.footer.title.services')</h4>
        <div class="ui inverted link list">
          <a href="https://forum.ghidorah.eu" class="item">@lang('global.footer.forum')</a>
          <a href="https://ghidorah.eu/wiki" class="item">@lang('global.footer.wiki')</a>
          <a href="http://incidents.ghidorah.eu" class="item">@lang('global.footer.incidents')</a>
        </div>
      </div>
      <div class="three wide column">
        <div class="text-center">
          <a href="https://www.facebook.com/SKFIndustries/" class="ui facebook button">
            <i class="facebook icon"></i> Facebook
          </a><br><br>
          <a href="https://twitter.com/SKFIndustries" class="ui twitter button">
            <i class="twitter icon"></i> Twitter
          </a><br><br>
          <a href="https://www.youtube.com/user/SKFIndustries" class="ui youtube button">
            <i class="youtube icon"></i> YouTube
          </a>
        </div>
      </div>
      <div class="seven wide column text-center">
        <h4 class="ui inverted header">@lang('global.footer.title.credits')</h4>
        <p>@lang('global.footer.credit', ['link' => '', 'username' => ''])</p>
        @if(defined('VERSION'))
          <p>@lang('global.footer.version', ['version' => VERSION])</p>
        @endif
      </div>
    </div>
  </div>
</div>
