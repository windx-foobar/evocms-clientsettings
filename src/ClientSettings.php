<?php

namespace EvolutionCMS\ClientSettings;

class ClientSettings
{
    private $params = [];
    private $manager = [];

    public function __construct()
    {
        $this->params = [
            'config_path' => EVO_CORE_PATH . 'custom/clientsettings/',
            'menu' => isset($_GET['type']) && is_string($_GET['type']) ? $_GET['type'] : 'default',
        ];

        $manager_id = evo()->getLoginUserID('mgr');
        $this->manager = evo()->getUserInfo($manager_id);
    }

    public function getConfigPath()
    {
        return $this->params['config_path'];
    }

    public function loadStructure($tabName = false)
    {
        $tabs = [];

        foreach (glob($this->params['config_path'] . '*.php') as $file) {
            $tab = include $file;

            if (!empty($tab) && is_array($tab)) {
                if ($this->manager['role'] != 1) {
                    if (isset($tab['role']) && $tab['role'] != $this->manager['role']) {
                        continue;
                    }

                    if (isset($tab['roles'])) {
                        if (!is_array($tab['roles'])) {
                            $tab['roles'] = array_map('trim', explode(',', $tab['roles']));
                        }

                        if (!in_array($this->manager['role'], $tab['roles'])) {
                            continue;
                        }
                    }
                }

                $alias = pathinfo($file, PATHINFO_FILENAME);

                if (!isset($tab['menu'])) {
                    $tab['menu'] = [
                        'alias' => 'default',
                    ];
                }

                $menualias = $tab['menu']['alias'];

                if (!isset($tabs[$menualias])) {
                    $tabs[$menualias] = [
                        'menu' => $tab['menu'],
                        'tabs' => [],
                    ];
                }

                unset($tab['menu']);
                $tabs[$menualias]['tabs'][$alias] = $tab;
            }
        }

        if ($tabName) {
            if (isset($tabs[$tabName])) {
                return $tabs[$tabName];
            }

            return [];
        }

        return $tabs;
    }

    public function prepareSave($tabs, $data)
    {
        foreach ($tabs['tabs'] as $tab) {
            foreach (array_keys($tab['settings']) as $field) {
                $postfield = 'tv' . $field;

                $type = $tab['settings'][$field]['type'];
                $value = '';

                if (isset($data[$postfield])) {
                    $value = $data[$postfield];
                } else if (isset($tab['settings'][$field]['default_value'])) {
                    $value = $tab['settings'][$field]['default_value'];
                }

                switch ($type) {
                    case 'url':
                        if ($data[$postfield . '_prefix'] != '--') {
                            $value = str_replace(array (
                                "feed://",
                                "ftp://",
                                "http://",
                                "https://",
                                "mailto:"
                            ), "", $value);
                            $value = $data[$postfield . '_prefix'] . $value;
                        }
                        break;

                    case 'custom_tv:multitv': {
                        $json = @json_decode($value);

                        if (isset($json->fieldValue)) {
                            $value = json_encode($json->fieldValue, JSON_UNESCAPED_UNICODE);
                        }
                        break;
                    }

                    default:
                        if (is_array($value)) {
                            $value = implode("||", $value);
                        }
                        break;
                }

                $fields[$field] = [$this->params['prefix'] . $field, $value];
            }
        }

        return $fields;
    }

    public function getRichTextConfiguration($structure)
    {
        $evo = evo();
        $defaulteditor = $evo->getconfig('which_editor');

        $params = [
            'editor'   => $defaulteditor,
            'elements' => [],
            'options'  => [],
        ];

        foreach ($structure['tabs'] as $tab) {
            foreach ($tab['settings'] as $field => $options) {
                if ($options['type'] != 'richtext') {
                    continue;
                }

                $editor    = $defaulteditor;
                $tvoptions = [];

                if (!empty($options['options'])) {
                    $tvoptions = array_merge($tvoptions, $options['options']);
                }

                if (!empty($options['elements'])) {
                    $tvoptions = array_merge($tvoptions, $evo->parseProperties($options['elements']));
                }

                if (!empty($tvoptions) && isset($tvoptions['editor'])) {
                    $editor = $tvoptions['editor'];
                };

                $params['elements'][] = 'tv' . $field;
                $params['options']['tv' . $field] = $tvoptions;
            }
        }

        if (!empty($params)) {
            $richtextinit = $evo->invokeEvent('OnRichTextEditorInit', $params);

            if (is_array($richtextinit)) {
                return implode($richtextinit);
            }
        }

        return '';
    }

    public function getPickerConfiguration()
    {
        return [
            'yearOffset' => evo()->getConfig('datepicker_offset'),
            'format'     => evo()->getConfig('datetime_format') . ' hh:mm:00',
            'path'       => evo()->getConfig('mgr_date_picker_path'),
        ];
    }
}
