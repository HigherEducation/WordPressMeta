<?php

    echo $labelHTML;
    echo $descriptionHTML;

?>

<input type="date" 
       name="<?php echo $name; ?>" 
       value="<?php echo $value; ?>" 
       placeholder="<?php echo $placeholder; ?>"
       <?php if ($style) echo ' style="' . $style . '"'; ?>
       <?php if ($pattern) echo ' pattern="' . $pattern . '"'; ?>
       <?php if ($required) echo ' required'; ?>
       <?php if ($readonly) echo ' readonly'; ?>/>
