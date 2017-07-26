<?php

    echo $labelHTML;
    echo $descriptionHTML;

?>

<input type="tel" 
       name="<?php echo $name; ?>"
       <?php if ($value) echo ' value="' . esc_attr($value) . '"'; ?>
       <?php if ($style) echo ' style="' . $style . '"'; ?>
       <?php if ($placeholder) echo ' placeholder="' . $placeholder . '"'; ?>
       <?php if ($pattern) echo ' pattern="' . $pattern . '"'; ?>
       <?php if ($readonly) echo ' readonly'; ?>
       <?php if ($maxlength) echo ' maxlength="' . $maxlength . '"'; ?>
       <?php if ($required) echo ' required'; ?>/>
