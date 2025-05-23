<?php

namespace AlAya\Common\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Question\Question;

#[AsCommand(
    name: 'make:bundle',
    description: '',
)]
class BundleMakerCommand extends Command
{

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');

        // Step 1: Ask for the path
        $question = new Question('Enter the folder inside src where the bundle should be created : ');
        $bundlePath = $helper->ask($input, $output, $question);

        // Step 2: Ask for the bundle name
        $question = new Question('Enter the name of the bundle (e.g., UserBundle): ');
        $bundleName = $helper->ask($input, $output, $question);

        // Validate inputs
        if (!$bundlePath || !$bundleName) {
            $io->error('Both path and bundle name are required.');
            return Command::FAILURE;
        }

        // Get the last folder in the path
        $fullBundleName = $bundlePath . $bundleName; // Example: AgentUserBundle

        // Define full path
        $fullPath = "src/" . $bundlePath . '/' . $bundleName;
        $filesystem = new Filesystem();

        // Create directory structure
        $filesystem->mkdir([$fullPath . '/Controller', $fullPath . '/templates']);

        // Create Bundle PHP file
        $bundleClassContent = <<<PHP
        <?php

        namespace AlAya\\$bundlePath\\$bundleName;

        use Symfony\Component\HttpKernel\Bundle\Bundle;

        class $fullBundleName extends Bundle
        {
        }
        PHP;

        $filesystem->dumpFile("$fullPath/$fullBundleName.php", $bundleClassContent);

        // Create a sample Controller
        $controllerContent = <<<PHP
        <?php

        namespace $bundlePath\\$bundleName\Controller;

        use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
        use Symfony\Component\HttpFoundation\Response;
        use Symfony\Component\Routing\Annotation\Route;

        class DefaultController extends AbstractController
        {
            #[Route('/$bundleName', name: '${bundleName}_index')]
            public function index(): Response
            {
                return new Response("<h1>Welcome to $fullBundleName!</h1>");
            }
        }
        PHP;

        $filesystem->dumpFile("$fullPath/Controller/DefaultController.php", $controllerContent);

        // Register the bundle in config/bundles.php
        $bundlesFile = 'config/bundles.php';
        $bundleRegistration = "    AlAya\\$bundlePath\\$bundleName\\$fullBundleName::class => ['all' => true],";

        if (file_exists($bundlesFile)) {
            $content = file_get_contents($bundlesFile);
            if (!str_contains($content, $bundleRegistration)) {
                $content = str_replace("];", "$bundleRegistration\n];", $content);
                file_put_contents($bundlesFile, $content);
            }
        }

        $io->success("Bundle '$fullBundleName' created successfully in '$bundlePath' and registered in 'config/bundles.php'.");

        return Command::SUCCESS;
    }
}