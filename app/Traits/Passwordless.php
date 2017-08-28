<?php
namespace App\Traits;
use App\Exceptions\InvalidTokenException;
use App\PasswordlessToken;
use Illuminate\Support\Facades\App;
trait Passwordless
{
    /**
     * Validate attributes.
     *
     * @param  string $token PasswordlessToken
     * @throws InvalidTokenException
     */
    public function validateToken($token)
    {
        if (! $this->isValidToken($token)) {
            throw new InvalidTokenException(trans("passwordless.invalid_token"));
        }
    }
    /**
     * Validate attributes.
     *
     * @param  string $token PasswordlessToken
     * @return bool
     */
    public function isValidToken($token)
    {
        if (! $token) {
            return false;
        }
        /**
         * @var $tokenModel PasswordlessToken
         */
        $tokenModel = $this->tokens()->where('token', $token)->first();
        return $tokenModel ? $tokenModel->isValid() : false;
    }
    /**
     * Generate a token for the current user.
     *
     * @param bool $save Generate token and save it.
     *
     * @return PasswordlessToken
     */
    public function generateToken($save = false)
    {
        $attributes = [
            'token'      => str_random(16),
            'is_used'    => false,
            'user_id'    => $this->id,
            'created_at' => time()
        ];
        $token = App::make(PasswordlessToken::class);
        $token->fill($attributes);
        if ($save) {
            try {
              $token->save();
            } catch (\Exception $e) {
              Log::error('Error Saving User Token: '. $e->getMessage());
            }
        }
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
