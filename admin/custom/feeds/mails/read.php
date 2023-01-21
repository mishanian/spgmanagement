<?
include 'composer/autoload_real.php';
use Ddeboer\Imap\Server;
use Ddeboer\Imap\SearchExpression;
use Ddeboer\Imap\Search\Email\To;
use Ddeboer\Imap\Search\Text\Body;

$server = new Server('mail.spg-canada.com');

// $connection is instance of \Ddeboer\Imap\Connection
$connection = $server->authenticate('info@spg-canada.com', '.D!*8~dCE^6;');

$mailboxes = $connection->getMailboxes();

foreach ($mailboxes as $mailbox) {
    // $mailbox is instance of \Ddeboer\Imap\Mailbox
    printf('Mailbox %s has %s messages', $mailbox->getName(), $mailbox->count());
}

$mailbox = $connection->getMailbox('INBOX');

$messages = $mailbox->getMessages();

foreach ($messages as $message) {
    // $message is instance of \Ddeboer\Imap\Message
 echo   $message->getNumber()."<br>";
    echo   $message->getId()."<br>";


    echo $message->getSubject()."<br>";
    echo $message->getFrom()."<br>";
    echo $message->getTo()."<br>";
    echo $message->getDate()."<br>";
    echo $message->isAnswered()."<br>";
    echo $message->isDeleted()."<br>";
    echo $message->isDraft()."<br>";
    echo $message->isSeen()."<br>";

    echo  $message->getHeaders()."<br>";


    echo  $message->getBodyHtml()."<br>";
    echo  $message->getBodyText()."<br>";
    echo $message->keepUnseen()->getBodyHtml()."<br>";
}
?>