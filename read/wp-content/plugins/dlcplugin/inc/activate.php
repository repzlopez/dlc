<?php
/**
*@package DLCPlugin
*/

class DLCPluginActivate
{
     function activate() {
          flush_rewrite_rules();
     }

}
