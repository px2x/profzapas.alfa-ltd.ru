<?php
# Created By Raymond Irving April, 2005
#::::::::::::::::::::::::::::::::::::::::
# Params:	
#
#	&tpl			- (Optional)
#		Chunk name or document id to use as a template
#				  
#	Note: Templats design:
#			section 1: change pwd template
#			section 2: notification template 
#
# Examples:
#
#	[[WebChangePwd? &tpl=`ChangePwd`]] 

# Set Snippet Paths 
$snipPath  = (($modx->isBackend())? "../":"");
$snipPath .= "assets/snippets/";

# check if inside manager
if ($m = $modx->isBackend()) {
	return ''; # don't go any further when inside manager
}


# Snippet customize settings
$tpl		= isset($tpl)? $tpl:"";

# System settings
$isPostBack		= count($_POST) && isset($_POST['cmdwebchngpwd']);

# Start processing
include_once $snipPath."weblogin/weblogin.common.inc.php";
include_once $snipPath."weblogin/webchangepwd.inc.php";

# Return
return $output;








?>