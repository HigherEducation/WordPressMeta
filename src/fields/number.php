<?php

    echo $labelHTML;
    echo $descriptionHTML;

?>

<input type="number" 
       name="<?php echo $name; ?>"
       <?php if ($value) echo ' value="' . esc_attr($value) . '"'; ?>
       <?php if ($style) echo ' style="' . $style . '"'; ?>
       <?php if ($placeholder) echo ' placeholder="' . $placeholder . '"'; ?>
       <?php if ($pattern) echo ' pattern="' . $pattern . '"'; ?>
       <?php if ($required) echo ' required'; ?>
       <?php if ($readonly) echo ' readonly'; ?>
       <?php if ($min) echo ' min="' . $min . '"'; ?>
       <?php if ($max) echo ' max="' . $max . '"'; ?>
       <?php if ($maxlength) echo ' maxlength="' . $maxlength . '"'; ?>/>
