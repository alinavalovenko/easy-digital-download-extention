<h1>Easy Digital Download Extension</h1>
<i>Choose directory with downloads, please: </i>
<form action="edit.php?post_type=download&page=edde_options&action=upload-ctp" method="post"
      enctype="multipart/form-data">

    <div class="folders">
        <span class="path"></span>
        <select name="foders-list" id="" class="folders-list">
			<?php
			$folders = $folders = glob( get_home_path() . '*', GLOB_ONLYDIR );
			foreach ( $folders as $item ) { ?>
                <option value="<?php echo $item ?>"> <?php echo $item ?> </option>
			<?php } ?>
        </select>
    </div>

    <input type="submit" value="Scan Directory">
</form>
