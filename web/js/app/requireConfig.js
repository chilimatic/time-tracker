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
        'angular-nvd3'      : '/vendor/angular-nvd3/dist/angular-nvd3.min',
        'draganddrop'       : '/js/lib/draganddrop',
        'd3'                : '/vendor/d3/d3.min',
        'nvd3'              : '/vendor/nvd3/build/nv.d3.min'
    },
    shim: {
        'app': {
            deps: ['angular', 'angular-route', 'angular-resource', 'angular-datepicker', 'draganddrop', 'angular-nvd3']
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
        },
        'angular-nvd3': {
            deps: ['d3', 'nvd3']
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