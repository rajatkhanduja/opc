<?
require_once "OpcTest.php";

require_once "SuAuthAdapter.php";

/* Test that Zend Test is working! */
require_once "lib/db.inc";

function safeSystem ($string) {
	system ($string, $ret);
	if ($ret != 0) {
		echo "$string: failed\n";
		assert (false);
	}
}
abstract class OpcDataTest extends OpcTest {
	public function setUp ()
	{
		$blankdir = "/tmp/opc-blank-dir";
		safeSystem ("rm -rf $blankdir");
		safeSystem ("mkdir $blankdir");
		$datadir = get_file_name ("data/");
		$testdatadir = getcwd () . "/data";
		safeSystem ("unionfs-fuse -o cow,nonempty,exec,allow_other $blankdir=RW:$testdatadir=RO $datadir");
		parent::setUp ();
	}

	public function login ($user)
	{
		Zend_Loader::loadClass('Zend_Auth');
                $adapter = new SuAuthAdapter ($user);
                Zend_Auth::getInstance()->authenticate($adapter);
	}

	public function tearDown ()
	{
		contestDB::get_zend_db ()->closeConnection();
		$datadir = get_file_name ("data/");
		
		/* let's move our log file somewhere */
		/* close the logger. */
		Logger::flush ();
		$logfile = "$datadir/logs/" . posix_getuid () . ".log";

		if (is_file ($logfile)) {
			$contents = file_get_contents ($logfile);
			file_put_contents ("logs/" . posix_getuid () . ".log", $contents, FILE_APPEND);
		}
		safeSystem ("fusermount -zu $datadir");
	} 
}
