/**
 *
 */
requirejs.config({
    baseUrl: '/js/app',
    paths: {
        'angular'           : '/vendor/angular/angular.min',
        'angular-route'     : '/vendor/angular-route/angular-route.min',
        'angular-resource'  : '/vendor/angular-resource/angular-resource.min',
        'angular-datepicker': '/vendor/angularjs-datepicker/dist/angular-datepicker.min',
        'draganddrop'         : '/js/lib/draganddrop'
    },
    shim: {
        'app': {
            deps: ['angular', 'angular-route', 'angular-resource', 'angular-datepicker', 'draganddrop']
        },
        'angular-route': {
            deps: ['angular']
        },
        'angular-resource': {
            deps: ['angular']
        },
        'angular-storage' : {
            deps: ['angular']
        },
        'angular-ui' : {
            deps: ['angular']
        },
        'angular-datepicker' : {
            deps: ['angular']
        }
    }
});

requirejs
(
    ['app'],
    function(app) {
        angular.bootstrap(document, ['timetracker-frontend']);
    }
);