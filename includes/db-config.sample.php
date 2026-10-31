<?php
// Copy to db-config.php on the server and fill in the cPanel MySQL details.
return [
    'host' => 'localhost',
    'port' => '3306',
    'name' => 'cpaneluser_clubyatri',
    'user' => 'cpaneluser_dbuser',
    'pass' => 'CHANGE_ME',
    // Secret used by the deploy workflow to run database/migrations (24+ random characters).
    // Must match the DEPLOY_TOKEN GitHub secret. Leave empty to disable deploy-migrate.php.
    'deploy_token' => '',
];
