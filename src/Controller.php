<?php

namespace EvolutionCMS\ClientSettings;

use EvolutionCMS\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class Controller
{
    public function show(Request $request, ClientSettings $cs, $tab = 'default')
    {
        $structure = $cs->loadStructure($tab);

        if (!empty($structure['menu']) && $structure['menu']['alias'] != 'default') {
            $head = $structure['menu'];
        } else {
            $head = [
                'caption' => __('cs::messages.module_title'),
                'icon'    => 'fa-cog',
            ];
        }

        if (empty($structure['tabs'])) {
            return view('cs::not_found', [
                'head' => $head,
                'path' => $cs->getConfigPath(),
            ]);
        }

        return view('cs::tabs', [
            'tabs'         => $structure['tabs'],
            'head'         => $head,
            'stay'         => $request->input('stay'),
            'values'       => evo()->config,
            'richtextinit' => $cs->getRichTextConfiguration($structure),
            'picker'       => $cs->getPickerConfiguration(),
        ]);
    }

    public function save(Request $request, ClientSettings $cs, $tab = 'default')
    {
        $evo = evo();
        $structure = $cs->loadStructure($tab);
        $fields = $cs->prepareSave($structure, $request->all());

        $evo->invokeEvent('OnBeforeClientSettingsSave', [
            'fields' => &$fields,
        ]);

        if (!empty($fields)) {
            foreach ($fields as $field) {
                SystemSetting::updateOrCreate([
                    'setting_name' => $field[0],
                ], [
                    'setting_value' => $field[1],
                ]);
            }
        }

        $evo->invokeEvent('OnDocFormSave', [
            'mode' => 'upd',
            'id'   => 0,
        ]);

        $evo->invokeEvent('OnClientSettingsSave', [
            'fields' => $fields,
        ]);

        $evo->clearCache('full');

        if ($request->input('stay') == 2) {
            return redirect()->route('cs::show', $tab);
        } else {
            return redirect(MODX_MANAGER_URL . '?a=2');
        }
    }
}
