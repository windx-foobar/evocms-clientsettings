<?php

use EvolutionCMS\ClientSettings\ClientSettings;

Event::listen('evolution.OnManagerMenuPrerender', function($params) {
    $cs   = app(ClientSettings::class);
    $tabs = $cs->loadStructure();

    if (!empty($tabs)) {
        $menuparams = ['client_settings', 'main', '<i class="fa fa-cog"></i>' . __('cs::messages.module_title'), route('cs::show'), __('cs::messages.module_title'), '', '', 'main', 0, 100, ''];

        if (count($tabs) > 1) {
            $menuparams[3] = 'javscript:;';
            $menuparams[5] = 'return false;';
            $sort = 0;

            $params['menu']['client_settings_main'] = ['client_settings_main', 'client_settings', '<i class="fa fa-cog"></i>' . __('cs::messages.module_title'), route('cs::show'), __('cs::messages.module_title'), '', '', 'main', 0, $sort, ''];

            foreach ($tabs as $alias => $item) {
                if ($alias != 'default') {
                    $params['menu']['client_settings_' . $alias] = ['client_settings_' . $alias, 'client_settings', '<i class="fa ' . (isset($item['menu']['icon']) ? $item['menu']['icon'] : 'fa-cog') . '"></i>' . $item['menu']['caption'], route('cs::show', $alias), $item['menu']['caption'], '', '', 'main', 0, $sort += 10, ''];
                }
            }
        }

        $params['menu']['client_settings'] = $menuparams;
        return serialize($params['menu']);
    }
});
