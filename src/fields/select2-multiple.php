<?php

    $valueArray = is_array($value) ? $value : json_decode($value, true);
    
    echo $labelHTML;
    echo $descriptionHTML;

?>

<input name="<?php echo $name; ?>" 
       value="<?php if (!empty($valueArray)) echo implode(',', $valueArray); ?>" 
       type="hidden" 
       tabindex="-1" />

<select style="width: 100%;" multiple="multiple" <?php if ($required) echo 'required'; ?>>
    
    <?php if (!empty($valueArray)) : foreach ($valueArray as $value) : ?>

        <option value="<?php echo $value; ?>" selected="selected"><?php echo $optionLabel = array_search($value, $options); ?></option>
        <?php unset($options[$optionLabel]); ?>
    
    <?php endforeach; endif; ?>

    <?php foreach ($options as $optionLabel => $optionValue) : ?>
        
        <?php 
            if (empty($optionValue)) {
                continue;
            }
        ?>
        
        <option value="<?php echo $optionValue; ?>"><?php echo $optionLabel; ?></option>

    <?php endforeach; ?>

</select>
