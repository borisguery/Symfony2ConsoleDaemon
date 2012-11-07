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

class DaemonRunCommand extends ContainerAwareCommand
{
    private $caughtSignal;

    public function configure()
    {
        $this
            ->setName('daemon:run')
            ->addOption('detach', 'd', InputOption::VALUE_NONE,     'Detach daemon and run in background')
            ->addOption('sleep',  'w', InputOption::VALUE_REQUIRED, 'Sleep time in seconds in each iteration of the daemon loop', 2)
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $isDetached = (bool) $input->getOption('detach');
        $pidFilename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $this->getContainer()->getParameter('bgy_daemon.pid_file');

        if (file_exists($pidFilename)) {

            $output->writeln(sprintf('<error>Existing PID file found, make sure a daemon is not already running and remove: "%s"</error>', $pidFilename));
            exit(-1);
        }

        if ($isDetached) {
            $STDOUT = fopen('daemon.log', 'w+');
            $output->writeln('<info>Writing result to: "daemon.log"</info>');
        } else {
            $STDOUT = STDOUT;
            $output->writeln('<info>Writing result to: "STDOUT"</info>');
        }

        declare(ticks=1) {
            if ($isDetached) {
                $processId = pcntl_fork();
                if (-1 === $processId) {
                    $output->writeln('<error>The process failed to fork.</error>');
                } elseif ($processId) {
                    $output->writeln('<info>Detaching...</info>');
                    exit(0);
                }

                if (posix_setsid() == -1) {
                    $output->writeln('<error>Unable to detach from the terminal window.</error>');
                }
            } else {
                pcntl_signal(SIGINT, array($this, 'setCaughtSignal'));
            }

            $posixProcessId = posix_getpid();
            if (!is_writable(dirname($pidFilename))) {

                throw new \RuntimeException(sprintf('PID File "%s" is not writable.', $pidFilename));
            }
            $pidFile = fopen($pidFilename, 'w+');
            $output->writeln(sprintf('<info>PID file: "%s"', $pidFilename));
            fwrite($pidFile, $posixProcessId);
            fclose($pidFile);

            while (true) {
                switch ($this->getCaughtSignal()) {
                    case SIGINT:
                        fwrite($STDOUT, "Caught ^C. Interrupting...\n");
                        fclose($STDOUT);
                        unlink($pidFilename);
                        exit(-1);
                }
                if (!file_exists($pidFilename)) {
                    fwrite($STDOUT, "PID file not found. Exiting...\n");
                    fclose($STDOUT);
                    exit(-1);
                }
                sleep($input->getOption('sleep'));
                fwrite($STDOUT, $posixProcessId . ' => ' . date(\DateTime::ISO8601) . "\n");
            }
        }
    }

    protected function setCaughtSignal($sig)
    {
        $this->caughtSignal = $sig;
    }

    protected function getCaughtSignal()
    {
        return $this->caughtSignal;
    }
}
