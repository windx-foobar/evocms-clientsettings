<?php include_once MODX_MANAGER_PATH . 'includes/header.inc.php' ?>

<h1>
    <i class="fa {{ $head['icon'] }}"></i>
    {{ $head['caption'] }}
</h1>

<form name="settings" method="post" id="mutate">
    @yield('buttons')

    <div class="sectionBody" id="settingsPane">
        <div class="tab-pane" id="documentPane">
            <script type="text/javascript">
                var tpSettings = new WebFXTabPane(document.getElementById('documentPane'), {{ evo()->getConfig('remember_last_tab') == 1 ? 'true' : 'false' }} );
            </script>

            @yield('body')
        </div>
    </div>

    <input type="submit" name="save" style="display: none;">
</form>

@stack('scripts')

<?php include_once MODX_MANAGER_PATH . 'includes/footer.inc.php' ?>
