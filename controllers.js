var init = function($scope) {
    $scope.message = 'Welcome';
};

calendarApp.controller('mainController', function($scope) {
    init($scope);

});

calendarApp.controller('administrationController', function($scope) {
    init($scope);
});

calendarApp.controller('logOutController', function($scope, $location, $localStorage) {
    init($scope);

    $localStorage.setItem('user', null);
    $location.path('/login');
});

calendarApp.controller('loginController', function($scope, $location, $localStorage) {
    $scope.message = 'login here';

    $scope.sendLoginForm = function(user) {
        if (user.login == 'janeczko' && user.password == '123456') {
            $localStorage.setItem('user', user.login);
            $location.path('/');
            console.log('prihlasen');
        } else {
            console.log('spatne udaje');
        }
    };
});