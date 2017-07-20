<?php

    echo $labelHTML;
    echo $descriptionHTML;
    
?>

<input type="email" 
       name="<?php echo $name; ?>"
       <?php if ($value) echo ' value="' . $value . '"'; ?> 
       <?php if ($style) echo ' style="' . $style . '"'; ?> 
       <?php if ($placeholder) echo ' placeholder="' . $placeholder . '"'; ?> 
       <?php if ($pattern) echo ' pattern="' . $pattern . '"'; ?> 
       <?php if ($required) echo ' required'; ?> 
       <?php if ($maxlength) echo ' maxlength="' . $maxlength . '"'; ?> 
       <?php if ($readonly) echo ' readonly'; ?> />
