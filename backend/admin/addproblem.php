#!/usr/bin/env php
<?php
/**
 * Copyright 2007-2009 Chennai Mathematical Institute
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @file   addproblem.php
 * @author Arnold Noronha <arnold@cmi.ac.in>
 */
 
/* read commandline options */
$namespace = "";
$outputlim = "50M";
$cpulim = "3";
$memlim = "64M";

for ($i = 1; $i < $argc; $i++) {
	if ($argv[$i] == "--id")
		$id = $argv[++$i];
	else if ($argv[$i] == "--nick")
		$nick = $argv[++$i];
	else if ($argv[$i] == "--contest")
		$contest = $argv[++$i];
	else if ($argv[$i] == "--num-test-case")
		$numcases = $argv[++$i];
	else if ($argv[$i] == "--total-score")
		$total_score = $argv[++$i];
	else if ($argv[$i] == "--scores")
		$scores = explode (',', $argv[++$i]);
	else if ($argv[$i] == "--memory-limit")
		$memlim = $argv[++$i];
	else if ($argv[$i] == "--cpu-limit")
		$cpulim = $argv[++$i];
	else if ($argv[$i] == "--output-limit")
		$outputlim = $argv[++$i];
	else if ($argv[$i] == "--submission-limit")
		$sublim = $argv[++$i];
	else if ($argv[$i] == "--resource-limits") /* deprecated */
		$rlim = $argv[++$i];
	else if ($argv[$i] == "--checker")
		$checker = $argv[++$i];
	else if ($argv[$i] == "--grading-style") {
		$grading_style = $argv[++$i];
		if ($grading_style != "ioi" && $grading_style != "opc") {
			echo "ERROR: invalid grading style. (should be ioi or opc)\n";
			exit (0);
		}
	}
	else if ($argv[$i] == "--only-update")
		$onlyupdate = true;
	else if ($argv[$i] == "--use-sample") 
		$namespace = "sample.";
	else if ($argv[$i] == "--force")
		$force_overwrite = true;
	else if ($argv[$i] == "--help") {
		display_help ();
		exit (1);
	}
	else if (empty($archive) && substr ($argv[$i], 0, 1) != "-"){
		if (!is_file ($argv[$i])) {
			echo "{$argv[$i]}: file does not exist\n";
			exit (1);
		}
		$archive = realpath ($argv[$i]);
	} 
	else {
		echo "Unknown option: " . $argv[$i] . "\n";
		exit (1);
	}
		
}

if (!empty($total_score) && !empty($scores)) {
	echo "ERROR: use only one of --total-score or --scores\n";
	exit (1);
}

if (!empty ($total_score)) {
	$scores = array();
	$left = $total_score;
	for ($i = 0; $i < $numcases; $i++) {
		$cur = floor($left/($numcases-$i));
		$left -= $cur;
		$scores [] = $cur;
	}
}

chdir(dirname($argv[0]));


/* delete these functions if you are having compiler troubles*/
if (!function_exists("readline"))  {
	function readline($s) {
		echo $s ; 
		return trim(fgets(STDIN)) ;
	}
}

/******/

function uncompress_archive ($file, $probid) {
	global $force_overwrite;

	$oldpwd = getcwd ();
	$file = realpath($file);

	chdir ("../data/problems/");
	if (empty($force_overwrite))
		$opt = "kxvzf";
	else 
		$opt = "xvzf";

	system ("tar $opt $file $probid/", $return_val);

	if ($return_val != 0) {
		echo "Error in extracting archive. Either the archive doesn't have $probid/ or perhaps you tried installing the problem before? Use --force in that case to overwrite.\n";
		exit (1);
	}
	
	if (!is_file ("$probid/index.html")) {
		echo "ERROR: index.html problem description missing in archive.\n";
		exit (1);
	}

	chdir ($oldpwd);
}

function display_help ()
{
	echo "
Usage: addproblem.php [OPTION]... [FILE]

Where [FILE] is a single .tar.gz file having all the testdata in the
right format. Note that even this is optional, if you do not specify
an archive it is assumed that the the testdata has already been copied
to the data/problems/

Options are (most options will be asked interactively if not specified
on commandline):
  --id <id>         A unique problem identifier (e.g. SAMPLE)
  --nick <nick>     A friendly name for this problem (e.g. 'A Sample Problem')
  --contest <name>  The contest to which this problem should be added to.
                    (Default: 'general')
  --num-test-case n Number of test cases 
  --scores n,n,..,n A list of <num-test-cases> integers indicating the score
                    for each testcase. Note that --scores should be used 
                    only after a --num-test-case.
  --total-score     The total score for this problem. The points will be
                    divided evenly between each test case. More convenient than
                    using --scores.
  --memory-limit m  Memory limit per execution (e.g. 64M, 1000K)
  --cpu-limit       Time limit in seconds per exectution (e.g. 3,1.5)
  --output-limit m  Limit the size of output produced by the user's program
                    (e.g. 64M, 1000K)
  --submission-limit n   Maximum number of submissions per user for this 
                         problem.
  --checker         Location to the checker to be used for this problem.
                    (See documentation for checker specification). The 
                    location of the checker should be given with relative to
                    the backend directory.
  --grading-style {ioi|opc} See documentation for details.
  --only-update     Used to update the problem, if the problem was already
                    added previously.
  --use-sample      Instead of using input files in 0.in,1.in etc, use
                    sample.0.in, sample.1.in etc. This makes it easy to keep
                    both sample and final testdata in the same archive.
  --force           Usually will be used with --only-update. The idea is to
                    keep the user aware that file overwrites will happen and
                    therefore you shouldn't make a mistake and provide the
                    wrong problemid for instance.
  --help            Display this help and exit.

";
}
require_once "../config.inc" ;
require_once "lib/db.inc" ;

ob_implicit_flush(true);

$dom = new DOMDocument("1.0", "UTF-8") ;
$dom->formatOutput = TRUE ; 
$root = $dom->createElement("problem") ;
$dom->appendChild($root) ;

if (empty($id))
	$id = readline( "Enter a unique problem ID: " ) ;

$element = $dom->createComment("id is used for informative purposes only, "
 . "and does not overload the id stored in the database or as the current "
							   . "filename.") ;
$root->appendChild($element) ;
$element = $dom->createElement("id", $id) ;
$root->appendChild($element) ;

if (!empty($archive)) {
	uncompress_archive ($archive, $id);
}

if ( !is_file(get_file_name("data/problems/$id/index.html") ) ){ 
  echo "data/problems/$id/index.html does not exist. Aborting right now.\n" ;
  exit(1) ;
 }

if (empty($nick))
	$nick = readline( "Enter a nickname for the problem: " ) ;

$element = $dom->createComment(
			"nickname is also stored for informative purposes only.");
$root->appendChild($element);

$element = $dom->createElement("nick", $nick) ;
$root->appendChild($element) ;

if (empty($contest) ) $contest = 'general' ;   
require_once "lib/contest.inc";
$c = Contest::factory($contest);
if ( empty($c) ) { 
	die("That contest does not exist.\n");
}

if (empty($numcases)) 
	$numcases = 0 + readline("Number of testcases: ") ;



for( $i = 0 ; $i < $numcases ; $i ++ ) {

  $testcase = $dom->createElement("test") ;

  $def = "data/problems/$id/$namespace$i.in" ;
  if ( !is_file(get_file_name($def)) ) 
	  die("$def: not found\n");

  $element = $dom->createElement("inputpath", $def) ;
  $testcase->appendChild($element) ;


  $def = "data/problems/$id/$namespace$i.out" ; 
  if ( !is_file(get_file_name($def)) ) 
	  die("$def: not found\n");

  $element = $dom->createElement("outputpath", $def) ;
  $testcase->appendChild($element) ;

  if (empty($scores[$i])) {
	  echo "Score for test case $i? : ";
	  fscanf(STDIN,"%d\n", $sc ) ;
  } else
	  $sc = $scores[$i];

  $element = $dom->createElement("score", $sc) ;
  $testcase ->appendChild($element) ;

  $root->appendChild($testcase);

 }

if (empty($sublim)) $sublim = 10000;

$res = $dom->createElement("resourcelimits") ;
$root->appendChild($res) ;


$element = $dom->createElement("memory", $memlim) ;
$res->appendChild($element) ;


$element = $dom->createElement("runtime", $cpulim) ;
$res->appendChild($element) ;

$element = $dom->createElement("output", $outputlim) ;
$res->appendChild($element) ;


if (!empty($checker)) {
	$element = $dom->createElement ("checker", $checker);
	$root->appendChild ($element);
}

if (empty($grading_style))
	$grading_style = config::$default_grading_style;
$element = $dom->createElement ("grading-style", $grading_style);
$root->appendChild ($element);

if (empty($rlim)) $rlim = "";
$element = $dom->createElement("resourcelimits_string", $rlim) ;
$root->appendChild($element) ;

file_put_contents(get_file_name("data/problems/" . $id . ".xml" ),
				  $dom->saveXML());
chmod(get_file_name("data/problems/$id.xml"), 0755) ;
chmod(get_file_name("data/problems/$id"), 0755);

echo "-----LOG-----\n" ;
echo $dom->saveXML();

$db = contestDB::get_zend_db() ;

$id=pg_escape_string($id);
$nick=pg_escape_string($nick);
$rlim=pg_escape_string($rlim);

if (empty($onlyupdate)) {
	$sql = "insert into problemdata (id,numcases,nickname,state,submissionlimit,owner) values 
('$id',$numcases,'$nick','ok',$sublim,'$contest')" ;
} else {
	$sql = "update problemdata set numcases=$numcases,nickname='$nick',
state='ok',submissionlimit=$sublim,owner='$contest' where id='$id'";

}


$db->query($sql ) ;



echo "Done, copy your problem statement to " . config::$problems_directory 
. "/$id/index.{html|pdf|ps}. Note that html file should contain only the body "
. "part!\n";


