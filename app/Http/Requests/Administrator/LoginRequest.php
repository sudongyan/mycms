<?php
/**
 * CMS - CMS based on laravel
 *
 * @category  CMS
 * @package   Laravel
 */

namespace App\Http\Requests\Administrator;

class LoginRequest extends Request
{
    public function rules()
    {
        return [
            'email' => ['required','string','email','max:255'],
            'password' => ['required','string','min:6'],
            'captcha' => ['required','captcha'],
        ];
    }
    
}
