<?php

declare(strict_types=1);

namespace YumemiInc\IntellijProfiles\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('install')]
class Install extends Command
{
    protected function configure(): void
    {
        $this
            ->addOption('use', 'u', InputOption::VALUE_NONE, 'Sets the installed profiles as project default.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->definitions() as $source => $target) {
            $source = __DIR__ . '/../../resources/' . $source;
            $target = getcwd() . '/' . $target;

            if (file_exists($target) && !$io->confirm("File '$target' already exists. Overwrite?", false)) {
                continue;
            }

            file_put_contents($target, file_get_contents($source));
        }

        if ($input->getOption('use')) {
            file_put_contents(
                getcwd() . '/.idea/inspectionProfiles/profiles_settings.xml',
                trim(
                    <<<'EOD'
<component name="InspectionProjectProfileManager">
  <settings>
    <option name="PROJECT_PROFILE" value="yumemi-inc/php-intellij-profiles" />
    <version value="1.0" />
  </settings>
</component>
EOD,
                ),
            );
        }

        $io->success('Installed IntelliJ profiles successfully!');

        return 0;
    }

    private function definitions(): array
    {
        return [
            'Profile.xml' => '.idea/inspectionProfiles/yumemi-inc.xml',
            'Scheme.xml' => '.idea/codeStyles/Project.xml',
        ];
    }
}
