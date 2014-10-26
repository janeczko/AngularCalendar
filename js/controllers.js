var init = function($scope) {
    $scope.message = 'Welcome';
};

var spinnerString = function() {
    return '&nbsp;<span class="fa fa-spinner fa-lg fa-spin"></span>';
};

calendarApp.controller('mainController', function($scope, $http) {
    /*init($scope);

    $http.get('api/index.php').success(function(data) {
        console.log(data);

        $scope.user = data;
    });*/
});

calendarApp.controller('administrationController', function($scope) {
    init($scope);
});

calendarApp.controller('logOutController', function($scope, $location, $rootScope) {
    sessionStorage.clear();

    $rootScope.logged = false;
    $location.path('/login');
});

calendarApp.controller('loginController', function($scope, $http, $location, $rootScope) {
    $scope.loginMessage = '';
    $scope.spinner = '';

    $scope.logIn = function(user) {
        $scope.spinner = spinnerString();

        if (user == undefined) {
            $scope.loginMessage = 'Nebyly vyplněny všechny údaje';
            $scope.spinner = '';
            return;
        }

        $http.get('api/index.php?login&username=' + user.login + '&password=' + user.password).
            success(function(data) {
                if (!data.status) {
                    $scope.loginMessage = data.login_error;
                    $scope.spinner = '';
                } else {
                    sessionStorage.user_username = data.username;
                    sessionStorage.user_password = data.password;
                    sessionStorage.user_id = data.id;

                    $rootScope.logged = true;
                    $location.path('/');
                }
            }).
            error(function(data) {
                console.log(data);
            });
    };
});