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
        $uuid = explode("+", $recipient);

        if (isset($uuid[1]))
        {
          $uuid = $uuid[1];
          $uuid = explode("@", $uuid)[0];
        }

        $text_body = $message->getTextBody();

        // search for the part '--Reply below this line--' in the message body and extract the text after it
        $reply_text = explode('--Reply below this line--', $text_body);
        $reply_text = $text_body[1];

        // remove all html tags from the reply text
        $reply_text = strip_tags($reply_text);

        dd($reply_text);

        // if $reply_text is empty, delete the message and continue with the next one
        if (empty($reply_text))
        {
          $message->delete();
          continue;
        }
        else
        {
          // get project id from message uuid
          $project_id = Project::where('uuid', $uuid)->first();

          // if there is no project with the given uuid, delete the message and continue with the next one
          if (empty($project_id))
          {
            $message->delete();
            continue;
          }

          // // create new message
          // $new_message = new Message();
          // $new_message->project_id = $project_id;
          // $new_message->sender_id = 1; // TODO: get sender id from message
          // $new_message->subject = $message->getSubject();
          // $new_message->body = $reply_text;
          // $new_message->save();

          // // delete message
          // $message->delete();
        }


        

        // echo $message->getSubject().'<br />';
        // echo 'Attachments: '.$message->getAttachments()->count().'<br />';
        // echo $message->getHTMLBody();

      }
    }
    
    //Get all Mailboxes
    /** @var \Webklex\PHPIMAP\Support\FolderCollection $folders */
    // $folders = $client->getFolders();
    
    //Loop through every Mailbox
    /** @var \Webklex\PHPIMAP\Folder $folder */
    // foreach($folders as $folder) 
    // {
    //   //Get all Messages of the current Mailbox $folder
    //   /** @var \Webklex\PHPIMAP\Support\MessageCollection $messages */
    //   $messages = $folder->messages()->all()->get();

    //   /** @var \Webklex\PHPIMAP\Message $message */
    //   foreach($messages as $message)
    //   {
    //     echo $message->getSubject().'<br />';
    //     echo 'Attachments: '.$message->getAttachments()->count().'<br />';
    //     echo $message->getHTMLBody();

    //     //Move the current Message to 'INBOX.read'
    //     if($message->move('INBOX.read') == true){
    //       echo 'Message has ben moved';
    //     }
    //     else{
    //       echo 'Message could not be moved';
    //     }
    //   }
    // }
  }
}
