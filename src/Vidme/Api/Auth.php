<?php

namespace Vidme\Api;

use GuzzleHttp\ClientInterface;
use Vidme\Storage\AuthStorageInterface;

class Auth
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var AuthStorageInterface
     */
    protected $authStorage;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var array
     */
    public $authData;

    /**
     * @param ClientInterface $client
     * @param string $username
     * @param string $password
     * @param AuthStorageInterface $authStorage
     */
    public function __construct(ClientInterface $client, AuthStorageInterface $authStorage, $username, $password)
    {
        $this->client = $client;
        $this->username = $username;
        $this->password = $password;
        $this->authStorage = $authStorage;
    }

    /**
     * Check if stored authentication data is still valid.
     */
    public function check()
    {
        if (!$storedAuthData = $this->authStorage->read()) {
            return false;
        }

        $response = $this->client->post('/auth/check', ['body' => ['token' => $storedAuthData['auth']['token']]]);

        $authData = $response->json();

        // check failed, remove stored data
        if (true !== $authData['status']) {
            $this->authStorage->clear();
            return false;
        }

        $this->authStorage->store($authData);

        $this->authData = $authData;

        return true;
    }

    /**
     * Create authentication session and store data.
     *
     * @return array|bool
     */
    public function create()
    {
        $response = $this->client->post('/auth/create', ['body' => ['username' => $this->username, 'password' => $this->password]]);

        $authData = $response->json();

        // authentication failed
        if (true !== $authData['status']) {
            return false;
        }

        $this->authStorage->store($authData);

        $this->authData = $authData;

        return true;
    }
}