@extends('layouts.app')

@section('title', __('vote.title'))

@section('content')
    <div class="ui container page-content">
        <div class="ui info message">
            <div class="header">
                @lang('vote.position', ['position' => '<span id="position">&nbsp;&nbsp;<div class="ui active inline tiny loader"></div>&nbsp;&nbsp;</span>'])
            </div>
            <p>
                @lang('vote.tutorial.title')
            </p>
        </div>

        <div class="ui ordered fluid stackable top attached steps">
            <div class="active step" data-step-display="1">
                <div class="content">
                    <div class="title">Verification</div>
                    <div class="description">Enter your verification code.</div>
                </div>
            </div>
            <div class="disabled step" data-step-display="2">
                <div class="content">
                    <div class="title">Checking</div>
                    <div class="description">Please verify the informations bellow</div>
                </div>
            </div>
            <div class="disabled step" data-step-display="3">
                <div class="content">
                    <div class="title">Questions</div>
                    <div class="description">Please answer to our questions.</div>
                </div>
            </div>
            <div class="disabled step" data-step-display="4">
                <div class="content">
                    <div class="title">Result</div>
                    <div class="description">Waiting to be verified.</div>
                </div>
            </div>
        </div>
        <div class="ui attached segment">
            <div data-step="1" class="active">
                <form class="ui form" method="post" action="{{ url('/verify/step/one') }}" data-ajax
                      data-ajax-custom-callback="afterStepOne">

                    <div class="field">
                        <label>Code</label>
                        <input type="text" name="code" placeholder="000-000-00" style="width:200px;text-align:center;" maxlength="10">
                    </div>

                    <button type="submit" class="ui green animated button">
                        <div class="visible content">Next</div>
                        <div class="hidden content"><i class="right arrow icon"></i></div>
                    </button>
                </form>
            </div>
            <div data-step="2">
                <form class="ui form" method="post" action="{{ url('/verify/step/two') }}" data-ajax
                      data-ajax-custom-callback="afterStepTwo">

                    <label>That's you?</label>

                    <div class="field">
                        <label>Username</label>
                        <input type="text" name="username" placeholder="Vakea" style="width:200px;text-align:center;" readonly>
                    </div>

                    <div class="field">
                        <label>DiscordID</label>
                        <input type="text" name="discordid" placeholder="Vakea" style="width:200px;text-align:center;" readonly>
                    </div>

                    <div class="field">
                        <label>Server</label>
                        <input type="text" name="servername" placeholder="Vakea" style="width:200px;text-align:center;" readonly>
                    </div>

                    <button type="submit" class="ui green animated button">
                        <div class="visible content">Next</div>
                        <div class="hidden content"><i class="right arrow icon"></i></div>
                    </button>
                </form>
            </div>
            <div data-step="3">
                <div class="ui info message">
                    <div class="header">
                        @lang('global.info')
                    </div>
                    <p>Please be specific. If your verification are not specific you will be denied.</p>
                    <p>Min age requirement <span>13+</span></p> 
                    <p>Don't lie on your age you will cause yourself trouble and you will be blacklisted from this server</p>
                </div>
                <form class="ui form" method="post" action="{{ url('/verify/step/three') }}" data-ajax
                      data-ajax-custom-callback="afterStepThree">

                    <div class="field">
                        <label>How did you find us?</label>
                        <textarea name="findus" autocomplete="off"
                               placeholder="please be specific answer like 'goggle' or 'website' will be declined"></textarea>
                    </div>

                    <div class="field">
                        <label>How old are you?</label>
                        <input type="number" name="age" placeholder="13" style="width:200px;text-align:center;"> 
                    </div>

                    <div class="field">
                        <label>What is a furry for you?</label>
                        <textarea name="furry" autocomplete="off"
                               placeholder="In your own words please."></textarea>
                    </div>

                    <div class="field">
                        <label>Do you have a fursona?</label>
                        <textarea name="fursona" autocomplete="off"
                               placeholder="If so, could you describe them?"></textarea>
                    </div>

                    <div class="field">
                        <label>Have you read the rules?</label>
                        <div class="ui checkbox">
                            <input type="checkbox" tabindex="0" name="rules" class="hidden"> 
                            <label>I accept the <a href=\"https://skf-studios.com/rules\">conditions</a> of SKFStudios</label>
                        </div>
                    </div>

                    <input type="text" name="pending" placeholder="Vakea" style="width:200px;text-align:center;" hidden>
                    <input type="text" name="targetserver" placeholder="Vakea" style="width:200px;text-align:center;" hidden>

                    <button type="submit" class="ui green animated button">
                        <div class="visible content">@lang('vote.step.three.content.input.btn')</div>
                        <div class="hidden content"><i class="right arrow icon"></i></div>
                    </button>
                </form>
            </div>
            <div data-step="4">
                <div id="verificationWait">
                    <div class="ui icon info message">
                        <i class="notched circle loading icon"></i>
                        <div class="content">
                        <div class="header">
                            Please wait...
                        </div>
                        <p>We're reading your verification..</p>
                        </div>
                    </div>
                </div>

                <div id="serverError" hidden>
                    <div class="ui icon error message">
                        <i class="notched circle loading icon"></i>
                        <div class="content">
                        <div class="header">
                            Server unreachable..
                        </div>
                        <p>Trying to reconnect with SKF Server please wait...</p>
                        </div>
                    </div>
                </div>

                <div class="ui info message" id="verificationAccepted" hidden>
                    <div class="header">
                        Verification accepted!
                    </div>
                    <p>Your verification has been accepted, please check on our discord server!</p>
                </div>

                <div class="ui error message" id="verificationDenied" hidden>
                    <div class="header">
                        We're sorry but your verification has been denied.
                    </div>
                    <p>Please try again and be more specific on your application.</p>
                </div>

            </div>
        </div>

    </div>
@endsection
@section('script')
    <script type="text/javascript">
        function next(step) {
            $('[data-step-display="' + (step - 1).toString() + '"]').removeClass('active').addClass('completed')
            $('[data-step-display="' + step.toString() + '"]').removeClass('disabled').addClass('active')
        }

        function afterStepOne(req, res) {
            $('[data-step="1"]').slideUp(100, function () {
                $('[data-step="2"]').slideDown(100)
                next(2)
            })
        }

        function afterStepTwo(req, res) {
            $('[data-step="2"]').slideUp(100, function () {
                $('[data-step="3"]').slideDown(100)
                next(3)
            })
        }

        function afterStepThree(req, res) {
            $('[data-step="3"]').slideUp(150, function () {
                $('[data-step="4"]').slideDown(150)
                next(4)
            })
        }

        function beforeStepFour(form, btn) {
            if (btn.attr('data-reward-type') == 'now')
                return {type: 'now'}
            else
                return {type: 'after'}
        }

        function afterStepFour(req, res) {
            $('[data-reward-type="now"]').addClass('disabled')
            $('[data-reward-type="after"]').addClass('disabled')
        }

        $(document).ready(function () {
            $.ajaxSetup({ cache: false });

            $('[data-step="2"] a').on('click', function () {
                $('[data-step="2"]').slideUp(100, function () {
                    $('[data-step="3"]').slideDown(100)
                    next(3)
                })
            })
            $('[data-toggle="popup"]').each(function (k, el) {
                $(el).popup({
                    html: $(el).attr('data-content'),
                    position: $(el).attr('data-placement')
                })
            })

            var finalId = "";

            $('input[name="code"]').on('keyup', function () {
                var value = $(this).val();
                var codeRegex = new RegExp("[0-9]{3}-[0-9]{3}-[0-9]{2}");

                if (codeRegex.test(value)) {
                    localStorage.setItem('code', value);
                    fetch(`https://api.skf-studios.com:8443/user/verify/${value}/fetchByCode`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status) {
                            $('input[name="username"]').prop('value', data.data.data.username);
                            $('input[name="discordid"]').prop('value', data.data.id);
                            localStorage.setItem('discordId', data.data.id);
                            fetch(`https://api.skf-studios.com:8443/servers/by-id/${data.data.guildId}/config`)
                                .then(response => response.json())
                                .then(fdata => {
                                    if (fdata.status) {
                                        $('input[name="servername"]').prop('value', fdata.data.name);
                                        localStorage.setItem('guildName', fdata.data.name);
                                        localStorage.setItem('guildId', fdata.data.id);
                                        localStorage.setItem('guildIcon', fdata.data.iconURL);
                                        localStorage.setItem('guildBanner', fdata.data.splashURL);
                                    }
                                });
                        }
                    })
                    .catch(() => {
                        $('input[name="code"]').prop('value', '');
                    });
                }
            });
            $('textarea[name="findus"]').on('keyup', function () {
                $('input[name="pending"]').prop('value', localStorage.getItem('discordId'));
                $('input[name="targetserver"]').prop('value', localStorage.getItem('guildId'));
            });
           
           setInterval(() => {
                if (localStorage.getItem('code') === undefined) return;

                fetch(`https://api.skf-studios.com:8443/user/verify/${localStorage.getItem('discordId')}/${localStorage.getItem('guildId')}/fetch`)
                    .then(response => response.json())
                    .then(data => {
                        $('#serverError').hide();
                        console.log(`${data.data.verifiedId}`);

                        switch (data.data.verifiedId) {
                            case 'verified': {
                                $('[data-step="3"]').slideUp(150, function () {
                                    $('[data-step="4"]').slideDown(150)
                                    next(4)
                                });

                                $('#verificationWait').hide();
                                $('#verificationAccepted').show();
                                $('#verificationDenied').hide();

                                localStorage.removeItem('code');
                                localStorage.removeItem('discordId');
                            }
                            break;
                            case 'denied': {
                                $('#verificationWait').hide();
                                $('#verificationAccepted').hide();
                                $('#verificationDenied').show();
                                
                                localStorage.removeItem('code');
                                localStorage.removeItem('discordId');
                            }
                            break;
                            default:
                                $('#verificationWait').show();
                                $('#verificationAccepted').hide();
                                $('#verificationDenied').hide();
                            break;
                        }
                    })
                    .catch(() => {
                        $('#verificationWait').hide();
                        $('#serverError').show();
                    });
            }, 15000);
        })
    </script>
@endsection
@section('style')
    <style media="screen">
        div[data-step] {
            text-align: center;
        }

        div[data-step]:not(.active) {
            display: none;
        }

        .colored-block h2 {
            margin-bottom: 50px !important;
        }

        .colored-block table.very.basic.table {
            color: #fff;
        }

        .colored-block table.very.basic.table thead th {
            color: #fff;
        }
    </style>
@endsection
