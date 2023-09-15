<?php
namespace App\Console\Commands;
use App\Models\Project;
use App\Models\Message;
use App\Services\Media;
use Illuminate\Console\Command;

class UpdateQuote extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'update:quote';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Updates the state of a quote';


  protected $salesforceFunctions;
  protected $instance_url;
  protected $access_token;

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();

    // $options = [
    //   'grant_type' => 'password',
    //   'client_id' => config('salesforce.consumer_key'),
    //   'client_secret' => config('salesforce.consumer_secret'),
    //   'username' => config('salesforce.username'),
    //   'password' => config('salesforce.password') . config('salesforce.secret')
    // ];
    
    // $salesforce = new \EHAERER\Salesforce\Authentication\PasswordAuthentication($options);
    // $salesforce->setEndpoint(config('salesforce.login_url'));
    // $salesforce->authenticate();

    // $this->access_token = $salesforce->getAccessToken();
    // $this->instance_url = $salesforce->getInstanceUrl();
    // $this->salesforceFunctions = new \EHAERER\Salesforce\SalesforceFunctions($this->instance_url, $this->access_token, "v52.0");
  }

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    $quoteId = $this->ask('What is the quote ID?');
    $state  = $this->ask('What is the new state? [Accepted, Draft]');
    $this->salesforceFunctions->update('Quote', $quoteId, ['Status' => $state]);

  }
}
