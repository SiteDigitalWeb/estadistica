<?php
 Route::group(['middleware' => ['auth','administrador']], function (){
 Route::prefix('sd')->group(function () {
 Route::get('/stadistics', 'DigitalsiteSaaS\Estadistica\Http\EstadisticaController@index');
 Route::get('stadistics-block', 'DigitalsiteSaaS\Estadistica\Http\EstadisticaController@blocks');
 Route::get('/stadistics-block/create', function(){
 return View::make('estadistica::crear-block');});
 Route::post('/stadistics-block/creates', 'DigitalsiteSaaS\Estadistica\Http\EstadisticaController@crearblocks');
 });
});

 