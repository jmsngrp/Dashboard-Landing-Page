<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\AreaAlias;
use Illuminate\Database\Seeder;

class AreaAliasSeeder extends Seeder
{
    public function run(): void
    {
        $aliases = [
            // Exact area name matches (for memo fields that use the full name)
            'Chippewa Valley' => 'Chippewa Valley',
            'Dane' => 'Dane',
            'Kenosha/ Racine' => 'Kenosha/Racine',
            'Kenosha/Racine' => 'Kenosha/Racine',
            'Milwaukee' => 'Milwaukee',
            'Northeast' => 'Northeast',
            'Sauk' => 'Sauk',
            'South Central' => 'South Central',
            'Western WI' => 'Western WI',
            'WI Statewide' => 'WI Statewide',

            // QBO memo abbreviations / variants
            'NEWI' => 'Northeast',
            'Statewide' => 'WI Statewide',
            'Lacrosse' => 'Western WI',
            'Western' => 'Western WI',
        ];

        $areaLookup = Area::pluck('id', 'name');

        foreach ($aliases as $aliasText => $areaName) {
            if (isset($areaLookup[$areaName])) {
                AreaAlias::updateOrCreate(
                    ['alias_text' => $aliasText],
                    ['area_id' => $areaLookup[$areaName]]
                );
            }
        }
    }
}
