var init = function($scope) {
    $scope.message = 'Welcome';
};

var spinnerString = function() {
    return '&nbsp;<span class="fa fa-spinner fa-lg fa-spin"></span>';
};

var accessKey = function() {
    return 'ac_key=' + sessionStorage.user_id + '_' + sessionStorage.user_password;
};

var firstDayOfMonth = function(date) {
    return new Date(date.getFullYear(), date.getMonth(), 1);
};

var nameOfMonth = function(month) {
    switch (parseInt(month)) {
        case 1:
            return 'Leden';
        case 2:
            return 'Únor';
        case 3:
            return 'Březen';
        case 4:
            return 'Duben';
        case 5:
            return 'Květen';
        case 6:
            return 'Červen';
        case 7:
            return 'Červenec';
        case 8:
            return 'Srpen';
        case 9:
            return 'Září';
        case 10:
            return 'Říjen';
        case 11:
            return 'Listopad';
        case 12:
            return 'Prosinec';
        default:
            return '';
    }
};

calendarApp.controller('mainController', function($scope, $http, $filter, $rootScope, $timeout) {
    var date = new Date();
    $scope.monthWord = $filter('date')(date, 'MMMM');
    var firstDay = $filter('date')(firstDayOfMonth(date), 'dd_MM_yyyy');

    $rootScope.globalMonth = $filter('date')(date, 'M');
    $rootScope.globalYear = $filter('date')(date, 'yyyy');
    $scope.nameOfMonth = nameOfMonth;

    var getCalendar = function(firstDay) {
        $http.get('api/api.php?calendar&' + accessKey() + '&first_day=' + firstDay).
            success(function(data) {
                $scope.weeks = data.weeks;
            });
    };

    getCalendar(firstDay);

    var timeLoop = function() {
        $rootScope.todayDate = $filter('date')(new Date, 'dd.MM.yyyy H:mm:ss');
        $timeout(timeLoop, 1000);
    };

    $timeout(timeLoop(), 1000);

    $scope.monthBefore = function() {
        if ($rootScope.globalMonth == 1) {
            $rootScope.globalMonth = 12;
            $rootScope.globalYear--;
        } else {
            $rootScope.globalMonth--;
        }

        var date = new Date(parseInt($rootScope.globalYear), parseInt($rootScope.globalMonth) - 1, 1, 0, 0, 0, 0);
        getCalendar($filter('date')(date, 'dd_MM_yyyy'));
    };

    $scope.monthAfter = function() {
        if ($rootScope.globalMonth == 12) {
            $rootScope.globalMonth = 1;
            $rootScope.globalYear++;
        } else {
            $rootScope.globalMonth++;
        }

        var date = new Date(parseInt($rootScope.globalYear), parseInt($rootScope.globalMonth) - 1, 1, 0, 0, 0, 0);
        getCalendar($filter('date')(date, 'dd_MM_yyyy'));
    };

    $scope.monthToday = function() {
        var date = new Date();
        $scope.monthWord = $filter('date')(date, 'MMMM');
        var firstDay = $filter('date')(firstDayOfMonth(date), 'dd_MM_yyyy');

        $rootScope.globalMonth = $filter('date')(date, 'M');
        $rootScope.globalYear = $filter('date')(date, 'yyyy');
        $scope.nameOfMonth = nameOfMonth;

        getCalendar(firstDay);
    };
});

calendarApp.controller('administrationController', function($scope) {
    init($scope);
});

calendarApp.controller('logOutController', function($scope, $location, $rootScope) {
    sessionStorage.clear();

    $rootScope.logged = false;
    $location.path('/login');
});

calendarApp.controller('dayController', function($scope, $http, $routeParams) {
    var dayArray = $routeParams.day.split('_');
    $scope.day = (dayArray[0].charAt(0) == '0') ? dayArray[0].charAt(1) : dayArray[0];
    $scope.day += '.' + ((dayArray[1].charAt(0) == '0') ? dayArray[1].charAt(1) : dayArray[1]) + '.' + dayArray[2];

    $scope.openNewInputForm = false;
    $scope.newInputMessage = '';

    $scope.range = function(number) {
        return new Array(number);
    };

    $scope.newInput = function() {
        $scope.openNewInputForm = $scope.openNewInputForm ? false : true;
    };

    $scope.addInput = function(input) {
        if (input == undefined) {
            $scope.newInputMessage = 'Nebyly vyplněny všechny údaje';
            return;
        }

        var input = {
            from: input.from_h == undefined || input.from_m == undefined ? null : input.from_h + '_' + input.from_m,
            to: input.to_h == undefined || input.to_m == undefined ? null : input.to_h + '_' + input.to_m,
            name: input.name == undefined ? null : input.name,
            text: input.text == undefined ? null : input.text.replace('\n', '<br>')
        };

        if (input.from == null || input.to == null || input.name == null) {
            $scope.newInputMessage = 'Nebyly vyplněny všechny údaje';
            return;
        }

        $http.get('api/api.php?new_input&' + accessKey() + '&from=' + input.from + '&to=' + input.to + '&name=' + input.name + '&text=' + input.text)
            .success(function(data) {
                console.log(data);

                $scope.openNewInputForm = false;
            });

        console.log(input);
    };
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

        $http.get('api/api.php?login&username=' + user.login + '&password=' + user.password).
            success(function(data) {
                console.log(data);

                if (!data.status) {
                    switch (data.login_error) {
                        case 'username does not exist':
                            $scope.loginMessage = 'Uživatel neexistuje';
                            break;
                        case 'bad password':
                            $scope.loginMessage = 'Špatné heslo';
                            break;
                        default:
                            $scope.loginMessage = 'Chyba';
                    }

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