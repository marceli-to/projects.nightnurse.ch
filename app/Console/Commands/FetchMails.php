<?php
namespace App\Console\Commands;
use App\Models\Project;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Client;
use Illuminate\Console\Command;

class FetchMails extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'fetch:mails';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Fetches emails from the IMAP server and processes them.';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    // Documentation: https://www.php-imap.com/
    $cm = new ClientManager(config_path() . '/imap.php');
    $client = $cm->account('gmail');
    $client->connect();

    $folder = $client->getFolderByName('INBOX');
    $messages = $folder->messages()->all()->get();
    
    if ($messages)
    {
      foreach($messages as $message)
      {
        // get recipient
        $recipient = $message->getAttributes()["to"][0]->mail;

        // get message uuid from recipient (i.e. nni+<uuid>@domain.com)
        $uuid = explode("+", $recipient)[1];
        $uuid = explode("@", $uuid)[0];
        // -> $uuid
        dd($uuid);
      }
    }
    
    //Get all Mailboxes
    /** @var \Webklex\PHPIMAP\Support\FolderCollection $folders */
    $folders = $client->getFolders();

    dd($folders);
    
    //Loop through every Mailbox
    /** @var \Webklex\PHPIMAP\Folder $folder */
    foreach($folders as $folder) 
    {
      //Get all Messages of the current Mailbox $folder
      /** @var \Webklex\PHPIMAP\Support\MessageCollection $messages */
      $messages = $folder->messages()->all()->get();

      /** @var \Webklex\PHPIMAP\Message $message */
      foreach($messages as $message)
      {
        echo $message->getSubject().'<br />';
        echo 'Attachments: '.$message->getAttachments()->count().'<br />';
        echo $message->getHTMLBody();

        //Move the current Message to 'INBOX.read'
        if($message->move('INBOX.read') == true){
          echo 'Message has ben moved';
        }
        else{
          echo 'Message could not be moved';
        }
      }
    }
  }
}
