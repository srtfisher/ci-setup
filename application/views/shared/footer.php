
<!-- End actual Content -->

<?php
$this->carabiner->display('js');

do_action('footer'); ?>
<script type="text/javascript">
<?php if (has_action('js_footer')) : ?>
$(document).ready(function () {
     <?php do_action('js_footer'); ?>
});
<?php endif; ?>

<?php do_action('footer_script'); ?>
</script>

<?php
// The jQuery Loading item
?>
<div id="loading" style="display:none;"> Loading&hellip; </div>
</body></html>