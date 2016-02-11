# time-tracker
This is a really simple App for tracking time
At the moment the projects are shared with all users and only the sessions are tracked per user!

The idea is pretty simple 
  * login your user
  * create a Project
  * select the Project
  * start a session
  * end a session 

## System Requirements: 
 * MySQL (Maria/Percona) (InnoDB with Foreign-Keys is needed -> CURRENT_TIMESTAMP as created default
 * PHP 5.6+ (the framework uses Generators and Traits)
 * npm (for bower, gulp, sass, ...)
 * phing (for the build script)

## Used Libraries / Tools
### JS 
 * Angular 1.5
 * Require.js
 * Bower
 * Gulp (look at gulpfile.js)
 * Bootstrap 3.* (atm only the CSS)
 * jQuery (for bootstrap)

### GIT Submodule
 * phing-build
 
### PHP
 * composer
 * chilimatic-framework

# build
git clone git@github.com:chilimatic/time-tracker.git --recursive
change into the directory 

# issues
* the user has to be created manually 
* the end session button changes the 
* the UX is really bad :D