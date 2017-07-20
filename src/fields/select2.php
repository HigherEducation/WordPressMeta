<?php

    echo $labelHTML;
    echo $descriptionHTML;
    
?>

<select name="<?php echo $name; ?>" style="width: 100%;" <?php if ($required) echo 'required'; ?>>
    
    <?php foreach ($options as $optionLabel => $optionValue) : ?>

        <option value="<?php echo $optionValue; ?>" <?php if ($optionValue == $value) echo 'selected="selected"'; ?>><?php echo $optionLabel; ?></option>

    <?php endforeach; ?>

</select>
