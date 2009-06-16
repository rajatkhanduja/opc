<?

function getgravatar ($user) 
{ 
	Zend_Loader::loadClass("UserModel");
	$user = UserModel::getRow ($user);
	$email = UserModel::getMember ($user, 0)->email;

	$grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=".md5( strtolower($email) ).
		'&size=40';

	return $grav_url;
}


class GravatarModel
{
	public function __construct() 
	{
		Zend_Loader::loadClass("Zend_Cache");
		$this->cache = Zend_Cache::factory('Function', 'File', 
					     array('lifetime' => 60,
						   'automatic_serialization' => true ) );
	}

	public function getGravatar($user) 
	{
		return $this->cache->call("getgravatar", array($user) );

	}
}