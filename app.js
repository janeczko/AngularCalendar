var calendarApp = angular.module('calendarApp', ['ngRoute', 'ngCookies']);

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
}).run(function($rootScope, $location, $localStorage) {
    $rootScope.$on("$routeChangeStart", function(event, next, current) {
        if ($localStorage.getItem('user') == null) {
            $location.path('/login');
        }
    });
});