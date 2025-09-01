<?php
foreach (glob(__DIR__ . '/configs/*.php') as $file) {
    require_once $file;
}
foreach (glob(__DIR__ . '/core/*.php') as $file) {
    require_once $file;
}

foreach (glob(__DIR__ . '/app/models/*.php') as $file) {
    require_once $file;
}

foreach (glob(__DIR__ . '/app/controllers/*.php') as $file) {
    require_once $file;
}


$test = new BaseModel();