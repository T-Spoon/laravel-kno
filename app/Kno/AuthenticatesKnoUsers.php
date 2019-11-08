<?php

namespace App\Kno;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

trait AuthenticatesKnoUsers
{
    use AuthenticatesUsers;

    protected function validateLogin(Request $request)
    {
        $request->validate([
            'knoToken' => 'required|string',
        ]);
    }

    protected function credentials(Request $request)
    {
        $knoToken = $request->input('knoToken');

        return ['kno_token' => $knoToken];
    }
}