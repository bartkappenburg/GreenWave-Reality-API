<?


class GWR extends Tools {
	
	var $config;
		
	function __construct($configVars) {
		
		
		$this->config['username'] = $configVars['username'];
		$this->config['password'] = $configVars['password'];
		$this->config['endpoint'] = $configVars['endpoint'];		
		$this->config['token'] = $this->getToken();

	}
	
	function getToken($value='token') {
	
		#login method
		#$config is the  defined
		#$value is value to return (false = return complete xml) 
			
			
			$xml = "
			
			
				<gip>
					<version>1</version>
					<email>".$this->config['username']."</email>
					<password>".$this->config['password']."</password>
				</gip>
			
			
			";
		
			$result = $this->urlPost($this->buildQuery('GWRLogin', $xml));
			
			if (!$value) {return $result;} else {
			
				$xml_result = new SimpleXMLElement($result);
				
				if (!(string)$xml_result->$value) {
				
					echo "Wrong username/password. Please edit the config variables.";
					die;
				}
				return (string)$xml_result->$value;
			
			}
		
	
	}
	
	function getData($method, $options = false, $output = 'json') {
	
			
		
		global $config;
		
		#convert the array of options to xml tags
		$xml_options = $this->constructOptions($options);
				
		$xml = "
		
		<gip>
			<version>1</version>
			<token>".$this->config['token']."</token>
			$xml_options
		</gip>
	
		
		";
		
		$result = $this->urlPost($this->buildQuery($method, $xml));
		
		$data = new SimpleXMLElement($result);
		
		#quick fix to make the data from the feeds more readable
		if (isset($options['feed'])) {$data = $this->fixData($data, $options['feed']);}
			
		return $this->returnOutput($data, $output);	
	
	}
	
	
	
	
	


}

class Tools {

	    
			function urlPost($post) {
				
					
				$ch = curl_init();
			
				curl_setopt($ch,CURLOPT_URL, $this->config['endpoint']);
				curl_setopt($ch,CURLOPT_POST, 1);
				curl_setopt($ch,CURLOPT_POSTFIELDS, $post);
				curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
				
				$result = curl_exec($ch);
				curl_close($ch);
				
				return $result;
			} 
			
			
			function buildQuery($function, $xml) {
			
				$query = "cmd=".$function."&data=".urlencode($this->cleanXml($xml));
				return $query;
			
			}
			
			
			function cleanXml($xml) {
			
				$xml = str_replace("\n", "", $xml);
				$xml = str_replace(" ", "", $xml);
				return $xml;
			
			}
		
			
			function fixData($xml, $keys) {
				
				$keys = str_replace(" ", "", $keys);
				$keys = explode(",", $keys);
				
				$json = json_encode($xml);
				$array = json_decode($json, true);
				
				foreach ($keys as $key) {
					
					# weird behaviour: setting option 'alert' gives back 'alerts' (typo on server side?)
					if ($key == 'alert') {$key = 'alerts';}
					
					if (isset($array['chart'][$key]) && !is_array($array['chart'][$key])) {
					
						$values = explode(",",$array['chart'][$key]);
						
						$j = 0;
						for ($i=0; $i < count($values); $i += 2) {
							
							$new_array[$j]["timestamp"] = $values[$i]; 	
							$new_array[$j]["value"] = $values[$i+1];
							$j++;
						}
						$array['chart'][$key] = $new_array;
					}
					
				
				}
					
				return $array;
			
			}
			
			function returnOutput($array, $output) {
				
				
				if ($output == 'json') {
				
					return json_encode($array);
				
				} elseif ($output == 'xml') {
				
					return array2xml($array);	
				
				} else {return $array;}
				
			}
			
			
			function array2xml($array, $xml = false){
			    
			    if($xml === false){
			        $xml = new SimpleXMLElement('<root/>');
			    }
			    foreach($array as $key => $value){
			        if(is_array($value)){
			            array2xml($value, $xml->addChild($key));
			        }else{
			            $xml->addChild($key, $value);
			        }
			    }
			    
			    header('Content-type: text/xml');
			    return $xml->asXML();
			    
			}
			
			
			function constructOptions($options) {
				
				$output = '';
				
				if (is_array($options)) {
				
					foreach ($options as $key => $value) {
					
						$output .= "<$key>$value</$key>\n";
					
					}
				
				}
				return $output;
				
			}
			
			
		

}



?>