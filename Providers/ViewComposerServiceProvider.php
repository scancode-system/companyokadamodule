<?php

namespace Modules\CompanyOkada\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Modules\Subsidiary\Http\ViewComposers\Loader\Products\EditComposer;
use Modules\CompanyOkada\Http\ViewComposers\Pdf\OrderComposer;

class ViewComposerServiceProvider extends ServiceProvider 
{

	public function boot() {
		// pdf
		View::composer('companyokada::pdf.order', OrderComposer::class);
	}

	public function register() {
        //
	}

}
