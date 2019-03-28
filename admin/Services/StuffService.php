<?php
/**
 * Created by PhpStorm.
 * User: Kyron Bao
 * Date: 19-3-25
 * Time: 下午11:15
 */

namespace Admin\Services;

use Admin\Models\Stuff;
use App\Services\BaseService;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Cookie;

class StuffService extends BaseService
{

    public $stuff;
    const TOKEN_LENGTH  = 32;
    const TOKEN_NAME    = 'admin_token';
    const TOKEN_EXPIRE  = 10;

    public function login($params)
    {
        return $this->register($params);
    }

    public function register($params)
    {
        $params[self::TOKEN_NAME] = $this->generateToken();

        $params['password'] = $this->generatePassword($params);

        $stuff = new Stuff();
        $stuff->fillable(array_keys($params));
        $stuff->fill($params);
        $stuff->save();

        return $this->outputSuccess($stuff, 'Register done')
        ->withCookie($this->generateCookie($params[self::TOKEN_NAME]));
    }


    private function generateToken()
    {
        return hash('sha256', Str::random(self::TOKEN_LENGTH));
    }

    private function generatePassword($params)
    {
        return md5($params['password']);
    }

    private function generateCookie($cookie_value)
    {
        return new Cookie(self::TOKEN_NAME, $cookie_value, time()+self::TOKEN_EXPIRE);
    }
}