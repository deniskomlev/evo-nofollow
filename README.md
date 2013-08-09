NoFollow plugin for MODx Evolution
==================================

This plugin allows to automatic extend external links with rel="nofollow" attribute.

Usage:

1) Create the new "NoFollow" plugin from Manager area.
2) Check the "OnWebPagePrerender" event on plugin's "System Events" tab.
3) Add following plugin configuration:

&whitelist=Friendly domains;text;example.com,test.com

where "example.com,test.com" is a list of external domains which should not be affected
by the plugin.