<?php

namespace ZhiEq\VerificationCode;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

class VerificationCodeManager
{
    private $cachePrefix = 'verification_code';

    protected $codeId;
    protected $recipient;
    protected $code;

    /**
     * @var \Illuminate\Cache\Repository|mixed
     */
    private $cache;

    /**
     * VerificationCodeManager constructor.
     */

    function __construct()
    {
        $this->cache = app('cache');
    }

    /**
     * @return int
     */

    public function generateCode()
    {
        return rand(100000, 999999);
    }

    /**
     * @param $codeId
     * @return $this
     */

    public function setCodeId($codeId)
    {
        logs()->info('Verification', ['codeId' => $codeId, 'thisCodeId' => $this->codeId]);
        if ($codeId !== $this->codeId) {
            $this->codeId = $codeId;
            list($this->recipient, $this->code) = $this->cache->tags([$this->cachePrefix])->get($codeId, [null, null]);
        }
        logs()->info('VerificationData:' . json_encode($this));
        return $this;
    }

    /**
     * @return mixed
     */

    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @return mixed
     */

    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param $recipient
     * @param $code
     * @param null $expiredTime
     * @return \Ramsey\Uuid\UuidInterface
     * @throws \Exception
     */

    public function saveCode($recipient, $code, $expiredTime = null)
    {
        $expiredTime = $expiredTime === null ? config('verification_code.expired_time') : $expiredTime;
        $codeId = Uuid::uuid4();
        logs()->info('saveCodeData:', ['recipient' => $recipient, 'code' => $code]);
        $this->cache->tags($this->cachePrefix)->put($codeId, [$recipient, $code], Carbon::now()->addSeconds($expiredTime));
        return $codeId;
    }

    /**
     * @param $recipient
     * @param null $expiredTime
     * @return array
     * @throws \Exception
     */

    public function generateAndSave($recipient, $expiredTime = null)
    {
        $code = $this->generateCode();
        $smsId = $this->saveCode($recipient, $code, $expiredTime);
        return [$smsId, $code];
    }

    /**
     * @param $codeId
     * @param $recipient
     * @return bool
     */

    public function validateCodeId($codeId, $recipient)
    {
        return $this->setCodeId($codeId)->getRecipient() === $recipient;
    }

    /**
     * @param $codeId
     * @param $code
     * @return bool
     */

    public function validateSaveCode($codeId, $code)
    {
        return $this->setCodeId($codeId)->getCode() === (int)$code;
    }

    /**
     * @param $codeId
     */

    public function destroySaveCode($codeId)
    {
        $this->cache->tags([$this->cachePrefix])->forget($codeId);
    }

    /**
     * @param $codeId
     * @param $code
     * @param bool $force
     * @return bool
     */

    public function validateAndDestroySaveCode($codeId, $code, $force = false)
    {
        $validateResult = $this->validateSaveCode($codeId, $code);
        if ($validateResult === true || $force === true) {
            $this->destroySaveCode($codeId);
        }
        return $validateResult;
    }
}
