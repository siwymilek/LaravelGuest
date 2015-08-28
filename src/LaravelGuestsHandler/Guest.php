<?php

namespace Siwymilek\LaravelGuestsHandler;

use Siwymilek\LaravelGuestsHandler\Models\Guest as GuestModel;
use Illuminate\Support\Facades\Session;

/**
 * Guest members handler
 *
 * @package App
 */
final class Guest
{
    /**
     * Guest id
     *
     * @var
     */
    protected $id;

    /**
     * Session token.
     *
     * @var
     */
    protected $token;

    /**
     * Token lifetime.
     *
     * @var int
     */
    protected $token_lifetime = 1209600; // two weeks

    /**
     * User agent.
     *
     * @var string
     */
    protected $user_agent = null;

    /**
     * Remote address.
     *
     * @var string
     */
    protected $remote_address = null;

    /**
     * Is guest?
     *
     * @var bool
     */
    protected $guest = true;

    /**
     * Model
     *
     * @var
     */
    protected $model;

    /**
     * Initialize handling.
     *
     * @param bool|false $user_agent
     * @param bool|false $remote_address
     */
    public function __construct($user_agent = false, $remote_address = false)
    {
        /**
         * Prevent by logged users
         */
        if(\Auth::check()) {
            $this->guest = false;
            return false;
        }

        $this->user_agent = $user_agent ?: \Request::header('User-Agent');
        $this->remote_address = $remote_address ?: \Request::ip();
        $this->generateToken();

        /**
         * Find guest in database or create new
         */
        $guestObject = GuestModel::where($this->getCredentials())->where('token_expiration_time', '>', new \DateTime('now'))->first();

        if( ! $guestObject) {

            //generate new brand token
            $this->generateToken(true);

            $guestObject = GuestModel::create($this->getCredentials(true));
        }

        $this->model = $guestObject;
    }

    /**
     * @return static
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Get guest id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->getModel()->id;
    }

    /**
     * Check uses as guest
     *
     * @return bool
     */
    public function check()
    {
        return $this->guest && $this->getId() > 0;
    }

    /**
     * Refresh token expiration time.
     *
     * @param GuestModel $guest
     */
    private function refreshSession(GuestModel $guest)
    {
        if($this->token && $guest) {
            Session::put('guest_token', $this->token);
            $guest->update($this->getCredentials(true, true));
        }
    }

    /**
     * Generate brand new token.
     *
     * @param bool|false $force
     */
    private function generateToken($force = false)
    {
        if($force) {
            Session::forget('guest_token');
        }

        /**
         * Set token if does not exist.
         */
        if( ! Session::has('guest_token')) {
            $this->token = $this->getVerifiedToken();
            Session::put('guest_token', $this->token);
        } else {
            $this->token = Session::get('guest_token');
        }
    }

    /**
     * Get unique token.
     *
     * @return string
     */
    private function getVerifiedToken()
    {
        $token = str_random(40);

        while(GuestModel::where('token', $token)->first()) {
            $token = str_random(40);
        }

        return $token;
    }

    /**
     * Get credentials to attempt guest.
     *
     * @param bool|false $setExpirationTime
     * @param bool|false $onlyExpirationTime
     * @return array
     */
    private function getCredentials($setExpirationTime = false, $onlyExpirationTime = false)
    {
        $credentials = [
            'user_agent' => $this->user_agent,
            'remote_address' => $this->remote_address,
            'token' => $this->token
        ];

        if($setExpirationTime) {
            if($onlyExpirationTime) {
                $credentials = [];
            }

            $credentials['token_expiration_time'] = (new \DateTime('now'))->add(new \DateInterval('PT'.$this->token_lifetime.'S'));
        }

        return $credentials;
    }
}