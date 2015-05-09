{@ js_inline @}
    var disqus_shortname = '<?php print $disquisShortName; ?>';
    //var disqus_identifier = '<?php print $disquisIdentifier; ?>';
    //var disqus_title = '<?php print $disquisTitle; ?>';
    //var disqus_url = '<?php print $disquisUrlSite; ?>';

    (function() {
        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
        dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
    })();
{@ end @}
<div id="disqus_thread"></div>
