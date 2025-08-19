<?php
namespace App\Services;
use Illuminate\Http\Request;
use App\Models\Project;
use Exception;

class VertecApi
{ 
  protected $auth_url = 'https://vertec.nightnurse.ch/auth/xml';
  protected $xml_url  = 'https://vertec.nightnurse.ch/xml';
  private $token;

  public function updateProject(Project $project)
  {
    $project = Project::with('manager')->findOrFail($project->id);

    $xml  = "<Envelope>";
    $xml .= "<Body>";
    $xml .= "<Update><Projektphase>";
    $xml .= "<objref>{$project->vertec_id}</objref>";

    if ($project->date_start)
    {
      $xml .= "<xstartdatum>{$project->iso_date_start}</xstartdatum>";
    }
    else
    {
      $xml .= "<xstartdatum/>";
    }
    if ($project->date_end)
    {
      $xml .= "<xenddatum>{$project->iso_date_end}</xenddatum>";
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
    curl_setopt ($curl, CURLOPT_HTTPHEADER, array("Content-Type: text/xml", "Expect:", "Authorization: Bearer " . $this->getToken()));
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $xml);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
    $output = curl_exec($curl);
    
    if(curl_errno($curl))
    {
      throw new Exception(curl_error($curl));
    }
    curl_close($curl);
    return $output;
  }

  public function getToken()
  {
    return env('VERTEC_TOKEN');
  }

}


