<?php namespace Liip\User\Classes;

use Illuminate\Contracts\Auth\Authenticatable;
use Liip\User\Models\Throttle;
use October\Rain\Auth\AuthException;
use \RainLab\User\Classes\AuthManager as BaseAuthManager;
use Session;
use Cookie;

class AuthManager extends BaseAuthManager
{
    const ERROR_AUTHENTICATION = 1;
    const ERROR_ACTIVATED = 2;
    const ERROR_SUSPENDED = 3;
    const ERROR_BANNED = 4;

    /**
     * @var string Throttle Model Class
     */
    protected $throttleModel = Throttle::class;

    /**
     * Finds a user by the given credentials.
     *
     * @param array $credentials The credentials to find a user by
     * @throws AuthException If the credentials are invalid
     * @return Models\User The requested user
     */
    public function findUserByCredentials(array $credentials)
    {
        $model = $this->createUserModel();
        $loginName = $model->getLoginName();

        if (!array_key_exists($loginName, $credentials)) {
            throw new AuthException(sprintf('Login attribute "%s" was not provided.', $loginName));
        }

        $query = $this->createUserModelQuery();
        $hashableAttributes = $model->getHashableAttributes();
        $hashedCredentials = [];

        /*
         * Build query from given credentials
         */
        foreach ($credentials as $credential => $value) {
            // All excepted the hashed attributes
            if (in_array($credential, $hashableAttributes)) {
                $hashedCredentials = array_merge($hashedCredentials, [$credential => $value]);
            }
            else {
                $query = $query->where($credential, '=', $value);
            }
        }

        $user = $query->first();
        if (!$this->validateUserModel($user)) {
            throw new AuthException('A user was not found with the given credentials.', self::ERROR_AUTHENTICATION);
        }

        /*
         * Check the hashed credentials match
         */
        foreach ($hashedCredentials as $credential => $value) {

            if (!$user->checkHashValue($credential, $value)) {
                // Incorrect password
                if ($credential == 'password') {
                    throw new AuthException(sprintf(
                        'A user was found to match all plain text credentials however hashed credential "%s" did not match.', $credential
                    ), self::ERROR_AUTHENTICATION);
                }

                // User not found
                throw new AuthException('A user was not found with the given credentials.', self::ERROR_AUTHENTICATION);
            }
        }

        return $user;
    }

    /**
     * Find a throttle record by login and ip address
     *
     * @param string $loginName
     * @param string $ipAddress
     * @return Models\Throttle
     */
    public function findThrottleByLogin($loginName, $ipAddress)
    {
        $user = $this->findUserByLogin($loginName);
        if (!$user) {
            throw new AuthException("A user was not found with the given credentials.", self::ERROR_AUTHENTICATION);
        }

        $userId = $user->getKey();
        return $this->findThrottleByUserId($userId, $ipAddress);
    }

    /**
     * Logs in the given user and sets properties
     * in the session.
     * @throws AuthException If the user is not activated and $this->requireActivation = true
     */
    public function login(Authenticatable $user, $remember = true)
    {
        /*
         * Fire the 'beforeLogin' event
         */
        $user->beforeLogin();

        /*
         * Activation is required, user not activated
         */
        if ($this->requireActivation && !$user->is_activated) {
            $login = $user->getLogin();
            throw new AuthException(sprintf(
                'Cannot login user "%s" as they are not activated.', $login
            ), self::ERROR_ACTIVATED);
        }

        $this->user = $user;

        /*
         * Create session/cookie data to persist the session
         */
        if ($this->useSession) {
            $toPersist = [$user->getKey(), $user->getPersistCode()];
            Session::put($this->sessionKey, $toPersist);

            if ($remember) {
                Cookie::queue(Cookie::forever($this->sessionKey, $toPersist));
            }
        }

        /*
         * Fire the 'afterLogin' event
         */
        $user->afterLogin();
    }
}
