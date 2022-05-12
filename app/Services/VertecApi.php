<?php
namespace App\Services;
use Illuminate\Http\Request;
use App\Models\Project;

class VertecApi
{ 
  protected $username = 'deiters';
  protected $password = '.heubergerweg8';
  protected $auth_url = 'https://nightnurse.vertec-mobile.com/auth/xml';
  protected $xml_url  = 'https://nightnurse.vertec-mobile.com/xml';
  private $token;

  public function __construct()
  {
    $this->authorize();
  }

  public function authorize()
  {
    $authxml = '&vertec_username='.$this->username.'&password='.$this->password;
    $authcurl = curl_init($this->auth_url);
    curl_setopt ($authcurl, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));
    curl_setopt($authcurl, CURLOPT_POST, true);
    curl_setopt($authcurl, CURLOPT_POSTFIELDS, $authxml);
    curl_setopt($authcurl, CURLOPT_RETURNTRANSFER, true);
    $token = curl_exec($authcurl);
    
    if(curl_errno($authcurl))
    {
      throw new Exception(curl_error($authcurl));
    }
    curl_close($authcurl);
    $this->setToken($token);
    return $token;
  }

  public function updateProject(Project $project)
  {
    $project = Project::with('manager')->findOrFail($project->id);

    $xml  = "<Envelope>";
    $xml .= "<Header><BasicAuth><Token>".$this->getToken()."</Token></BasicAuth></Header>";
    $xml .= "<Body>";
    $xml .= "<Update><Projektphase>";
    $xml .= "<objref>{$project->vertec_id}</objref>";

    if ($project->date_start)
    {
      $xml .= "<xstartdatum>{$project->date_start}</xstartdatum>";
    }
    else
    {
      $xml .= "<xstartdatum/>";
    }
    if ($project->date_end)
    {
      $xml .= "<xenddatum>{$project->date_end}</xenddatum>";
    }
    else
    {
      $xml .= "<xenddatum/>";
    }
    $xml .= "<verantwortlicher><objref>{$project->manager->vertec_id}</objref></verantwortlicher>";
    $xml .= "</Projektphase></Update>";
    $xml .= "</Body>";
    $xml .= "</Envelope>";

    $curl = curl_init($this->xml_url);
    curl_setopt ($curl, CURLOPT_HTTPHEADER, array("Content-Type: text/xml","Expect:"));
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $xml);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($curl);
    
    if(curl_errno($curl))
    {
      throw new Exception(curl_error($curl));
    }
    curl_close($curl);
  }

  public function setToken($token = NULL)
  {
    $this->token = $token;
  }

  public function getToken()
  {
    return $this->token;
  }

}


