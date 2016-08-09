"use strict";

define(['app'], function(app)
{
    app
        .controller('loginController', ['$scope','$rootScope', '$interval', '$location', 'login', 'user', function($scope, $rootScope, $interval, $location, login, user)
        {
            /**
             * @type {{email: string, password: string}}
             */
            $scope.loginData = {
                email       : '',
                password    : ''
            };

            /**
             * switch for displaying the creation password
             * @type {boolean}
             */
            $scope.showForm = false;

            /**
             * @type {{type: string, message: string}}
             */
            $scope.message = {
                type    : '',
                message : ''
            };

            /**
             *
             * @type {{email: {value: string, error: Array}, emailVerify: {value: string, error: Array}, password: {value: string, error: Array}, passwordVerfiy: {value: string, error: Array}}}
             */
            $scope.creationData = {
                email           : {
                    value: '',
                    error: []
                },
                verifyEmail     : {
                    value: '',
                    error: []
                },
                password        : {
                    value: '',
                    error: []
                },
                verifyPassword  : {
                    value: '',
                    error: []
                }
            };

            $rootScope.$on('login-error', function(e, param){
                if (!param) {
                    return;
                }
                $scope.message['type'] = 'error';
                $scope.message['message'] = param.msg;

            });

            /**
             * http://stackoverflow.com/questions/46155/validate-email-address-in-javascript
             * @param email
             * @returns {boolean}
             */
            function validateEmail(email) {
                var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(email);
            }

            $scope.createUser = function() {
                var valid = true;

                valid = $scope.checkUsername() & valid;
                valid = $scope.checkPassword() & valid;
                if (!valid) {
                    return false;
                }

                user.create(
                    {
                        'actionName' : 'create',
                        'controllerName' : 'user'
                    },
                    {
                        password : $scope.creationData.password.value,
                        username : $scope.creationData.email.value
                    },
                    function(promise)
                    {
                        if (!promise.response) {
                            return;
                        }

                        if (promise.response.success) {
                            $scope.message.type = 'success';
                            $scope.message.message = promise.response.msg;
                            $scope.showForm = !$scope.showForm;
                        } else if (promise.response.error) {
                            $scope.message.type = 'error';
                            $scope.message.message = promise.response.msg;
                        }
                    }
                )
            };


            $scope.checkUsername = function()
            {
                var returnValue = true;
                $scope.creationData.verifyEmail.error = [];
                $scope.creationData.email.error = [];

                if (!$scope.creationData.email.value) {
                    $scope.creationData.email.error.push('Email Address entered is not valid');
                    returnValue = false;
                }

                if (!validateEmail($scope.creationData.email.value)) {
                    $scope.creationData.email.error.push('Email Address entered is not valid');
                    returnValue = false;

                }

                if ($scope.creationData.verifyEmail && !validateEmail($scope.creationData.verifyEmail.value)) {
                    $scope.creationData.verifyEmail.error.push('Email Address entered is not valid');
                    returnValue = false;
                }

                if ($scope.creationData.email.value != $scope.creationData.verifyEmail.value) {
                    $scope.creationData.verifyEmail.error.push('Verification email does not match');
                    returnValue = false;
                }

                return returnValue;
            };



            $scope.checkPassword = function()
            {
                var returnValue = true;
                $scope.creationData.verifyPassword.error = [];
                $scope.creationData.password.error = [];


                if ($scope.creationData.verifyPassword && $scope.creationData.password.value != $scope.creationData.verifyPassword.value) {
                    $scope.creationData.verifyPassword.error.push('Passwords do not match');
                    returnValue = false;
                }

                return returnValue;
            };


            /**
             * clears the creation form
             */
            $scope.showCreateForm = function() {
                $scope.showForm = !$scope.showForm;
                // clear form data
                for (var i in $scope.creationData) {
                    $scope.creationData[i] = '';
                }
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