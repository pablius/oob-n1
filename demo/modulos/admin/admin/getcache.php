<?php

global $ari;
$ari->popup = 1;
$cache_tab = new admin_session_state();
echo $cache_tab->get_cache();

?>