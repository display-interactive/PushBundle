<?php

namespace Display\PushBundle\Command;

use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PushCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('display:push')
            ->setDescription('command for sending pending push')
            ->addOption('text', 't', InputOption::VALUE_OPTIONAL, 'text for push')
            ->addOption('locale', 'l', InputOption::VALUE_OPTIONAL, 'filter devices by locale')
            ->addOption('os', 'o', InputOption::VALUE_OPTIONAL, 'filter devices by os')
            ->addOption('uid', 'u', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'force device uid')
            ->addOption('app_id', 'a', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'force application id')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'number max of send push', 10000)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Logger $logger */
        $logger = $this->getContainer()->get('logger');

        $lock = sys_get_temp_dir().'/push.lock';
        if (file_exists($lock)) {
            $output->writeln('already running push');
            return;
        }

        file_put_contents($lock, time());

        $output->writeln('starting push');

        try {
            $text = $input->getOption('text');
            $locale = $input->getOption('locale');
            $uids = $input->getOption('uid');
            $appIds = $input->getOption('app_id');
            $os = $input->getOption('os');

            $pm = $this->getContainer()->get('display.push.manager');
            if ($text) {
                $pm->sendMessage($text, $os, $appIds, $locale, $uids);
            } else {
                $pm->sendPendingMessages($input->getOption('limit'));
            }
        } catch (\Exception $e) {
            $logger->addError($e->getMessage());
            $output->writeln($e->getMessage());
            $output->writeln($e->getTraceAsString());

            $now = new \DateTime();
            $message = \Swift_Message::newInstance()
                ->setSubject('SocialSoccer Error on push command - '. $now->format('Y-m-d H:i:s'))
                ->setFrom('florian.roy@display-interactive.com')
                ->setTo($this->getContainer()->getParameter('developper_email'))
                ->setBody($e->getMessage() . PHP_EOL . $e->getTraceAsString())
            ;
            $this->getContainer()->get('mailer')->send($message);
        }

        unlink($lock);
        $output->writeln('ending push');
    }
}