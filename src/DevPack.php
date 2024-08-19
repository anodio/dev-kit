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

        $packageExploded = explode('/', $packageName);
        $vendor = $packageExploded[0];
        $packageWithoutVendorName = $packageExploded[1];
        @mkdir(BASE_PATH.'/'.$vendor);
        if (!file_exists(BASE_PATH.'/'.$vendor.'/'.$packageWithoutVendorName)) {
            throw new \Exception('You forgot to clone package to '.BASE_PATH.'/'.$vendor.'/'.$packageWithoutVendorName);
        }
        shell_exec('rm -rf '.$vendorDir.'/'.$packageName);
        symlink('../../'.$packageName, $vendorDir.'/'.$packageName);
        return 0;
    }

    protected function configure()
    {
        $this->addArgument('packageName', InputArgument::REQUIRED, 'package name');
    }
}
