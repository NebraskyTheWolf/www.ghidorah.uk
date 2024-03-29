@extends('layouts.app')

@section('title', 'PreSignup')

@section('content')
  <div class="ui container page-content">
    <h2 class="ui center aligned icon header">
      <i class="circular protect icon"></i>
       Please enter the access code you got with GHIDORAH on the server
    </h2>

    <div class="text-center">
      <img src="{{ $qrCodeUrl }}" alt="">
      <p>
        <small style="color:#777;">@lang('user.two_factor_auth.field.secret', ['secret' => $secret])</small>
      </p>

      <form class="ui form" method="post"  action="{{ url('/presignup') }}" data-ajax>

        <div class="field">
          <label>Enter access code</label>
          <input type="text" name="code" style="width:200px;">
        </div>

        <button type="submit" class="ui green button">Next</button>
      </form>
    </div>
  </div>
@endsection
