@extends('cs::layout')

@section('buttons')
    <div id="actions">
        <div class="btn-group">
            <div class="btn-group dropdown">
                <a id="Button1" class="btn btn-success" href="javascript:;" onclick="save_settings();">
                    <i class="fa fa-floppy-o"></i><span>{{ __('global.save') }}</span>
                </a>

                <span class="btn btn-success plus dropdown-toggle"></span>

                <select id="stay" name="stay">
                    <option id="stay2" value="2" @if ($stay == 2) selected="selected" @endif>{{ __('global.stay') }}</option>
                    <option id="stay3" value="" @empty($stay) selected="selected" @endempty>{{ __('global.close') }}</option>
                </select>
            </div>

            <a id="Button5" class="btn btn-secondary" href="{{ MODX_MANAGER_PATH }}">
                <i class="fa fa-times-circle"></i><span>{{ __('global.cancel') }}</span>
            </a>
        </div>
    </div>
@endsection

@section('body')
    <style>
        .image_for_field[data-image] {
            display: block;
            content: "";
            width: 120px;
            height: 120px;
            margin: .1rem .1rem 0 0;
            border: 1px #ccc solid;
            background: #fff 50% 50% no-repeat;
            background-size: contain;
            cursor: pointer;
        }
        .image_for_field[data-image=""] {
            display: none;
        }
    </style>

    <script>
        function evoRenderTvImageCheck(a) {
            var preview = document.getElementById('image_for_' + a.target.id),
                image = new Image;

            if (a.target.value) {
                image.src = "{{ evo()->getConfig('site_url') }}" + a.target.value;

                image.onerror = function () {
                    preview.style.backgroundImage = '';
                    preview.setAttribute('data-image', '');
                };

                image.onload = function () {
                    preview.style.backgroundImage = 'url(\'' + this.src + '?' + (new Date).getTime() + '\')';
                    preview.setAttribute('data-image', this.src);
                };
            } else {
                preview.style.backgroundImage = '';
                preview.setAttribute('data-image', '');
            }
        }
    </script>

    @foreach ($tabs as $name => $tab)
        <div class="tab-page" id="tab_{{ $name }}">
            <h2 class="tab">{{ $tab['caption'] }}</h2>

            <script type="text/javascript">
                tpSettings.addTabPage(document.getElementById('tab_{{ $name }}'));
            </script>

            <table border="0" cellspacing="0" cellpadding="3" style="font-size: inherit; line-height: inherit;">
                @if (!empty($tab['introtext']))
                    <tr>
                        <td class="warning" nowrap="" colspan="2">
                            {!! $tab['introtext'] !!}
                            <div class="split" style="margin-bottom: 20px; margin-top: 10px;"></div>
                        </td>
                    </tr>
                @endif

                @foreach ($tab['settings'] as $field => $options)
                    @if ($options['type'] == 'divider')
                        <tr>
                            <td colspan="2">
                                <h4 style="margin-top: 20px;">
                                    {!! $options['caption'] !!}
                                </h4>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td class="warning" nowrap="">
                                @if ($options['type'] === 'title')
                                    <div style="font-size:120%;padding:20px 0 10px;font-weight:bold;">
                                        {{ $options['caption'] }}
                                    </div>
                                @else
                                    {{ $options['caption'] }} <br>
                                    <small>{{ $params['prefix'] . $field }}</small>
                                @endif
                            </td>

                            <td data-type="{{ $options['type'] }}">
                                @if ($options['type'] !== 'title')
                                    <?php
                                        $value = $values[$params['prefix'] . $field] ?? false;

                                        $row = [
                                            'type'         => $options['type'],
                                            'name'         => $field,
                                            'caption'      => $options['caption'],
                                            'id'           => $field,
                                            'default_text' => isset($options['default_text']) && $value === false ? $options['default_text'] : '',
                                            'value'        => $value,
                                            'elements'     => isset($options['elements']) ? $options['elements'] : '',
                                        ];
                                    ?>

                                    {!! renderFormElement(
                                        $row['type'],
                                        $row['name'],
                                        '',
                                        $row['elements'],
                                        $row['value'] !== false ? $row['value'] : $row['default_text'],
                                        isset($options['style']) ? 'style="' . $options['style'] . '"' : '',
                                        $row
                                    ); !!}
                                @endif

                                @if (isset($options['note']))
                                    <div class="comment">
                                        {!! $options['note'] !!}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endif

                    @if ($options['type'] !== 'title')
                        <tr>
                            <td colspan="2"><div class="split"></div></td>
                        </tr>
                    @endif
                @endforeach
            </table>
        </div>
    @endforeach
@endsection

@push('scripts')
    {!! $richtextinit !!}

    {!! evo()->manager->loadDatePicker($picker['path']) !!}

    <script>
        jQuery('input.DatePicker').each(function() {
            new DatePicker(this, {
                yearOffset: {{ $picker['yearOffset'] }},
                format:     '{{ $picker['format'] }}',
                dayNames:   {!! __('global.dp_dayNames') !!},
                monthNames: {!! __('global.dp_monthNames') !!},
                startDay:   {!! __('global.dp_startDay') !!}
            });
        });

        function save_settings() {
            documentDirty = false;
            document.settings.save.click();
        }
    </script>
@endpush
