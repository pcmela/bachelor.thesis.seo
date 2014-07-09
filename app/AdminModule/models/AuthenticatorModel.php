<?php

namespace AdminModule;

use Nette\Object, Nette\Environment, Nette\Security\IAuthenticator;

class AuthenticatorModel extends \BaseModel implements IAuthenticator
{

    public function authenticate(array $credentials)
    {
        $username = $credentials[self::USERNAME];
        $password = sha1($credentials[self::PASSWORD] /*. $credentials[self::USERNAME]*/);

        // přečteme záznam o uživateli z databáze
        $row = $this->connection->fetch('SELECT * FROM elpod_all_user WHERE ea_user_mail=%s', $username);

        if (!$row) { // uživatel nenalezen?
            throw new \Nette\Security\AuthenticationException("Uživatel '$username' nebyl nalezen.", self::IDENTITY_NOT_FOUND);
        }

        if ($row->ea_user_password !== $password) { // hesla se neshodují?
            throw new \Nette\Security\AuthenticationException("Neplatné heslo.", self::INVALID_CREDENTIAL);
        }

        return new \Nette\Security\Identity($row->ea_user_id, $row->ea_user_role, array(
            'name' => $row->ea_user_name,
            'email' => $row->ea_user_mail
        ));
    }

}

?>
