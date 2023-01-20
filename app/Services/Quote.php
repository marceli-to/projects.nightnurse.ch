<?php
namespace App\Services;
use Illuminate\Http\Request;

class Quote
{
  protected $salesforceFunctions;

  public function __construct()
  {
    $options = [
      'grant_type' => 'password',
      'client_id' => config('salesforce.consumer_key'),
      'client_secret' => config('salesforce.consumer_secret'),
      'username' => config('salesforce.username'),
      'password' => config('salesforce.password') . config('salesforce.secret')
    ];
    
    $salesforce = new \EHAERER\Salesforce\Authentication\PasswordAuthentication($options);
    $salesforce->setEndpoint(config('salesforce.login_url'));
    $salesforce->authenticate();

    $access_token = $salesforce->getAccessToken();
    $instance_url = $salesforce->getInstanceUrl();
    $this->salesforceFunctions = new \EHAERER\Salesforce\SalesforceFunctions($instance_url, $access_token, "v52.0");
  }

  public function get($quoteId = NULL)
  {
    return [
      'quote'      => $this->getQuote($quoteId),
      'quoteItems' => $this->getQuoteLineItemsAndProduct2($quoteId),
    ];
  }

  private function getQuote($quoteId)
  {
    // Get Quote data
    $query = 'SELECT+Fields(all),Contact.name,Contact.firstname,Contact.lastname,Contact.Email_Salutation__c+FROM+Quote+WHERE+ID=\''.$quoteId.'\'+LIMIT+200';
    $data = $this->salesforceFunctions->query(urldecode($query));

    if ($data['totalSize'] > 0 && isset($data['records'][0]))
    {
      $quote = $data['records'][0];

      if (isset($quote['Quote_Detail__c']))
      {
        $quote['Quote_Details__c'] = $this->getQuoteText($quote['Quote_Detail__c']);
      }
      return $quote;
    }
    return $data;

  }

  private function getQuoteLineItemsAndProduct2($quoteId)
  {
    // Get QuoteLineItem and Product2 data
    $query = 'SELECT+Fields(all),Product2.Name,Product2.Description+FROM+ QuoteLineItem+WHERE+QuoteId=\''.$quoteId.'\'+LIMIT+200';
    $data = $this->salesforceFunctions->query(urldecode($query));
    if (isset($data['records']))
    {
      return $data['records'];
    }
    return $data;
  }

  private function getQuoteText($quoteDetailCId)
  {
    $query = 'SELECT+Fields(all)+FROM+Quote_Details__c+WHERE+ID=\''.$quoteDetailCId.'\'+LIMIT+200';
    $data = $this->salesforceFunctions->query(urldecode($query));
    if (isset($data['records'][0]))
    {
      return $data['records'][0];
    }
    return $data;
  }

}