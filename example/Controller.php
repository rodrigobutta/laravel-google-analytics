<?php
namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use RodrigoButta\LaravelGoogleAnalytics\Analytics as GoogleAnalytics;


class ReportController extends Controller
{

    private $ga;

    function __construct(GoogleAnalytics $ga) {
         $this->ga = $ga;
    }


    public function gaQuery(Request $request){

        $metrics = $request->get('metrics');
        $dimensions = $request->get('dimensions');
        $segment = $request->get('segment');
        $sort = $request->get('sort');
        $filters = $request->get('filters');
        $max_results = $request->get('max-results');


        $start = (new Carbon('first day of this month'))->hour(0)->minute(0)->second(0);
        $end = (new Carbon('last day of this month'))->hour(23)->minute(59)->second(59);
        // var_dump($start);
        // var_dump($end);
        $this->ga->setupDates($start,$end);

        // $results = $this->ga->getUsersAndPageviewsOverTime();
        // $results = $this->ga->getBrowserAndOperatingSystem();

        $params = [];

        $params = [
            'metrics'    => $metrics,
            'dimensions' => $dimensions,
        ];

        if($sort!=''){
            $params['sort'] = $sort;
        }

        if($filters!=''){
            $params['filters'] = $filters;
        }


        if($max_results!=''){
            $this->ga->setMaxResults($max_results);
        }



        $this->ga->setParams($params);


        $results = $this->ga->execute();
        // var_dump($results);

        return json_encode($results);
    }





}
