<?php

namespace App\Utils;

class IndividualUtils
{

    public static function mergeSex($request) {
        if(is_string($request->input('sex'))) {
            $request->merge([
                'sex' => mb_strtolower($request->input('sex'))
            ]);
        }
    }
}
