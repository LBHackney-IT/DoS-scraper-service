<?php

namespace App\Jobs;

use App\Plugins\WebPageScraper\WebPageScraperPlugin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Rapide\LaravelQueueKafka\Queue\Jobs\KafkaJob;
use ReflectionException;

class ProcessWebPageScrapeJob extends Job
{
    /**
     * @var array
     */
    protected $webPageScrape;

    /**
     * @var \Laravel\Lumen\Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $webPageScrapers;

    /**
     * Create a new Web Page scraper job instance.
     *
     * @param string $package
     * @param string $operation
     * @param array $parameters
     *
     * @return void
     */
    public function __construct()
    {
        $this->app = app();
        try {
            $webScraperPlugin = new WebPageScraperPlugin($this->app);
            $this->webPageScrapers = $webScraperPlugin->getWebPlugins();
        } catch (ReflectionException $e) {
            return;
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    }

    public function fire(KafkaJob $job)
    {
        $data = $job->payload()['data'];
        if (!is_array($data)) {
            return;
        }
        $package = $data['package'];
        $op = $data['operation'];
        if (!empty($this->webPageScrapers[$package])) {
            $scraper = $this->webPageScrapers[$package];
            $operation = $scraper['operations'][$op];
            $controller = $operation['controller'];
            $this->app->call($controller, $data);
            $job->delete();
        }
        return;
    }

    /**
     * @return array
     */
    public function getWebPageScrapers(): array
    {
        return $this->webPageScrapers;
    }
}
