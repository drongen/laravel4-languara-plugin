<?php

Route::match(array('GET', 'POST'), 'languara/pull', array('uses' => 'Languara\Plugin\Controllers\LanguaraController@pull'));
Route::match(array('GET', 'POST'), 'languara/push', array('uses' => 'Languara\Plugin\Controllers\LanguaraController@push'));
