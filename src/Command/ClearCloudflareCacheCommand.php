<?php
namespace App\Command;

use App\Service\CloudflareService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearCloudflareCacheCommand extends Command
{
    const string COMMAND_NAME = 'app:cloudflare';

    public function __construct(
        private readonly CloudflareService $cloudflare,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(self::COMMAND_NAME);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->cloudflare->isServiceAvailable()) {
            $output->writeln('Cloudflare service is not available.');
            return 1;
        }

        $output->writeln('Clearing Cloudflare cache...');
        $this->cloudflare->purgeCache();
        $output->writeln('Cloudflare cache cleared.');

        return 0;
    }
}
