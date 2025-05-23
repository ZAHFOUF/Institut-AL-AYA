<?php

namespace AlAya\Common\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'auto:remoteSql',
    description: 'Execute a SQL query remotely via doctrine:query:sql'
)]
class RemoteSqlCommand extends Command
{
    private string $serverIp;
    private string $serverUser;
    private string $serverPass;
    private string $remotePath;
    private string $phpCmd;

    public function __construct(
        private ParameterBagInterface $params
    ) {
        parent::__construct();

        $this->serverIp   = $params->get('server.ip');
        $this->serverUser = $params->get('server.user');
        $this->serverPass = $params->get('server.pass');
        $this->remotePath = $params->get('server.path');
        $this->phpCmd     =  "php8.2-cli"; // e.g., 
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        // Ask user for SQL query
        $question = new Question('Enter your SQL query to run remotely (or press Enter to cancel): ', null);
        $sql = $helper->ask($input, $output, $question);

        if (empty($sql)) {
            $output->writeln('<comment>No query entered. Exiting.</comment>');
            return Command::SUCCESS;
        }

        // Establish SSH connection
        $output->writeln('<info>Connecting to remote server...</info>');
        $conn = ssh2_connect($this->serverIp, 22);

        if (!$conn) {
            $output->writeln('<error>Failed to connect to SSH.</error>');
            return Command::FAILURE;
        }

        if (!ssh2_auth_password($conn, $this->serverUser, $this->serverPass)) {
            $output->writeln('<error>SSH authentication failed.</error>');
            return Command::FAILURE;
        }

        $output->writeln('<info>SSH connected successfully.</info>');

        // Build and execute the remote doctrine SQL command
        $escapedSql = addslashes($sql);
        $remoteCmd = "cd {$this->remotePath} && {$this->phpCmd} bin/console doctrine:query:sql \"$escapedSql\"";

        $output->writeln("<comment>Running: $remoteCmd</comment>");
        $stream = ssh2_exec($conn, $remoteCmd);

        if (!$stream) {
            $output->writeln('<error>Failed to execute command on remote server.</error>');
            return Command::FAILURE;
        }

        stream_set_blocking($stream, true);
        $result = stream_get_contents($stream);
        fclose($stream);

        $output->writeln("<info>Query Output:</info>\n$result");

        return Command::SUCCESS;
    }
}
