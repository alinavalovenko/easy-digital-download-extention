<h1>Easy Digital Download Extension</h1>
<i>Choose directory with downloads, please: </i>
<form action="edit.php?post_type=download&page=edde_options&action=upload-ctp" method="post"
      enctype="multipart/form-data">

    <div class="folders">
        <span class="path"><?php echo ROOT_UPLOAD_FOLDER . DIRECTORY_SEPARATOR; ?></span>
        <select name="foders-list" id="" class="folders-list">
            <option><?php echo NONE_ELEMENT; ?></option>
			<?php
			$folders = $folders = glob( ROOT_UPLOAD_FOLDER . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR );
			foreach ( $folders as $item ) {
			    $value = substr( $item, strripos( $item, DIRECTORY_SEPARATOR ) + 1 ); ?>
                <option value="<?php echo  $value; ?>"> <?php echo $value; ?> </option>
			<?php } ?>
        </select>
    </div>

    <input type="submit" value="Scan Directory">
</form>
