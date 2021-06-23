<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('document_types')->insert(['type' => 'drivers_licence']);
        DB::table('document_types')->insert(['type' => 'passport']);
        DB::table('document_types')->insert(['type' => 'id_card']);
    }
}
