/**
 *
 */
requirejs.config({
    baseUrl: '/js/app',
    paths: {
        'angular'           : '/vendor/angular/angular.min',
        'angular-route'     : '/vendor/angular-route/angular-route.min',
        'angular-resource'  : '/vendor/angular-resource/angular-resource.min',
        'header-controller' : '/js/app/frontend/header/controller'
    },
    shim: {
        'app': {
            deps: ['angular', 'angular-route', 'angular-resource']
        },
        'angular-route': {
            deps: ['angular']
        },
        'angular-resource': {
            deps: ['angular']
        },
        'angular-storage' : {
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