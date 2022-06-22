<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use EntrustUserTrait;

    protected $fillable = ['username', 'email', 'password', 'ip'];

    public function connectionLog()
    {
        return $this->hasMany('App\UsersConnectionLog');
    }

    public function usernameHistory()
    {
        return $this->hasMany('App\UsersEditUsernameHistory');
    }

    public function obsiguardIP()
    {
        return $this->hasMany('App\UsersObsiguardIP');
    }

    public function obsiguardLog()
    {
        return $this->hasMany('App\UsersObsiguardLog');
    }

    public function refundHistory()
    {
        return $this->hasOne('App\UsersRefundHistory');
    }

    public function twitterAccount()
    {
        return $this->hasOne('App\UsersTwitterAccount');
    }

    public function youtubeChannel()
    {
        return $this->hasOne('App\UsersYoutubeChannel');
    }

    public function transferMoneyHistory()
    {
        return $this->hasMany('App\UsersTransferMoneyHistory');
    }

    public function purchaseItemsHistory()
    {
        return $this->hasMany('App\ShopItemsPurchaseHistory');
    }

    public function purchaseCreditsHistory()
    {
        return $this->hasMany('App\ShopCreditHistory');
    }

    static public function hash($password, $username)
    {
        return sha1($username . 'PApVSuS8hDUEsOEP0fWZESmODaHkXVst27CTnYMM' . $password);
    }

    static public function getStatsFromUsername($username)
    {
        $body = @file_get_contents('https://api.skf-studios.com:8443/user/' . $username . '/stats');
        if (!$body) return false;
        $data = @json_decode($body);
        if (!$data) return false;
        if (!$data->status) return false;
        $user = $data->data;
       // $user->faction = \App\Faction::getFactionFromUsername($username);
        return ($user);
    }

    static public function getSuccessList($uuid) 
    {
        return [];
    }

    public static function getStaff()
    {
        $staffList = [];
        $staff = json_decode(file_get_contents('https://api.skf-studios.com:8443/users/staff'))->data;

        $staffColors = ['red', 'red', 'green', 'olive', 'yellow'];
        $i = 0;
        foreach ($staff as $group => $users) {
            if ($group === '934501005641523290' || $group === '938977417492590614' || $group === '917732325427666944' || $group === '941474312714604544' || $group === '934501008963407881')
                $group = 'Owner';
            if ($group === '918172952552435733')
                $group = 'Head Admin';
            if ($group === '917760304098709535')
                $group = 'Admin';
            if ($group === '918172201381920788')
                $group = 'Moderator';
            if ($group === '934501083651379230')
                $group = 'Cuties';
            $group = $group . 's';
            if (!isset($staffList[$group]))
                $staffList[$group] = ['color' => $staffColors[$i++], 'users' => []];
            foreach ($users as $user)
                array_push($staffList[$group]['users'], $user);
        }
        return ($staffList);
    }
}
