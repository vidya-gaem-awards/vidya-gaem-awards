<?php
namespace App\Service;

use App\Command\ResultsCommand;
use TiBeN\CrontabManager\CrontabAdapter;
use TiBeN\CrontabManager\CrontabJob;
use TiBeN\CrontabManager\CrontabRepository;

class CronJobService
{
    private string $commandLine;
    private CrontabRepository $crontab;
    private CrontabJob $job;
    private bool $available;

    public function __construct(string $projectDir, bool $enabled)
    {
        $this->available = $enabled;
        if (!$enabled) {
            return;
        }

        $this->crontab = new CrontabRepository(new CrontabAdapter());

        $phpVersion = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
        $this->commandLine = 'php' . $phpVersion . '-sp ' . $projectDir . '/bin/console ' . ResultsCommand::COMMAND_NAME;

        /** @var CrontabJob $job */
        foreach ($this->crontab->getJobs() as $job) {
            if ($job->taskCommandLine === $this->commandLine) {
                $this->job = $job;
                break;
            }
        }
    }

    public function isCronJobAvailable(): bool
    {
        return $this->available;
    }

    public function isCronJobEnabled(): bool
    {
        return $this->job && $this->job->enabled;
    }

    /**
     * Enable the cron job. If it doesn't already exist, it will be added to the crontab.
     */
    public function enableCronJob(): void
    {
        if ($this->job) {
            $this->job->enabled = true;
            $this->crontab->persist();
            return;
        }

        // Install the job if it doesn't yet exist
        $job = new CrontabJob();
        $job->hours = $job->dayOfMonth = $job->months = $job->dayOfWeek = '*';
        $job->minutes = '*/30';
        $job->taskCommandLine = $this->commandLine;
        $this->crontab->addJob($job);
        $this->crontab->persist();

        $this->job = $job;
    }

    public function disableCronJob(): void
    {
        if (!$this->job) {
            return;
        }

        $this->job->enabled = false;
        $this->crontab->persist();
    }

    /**
     * Remove the cron job completely from the cron tab.
     */
    public function removeCronJob(): void
    {
        if (!$this->job) {
            return;
        }

        $this->crontab->removeJob($this->job);
        $this->crontab->persist();
    }
}
