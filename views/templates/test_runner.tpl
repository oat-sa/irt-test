<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8"/>
    <link rel="stylesheet" type="text/css" href="<?= TAOBASE_WWW ?>css/tao-main-style.css">
    <link rel="stylesheet" type="text/css" href="<?= BASE_WWW ?>css/test-runner.css">
    <script type="text/javascript" src="<?=TAOBASE_WWW?>js/lib/require.js"></script>
    <script type="text/javascript">
        (function(){
            require(['<?=get_data('client_config_url')?>'], function(){
                require(['irtTest/controller/runtime/testRunner'], function(testRunner){
                    testRunner.start(<?=json_encode(get_data('test_context'), JSON_HEX_QUOT | JSON_HEX_APOS)?>);
                });
            });
        }());
    </script>
  </head>
  <body class="tao-scope">
    <div id="navigation">
    	<button id="next" class="btn-info" type="button"><span class="icon-forward"></span>Next</button>
    </div>
  </body>
</html>