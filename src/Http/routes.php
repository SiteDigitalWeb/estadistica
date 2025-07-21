<?php
 Route::group(['middleware' => ['auth','administrador']], function (){
 Route::prefix('sd')->group(function () {
 Route::get('/stadistics', 'Sitedigitalweb\Estadistica\Http\EstadisticaController@index');
 Route::get('stadistics-block', 'Sitedigitalweb\Estadistica\Http\EstadisticaController@blocks');
 Route::get('/stadistics-block/create', function(){
 return View::make('estadistica::crear-block');});
 Route::post('/stadistics-block/creates', 'Sitedigitalweb\Estadistica\Http\EstadisticaController@crearblocks');
 });
});

 