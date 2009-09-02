<?
/*
	Multigraph is a class to encapsulate basic graphing functionality
	for N series of data -- the number of series that are used is completely
	up to the caller, bearing in mind that the resultant graph may be 
	a little cluttered -- but ui design should be up to the caller, not
	the grapher.

	A lot of the goodness of this class can be attributed to time spent
	looking at Carlos Reche's PowerGraphic class. Good work, dude. I just
	wanted N-series functionality, and hacking away at PowerGraph was getting
	a little tedious -- so I did the natural thing: start from scratch.
	Some concepts and algorythms may be borrowed from Powergraph -- but that's
	the advantage and nature of open source.
*/
include_once("_private/log.php");

class Multigraph extends Logger{
	var $series = array();
	var $options = array();
	
	// an array of arrays of colors

	var $font_reg = array();

	function Multigraph ($set = array()) {/*<<<*/
		$this->setup($set);
	}
/*>>>*/
	function setup ($set) { /*<<<*/
		$this->set_or_default($set, "title", "");
		$this->set_or_default($set, "x_title", "");
		$this->set_or_default($set, "y_title", "");
		$this->set_or_default($set, "y_title_orient", "v");
		$this->set_or_default($set, "style", "office");
		$this->set_or_default($set, "type", "bar");
		$this->set_or_default($set, "width", 600);
		$this->set_or_default($set, "height", 400);
		$this->set_or_default($set, "title_hperc", 0.1, "perc");
		$this->set_or_default($set, "graph_hperc", 
			1 - $this->options["title_hperc"], "perc");
		$this->set_or_default($set, "graph_wperc", 0.90, "perc");
		$this->set_or_default($set, "title_wperc", 0.8, "perc");
		$this->set_or_default($set, "fontpath", ".");
		$this->set_or_default($set, "font_title", "tahoma");
		$this->set_or_default($set, "font_axis", "arial");
		$this->set_or_default($set, "font_label", "arial");
		$this->set_or_default($set, "font_label_size", 7);
		$this->set_or_default($set, "font_axis_size", 8);
		$this->set_or_default($set, "font_legend", "arial");
		$this->set_or_default($set, "legend_wperc", 0.20, "perc");
		$this->set_or_default($set, "force_legend", 0);
		$this->set_or_default($set, "padding", 2);
		if (is_array($this->options["scolors"])) {
			$this->scolors = $this->options["scolors"];
		}
		$this->set_or_default($set, "swatch_size", 11);
		$this->set_or_default($set, "legend_max_font_size", 
			$this->options["swatch_size"]);
		$this->set_or_default($set, "dropshadows", 1);
		$this->set_or_default($set, "shadow_y_offset", 2);
		$this->set_or_default($set, "shadow_x_offset", 2);
		$this->set_or_default($set, "shadow_color", "#555555");
		$this->set_or_default($set, "shadow_trans", "60");
		$this->set_or_default($set, "dot_diam", 7);
		$this->set_or_default($set, "linedots", 0);
		$this->set_or_default($set, "pie_tperc", 0.05, "perc");
		$this->set_or_default($set, "pie_wperc", 0.95, "perc");
		$this->set_or_default($set, "pie_hperc", 0.60, "perc");
		$this->set_or_default($set, "square", 0);
		$this->set_or_default($set, "debug", 0);
		$this->set_or_default($set, "scriptbase", "multigraph.php");
		$this->set_or_default($set, "half_marks", 1);
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
		$this->img = imagecreatetruecolor($this->options["width"],
			$this->options["height"]);
	}
/*>>>*/
	// some color manipulation functions
	function allocate_color($name, $color, $trans=0) {/*<<<*/
	// allows the allocation of color by hex -- easier to read and set
		$this->colors[$name] = $this->allocate_hex_color($color, $trans);
	}
/*>>>*/
	function allocate_hex_color($color, $trans=0) {/*<<<*/
		return imagecolorallocatealpha($this->img,
			$this->componentval($color, "r"),
			$this->componentval($color, "g"),
			$this->componentval($color, "b"),
			$trans);
	}
/*>>>*/
	function zp($str, $num = 2) {/*<<<*/
		while (strlen($str) < $num) {
			$str="0".$str;
		}
		return $str;
	}
/*>>>*/
	function componentval($color, $component) {/*<<<*/
		if (substr($color, 0, 1) == "#") {
			$color = substr($color, 1);
		}
		switch (strtolower(substr($component, 0, 1))) {
			case "r": {
				return base_convert(substr($color, 0, 2), 16, 10);
				break;
			}
			case "g": {
				return base_convert(substr($color, 2, 2), 16, 10);
				break;
			}
			case "b": {
				return base_convert(substr($color, 4, 2), 16, 10);
				break;
			}
			default: {
				print("can't get component $component");
			}
		}
	}
/*>>>*/
	function color_from_components ($r, $g, $b) {/*<<<*/
		return "#".$this->zp(base_convert($r, 10, 16), 2)
			.$this->zp(base_convert($g, 10, 16),2)
			.$this->zp(base_convert($b, 10, 16), 2);
	}
/*>>>*/
	function darken_to($col, $perc) {/*<<<*/
		$r = (int)($this->componentval($col, "r") * $perc);
		$g = (int)($this->componentval($col, "g") * $perc);
		$b = (int)($this->componentval($col, "b") * $perc);
		return $this->color_from_components($r, $g, $b);
	}
/*>>>*/
	function set_or_default(&$srcarr, $idx, $def, $checking = "") {/*<<<*/
		if (array_key_exists($idx, $srcarr)) {
			$this->options[$idx] = $srcarr[$idx];
			$this->log("setting $idx to requested val ".$srcarr[$idx]);
		} else {
			$this->options[$idx] = $def;
			$this->log("setting $idx to default val ".$def);
		}
		switch ($checking) {
			case "percentage":
			case "perc": {
				if (is_numeric($this->options[$idx])) {
					if ($this->options[$idx] > 100) {
						$this->log("option $idx should be %, but is > 100,"
							." using default $def");
						$this->options[$idx] = $def;
					} elseif ($this->options[$idx] > 1) {
						$this->options[$idx] /= 100;
					}
				} else {
					$this->log("option $idx should be %, but is non-numeric,"
						." using default $def");
					$this->options[$idx] = $def;
				}
				break;
			}
			default: {
			}
		}
	}
/*>>>*/
	function add_series(&$arr, $name = "") {/*<<<*/
		// a series is an array where the index of each element is the
		//	x value and the value of that element is the y-value
	
		if ($name == "") {
			$name = "Series ".count($this->series);
		}
		$this->log("adding series: $name");
		foreach ($arr as $idx => $val) {
			$this->series[$name][$idx] = $val;
		}
	}
/*>>>*/
	function parseurl() {/*<<<*/
		// looks for settings in the _GET array
		foreach ($_GET as $idx => $val) {
			$idx = strtolower($idx); // just in case
			if (preg_match("/^[xy][0123456789]+_[0123456789]+$/i", $idx)) {
				// x-val for series
				$this->log("$idx matched for series point expr");
				$axis = substr($idx, 0, 1);
				$tmp = substr($idx, 1);
				$spoint = explode("_", $tmp);
				$this->log("adding to series: ".$spoint[0].", axis: "
					.$axis.", pointnum: ".$spoint[1]);
				$series[$spoint[0]][$axis][$spoint[1]] = $val;
				continue;
			}
			if (preg_match("/^[xy][0123456789]+/i", $idx)) {
				// allow for single series to be specified with x1=val&y1=val
				//	shorthand
				$this->log("$idx matched for short series point expr");
				$axis = substr($idx, 0, 1);
				$pointnum = substr($idx, 1);
				$series[0][$axis][$pointnum] = $val;
				continue;
			}
			if (preg_match("/^s[0123456789]+/i", $idx)) {
				$this->log("$idx matched for series name expr");
				$i = substr($idx, 1);
				$snames[$i] = $val;
				continue;
			}
			if (preg_match("/^[xy]l/i", $idx)) {
				// x or y label, for non-numeric axes
				$axis = substr($idx, 0, 1);
				switch ($axis) {
					case "x": {
						$this->xlabels = explode(",", $val);
						break;
					}
					case "y": {
						$this->ylabels = explode(",", $val);
						break;
					}
					default: {
						$this->log("label for axis $axis ignored ($idx)");
					}
				}
			}
			
			$options[$idx] = $val;
		}
		// check that each spoint has an x and a y value; generate series
		if (is_array($series)) {
			foreach ($series as $sidx => $s) {
				$this->log("working with series $sidx");
				$tmp = array();
				foreach ($s["x"] as $idx => $val) {
					if (array_key_exists($idx, $s["y"])) {
						$tmp[$val] = $s["y"][$idx];
					}
				}
				if (array_key_exists($sidx, $snames)) {
					$sname = $snames[$sidx];
				} else {
					$sname = "Series $sidx";
				}
				$this->log("series $sidx has name $sname");
				if (array_key_exists("sc".$sidx, $_GET)) {
					$this->scolors[$sidx] = $_GET["sc".$sidx];
				}
				$this->add_series($tmp, $sname);
			}
		}

		$this->setup($options);
	}
/*>>>*/
	function render() {/*<<<*/
		$this->log("starting render cycle for ".$this->options["type"]." graph");
		$this->select_style();
		imagefilledrectangle($this->img, 0, 0, $this->options["width"], 
			$this->options["height"], $this->colors["bg"]);
		$foo = imagecolorallocatealpha($this->img, 255, 0, 255, 64);
		// get bounds
		$this->get_bounds();
		// title
		$this->render_title();
		// the actual graph -- get the member function to draw it.
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
	function render_label($blx, $bly, $font, $size, $color, $text, $opts="") {/*<<<*/
		/* attempts to put a label at the required position with the required
			font, size, color & text. Will try moving the label around if
			it conflicts with the position of another label. Will add
			successfully rendered labels to the robjects repository
			We try first going left-to right at the co-ords given, then
			trying "one line" down, then to the left of the pos, then "one
			line" down from there, and finally vertically, first above, and then
			below the requested place

			$opts is an array with the following possibilities:
			"displace"	:: displace the label by this amount. We assume that
							there is a known object that will have a radius
							of this size, and we move around it. good for
							labels on points
			"shadow"	:: boolean, if on, a dropshadow is drawn
			"x_pos"		:: "left" or "right"
			"y_pos"		:: "top" or "bottom"
		*/
		$options = array(
			"shadow"	=>	$this->options["dropshadows"],
			"displace"	=>	0,
			"x_pos"		=>	"left",
			"y_pos"		=>	"bottom",
			"angle"		=>	0,
		);
		if (is_array($opts)) {
			foreach ($opts as $idx => $val) {
				$options[$idx] = $val;
			}
		}
		
		$red = $this->allocate_hex_color("#ff0000");
		$angle = $options["angle"];
		$reqdspace = imagettfbbox($size, $angle, 
			$this->get_font_file($font), $text);
		$w = abs($reqdspace[2] - $reqdspace[0]);
		$h = abs($reqdspace[5] - $reqdspace[3]);
		$i = 0;
		if ($options["x_pos"] == "right") {
			$this->log("x_pos is right: adding $w");
			$blx -= $w;
		}
		if ($blx < 0) {
			$this->log("cannot render label \"$text\" at x = $blx");
			return;
		}
		if ($options["y_pos"] == "top") {
			$this->log("y_pos is top: adding $h");
			$bly += $h;
		}
		if ($y > $this->options["height"]) {
			$this->log("cannot render label \"$text\" at y = $bly");
			return;
		}
		if (is_array($this->robjects)) {
			// check that no object listed would be overdrawn. Only
			//	objects that should not be overdrawn should be listed.
			while ($i < 10) {
				$dobreak = false;
				switch ($i) {
					case 0: { // at the point
						$tx0 = $blx + $options["displace"];
						$ty0 = $bly;
						$this->log("trying at point, ($blx,$bly), with "
							."displacment ".$options["displace"]
							." and angle $angle");
						break;
					}
					case 3: { // below the point
						$tx0 = $blx + $options["displace"];
						$ty0 = $bly + $h + 2 + $options["displace"];
						$this->log("trying $h below the point");
						break;
					}
					case 2: { // left of the point
						$tx1 = $blx - $w - 2 - $options["displace"];
						$ty1 = $bly;
						$this->log("trying left of the point");
						break;
					}
					case 1: { // above the point
						$tx0 = $blx - $options["displace"];
						$ty0 = $bly - $h - 2 - $options["displace"];
						$this->log("trying $h above the point");
						break;
					}
					case 4: { // left and down of the point
						$tx0 = $blx - $w - 2 - $options["displace"];
						$ty0 = $bly + $h + 2 + $options["displace"];
						break;
					}
					case 6: { //right and down of the point
						$tx0 = $blx + $w + 2 + $options["displace"];
						$ty0 = $bly + $h + 2 + $options["displace"];
						break;
					}
					case 5: { // left and up of the point
						$tx0 = $blx - $w - 2 - $options["displace"];
						$ty0 = $bly - $h - 2 - $options["displace"];
						break;
					}
					case 7: { //right and up of the point
						$tx0 = $blx + $w + 2 + $options["displace"];
						$ty0 = $bly - $h - 2 - $options["displace"];
						break;
					}
					case 8: { // vertical, up, away from point
						$angle = 90;
						$reqdspace = imagettfbbox($size, $angle, 
							$this->get_font_file($font), $text);
						$w = $reqdspace[0] - $reqdspace[6];
						$h = $reqdspace[3] - $reqdspace[1];
						$tx0 = $blx;
						$ty0 = $bly - $options["displace"];
						break;
					}
					case 9: { // vertical, down, away from point
						$angle = 270;
						$reqdspace = imagettfbbox($size, $angle, 
							$this->get_font_file($font), $text);
						$w = $reqdspace[4] - $reqdspace[2];
						$h = $reqdspace[2] - $reqdspace[5];
						$tx0 = $blx - $tx;
						$ty0 = $bly + $w + $options["displace"];
						break;
					}

				}

				// check that the label can fit on the image
				if ($tx0 < 0) $tx0 = 0;
				if ($ty0 > $this->options["height"]) {
					$ty0 = $this->options["height"];
					$this->log("$text was moved to ".$this->options["height"]);
				}
				$tx1 = $tx0 + $w;
				$ty1 = $ty0 - $h;
				
				foreach ($this->robjects as $robj) {
					// simple cases first
					if (count($robj) > 3) {
						if ($this->areas_intersect($tx0, $ty1, $tx1, $ty0,
							$robj["x0"], $robj["y0"], $robj["x1"],
							$robj["y1"], "label", $robj["type"])) {
							$dobreak = false;
							break;
						}
						$dobreak = true;
					}
				}
				if ($dobreak) break;
				$i++;
			}
		} else {
			$tx0 = $blx;
			$ty0 = $bly;
			$tx1 = $bly + $w;
			$ty1 = $bly - $h;
		}
		if ($i < 10) {
			if ($options["shadow"]) {
				$shadow_x_offset = $this->options["shadow_x_offset"];
				$shadow_y_offset = $this->options["shadow_y_offset"];
				if ($size < 10) {
					// small fonts look funny with too deep a shadow
					$shadow_x_offset -= 1;
					$shadow_y_offset -= 1;
				}
				imagettftext($this->img, $size, $angle, 
					$tx0 + $this->options["shadow_x_offset"], 
					$ty0 + $this->options["shadow_y_offset"], 
					$this->colors["shadow"], 
					$this->get_font_file($font), $text);
			}
			$this->log("drawing label \"$text\" at $tx0,$ty0 (fit ".($i).")");
			imagettftext($this->img, $size, $angle, $tx0, $ty0, $color, 
				$this->get_font_file($font), $text);
			// add this label into the robjects array
			$this->robjects[] = array(
				"x0"	=>	$tx0,
				"x1"	=>	$tx1,
				"y0"	=>	$ty1,
				"y1"	=>	$ty0,
				"type"	=> 	"label",
				"value"	=>	$text,
			);
		} else {
			$this->log("could not render label \"$text\" at $blx,$bly; nor"
				." could I find a close yet better place.");
		}
	}
/*>>>*/
	function areas_intersect ($ax0, $ay0, $ax1, $ay1, $bx0, $by0, $bx1, 
		$by1, $type0 = "rect", $type1 = "rect") {/*<<<*/
		/* make sure we have the same ordering on these co-ords
		*/
		
		// first, simple rectangular check, since we would have to do it 
		//	anyway, and it's quite quick
		if ((($ax0 <= $bx0) && ($ax1 <= $bx0)) 
			|| (($ax0 >= $bx1) && ($ax1 >= $bx1))) {
			$x_ok = true;
		} else {
			$x_ok = false;
		}
		if ((($ay0 <= $by0) && ($ay1 <= $by0)) 
			|| (($ay0 >= $by1) && ($ay1 >= $by1))) {
			$y_ok = true;
		} else {
			$y_ok = false;
		}
		if ($y_ok || $x_ok) {
			$ret = false;
		} else {
			$ret = true;
		}
		if ($ret) {
			// extended tests for shapes that don't fill their rectangles
			switch ($type1) {
				case "line": {
					/* the line presents a space where most of the marked area
						is actually available, so we need to find another way to
						check intersection
					*/
					// get the equation that represents the line
					if ($bx0 == $bx1) {
						$this->log("would have hit a div-zero for line "
							."x0: $bx0; y0: $by0; x1: $bx1; y1: $bx1");
						return $ret;
					}
					$mb = ($by1 - $by0) / ($bx1 - $bx0);
					$cb = ($by1 / $mb);
					
					// check this against the first object
					switch ($type0) {
						case "line": {
							$ma = ($by1 - $by0) / ($bx1 - $bx0);
							$ca = ($by1 / $mb);
							// easiest case: find the x point at which the
							// eqns are equal:
							if ($ma == $mb) return false; // lines are parallel
							$x = ($cb - $ca) / ($ma - $mb);
							if (($x < $ax0) || ($x > $ax1)) {
								// intersection occurs outside of spread of line
								$ret = false;
							} else {
								$ret = true;
							}
							break;
						}
						case "rect":
						case "label":
						default: {
							// check if any part of this line falls in the
							//	space of B object -- remember that we've
							//	already done a rect check, so the line check is
							//	simplified.
							$ret = false;
							for ($x = $ax0; $x <= $ax1; $x++) {
								$y = ($ma * $x) + $ca;
								if (($y >= $by0) && ($y <= $by1)) {
									$ret = true;
									break;
								}
							}
						}
					}
					break;
				}
			}
		}
		/*
		if ($ret) {
			$this->log("area a ($ax0,$ay0  $ax1,$ay1) intersects area b"
				."($bx0,$by0  $bx1,$by1)");
		} else {
			$this->log("area a ($ax0,$ay0  $ax1,$ay1) misses area b"
				."($bx0,$by0  $bx1,$by1)");
		}
		*/
		return $ret;
	}
/*>>>*/
	function select_style() {/*<<<*/
		switch ($this->options["style"]) {
			case "matrix": {
				// green/white/black style <<<
				if (!is_array($this->scolors)) {
					$this->scolors = array(
						"#00ff00",
						"#005500",
						"#ff0000",
						"#6464fa",
						"#0000ff",
						"#b58a31",
						"#008888",
						"#98153e",
						"#f57e4f",
						"#222c81",
						"#c5f209",
						"#b13c66",
					);
				}
				$this->allocate_color("title", "#00ff00");
				$this->allocate_color("bg", "#000000", 127);
				$this->allocate_color("axis_values", "#aaffaa");
				$this->allocate_color("axis_line", "#eeeeee");
				$this->allocate_color("scale", "#eeeeee");
				$this->allocate_color("legend_bg", "#003300", 64);
				$this->allocate_color("legend_border", "#003300", 64);
				$this->allocate_color("legend_text", "#00ff88");
				$this->allocate_color("axis_text", "#eeeeee");
				$this->allocate_color("grid", "#006633");
				$this->allocate_color("point_label_text", "#00ff55");
				break;
				// >>>
			}
			case "spring": {
				// lightish brown/green/yellow/orange theme <<<
				if (!is_array($this->scolors)) {
					$this->scolors = array(
						"#ff0000",
						"#00ff00",
						"#0000ff",
						"#b58a31",
						"#008888",
						"#98153e",
						"#6464fa",
						"#f57e4f",
						"#222c81",
						"#c5f209",
						"#b13c66",
						"#f4ba56",
					);
				}
				$this->allocate_color("title", "#885300");
				$this->allocate_color("bg", "#dcdcdc");
				$this->allocate_color("axis_values", "#323232");
				$this->allocate_color("axis_line", "#646464");
				$this->allocate_color("scale", "#f0f0ff");
				$this->allocate_color("legend_bg", "#d4a151", 64);
				$this->allocate_color("legend_border", "#333333", 64);
				$this->allocate_color("legend_text", "#000000");
				$this->allocate_color("axis_text", "#000000");
				$this->allocate_color("grid", "#cccccc");
				$this->allocate_color("point_label_text", "#000000");
				break;
				// >>>
			}
			case "translite": {
				// transparent, on a light background <<<
				if (!is_array($this->scolors)) {
					$this->scolors = array(
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
				}
				$this->allocate_color("title", "#000064");
				$this->allocate_color("bg", "#fffffe");
				imagecolortransparent($this->img, $this->colors["bg"]);
				$this->allocate_color("axis_values", "#323232");
				$this->allocate_color("axis_line", "#646464");
				$this->allocate_color("scale", "#f0f0ff");
				$this->allocate_color("legend_bg", "#cdcdcd", 64);
				$this->allocate_color("legend_border", "#333333", 64);
				$this->allocate_color("legend_text", "#000000");
				$this->allocate_color("axis_text", "#000000");
				$this->allocate_color("grid", "#cccccc");
				$this->allocate_color("point_label_text", "#000000");
				break;
				// >>>
			}
			case "transdark": {
				// transparent, on a dark background <<<
				if (!is_array($this->scolors)) {
					$this->scolors = array(
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
				}
				$this->allocate_color("title", "#f0f0ff");
				$this->allocate_color("bg", "#000001");
				imagecolortransparent($this->img, $this->colors["bg"]);
				$this->allocate_color("axis_values", "#d0d0d0");
				$this->allocate_color("axis_line", "#aaaaaa");
				$this->allocate_color("scale", "#888888");
				$this->allocate_color("legend_bg", "#555588", 64);
				$this->allocate_color("legend_border", "#333333", 64);
				$this->allocate_color("legend_text", "#fcfcff");
				$this->allocate_color("axis_text", "#fcfcff");
				$this->allocate_color("grid", "#555555");
				$this->allocate_color("point_label_text", "#eeeeee");
				break;
				// >>>
			}
			case "office": 
			default: {
				// looks a little like something you might get from an
				//	office suite <<<
				if (!is_array($this->scolors)) {
					$this->scolors = array(
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
				}
				$this->allocate_color("title", "#000064");
				$this->allocate_color("bg", "#dcdcdc");
				$this->allocate_color("axis_values", "#323232");
				$this->allocate_color("axis_line", "#646464");
				$this->allocate_color("scale", "#f0f0ff");
				$this->allocate_color("legend_bg", "#cdcdcd");
				$this->allocate_color("legend_border", "#333333");
				$this->allocate_color("legend_text", "#000000");
				$this->allocate_color("point_label_text", "#000000");
				$this->allocate_color("axis_text", "#000000");
				$this->allocate_color("grid", "#cccccc");
				// >>>
			}
		}
		// shadow color: black, but transparent
		$this->allocate_color("shadow", $this->options["shadow_color"], 
			$this->options["shadow_trans"]);
		// set the allocated series colors.
		$i = 0;
		foreach ($this->series as $idx => $arr) {
			if (!array_key_exists($i, $this->scolors)) {
				$this->scolors[$i] = $this->color_from_components(rand(0, 255),
					rand(0, 255), rand(0, 255));
			}
			$this->ascolors[$idx] = 
				$this->allocate_hex_color($this->scolors[$i]);
			$this->log("allocated color ".$this->scolors[$i]." to series ".$idx." as color number: ".$this->ascolors[$idx]);
			$i++;
		}
	}
/*>>>*/
	function get_font_that_fits(&$str, $width, $height, $font, $max_size = 18, $angle = 0) {/*<<<*/
		// searches for the largest font that will fit a string
		//	into a given area
		$this->log("trying to get font $font to fit \"$str\" into box $width x $height");
		if (trim($str) == "") {
			$str = "fg"; // default in case!
			$this->log("empty string sent to get_font_that_fits; using 'fg' as"
				." default.");
		}
		for ($i = $max_size; $i > 4; $i--) {
			$area = imagettfbbox($i, $angle, $this->get_font_file($font), $str);
			$w = abs($area[2] - $area[6]);
			$h = abs($area[7] - $area[3]);
			if (($w < $width) && ($h < $height)) {
				$foo =  array(
					"size"	=> $i, 
					"width"	=> $w, 
					"height"=> $h,
				);
				$this->log("fitting with font size $i; bbox is $w x $h");
				return $foo;
			}
		}
	}
/*>>>*/
	function get_font_file($font) {/*<<<*/
		if (array_key_exists($font, $this->font_reg)) {
			return $this->font_reg[$font];
		}
		$try = $this->options["fontpath"]."/".$font;
		if (file_exists($try)) {
			if (is_readable($try)) {
				$this->font_reg[$font] = $try;
				return $try;
			}
		}
		$try = $this->options["fontpath"]."/".$font.".ttf";
		if (file_exists($try)) {
			if (is_readable($try)) {
				$this->font_reg[$font] = $try;
				return $try;
			}
		}
		// last resort: look for first ttf font in font-path
		$fonts = glob($this->options["fontpath"]."/*.ttf");
		if (is_array($fonts)) {
			if (is_readable($fonts[0])) {
				$this->font_reg[$font] = $fonts[0];
				return $fonts[0];
			}
		}
		$this->create_error_graphic("Could not find or open font ".$font);
	}
/*>>>*/
	function render_legend($keys = "") {/*<<<*/
		// only renders a legend if there is more than one series
		//	sets the graph width and height in pixels
		$legend_width = 0;
		if (!is_array($keys)) {
			$keys = array_keys($this->series);
			$this->series_count = count($this->series);
		} else {
			$this->options["force_legend"] = true;
			$this->series_count = count($keys);
		}
		$this->graph_y0 = $this->title_h + (4 * $this->options["padding"]);
		if (($this->options["force_legend"])
			|| (count($this->series) > 1)) { // we need a legend
			$this->log("we have ".count($this->series)." series, cap'n!");
			$longest_sname = "";
			foreach ($keys as $name) {
				if (strlen($name) > strlen($longest_sname)) {
					$longest_sname = $name;
				}
			}
			$w = $this->options["width"] * $this->options["legend_wperc"] -
				(4 * $this->options["padding"]);
			$x0 = $this->options["width"] - $w - 
				(3 * $this->options["padding"]);
			$y0 = $this->graph_y0;
			$line_height = ceil(($this->options["height"] * 
				(1 - $this->options["title_hperc"]) / $this->series_count)
				- 2 * ($this->options["padding"]));
			$size = $this->get_font_that_fits($longest_sname, $w - 
				(3 * $this->options["padding"]) - $this->options["swatch_size"],
					$line_height, $this->options["font_legend"], 
					$this->options["legend_max_font_size"]);
			if ($size["height"] > $this->options["swatch_size"]) {
				$line_height = $size["height"] + 
					(2 * $this->options["padding"]);
			} else {
				$line_height = $this->options["swatch_size"] + 
					(2 * $this->options["padding"]);
			}
			$h = $line_height * $this->series_count
				+ (2 * $this->options["padding"]);
			$this->log("series count: ".$this->series_count);
			$this->log("set legend height to $h");
			// draw containing box
			$this->legend_x0 = $x0;
			$this->legend_x1 = $x0 + $w;
			$this->legend_y0 = $y0;
			$this->legend_y1 = $y0 + (2 * $this->options["padding"]) + $h;
			if ($this->legend_y1 > $this->options["height"]) {
				$this->create_error_graphic("Not enough space for all series.");
			}
			$this->render_bordered_rect(
				$this->legend_x0, 
				$this->legend_y0, 
				$this->legend_x1,
				$this->legend_y1,
				$this->colors["legend_bg"], 
				$this->colors["legend_border"],
				1);
			// draw each key
			$i = 0;
			foreach ($keys as $sname) {
				$y = $y0 + ($i * $line_height) + (($line_height - 
					$this->options["swatch_size"])/2)
					+ (2 * $this->options["padding"]);
				$x = $x0 + $this->options["padding"];
				$this->render_bordered_rect($x, $y, 
					$x + $this->options["swatch_size"],
					$y + $this->options["swatch_size"],
					$this->ascolors[$sname],
					$this->colors["legend_border"],
					1);
				$i++;
				$y = $y0 + ($i * $line_height);
				$x = $x0 + $this->options["swatch_size"] + 
					$this->options["padding"];
				$this->render_label($x + (2 * $this->options["padding"]),
					$y, $this->options["font_legend"], $size["size"],
					$this->colors["legend_text"], $sname);
				/*
				imagettftext($this->img, $size["size"], 0, 
					$x + (2 * $this->options["padding"]), 
					$y, 
					$this->colors["legend_text"], 
					$this->get_font_file($this->options["font_legend"]),
					$sname);
				*/
			}
			// now that all the labels are drawn, register the legend
			//	area as non-overwritable
			$this->robjects[] = array(
				"x0"	=> $this->legend_x0,
				"y0"	=> $this->legend_y0,
				"x1"	=> $this->legend_x1,
				"y1"	=> $this->legend_y1,
				"type"	=> "rect",
				"value"	=> "legend bg",
			);
			$this->graph_w = $x0 * $this->options["graph_wperc"];
			$diff = ($x0 - $this->graph_w) / 2;
			$this->graph_x0 = $diff;
			$this->graph_x1 = $x0 - $diff;
		} else {
			$this->graph_w = $this->options["width"] * 
				$this->options["graph_wperc"];
			$diff = ($this->options["width"] - $this->graph_w) / 2;
			$this->graph_x0 = $diff;
			$this->graph_x1 = $diff + $this->graph_w;
			$this->legend_x0 = $this->options["width"];
			$this->legend_x1 = $this->options["width"];
			$this->legend_y0 = $this->title_h;
			$this->legend_y1 = $this->title_h;
		}
		$rem_area = $this->options["height"] - $this->title_h;
		$this->graph_h = $rem_area * $this->options["graph_hperc"];
		$this->log("graph_h = ".$this->graph_h);
		$this->log("title_h = ".$this->title_h);
		$this->log("rem_area = ".$rem_area);
		$diff = ($rem_area - $this->graph_h) / 2;
		$this->graph_y1 = $this->options["height"] - $diff;
		
		$this->graph_x0 = ceil($this->graph_x0);
		$this->graph_y0 = ceil($this->graph_y0);
		$this->graph_x1 = floor($this->graph_x1);
		$this->graph_y1 = floor($this->graph_y1);
		$this->log("legend complete: graph should be found in area: x0: "
			.$this->graph_x0.", y0: ".$this->graph_y0.", x1: ".$this->graph_x1
			.", y1: ".$this->graph_y1);
		/* renders a rectangle where the graph area is (for debug purposes)
		$this->render_bordered_rect($this->graph_x0, $this->graph_y0,
			$this->graph_x1, $this->graph_y1, $this->colors["legend_bg"],
			$this->colors["legend_border"]);
		*/
	}
/*>>>*/
	function render_bordered_rect($x0, $y0, $x1, $y1, $int_cn, $border_cn, $shadow=0, $check_collisions=0, $options="") {/*<<<*/
		// expects an area given by x0, y0, x1, y1; will normalise a negative
		//	area; expects an allocated color number for the interior and border.
		if (!is_array($options)) $options = array();
		if ($x0 > $x1) {
			$tmp = $x0;
			$x0 = $x1;
			$x1 = $tmp;
		}
		if ($y0 > $y1) {
			$tmp = $y0;
			$y0 = $y1;
			$y1 = $tmp;
		}
		if ($check_collisions) {
			foreach ($this->robjects as $robj) {
				if ($this->areas_intersect($x0, $y0, $x1, $y1, $robj["x0"],
					$robj["y0"], $robj["x1"], $robj["y1"])) {
					$this->log("cannot render rect from ($x0,$y0) to ($x1,$y1)"
						." -- intersects with ".$robj["type"]." ("
						.$robj["value"]);
					return;
				}
			}
		}
		if ($shadow) {
			$sx0 = $x0 + $this->options["shadow_x_offset"];
			$sx1 = $x1 + $this->options["shadow_x_offset"];
			if (array_search("no bottom shadow", $options) !== false) {
				$sy1 = $y1;
			} else {
				$sy1 = $y1 + $this->options["shadow_y_offset"];
			}
			if (array_search("extra top shadow", $options) !== false) {
				$sy0 = $y0;
			} else {
				$sy0 = $y0 + $this->options["shadow_y_offset"];
			}
			$this->log("drawing shadow at $sx0,$sy0 to $sx1,$sy1");
			if ($this->options["dropshadows"]) {
				imagefilledrectangle($this->img, 
					$sx0, 
					$sy0, 
					$sx1,
					$sy1, 
					$this->colors["shadow"]);
			}
		}
		imagerectangle($this->img, $x0, $y0, $x1, $y1, $border_cn);
		imagefilledrectangle($this->img, $x0+1, $y0+1, $x1-1, $y1-1, $int_cn);
	}
/*>>>*/
	function render_title() {/*<<<*/
		if (strlen($this->options["title"])) {
			$h = $this->options["height"] * $this->options["title_hperc"];
			$this->title_h = $h;
			$w = $this->options["width"] * $this->options["title_wperc"];
			$size = $this->get_font_that_fits($this->options["title"],
				$w, $h, $this->options["font_title"]);
			$x = ($this->options["width"] - $size["width"]) / 2; // centered
			$y = $h - (($h - $size["height"]) / 2); //centered in top 10%
			$this->render_label($x, $y, $this->options["font_title"],
				$size["size"], $this->colors["title"], $this->options["title"],
				array("shadow" => 1));
		} else {
			$this->title_h = 0;
		}
	}
/*>>>*/
	function translate_coord(&$acoord) {/*<<<*/
		// translates a x,y series pair into a pixel-value pair that
		//	marks the location of that point in graph space.
		//	modifies the given array of coords.
		$acoord["x"] = $this->translate_x_coord($acoord["x"]);
		$acoord["y"] = $this->translate_y_coord($acoord["y"]);
	}
/*>>>*/
	function translate_x_coord($x) {/*<<<*/
		if ($this->x_numeric) {
			$trans = (int)($this->x0px + ($x * $this->xtranspx));
		} else {
			//locate $x in the x axis
			if (is_numeric($x)) {
				$pos = $y;
			} else {
				$pos = array_search($x, $this->xlabels);
			}
			if ($pos === false) {
				$this->create_error_graphic("unable to find ($x) in the x lookup "
					."array");
			} else {
				$trans = $this->x0px + ($pos * $this->xtranspx);
			}
		}
		$this->log("translating $x to ".$trans);
		return $trans;
	}
/*>>>*/
	function translate_y_coord($y) {/*<<<*/
		if ($this->y_numeric) {
			return (int)($this->y0px - ($y * $this->ytranspx));
		} else {
			if (is_numeric($y)) {
				$pos = $y;
			} else {
				$pos = array_search($y, $this->ylabels);
			}
			if ($pos === false) {
				$this->create_error_graphic("unable to find ($y) in the y lookup "
					."array");
			} else {
				return $this->y0px - ($pos * $this->ytranspx);
			}
		}
	}
/*>>>*/
	function get_bounds() {/*<<<*/
		if (!is_array($this->series)) {
			$this->create_error_graphic("No series are defined.");
		}
		// determine if this is a purely numeric graph
		$this->x_numeric = true;
		foreach ($this->series as $s) {
			foreach (array_keys($s) as $x) {
				if (!is_numeric($x)) {
					$this->x_numeric = false;
					if ($this->options["type"] != "pie") {
						if (!is_array($this->xlabels)) {
							$this->create_error_graphic("X axis is non-numeric"
								." but X labels were not specified.");
						}
					}
				}
			}
		}
		$this->y_numeric = true;
		foreach ($this->series as $s) {
			foreach (array_values($s) as $y) {
				if (!is_numeric($y)) {
					$this->y_numeric = false;
					if (($this->options["type"] != "pie")) {
						if (!is_array($this->ylabels)) {
							$this->create_error_graphic("Y axis is non-numeric,"
								."but Y labels were not specified");
						}
					} elseif ($this->x_numeric == false) {
						$this->create_error_graphic("Both axes are non-numeric:"
							." a pie chart cannot be made out of this.");
					}
				}
			}
		}
		
		$this->min_x = 0;
		$this->min_y = 0;
		$this->max_x = 0;
		$this->max_y = 0;
		if ($this->x_numeric) {
			// min and max x and y
			foreach ($this->series as $s) {
				foreach (array_keys($s) as $x) {
					$this->log("testing x value $x");
					if ($x < $this->min_x) {
						$this->min_x = $x;
					}
					if ($x > $this->max_x) {
						$this->max_x = $x;
					}
				}
			}
		} else {
			// in non-numeric, we assume that the first series
			//	has all the x-values, so that we can get the max
			//	and min indeces of these values
			$this->max_x = count($this->xlabels) - 1;
		}
		
		if ($this->y_numeric) {
			foreach ($this->series as $s) {
				foreach (array_values($s) as $y) {
					if ($y < $this->min_y) {
						$this->min_y = $y;
					}
					if ($y > $this->max_y) {
						$this->max_y = $y;
					}
				}
			}
		} else {
			$this->max_y = count($this->ylabels) - 1;
		}

		$this->log("min x: ".$this->min_x."; min y ".$this->min_y);
		$this->log("max x: ".$this->max_x."; max y ".$this->max_y);
		if ($this->y_numeric) {
			$this->log("y values are numeric");
		} else {
			$this->log("y values are non-numeric");
		}
		if ($this->x_numeric) {
			$this->log("x values are numeric");
		} else {
			$this->log("x values are non-numeric");
		}
	}
/*>>>*/
	function draw_dot($xpx, $ypx, $innercolor, $outercolor, $diam = 0) {/*<<<*/
		if ($diam == 0) {
			$diam = $this->options["dot_diam"];
		}
		imagefilledellipse($this->img, $xpx, $ypx, $diam, $diam, $innercolor);
		imageellipse($this->img, $xpx, $ypx, $diam, $diam, $outercolor);
	}
/*>>>*/
	function render_axes() {/*<<<*/
		// figures out where the axes should lie & where to make zero,zero
		//	sets $this->x0px and $this->y0px, which are the pixel positions of
		//	x0 and y0 on the graph
		
		// make sure that axis markings will fit
		//	1) height of axis font <<<
		if ($this->y_numeric) {
			$str = (strlen($this->max_y) > strlen($this->min_y))
				? $this->max_y : $this->min_y;
		} else {
			$str = (strlen($this->ylabels[$this->max_y]) > 
				strlen($this->ylabels[$this->min_y]))
				? $this->ylabels[$this->max_y] : $this->ylabels[$this->min_y];
		}
		$farea = imagettfbbox($this->options["font_axis_size"], 0, 
			$this->get_font_file($this->options["font_axis"]),
			$str);
		$fw = abs($farea[2] - $farea[0]);
		$fh = abs($farea[7] - $farea[1]);
		$absolute_min_x = ($fw + (2 * $this->options["padding"]));

		if ($this->x_numeric) {
			$str = (strlen($this->max_x) > strlen($this->min_x))
				? $this->max_x : $this->min_x;
		} else {
			$str = (strlen($this->xlabels[$this->max_x]) > 
				strlen($this->xlabels[$this->min_x]))
				? $this->xlabels[$this->max_x] : $this->xlabels[$this->min_x];
		}
		$farea = imagettfbbox($this->options["font_axis_size"], 0, 
			$this->get_font_file($this->options["font_axis"]),
			$str);
		$fw = abs($farea[2] - $farea[0]);
		$fh = abs($farea[7] - $farea[1]);
		$absolute_max_y = 
			($this->options["height"] - $fh - (2 * $this->options["padding"]));
		if ($this->graph_y1 > $absolute_max_y)
			$this->graph_y1 = $absolute_max_y;
		// >>>
		//	2) width of longest x axis string <<<
		$absolute_max_x = 
			($this->legend_x0 - $fw - (8 * $this->options["padding"]));

		if ($this->graph_x1 > $absolute_max_x) 
			$this->graph_x1 = $absolute_max_x;
		// >>>
		// y axis title (if applicable) <<<
		if ($this->options["y_title"] != "") {
			if (strtolower(substr($this->options["y_title_orient"], 0, 1))
				== "v") {
				$angle = 90;
			} else {
				$angle = 0;
			}
			$ylarea = imagettfbbox($this->options["font_axis_size"], $angle,
				$this->get_font_file($this->options["font_axis"]),
				$this->options["y_title"]);
			$this->log("y title at angle $angle");
			if ($angle) {
				$y_label_w = abs($ylarea[0] - $ylarea[6]);
				$y_label_h = abs($ylarea[3] - $ylarea[1]);
			} else {
				$y_label_w = $ylarea[2] - $ylarea[0];
				$y_label_h = $ylarea[7] - $ylarea[1];
			}
			$diff = (4 * $this->options["padding"]) + $y_label_w;
			$absolute_min_x += $diff;
			if ($this->graph_x0 < $absolute_min_x)
				$this->graph_x0 = $absolute_min_x;
			$this->graph_w = $this->graph_x1 - $this->graph_x0;
			
			$x = (int)($this->options["padding"]);
			if ($angle) {
				$this->log("adding label width ($y_label_w) to xpos");
				$x += $y_label_w;
			}
			$y = (int)($this->graph_y1 - (($this->graph_y1 - $y_label_w) / 2));
			$this->render_label($x, $y, $this->options["font_axis"],
				$this->options["font_axis_size"],
				$this->colors["axis_text"],
				$this->options["y_title"],
				array("angle" => $angle));
		}
		// >>>
		// x axis title (if applicable) <<<
		if ($this->options["x_title"] != "") {
			$xlarea = imagettfbbox($this->options["font_axis_size"], 0,
				$this->get_font_file($this->options["font_axis"]),
				$this->options["x_title"]);
			$x_label_w = abs($xlarea[2] - $xlarea[0]);
			$x_label_h = abs($xlarea[7] - $xlarea[1]);
			$this->log("absolute_max_y is $absolute_max_y");
			$absolute_max_y -= ((4 * $this->options["padding"]) + $x_label_h);
			$this->log("absolute_max_y is $absolute_max_y");
			if ($this->graph_y1 > $absolute_max_y)
				$this->graph_y1 = $absolute_max_y;
			$this->graph_h = $this->graph_y1 - $this->graph_y0;
			$x = (int)($this->graph_x1 - (($this->graph_x1 - $x_label_w) / 2));
			$y = (int)($this->options["height"] - 
				($this->options["padding"] * 2));
			$this->log("adding x_title \"".$this->options["x_title"]
				."\" at $x,$y");
			$this->render_label($x, $y, $this->options["font_axis"],
				$this->options["font_axis_size"],
				$this->colors["axis_text"],
				$this->options["x_title"]);
		}
		// >>>
		$this->log("graph x0 cannot be less than: ".$absolute_min_x);
		if ($this->graph_x0 < $absolute_min_x)
			$this->graph_x0 = $absolute_min_x;
		// y-axis requirements <<<
		if ($this->y_numeric) {
			// (thanks, Carlos Reche (author of Powergraphic))
			$digits = strlen(abs(round($this->max_y - $this->min_y)));
			$this->log("digits: $digits");
			$this->yinterval = pow(10, $digits-1);
			$this->log("yinterval: ".$this->yinterval);
			$this->max_scale_y = round($this->max_y 
				+ ($this->yinterval - ($this->max_y % $this->yinterval)), 1);
			$this->log("max_scale_y is ".$this->max_scale_y);
			$this->min_scale_y = round(($this->min_y
				- ($this->min_y % $this->yinterval)), 1);
			$total_y_dist = $this->max_scale_y - $this->min_scale_y;
			$this->ytranspx = ($this->graph_h / $total_y_dist);
			$this->y0px = $this->graph_y1 + ($this->min_y * $this->ytranspx);
		} else {
			$this->min_scale_y = 0;
			$this->max_scale_y = $this->max_y;
			$this->yinterval = 1;
			$this->ytranspx = ($this->graph_h / $this->max_scale_y);
			$this->y0px = $this->graph_y1;
		}
		$this->log("scales: y : ".$this->min_scale_y." to ".$this->max_scale_y);
		// y-axis: if there is a lot of space, add in yinterval/2 markers
		$this->y_marker_distance = 
			$this->translate_y_coord($this->max_y - $this->yinterval) -
			$this->translate_y_coord($this->max_y);
		if ($this->options["half_marks"]) {
			if ($this->y_marker_distance > (3 * $fh)) {
				$this->yinterval /= 2;
			}
			// if there is really a lot of space, we drop markers even more, to 1/10
			if ($this->y_marker_distance > (15 * $fh)) {
				$this->yinterval /= 5;
			}
		}
		$this->y_marker_distance = 
			$this->translate_y_coord($this->max_y - $this->yinterval) -
			$this->translate_y_coord($this->max_y);
		// >>>
		// x-axis requirements <<<
		if ($this->options["type"] == "bar") {
			// when drawing a bar graph, the interval *must* be one!
			//	also, we draw from min_x to max_x
			$this->log("FOOBAR");
			$this->min_scale_x = $this->min_x;
			$this->log("min_scale_x is ".$this->min_scale_x);
			if ($this->x_numeric) {
				$this->max_scale_x = $this->max_x;
			} else {
				$this->max_x++;
				$this->max_scale_x = $this->xlabels[count($this->xlabels)-1];
				$this->log("setting max_scale_x to last element of xlabels");
			}
			$this->xinterval = 1;
			if ($this->x_numeric) {
				$total_x_dist = $this->max_scale_x - $this->min_scale_x;
			} else {
				$total_x_dist = count($this->xlabels);
			}
			$this->xtranspx = ($this->graph_w / $total_x_dist);
			$this->x0px = $this->graph_x0 + 
				abs($this->xtranspx * $this->min_scale_x);
			$this->x_marker_distance = $this->xtranspx;
		} else {
			if ($this->x_numeric) {
				$digits = strlen(abs(round($this->max_x - $this->min_x)));
				$this->xinterval = pow(10, $digits-1);
				$this->log("xinterval is: ".$this->xinterval);
				$this->max_scale_x = $this->max_x;
				$this->min_scale_x = $this->min_x;
				$total_x_dist = $this->max_scale_x - $this->min_scale_x;
				if ($total_x_dist == 0) {
					$this->create_error_graphic("X distance is zero!");
				} {
					$this->xtranspx = ($this->graph_w / $total_x_dist);
				}
				$this->x0px = $this->graph_x0 + 
					abs($this->xtranspx * $this->min_scale_x);
			} else {
				// having a non-numeric x-axis, we have to ascertain the number
				//	of points, and use each point as a marker (because that's
				//	what the client would expect). Also, x0 would be on the 
				//	extreme left.
				$this->min_scale_x = 0;
				$this->max_scale_x = $this->max_x;
				$this->xtranspx = $this->graph_w / $this->max_scale_x;
				$this->xinterval = 1;
				$this->x0px = $this->graph_x0;
			}
			// x-axis: if there is a lot of space, add in xinterval/2 markers
			$this->x_marker_distance = $this->translate_x_coord($this->max_x) -
					$this->translate_x_coord($this->max_x - $this->xinterval);
			if ($this->options["half_marks"]) {
				if ($this->x_marker_distance > (3 * $fw)) {
					$this->xinterval /= 2;
				}
				if ($this->x_marker_distance > (15 * $fw)) {
					$this->xinterval /= 5;
				}
			}
			$this->x_marker_distance = $this->translate_x_coord($this->max_x) -
					$this->translate_x_coord($this->max_x - $this->xinterval);
		}
		if ($this->options["square"]) {
			// we choose the smallest scale, apply to both for "square"
			//	scales (where 1y is the same distance as 1x)
			if ($this->xtranspx < $this->ytranspx) {
				$this->yinterval = $this->xinterval;
				$this->y_marker_distance = $this->x_marker_distance;
				$this->ytranspx = $this->xtranspx;
			} else {
				$this->xinterval = $this->yinterval;
				$this->x_marker_distance = $this->y_marker_distance;
				$this->xtranspx = $this->ytranspx;
			}
		}
		$this->log("scales: x : ".$this->min_scale_x." to ".$this->max_scale_x);
		// >>>
		$this->x0px = round($this->x0px);
		$this->y0px = round($this->y0px);
		$this->log("graph center is at: ".$this->x0px.",".$this->y0px);
		// draw y-axis line <<<
		imageline($this->img, $this->x0px, 
			$this->translate_y_coord($this->max_scale_y), $this->x0px,
			$this->translate_y_coord($this->min_scale_y), 
			$this->colors["axis_line"]);
		// draw grid & markers
		$max_scale_x_px = $this->translate_x_coord($this->max_scale_x);
		if (($this->options["type"] == "bar")
			|| ($this->options["type"] == "hbar")) {
			$max_scale_x_px += $this->xtranspx;
		}
		$min_scale_x_px = $this->translate_x_coord($this->min_scale_x);
		$this->log("max_scale_x_px is $max_scale_x_px (".$this->max_scale_x.")");
		for ($i = $this->max_scale_y; $i >= $this->min_scale_y; 
				$i-=$this->yinterval) {
			$this->log("y translation is: ".$this->ytranspx."px per y unit");
			$this->log("making y marking at value $i");
			$y = $this->translate_y_coord($i); 
			$label = ($this->y_numeric)?$i:$this->ylabels[$i];
			// gridline
			if ($i == 0) continue; // no need to draw grid on y0
			imageline($this->img, $min_scale_x_px + 2, $y,	
				$max_scale_x_px,
				$y, $this->colors["grid"]);
			// marker
			$x = $this->x0px - 1 - $this->options["padding"];
			$this->log(" -- making y marking ($label) at px $x,$y");
			imageline($this->img, $this->x0px - 1, $y,	$this->x0px + 1,
				$y, $this->colors["axis_line"]);
			$this->render_label($x,
				$y, $this->options["font_axis"], 
				$this->options["font_axis_size"],
				$this->colors["axis_text"], 
				$label, array("x_pos" => "right"));
		}
		// >>>
		// draw x-axis line <<<
		$this->log("starting x axis render");
		$max_scale_y_px = $this->translate_y_coord($this->max_scale_y);
		$min_scale_y_px = $this->translate_y_coord($this->min_scale_y);
		
		imageline($this->img, 
			$this->translate_x_coord($this->min_scale_x), 
			$this->y0px, $max_scale_x_px,
			$this->y0px, $this->colors["axis_line"]);
		if ($this->max_scale_x % $this->xinterval) {
			$last_mark = $this->max_scale_x - 
				($this->max_scale_x % $this->xinterval);
		} else 
			$last_mark = $this->max_scale_x;
		if (!$this->x_numeric)
			$last_mark = array_search($last_mark, $this->xlabels);
		if ($this->min_scale_x % $this->xinterval) {
			$first_mark = $this->min_scale_x + 
				($this->min_scale_x % $this->xinterval);
		} else 
			$first_mark = $this->min_scale_x;
			
		$half_mark_dist = round($this->x_marker_distance / 2);
		if (is_array($this->xlabels))
			$this->log("xlabels are: ".implode(",", $this->xlabels));
		$this->log("last mark: $last_mark; first mark: $first_mark");
		for ($i = $last_mark; $i >= $first_mark; $i-=$this->xinterval) {
			$this->log("x translation is: ".$this->xtranspx."px per y unit");
			$this->log("making x marking at value $i");
			$this->log("xlabel[$i] is ".$this->xlabels[$i]);
			$label = ($this->x_numeric)?$i:$this->xlabels[$i];
			$this->log("doing label ($i): $label");
			$x = $this->translate_x_coord($label); 
			// gridline
			if (($i == 0) && ($this->x_numeric)) {
				$this->log("not drawing numeric x value at 0");
				continue; // no need to draw grid on x0
			}
			if ($i != 0) 
				imageline($this->img, $x, $min_scale_y_px - 2, $x,	
					$max_scale_y_px, $this->colors["grid"]);
			$this->log("i is: $i");
			$this->log(" -- making x marking at value $x");
			imageline($this->img, $x, $this->y0px - 1, $x, $this->y0px + 1,
				$this->colors["axis_line"]);
			$this->render_label($x,
				$this->y0px + 1 + $this->options["padding"],
				$this->options["font_axis"], 
				$this->options["font_axis_size"],
				$this->colors["axis_text"], 
				$label, array("y_pos" => "top"));
		}
		if (($this->options["type"] == "bar") 
			|| ($this->options["type"] == "hbar")) {
			// add on outer axis grid
			imageline($this->img, $max_scale_x_px, $min_scale_y_px, 
				$max_scale_x_px, $max_scale_y_px, $this->colors["grid"]);
			
		}
		$this->log("ending x axis render");
		// >>>
	}
/*>>>*/
	function render_vbars() {/*<<<*/
		// work out possible bar width, remembering space between x-points
		$this->log("x_marker_distance is: ".$this->x_marker_distance);
		$this->bar_width = $this->x_marker_distance / 
			max(count($this->series), 1) 
			- (2 * $this->options["padding"]);
		$this->log("bar_width is $bar_width");
		$sidx = 0;
		foreach ($this->series as $sname => $sdata) {
			foreach ($sdata as $x => $y) {
				if ($y < 0) {
					$opts = array("extra top shadow");
				} else {
					$opts = array("no bottom shadow");
				}
				$x_px = $this->translate_x_coord($x)
					+ ($sidx * $this->bar_width);
				$y_px = $this->translate_y_coord($y);
				$this->render_bordered_rect(
					$x_px,
					$y_px,
					$x_px + $this->bar_width,
					$this->y0px,
					$this->ascolors[$sname],
					$this->colors["axis_line"],
					1, 0, $opts);
			}
			$sidx++;
		}
	}
/*>>>*/
	function render_hbars() {/*<<<*/
		// work out possible bar width, remembering space between x-points
		$this->bar_width = ($this->ytranspx / count($this->series))
			- (2 * $this->options["padding"]);
		$sidx = 0;
		foreach ($this->series as $sname => $sdata) {
			foreach ($sdata as $x => $y) {
				$x_px = $this->translate_x_coord($x);
				$y_px = $this->translate_y_coord($y)
					- ($sidx * $this->bar_width);
				$this->render_bordered_rect(
					$this->x0px,
					$y_px,
					$x_px,
					$y_px + $this->bar_width,
					$this->ascolors[$sname],
					$this->colors["axis_line"],
					1, 0, $opts);
			}
			$sidx++;
		}
	}
/*>>>*/
	function render_dots() {/*<<<*/
		// get all the dots!
		// note that dots are toplevel: they can overwrite other things,
		//	but we don't want them overwritten, so we register them.
		foreach ($this->series as $sname => $sdata) {
			foreach ($sdata as $x => $y) {
				$x_px = $this->translate_x_coord($x);
				$y_px = $this->translate_y_coord($y);
				$dot_rad = ceil($this->options["dot_diam"] / 2);
				$this->draw_dot(
					$x_px, $y_px,
					$this->ascolors[$sname],
					$this->colors["axis_line"]);
				// register the dot as an robject
				$this->robjects[] = array(
					"x0" => $x_px - $dot_rad,
					"y0" => $y_px - $dot_rad,
					"x1" => $x_px + $dot_rad,
					"y1" => $y_px + $dot_rad,
					"type" => "dot",
					"value" => "$x,$y",
				);
			}
		}
	}
/*>>>*/
	function render_lines() {/*<<<*/
		foreach ($this->series as $sname => $sdata) {
			if ($this->x_numeric) {
				// need to make sure that the series are all in x-order, 
				//	otherwise we could get a mess of lines.
				// we don't sort non-numeric: assume they come in the correct
				//	order
				$this->log("sorting x data");
				ksort($sdata);
			}
			$at_start = true;
			foreach ($sdata as $x => $y) {
				// rounding of positions is to try make the line more
				//	accurate, especially for a continuous series. Still,
				//	it's not perfect: a 45-degree line has 1px kinks in it.
				//	If you know why, let me know! ;p
				if ($at_start) {
					$last_x_px = round($this->translate_x_coord($x));
					$last_y_px = round($this->translate_y_coord($y));
					$at_start = false;
					continue;
				}
				$x_px = round($this->translate_x_coord($x));
				$y_px = round($this->translate_y_coord($y));
				// draw the line
				imageline($this->img, $last_x_px, $last_y_px, $x_px, $y_px,
					$this->ascolors[$sname]);
				$last_x = $x;
				$last_y = $y;
				$this->log("line: $last_x, $last_y to $x, $y");
				$this->log(" -- in px: $last_x_px, $last_y_px to $x_px, $y_px");
				$last_x_px = $this->translate_x_coord($x);
				$last_y_px = $this->translate_y_coord($y);
				// register the line as an robject
				$this->robjects[] = array(
					"x0" => $x_px - $dot_rad,
					"y0" => $y_px - $dot_rad,
					"x1" => $x_px + $dot_rad,
					"y1" => $y_px + $dot_rad,
					"type" => "line",
					"value" => "$last_x,$last_y to $x,$y",
				);
			}
		}
	}
/*>>>*/
	function render_pie() {/*<<<*/
		// the pie graph is a bit of a speciality: we can only examine
		//	one series, so we just create an error graphic if there is > 1
		if  (count($this->series) > 1) {
			$this->create_error_graphic("Pie graph can only have one series");
		}
		if (!$this->y_numeric) {
			$this->create_error_graphic("Pie graph must have a numeric y"
				."series");
		}
		if ($this->x_numeric) {
			$this->log("Creating pie graph with numeric x values: may not be"
				."quite what you expected.");
		}
		// tally up total y-value for all points, get x-value names for legend
		$longest_x = "";
		$total_y = 0;
		foreach ($this->series as $sname => $sdata) {
			foreach ($sdata as $x => $y) {
				if (strlen($x) > strlen($longest_x)) $longest_x = $x;
				if ($y < 0) {
					$this->create_error_graphic("Pie graph y-vals must be +ve");
				}
				$total_y += $y;
				$rawdata[$x] = $y;
			}
			break;	// only using a foreach because I don't know the name of
					// the series (not that it really matters)
		}

		$colidx = 0;
		if (!is_array($rawdata)) {
			$this->create_error_graphic("No data to make pie from!");
		}
		$piedata = array();
		foreach ($rawdata as $x => $y) {
			// get angle of slice;
			if (array_key_exists($x, $piedata)) {
				// add to existing value
				$piedata[$x] += (360 * ($y / $total_y));
			} else {
				$piedata[$x] = 360 * ($y / $total_y);
				// allocate color for slice
				if (!array_key_exists($colidx, $this->scolors)) {
					$this->scolors[$colidx] = 
						$this->color_from_components(rand(0, 255), rand(0, 255),
							rand(0, 255));
				}
				$this->ascolors[$x] = 
					$this->allocate_hex_color($this->scolors[$colidx]);
				// darker color for edging
				$dcolors[$x] = $this->darken_to($this->scolors[$colidx], 0.75);
				$adcolors[$x] = $this->allocate_hex_color($dcolors[$x]);
				$colidx++;
			}
		}

		// legend
		$this->render_legend(array_keys($piedata));
		$piew = $this->graph_w * $this->options["pie_wperc"];
		$pieh = $this->graph_h * $this->options["pie_hperc"];
		$pie_thickness = $piew * $this->options["pie_tperc"];
		$piecx = ($this->graph_w / 2) + $this->graph_x0;
		$piecy = ($this->graph_h / 2) + $this->graph_y0
			- ($pie_thickness / 2);
		$this->log("creating pie: center: $piecx,$piecy; w: $piew; h: $pieh; thickness: ".$pie_thickness);
		// build "3d" base
		$angle = 0;
		for ($i = $piecy + $pie_thickness; $i > $piecy; $i--) {
			foreach ($piedata as $name => $value) {
				$endangle = $angle + $value;
				// only need to draw the bottom 1/2 of the circle for this.
				if ($endangle > 180) {
					imagefilledarc($this->img, $piecx, $i, $piew, $pieh, $angle,
						$endangle, $adcolors[$name], IMG_ARC_PIE);
				}
				$angle += $value;
			}
		}
		// top faces
		$angle = 0;
		foreach ($piedata as $name => $value) {
			imagefilledarc($this->img, $piecx, $i, $piew, $pieh, $angle,
				$angle + $value, $this->ascolors[$name], IMG_ARC_PIE);
			$angle += $value;
		}

		// relief effect
		$angle = 0;
		$edge = $this->allocate_hex_color("#ffffff", 115);
		foreach ($piedata as $value) {
			$this->log("drawing relief at $piecx,$piecy, $piew, $pieh, value $value, angle $angle");
			imagefilledarc($this->img, $piecx, $piecy, $piew, $pieh, $angle,
				$angle + $value, $edge, IMG_ARC_NOFILL + IMG_ARC_EDGED);
			$angle += $value;
		}
	}
/*>>>*/
	function render_point_labels() {/*<<<*/
		$mode = 0;
		if ($this->options["label_xval"]) $mode = 1;
		if ($this->options["label_yval"]) $mode += 2;
		$this->log("point label mode is $mode");
		$dot_rad = ceil($this->options["dot_diam"] / 2);
		$done_labels = array();
		$snum = 0;
		if ($mode) {
			foreach ($this->series as $sdata) {
				foreach ($sdata as $x => $y) {
					if (array_search("$x,$y", $done_labels) !== false)
						continue;
					$done_labels[]="$x,$y";
					switch ($mode) {
						case 1: { // x value only
							$lval = $x;
							break;
						}
						case 2: { // y value only
							$lval = $y;
							break;
						}
						case 3: { // both values
							$lval = "($x,$y)";
							break;
						}
					}
					switch ($this->options["type"]) {
						case "hbar": {
						}
						case "bar":	
						case "vbar": {
							if ($y > 0) {
								$y_px = $this->translate_y_coord($y) 
									- $this->options["font_label_size"]
									- $this->options["padding"];
							} else {
								$y_px = $this->translate_y_coord($y) 
									+ $this->options["font_label_size"]
									+ $this->options["padding"];
							}
							$this->render_label(
								$this->translate_x_coord($x) +
									($snum * $this->bar_width) + 
									$this->bar_width / 2,
								$y_px,
								$this->options["font_label"],
								$this->options["font_label_size"],
								$this->colors["point_label_text"],
								$lval);
							break;
						}
						case "dot": {
							$this->render_label(
								$this->translate_x_coord($x),
								$this->translate_y_coord($y),
								$this->options["font_label"],
								$this->options["font_label_size"],
								$this->colors["point_label_text"],
								$lval,
								array(
									"displace" => $dot_rad,
								));
							break;
						}
						case "line": {
							$this->render_label(
								$this->translate_x_coord($x),
								$this->translate_y_coord($y),
								$this->options["font_label"],
								$this->options["font_label_size"],
								$this->colors["point_label_text"],
								$lval);
							break;
						}
					}
				}
				$snum++;
			}
		}
	}
/*>>>*/
	function createurl($scriptbase="") {/*<<<*/
		if ($scriptbase == "") {
			$scriptbase = $this->options["scriptbase"];
		}
		// options
		$url = $scriptbase;
		foreach ($this->options as $idx => $val) {
			switch ($idx) {
				case "scolors": {
					foreach ($val as $idx => $col) {
						$this->tack_arg($url, "sc".$idx, $col);
					}
					break;
				}
				default: {
					$this->tack_arg($url, $idx, $val);
				}
			}
		}
		// series
		$sidx = 0;
		foreach ($this->series as $sname => $sdata) {
			$this->tack_arg($url, "s".$sidx, $sname);
			$pidx = 0;
			foreach ($sdata as $x => $y) {
				$this->tack_arg($url, "x".$sidx."_".$pidx, $x);
				$this->tack_arg($url, "y".$sidx."_".$pidx, $y);
				$pidx++;
			}
			$sidx++;
		}
		if (is_array($this->xlabels)) {
			$labels = implode(",", $this->xlabels);
			$this->tack_arg($url, "xl", $labels);
		}
		if (is_array($this->ylabels)) {
			$labels = implode(",", $this->ylabels);
			$this->tack_arg($url, "yl", $labels);
		}
		return $url;
	}
/*>>>*/
	function tack_arg(&$url, $arg, $val) {/*<<<*/
		$url.= (strpos($url, "?")>0)?"&":"?";
		$url.= urlencode($arg)."=".urlencode($val);
	}
/*>>>*/
	function create_error_graphic($str) {/*<<<*/
		if ($this->options["debug"]) {
			$this->log("Fatal error: ".$str);
			$this->dump_errors();
		} else {
			$img = imagecreatetruecolor(500, 40);
			$colors["red"] = imagecolorallocate($img, 255, 0, 0);
			$colors["black"] = imagecolorallocate($img, 0, 0, 0);
			$colors["drop"] = imagecolorallocatealpha($img, 0, 0, 0, 64);
			imagefill($img, 0, 0, $colors["red"]);
			imagestring($img, 5, 6, 6, "Fatal error:", $colors["drop"]);
			imagestring($img, 5, 5, 5, "Fatal error:", $colors["black"]);
			imageline($img, 5, 20, 110, 20, $colors["black"]);
			imagestring($img, 5, 16, 22, $str, $colors["drop"]);
			imagestring($img, 5, 15, 21, $str, $colors["black"]);
			
			header("Content: image/png");
			imagepng($img);
			imagedestroy($img);
		}
		die(); // quit with the error
	}
/*>>>*/
}
$scriptname = basename($_SERVER["PHP_SELF"]);
switch ($scriptname) {
	case "multigraph.php": {
		// called to render inline
		$foo = new Multigraph();
		$foo->parseurl();
		$foo->render();
		if ($foo->options["debug"]) {
			$foo->dump_errors();
		}
		break;
	}
}
?>
