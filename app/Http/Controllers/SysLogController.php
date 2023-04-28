<?php

namespace App\Http\Controllers;
use App\Models\SysLog;
use Illuminate\Http\Request;

class SysLogController extends Controller
{
    public function getSysLog(Request $request)
    {
    }

    public static function storeSyslog($status, $content, $created_by)
    {
        SysLog::create([
            "status_log" => $status,
            "content_log" => $content,
            "ip_log" => getVisitorAddressIp(),
            "created_by" => $created_by
        ]);
    }
}
