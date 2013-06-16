<?php
class AuthorizationControl {
	public static  function getAuthorizationStatus($login, $password) {
		if($login=="testeur1" && $password=="crdp1" )
			return AuthorizationStatus::READ_ONLY;
		if($login=="testeur2" && $password=="crdp2" )
			return AuthorizationStatus::READ_WRITE;
		return AuthorizationStatus::NOT_CONNECTED;
	}

}?>