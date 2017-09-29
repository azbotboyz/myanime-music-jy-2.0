<?php
/**
 * JYmusic音乐管理系统 PHP version 5.4+
 *
 * @version     2.0
 * @author      战神~~巴蒂 <jyuucn@163.com>
 * @license     http://jyuu.cn/license [未经授权严禁私自出售，否者承担法律责任]
 * @copyright   2014 - 2017 JYmusic
 */

use think\Route;

//use think\Config;

/**
 * 加载域名配置列表
 */
//Config::load(ROOT_PATH . 'config' . DS . 'app.php');

//$config = Config::get();

//全局变量规则
Route::pattern(['name' => '\w+', 'type'=>'\w+', 'keys'=>'\w+', 'token' => '\w+','order'=>'[A-Za-z]+', 'id' => '\d+', 'uid' => '\d+',  'page' => '\d+', 'year' => '\d{4}', 'month' => '\d{2}']);

Route::rule('apply/:addons/[:controller]/[:action]', 'home/apply/index');

Route::get('/', 'home/Index/index');
Route::get('assets/css/:name', 'home/Assets/css', ['ext'=>'css']);
Route::get('assets/js/:name', 'home/Assets/js', ['ext'=>'js']);

Route::get('music/:id', 'home/Music/read');
Route::get('down/:id', 'home/Music/down');

Route::get('album/type/:name', 'home/Album/type');
Route::get('album/page/:page', 'home/Album/index');
Route::get('album/:id', 'home/Album/read');
Route::get('album', 'home/Album/index');

Route::get('artist/type/:name', 'home/Artist/type');
Route::get('artist/page/:page', 'home/Artist/index');
Route::get('artist/:id/songs', 'home/Artist/songs');
Route::get('artist/:id/albums', 'home/Artist/albums');
Route::get('artist/:id', 'home/Artist/read');
Route::get('artist', 'home/Artist/index');


Route::get('genre/:name/:order$', 'home/Genre/order');
Route::get('genre/:name/:order/page/[:page]', 'home/Genre/order');
Route::get('genre/:name', 'home/Genre/read');
Route::get('genre', 'home/Genre/index');


Route::get('tag/:name', 'home/Tag/read');
Route::get('tag/:name/page/:page', 'home/Tag/read');
Route::get('tag', 'home/Tag/index');

Route::get('ranks/:name', 'home/Ranks/read');
Route::get('ranks', 'home/Ranks/index');

Route::get('search/[:action]/[:type]/[:keys]', 'home/Search/index');

/**********资讯页面*************/
Route::get('/article/cate/:name', 'article/Category/read');
Route::get('/article/cate/:name/page/:page', 'article/Category/read');
Route::get('/article/cate', 'article/Category/index');
Route::get('/article/:id', 'article/Index/read');
Route::get('/article', 'article/Index/index');

Route::get('/site/:type/:name', 'article/Site/read');


/**********用户中心*************/
$userRules = function () {
    Route::get('/pact', 'user/Auth/pact');
    Route::Rule('/active', 'user/Auth/active');
    Route::get('/login', 'user/Auth/login');
    Route::get('/signup', 'user/Auth/signup');
    Route::Rule('/sign', 'user/Auth/loginUser');
    Route::get('/confirm/:token', 'user/Auth/confirm');
    Route::Rule('/activate', 'user/Auth/activate');
    Route::Rule('/captcha', 'user/Auth/captcha');
    Route::Rule('/findpwd', 'user/Auth/reset');
    Route::Rule('/getvcode', 'user/Auth/getVcode');
    Route::post('/down/check/', 'user/Down/check');
    Route::post('/down/get/', 'user/Down/get');
    Route::get('/down/:id', 'user/Down/read');
    
    Route::get('/oauth/call', 'user/Oauth/callback');
    //Route::get('/oauth/token/:driver', 'user/Oauth/token');
    Route::get('/oauth/:driver', 'user/Oauth/index');
    
    Route::get('/account/charge', 'user/Account/charge');
    Route::get('/account/upgrade', 'user/Account/upgrade');
    Route::post('/account/upgrade', 'user/Account/updateGroup');
    Route::get('/account', 'user/Account/index');
    Route::Rule('/logout', 'user/Auth/logout');
    Route::Rule('/validation/:field', 'user/Auth/validation');

    Route::get('/music/share', 'user/Music/create');
    Route::post('/music/share', 'user/Music/save');
    Route::get('/music/edit/:id', 'user/Music/edit');
    Route::post('/music/edit', 'user/Music/update');
    Route::get('/music/audit', 'user/Music/audit');
    Route::get('/music/back', 'user/Music/back');
    Route::get('/music/down', 'user/Music/down');
    Route::get('/music', 'user/Music/index');

    Route::get('/album/create', 'user/Album/create');
    Route::post('/album/create', 'user/Album/save');
    Route::get('/album/edit', 'user/Album/edit');
    Route::post('/album/edit', 'user/Album/update');
    Route::get('/album/audit', 'user/Album/audit');
    Route::get('/album', 'user/Album/index');

    Route::get('/fav/album', 'user/Fav/album');
    Route::get('/fav/songs', 'user/Fav/songs');
    Route::get('/fav/artist', 'user/Fav/artist');
    Route::get('/fav', 'user/Fav/index');
    
    Route::get('/relation', 'user/Relation/index');
    Route::get('/follow', 'user/Relation/follow');
    Route::get('/fans', 'user/Relation/fans');

    Route::get('/setting/avatar', 'user/Setting/avatar');
    Route::post('/setting/avatar', 'user/Setting/changeAvatar');
    Route::get('/setting/password', 'user/Setting/password');
    Route::post('/setting/password', 'user/Setting/changePwd');
    Route::get('/setting', 'user/Setting/index');
    Route::post('/setting', 'user/Setting/update');

    Route::post('/message/create', 'user/Message/create');
    Route::post('/message/del/:id', 'user/Message/delete');
    Route::get('/message/:id', 'user/Message/read');
    Route::get('/message', 'user/Message/index');
    
    Route::post('/notice/del/:id', 'user/Notice/delete');
    Route::get('/notice/:id', 'user/Notice/read');
    Route::get('/notice', 'user/Notice/index');
    
    Route::Rule('/join', 'user/Auth/createUser');
    Route::Rule('/findpwd', 'user/Auth/resetPwd');

    Route::get('/musician/auth', 'user/Musician/auth');
    Route::post('/musician/auth', 'user/Musician/save');
    Route::get('/musician/edit', 'user/Musician/edit');
    Route::post('/musician/edit', 'user/Musician/update');
    Route::get('/musician/:id', 'user/Musician/read');
    Route::get('/musician', 'user/Musician/index');
    
    Route::get('/:uid/music', 'user/Index/music');
    Route::get('/:uid/share', 'user/Index/music');
    Route::get('/:uid/album', 'user/Index/album');
    Route::get('/:uid/fans', 'user/Index/fans');
    Route::get('/:uid/follow', 'user/Index/follow');
    Route::get('/:uid/visitor', 'user/Index/visitor');

    Route::get('/:uid', 'user/Index/read');
    Route::get('/', 'user/Index/index');
};
Route::group('user', $userRules);

//api配置
/**********api配置*************/
$apiRules = function () {
    Route::Rule('actions/:action', 'api/v1.Actions/:action', 'get|post');
    Route::resource('songs', 'api/v1.Songs');
    Route::resource('albums', 'api/v1.Albums');
    Route::resource('artists', 'api/v1.Artists');
    Route::resource('genres', 'api/v1.Genres');
    Route::resource('ranks', 'api/v1.Ranks');
    Route::resource('users', 'api/v1.Users');
    Route::get('/', 'api/v1.Index/index');
};

Route::group('api', $apiRules);

Route::miss('home/Index/miss','GET');
