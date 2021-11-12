<?php

namespace Zbxin\VerificationCode\Validators;

use Zbxin\Contracts\Validator;
use Zbxin\VerificationCode\VerificationCodeManager;

class CodeIdValidator extends Validator
{
    /**
     * @param string $attribute
     * @param $value
     * @param array $parameters
     * @param \Illuminate\Validation\Validator $validator
     * @return mixed
     */
    public function validator($attribute, $value, $parameters, $validator)
    {
        $data = $validator->getData();
        logs()->info('validatorCodeId:' . $data[$parameters[0]]);
        if (isset($data[$parameters[0]])) {
            return $this->verificationCode()->validateCodeId($value, $data[$parameters[0]]);
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
