<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Pdf\Repositories\PdfLayoutRepository;

class InsertPdfLayoutsRecordsCompanyOkada extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        PdfLayoutRepository::create(['title' => 'Modali - Filial e Estoque (atual e futuro)', 'description' => 'Layout de filiais com estoque atual e futuro', 'path' => 'companyokada::pdf.order']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        PdfLayoutRepository::deleteByTitle('Modali - Filial e Estoque (atual e futuro)');
    }
}

 