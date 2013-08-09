//<?php
/**
 * NoFollow plugin for MODx Evolution
 * 
 * Adds attribute rel="nofollow" to external links.
 * 
 * System events:
 * OnWebPagePrerender
 * 
 * Plugin configuration:
 * &whitelist=Friendly domains;text;
 * 
 * @version 1.1
 * @author  Denis Komlev <deniskomlev@hotmail.com>
 */

if ($modx->event->name == 'OnWebPagePrerender')
{
    $content = $modx->documentOutput;

    // Collect all link tags on the page to $matches array
    preg_match_all(
        "/<a [^>]*?href=[\"\'](.*?)[\"\'][^>]*>.*?<\/a>/im",
        $content,
        $matches
    );

    if (!empty($matches[0]))
    {
        // Get the list of friendly domains as array
        if (!empty($whitelist)) {
            $whitelist = explode(',', str_replace(' ', '', $whitelist));
        }
        else {
            $whitelist = array();
        }

        // Add own domain to whitelist
        $site_url = parse_url($modx->config['site_url']);
        if (isset($site_url['host'])) { $whitelist[] = $site_url['host']; }

        foreach ($matches[0] as $key => $tag) {
            // Get and parse the value of "href" attribute
            $href = trim($matches[1][$key]);
            $url_info = parse_url($href);

            // Skip non-external links (if the link destination is not
            // beginning with "http://" or "https://")
            if (!isset($url_info['scheme']) ||
                !in_array($url_info['scheme'], array('http', 'https'))) {
                continue;
            }

            // Skip already nofollowed links (if the link tag has occurence
            // of rel attribute with "nofollow" value)
            if (preg_match("/ rel=[\"\']nofollow[\"\']/i", $tag)) {
                continue;
            }

            // Skip the domains included to white list (regardless to "www")
            $domain = preg_replace("/^www\./i", '', $url_info['host']);
            if (in_array($domain, $whitelist)) {
                continue;
            }

            // Add "nofollow" attribute to the beginning of a link tag
            // and replace occurrences of old tag with new one
            $new_tag = preg_replace("/^<a/i", '<a rel="nofollow"', $tag);
            $content = str_replace($tag, $new_tag, $content);
        }

        // Set the new content to document output
        $modx->documentOutput = $content;
    }
}