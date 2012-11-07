<?php
namespace Bgy\DaemonBundle\Command;

use
    Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
;
use
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Input\InputArgument
;

class DaemonStopCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this
            ->setName('daemon:stop')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $pidFilename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $this->getContainer()->getParameter('bgy_daemon.pid_file');

        if (!file_exists($pidFilename)) {
            $output->writeln(sprintf('<error>No daemon are running</error>'));
            exit(-1);
        }

        if (!is_readable($pidFilename)) {
            $output->writeln(sprintf('<error>Unable to read PID file: "%s"</error>', $pidFilename));
            exit(-2);
        }

        if (!unlink($pidFilename)) {
            $output->writeln(sprintf('<error>Unable to read PID file: "%s"</error>', $pidFilename));
            exit(-2);
        }

        $output->writeln(sprintf('<info>Successfully stoped daemon.</info>'));
    }
}
