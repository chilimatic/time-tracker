"use strict";

define(['app'], function(app)
{
    app
        .controller('loginController', ['$scope', '$location', 'login', function($scope, $location, login)
        {
            /**
             * @type {{email: string, password: string}}
             */
            $scope.loginData = {
                email       : '',
                password    : ''
            };

            /**
             *
             */
            $scope.sendLogin = function() {
                login.logIn($scope.loginData.email, $scope.loginData.password);
            };


            /**
             *
             */
            $scope.init = function() {
                login.loggedIn();
            };

            $scope.init();

        }]
    );
});