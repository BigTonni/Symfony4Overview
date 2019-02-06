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

    public function __construct(NotificationSender $notification)
    {
        $this->notification = $notification;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Send notifications about new articles in selected categories')
            ->setHelp('This command allows you to send notifications to the user by email.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Send notifications about new articles in selected categories');
        $this->notification->sendNotification();
        $output->writeln('Notifications sent');
    }
}
