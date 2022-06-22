<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

use App\User;
use App\Vote;
use \Cache;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;

class VoteController extends Controller
{
  public function __construct()
  {
    \Carbon\Carbon::setLocale(\Config::get('app.locale'));
    \Carbon\Carbon::setToStringFormat('d/m/Y à H:i:s');
  }

  public function index(Request $request)
  {
    return view('vote/index');
  }

  public function stepOne(Request $request)
  {
    if (!$request->has('code'))
      return response()->json([
        'status' => false,
        'error' => __('form.error.fields')
      ]);

    $file = file_get_contents('https://api.skf-studios.com:8443/user/verify/' . $request->input('code') . '/fetchByCode');
    $content = @json_decode($file, true);

    if (!$content['status'])
      return response()->json([
        'status' => false,
        'error' => 'Code invalid.'
      ]);

    if ($content['data']['data']['system'] 
                || $content['data']['data']['bot'])
      return response()->json([
        'status' => false,
        'error' => 'Bot or system app can\'t apply for verification.'
      ]);
    
    if (empty($content['data']['id']))
      return response()->json([
        'status' => false,
        'error' => 'Please enter a valid code.'
      ]);

    $blacklist = file_get_contents('https://api.skf-studios.com:8443/users/isBlacklisted/' . $content['data']['id']);
    $blData = @json_decode($blacklist, true);

    if (!empty($blData['data']) && $blData['data']['active'])
      return response()->json([
        'status' => false,
        'error' => 'You are blacklisted.'
      ]);

    if ($content['data']['verified'])
      return response()->json([
        'status' => false,
        'error' => 'User already verified.'
      ]);

    // Success
    return response()->json([
      'status' => true,
      'success' => __('vote.step.one.success'),
      'data' => $content['data']
    ]);
  }

  public function stepTwo(Request $request) {
    return response()->json([
      'status' => true,
      'success' => 'Success',
    ]);
  }

  public function stepThree(Request $request) {
    if (!$request->has('findus') || !$request->has('age') || !$request->has('furry') || !$request->has('fursona') || !$request->has('targetserver'))
      return response()->json([
        'status' => false,
        'error' => __('form.error.fields')
      ]);
    
    // CHECK RULES
    if (!$request->has('rules'))
      return response()->json([
        'status' => false,
        'error' => 'Please accept the rules.'
      ]);

      $client = new Client();
      $res = $client->request('POST', 'http://109.23.209.84:3000/notification/verify', [
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode([
          'status' => true,
          'success' => 'verify:pending',
          'guildId' => $request->input('targetserver'),
          'userId' => $request->input('pending'),
          'answers' => [
            'findus' => $request->input('findus'),
            'age' => $request->input('age'),
            'furry' => $request->input('furry'),
            'fursona' => $request->input('fursona'),
            'rules' => $request->input('rules')
          ]])
     ]);
     file_get_contents('https://api.skf-studios.com:8443/user/verify/' . $request->input('pending') . '/update');

    return response()->json([
      'status' => true,
      'success' => 'Verification sent',
    ]);
  }

  public function stepFour(Request $request) {}

  public function getWaitingPosition(Request $request)
  {
    // cache
    if (Cache::has('vote.position'))
      return response()->json(['status' => true, 'position' => Cache::get('vote.position')]);
    
    // Cache::put('vote.position', $position, 120); // 2 hours
    // return response()->json(['status' => true, 'position' => $position]);
    Cache::put('vote.position', 2, 120); 
    return response()->json(['status' => true, 'position' => 2]);
  }
}
