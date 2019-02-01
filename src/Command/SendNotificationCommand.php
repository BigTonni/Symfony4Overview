<?php

namespace App\Command;

use App\Service\NotificationSender;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendNotificationCommand extends Command
{
    protected static $defaultName = 'app:send-notification';

    private $notification;

    private $mailer;

    public function __construct(NotificationSender $notification, \Swift_Mailer $mailer)
    {
        $this->notification = $notification;
        $this->mailer = $mailer;
        parent::__construct();
    }
    protected function configure(): void
    {
        $this->setDescription('Send notifications')
            ->setHelp('This command allows you to send notifications to the user by email.');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Send Notifications');
//        $this->notification->sendNotification($this->mailer);
        $output->writeln('Notification sent');
    }
}
