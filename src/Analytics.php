<?php

namespace RodrigoButta\LaravelGoogleAnalytics;

use Carbon\Carbon;
use RodrigoButta\LaravelGoogleAnalytics\Core\Core;
use RodrigoButta\LaravelGoogleAnalytics\Traits\HelperFunctions;
use RodrigoButta\LaravelGoogleAnalytics\Traits\Handlers\SortHandler;
use RodrigoButta\LaravelGoogleAnalytics\Traits\Handlers\DatesHandler;
use RodrigoButta\LaravelGoogleAnalytics\Traits\Handlers\ParamsHandler;
use RodrigoButta\LaravelGoogleAnalytics\Traits\Handlers\FiltersHandler;
use RodrigoButta\LaravelGoogleAnalytics\Traits\Handlers\MetricsHandler;
use RodrigoButta\LaravelGoogleAnalytics\Traits\Handlers\SegmentHandler;
use RodrigoButta\LaravelGoogleAnalytics\Traits\Handlers\DimensionsHandler;
use RodrigoButta\LaravelGoogleAnalytics\Traits\Filters\CustomCommonFilters;
use RodrigoButta\LaravelGoogleAnalytics\Traits\Filters\GoogleCommonFilters;
use RodrigoButta\LaravelGoogleAnalytics\Exceptions\UndefinedViewIdException;

class Analytics
{
    use HelperFunctions;
    use CustomCommonFilters, GoogleCommonFilters;
    use DatesHandler, DimensionsHandler, FiltersHandler, MetricsHandler, ParamsHandler, SegmentHandler, SortHandler;

    /**
     * Google services core.
     *
     * @var \RodrigoButta\LaravelGoogleAnalytics\Core\Core
     */
    protected $googleServicesCore;

    /**
     * Google analytics view id.
     *
     * @var string
     */
    protected $viewId;

    /**
     * Parameters
     */
    protected $metrics = [];
    protected $dimensions = [];
    protected $sort;
    protected $filters;
    protected $segment;

    /**
     * Time period.
     *
     * @var \RodrigoButta\LaravelGoogleAnalytics\Period
     */
    protected $period;

    /**
     * Google Analytics service instance.
     *
     * @var \Google_Service_Analytics
     */
    protected $service;

    public function __construct(Core $googleServicesCore)
    {
        $this->googleServicesCore = $googleServicesCore;

        $this->configure();

        $this->setupAnalyticsService();

        // $this->setupDates();
    }

    /**
     * Getter for viewId.
     *
     * @return string
     */
    public function getViewId()
    {
        return $this->viewId;
    }

    /**
     * Setter for `viewId`, allows manual update inside code.
     *
     * @param string $viewId
     */
    public function setViewId($viewId)
    {
        $this->viewId = $viewId;
    }

    /**
     * Set the configuration details of analytics.
     *
     * @return void
     */
    private function configure()
    {
        $analyticsConfig = $this->googleServicesCore->getConfig('analytics');

        if (array_key_exists('viewId', $analyticsConfig)) {
            $this->viewId = $analyticsConfig['viewId'];
        }
    }

    private function setupAnalyticsService()
    {
        // Create Google Service Analytics object with our preconfigured Google_Client
        $this->service = new \Google_Service_Analytics(
            $this->googleServicesCore->getClient()
        );
    }

    public function setupDates($startDate = null, $endDate = null)
    {


        return $this->setPeriod(new Period($startDate, $endDate));
    }

    // private function setupDates($startDate = null, $endDate = null)
    // {

    //     $start = (new Carbon('first day of last month'))->hour(0)->minute(0)->second(0);

    //     $end = (new Carbon('last day of last month'))->hour(23)->minute(59)->second(59);

    //     return $this->setPeriod(new Period($start, $end));
    // }

    /**
     * Execute the query and fetch the results to a collection.
     *
     * @return array
     */
    public function getRealtimeData()
    {
        $this->validateViewId();

        $this->setMetrics('rt:activeUsers');

        $data = $this->service->data_realtime->get(
            $this->viewId,
            $this->getMetricsAsString(),
            $this->getOptions()
        );

        return $data->toSimpleObject()->totalsForAllResults;
    }

    /**
     * Execute the query by merging arrays to current ones.
     *
     * @param array $parameters
     *
     * @return $this
     */
    public function execute($parameters = [], $parseResult = true)
    {
        $this->validateViewId();

        $this->mergeParams($parameters);

        /*
         * A query can't run without any metrics.
         */
        if (!$this->metricsAreSet()) {
            throw new UndefinedMetricsException();
        }

        $result = $this->service->data_ga->get(
            $this->viewId,
            $this->period->getStartDate()->format('Y-m-d'),
            $this->period->getEndDate()->format('Y-m-d'),
            $this->getMetricsAsString(),
            $this->getOptions()
        );

        if ($parseResult) {
            return $this->parseResult($result);
        }

        return $result;
    }

    /**
     * Validate analytics view ID.
     *
     * @throws
     *
     * @return void
     */
    private function validateViewId()
    {
        if (!$this->viewId) {
            throw new UndefinedViewIdException();
        }
    }

    /**
     * Parse the dirty google data response.
     *
     * @var \Google_Service_Analytics_GaData results
     *
     * @return array
     */
    public function parseResult($results)
    {
        // var_dump($results);
        // var_dump($results->dataTable->cols);
        // var_dump($results->dataTable->rows[0]["c"]);
        // exit();

        $simpleDataTable = $results->getDataTable()->toSimpleObject();

        foreach ($simpleDataTable->cols as $col) {
            $cols[] = $col->label;
        }

        foreach ($simpleDataTable->rows as $row) {
            foreach ($row->c as $key => $value) {
                $rowData[$cols[$key]] = $value->v;
            }

            $rows[] = $rowData;

            unset($rowData);
        }

        return [
            'cols' => $cols,
            'rows' => $rows,
        ];
    }

}
