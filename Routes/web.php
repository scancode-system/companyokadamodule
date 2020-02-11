<?php

Route::prefix('companyokada')->group(function() {
    Route::get('export/txt/orders', 'ExportController@txtOrders')->name('companyokada.export.txt.orders');
});
