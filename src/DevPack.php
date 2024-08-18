<?php
namespace Anodio\DevKit;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[\Anodio\Core\Attributes\Command('dev:pack', 'Move package from vendor for development')]
class DevPack extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $vendorDir = BASE_PATH . '/vendor';
        $packageName = $input->getArgument('packageName');
        $composerJsonPath = $vendorDir.'/'.$packageName.'/composer.json';
        $composerJson = json_decode(file_get_contents($composerJsonPath), true);

        $httpGitAddress = $composerJson['extra']['dev-kit']['git']['https'];
        $sshGitAddress = $composerJson['extra']['dev-kit']['git']['ssh'];

        //now lets clone package to another dir
        shell_exec('rm -rf '.BASE_PATH.'/'.$packageName);
        @mkdir(BASE_PATH.'/'.$packageName);
        shell_exec('git clone '.$httpGitAddress.' '.BASE_PATH.'/'.$packageName);
        shell_exec('cd '.BASE_PATH.'/'.$packageName.' && git remote remove origin');
        shell_exec('cd '.BASE_PATH.'/'.$packageName.' && git remote add origin '.$sshGitAddress);
        shell_exec('rm -rf '.$vendorDir.'/'.$packageName);
        symlink('../../'.$packageName, $vendorDir.'/'.$packageName);
        return 0;
    }

    protected function configure()
    {
        $this->addArgument('packageName', InputArgument::REQUIRED, 'package name');
    }
}
