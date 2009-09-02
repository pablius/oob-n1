<?php
  include dirname(__FILE__) . "/class.graph.php";

  class Graph extends CGraph {
    var $bar_width = 32;
    var $bar_height = 8;
    var $bar_padding = 5;
    var $bar_bordercolor = array(39, 78, 120);
    var $bar_bgcolor = array(69, 129, 194);
    var $bar_textfont = 2;

    function Graph() {
      $this->axis_deepness = $this->bar_height;
      $this->graph_height = $this->graph_padding['top'] + $this->graph_areaheight + $this->graph_padding['bottom'];
    }

    /**
     * Graph::SetBarBorderColor()
     * Sets border color for bars
     **/
    function SetBarBorderColor($red, $green, $blue) {
      $this->bar_bordercolor = array($red, $green, $blue);
    }

    /**
     * Graph::SetBarBackgroundColor()
     * Sets background color for bars
     **/
    function SetBarBackgroundColor($red, $green, $blue) {
      $this->bar_bgcolor = array($red, $green, $blue);
    }

    /**
     * Graph::SetBarDimensions()
     * Sets with and height of each bar
     **/
    function SetBarDimensions($width, $height) {
      if ($width > 0) $this->bar_width = $width;
      if ($height >= 0) {
        $this->bar_height = $height;
        $this->axis_deepness = $height;
      }
    }

    /**
     * Graph::SetBarPadding()
     * Sets padding (border) around each bar
     **/
    function SetBarPadding($padding) {
      if ($padding > 0) $this->bar_padding = $padding;
    }
    function SetBarFont($font) {
      $this->bar_textfont = 0;
      switch ($font) {
        case 'x-large': $this->bar_textfont++;
        case 'large':   $this->bar_textfont++;
        case 'medium':  $this->bar_textfont++;
        case 'small':   $this->bar_textfont++;
        case 'x-small': $this->bar_textfont++; break;
        default:
          $this->bar_textfont = $font;
      }
    }
    function DrawGraph($file = "") {
      $this->graph_width = $this->graph_padding['left'] +
                           (count($this->data) * ($this->bar_width + ($this->bar_padding * 2))) +
                           $this->graph_padding['right'];
      $this->axis_deepness = $this->bar_height;

      $this->im = imagecreatetruecolor($this->graph_width, $this->graph_height);

      $this->axis_frontgridlines = 0;
      $this->axis_xscalevisible = 0;
      $this->axis_positions = array($this->axis_positions[0], 0, 0, 0);

      CGraph::DrawGraph();
      
      if ($this->axis_minY > 0) $this->axis_minY = 0;
      if ($this->axis_maxY < 0) $this->axis_maxY = 0;

      $this->__Draw_LeftBottom_Axis();
      
      $p = 0;
      foreach ($this->data as $name => $value) {
        $p++;
        $this->__DrawBarText($p, $name);
        $this->__DrawBar($p, $value);
      }
      
      $this->__Draw_TopRight_Axis();

      CGraph::DrawGraph2();

      if (strlen($file)) {
        $ret = imagepng($this->im, $file);
      } else {
        header("Content-Type: image/png"); // thanks to Marcin G. :)
        imagepng($this->im);
        $ret = true;
      }
      imagedestroy($this->im);
      return $ret;
    }

    /**
     * Graph::__DrawBarText()
     * Determines top and left to draw text to a choosen bar
     **/
    function __DrawBarText($bar, $text) {
      $this->__DrawText($text,
                        $this->graph_padding['left'] + (($this->bar_width + ($this->bar_padding * 2)) * ($bar - 0.5)),
                        $this->graph_height - $this->graph_padding['bottom'] + 1,
                        $this->im_axis_scalecolor,
                        $this->bar_textfont,
                        1);
    }

    /**
     * Graph::__DrawBar()
     * Draws a choosen bar with it's value
     **/
    function __DrawBar($bar, $value) {
      $x = $this->graph_padding['left'] +
           (($this->bar_width + ($this->bar_padding * 2)) * ($bar - 1)) +
           $this->bar_padding;
      $y = $this->graph_areaheight / ($this->axis_maxY - $this->axis_minY);
      if ($value >= 0) {
        $this->____DrawBar($x,
                           $this->graph_height - $this->graph_padding['bottom'] - abs(floor($this->axis_minY * $y)) - floor($y * $value),
                           $x + $this->bar_width,
                           $this->graph_height - $this->graph_padding['bottom'] - abs(floor($this->axis_minY * $y)));
      } else {
        $this->____DrawBar($x,
                           $this->graph_height - $this->graph_padding['bottom'] - abs(floor($this->axis_minY * $y)),
                           $x + $this->bar_width,
                           $this->graph_height - $this->graph_padding['bottom'] - floor($this->axis_minY * -$y) + floor($y * -$value));
      }
    }
    
    /**
     * Graph::____DrawBar()
     * Draws the actual rectangles that form a bar
     **/
    function ____DrawBar($x1, $y1, $x2, $y2) {
      $this->__AllocateColor("im_bar_bordercolor",
                             $this->bar_bordercolor,
                             $this->graph_transparencylevel);
      $this->__AllocateColor("im_bar_bgcolor",
                             $this->bar_bgcolor,
                             $this->graph_transparencylevel);
                             
      // base square
      $this->__DrawPolygon(array($x1, $y2, $x2, $y2, $x2 + $this->bar_height,
                                 $y2 - $this->bar_height, $x1 + $this->bar_height,
                                 $y2 - $this->bar_height),
                           $this->im_bar_bgcolor,
                           true);
      $this->__DrawPolygon(array($x1, $y2, $x2, $y2, $x2 + $this->bar_height,
                                 $y2 - $this->bar_height, $x1 + $this->bar_height,
                                 $y2 - $this->bar_height),
                           $this->im_bar_bordercolor);

      // back square
      $this->__DrawPolygon(array($x1 + $this->bar_height, $y1 - $this->bar_height,
                                 $x2 + $this->bar_height, $y1 - $this->bar_height,
                                 $x2 + $this->bar_height, $y2 - $this->bar_height,
                                 $x1 + $this->bar_height, $y2 - $this->bar_height),
                           $this->im_bar_bgcolor,
                           true);
      $this->__DrawPolygon(array($x1 + $this->bar_height, $y1 - $this->bar_height,
                                 $x2 + $this->bar_height, $y1 - $this->bar_height,
                                 $x2 + $this->bar_height, $y2 - $this->bar_height,
                                 $x1 + $this->bar_height, $y2 - $this->bar_height),
                           $this->im_bar_bordercolor);

      // left square
      $this->__DrawPolygon(array($x1, $y1, $x1 + $this->bar_height, $y1 - $this->bar_height,
                                 $x1 + $this->bar_height, $y2 - $this->bar_height,
                                 $x1, $y2),
                           $this->im_bar_bgcolor,
                           true);
      $this->__DrawPolygon(array($x1, $y1, $x2, $y1, $x2, $y2, $x1, $y2),
                           $this->im_bar_bordercolor);

      // front square
      $this->__DrawPolygon(array($x1, $y1, $x2, $y1, $x2, $y2, $x1, $y2),
                           $this->im_bar_bgcolor,
                           true);
      $this->__DrawPolygon(array($x1, $y1, $x2, $y1, $x2, $y2, $x1, $y2),
                           $this->im_bar_bordercolor);
                           
      // right square
      $this->__DrawPolygon(array($x2, $y2, $x2, $y1, $x2 + $this->bar_height,
                                 $y1 - $this->bar_height, $x2 + $this->bar_height,
                                 $y2 - $this->bar_height),
                           $this->im_bar_bgcolor,
                           true);
      $this->__DrawPolygon(array($x2, $y2, $x2, $y1, $x2 + $this->bar_height,
                                 $y1 - $this->bar_height, $x2 + $this->bar_height,
                                 $y2 - $this->bar_height),
                           $this->im_bar_bordercolor);

      // top square
      $this->__DrawPolygon(array($x1, $y1, $x2, $y1, $x2 + $this->bar_height,
                                 $y1 - $this->bar_height, $x1 + $this->bar_height,
                                 $y1 - $this->bar_height),
                           $this->im_bar_bgcolor,
                           true);
      $this->__DrawPolygon(array($x1, $y1, $x2, $y1, $x2 + $this->bar_height,
                                 $y1 - $this->bar_height, $x1 + $this->bar_height,
                                 $y1 - $this->bar_height),
                           $this->im_bar_bordercolor);
    }
    
    /**
     * Graph::__Load3dbarValues()
     * Loads definitions to 3d bar settings
     **/
    function __Load3dbarValues($data) {
      foreach ($data as $name => $value) {
        $name = strtolower($name);
        switch ($name) {
          case 'background-color':
            $this->__SetColorToValue("bar_bgcolor", $value);
            break;
          case 'border-color':
            $this->__SetColorToValue("bar_bordercolor", $value);
            break;
          case 'padding':
            $this->bar_padding = $value;
            break;
          case 'width':
            $this->bar_width = (int) $value;
            break;
          case 'height':
            $this->bar_height = (int) $value;
            $this->axis_deepness = (int) $value;
            break;
        }
      }
    }
  }
?>
