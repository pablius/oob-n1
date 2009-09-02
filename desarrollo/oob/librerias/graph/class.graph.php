<?php
  class CGraph {
    var $data = array();
    //---------------------------------------------
    var $graph_areaheight = 100;
    var $graph_areawidth = 50;
    var $graph_padding = array('left' => 50, 'top' => 20, 'right'  => 20, 'bottom' => 20);
    var $graph_title = "";
    var $graph_titlefont = 3;
    var $graph_bgcolor = array(255, 255, 255);
    var $graph_bgtransparent = false;
    var $graph_transparencylevel = 0;
    var $graph_borderwidth = 1;
    var $graph_bordercolor = array(218, 218, 239);
    var $graph_titlecolor = array(99, 88, 78);
    //---------------------------------------------
    var $axis_stepX = 1;
    var $axis_stepY = 1;
    var $axis_stepSize = 3;
    var $axis_deepness = 0;
    var $axis_maxX = 0;
    var $axis_minX = 0;
    var $axis_maxY = 0;
    var $axis_minY = 0;
    var $axis_bordercolor = array(99, 88, 78);
    var $axis_bgcolor = array(152, 137, 124);
    var $axis_scalefont = 2;
    var $axis_scalecolor = array(0, 0, 255);
    var $axis_xscalevisible = true;
    var $axis_yscalevisible = true;
    var $axis_gridlines = true;
    var $axis_frontgridlines = true;
    var $axis_positions = array(true, false, false, true);
    var $axis_modeX = 0; // 0=diference between steps 1=number of steps
    var $axis_modeY = 0; // 0=diference between steps 1=number of steps
    //---------------------------------------------
    var $scale_roundX = 1;
    var $scale_roundY = 1;
    var $scale_funX = "";
    var $scale_funY = "";
    //---------------------------------------------
    var $legend_visible = false;
    var $legend_floating = false;
    var $legend_borderwidth = 1;
    var $legend_bgcolor = array(255, 255, 255);
    var $legend_bordercolor = array(0, 0, 0);
    var $legend_width = 0;
    var $legend_height = 0;
    var $legend_padding = 30;
    var $legend_insidepadding = 3;
    var $legend_font = 1;
    var $legend_position = 3; // 1=bottom left | 2=top left | 3=top right | 4=bottom right
    var $legend_color = array(array(0, 0, 255));
    var $legend_data = array("Item 1");

    function CGraph() {
      //nothing @ the moment.. maybe later will set image at startup
    }

    /****************************************************************
                                GRAPH
    ****************************************************************/
    function SetGraphAreaHeight($height) {
      if ($height > 0) {
        $this->graph_areaheight = $height;
        $this->graph_height = $this->graph_padding['top'] + $height + $this->graph_padding['bottom'];
      }
    }
    function SetGraphAreaWidth($width) {
      if ($width > 0) {
        $this->graph_areawidth = $width;
        $this->graph_width = $this->graph_padding['left'] + $width + $this->graph_padding['right'];
      }
    }
    function SetGraphPadding($left, $top, $right, $bottom) {
      $this->graph_padding = array('left' => (int)$left, 'top' => (int)$top, 'right' => (int)$right, 'bottom' => (int)$bottom);
    }
    function SetGraphTitle($title) {
      $this->graph_title = $title;
    }
    function SetGraphTitleFont($font) {
      $this->graph_titlefont = 0;
      switch ($font) {
        case 'x-large': $this->graph_titlefont++;
        case 'large':   $this->graph_titlefont++;
        case 'medium':  $this->graph_titlefont++;
        case 'small':   $this->graph_titlefont++;
        case 'x-small': $this->graph_titlefont++; break;
        default:
          $this->graph_titlefont = $font;
      }
    }
    function SetGraphTitleColor($red, $green, $blue) {
      $this->graph_titlecolor = array($red, $green, $blue);
    }
    function SetGraphBorderColor($red, $green, $blue) {
      $this->graph_bordercolor = array($red, $green, $blue);
    }
    function SetGraphBorderWidth($width = 0) {
      $this->graph_borderwidth = $width;
    }
    function SetGraphBackgroundColor($red, $green, $blue) {
      $this->graph_bgcolor = array($red, $green, $blue);
    }
    function SetGraphBackgroundTransparent($addtransparency = true, $red = 255, $green = 0, $blue = 255) {
      $this->graph_bgcolor = array($red, $green, $blue);
      $this->graph_bgtransparent = ($addtransparency ? true : false);
    }
    function SetGraphTransparency($percent) {
      if ($percent < 0) $percent = 0;
      elseif ($percent > 100) $percent = 127;
      else $percent = $percent * 1.27;
      $this->graph_transparencylevel = $percent;
    }

    /****************************************************************
                                 AXIS
    ****************************************************************/
    function SetAxisBorderColor($red, $green, $blue) {
      $this->axis_bordercolor = array($red, $green, $blue);
    }
    function SetAxisBackgroundColor($red, $green, $blue) {
      $this->axis_bgcolor = array($red, $green, $blue);
    }
    function SetAxisScaleColor($red, $green, $blue) {
      $this->axis_scalecolor = array($red, $green, $blue);
    }
    function SetAxisStepX($step) {
      if ($step > 0) $this->axis_stepX = $step;
    }
    function SetAxisStepY($step) {
      if ($step > 0) $this->axis_stepY = $step;
    }
    function SetAxisStepSize($size) {
      $this->axis_stepSize = (int) $size;
    }
    function SetAxisScaleXVisibility($state) {
      $this->axis_xscalevisible = ($state ? true : false);
    }
    function SetAxisScaleYVisibility($state) {
      $this->axis_yscalevisible = ($state ? true : false);
    }
    function SetAxisModeX($mode) {
      switch ($mode) {
        case '0':
        case 'value':
          $this->axis_modeX = 0; break;
        case '1':
        case 'dynamic':
          $this->axis_modeX = 1; break;
        case '2':
        case 'pixel':
          $this->axis_modeX = 2; break;
      }
    }
    function SetAxisModeY($mode) {
      switch ($mode) {
        case '0':
        case 'value':
          $this->axis_modeY = 0; break;
        case '1':
        case 'dynamic':
          $this->axis_modeY = 1; break;
        case '2':
        case 'pixel':
          $this->axis_modeY = 2; break;
      }
    }
    function SetAxisDeepness($deepness) {
      $this->axis_deepness = (int) $deepness;
    }
    function SetAxisScaleFont($font) {
      $this->axis_scalefont = 0;
      switch ($font) {
        case 'x-large': $this->axis_scalefont++;
        case 'large':   $this->axis_scalefont++;
        case 'medium':  $this->axis_scalefont++;
        case 'small':   $this->axis_scalefont++;
        case 'x-small': $this->axis_scalefont++; break;
        default:
          $this->axis_scalefont = $font;
      }
    }
    function SetAxisPositions($positions) {
      $positions = explode(",", strtolower($positions));
      $this->axis_positions = array(0, 0, 0, 0);
      for ($i = 0; $i < count($positions); $i++) {
        if ($positions[$i][0] == '-') {
          $v = false;
          $positions[$i] = substr($positions[$i], 1);
        } else $v = true;
        switch ($positions[$i]) {
          case 'all':    $this->axis_positions = array($v, $v, $v, $v); break;
          case 'left':   $this->axis_positions[0] = $v; break;
          case 'top':    $this->axis_positions[1] = $v; break;
          case 'right':  $this->axis_positions[2] = $v; break;
          case 'bottom': $this->axis_positions[3] = $v; break;
        }
      }
    }
    function SetAxisGridlines($state) {
      $this->axis_gridlines = ($state ? true : false);
    }
    function SetAxisFrontGridlines($state) {
      $this->axis_frontgridlines = ($state ? true : false);
    }
    /****************************************************************
                                 SCALE
    ****************************************************************/
    function SetScaleRoundX($number) {
      if ($number < 0) $number = 0;
      $this->scale_roundX = (int) $number;
    }
    function SetScaleRoundY($number) {
      if ($number < 0) $number = 0;
      $this->scale_roundY = (int) $number;
    }
    function SetScaleFunctionX($name) {
      $this->scale_funX = $name;
    }
    function SetScaleFunctionY($name) {
      $this->scale_funY = $name;
    }
    
    /****************************************************************
                                LEGEND
    ****************************************************************/
    function SetLegendVisible($visible) {
      $this->legend_visible = ($visible ? true : false);
    }
    function SetLegendFloating($floating) {
      $this->legend_floating = ($floating ? true : false);
    }
    function SetLegendBackgroundColor($red, $green, $blue) {
      $this->legend_bgcolor = array($red, $green, $blue);
    }
    function SetLegendBorderColor($red, $green, $blue) {
      $this->legend_bordercolor = array($red, $green, $blue);
    }
    function SetLegendBorderWidth($width = 0) {
      $this->legend_borderwidth = $width;
    }
    function SetLegendColors($colors) {
      $this->__SetColorToValue("legend_color", $colors);
    }
    function SetLegendPadding($padding = 0) {
      $this->legend_padding = $padding;
    }
    function SetLegendInsidePadding($padding = 0) {
      $this->legend_insidepadding = $padding;
    }
    function SetLegendPosition($position) {
      switch ($position) {
        case 1:
        case 'bottom left':
          $this->legend_position = 1; break;
        case 2:
        case 'top left':
          $this->legend_position = 2; break;
        case 3:
        case 'top right':
          $this->legend_position = 3; break;
        case 4:
        case 'bottom right':
          $this->legend_position = 4; break;

      }
    }
    function SetLegendData($data) {
      if (is_array($data)) {
        $this->legend_data = $data;
      }
    }
    function SetLegentFont($font) {
      $this->legend_font = 0;
      switch ($font) {
        case 'x-large': $this->legend_font++;
        case 'large':   $this->legend_font++;
        case 'medium':  $this->legend_font++;
        case 'small':   $this->legend_font++;
        case 'x-small': $this->legend_font++; break;
        default:
          $this->legend_font = $font;
      }
    }

    /****************************************************************
                                 DATA
    ****************************************************************/
    function SetData($data) {
      if (is_array($data)) {
        $this->data = $data;
      }
    }

    function LoadGraph($path) {
      if (($fp = @fopen($path, "r")) !== false) {
        $content = "";
        while (!feof($fp)) {              // I do not use filesize() here
          $content .= fread($fp, 4096);   // because of remote files. If
        }                                 // there is no problem with them
        fclose($fp);                      // please let me know
        $this->__LoadGraphDefinitions($content);
        return true;
      } else return false;
    }

    function DrawGraph() {
      if ($this->graph_transparencylevel) {
        imagealphablending($this->im, true);
      }
      
      if ($this->legend_visible) {
        $maxlength = 0;
        for ($i = 0; $i < count($this->legend_data); $i++) {
          if (strlen($this->legend_data[$i]) > $maxlength) $maxlength = strlen($this->legend_data[$i]);
        }
        $this->legend_width = ($this->legend_insidepadding * 4) + ($maxlength * imagefontwidth($this->legend_font));

        if (!$this->legend_floating) {
          $this->graph_padding[($this->legend_position < 3 ? 'left' : 'right')] += $this->legend_padding + $this->legend_width;
          $this->graph_areawidth -= ($this->legend_padding + $this->legend_width);
        }
      }

      $this->__PaintBackground();

      $this->__DrawAxis();
    }
    
    function DrawGraph2() {
      if (strlen($this->graph_title)) {
        $this->__AllocateColor("im_graph_titlecolor",
                               $this->graph_titlecolor,
                               $this->graph_transparencylevel);
        $this->__DrawText($this->graph_title,
                          floor($this->graph_width / 2),
                          floor(($this->graph_padding['top'] - $this->axis_deepness - imagefontwidth($this->graph_titlefont)) / 2),
                          $this->im_graph_titlecolor,
                          $this->graph_titlefont,
                          1);
      }

      $this->__DrawLegend();
    }

    function __PaintBackground() {
      $this->__AllocateColor("im_graph_bgcolor",
                             $this->graph_bgcolor,
                             0);
      if ($this->graph_bgtransparent) {
        imagecolortransparent($this->im, $this->im_graph_bgcolor);
      }
      imagefilledrectangle($this->im, 0, 0, $this->graph_width, $this->graph_height, $this->im_graph_bgcolor);
      if ($this->graph_borderwidth) {
        $this->__AllocateColor("im_graph_bordercolor",
                               $this->graph_bordercolor,
                               $this->graph_transparencylevel);
        for ($i = 0; $i < $this->graph_borderwidth; $i++) {
          imagerectangle($this->im,
                         $i,
                         $i,
                         $this->graph_width - 1 - $i,
                         $this->graph_height - 1 - $i,
                         $this->im_graph_bordercolor);
        }
      }
    }

    function __DrawAxis() {
      $this->__AllocateColor("im_axis_bordercolor",
                             $this->axis_bordercolor,
                             $this->graph_transparencylevel);
      $this->__AllocateColor("im_axis_bgcolor",
                             $this->axis_bgcolor,
                             $this->graph_transparencylevel);
      $this->__AllocateColor("im_axis_scalecolor",
                             $this->axis_scalecolor,
                             $this->graph_transparencylevel);

      list($this->axis_minX,
           $this->axis_maxX,
           $this->axis_minY,
           $this->axis_maxY) = $this->__GetMinMaxGraphValue();

      if ($this->axis_gridlines) {
        $style = array($this->im_axis_bordercolor, $this->im_graph_bgcolor);
        imagesetstyle($this->im, $style);
      }

      if ($this->axis_modeX == 1) {
        $this->axis_stepX = ($this->axis_maxX - $this->axis_minX) / $this->axis_stepX;
      } elseif ($this->axis_modeX == 2) {
        $this->axis_stepX = $this->axis_stepX * ($this->axis_maxX - $this->axis_minX) / $this->graph_areawidth;
      }

      if ($this->axis_modeY == 1) {
        $this->axis_stepY = ($this->axis_maxY - $this->axis_minY) / $this->axis_stepY;
      } elseif ($this->axis_modeY == 2) {
        $this->axis_stepY = $this->axis_stepY * ($this->axis_maxY - $this->axis_minY) / $this->graph_areaheight;
        $rest = abs($this->axis_maxY) % $this->axis_stepY;
          // need to center a step on coord 0
          
      }
      
      if (!$this->axis_deepness) {
        $this->axis_frontgridlines = 1;
      }

      if (!$this->axis_xscalevisible) {
        $this->axis_positions[1] = 0;
        $this->axis_positions[3] = 0;
      }
      if (!$this->axis_yscalevisible) {
        $this->axis_positions[0] = 0;
        $this->axis_positions[2] = 0;
      }
      $this->__CorrectMinMax($this->axis_minX, $this->axis_maxX, $this->axis_stepX);
      $this->__CorrectMinMax($this->axis_minY, $this->axis_maxY, $this->axis_stepY);
      if ($this->axis_yscalevisible) {
        $this->__DrawHorizontalGridlines();
      }
      $this->__DrawHorizontalGideGridlines();
      if ($this->axis_xscalevisible) {
        $this->__DrawVerticalGridlines();
      }
      $this->__DrawVerticalGideGridlines();
    }
    
    function __Draw_LeftBottom_Axis() {
      $w = $this->graph_width;
      $h = $this->graph_height;
      $p = $this->graph_padding;
      if ($this->axis_positions[3]) {
        $this->__DrawAxisPart($p['left'], $h - $p['bottom'], $w - $p['right'], $h - $p['bottom'],
                              $this->axis_minX, $this->axis_maxX, $this->axis_stepX,
                              "right", "bottom");
      }
      if ($this->axis_positions[0]) {
        $this->__DrawAxisPart($p['left'], $p['top'], $p['left'], $h - $p['bottom'],
                              $this->axis_minY, $this->axis_maxY, $this->axis_stepY,
                              "up", "left");
      }
    }
    
    function __Draw_TopRight_Axis() {
      $w = $this->graph_width;
      $h = $this->graph_height;
      $p = $this->graph_padding;
      if ($this->axis_positions[1]) {
        $this->__DrawAxisPart($p['left'], $p['top'], $w - $p['right'], $p['top'],
                              $this->axis_minX, $this->axis_maxX, $this->axis_stepX,
                              "right", "top");
      }
      if ($this->axis_positions[2]) {
        $this->__DrawAxisPart($w - $p['right'], $p['top'], $w - $p['right'], $h - $p['bottom'],
                              $this->axis_minY, $this->axis_maxY, $this->axis_stepY,
                              "up", "right");
      }
    }
    
    function __CorrectMinMax(&$min, &$max, &$step) {
      if (($max % $step) != 0) $max += ($step - abs($max % $step));
      if (($min % $step) != 0) $min -= abs($min % $step);
    }
    
    function __DrawHorizontalGridlines() {
      $maxy = $this->graph_height - $this->graph_padding['bottom'];
      $miny = $this->graph_padding['top'];
      $maxx = $this->graph_width - $this->graph_padding['right'];
      $minx = $this->graph_padding['left'];
      $offset = ($maxy - $miny) / ($this->axis_maxY - $this->axis_minY) * $this->axis_stepY;
      $v = $miny + $offset;

      $deep = $this->axis_deepness;
      if (!$deep) $grid_offset = $this->axis_stepSize;
      else $grid_offset = 1;
      while ($v < $maxy) {
        imageline($this->im, $minx + $deep + (!$this->axis_positions[0] ? 0 : $grid_offset),
                             floor($v) - $deep,
                             $maxx + $deep - (!$this->axis_positions[2] ? 0 : $grid_offset),
                             floor($v) - $deep, IMG_COLOR_STYLED);
        $v += $offset;
      }
    }
    
    function __DrawHorizontalGideGridlines() {
      $maxy = $this->graph_height - $this->graph_padding['bottom'];
      $miny = $this->graph_padding['top'];
      $maxx = $this->graph_width - $this->graph_padding['right'];
      $minx = $this->graph_padding['left'];

      $deep = $this->axis_deepness;
      if (!$deep) $grid_offset = $this->axis_stepSize;
      else $grid_offset = 1;
      
      if (!$this->axis_positions[1]) {
        if ($deep) {
          imageline($this->im, $minx + $deep + $grid_offset, $miny - $deep,
                    $maxx + $deep + $grid_offset, $miny - $deep, IMG_COLOR_STYLED);
        }
        if ($this->axis_frontgridlines) {
          imageline($this->im, $minx, $miny, $maxx, $miny, IMG_COLOR_STYLED);
        }
      }
      if (!$this->axis_positions[3]) {
        if ($deep) {
          imageline($this->im, $minx + $deep + $grid_offset, $maxy - $deep,
                    $maxx + $deep + $grid_offset, $maxy - $deep, IMG_COLOR_STYLED);
        }
        if ($this->axis_frontgridlines) {
          imageline($this->im, $minx, $maxy, $maxx, $maxy, IMG_COLOR_STYLED);
        }
      }
    }
    
    function __DrawVerticalGridlines() {
      $maxy = $this->graph_height - $this->graph_padding['bottom'];
      $miny = $this->graph_padding['top'];
      $maxx = $this->graph_width - $this->graph_padding['right'];
      $minx = $this->graph_padding['left'];
      $offset = ($maxx - $minx) / ($this->axis_maxX - $this->axis_minX) * $this->axis_stepX;
      $v = $minx + $offset;

      $deep = $this->axis_deepness;
      if (!$deep) $grid_offset = $this->axis_stepSize;
      else $grid_offset = 1;

      while ($v < $maxx) {
        imageline($this->im, floor($v) + $deep,
                             $miny - $deep + (!$this->axis_positions[1] ? 0 : $grid_offset),
                             floor($v) + $deep,
                             $maxy - $deep - (!$this->axis_positions[3] ? 0 : $grid_offset),
                             IMG_COLOR_STYLED);
        $v += $offset;
      }
    }
    
    function __DrawVerticalGideGridlines() {
      $maxy = $this->graph_height - $this->graph_padding['bottom'];
      $miny = $this->graph_padding['top'];
      $maxx = $this->graph_width - $this->graph_padding['right'];
      $minx = $this->graph_padding['left'];

      $deep = $this->axis_deepness;
      if (!$deep) $grid_offset = $this->axis_stepSize;
      else $grid_offset = 1;
      
      if (!$this->axis_positions[0]) {
        if ($deep) {
          imageline($this->im, $minx + $deep + $grid_offset, $miny - $deep,
                    $minx + $deep + $grid_offset, $maxy - $deep, IMG_COLOR_STYLED);
        }
        if ($this->axis_frontgridlines) {
          imageline($this->im, $minx, $miny, $minx, $maxy, IMG_COLOR_STYLED);
        }
      }
      if (!$this->axis_positions[2]) {
        if ($deep) {
          imageline($this->im, $maxx + $deep + $grid_offset, $miny - $deep,
                    $maxx + $deep + $grid_offset, $maxy - $deep, IMG_COLOR_STYLED);
        }
        if ($this->axis_frontgridlines) {
          imageline($this->im, $maxx, $miny, $maxx, $maxy, IMG_COLOR_STYLED);
        }
      }
    }
    
    function __DrawAxisPart($x1, $y1, $x2, $y2, $scale_start, $scale_end, $scale_step, $scale_direction, $scaletext_side) {
      $deep = $this->axis_deepness;
      if ($deep > 0) {
        $this->__DrawPolygon(array($x1, $y1, $x1 + $deep, $y1 - $deep, $x2 + $deep, $y2 - $deep, $x2, $y2), $this->im_axis_bgcolor, true);
        $this->__DrawPolygon(array($x1, $y1, $x1 + $deep, $y1 - $deep, $x2 + $deep, $y2 - $deep, $x2, $y2), $this->im_axis_bordercolor);
      } else {
        imageline($this->im, $x1, $y1, $x2, $y2, $this->im_axis_bordercolor);
      }
      
      // reverse order if needed
      if ($x1 == $x2) {
        if ($scale_direction == "up") {
          $scale_direction = "down";
          $v = $scale_start;
          $scale_start = $scale_end;
          $scale_end = $v;
          $scale_step = -$scale_step;
        } else $scale_direction = "down";
        if ($scaletext_side != "left") $scaletext_side = "right";
      } else {
        if ($scale_direction == "left") {
          $scale_direction = "right";
          $v = $scale_start;
          $scale_start = $scale_end;
          $scale_end = $v;
          $scale_step = -$scale_step;
        } else $scale_direction = "right";
        if ($scaletext_side != "top") $scaletext_side = "bottom";
      }

      $v = $scale_start;
      $total = $scale_end - $v;
      if ($x1 == $x2) {
        $totalarea = $this->graph_areaheight;
      } else {
        $totalarea = $this->graph_areawidth;
      }
      
      while (($v <= $scale_end && $scale_step > 0) || ($v >= $scale_end && $scale_step < 0)) {
        if ($x1 == $x2) {
          $offset = floor($y1 + (($v - $scale_start) * $totalarea / $total));
          if (strlen($this->scale_funY)) {
            $fun = str_replace("%d", $v, $this->scale_funY);
            eval("\$scale_value = " . $fun . ";");
          } else {
            $scale_value = $this->__RoundNumber($v, $this->scale_roundY);
          }
          // vertical axis scale text
          $this->__DrawText($scale_value, $x1 + ($scaletext_side == "left" ? -6 : 6 + $deep),
                            $offset, $this->im_axis_scalecolor, $this->axis_scalefont,
                            ($scaletext_side == "left" ? 2 : 0), 1);
          if ($v != $scale_start && $v != $scale_end) {
            // vertical axis scale line
            imageline($this->im, $x1 + ($scaletext_side == "left" ? 1 : -1), $offset,
                      $x1 + ($scaletext_side == "left" ? $this->axis_stepSize : -$this->axis_stepSize),
                      $offset, $this->im_axis_bordercolor);
          }
        } else {
          $offset = floor($x1 + (($v - $scale_start) * $totalarea / $total));
          if (function_exists($this->scale_funX)) {
            $fun = $this->scale_funX;
            $scale_value = $fun($v);
          } else {
            $scale_value = $this->__RoundNumber($v, $this->scale_roundX);
          }
          // horizontal axis scale text
          $this->__DrawText($scale_value, $offset, $y1 + ($scaletext_side == "top" ? -6 - $deep : 6),
                            $this->im_axis_scalecolor, $this->axis_scalefont, 1,
                            ($scaletext_side == "top" ? 2 : 0));
          if ($v != $scale_start && $v != $scale_end) {
            // horizontal axis scale line
            imageline($this->im, $offset, $y1 + ($scaletext_side == "top" ? 1 : -1),
                      $offset,
                      $y1 + ($scaletext_side == "top" ? $this->axis_stepSize : -$this->axis_stepSize),
                      $this->im_axis_bordercolor);
          }
        }
        $v += $scale_step;
      }
    }
    
    function __DrawText($text, $x, $y, $color, $size = 1, $align = 0, $valign = 0) {
      /* Align: 0=left | 1=center | 2=right */
      if ($align == 1) $x -= floor(strlen($text) * imagefontwidth($size) / 2);
      elseif ($align == 2) $x -= (strlen($text) * imagefontwidth($size));
      if ($valign == 1) $y -= floor(imagefontheight($size) / 2);
      elseif ($valign == 2) $y -= imagefontheight($size);
      imagestring($this->im, $size, $x, $y, $text, $color);
    }

    function __GetMinMaxGraphValue() {
      $arrki = array_keys($this->data);
      if (is_array($this->data[$arrki[0]])) {
        for ($i = 0; $i < count($arrki); $i++) {
          $arrkj = array_keys($this->data[$arrki[$i]]);
          if ($i == 0) {
            $maxX = $minX = (int) $arrkj[0];
            $maxY = $minY = (int) $this->data[$arrki[0]][$arrkj[0]];
          }
          for ($j = 0; $j < count($arrkj); $j++) {
            if ($arrkj[$j] > $maxX) $maxX = $arrkj[$j];
            elseif ($arrkj[$j] < $minX) $minX = $arrkj[$j];
            if ($this->data[$arrki[$i]][$arrkj[$j]] > $maxY) $maxY = $this->data[$arrki[$i]][$arrkj[$j]];
            elseif ($this->data[$arrki[$i]][$arrkj[$j]] < $minY) $minY = $this->data[$arrki[$i]][$arrkj[$j]];
          }
        }
      } else {
        $maxX = $minX = (int) $arrki[0];
        $maxY = $minY = (int) $this->data[$arrki[0]];
        foreach ($this->data as $x => $y) {
          if ($x > $maxX) $maxX = $x;
          elseif ($x < $minX) $minX = $x;
          if ($y > $maxY) $maxY = $y;
          elseif ($y < $minY) $minY = $y;
        }
      }
      return array($minX, $maxX, $minY, $maxY);
    }

    function __DrawPolygon($points, $color, $filled = false) {
      if ($filled) {
        imagefilledpolygon($this->im, $points, 4, $color);
      } else {
        imagepolygon($this->im, $points, 4, $color);
      }
    }

    function __LoadGraphDefinitions($text) {
      $text = preg_split("/\r?\n/", $text);
      $data = array();
      $section = '';
      for ($i = 0; $i < count($text); $i++) {
        if (preg_match("/^\s*#/", $text[$i])) {
          //ignore.. it's just a comment
        } elseif (preg_match("/^\s*\}\s*/", $text[$i])) {
          $section = '';
        } elseif (preg_match("/^\s*(\w+)\s*\{\s*$/", $text[$i], $r)) {
          $section = $r[1];
          $index = -1;
        } elseif (preg_match("/^\s*\-\s*$/", $text[$i]) && strlen($section)) {
          $index++;
        } else {
          $p = strpos($text[$i], "=");
          if ($p !== false) {
            $k = trim(substr($text[$i], 0, $p));
            $v = trim(substr($text[$i], $p + 1));
            if ($index >= 0) {
              $data[$section][$index][$k] = $v;
            } else {
              if (preg_match("/^\s*\[(.*)\]\s*$/", $v, $r)) {
                // array
                $data[$section][$k] = explode(";", $r[1]);
              } else {
                $data[$section][$k] = $v;
              }
            }
          }
        }
      }
      foreach ($data as $key => $settings) {
        $func = "__Load" . ucfirst($key) . "Values";
        if (method_exists($this, $func)) {
          $this->$func($settings);
        }
      }
      if (is_array($data['data'])) {
        $this->data = $data['data'];
      }
    }

    function __LoadGraphValues($data) {
      foreach ($data as $name => $value) {
        $name = strtolower($name);
        switch ($name) {
          case 'background-color':
            $this->__SetColorToValue("graph_bgcolor", $value);
            break;
          case 'border-color':
            $this->__SetColorToValue("graph_bordercolor", $value);
            break;
          case 'title-color':
            $this->__SetColorToValue("graph_titlecolor", $value);
            break;
          case 'background-transparent':
            $this->graph_bgtransparent = ($value == 1 || $value == 'yes' ? 1 : 0);
            break;
          case 'transparency':
            $this->SetGraphTransparency(str_replace('%', '', $value));
            break;
          case 'title':
            $this->graph_title = $value;
            break;
          case 'title-font':
            $this->SetGraphTitleFont($value);
            break;
          case 'border-width':
            $this->graph_borderwidth = (int) $value;
            break;
          case 'area-height':
            $this->graph_areaheight = (int) $value;
            $this->graph_height = $this->graph_padding['top'] + (int)$value + $this->graph_padding['bottom'];
            break;
          case 'area-width':
            $this->graph_areawidth = (int) $value;
            $this->graph_width = $this->graph_padding['left'] + (int)$value + $this->graph_padding['right'];
            break;
          default:
            if (substr($name, 0, 8) == 'padding-' && strlen($name) > 8) {
              $this->graph_padding[substr($name, 8)] = $value;
            }
        }
      }
    }

    function __LoadAxisValues($data) {
      foreach ($data as $name => $value) {
        $name = strtolower($name);
        switch ($name) {
          case 'x-step':
            $this->SetAxisStepX($value);
            break;
          case 'y-step':
            $this->SetAxisStepY($value);
            break;
          case 'step-size':
            $this->axis_stepSize = (int) $value;
            break;
          case 'x-step-mode':
            $this->SetAxisModeX($value);
            break;
          case 'y-step-mode':
            $this->SetAxisModeY($value);
            break;
          case 'background-color':
          case 'border-color':
          case 'scale-color':
            $this->__SetColorToValue("axis_" . str_replace(array("ackground", "-"),
                                                           array("g", ""),
                                                           $name),
                                     $value);
            break;
          case 'scale-font':
            $this->SetAxisScaleFont($value);
            break;
          case 'show-xscale':
            $this->axis_xscalevisible = ($value == 1 || $value == 'yes' ? 1 : 0);
            break;
          case 'show-yscale':
            $this->axis_yscalevisible = ($value == 1 || $value == 'yes' ? 1 : 0);
            break;
          case 'gridlines':
            $this->axis_gridlines = ($value == 1 || $value == 'yes' ? 1 : 0);
            break;
          case 'position':
            $this->SetAxisPositions($value);
            break;
          case 'deepness':
            $this->axis_deepness = (int) $value;
            break;
        }
      }
    }

    function __LoadScaleValues($data) {
      foreach ($data as $name => $value) {
        $name = strtolower($name);
        switch ($name) {
          case 'x-round':
            $this->SetScaleRoundX($value);
            break;
          case 'y-round':
            $this->SetScaleRoundY($value);
            break;
          case 'x-fun':
            $this->SetScaleFunctionX($value);
            break;
          case 'y-fun':
            $this->SetScaleFunctionY($value);
            break;
        }
      }
    }
    
    function __LoadLegendValues($data) {
      foreach ($data as $name => $value) {
        $name = strtolower($name);
        switch ($name) {
          case 'background-color':
          case 'border-color':
          case 'color':
            $this->__SetColorToValue("legend_" . str_replace(array("ackground", "-"),
                                                             array("g", ""),
                                                             $name),
                                     $value);
            break;
          case 'visible':
            $this->SetLegendVisible($value);
            break;
          case 'floating':
            $this->SetLegendFloating($value);
            break;
          case 'position':
            $this->SetLegendPosition($value);
            break;
          case 'borderwidth':
            $this->SetLegendBorderWidth($value);
            break;
          case 'padding':
            $this->SetLegendPadding($value);
            break;
          case 'inside-padding':
            $this->SetLegendInsidePadding($value);
            break;
          case 'data':
            $this->SetLegendData($value);
            break;
        }
      }
    }

    function __SetColorToValue($varname, $color, $index = false) {
      if (is_array($color)) {
        for ($i = 0; $i < count($color); $i++) {
          $this->__SetColorToValue($varname, $color[$i], $i);
        }
      } else {
        if ($color[0] == "#") { // if it's hex (html format), change to rgb array
          if (strlen($color) == 4) {
            // if only 3 hex values (I assume it's a shade of grey: #ddd)
            $color .= substr($color, -3);
          }
          $color = array(hexdec($color[1].$color[2]),
                         hexdec($color[3].$color[4]),
                         hexdec($color[5].$color[6]));
        }
        if ($index !== false) $this->{$varname}[$index] = $color;
        else $this->$varname = $color;
      }
    }
    
    function __AllocateColor($varname, $color, $alpha, $index = false) {
      if ($index !== false) {
        $this->{$varname}[$index] = imagecolorallocatealpha($this->im, $color[0], $color[1], $color[2], $alpha);
      } else {
        $this->$varname = imagecolorallocatealpha($this->im, $color[0], $color[1], $color[2], $alpha);
      }
    }
    
    function __DrawLegend() {
      if (!$this->legend_visible) return;
      
      $this->legend_height = $this->legend_insidepadding + (count($this->legend_data) * (imagefontheight($this->legend_font) + $this->legend_insidepadding));

      switch ($this->legend_position) {
        case 1:
          $x = $this->legend_padding;
          $y = $this->graph_height - $this->legend_padding - $this->legend_height;
          break;
        case 2:
          $x = $y = $this->legend_padding;
          break;
        case 3:
          $x = $this->graph_width - $this->legend_padding - $this->legend_width;
          $y = $this->legend_padding;
          break;
        case 4:
          $x = $this->graph_width - $this->legend_padding - $this->legend_width;
          $y = $this->graph_height - $this->legend_padding - $this->legend_height;
          break;
      }
      if ($this->legend_floating) {
        $x = $x + ($this->legend_position < 3 ? $this->graph_padding['left'] : -$this->graph_padding['right']);
        $y = $y + ($this->legend_position == 2 || $this->legend_position == 3 ? $this->graph_padding['top'] : -$this->graph_padding['bottom']);
      }
      $this->__AllocateColor("im_legend_bordercolor",
                             $this->legend_bordercolor,
                             $this->graph_transparencylevel);
      $this->__AllocateColor("im_legend_bgcolor",
                             $this->legend_bgcolor,
                             $this->graph_transparencylevel);

      imagefilledrectangle($this->im, $x + 1, $y + 1, $x + $this->legend_width - 1, $y + $this->legend_height - 1, $this->im_legend_bgcolor);
      imagerectangle($this->im, $x, $y, $x + $this->legend_width, $y + $this->legend_height, $this->im_legend_bordercolor);
      for ($i = 0; $i < count($this->legend_data); $i++) {
        $this->__AllocateColor("im_legend_color", $this->legend_color[$i], $this->graph_transparencylevel, $i);
        $this->__DrawLegendItem($x, $y, $i, $this->legend_data[$i], $this->im_legend_color[$i]);
      }
    }
    
    function __DrawLegendItem($legendx, $legendy, $position, $text, $color) {
      $x = $legendx + $this->legend_insidepadding * 3;
      $y = $legendy + $this->legend_insidepadding + (($this->legend_insidepadding + imagefontheight($this->legend_font)) * $position);
      imagefilledrectangle($this->im, $legendx + $this->legend_insidepadding,
                                      $y + ((imagefontheight($this->legend_font) - $this->legend_insidepadding) / 2),
                                      $legendx + $this->legend_insidepadding * 2,
                                      $y + ((imagefontheight($this->legend_font) - $this->legend_insidepadding) / 2) + $this->legend_insidepadding,
                                      $color);
      $this->__DrawText($text, $x, $y, $color, $this->legend_font, 0, 0);
    }
    
    function __RoundNumber($n, $round = 1) {
      if (is_numeric($n)) {
        $weights = " KMG";
        $p = 0;
        while (abs($n) >= 1000) {
          $n = $n / 1000;
          $p++;
        }
        return number_format($n, $round) . trim($weights[$p]);
      } else return $n;
    }
  }
?>
