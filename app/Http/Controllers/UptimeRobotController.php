<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use GrahamCampbell\DigitalOcean\Facades\DigitalOcean;

class UptimeRobotController extends Controller
{
    public function webhook(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'alertTypeFriendlyName' => 'required|string',
            'monitorFriendlyName' => [
                'required',
                Rule::in(collect(config('servers'))->keys()), // this outputs our servers' names from the config file
            ],
        ]);

        abort_if($request->key != env('UPTIME_ROBOT_KEY'), 403);

        if ($request->alertTypeFriendlyName == 'Down') {
            DigitalOcean::droplet()->reboot(config("servers.{$request->monitorFriendlyName}"));
        }
    }
}
