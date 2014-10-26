var isLogged = function() {
    return !(sessionStorage.user_username == undefined || sessionStorage.user_password == undefined || sessionStorage.user_id == undefined);
};

var calendarApp = angular.module('calendarApp', ['ngRoute']);

calendarApp.config(function($routeProvider) {
    $routeProvider

        .when('/', {
            templateUrl : 'templates/home.html',
            controller  : 'mainController'
        })

        .when('/login', {
            templateUrl: 'templates/login.html',
            controller: 'loginController'
        })

        .when('/administration', {
            templateUrl : 'templates/administration.html',
            controller  : 'administrationController'
        })

        .when('/logout', {
            templateUrl : 'templates/logOut.html',
            controller  : 'logOutController'
        });

}).run(function($rootScope, $location) {
     $rootScope.logged = isLogged();

     $rootScope.$on("$routeChangeStart", function(event, next, current) {
        if (!isLogged()) {
            $location.path('/login');
        }
     });
});

calendarApp.filter('rawHtml', ['$sce', function($sce) {
    return function(val) {
        return $sce.trustAsHtml(val);
    };
}]);