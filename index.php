<?php
	
	include("config.php");
	include("_includes/sistema.inc.php");

	$sistema	=	new Pescador($database, $config);
	$sistema->PowerOn();



?>