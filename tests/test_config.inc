<?

$test_nonadmin_uid1 = 263; /* srivatsan_91 */
$test_nonadmin_uid2 = 402; /* anish shankar */
$test_admin_uid = 262; /* arnstein */

/* uid2 should not be able to access this submissions, but uid1 and admin_uid should be able to */
$submission_owned_by_uid1 = 6290;
$submission_owned_by_uid2 = 6269; 

/* a problem in the general contest */
$general_contest_problem = 'POINTS';
/* a non-general contest */

$test_non_general_contest = "zco1";
$test_non_general_contest_problem = "zco-2008-1b"; 

/* known submissions with their known scores */
$test_submissions = array (array (6601, 100, "Accepted"),
	array(6091, 0, "Compile Error"), /* OPC style */ 
	array(5124, 20, "Wrong Answer"), /* OPC style */
	array(6696, 64, "Wrong Answer"), /* ICO style */
	array(6387, 70, "Time Limit Exceeded"), /* IOI style */
	array(6145, 20, "Time Limit Exceeded"), /* OPC style */
	array(6697, 100, "Accepted")); /* TODO: I need a case where OPC style submission would've given a different score */


/* iarcs-apr05-ad-2 is a problem with 1s time limit */
$test_uploads = array (

	array (262, 'POINTS', 'cpp', 'code-samples/points.cpp', 'general', 100, 'Accepted'),
        array ('263', 'POINTS', 'cpp', 'code-samples/points.cpp', 'general', 100, 'Accepted'),
	array (263, 'POINTS', 'cpp', 'code-samples/points-wrong.cpp', 'general', 0, 'Wrong Answer'),
	array (262, 'POINTS', 'python', 'code-samples/points.py', 'general', 20, 'Wrong Answer'), /* python test! */
	array (262, 'POINTS', 'java', 'code-samples/points-java/Main.java', 'general', 20, 'Wrong Answer'),
	array (262, 'POINTS', 'gcj',  'code-samples/points-java/Main.java', 'general', 20, 'Wrong Answer'),
	array (262, 'iarcs-apr05-ad-2', 'c', 'code-samples/sleep.c', 'general', 0, 'Runtime Error (KILL)'),
	array (262, 'POINTS', 'c', 'code-samples/fork.c', 'general', 0, 'Runtime Error (ABRT)'),
	array (262, 'POINTS', 'c', 'code-samples/mem.c', 'general', 0, 'Runtime Error (ABRT)'),
	array (262, 'iarcs-apr05-ad-2', 'c', 'code-samples/TLE.c', 'general', 0, 'Time Limit Exceeded'),
	array (262, 'POINTS', 'c', 'code-samples/SEG.c', 'general', 0, 'Runtime Error (SEG)'),
	array (262, 'iarcs-apr05-ad-2', 'c', 'code-samples/sigxcpu-handled.c', 'general', 0, 'Runtime Error (KILL)'),
	array (262, 'POINTS', 'c', 'code-samples/simple.c', 'general', 0, 'Wrong Answer'),
	array (262, "iarcs-mar05-ad-1", "python", "code-samples/iarcs-mar05-ad-1-case4.py", "general", 0, "Wrong Answer") 
);

$problems_for_validation = array (
	"POINTS",
	"VPATTERN" 

	/* I know the following fail because of a badly formed id */
//	"iarcs-oct04-ad-1",
//	"iarcs-sep07-ad-1",
//	"iarcs-jan05-ad-1");
	
);

