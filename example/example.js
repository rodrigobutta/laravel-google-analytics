

function getGoogleAnalyticsData(metrics,dimensions,segment='',sort='',filters='',max_results='',callback){

    $.ajax({
        type: 'POST',
        url: URL_REPORT_CONTROLLER,
        data: {
          dimensions: dimensions,
          metrics: metrics,
          segment: segment,
          sort: sort,
          filters: filters,
          "max-results": max_results,
        },
        dataType: 'json',
        success: function (data) {                    
          if(typeof callback === "function"){
            callback(data);
          }

        }
    });

}







// ejemplo 1
getGoogleAnalyticsData('ga:sessions','ga:pagePath,ga:pageTitle','','-ga:sessions','','10', function (data) {
  console.log(data);
});





// ejemplo 2 nuevos vs recurrentes 
getGoogleAnalyticsData('ga:sessions','ga:userType','','','','', function (data) {
  console.log(data);
});


