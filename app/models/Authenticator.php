<?php
/**
 * Demo - knihovní systém
 *
 * @package    Models
 */

namespace Models;

use Nette\Security\AuthenticationException,
    Nette\Security\Identity,
    Nette\Environment;

/**
 * Authenticator
 *
 * @author     Tomáš Penc
 */
class Authenticator implements \Nette\Security\IAuthenticator
{
	/**
	 * Performs an authentication
	 * @param  array
	 * @return IIdentity
	 * @throws AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		$username = strtolower($credentials[self::USERNAME]);
		$password = strtolower($credentials[self::PASSWORD]);

		$user = Environment::getService('Doctrine\ORM\EntityManager')
			->getRepository('Models\User')
			->findOneByUsername($username);

		if (!$user) {
			throw new AuthenticationException("Uživatel '$username' neexistuje.", self::IDENTITY_NOT_FOUND);
		}

		if (!$user->verifyPassword($password)) {
			throw new AuthenticationException("Špatné heslo.", self::INVALID_CREDENTIAL);
		}

		return new Identity($user->id, NULL, $user);
	}
}