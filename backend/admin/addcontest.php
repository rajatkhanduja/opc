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
 * @file   addcontest.php
 * @author Arnold Noronha <arnold@cmi.ac.in>
 */
 
/* read commandline options */
$namespace = "";
for ($i = 1; $i < $argc; $i++) {
	if ($argv[$i] == "--id")
		$id = $argv[++$i];
	else if ($argv[$i] == "--name")
		$name = $argv[++$i];
	else if ($argv[$i] == "--start-time")
		$start_time = $argv[++$i];
	else if ($argv[$i] == "--duration")
		$duration = $argv[++$i];
	else if ($argv[$i] == "--end-time")
		$end_time = $argv[++$i];
	else if ($argv[$i] == "--enable-queue-privacy")
		$enable_queue_privacy = true;
	else if ($argv[$i] == "--quiet")
		$quiet = true;
	else if ($argv[$i] == "--help") {
		display_help ();
		exit (1);
	}
	else {
		echo "Unknown option: " . $argv[$i] . "\n";
		exit (1);
	}
		
}

/* quick sanity checks */
if(!empty ($contest_length) && !empty($end_time)) {
	echo "Use only one of --duration or --end-time\n";
	exit (1);
}



function display_help ()
{
	
}

chdir(dirname($argv[0]));

require_once "../config.inc" ;
require_once "lib/db.inc" ;
require_once "lib/logger.inc";

ob_implicit_flush(true);

$dom = new DOMDocument("1.0", "UTF-8") ;
$dom->formatOutput = TRUE ; 
$root = $dom->createElement("contest") ;
$dom->appendChild($root) ;

$xsdformat = "c"; /* RFC 8601 */
if (!empty($start_time)) {
	$unix = strtotime ($start_time);
	$startTime = $dom->createElement ("contestTime", date ($xsdformat, $unix));
	$root->appendChild($startTime);

	if (!empty($duration))
		$unix = strtotime ($duration, strtotime ($start_time));
	else if (!empty($end_time))
		$unix = strtotime ($end_time);
	else {
		echo "You have to specify an end time or duration.\n";
		exit (1);
	}

	$endTime = $dom->createElement ("contestEndTime", date ($xsdformat, $unix));
	
	$root->appendChild ($endTime);
}


if (empty($name)) $name = "Unnamed contest";
$e = $dom->createElement ("name", $name);
$root->appendChild ($e);

if (!empty($enable_queue_privacy)) {
	$e = $dom->createElement ("enable-queue-privacy", "true");
	$root->appendChild ($e);
}	

$frontend = $dom->createElement ("frontend");
$root->appendChild ($frontend);

$home = $dom->createElement ("page", "Home");
$frontend->appendChild ($home);

$home->setAttribute ("id", "home");
$home->setAttribute ("href", "general/home.html");


file_put_contents (get_file_name ("data/contests/$id.xml"), $dom->saveXML ());
chmod(get_file_name("data/contests/$id.xml"), 0755) ;

if (empty($quiet)) {
	echo "-----LOG-----\n" ;
	echo $dom->saveXML();

	echo "\nJust verify that the timestamps have been correctly parsed.\n";
	Logger::get_logger()->info ("contest added with: " . $argv);	
}


