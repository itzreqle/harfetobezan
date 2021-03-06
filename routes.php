<?php

require_once("{$_SERVER['DOCUMENT_ROOT']}/router.php");
require_once("{$_SERVER['DOCUMENT_ROOT']}/config.php");

// ##################################################
// ##################################################
// ##################################################

// Static GET
// In the URL -> http://localhost
// The output -> Index
get('/', '/pages/index.php');
post('/', '/pages/index.php');
get('/index', '/pages/index.php');
post('/index', '/pages/index.php');
get('/home', '/pages/index.php');
post('/home', '/pages/index.php');
get('/profile', '/pages/view.php');
post('/profile', '/pages/view.php');
get('/view', '/pages/view.php');
post('/view', '/pages/view.php');
get('/challenge/$challenge_id', '/pages/challenge.php');
post('/challenge/$challenge_id', '/pages/challenge.php');

// API
get('/api/v1/theme', '/api/v1/theme.php');
post('/api/v1/theme', '/api/v1/theme.php');
get('/theme', '/api/v1/theme.php');
post('/theme', '/api/v1/theme.php');

// Dynamic GET. Example with 1 variable
// The $id will be available in user.php
// get('/user/$id', 'user.php');

// Dynamic GET. Example with 2 variables
// The $name will be available in user.php
// The $last_name will be available in user.php
// get('/user/$name/$last_name', 'user.php');

// Dynamic GET. Example with 2 variables with static
// In the URL -> http://localhost/product/shoes/color/blue
// The $type will be available in product.php
// The $color will be available in product.php
// get('/product/$type/color/:color', 'product.php');

// Dynamic GET. Example with 1 variable and 1 query string
// In the URL -> http://localhost/item/car?price=10
// The $name will be available in items.php which is inside the views folder
// get('/item/$name', 'views/items.php');


// ##################################################
// ##################################################
// ##################################################
// any can be used for GETs or POSTs

// For GET or POST
// The 404.php which is inside the views folder will be called
// The 404.php has access to $_GET and $_POST
any('/404', '/pages/404.php');
