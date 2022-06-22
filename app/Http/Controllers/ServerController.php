<?php
namespace App\Http\Controllers;

use App\UsersVersion;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

use Carbon\Carbon;

class ServerController extends Controller
{
    public function __construct()
    {
        Carbon::setLocale(\Config::get('app.locale'));
        Carbon::setToStringFormat('d/m/Y at H:i:s');
    }

    public function index(Request $request) {
        $servers =  file_get_contents('https://api.skf-studios.com:8443/servers');
        $dataServer = @json_decode($servers, true);
        $dataServer = $dataServer['data'];

        return view('server.index')
               ->with('servers', $dataServer);
    }

    public function about(Request $request) {
        return view('server.about');
    }

    public function feedback(Request $request) {
        return view('server.feedback');
    }

    public function stats(Request $request) {
        $data = file_get_contents('https://api.skf-studios.com:8443/servers/by-id/' . $request->guildid . '/config');
        $decode = @json_decode($data);
        $decode = $decode->data;

        $stats = file_get_contents('https://api.skf-studios.com:8443/servers/by-id/' . $request->guildid);
        $decodedStats = @json_decode($stats);
        $decodedStats = $decodedStats->data;

        $members = file_get_contents('https://api.skf-studios.com:8443/servers/by-id/' . $request->guildid . '/members');
        $decodedMembers = @json_decode($members);
        $decodedMembers = $decodedMembers->data;

        $owner = file_get_contents('https://api.skf-studios.com:8443/user/by-id/' . $decode->ownerId . '/' . $decode->id);
        $decodedOwner = @json_decode($owner);
        $decodedOwner = $decodedOwner;

        return view('server.serverstats')
               ->with('serverName', $decode->name)
               ->with('serverId', $decode->id)
               ->with('serverIcon', $decode->iconURL)
               ->with('serverSplash', $decode->splashURL)
               ->with('serverBanner', $decode->bannerURL)
               ->with('serverLanguage', $decode->preferredLocale)
               ->with('serverBoost', $decode->premiumTier)
               ->with('serverBoostBar', $decode->premiumProgressBarEnabled)
               ->with('serverMemberCount', $decode->memberCount)
               ->with('serverPremiumCount', $decode->premiumSubscriptionCount)
               ->with('serverNSFWLevel', $decode->nsfwLevel)
               ->with('serverEvents', $decode->scheduledEvents)
               ->with('serverEmojis', $decode->emojis)
               ->with('serverStickers', $decode->stickers)
               ->with('serverVerification', $decodedStats->verification)
               ->with('serverOwner', $decodedOwner->username)
               ->with('serverOwnerId', $decode->ownerId)
               ->with('serverMembers', $decodedMembers)
               ->with('serverRules', false);
    }

    public function leaderboards(Request $request) {
        $data = file_get_contents('https://api.skf-studios.com:8443/servers/by-id/' . $request->guildid . '/config');
        $decode = @json_decode($data);
        $decode = $decode->data;

        $dataBoard = file_get_contents('https://api.skf-studios.com:8443/servers/by-id/' . $request->guildid . '/leaderboard');
        $decodeBoard = @json_decode($dataBoard);
        $decodeBoard = $decodeBoard->datatable;

        return view('server.leaderboard')
               ->with('serverid', $request->guildid)
               ->with('servername', $decode->name)
               ->with('servericon', $decode->iconURL)
               ->with('datatable', $decodeBoard);
    }

    public function rules(Request $request) {
        return view('server.rules');
    }

    public function members(Request $request) {}

    public function manifest(Request $request) {}

    public function userProfile(Request $request) {
        $data = file_get_contents('https://api.skf-studios.com:8443/servers/by-id/' . $request->guildid . '/config');
        $decode = @json_decode($data);
        $decode = $decode->data;

        $owner = file_get_contents('https://api.skf-studios.com:8443/user/by-id/' . $request->userid . '/' . $request->guildid);
        $decodedOwner = @json_decode($owner);
        $decodedOwner = $decodedOwner;

        $dataLevel = file_get_contents('https://api.skf-studios.com:8443/user/by-id/' . $request->userid . '/' . $request->guildid . '/level');
        $decodedLevel = @json_decode($dataLevel);
        $decodedExtra = $decodedLevel->extra;
        $decodedLevel = $decodedLevel->data;

        if ($decodedLevel) {
            return view('server.userprofile')
               ->with('username', $decodedOwner->username)
               ->with('userDID', $request->userid)
               ->with('iconURL', $decodedOwner->iconURL)
               ->with('userXP', $decodedLevel->xp)
               ->with('userLevel', $decodedLevel->level)
               ->with('serverName', $decode->name)
               ->with('serverId', $decode->id)
               ->with('serverIcon', $decode->iconURL)
               ->with('serverSplash', $decode->splashURL)
               ->with('serverBanner', $decode->bannerURL)
               ->with('serverOwnerId', $decode->ownerId)
               ->with('serverLanguage', $decode->preferredLocale)
               ->with('rankPosition', $decodedExtra->position)
               ->with('rankName', $decodedExtra->rank->name)
               ->with('requiredXp', $decodedExtra->requiredXp);
        }

        return view('server.userprofile')
            ->with('username', $decodedOwner->username)
            ->with('userDID', $request->userid)
            ->with('iconURL', $decodedOwner->iconURL)
            ->with('userXP', 0)
            ->with('userLevel', 0)
            ->with('serverName', $decode->name)
            ->with('serverId', $decode->id)
            ->with('serverIcon', $decode->iconURL)
            ->with('serverSplash', $decode->splashURL)
            ->with('serverBanner', $decode->bannerURL)
            ->with('serverOwnerId', $decode->ownerId)
            ->with('serverLanguage', $decode->preferredLocale)
            ->with('rankPosition', 'N/a')
            ->with('rankName', 'Rookie')
            ->with('requiredXp', 400);
    }

    public function userSanctions(Request $request) {}

    public function userManifest(Request $request) {}
}