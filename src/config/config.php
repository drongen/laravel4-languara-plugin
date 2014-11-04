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
    "upload_translations"	=> "https://local.languara.com/api/project/process_batch_data",
    ),
"conf" => array(
    "project_id"            => "",
    "origin_site"           => "",
    "project_api_key"       => "",
    "project_deployment_id" => "",
    "project_api_secret"    => "",
    "auto_create"           => false,
    "platform"              => "",
    "storage_engine"        => "",
    "file_prefix"           => "",
    "file_suffix"           => "",
    ),
);