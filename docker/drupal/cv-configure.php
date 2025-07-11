{
    "sites": {
        "/opt/drupal/web/sites/default/civicrm.settings.php": {
            "TEST_DB_DSN": "mysql://<?php echo getenv('TEST_DB_USER') ?>:<?php echo getenv('TEST_DB_PASSWORD') ?>@<?php echo getenv('TEST_DB_HOST') ?>/<?php echo getenv('TEST_DB_NAME') ?>?new_link=true"
        }
    }
}