<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Modules\Dashboard\Repositories\TxtRepository;

class InsertTxtsRecordsModuleCompanyOkada extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        TxtRepository::new(['module' => 'CompanyOkada', 'service' => 'Txt', 'alias' => 'Pedidos - Modali']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        TxtRepository::deleteByAlias('Pedidos - Modali');
    }
}
