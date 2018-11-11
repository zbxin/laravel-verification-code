<?php

namespace ZhiEq\VerificationCode\Validators;

use ZhiEq\Contracts\Validator;
use ZhiEq\VerificationCode\VerificationCodeManager;

class VerificationCodeValidator extends Validator
{

    /**
     * @param string $attribute
     * @param $value
     * @param array $parameters
     * @param \Illuminate\Validation\Validator $validator
     * @return bool
     */

    public function validator($attribute, $value, $parameters, $validator)
    {
        $data = $validator->getData();
        logs()->info('validatorCode:' . $data[$parameters[0]]);
        if (isset($data[$parameters[0]])) {
            return $this->verificationCode()->validateAndDestroySaveCode($data[$parameters[0]], $value);
        }
        return false;
    }

    /**
     * @return VerificationCodeManager
     */

    protected function verificationCode()
    {
        return app('verification_code');
    }
}
