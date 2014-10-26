var init = function($scope) {
    $scope.message = 'Welcome';
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

calendarApp.controller('logOutController', function($scope, $location) {
    sessionStorage.clear();

    $location.path('/login');
});

calendarApp.controller('loginController', function($scope, $http, $location) {
    $scope.loginMessage = '';

    $scope.logIn = function(user) {
        if (user == undefined) {
            user = {
                login: null,
                password: null
            };
        }

        $http.get('api/index.php?login&username=' + user.login + '&password=' + user.password).
            success(function(data) {
                if (!data.status) {
                    $scope.loginMessage = data.login_error;
                } else {
                    sessionStorage.user_username = data.username;
                    sessionStorage.user_password = data.password;
                    sessionStorage.user_id = data.id;

                    $location.path('/');
                }
            }).
            error(function(data) {
                console.log(data);
            });
    };
});