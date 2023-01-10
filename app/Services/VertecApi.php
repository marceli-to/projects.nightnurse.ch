<?php
namespace App\Services;
use Illuminate\Http\Request;
use App\Models\Project;

class VertecApi
{ 
  protected $auth_url = 'https://nightnurse.vertec-mobile.com/auth/xml';
  protected $xml_url  = 'https://nightnurse.vertec-mobile.com/xml';
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
      $xml .= "<xenddatum>{$project->iso_date_start}</xenddatum>";
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
    $output = curl_exec($curl);
    
    if(curl_errno($curl))
    {
      throw new Exception(curl_error($curl));
    }
    curl_close($curl);

    dd($output);
    return $output;
  }

  public function getToken()
  {
    return env('VERTEC_TOKEN');
  }

}


