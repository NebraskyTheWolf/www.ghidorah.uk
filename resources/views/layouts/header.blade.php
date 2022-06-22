<div class="site-header" style="background-image: url('/img/background-min.png')">
  @include('layouts.navbar')
  <div class="ui container">
    @if (isset($didYouKnow))
      <h2 class="ui header">
        <i class="info icon"></i>
        <div class="content">
          @lang('global.header.did-you-know')
        </div>
        <p>{{ $didYouKnow }}</p>
      </h2>
    @endif
  </div>
</div>
