<?php

use Inphinit\Routing\Route;

Route::set('GET',       '/collections',                                   'Collections:browse');
Route::set('GET',       '/collections/{:[a-zA-Z-]+?:}',                   'Collections:read');
Route::set('PUT',       '/collections',                                   'Collections:edit');
Route::set('POST',      '/collections',                                   'Collections:add');
Route::set('DELETE',    '/collections',                                   'Collections:delete');

Route::set('POST',      '/store',                                    'Store:add');
Route::set('PATCH',     '/store',                                    'Store:patch');
Route::set('PUT',       '/store',                                    'Store:edit');
Route::set('DELETE',    '/store',                                    'Store:delete');

Route::set('POST',      '/query',                                   'Query:run');
