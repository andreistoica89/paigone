<?php

class EuropabankMpiApi
{
	private $_parameters;
	private $_errorMessage;
	private $_responseUrl;
	
	public function getErrorMessage()
	{
		return 'Error, please contact the webmaster. ('.$this->_errorMessage.')';
	}
	
	public function getResponseUrl()
	{
		return $this->_responseUrl;
	}
	
	/**
	* Constructor
	* @param $parameters: array with all the parameters for this payment transaction. (mpi_url, uid, css, template, title, beneficiary, feedbacktype, feedbackurl,
	* feedbackemail, redirecttype, redirecturl, merchantemail, plugin, customername, country, customeremail, language, brand, orderid, amount, description, serversecret,
	* emailtype, emailfrom, emailtemplate, erroremail, recurringFrequency, recurringExpiry)
	*/
	public function __construct(Array $parameters)
	{
		$this->_parameters = $parameters;
	}
	
	/**
 	* Build and return an XML string based on the parameters supplied at cunstruction
 	* @return XML string
 	*/
	public function buildXml()
	{
		// <MPI Interface>
		$requestXml = new DomDocument('1.0', 'UTF-8');
		$mpiInterface = $requestXml->createElement('MPI_Interface');
		// <Authorize>
		$authorize = $requestXml->createElement('Authorize');
		// <Version> 
		$version = $requestXml->createElement('version');
		$version->appendChild($requestXml->createTextNode("1.1"));
		$authorize->appendChild($version);
		// <Merchant>
		$merchant = $requestXml->createElement('Merchant');
		$uid = $requestXml->createElement('uid');
		$uid->appendChild($requestXml->createTextNode($this->_parameters['uid']));
		$merchant->appendChild($uid);
		if(isset($this->_parameters['css']) && strlen($this->_parameters['css']) > 0)
		{
			$css = $requestXml->createElement('css');
			$css->appendChild($requestXml->createTextNode($this->_parameters['css']));
			$merchant->appendChild($css);
		}
		if(isset($this->_parameters['template']) && strlen($this->_parameters['template']) > 0)
		{
			$template = $requestXml->createElement('template');
			$template->appendChild($requestXml->createTextNode($this->_parameters['template']));
			$merchant->appendChild($template);
		}
		if(isset($this->_parameters['title']) && strlen($this->_parameters['title']) > 0)
		{
			$title = $requestXml->createElement('title');
			$title->appendChild($requestXml->createTextNode($this->_parameters['title']));
			$merchant->appendChild($title);
		}
		$beneficiary = $requestXml->createElement('beneficiary');
		$beneficiaryString = $this->_parameters['beneficiary'];
		if(strlen($beneficiaryString) > 25) {
			$beneficiaryString = substr($beneficiaryString, 0, 25);
		}
		$beneficiary->appendChild($requestXml->createTextNode($beneficiaryString));
		$merchant->appendChild($beneficiary);
		if(isset($this->_parameters['parameters']) && strlen($this->_parameters['parameters']) > 0)
		{
			$parameters = $requestXml->createElement('param');
			$parameters->appendChild($requestXml->createTextNode($this->_parameters['parameters']));
			$merchant->appendChild($parameters);
		}
		if(isset($this->_parameters['customeremail']) && strlen($this->_parameters['customeremail']) > 0 &&
			isset($this->_parameters['merchantemail']) && strlen($this->_parameters['merchantemail']) > 0)
		{
			$mailMerchant = $requestXml->createElement('email');
			$mailMerchant->appendChild($requestXml->createTextNode($this->_parameters['merchantemail']));
			$merchant->appendChild($mailMerchant);
		}
		if(isset($this->_parameters['redirecttype']) && $this->_parameters['redirecttype'] !== 'NOREDIRECT' && $this->_parameters['redirecttype'] !== '')
		{
			$redirecturl = $requestXml->createElement('redirecturl');
			$redirecturl->appendChild($requestXml->createTextNode($this->_parameters['redirecturl']));
			$merchant->appendChild($redirecturl);
			$redirecttype = $requestXml->createElement('redirecttype');
			$redirecttype->appendChild($requestXml->createTextNode($this->_parameters['redirecttype']));
			$merchant->appendChild($redirecttype);
		}
		if(isset($this->_parameters['feedbacktype']) && $this->_parameters['feedbacktype'] !== 'NOFEEDBACK' && $this->_parameters['feedbacktype'] !== '')
		{
			$feedbackurl = $requestXml->createElement('feedbackurl');
			$feedbackurl->appendChild($requestXml->createTextNode($this->_parameters['feedbackurl']));
			$merchant->appendChild($feedbackurl);
			$feedbacktype = $requestXml->createElement('feedbacktype');
			$feedbacktype->appendChild($requestXml->createTextNode($this->_parameters['feedbacktype']));
			$merchant->appendChild($feedbacktype);
		}
		if(isset($this->_parameters['feedbackemail']) && strlen($this->_parameters['feedbackemail']) > 0)
		{
			$feedbackmail = $requestXml->createElement('feedbackemail');
			$feedbackmail->appendChild($requestXml->createTextNode($this->_parameters['feedbackemail']));
			$merchant->appendChild($feedbackmail);
		}
		$plugin = $requestXml->createElement('plugin');
		$plugin->appendChild($requestXml->createTextNode($this->_parameters['plugin']));
		$merchant->appendChild($plugin);
		$authorize->appendChild($merchant);
		// <Customer>
		$customer = $requestXml->createElement('Customer');
		if(isset($this->_parameters['customername']) && strlen($this->_parameters['customername']) > 0)
		{
			$name = $requestXml->createElement('name');
			$nameString = $this->_parameters['customername'];
			if(strlen($nameString) > 40) {
				$nameString = substr($nameString, 0, 40);
			}
			$name->appendChild($requestXml->createTextNode($nameString));
			$customer->appendChild($name);
		}
		if(isset($this->_parameters['country']) && strlen($this->_parameters['country']) > 0)
		{
			$country = $requestXml->createElement('country');
			$country->appendChild($requestXml->createTextNode($this->_parameters['country']));
			$customer->appendChild($country);
		}
		// Try to retrieve users ip-address, even when behind proxy(s)
		foreach(array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key)
		{
			if(array_key_exists($key, $_SERVER) == true)
			{
				foreach (explode(',', $_SERVER[$key]) as $ipString)
				{
					$ipString = trim($ipString);
					if (filter_var($ipString, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) != false)
					{
						$ip = $requestXml->createElement('ip');
						$ip->appendChild($requestXml->createTextNode($ipString));
						$customer->appendChild($ip);
						break 2;
					}
				}
			}
		}
		if(isset($this->_parameters['customeremail']) && strlen($this->_parameters['customeremail']) > 0 &&
			(isset($this->_parameters['merchantemail']) && strlen($this->_parameters['merchantemail']) > 0 ||
			(isset($this->_parameters['emailtype']) && strlen($this->_parameters['emailtype']) > 0 &&
			isset($this->_parameters['emailfrom']) && strlen($this->_parameters['emailfrom']) > 0)))
		{
			$mailCustomer = $requestXml->createElement('email');
			$mailCustomer->appendChild($requestXml->createTextNode($this->_parameters['customeremail']));
			$customer->appendChild($mailCustomer);
		}
		if(isset($this->_parameters['language']) && (strtolower($this->_parameters['language']) === 'nl' || strtolower($this->_parameters['language']) === 'fr' || strtolower($this->_parameters['language']) === 'en' || strtolower($this->_parameters['language']) === 'de'))
		{
			$language = $requestXml->createElement('language');
			$language->appendChild($requestXml->createTextNode($this->_parameters['language']));
			$customer->appendChild($language);
		}
		$authorize->appendChild($customer);
		// <Transaction>
		$transaction = $requestXml->createElement('Transaction');
		if(isset($this->_parameters['brand']) && (strtolower($this->_parameters['brand']) === 'v' || strtolower($this->_parameters['brand']) === 'm' || strtolower($this->_parameters['brand']) === 'a' || strtolower($this->_parameters['brand']) === 'i'))
		{
			$brand = $requestXml->createElement('brand');
			$brand->appendChild($requestXml->createTextNode($this->_parameters['brand']));
			$transaction->appendChild($brand);
		}
		$orderid = $requestXml->createElement('orderid');
		$orderid->appendChild($requestXml->createTextNode($this->_parameters['orderid']));
		$transaction->appendChild($orderid);
		$amount = $requestXml->createElement('amount');
		$amount->appendChild($requestXml->createTextNode(floor($this->_parameters['amount'])));
		$transaction->appendChild($amount);
		$description = $requestXml->createElement('description');
		$descriptionString = $this->_parameters['description'];
		$descriptionString = preg_replace("/[^a-zA-Z0-9\s]/", "", $descriptionString);
		if(strlen($descriptionString) > 125) {
			$descriptionString = substr($descriptionString, 0, 122);
			$descriptionString .= '...';
		}
		$description->appendChild($requestXml->createTextNode($descriptionString));
		$transaction->appendChild($description);
		// <Recurring>
		if(isset($this->_parameters['recurringFrequency']) && isset($this->_parameters['recurringExpiry']))
		{
			$recurring = $requestXml->createElement('Recurring');
			$recurringFrequency = $requestXml->createElement('frequency');
			$recurringFrequency->appendChild($requestXml->createTextNode($this->_parameters['recurringFrequency']));
			$recurring->appendChild($recurringFrequency);
			$recurringExpiry = $requestXml->createElement('expiry');
			$recurringExpiry->appendChild($requestXml->createTextNode($this->_parameters['recurringExpiry']));
			$recurring->appendChild($recurringExpiry);
			$transaction->appendChild($recurring);
		}
		// <Email>
		if(isset($this->_parameters['customeremail']) && strlen($this->_parameters['customeremail']) > 0 &&
			isset($this->_parameters['emailtype']) && strlen($this->_parameters['emailtype']) > 0 &&
			isset($this->_parameters['emailfrom']) && strlen($this->_parameters['emailfrom']) > 0)
		{
			$email = $requestXml->createElement('Email');
			$emailType = $requestXml->createElement('type');
			$emailType->appendChild($requestXml->createTextNode($this->_parameters['emailtype']));
			$email->appendChild($emailType);
			$emailFrom = $requestXml->createElement('from');
			$emailFrom->appendChild($requestXml->createTextNode($this->_parameters['emailfrom']));
			$email->appendChild($emailFrom);
			if(isset($this->_parameters['emailtemplate']) && strlen($this->_parameters['emailtemplate']) > 0)
			{
				$emailTemplate = $requestXml->createElement('template');
				$emailTemplate->appendChild($requestXml->createTextNode($this->_parameters['emailtemplate']));
				$email->appendChild($emailTemplate);
			}
			$transaction->appendChild($email);
		}
		$authorize->appendChild($transaction);
		// <Hash>
		$hash = $requestXml->createElement('hash');
		$hashString = sha1($this->_parameters['uid'].$this->_parameters['orderid'].$this->_parameters['amount'].$descriptionString.$this->_parameters['serversecret']);
		$hash->appendChild($requestXml->createTextNode($hashString));
		$authorize->appendChild($hash);
		$mpiInterface->appendChild($authorize);
		$requestXml->appendChild($mpiInterface);
		return $requestXml->saveXML();
	}
	
	/*
	* Posts an xml string
	* Sets the 
	* @param $xmlString: XML string to be posted
	* @return true if the xmlString was successfully posted and the authorization succeeded (response url in $this->_responseUrl), false otherwise (error message in $this->_errorMessage)
	*/
	public function postXml($xmlString)
	{
		if(extension_loaded("curl"))
		{
			// POST with cURL
			$ch = curl_init($this->_parameters['mpi_url']);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "$xmlString");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$replyXmlString = curl_exec($ch);
			$error = curl_error($ch);
			curl_close($ch);
			if($error)
			{
				$this->logError('cURL error: '.$error);
				return false;
			}
		}
		else
		{
			// POST with fopen
			$ctx = stream_context_create(array('http' => array('method' => 'POST', 'content' => $xmlString, 'header' => 'Content-Type: text/xml')));
			$fp = @fopen($this->_parameters['mpi_url'], 'rb', false, $ctx);
			if(!$fp)
			{
				$this->logError('fopen function not working. Is your hosting blocking fopen?');
				return false;
			}
			else
				$replyXmlString = stream_get_contents($fp);
		}
		$replyXml = simplexml_load_string($replyXmlString);
		if(isset($replyXml->Response->url))
		{
			$this->_responseUrl = (string)$replyXml->Response->url;
			return true;
		}
		else
		{
			if(isset($replyXml->Error->errorDetail) && $replyXml->Error->errorDetail != '')
				$this->logError($replyXml->Error->errorDetail);
			else
				$this->logError($replyXml->Error->errorMessage);
			return false;
		}
	}
	
	/*
	* Log an error message:
	* - Stores the message in the class member $_errorMessage
	* - Logs the message in the php log file
	* - Mails the message to the store admin
	* @param $errorMessage: The error message to be logged
	*/
	private function logError($errorMessage)
	{
		$this->_errorMessage = $errorMessage;
		error_log('Europabank MPI error --> '.$errorMessage, 0);
		if(isset($this->_parameters['erroremail']) && strlen($this->_parameters['erroremail']) > 0)
			mail($this->_parameters['erroremail'], 'Europabank MPI error', $errorMessage);
	}
}