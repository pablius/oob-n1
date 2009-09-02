<html>
<?php
include_once("multigraph.php");
include_once("eqn_multigraph.php");
error_reporting(0);
?>
<head>
<title>Multigraph test page</title>
</head>
<body>
<?
	$gwidth = 600;
	$gheight = 450;

// pie demo
	$g = new Multigraph(array(
		"title" => "MMMM PIE!",
		"type"	=> "pie",
		"width" => $gwidth,
		"height"=> $gheight,
	));
	$data = array(
		"dogs"	=> 13,
		"cats"	=> 5,
		"lemurs" => 20,
		"budgies" => 10,
	);
	$g->add_series($data);
	print("<a href=\"".$g->createurl()."&debug=1\"><img src=\""
		.$g->createurl()."\" title=\"click to see debug output\"></a><br>");

	$types = array("dot", "line", "bar", "hbar");
	foreach ($types as $type) {
		if ($type == "line") {
			$sq = 1;
			$squarified = " (squarified)";
		} else {
			$squarified = "";
			$sq = 0;
		}
		$g = new Multigraph(array(
			"title"		=>	"$type graph".$squarified,
			"x_title"	=>	"Foo",
			"y_title"	=>	"Bar",
			"type"		=>	$type,
			"width"		=>  $gwidth,
			"height"	=>	$gheight,
			"square"	=>	$sq,
		));
		// 45 degree line
		for ($i = -5; $i < 10; $i++) {
			$s45[$i] = $i;
		}
		// 60 degree line
		for ($i = -5; $i < 10; $i++) {
			$s60[$i] = 2 * $i;
		}
		
		$g->add_series($s45, "Y = X");
		$g->add_series($s60, "Y = 2X");
		print("<a href=\"".$g->createurl()."&debug=1\"><img src=\""
			.$g->createurl()."\" title=\"click to see debug output\"></a><br>");
	}
	
	$eqn = "y = x^2;";
	$g = new Eqn_multigraph(array(
		"title"		=>	"testing equation graph",
		"width"		=>	$gwidth,
		"height"	=>	$gheight,
		"type"		=>	"line",
		"square"	=>	1,
		"x_start"	=>	-5,
		"x_end"		=>	5,
		"eqn"		=>	"y = x^2",
	));
	print("<a href=\"".$g->createurl()."&debug=1\"><img src=\""
		.$g->createurl()."\" title=\"click to see debug output\"></a><br>");
?>
</body>
</html>
