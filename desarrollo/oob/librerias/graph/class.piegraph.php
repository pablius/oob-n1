<?php
  include dirname(__FILE__) . "/class.graph.php";

  class Graph extends CGraph {
    var $pie_color = array(array(39, 78, 120));
    var $pie_deepnesscolor = array(array(9, 48, 90));
    var $pie_bgcolor = array(array(69, 129, 194));
    var $pie_deepness = 10;
    var $pie_total = 0;
    var $pie_startoffset = 0;
	var $im_pie_deepnesscolor;
	var  $offset;

    function Graph() {
      $this->graph_width = $this->graph_padding['left'] + $this->graph_areawidth + $this->graph_padding['right'];
      $this->graph_height = $this->graph_padding['top'] + $this->graph_areaheight + $this->graph_padding['bottom'];
    }
    
    function SetPieColor($red, $green, $blue) {
      $this->pie_color = array($red, $green, $blue);
    }
    function AddPieColor($red, $green, $blue) {
      if (!is_array($this->pie_color[0])) {
        $this->pie_color = array($this->pie_color);
      }
      $this->pie_color[] = array($red, $green, $blue);
    }
    function SetPieBackgroundColor($red, $green, $blue) {
      $this->pie_bgcolor = array($red, $green, $blue);
    }
    function AddPieBackgroundColor($red, $green, $blue) {
      if (!is_array($this->pie_bgcolor[0])) {
        $this->pie_bgcolor = array($this->pie_bgcolor);
      }
      $this->pie_bgcolor[] = array($red, $green, $blue);
    }
    function SetPieDeepnessColor($red, $green, $blue) {
      $this->pie_deepnesscolor = array($red, $green, $blue);
    }
    function AddPieDeepnessColor($red, $green, $blue) {
      if (!is_array($this->pie_deepnesscolor[0])) {
        $this->pie_deepnesscolor = array($this->pie_deepnesscolor);
      }
      $this->pie_deepnesscolor[] = array($red, $green, $blue);
    }
    function SetPieTotalValue($total) {
      $this->pie_total = $total;
    }
    function SetPieStartOffset($offset) {
      if ($offset < 0 || $offset > 359) $offset = 0;
      $this->pie_startoffset = $offset;
    }
    function SetPieData($data) {
      CGraph::SetData($data);
    }
    function DrawGraph($file = "") {
      $this->im = imagecreatetruecolor($this->graph_width, $this->graph_height);

      $this->axis_positions = array(0, 0, 0, 0);
      $this->axis_xscalevisible = 0;
      $this->axis_yscalevisible = 0;
      $this->axis_gridlines = 0;
      
      CGraph::DrawGraph();
      
      if ($this->pie_total == 0) {
        foreach ($this->data as $name => $value) {
          $this->pie_total += $value;
        }
      }
      // deepness
      for ($i = $this->pie_deepness; $i > 0; $i--) {
       $offset = 0;
        $p = 0;
        foreach ($this->data as $n => $value) {
          if (!$this->im_pie_deepnesscolor[$p]) {
            $this->__AllocateColor("im_pie_deepnesscolor", $this->pie_deepnesscolor[$p], 0, $p);
          }
          $from = round($this->pie_startoffset - ($offset * 360 / $this->pie_total));
          $to = round($this->pie_startoffset - (($value + $offset) * 360 / $this->pie_total));
          if ($from < 0) $from += 360;
          if ($to < 0) $to += 360;
          imagefilledarc($this->im, round($this->graph_width / 2), round($this->graph_height / 2) + $i,
                                    $this->graph_areawidth, $this->graph_areaheight,
                                    $to, $from, $this->im_pie_deepnesscolor[$p], IMG_ARC_PIE);
          $offset += $value;
          $p++;
        }
      }
      $offset = 0;
      $p = 0;
      foreach ($this->data as $n => $value) {
        $this->__AllocateColor("im_pie_color", $this->pie_color[$p], 0, $p);

        $from = round($this->pie_startoffset - ($offset * 360 / $this->pie_total));
        $to = round($this->pie_startoffset - (($value + $offset) * 360 / $this->pie_total));
        if ($from < 0) $from += 360;
        if ($to < 0) $to += 360;
        imagefilledarc($this->im, round($this->graph_width / 2), round($this->graph_height / 2),
                                  $this->graph_areawidth, $this->graph_areaheight,
                                  $to, $from, $this->im_pie_color[$p], IMG_ARC_PIE);
        $offset += $value;
        $p++;
      }

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
    
    function __DrawPieSlice($frompercent, $topercent, $color, $deepcolor) {
      $from = round(270 - ($frompercent * 3.6));
      $to = round(270 - ($topercent * 3.6));
      if ($from < 0) $from += 360;
      if ($to < 0) $to += 360;
      //echo "FROM:" . $from . " TO:" . $to . "<br>\n";
      for ($i = $this->pie_deepness; $i > 0; $i--) {
        imagefilledarc($this->im, round($this->graph_width / 2), round($this->graph_height / 2) + $i,
                                  round($this->graph_areawidth / 2), round($this->graph_areaheight / 2),
                                  $to, $from, $deepcolor, IMG_ARC_PIE);
      }
      imagefilledarc($this->im, round($this->graph_width / 2), round($this->graph_height / 2),
                                round($this->graph_areawidth / 2), round($this->graph_areaheight / 2),
                                $to, $from, $color, IMG_ARC_PIE);
    }

    /**
     * Graph::__LoadPieValues()
     * Loads definitions to pie settings
     **/
    function __LoadPieValues($data) {
      foreach ($data as $name => $value) {
        $name = strtolower($name);
        switch ($name) {
          case 'background-color':
            $this->__SetColorToValue("pie_bgcolor", $value);
            break;
          case 'color':
            $this->__SetColorToValue("pie_color", $value);
            break;
          case 'deepness-color':
            $this->__SetColorToValue("pie_depnesscolor", $value);
            break;
          case 'offset':
            $this->SetPieStartOffset($value);
            break;
          case 'total':
            $this->SetPieTotalValue($value);
        }
      }
    }
  }
?>
