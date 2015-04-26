<?php
class AuthorizationControl {
	public static function getAuthorizationStatus($login, $password, array $parameters) {
		if (! array_key_exists ( "acl", $parameters ) || ! array_key_exists ( "users", $parameters ))
			die ( "Les paramètres de configuration doivent définir des acl et une liste d'utilisateurs." );
		foreach ( $parameters ['users'] as $userLogin => $userPassword ) {
			if ($login == $userLogin && $password == $password)
				if (! array_key_exists ( $login, $parameters ['acl'] ))
					die ( "Les droits de $userLogin ne sont pas définis." );
				else {
					$rights = $parameters ['acl'] [$userLogin];
					
					return constant ( 'AuthorizationStatus::' . $rights );
				}
		}
		
		return AuthorizationStatus::NOT_CONNECTED;
	}
}
?>