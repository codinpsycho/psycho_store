<script type="text/javascript">
var tag_name = '<?php echo $tag_name; ?>';

var feed = new Instafeed({
        get: 'tagged',
        tagName: tag_name,
        limit:'14',
        accessToken: '1323742782.745c638.e4be8f24f0994e7289acd341cf2e250d'
    });
    feed.run(); 
</script>