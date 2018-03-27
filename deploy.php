<?php
namespace Deployer;

require 'recipe/drupal8.php';

// Project name
set('application', 'test-deploy'); // challenge-platform

// Project repository
set('repository', 'https://github.com/drupalwxt/site-pco-cities.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

// Shared files/dirs between deploys 
add('shared_files', [

]);

add('shared_dirs', [
  'html/sites',
]);

// Writable dirs by web server 
add('writable_dirs', [
  'html/sites/{{drupal_site}}/files',
]);

// Tasks
task('build', function () {
    run('cd {{release_path}} && composer install');
});

task('drush:cr', '
  cd {{release_path}}/html
  ../vendor/bin/drush cr
');

task('drush:updb', '
  cd {{release_path}}/html;
  ../vendor/bin/drush updb
');

task('deploy', [
  'deploy:info',
  'deploy:prepare',
  'deploy:lock',
  'deploy:release',
  'deploy:update_code',
  'deploy:shared',
  'deploy:vendors',
  'drush:updb',
  'drush:cr',
  'deploy:symlink',
  'deploy:unlock',
  'cleanup'
]);

// Hosts
host('52.235.24.208')
  ->set('deploy_path', '~/{{application}}')
  ->stage('staging')
  ->user('pcocp')
  ->identityFile('~/.ssh/id_rsa');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
