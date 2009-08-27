<?php


$config = SimpleSAML_Configuration::getInstance();
$session = SimpleSAML_Session::getInstance();

$authTokenContactsSP = sha1('ldapstatus:hobbit|' . $config->getValue('secret'));


if (isset($_REQUEST['getToken'])) {
	SimpleSAML_Utilities::requireAdmin();
	echo $authTokenContactsSP; exit;
}




if (!array_key_exists('token', $_REQUEST)) {
	throw new SimpleSAML_Error_BadRequest('Missing authToken.');
}

$token = $_REQUEST['token'];

if ($token !== $authTokenContactsSP) {
	throw new SimpleSAML_Error_Exception('Invalid AuthToken');
}




$ldapconfig = SimpleSAML_Configuration::getConfig('config-login-feide.php');
$ldapStatusConfig = SimpleSAML_Configuration::getConfig('module_ldapstatus.php');

$debug = $ldapconfig->getValue('ldapDebug', FALSE);
$orgs = $ldapconfig->getValue('organizations');
$locationTemplate = $ldapconfig->getValue('locationTemplate');


$isAdmin = FALSE;
$secretURL = NULL;

$ignore = '';
if (array_key_exists('ignore', $_REQUEST)) $ignore = '&ignore=' . $_REQUEST['ignore'];


$secretKey = sha1('ldapstatus|' . $config->getValue('secret') . '|hobbit');
$secretURL = SimpleSAML_Utilities::addURLparameter(
	SimpleSAML_Utilities::selfURLNoQuery(), array(
		'key' => $secretKey,
	)
);

function generateSecret($salt, $orgtest) {
	$secretKey = sha1('ldapstatus|' . $salt . '|' . $orgtest);
	return $secretKey;
}



echo('<pre>');



foreach($orgs AS $orgkey => $org) {
	
	$url = SimpleSAML_Utilities::addURLparameter(
		SimpleSAML_Utilities::selfURLhost() . SimpleSAML_Utilities::getFirstPathElement() . '/module.php/ldapstatus/', array(
			'orgtest' => $orgkey,
			'output' => 'text',
			'key' => generateSecret($config->getValue('secret'), $orgkey)
		)
	);
	
	echo("0.0.0.0 " . $orgkey . " # noconn  cont=sl;" . $url . $ignore . ";OOOKKK\n");
	
}


