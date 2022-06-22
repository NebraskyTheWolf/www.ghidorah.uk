@extends('layouts.app')

@section('title', __('user.signup'))

@section('content')
  <div class="sub-header rotate">
    <div class="ui large header">@lang('user.signup.join_now')</div>
  </div>
  <div class="parallax-block" style="background-image: url('{{ url('/img/parallax-2.jpg') }}');">
    <div class="ui container page-content">

      <div class="ui grid">
        <div class="ten wide computer only column explain">
          <h2>Why sign up for SKFStudios?</h2>

          <p>We have chosen to create a player registration system, holding player IDs and other data in our database. This allows us to add a lot of features, in addition to optimal protection.</p>

          <ul class="list-unstyled login-features">
            <li>
              <i class="lock icon"></i> Our databases are among the most secure.
            </li>
            <li>
              <i class="shield icon"></i> FoxGuard protects your accounts from password theft.
            </li>
            <li>
              <i class="crosshairs icon"></i> A perfect game quality for optimal playing.
            </li>
            <li>
              <i class="question circle icon"></i> Moderators are available to answer your questions.
            </li>
            <li>
              <i class="legal icon"></i> A complete forum to share your exploits.
            </li>
            <li>
              <i class="microphone icon"></i> A Discord designed to host all our members.
            </li>
            <li>
              <i class="key icon"></i> And Events unlocking unprecedented benefits!
            </li>
          </ul>
        </div>
        <div class="sixteen wide mobile sixteen wide tablet six wide computer column">
          <div class="ui raised padded segment">
            <h2 class="ui header">
              <i class="signup icon"></i>
              <div class="content">
                @lang('user.signup')
              </div>
            </h2>
            <div class="ui divider"></div>

            <form method="post" data-ajax class="ui form" id="searchUser">
                <div class="field">
                  <label>Find your account with your code</label>
                  <div class="ui left icon input">
                    <input type="text" name="tokenTemp" placeholder="00000000-0000-0000-0000-000000000000" id="tokenTemp">
                    <i class="user icon"></i>
                  </div>
                </div>
            </form>


            <form method="post" action="{{ url('/signup') }}" data-ajax class="ui form" id="register-modal">
              <div class="field">
                  <label>Access code</label>
                  <div class="ui left icon input">
                    <input type="text" name="token" placeholder="00000000-0000-0000-0000-000000000000" id="token" value="" readonly>
                    <i class="user icon"></i>
                  </div>
                </div>
              <div class="field">
                <label>@lang('user.field.username')</label>
                <div class="ui left icon input">
                  <input type="text" name="username" placeholder="" id="username" value="" readonly>
                  <i class="user icon"></i>
                </div>
              </div>
              <div class="field">
                <label>Discord ID</label>
                <div class="ui left icon input">
                  <input type="text" name="discordid" placeholder="" id="discordid" value="" readonly>
                  <i class="user icon"></i>
                </div>
              </div>
              <div class="field">
                <label>@lang('user.field.email')</label>
                <div class="ui left icon input">
                  <input type="email" name="email" placeholder="contact@kibblelands.net">
                  <i class="mail icon"></i>
                </div>
              </div>
              <div class="field">
                <label>@lang('user.field.password')</label>
                <div class="ui left icon input">
                  <input type="password" name="password" placeholder="*********">
                  <i class="lock icon"></i>
                </div>
              </div>
              <div class="ui indicating progress password-strengh">
                <div class="bar"></div>
              </div>
              <div class="field">
                <label>@lang('user.field.password')</label>
                <div class="ui left icon input">
                  <input type="password" name="password_confirmation" placeholder="*********">
                  <i class="lock icon"></i>
                </div>
              </div>
              <div class="field">
                <div class="ui checkbox">
                  <input type="checkbox" tabindex="0" name="legal" class="hidden">
                  <label>@lang('user.signup.field.legal', ['link' => 'https://skf-studios.com/rules'])</label>
                </div>
              </div>
              <div class="text-center">
                <button type="submit" class="ui red submit button">@lang('user.signup')</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
@endsection
@section('style')
  <style media="screen">
    .explain {
      font-weight: 300;
      font-size: 15px;
    }
    .explain ul {
      padding-left: 0;
      list-style: none;
    }
    .explain ul li {
      padding: 8px 0;
      font-size: 16px;
      font-weight: 300;
      line-height: 30px;
    }

    .progress.password-strengh {
      margin-bottom: 10px;
    }
  </style>
@endsection
@section('script')
  <script type="text/javascript">
    $(document).ready(function () {
      $('#register-modal').hide();

      $('input[name="tokenTemp"]').on('keyup', function() {
          var value = $(this).val();

          /// [0-9]{3}-[0-9]{3}-[0-9]{2}

          var uuidRegex = new RegExp("[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}");

          if (uuidRegex.test(value)) {
             $('#tokenTemp').prop('disabled', true);
             $('#searchUser').hide();

             $('#register-modal').show();

             fetch(`https://api.skf-studios.com:8443/user/by-token/${value}`)
              .then(response => response.json())
              .then(data => {
                  if (data.error === undefined) {
                    $('input[name="token"]').prop('value', value);
                    $('input[name="username"]').prop('value', data.user.username);
                    $('input[name="discordid"]').prop('value', data.id);
                  }
              }).catch(() => {
                 $('#register-modal').hide();
                 $('#tokenTemp').prop('disabled', false);
                 $('#tokenTemp').val("");
                 $('#searchUser').show();
              });
          } else {
            $('#register-modal').hide();
            $('#searchUser').show();
          }
      });

      $('.password-strengh').progress({percent: 0})

      $('input[name="password"]').on('keyup', function () { // Test strengh
        var value = $(this).val()
        // Must have capital letter, numbers and lowercase letters
        var strongRegex = new RegExp("^(?=.{8,})(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*\\W).*$", "g")
        // Must have either capitals and lowercase letters or lowercase and numbers
        var mediumRegex = new RegExp("^(?=.{7,})(((?=.*[A-Z])(?=.*[a-z]))|((?=.*[A-Z])(?=.*[0-9]))|((?=.*[a-z])(?=.*[0-9]))).*$", "g")
        // Must be at least 6 characters long
        var okRegex = new RegExp("(?=.{6,}).*", "g")

        if (strongRegex.test(value))
          $('.password-strengh').progress({percent: 100})
        else if(mediumRegex.test(value))
          $('.password-strengh').progress({percent: 60})
        else if(okRegex.test(value))
          $('.password-strengh').progress({percent: 30})
        else
          $('.password-strengh').progress({percent: 10})
      })
    })
  </script>
@endsection
