<?php

$GRIFFIN_CFG['classpath'] = array(
	GRIFFIN_ROOT.'/core',
	GRIFFIN_WEBAPP.'/app/models',
	GRIFFIN_WEBAPP.'/app/controllers',
	GRIFFIN_WEBAPP.'/app/helpers'
);

if(getenv('TEST') != null) {
	$GRIFFIN_CFG['classpath'][] = GRIFFIN_ROOT.'/tests';
}

?>