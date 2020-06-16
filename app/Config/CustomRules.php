<?php

namespace Config;

use App\Models\UserModel;

//--------------------------------------------------------------------
// Custom Rule Functions
//--------------------------------------------------------------------

class CustomRules
{

    /**
     * Rule to validate if value is not equal to 0
     *
     * @param string $str
     * @param string $error Error message to be shown if validation failed
     *
     * @return boolean
     */
    public function not_zero(string $str, string &$error = null): bool
    {
        $error = lang('equation.not_zero');
        return $str !== '0';
    }

    /**
     * Rule to validate token with a, b and c variables
     *
     * @param string $str
     * @param string $fields
     * @param array  $data  Other field/value pairs
     * @param string $error Error message to be shown if validation failed
     *
     * @return boolean
     */
    public function abctoken(string $str, string $fields, array $data, string &$error = NULL): bool
    {
        $error = lang('equation.abctoken_wrong');

        // In case either a, b or c is not set token will be wrong anyway
        if(!isset($data['a']) ||!isset($data['b']) ||!isset($data['c'])) {
            return false;
        }

        return sha1($data['a'].$data['b'].$data['c']) === $str;
    }

}