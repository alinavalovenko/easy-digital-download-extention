<?php $path = ROOT_UPLOAD_FOLDER . DIRECTORY_SEPARATOR; ?>
<h1>Easy Digital Download Extension</h1>
<i>Choose directory with downloads, please: </i>
<form action="edit.php?post_type=download&page=edde_options&action=upload-ctp" method="post"
      enctype="multipart/form-data" id="scan-directory">

    <div class="folders">
        <span class="path"><?php echo $path; ?></span>
        <select name="foders-list" id="" class="folders-list">
            <option><?php echo NONE_ELEMENT; ?></option>
			<?php
			$folders = $folders = glob( $path . '*', GLOB_ONLYDIR );
			foreach ( $folders as $item ) {
				$value = substr( $item, strripos( $item, DIRECTORY_SEPARATOR ) + 1 ); ?>
                <option value="<?php echo $value; ?>"> <?php echo $value; ?> </option>
			<?php } ?>
        </select>
        <span class="clear-path"> < </span>
        <div class="date-option">
            <label for="date-of-selectin">Choose a date of the selection <br/><input id="date-of-selectin" type="date"/></label>
        </div>
		<?php $files = get_all_available_files( $path ); ?>
        <div class="available-files">
			<?php if ( $files ) {
				echo edde_dispay_available_files( $files );
			}
			?>
        </div>
    </div>
</form>
