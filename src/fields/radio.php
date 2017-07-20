<?php

    if (!empty($label)) {

        echo '<span class="label">' . $label . ($required ? '<span class="required">*</span>' : '') . '</span>';

    }
    
    echo $description;

    foreach ($options as $optionLabel => $optionValue) : ?>

        <label for="<?php echo $name . '-' . $optionValue; ?>">

        <input type="radio" 
               id="<?php echo $name . '-' . $optionValue; ?>" 
               value="<?php echo $optionValue; ?>" 
               name="<?php echo $name; ?>"
               <?php if ($optionValue == $value) echo ' checked="checked"'; ?> />
                    <?php echo $optionLabel; ?>
               </label>

    <?php endforeach;
