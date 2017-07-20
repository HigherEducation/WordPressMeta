<?php 

echo $labelHTML;
echo $descriptionHTML;

?>

<input list="<?php echo $name; ?>" 
       name="<?php echo $name; ?>" 
       value="<?php echo $value; ?>" 
       placeholder="<?php echo $placeholder; ?>" 
       class="input-datalist" 
       <?php if ($required) echo 'required'; ?> 
       <?php if ($maxlength) echo 'maxlength="' . $maxlength . '"'; ?> 
       <?php if ($readonly) echo 'readonly'; ?> />

<datalist id="<?php echo $name; ?>">

    <?php foreach ($options as $optionLabel => $optionValue) : ?>

        <option value="<?php echo $optionValue; ?>" <?php if ($optionValue = $value) echo 'selected="selected"'; ?>><?php echo $optionLabel; ?></option>

    <?php endforeach; ?>

</datalist>
