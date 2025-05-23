<?php

namespace AlAya\Common\Command;


use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'auto:deploy',
    description: 'Auto deploy the project',
)]
class AutoDeployCommand extends Command
{

    private string $serverIp    ;  // Replace with your server IP
    private string $serverUser ; // Replace with your SSH user
    private string $serverPass ; // Replace with your SSH password (for password auth)
    private string $absolutePath ; // Replace with your active project directory
    private string $deployCmd ;
    private string $composerCmd ;
    


    public function __construct(
        private ParameterBagInterface $parameterBag
    ) {
        $this->serverIp = $this->parameterBag->get('server.ip');
        $this->serverUser = $this->parameterBag->get('server.user');
        $this->serverPass = $this->parameterBag->get('server.pass');
        $this->absolutePath = $this->parameterBag->get('server.path');
        $this->deployCmd = $this->parameterBag->get('server.deployCmd');
        $this->composerCmd = $this->parameterBag->get('server.composer');
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Create SSH connection
        $output->writeln('<info>Connecting to server...</info>');
        $connection = ssh2_connect($this->serverIp, 22);

        if (!$connection) {
            $output->writeln('<error>Failed to connect to SSH.</error>');
            return Command::FAILURE;
        }

        // Authenticate with password
        if (!ssh2_auth_password($connection, $this->serverUser, $this->serverPass)) {
            $output->writeln('<error>SSH Authentication failed.</error>');
            return Command::FAILURE;
        }

        $output->writeln('<info>SSH Authentication successful.</info>');

        // Navigate to active project directory
        $this->executeRemoteCommand($connection, "cd /", $output);



        // Pull latest changes from Git using username & token
        $this->executeRemoteCommand($connection, $this->deployCmd, $output);

        // Ask if we should run composer install
        $output->writeln('<info>Run composer install with autoload</info>');
        $this->executeRemoteCommand($connection,$this->composerCmd, $output);

        // Ask if we should run make:migration
        $output->writeln('<info>Run doctrine migrations...</info>');
        $this->executeRemoteCommand($connection,'rm -rf migrations/*' , $output);
        $this->executeRemoteCommand($connection,'php8.2-cli bin/console make:migration --no-interaction', $output);
        $this->executeRemoteCommand($connection,'php8.2-cli bin/console --no-interaction d:m:m', $output);

        // Clear cache
        $output->writeln('<info>Clearing cache...</info>');
        $this->executeRemoteCommand($connection, 'sudo rm -r /var/www/html/mekezalfahm/var/cache/prod', $output);

        $output->writeln('<info>Deployment completed successfully!</info>');
        return Command::SUCCESS;
    }

    /**
     * Execute a command on the remote server
     */
    private function executeRemoteCommand($connection, string $command, OutputInterface $output)
    {
        $output->writeln("<comment>Executing: $command</comment>");
        $stream = ssh2_exec($connection, "cd $this->absolutePath". " && " . $command);

        if (!$stream) {
            $output->writeln("<error>Command failed: $command</error>");
            return;
        }

        stream_set_blocking($stream, true);
        $response = stream_get_contents($stream);
        fclose($stream);
        
        $output->writeln($response);
    }


}