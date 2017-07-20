<?php echo $descriptionHTML; ?>

<label for="<?php echo $name; ?>">

    <input type="hidden" 
           name="<?php echo $name; ?>" 
           value="0" />

    <input type="checkbox" 
           id="<?php echo $name; ?>" 
           name="<?php echo $name; ?>" 
           value="1"
           <?php if (!empty($value)) echo ' checked="checked"'; ?>/>

<?php echo $label; ?>

</label>
