<?php

namespace App\Traits;

use App\Exceptions\InvalidTokenException;
use App\PasswordlessToken;
use App\UserToken;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

trait Passwordless
{
    /**
     * Validate attributes.
     *
     * @param string $token PasswordlessToken
     * @throws InvalidTokenException
     */
    public function validateToken($token)
    {
        if (!$this->isValidToken($token)) {
            throw new InvalidTokenException(trans("passwordless.invalid_token"));
        }
    }

    /**
     * Validate attributes.
     *
     * @param string $token PasswordlessToken
     * @return bool
     */
    public function isValidToken($token)
    {
        return true;
//        if (!$token) {
//            return false;
//        }
//        /**
//         * @var $tokenModel PasswordlessToken
//         */
//        $tokenModel = $this->tokens()->where('token', $token)->first();
//        return $tokenModel ? $tokenModel->isValid() : false;
    }

    /**
     * Generate a token for the current user.
     *
     * @param $user_id
     * @param bool $save Generate token and save it.
     * @param integer $job_id Id of the job so that a token is only tied to that job not some other contractors job.
     * @param string $job_step
     * @param string $job_task_step
     * @param string $sub_step
     * @param string $type what the token was created for, email, text, etc.
     *
     * @param null $taskId
     * @return UserToken|null
     */
//    public function generateToken($save = false)
    public function generateToken(
        $user_id,
        $save = false,
        $job_id = -1,
        $job_step = 'not set',
        $job_task_step = 'not set',
        $sub_step = 'not set',
        $type = 'not set',
        $jobTaskId = null
    )
    {
        $now = Carbon::now();
        $token = new UserToken();
        $attributes = [
            'job_id' => $job_id,
            'user_id' => $user_id,
            'job_step' => $job_step,
            'job_task_step' => $job_task_step,
            'sub_step' => $sub_step,
            'token' => str_random(16),
            'type' => $type,
            'created_at' => time(),
            'expires_at' => $now->addWeeks(4),
            'job_task_id' => $jobTaskId,
        ];
//        $token = App::make(PasswordlessToken::class);
        $token->fill($attributes);
        if ($save) {
            try {
                $token->save();
            } catch (\Exception $e) {
                Log::error('Error Saving User Token: ' . $e->getMessage());
                return null;
            }
        }

//        if (gettype($token) == 'object') {
//            Log::info($token->token);
//        } else {
//            Log::info($token);
//        }

        return $token;
    }

    /**
     * User tokens relation.
     *
     * @return mixed
     */
    public function tokens()
    {
        return $this->hasMany(PasswordlessToken::class, 'user_id', 'id');
    }

    /**
     * Identifier name to be used with token.
     *
     * @return string
     */
    protected function getIdentifierKey()
    {
        return 'email';
    }
}
