<?php

    echo $labelHTML;
    echo $descriptionHTML;
    
?>

<textarea name="<?php echo $name; ?>"
          <?php if ($required) echo ' required'; ?>
          <?php if ($style) echo ' style="' . $style . '"'; ?>
          <?php if ($readonly) echo ' readonly'; ?>><?php echo $value; ?></textarea>
