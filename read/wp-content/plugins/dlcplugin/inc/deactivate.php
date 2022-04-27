<?php
/**
*@package DLCPlugin
*/

class DLCPluginDeactivate
{
     static function deactivate() {
          flush_rewrite_rules();
     }
}
