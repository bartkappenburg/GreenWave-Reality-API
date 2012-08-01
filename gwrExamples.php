<?

include_once('gwrClasses.php');

# fill in your details
$config['username'] = 'email@domain.com'; #login name at energy company
$config['password'] = md5('password'); #md5 hash of your password
$config['endpoint'] = "https://nuon.greenwavereality.com/gwr/gop.php"; #endpoint (change subdomain for other reseller!)

# init the class
$gwr = new GWR($config);

########################## FIRST EXAMPLE ######################################
# This example gets your account details. The output will be a JSON (default) file.

# specify options (see methods.txt for the options per method)
$options = '' # this method has no options

# usage: $gwr->getData("method", $options, $output = "json|array")
$account = $gwr->getData("AccountGetDetails");

#uncomment to see output
//print_r($account);

##############################################################################


########################## SECOND EXAMPLE ######################################
# This example gets the electricity from the last day. The output will be a JSON file with the usages for different (UNIX)-timestamps.

# specify options (see methods.txt for the options per method)
$options = array('period' => 'day', "feed" => 'data, avg', "datatype" => 'el');

# usage: $gwr->getData('method', $options, $output = "json|array")
$usage = $gwr->getData("UserGetchart", $options);

#uncomment to see output
//print_r($usage);

##############################################################################





?>