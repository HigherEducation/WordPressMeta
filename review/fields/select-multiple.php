<?php 

    $valueArray = is_array($value) ? $value : json_decode($value, true);

    if (!empty($label)) {

        echo '<label for="' . $name . '">' . $label . ($required ? '<span class="required">*</span>' : '') . '</label>';
        
    }

    echo $descriptionHTML;

?>

<select name="<?php echo $name; ?>" 
        multiple="multiple"
        <?php if ($required) echo ' required'; ?> 
        <?php if ($style) echo ' style="' . $style . '"'; ?>>

    <?php foreach ($options as $optionLabel => $optionValue) : ?>

        <option value="<?php echo $optionValue; ?>" <?php if (!empty($valueArray) && in_array($optionValue, $valueArray)) echo 'selected="selected"'; ?>><?php echo $optionLabel; ?></option>
    
    <?php endforeach; ?>

</select>
