<h3>
    Importer des <?php echo $pageTitle ?>
</h3>

<form action=" <?= get_site_url() ?>/wp-admin/admin.php?page=<?php echo $this->pageSlug ?>" method="post"
      enctype="multipart/form-data" style="padding: 10px;">


    <label class="button button-primary customize" for="fileToUpload">
        Import
    </label>

    <input
            class="input-control customize"
            style="display: none"
            type="file"
            name="fileToUpload"
            id="fileToUpload"
            onclick="document.getElementById('submitbutton').disabled = false"
    >

    <input
            disabled
            class="button button-primary customize  "
            type="submit" value="Submit"
            id="submitbutton"
    >

    <a
            target="_blank"
            class=" button button-primary customize load-customize hide-if-no-customize"
            href="../wp-content/plugins/<?= $this->pluginName ?>/uploads/<?php echo $this->postType ?>.xlsx"
    >
        Exporter
        tous
        (Excel)
    </a>
</form>


<script>

</script>