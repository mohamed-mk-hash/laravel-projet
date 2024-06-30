<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries  = array(
            array('code' => 'ALG', 'name' => 'Algiers'),
            array('code' => 'ORN', 'name' => 'Oran'),
            array('code' => 'CNS', 'name' => 'Constantine'),
            array('code' => 'ANN', 'name' => 'Annaba'),
            array('code' => 'BLD', 'name' => 'Blida'),
            array('code' => 'TLM', 'name' => 'Tlemcen'),
            array('code' => 'STF', 'name' => 'Sétif'),
            array('code' => 'BTN', 'name' => 'Batna'),
            array('code' => 'DLF', 'name' => 'Djelfa'),
            array('code' => 'BJA', 'name' => 'Béjaïa'),
            array('code' => 'MEA', 'name' => 'Médéa'),
            array('code' => 'CHL', 'name' => 'Chlef'),
            array('code' => 'SBA', 'name' => 'Sidi Bel Abbès'),
            array('code' => 'SKI', 'name' => 'Skikda'),
            array('code' => 'MSL', 'name' => 'Mostaganem'),
            array('code' => 'TBZ', 'name' => 'Tébessa'),
            array('code' => 'BEJ', 'name' => 'Béchar'),
            array('code' => 'TMR', 'name' => 'Tamanrasset'),
            array('code' => 'JIJ', 'name' => 'Jijel'),
            array('code' => 'TGR', 'name' => 'Tiaret'),
            array('code' => 'SKH', 'name' => 'Souk Ahras'),
            array('code' => 'GHA', 'name' => 'Ghardaïa'),
            array('code' => 'TIS', 'name' => 'Tissemsilt'),
            array('code' => 'OEB', 'name' => 'Oum El Bouaghi'),
            array('code' => 'RSB', 'name' => 'Relizane'),
            array('code' => 'SAH', 'name' => 'Saïda'),
            array('code' => 'MIL', 'name' => 'Mila'),
            array('code' => 'ADR', 'name' => 'Adrar'),
            array('code' => 'MOS', 'name' => 'M\'Sila'),
            array('code' => 'HGR', 'name' => 'Hassi Messaoud'),
            array('code' => 'LAG', 'name' => 'Laghouat'),
            array('code' => 'BUI', 'name' => 'Bouira'),
            array('code' => 'BRT', 'name' => 'Birtouta'),
            array('code' => 'ALX', 'name' => 'Aïn El Bell'),
            array('code' => 'KHE', 'name' => 'Khemis El Khechna'),
            array('code' => 'SDF', 'name' => 'Sidi Fredj'),
            array('code' => 'TLP', 'name' => 'Tipasa'),
            array('code' => 'LMD', 'name' => 'Lakhdaria'),
            array('code' => 'BLZ', 'name' => 'Blida'),
            array('code' => 'BOU', 'name' => 'Boumerdès'),
            array('code' => 'CZL', 'name' => 'Chlef'),
            array('code' => 'MLA', 'name' => 'Mila'),
            array('code' => 'MSD', 'name' => 'Mascara'),
            array('code' => 'MZI', 'name' => 'M\'Sila'),
            array('code' => 'TIP', 'name' => 'Tizi Ouzou'),
            array('code' => 'TLT', 'name' => 'Tissemsilt'),
            array('code' => 'SBA', 'name' => 'Sidi Bel Abbès'),
            array('code' => 'SIG', 'name' => 'Sidi Ghiles'),
            array('code' => 'OGX', 'name' => 'Oued Ghir'),
            array('code' => 'CHE', 'name' => 'Chettia'),
            array('code' => 'RER', 'name' => 'Reghaia'),
        );

        DB::table('countries')->insert($countries);
        
    }
}
