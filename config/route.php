<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Webman\Route;

Route::options('[{path:.+}]', function (){
    return response('');
});
Route::group('/api/v1', function() {
	Route::get('/config', [app\controller\IndexController::class, 'config']);
	
	Route::get('/goods/cate', [app\controller\GoodsController::class, 'cate']);
    Route::get('/goods/price/{gtin}', [app\controller\PricesController::class, 'goods']);
    Route::put('/goods/price/{id:\d+}', [app\controller\PricesController::class, 'goodsPost']);

    Route::get('/goods/{sn}', [app\controller\GoodsController::class, 'info']);
    Route::post('/goods/{sn}', [app\controller\GoodsController::class, 'infoPost']);
    Route::put('/goods/{id:\d+}', [app\controller\GoodsController::class, 'infoEdit']);
	
	Route::get('/likes/goods', [app\controller\GoodsController::class, 'likes']);
	

    Route::get('/brand/search', [app\controller\BrandsController::class, 'search']);
    Route::get('/brand/{name}', [app\controller\BrandsController::class, 'info']);
    Route::put('/brand/{id:\d+}', [app\controller\BrandsController::class, 'infoEdit']);
    Route::post('/brand', [app\controller\BrandsController::class, 'infoPost']);

    Route::get('/shop/{name}', [app\controller\ShopController::class, 'info']);
    Route::put('/shop/{id:\d+}', [app\controller\ShopController::class, 'infoEdit']);
    Route::post('/shop', [app\controller\ShopController::class, 'infoPost']);


});

Route::group('/api/app', function() {
	Route::get('/sync/goods', [app\controller\ApiController::class, 'goodsSyncUpdate']);
	Route::get('/check/goods/sn', [app\controller\ApiController::class, 'goodsCheckInfo']); //检查临时库里是否有存在的条码数据
    Route::get('/goods/list', [app\controller\ApiController::class, 'goodsList']);
    Route::post('/goods/delete/{id:\d+}', [app\controller\ApiController::class, 'goodDelete']);
    Route::get('/brand/list', [app\controller\ApiController::class, 'brandList']);
    Route::get('/brand/search', [app\controller\ApiController::class, 'brandSearch']);
    Route::post('/brand/delete/{id:\d+}', [app\controller\ApiController::class, 'brandDelete']);
    Route::get('/shop/list', [app\controller\ApiController::class, 'shopList']);
    Route::post('/shop/delete/{id:\d+}', [app\controller\ApiController::class, 'shopDelete']);
	Route::post('/files', [app\controller\FilesController::class, 'index']);
});

Route::group('/api/network', function() {
    Route::post('/sync', [app\controller\NetWorkController::class, 'sync']);
});