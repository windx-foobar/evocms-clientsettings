<?php

namespace Database\Seeders;

use EvolutionCMS\Models\SystemEventname;
use Illuminate\Database\Seeder;

class ClientSettingsSeeder extends Seeder
{
    public function run()
    {
        $events = $this->getEvents();

        foreach ($events as $name) {
            SystemEventname::updateOrCreate([
                'name' => $name,
            ], [
                'service'   => 6,
                'groupname' => 'ClientSettings',
            ]);
        }
    }

    protected function getEvents()
    {
        return [
            'OnBeforeClientSettingsSave',
            'OnClientSettingsSave'
        ];
    }
}