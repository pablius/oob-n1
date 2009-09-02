<?php
  include dirname(__FILE__) . "/class.graph.php";

  class Graph extends CGraph {
    var $line_color = array(39, 78, 120);
    var $line_bgcolor = array(69, 129, 194);

    function Graph() {
      $this->graph_width = $this->graph_padding['left'] + $this->graph_areawidth + $this->graph_padding['right'];
      $this->graph_height = $this->graph_padding['top'] + $this->graph_areaheight + $this->graph_padding['bottom'];
    }
    
    /**
     * Graph::SetLineColor()
     * Sets line color
     **/
    function SetLineColor($red, $green, $blue) {
      $this->line_color = array($red, $green, $blue);
    }

    /**
     * Graph::AddLineColor()
     * Sets line color
     **/
    function AddLineColor($red, $green, $blue) {
      if (!is_array($this->line_color[0])) {
        $this->line_color = array($this->line_color);
      }
      $this->line_color[] = array($red, $green, $blue);
    }

    /**
     * Graph::SetLineBackgroundColor()
     * Sets background color for line (when 3D)
     **/
    function SetLineBackgroundColor($red, $green, $blue) {
      $this->line_bgcolor = array($red, $green, $blue);
    }
    
    /**
     * Graph::AddLineBackgroundColor()
     * Sets line background color
     **/
    function AddLineBackgroundColor($red, $green, $blue) {
      if (!is_array($this->line_bgcolor[0])) {
        $this->line_bgcolor = array($this->line_bgcolor);
      }
      $this->line_bgcolor[] = array($red, $green, $blue);
    }
    
    /**
     * Graph::DrawGraph()
     * Draw all the graph: bg, axis, bars, text.. and output it
     * Optional file parameter turns output to file, and bool on success
     **/
    function DrawGraph($file = "") {
      $this->im = imagecreatetruecolor($this->graph_width, $this->graph_height);

      CGraph::DrawGraph();

      $this->__Draw_LeftBottom_Axis();
      
      $arrki = array_keys($this->data);
      if (is_array($this->data[$arrki[0]])) { // more than 1 line
        if (!is_array($this->line_color)) {
          $this->line_color = array($this->line_color);
        }
        if (!is_array($this->line_bgcolor)) {
          $this->line_bgcolor = array($this->line_bgcolor);
        }
        for ($i = 0; $i < count($arrki); $i++) {
          $this->__AllocateColor("im_line_color",
                                 $this->line_color[$i],
                                 $this->graph_transparencylevel,
                                 $i);
          if ($this->axis_deepness > 0) {
            $this->__AllocateColor("im_line_bgcolor",
                                   $this->line_bgcolor[$i],
                                   $this->graph_transparencylevel,
                                   $i);
          }
          $arrkj = array_keys($this->data[$arrki[$i]]);
          for ($j = 1; $j < count($arrkj); $j++) {
            $this->__DrawLine(array($arrkj[$j - 1],
                                    $arrkj[$j],
                                    $this->data[$arrki[$i]][$arrkj[$j - 1]],
                                    $this->data[$arrki[$i]][$arrkj[$j]]),
                              $this->im_line_color[$i],
                              $this->im_line_bgcolor[$i]);
          }
        }
      } else {
        $this->__AllocateColor("im_line_color",
                               $this->line_color,
                               $this->graph_transparencylevel);
        $this->__AllocateColor("im_line_bgcolor",
                               $this->line_bgcolor,
                               $this->graph_transparencylevel);
        for ($i = 1; $i < count($arrki); $i++) {
          $this->__DrawLine(array($arrki[$i - 1],
                                  $arrki[$i],
                                  $this->data[$arrki[$i - 1]],
                                  $this->data[$arrki[$i]]),
                            $this->im_line_color,
                            $this->im_line_bgcolor);
        }
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
     * Graph::__DrawLine()
     * Draws a line between 2 points
     **/
    function __DrawLine($points, $color, $bgcolor) {
      if (!isset($this->line_unitX) || !isset($this->line_unitY)) {
        $this->line_unitX = ($this->graph_width - $this->graph_padding['left'] - $this->graph_padding['right']) / ($this->axis_maxX - $this->axis_minX);
        $this->line_unitY = $this->graph_areaheight / ($this->axis_maxY - $this->axis_minY);
      }
      $x1 = $this->graph_padding['left'] + floor(($points[0] - $this->axis_minX) * $this->line_unitX);
      $x2 = $this->graph_padding['left'] + floor(($points[1] - $this->axis_minX) * $this->line_unitX);
      $y1 = $this->graph_height - $this->graph_padding['bottom'] - floor(($points[2] - $this->axis_minY) * $this->line_unitY);
      $y2 = $this->graph_height - $this->graph_padding['bottom'] - floor(($points[3] - $this->axis_minY) * $this->line_unitY);
      if ($this->axis_deepness > 0) {
        $this->__DrawPolygon(array($x1, $y1,
                                   $x1 + $this->axis_deepness, $y1 - $this->axis_deepness,
                                   $x2 + $this->axis_deepness, $y2 - $this->axis_deepness,
                                   $x2, $y2),
                             $bgcolor,
                             true);
        $this->__DrawPolygon(array($x1, $y1,
                                   $x1 + $this->axis_deepness, $y1 - $this->axis_deepness,
                                   $x2 + $this->axis_deepness, $y2 - $this->axis_deepness,
                                   $x2, $y2),
                             $color);
      } else {
        imageline($this->im, $x1, $y1, $x2, $y2, $color);
      }
    }

    /**
     * Graph::__LoadLineValues()
     * Loads definitions to line settings
     **/
    function __LoadLineValues($data) {
      foreach ($data as $name => $value) {
        $name = strtolower($name);
        switch ($name) {
          case 'background-color':
            $this->__SetColorToValue("line_bgcolor", $value);
            break;
          case 'color':
            $this->__SetColorToValue("line_color", $value);
            break;
        }
      }
    }
  }
?>
