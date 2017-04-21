<?php

namespace Necktie\GatewayBundle\OAuth;

use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Trinity\Bundle\LoggerBundle\Interfaces\UserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class GWOAuthUserProvider
 * @package Necktie\GatewayBundle\OAuth
 */
class GWOAuthUserProvider extends OAuthUserProvider implements UserProviderInterface
{

    /** @var  TokenStorage */
    protected $token;


    /**
     * GWOAuthUserProvider constructor.
     *
     * @param TokenStorage $token
     */
    public function __construct(TokenStorage $token)
    {
        $this->token = $token;
    }


    /**
     * Get user by id.
     *
     * @param int $userId
     *
     * @return \Trinity\Component\Core\Interfaces\UserInterface
     */
    public function getUserById(int $userId)
    {
        return $this->token->getToken()->getUser();
    }
}
