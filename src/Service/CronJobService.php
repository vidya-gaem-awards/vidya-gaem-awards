<?php
namespace App\Service;

use App\Command\ResultsCommand;
use TiBeN\CrontabManager\CrontabAdapter;
use TiBeN\CrontabManager\CrontabJob;
use TiBeN\CrontabManager\CrontabRepository;

class CronJobService
{
    /** @var string */
    private $commandLine;

    /** @var CrontabRepository */
    private $crontab;

    /** @var CrontabJob */
    private $job;

    public function __construct(string $projectDir)
    {
        $this->crontab = new CrontabRepository(new CrontabAdapter());
        $this->commandLine = $projectDir . '/bin/console ' . ResultsCommand::COMMAND_NAME;

        /** @var CrontabJob $job */
        foreach ($this->crontab->getJobs() as $job) {
            if ($job->taskCommandLine === $this->commandLine) {
                $this->job = $job;
                break;
            }
        }
    }

    public function isCronJobEnabled()
    {
        return $this->job && $this->job->enabled;
    }

    /**
     * Enable the cron job. If it doesn't already exist, it will be added to the crontab.
     */
    public function enableCronJob()
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

    public function disableCronJob()
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
    public function removeCronJob()
    {
        if (!$this->job) {
            return;
        }

        $this->crontab->removeJob($this->job);
        $this->crontab->persist();
    }
}
