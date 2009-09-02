<?php
include_once("multigraph.php");

class Eqn_multigraph extends Multigraph {
	function Eqn_multigraph($set=array()) {/*<<<*/
		$this->setup($set);
	}
/*>>>*/
	function setup($set) {/*<<<*/
		$this->set_or_default($set, "eqn", "");
		$eqn = $this->options["eqn"];
		$eqn = str_replace(";", "", $eqn);
		$eqn = str_replace("log(", "log10(", $eqn);
		$eqn = str_replace("ln(", "log(", $eqn);
		$aeqn = explode(" ", $eqn);
		$eqn = "";
		foreach ($aeqn as $comp) {
			if (strpos($comp, "^")) {
				$tmp = explode("^", $comp);
				$eqn .= " pow(".$tmp[0].", ".$tmp[1].")";
			} else {
				$eqn .= " ".$comp;
			}
		}
		$eqn .= ";";
		$this->options["eqn"] = $eqn;
		//print($this->options["eqn"]);
		$this->set_or_default($set, "x_start", -5);
		$this->set_or_default($set, "x_end", 5);
		$set["scriptbase"] = "eqn_multigraph.php";
		$scolors = array(
			"#98153e",
			"#6464fa",
			"#f57e4f",
			"#222c81",
			"#c5f209",
			"#b13c66",
			"#19194b",
			"#b58a31",
			"#f4ba56",
		);
		$this->set_or_default($set, "title", "Graph");
		$this->set_or_default($set, "x_title", "");
		$this->set_or_default($set, "y_title", "");
		$this->set_or_default($set, "y_title_orient", "v");
		$this->set_or_default($set, "style", "office");
		$this->set_or_default($set, "type", "bar");
		$this->set_or_default($set, "width", 600);
		$this->set_or_default($set, "height", 400);
		$this->set_or_default($set, "title_hperc", 0.1);
		$this->set_or_default($set, "graph_hperc", 0.90);
		$this->set_or_default($set, "graph_wperc", 0.90);
		$this->set_or_default($set, "title_wperc", 0.8);
		$this->set_or_default($set, "font_title", "tahoma");
		$this->set_or_default($set, "font_axis", "arial");
		$this->set_or_default($set, "font_label", "arial");
		$this->set_or_default($set, "font_label_size", 7);
		$this->set_or_default($set, "font_axis_size", 8);
		$this->set_or_default($set, "font_legend", "arial");
		$this->set_or_default($set, "axis_wperc", 0.1);
		$this->set_or_default($set, "legend_wperc", 0.20);
		$this->set_or_default($set, "force_legend", 0);
		$this->set_or_default($set, "padding", 2);
		$this->set_or_default($set, "scolors", $scolors);
		$this->scolors = $this->options["scolors"];
		$this->set_or_default($set, "legend_max_font_size", 11);
		$this->set_or_default($set, "swatch_size", 11);
		$this->set_or_default($set, "dropshadows", 1);
		$this->set_or_default($set, "shadow_y_offset", 2);
		$this->set_or_default($set, "shadow_x_offset", 2);
		$this->set_or_default($set, "shadow_color", "#000000");
		$this->set_or_default($set, "shadow_trans", "90");
		$this->set_or_default($set, "label_points", 1);
		$this->set_or_default($set, "dot_diam", 7);
		$this->set_or_default($set, "pie_thickness", 15);
		$this->set_or_default($set, "pie_wperc", 0.95);
		$this->set_or_default($set, "pie_hperc", 0.60);
		$this->set_or_default($set, "linedots", 0);
		$this->set_or_default($set, "square", 0);
		$this->set_or_default($set, "debug", 0);
		$this->set_or_default($set, "scriptbase", "multigraph.php");
		$this->log("requested type: ".$this->options["type"]);
		switch (strtolower($this->options["type"])) {
			case "vbar":
			case "bar":
			case "bars":
			case "vbars"; {
				$this->options["type"] = "bar";
				$this->set_or_default($set, "label_yval", 1);
				$this->set_or_default($set, "label_xval", 0);
				$this->log("graph type is bar");
				break;
			}
			case "dot":
			case "dots":
			case "point":
			case "points": {
				$this->options["type"] = "dot";
				$this->set_or_default($set, "label_yval", 1);
				$this->set_or_default($set, "label_xval", 1);
				$this->log("graph type is dot");
				break;
			}
			case "line":
			case "lines": {
				$this->options["type"] = "line";
				$this->set_or_default($set, "label_yval", 1);
				$this->set_or_default($set, "label_xval", 1);
				$this->log("graph type is line");
				break;
			}
			case "hbar":
			case "hbars": {
				$this->options["type"] = "hbar";
				$this->set_or_default($set, "label_yval", 0);
				$this->set_or_default($set, "label_xval", 1);
				$this->log("graph type is hbar");
				break;
			}
			case "pie": {
				$this->options["type"] = "pie";
				$this->set_or_default($set, "label_yval", 1);
				$this->set_or_default($set, "label_xval", 0);
				$this->log("graph type is pie");
				break;
			}
		}
 // default searches current path for fonts. Change this if you want the
 //		ttf fonts that are used here to be used from a system location
 //		like "c:/WINNT/Fonts". As far as I know, M$ is ok with distributing
 //		the ttf fonts included and used: verdana, tahoma, arial
		$this->set_or_default($set, "fontpath", ".");
		$this->img = imagecreatetruecolor($this->options["width"],
			$this->options["height"]);
	}
/*>>>*/
	function render() {/*<<<*/
		if ($this->options["eqn"] == "") {
			$this->create_error_graphic("No eqn specified.");
		}
		$this->log("modified eqn is: ".$this->options["eqn"]);
		$this->select_style();
		imagefilledrectangle($this->img, 0, 0, $this->options["width"], 
			$this->options["height"], $this->colors["bg"]);
		$foo = imagecolorallocatealpha($this->img, 255, 0, 255, 64);
		// get rough dataset
		$interval = ($this->options["x_end"] - $this->options["x_start"]) /
			20;
		for ($x = $this->options["x_start"]; $x <= $this->options["x_end"];
			$x += $interval) {
			eval(str_replace("y", "\$y", 
				str_replace("x", $x, $this->options["eqn"])));
			$roughdata[$x] = $y;
		}
		$this->add_series($roughdata, "eqn");
		$this->log("dumping series:");
		// get bounds
		$this->get_bounds();
		// title
		$this->render_title();
		// the actual graph -- get the member function to draw it.
		// legend (required to figure out x bounds)
		$this->render_legend();
		switch ($this->options["type"]) {
			case "bar": {
				// legend (required to figure out x bounds)
				$this->render_legend();
				$this->render_axes();
				$this->render_vbars();
				$this->render_point_labels();
				break;
			}
			case "hbar": {
				// legend (required to figure out x bounds)
				$this->render_legend();
				$this->render_axes();
				$this->render_hbars();
				$this->render_point_labels();
				break;
			}
			case "dot": {
				// legend (required to figure out x bounds)
				$this->render_legend();
				$this->render_axes();
				$this->render_dots();
				$this->render_point_labels();
				break;
			}
			case "line": {
				// legend (required to figure out x bounds)
				$this->render_legend();
				$this->render_axes();
				// with all the dimensioning in place, we can get a more 
				//	accurate dataset
				$increment = 3.0 / $this->xtranspx;
				$this->log("accurate increment is $increment");
				// make sure end point is in the series
				$x = $this->options["x_end"];
				eval(str_replace("y", "\$y", 
					str_replace("x", $x,
					$this->options["eqn"])));
				$data["$x"] = $y;
				
				for($x = $this->options["x_start"]; 
					$x < $this->options["x_end"];
					$x += $increment) {
					eval(str_replace("y", "\$y", 
						str_replace("x", $x, $this->options["eqn"])));
					$data["$x"] = $y;
				}
				//var_dump($data);
				// replace rough data.
				$this->series["eqn"] = array();
				foreach ($data as $x => $y) {
					$this->series["eqn"][$x] = $y;
				}
				$this->render_lines();
				if ($this->options["linedots"]) {
					$this->render_dots();
					$this->render_point_labels();
				};
				break;
			}
			case "pie": {
				$this->render_pie();
				break;
			}
			default: {
				$this->create_error_graphic("unknown graph type: "
					.$this->options["type"]);
				break;
			}
		}
		if ($this->options["debug"]) {
			$this->log("would have output png now.");
		} else {
			imagepng($this->img);
		}
		imagedestroy($this->img);
	}
/*>>>*/
}
$scriptname = basename($_SERVER["PHP_SELF"]);
switch ($scriptname) {
	case "eqn_multigraph.php": {
		// called to render inline
		$foo = new Eqn_multigraph();
		$foo->parseurl();
		$foo->render();
		if ($foo->options["debug"]) {
			$foo->dump_errors();
		}
		break;
	}
}
?>
