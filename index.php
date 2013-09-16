<?php
// Format the JSOn so it looks perty!
function format_json($json, $html = false, $tabspaces = null)
    {
        $tabcount = 0;
        $result = '';
        $inquote = false;
        $ignorenext = false;

        if ($html) {
            $tab = str_repeat("&nbsp;", ($tabspaces == null ? 4 : $tabspaces));
            $newline = "<br/>";
        } else {
            $tab = ($tabspaces == null ? "\t" : str_repeat(" ", $tabspaces));
            $newline = "\n";
        }

        for($i = 0; $i < strlen($json); $i++) {
            $char = $json[$i];

            if ($ignorenext) {
                $result .= $char;
                $ignorenext = false;
            } else {
                switch($char) {
                    case ':':
                        $result .= $char . (!$inquote ? " " : "");
                        break;
                    case '{':
                        if (!$inquote) {
                            $tabcount++;
                            $result .= $char . $newline . str_repeat($tab, $tabcount);
                        }
                        else {
                            $result .= $char;
                        }
                        break;
                    case '}':
                        if (!$inquote) {
                            $tabcount--;
                            $result = trim($result) . $newline . str_repeat($tab, $tabcount) . $char;
                        }
                        else {
                            $result .= $char;
                        }
                        break;
                    case ',':
                        if (!$inquote) {
                            $result .= $char . $newline . str_repeat($tab, $tabcount);
                        }
                        else {
                            $result .= $char;
                        }
                        break;
                    case '"':
                        $inquote = !$inquote;
                        $result .= $char;
                        break;
                    case '\\':
                        if ($inquote) $ignorenext = true;
                        $result .= $char;
                        break;
                    default:
                        $result .= $char;
                }
            }
        }

        return $result;
    }
$fieldname = '$t';

if(isset($_REQUEST['sk']))
	{
	$spreadsheetkey = $_REQUEST['sk'];
	} 
else
	{
	// Template located at - https://docs.google.com/spreadsheet/pub?key=0AmRmiTou7vbjdGtqM193ajJuVTFidnNVMU9IRkdtZWc&output=html
	$spreadsheetkey = "0AmRmiTou7vbjdGtqM193ajJuVTFidnNVMU9IRkdtZWc";
	} 	

?>
<h1>Government Services Schema</h1>
<p>This is a basic tool that pulls from a Google Spreadsheet that is setup as a government services template, allowing the editing of groups of services to renders as JSON output.</p>
<form action="index.php" method="get">
	<strong>Google Doc ID:</strong> <input type="text" name="sk" value="<?php echo $spreadsheetkey; ?>" size="75" />
	<input type="submit" name="changekey" value="Change Key" />
</form>
<p>By default this tool uses <a href="https://docs.google.com/spreadsheet/pub?key=0AmRmiTou7vbjdGtqM193ajJuVTFidnNVMU9IRkdtZWc&output=html" target="_blank">this spreadsheet</a>, but you can copy the template and populate with different data, publish publicy, and use the key to generate new JSON output.</p>
<p>Right now this is a prototype, but will be made more flexibile over time.</p>
<?php

// Load Service Worksheet
$serviceurl = 'http://spreadsheets.google.com/feeds/list/' . $spreadsheetkey . '/1/public/values?alt=json';
$servicefile= file_get_contents($serviceurl);
$servicefile = str_replace('gsx$','',$servicefile);
$servicejson = json_decode($servicefile);
$servicerows = $servicejson->{'feed'}->{'entry'};

// Load Provider Worksheet
$providerurl = 'http://spreadsheets.google.com/feeds/list/' . $spreadsheetkey . '/2/public/values?alt=json';
$providerfile= file_get_contents($providerurl);
$providerfile = str_replace('gsx$','',$providerfile);
$providerjson = json_decode($providerfile);
$providerrows = $providerjson->{'feed'}->{'entry'};

// Load Operator Worksheet
$operatorurl = 'http://spreadsheets.google.com/feeds/list/' . $spreadsheetkey . '/3/public/values?alt=json';
$operatorfile= file_get_contents($operatorurl);
$operatorfile = str_replace('gsx$','',$operatorfile);
$operatorjson = json_decode($operatorfile);
$operatorrows = $operatorjson->{'feed'}->{'entry'};

// Load Service Location Worksheet
$servicelocationurl = 'http://spreadsheets.google.com/feeds/list/' . $spreadsheetkey . '/4/public/values?alt=json';
$servicelocationfile= file_get_contents($servicelocationurl);
$servicelocationfile = str_replace('gsx$','',$servicelocationfile);
$servicelocationjson = json_decode($servicelocationfile);
$servicelocationrows = $servicelocationjson->{'feed'}->{'entry'};

// For Each Spreadsheet Row (Separate service definition will be created for each)
foreach($servicerows as $servicerow) 
	{		
	$id = $servicerow->id->$fieldname;
	$updated = $servicerow->updated->$fieldname;
	$title = $servicerow->title->$fieldname;
	
	$servicename = $servicerow->servicename->$fieldname;
	$uniqueid = $servicerow->uniqueid->$fieldname;
	$description = $servicerow->description->$fieldname;
	$url = $servicerow->url->$fieldname;
	$image = $servicerow->image->$fieldname;
	$alternatenames = $servicerow->alternatenames->$fieldname;
	$servicearea = $servicerow->servicearea->$fieldname;
	$provider = $servicerow->provider->$fieldname;
	$operator = $servicerow->operator->$fieldname;
	$servicetype = $servicerow->servicetype->$fieldname;
	$servicetypetaxonomyused = $servicerow->servicetypetaxonomyused->$fieldname;
	$produces = $servicerow->produces->$fieldname;
	$audience = $servicerow->audiencetype->$fieldname;
	$availablelanguages = $servicerow->availablelanguages->$fieldname;
	$serviceurl = $servicerow->serviceurl->$fieldname;
	$servicephone = $servicerow->servicephone->$fieldname;
	
	echo "<br /><strong>" . $servicename . "</strong><br />";
	
	//echo "Unique ID: " . $uniqueid . "<br />";
	//echo "Description: " . $description . "<br />";
	//echo "URL: " . $url . "<br />";
	//echo "Image: " . $image . "<br />";
	//echo "Alternate Names: " . $alternatenames . "<br />";
	//echo "Service Area: " . $servicearea . "<br />";
	//echo "Provider: " . $provider . "<br />";
	//echo "Operator: " . $operator . "<br />";
	//echo "Service Taxonomy: " . $servicetypetaxonomyused . "<br />";
	//echo "Produces: " . $produces . "<br />";
	//echo "Audience: " . $audience . "<br />";
	//echo "Available Languages: " . $availablelanguages . "<br />";
	//echo "Service URL: " . $serviceurl . "<br />";
	//echo "Service Phone: " . $servicephone . "<br />";	
	//echo "<br />";
		
	// Let's build the Schema - yay!
	$Schema = array();
	$F['@context'] = 'http://schema.org/';
	$F['@type'] = 'GovernmentService';
	$F['@id'] = $uniqueid;
	$F['name'] = $servicename;
	
	// Audience
	$audienceArray = explode(";",$audience);
	$A = array();
	$A["@type"] = "CivicAudience";
	$A['audienceType'] = array();
	foreach($audienceArray as $eachAudience)
		{
		array_push($A['audienceType'], $eachAudience);	
		}
	$F['audience'] = $A;		

	$F['Description'] = $description;

	$P = array();
	$P["@type"] = "GovernmentOrganization";
	
	$r = 1;
	foreach($providerrows as $providerrow) 
		{		
		$provider_name = $providerrow->providername->$fieldname;
		$provider_url = $providerrow->url->$fieldname;	
		$provider_logo = $providerrow->logo->$fieldname;
		$provider_service_name = $providerrow->servicename->$fieldname;
				
		if(strpos($provider_service_name, $servicename) !== false)
			{
			//echo "22--Service Name: " . $provider_service_name . " = " . $servicename . "<br />";		
			$P["name"] = $provider_name;
			$P["url"] = $provider_url;
			$P["logo"] = $provider_logo;
			}
	  	$r++;	 		 
		}
	$F['provider'] = $P;		
	
	$SA = array();
	$SA["@type"] = "Country";
	$SA["name"] = "ocd-division/country:us";				
	$F['serviceArea'] = $SA;
	
	
	$F['serviceChannel'] = array();
	$SC = array();
	$SC["@type"] = "ServiceChannel";
	$SC["servicePhone"] = $servicephone;	
	$SC["serviceLocation"] = array();
	$r = 1;
	foreach($servicelocationrows as $servicelocationrow) 
		{
		
		$servicelocation_name = $servicelocationrow->name->$fieldname;
		$servicelocation_addressstreet = $servicelocationrow->addressstreet->$fieldname;
		$servicelocation_addresscity = $servicelocationrow->addresscity->$fieldname;
		$servicelocation_addressstate = $servicelocationrow->addressstate->$fieldname;
		$servicelocation_addresszip = $servicelocationrow->addresszip->$fieldname;
		$servicelocation_service_name = $servicelocationrow->servicename->$fieldname;
						
		//echo "Location Name: " . $servicelocation_name . "<br />";
		//echo "Location Address: " . $servicelocation_addressstreet . "<br />";
		//echo "Location City: " . $servicelocation_addresscity . "<br />";
		//echo "Location State: " . $servicelocation_addressstate . "<br />";
		//echo "Location Zip: " . $servicelocation_addresszip . "<br />";						

		if(strpos($servicelocation_service_name, $servicename) !== false)
			{
			//echo "44--Service Name: " . $servicelocation_service_name . " = " . $servicename . "<br />";	
						
			$SL = array();
			$SL["@type"] = "CivicStructure";	
			$SL["name"] = $servicelocation_name;
			$SL['address'] = array();
			
			$SLA = array();
			$SLA["streetAddress"] = $servicelocation_addressstreet;
			$SLA["addressLocality"] = $servicelocation_addresscity;
			$SLA["addressRegion"] = $servicelocation_addressstate;
			$SLA["postalCode"] = $servicelocation_addresszip;		
				
			array_push($SL['address'], $SLA);	
			
			array_push($SC['serviceLocation'], $SL);	
	
		  	}
		$r++;	
		}		
	array_push($F['serviceChannel'], $SC);			
		
	// ServiceType
	$ST = array();
	$ST["@type"] = "ServiceType";
	$ST['name'] = $servicetype;			
	$ST['serviceTaxonomy'] = $servicetypetaxonomyused;		
	$F['serviceType'] = $ST;	
	
	$Service = format_json(json_encode($F));
	
	?>
	<textarea cols="100" rows="30" style="margin-left: 25px;" /><?php echo $Service; ?></textarea><br /><?php
}
?>