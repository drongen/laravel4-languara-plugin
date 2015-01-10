<?php
// Languara CodeIgniter Plugin Configuration
// For more information visit http://languara.com
// (c) 2014 - Languara.  All rights reserved.
//

return array(
"language_location" => "app/lang/",
"endpoints"	=> array(
    "translation"			=> "https://languara.com/api/translation/find_all.json",
    "resource"				=> "https://languara.com/api/resource/find_all.json",
    "resource_group"		=> "https://languara.com/api/resourcegroup/find_all.json",
    "project_locale"		=> "https://languara.com/api/project/locales.json",
    "translation_local"		=> "https://local.languara.com/api/translation/find_all.json",
    "resource_local"        => "https://local.languara.com/api/resource/find_all.json",
    "resource_group_local"	=> "https://local.languara.com/api/resourcegroup/find_all.json",
    "project_locale_local"	=> "https://local.languara.com/api/project/locales.json",
    "upload_translations_local"	=> "https://local.languara.com/api/project/process_batch_data",
    "upload_translations"	=> "https://languara.com/api/project/process_batch_data",
    ),
"conf" => array(
    "project_id"            => "2",
    "origin_site"           => "https://local.languara.com",
    "project_api_key"       => "aOIC0yR69z7DucSI",
    "project_deployment_id" => "4",
    "project_api_secret"    => "XGXmKz7TelQlnKfaYVYT9AzY2dO27jqn",
    "auto_create"           => false,
    "platform"              => "fuelphp",
    "storage_engine"        => "php_array",
    "file_prefix"           => "",
    "file_suffix"           => "",
    ),
);