<?php

namespace Config;

use App\Models\UserModel;

//--------------------------------------------------------------------
// Custom Rule Functions
//--------------------------------------------------------------------

class CustomRules
{
    public function not_zero(string $str, string &$error = null): bool
    {
        $error = 'Field value can not be 0';
        return $str !== '0';
    }

    public function abctoken(string $str, string $fields, array $data, string &$error = NULL): bool
    {
        $error = 'Token is wrong';
        if(!isset($data['a']) ||!isset($data['b']) ||!isset($data['c'])) {
            return false;
        }

        return sha1($data['a'].$data['b'].$data['c']) === $str;
    }

}