<?php

namespace WordPressMeta;

/**
 * Metabox Class
 *
 */
class Metabox
{

    /**
     * Settings for Metabox
     * @var array
     */
    private $settings = array(
        'title'      => 'Custom Meta',
        'id'         => '',
        'message'    => '',
        'post_type'  => array(),
        'post'       => array(),
        'parent'     => array(),
        'taxonomy'   => array(),
        'template'   => array(),
        'operator'   => 'OR',
        'location'   => 'normal',
        'priority'   => 'high',
        'dependency' => array(),
    );


    /**
     * Fields/Messages for Metabox
     * @var array
     */
    private $fields = array();


    /**
     * Acceptable Field Attributes for Metabox
     * @var array
     */
    private $fieldAttributes = array(
        'type'        => '',
        'value'       => '',
        'label'       => '',
        'description' => '',
        'placeholder' => '',
        'options'     => array(),
        'required'    => false,
        'pattern'     => '',
        'style'       => '',
        'min'         => '',
        'max'         => '',
        'maxlength'   => '',
        'readonly'    => false,
        'dependency'  => array(),
        'repeater'    => false,
        'subfields'   => array(),
        'callback'    => '',
    );


    /**
     * A nonce name value used when validating the save request
     * @var string
     */
    private $nonceName = 'custom_metabox_nonce';


    /**
     * A nonce action used when validating the save request
     * @var string
     */
    private $nonceAction = 'customMetaboxNonceAction';


    /**
     * Assets already loaded.
     * @var boolean
     */
    static $assetsLoaded = false;


    /**
     * Construct
     * @param  array $args
     *
     */
    public function __construct($args = array())
    {

        // Check if shortcut arguments are set.
        if (empty($args) || (empty($args['fields']) && empty($args['settings']['message']))) {

            return;

        }

        // Set settings if argument settings are set in shortcut arguments.
        if (!empty($args['settings']) && is_array($args['settings'])) {

            $this->setSettings($args['settings']);

        }

        // Set fields if argument fields are set in shortcut arguments.
        if (!empty($args['fields']) && is_array($args['fields'])) {

            $this->setFields($args['fields']);

        }

        // Iniitate shortcut setup.
        $this->build();

    }


    /**
     * Build
     *
     */
    public function build()
    {

        add_action('init', function() {

            // Initiate if in adminstrator area.
            if ($this->validateSettings()) {

                // Check to see if assets already loaded, only load assets once.
                if (!self::$assetsLoaded) {

                    self::$assetsLoaded = true;

                    $this->addCDNAssets();
                    $this->assetsHeader();
                    $this->assetsFooter();
                    $this->removeWPCustomMetabox();

                }

                // Add & Save Metabox Init.
                $this->initMetabox();
                $this->initSavePost();
            }

        }, 
            15
        );

    }


    /**
     * Set Settings
     * @param  array $args
     *
     */
    public function setSettings($args = array())
    {

        // Check if arguments are empty.
        if (empty($args)) {

            return;

        }

        // Set Metabox ID
        if (empty($args['id'])) {

            $args['id'] = rand(1, 100000);

        }

        // Set post type to array if string.
        if (!empty($args['post_type']) && !is_array($args['post_type'])) {

            $args['post_type'] = array($args['post_type']);

        }

        // Set metabox object settings.
        $this->settings = array_merge($this->settings, $args);

    }


    /**
     * Set Fields
     * @param  array $args
     *
     */
    public function setFields($args = array())
    {
        // Check if arguments are empty.
        if (empty($args)) {

            return;

        }

        // Set metabox object fields.
        $this->fields = $args;

    }


    /**
     * Custom Meta Assets(CSS & JS)
     *
     */
    private function addCDNAssets()
    {

        add_action('admin_enqueue_scripts', function()
        {

            // Remove Old ACF Versions of Select2
            wp_deregister_script('select2');
            wp_deregister_style('select2');

            // Add styles to administrator area.
            wp_register_style('select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css', false, '1.0.0');
            wp_enqueue_style('select2-css');

            // Add script to administrator area.
            wp_enqueue_script('select2-js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array('jquery', 'jquery-ui-sortable'), null, false);
            wp_enqueue_script('select2-js');

        });

    }


    /**
     * Header - Custom Meta Assets
     *
     */
    private function assetsHeader()
    {

        add_action('admin_head', function()
        {

            echo file_get_contents(dirname(__FILE__). '/assets/metabox-styles.css');

        });

    }


    /**
     * Footer - Custom Meta Assets
     *
     */
    private function assetsFooter()
    {

        add_action('admin_footer', function()
        {

            echo file_get_contents(dirname(__FILE__) . '/assets/metabox-scripts.js');

        },
            20
        );

    }


    /**
     * Remove the default Wordpress post custom metabox from
     * all Posts/Pages/CPT that have a Custom Metabox added.
     * This simplifies the user selections to only one area on
     * admin post area.
     *
     * @return void
     */
    private function removeWPCustomMetabox()
    {

        add_action('admin_menu', function()
        {

            foreach (get_post_types('', 'names') as $post_type) {

                remove_meta_box('postcustom', $post_type, 'normal');

            }

        });

    }


    /**
     * Build the HTML for the Metabox fields defined by the $fields property.
     * Set up the nonce field, re-populate the input values and create.
     * Include minimal CSS/JS for making the metabox UI.
     *
     * @return void
     */
    private function initMetabox()
    {

        add_action('add_meta_boxes', function()
        {

            // Add Metabox.
            add_meta_box(
                'custom-metabox-' . $this->settings['id'],
                $this->settings['title'],
                array($this, 'showMetaboxHTML'),
                $this->settings['operator'] == 'AND' ? $this->settings['post_type'] : null,
                $this->settings['location'],
                $this->settings['priority']
            );

            // Check for dependency.
            if (empty($this->settings['dependency'])) {

                return;

            }

            // Add dependency class to metabox container to initially hide.
            foreach (get_post_types('', 'names') as $post_type) {

                add_filter('postbox_classes_' . $post_type . '_custom-metabox-' . $this->settings['id'], array($this, 'addMetaboxClass'));

            }

        });

    }


    /**
     * Show Metabox Markup
     * @param  object $post
     *
     */
    public function showMetaboxHTML($post)
    {

        // Add nonce for security and authentication.
        wp_nonce_field($this->nonceAction, $this->nonceName);

        // Check for dependency.
        if (!empty($this->settings['dependency'])) {

            $dataDependency  = !empty($this->settings['dependency']['key']) ? 'data-dependency-key="' . $this->settings['dependency']['key'] . '"' : '';
            $dataDependency .= !empty($this->settings['dependency']['value']) ? 'data-dependency-value=\'' . $this->settings['dependency']['value'] . '\'' : '';
            $dataDependency .= !empty($this->settings['dependency']['condition']) ? 'data-dependency-condition="' . $this->settings['dependency']['condition'] . '"' : '';
            echo '<span ' . $dataDependency . ' data-key="custom-metabox-' . $this->settings['id'] . '" data-type="metabox"></span>';

        }

        // Show metabox introduction message.
        if (!empty($this->settings['message'])) {

            echo '<div class="message">' . $this->settings['message'] . '</div>';

        }

        // Check if fields are empty.
        if (empty($this->fields)) {

            return;

        }

        $this->getFields($post, $this->fields);

    }


    /**
     * Meta Field Markup
     * @param  string $name, array $attributes, string $storedValue
     *
     */
    private function getFields($post, $fields)
    {

        // Show headers and fields.
        foreach ($fields as $name => $attributes) {

            // Stored Value & Attributes.
            $storedValue = get_post_meta($post->ID, $name, true);
            $attributes  = array_merge($this->fieldAttributes, $attributes);

            // HTML/Message Snippet.
            if (empty($attributes['type']) && is_int($name)) {

                echo '<div class="snippet">' . $attributes . '</div>';
                continue;

            }

            // Subfields.
            if (!empty($attributes['subfields'])) {

                // Stored Value.
                $valueArray     = json_decode($storedValue);
                $groupSubfields = array();

                // Dependencies.
                $dataDependency = '';
                if (!empty($attributes['dependency'])) {

                    $dataDependency  = !empty($attributes['dependency']['key']) ? 'data-dependency-key="' . $attributes['dependency']['key'] . '"' : '';
                    $dataDependency .= !empty($attributes['dependency']['value']) ? 'data-dependency-value=\'' . $attributes['dependency']['value'] . '\'' : '';
                    $dataDependency .= !empty($attributes['dependency']['condition']) ? 'data-dependency-condition="' . $attributes['dependency']['condition'] . '"' : '';

                }

                // Show field by input type.
                echo '<div class="input-group type-object" ' . $dataDependency . ' data-key="' . $name . '" data-type="object">';

                // Set label if exists.
                if (!empty($attributes['label'])) {

                    echo '<span class="label">' . $attributes['label'] . '</span>';

                }

                // Set description if exists.
                if (!empty($attributes['description'])) {

                    echo '<div class="description">' . $attributes['description'] . '</div>';

                }

                // Merge Fields & StoredValues
                if (!empty($valueArray) && (is_array($valueArray) || is_object($valueArray))) {

                    // Repeater Values
                    if ($attributes['repeater'] && array_key_exists('0', $valueArray)) {

                        // Loop Values.
                        foreach ($valueArray as $groupIndex => $groupInputs) {

                            $groupSubfields[$groupIndex] = $attributes['subfields'];

                            // Update Names & Values.
                            foreach ($attributes['subfields'] as $inputName => $inputAttributes) {

                                $subName = $name . '[' . $groupIndex. '][' . $inputName . ']';
                                $groupSubfields[$groupIndex][$inputName]['value'] = !empty($groupInputs->$inputName) ? $groupInputs->$inputName : '';
                                $groupSubfields[$groupIndex][$subName] = $groupSubfields[$groupIndex][$inputName];

                                unset($groupSubfields[$groupIndex][$inputName]);

                            }

                        }

                    } else {

                        // Nested Values
                        $groupSubfields[0] = $attributes['subfields'];

                        // If repeater, & changed to nested grab first.
                        if (array_key_exists('0', $valueArray)) {

                            $valueArray = $valueArray[0];

                        }

                        // Update Names & Values.
                        foreach ($attributes['subfields'] as $inputName => $inputAttributes) {

                            $subName = $attributes['repeater'] ? $name . '[0][' . $inputName . ']' : $name . '[' . $inputName . ']';
                            $groupSubfields[0][$inputName]['value'] = !empty($valueArray->$inputName) ? $valueArray->$inputName : '';
                            $groupSubfields[0][$subName] = $groupSubfields[0][$inputName];

                            unset($groupSubfields[0][$inputName]);

                        }

                    }

                } else {

                    // Update Names.
                    foreach ($attributes['subfields'] as $subName => $subAttributes) {

                        unset($attributes['subfields'][$subName]);

                        $subName = $attributes['repeater'] ? $name . '[0][' . $subName . ']' : $name . '[' . $subName . ']';
                        $attributes['subfields'][$subName] = $subAttributes;

                    }

                }

                // Subfields.
                echo '<div data-subfields-container="' . $name . '" ' . ($attributes['repeater'] ? 'data-repeater="true"' : '') . '>';

                    if (!empty($groupSubfields)) {

                        foreach ($groupSubfields as $subfields) {

                            echo '<div data-subfields-parent="' . $name . '">';

                                $this->getFields($post, $subfields);

                                if (count($groupSubfields) > 1) {

                                    echo '<button class="button-remove">x</button>';

                                }

                            echo '</div>';

                        }

                    } else {

                        echo '<div data-subfields-parent="' . $name . '">';
                            $this->getFields($post, $attributes['subfields']);
                        echo '</div>';

                    }

                // End of Input Area.
                echo '</div>';

                // If repeater.
                if ($attributes['repeater']) {

                    echo '<div class="button-controls"><button data-subfields-add="' . $name . '" class="button-primary">Add</button></div>';

                }

                // End of Field Group.
                echo '</div>';

                // Jump to next field.
                continue;

            }

            // Select
            if ($attributes['type'] == 'select-multiple') {

                $name .= '[]';

            }

            // Field type exists.
            if (empty($attributes['type']) || !file_exists(dirname(__FILE__) . '/fields/' . $attributes['type'] . '.php')) {

                echo '<div class="notification-error"><strong>Type</strong> is empty or field does not exist.</div>';
                continue;

            }

            // Show Fields.
            $this->getFieldHTML($name, $attributes, $storedValue);

        }

    }


    /**
     * Meta Field Markup
     * @param  string $name, array $attributes, string $storedValue
     *
     */
    private function getFieldHTML($name, $attributes, $storedValue = '')
    {

        // Extract array elements into variables.
        extract($attributes);

        // Set stored value if exists.
        $value = !empty($storedValue) ? $storedValue : $value;

        // Set label if exists.
        $labelHTML = !empty($label) ? '<label for="' . $name . '">' . $label . ($required ? '<span class="required">*</span>' : '') . '</label>' : '';

        // Set description if exists.
        $descriptionHTML = !empty($description) ? '<div class="description">' . $description . '</div>' : '';

        // Dependencies.
        $dataDependency = '';
        if (!empty($dependency)) {

            $dataDependency  = !empty($dependency['key']) ? 'data-dependency-key="' . $dependency['key'] . '"' : '';
            $dataDependency .= !empty($dependency['value']) ? 'data-dependency-value=\'' . $dependency['value'] . '\'' : '';
            $dataDependency .= !empty($dependency['condition']) ? 'data-dependency-condition="' . $dependency['condition'] . '"' : '';

        }

        // Show field by input type.
        echo '<div class="input-group type-' . $type . '" ' . $dataDependency . ' data-key="' . $name . '" data-type="' . $type . '">';
            include dirname(__FILE__) . '/fields/' . $type . '.php';
        echo '</div>';

    }


    /**
     * Save Meta Field Inputs
     *
     */
    private function initSavePost()
    {

        add_action('save_post', function($postID)
        {

            // Validation Check.
            if (!$this->validateSaveRequest($postID)) {

                return;

            }

            // Loop through Meta Fields.
            foreach ($this->fields as $name => $attributes) {

                // Check if key exists.
                if (!array_key_exists($name, $_POST)) {

                    continue;

                }

                // Callback.
                if (!$this->callback($postID, $name, $attributes)) {

                    continue;

                }

                // Subfields.
                if (!empty($attributes['subfields'])) {

                    $hasValue = false;

                    // If repeater check deeper array.
                    if ($attributes['repeater']) {

                        foreach ($_POST[$name] as &$inputGroups) {

                            foreach ($inputGroups as $inputName => &$inputValue) {

                                if (empty($inputValue)) {

                                    continue;

                                }

                                if (!empty($attributes['subfields'][$inputName]) && $attributes['subfields'][$inputName]['type'] == 'select2-multiple') {

                                    $inputValue = explode(',', $inputValue);

                                }

                                $hasValue = true;

                            }

                        }

                    } else {

                        foreach ($_POST[$name] as $inputName => &$inputValue) {

                            if (empty($inputValue)) {

                                continue;

                            }

                            if (!empty($attributes['subfields'][$inputName]) && $attributes['subfields'][$inputName]['type'] == 'select2-multiple') {

                                $inputValue = explode(',', $inputValue);

                            }

                            $hasValue = true;

                        }

                    }

                    // Check if multidimensional array is empty.
                    if (!$hasValue) {

                        $_POST[$name] = '';

                    }

                }

                // Delete Post Meta Fields if Empty.
                if (empty($_POST[$name]) && $attributes['type'] != 'radio') {

                    delete_post_meta(
                        $postID,
                        $name
                    );
                    continue;

                }

                // select2-multiple: Convert Value to Array.
                if (!empty($attributes['type']) && $attributes['type'] == 'select2-multiple') {

                    $_POST[$name] = explode(',', $_POST[$name]);

                }

                // If is value is array
                if (is_array($_POST[$name])) {

                    $_POST[$name] = json_encode($_POST[$name], JSON_PRETTY_PRINT|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS);

                    if (JSON_ERROR_NONE != json_last_error()) {

                        throw new \Exception('JSON encoding failed with error:' . json_last_error_msg());

                    }

                }

                // Update Post Meta Fields.
                update_post_meta(
                    $postID,
                    $name,
                    $_POST[$name]
                );

            }

        });

    }

    /**
     * Run through a series of tests to confirm that the save request
     * Is valid, including checking the nonce set up in metaboxHTML()
     *
     * @return bool does the save request validate?
     */
    private function validateSaveRequest($postID)
    {

        // Check if $_POST request.
        if (empty($_POST)) {

            return false;

        }

        // Add nonce for security and authentication
        if (!isset($_POST[$this->nonceName])) {

            return false;

        }

        // Check if nonce is set & nonce is valid.
        if (!wp_verify_nonce($_POST[$this->nonceName], $this->nonceAction)) {

            return false;

        }

        // Check if user has permissions to save data.
        if (!current_user_can('edit_post', $postID)) {

            return false;

        }

        // Check if not an autosave.
        if (wp_is_post_autosave($postID)) {

            return false;

        }

        // Check if not a revision.
        if (wp_is_post_revision($postID)) {

            return false;

        }

        // Check if fields are empty.
        if (empty($this->fields)) {

            return false;

        }

        return true;

    }


    /**
     * Run through a series of tests to confirm that the metabox can be
     * added to the current post, based off of the defined settings.
     * Checks is Admin, Post Exists, Posts, Parent, Taxonomy,
     * and Template to display metabox.
     *
     * @return bool does the save request validate?
     */
    private function validateSettings()
    {
        // vars.
        $hasPost = $hasPostType = $hasParent = $hasTaxonomy = $hasTemplates = ($this->settings['operator'] == 'AND');
        $postID  = !empty($_GET['post']) ? $_GET['post'] : '';

        // Check if Post Save.
        if (!empty($_POST['post_ID'])) {

            $postID = $_POST['post_ID'];

        }

        // Check if admin.
        if (!is_admin()) {

            return false;

        }

        // Check if post is empty.
        if (empty($postID)) {

            return false;

        }

        // Check if settings.posts is set.
        if (!empty($this->settings['post'])) {

            $hasPost = false;

            if (
                (is_array($this->settings['post']) && in_array($postID, $this->settings['post'])) ||
                ($this->settings['post'] == $postID)
            ) {

                $hasPost = true;

            }

        }

        // Check if settings.parent is set.
        if (!empty($this->settings['post_type'])) {

            $hasPostType = false;

            if (
                (is_array($this->settings['post_type']) && in_array(get_post_type($postID), $this->settings['post_type'])) ||
                $this->settings['post_type'] == get_post_type($postID)
            ) {
                
                $hasPostType = true;

            }

        }

        // Check if settings.parent is set.
        if (!empty($this->settings['parent'])) {

            $hasParent = false;

            if (
                (is_array($this->settings['parent']) && in_array(wp_get_post_parent_id($postID), $this->settings['parent'])) ||
                $this->settings['parent'] == wp_get_post_parent_id($postID)
            ) {

                $hasParent = true;

            }

        }

        // Check if settings.taxonomy is set.
        if (!empty($this->settings['taxonomy']) && is_array($this->settings['taxonomy'])) {

            $hasTaxonomy = false;

            // Loop taxonomies & match terms for post.
            foreach ($this->settings['taxonomy'] as $taxonomyAllowed => $termsAllowed) {

                // Get post terms based on taxonomy.
                $postTerms = get_the_terms($postID, $taxonomyAllowed);

                // Check for if post terms.
                if (is_wp_error($postTerms) || empty($postTerms)) {

                    continue;

                }

                // Loop through post terms in taxonomy.
                foreach ($postTerms as $postTerm) {

                    // Check terms compared to user inputted array of values.
                    if (
                        (
                            is_array($termsAllowed) &&
                            (
                                in_array($postTerm->slug, $termsAllowed) ||
                                in_array($postTerm->term_id, $termsAllowed) ||
                                in_array($postTerm->name, $termsAllowed)
                            )
                        ) ||
                        (
                            $postTerm->slug == $termsAllowed ||
                            $postTerm->term_id == $termsAllowed ||
                            $postTerm->name == $termsAllowed
                        )
                    ) {

                        $hasTaxonomy = true;
                        break;

                    }

                }

                // Stop Loop once found true
                if ($hasTaxonomy) {

                    break;

                }

            }

        }

        // Check if settings.template is set.
        if (!empty($this->settings['template'])) {

            // vars.
            $hasTemplates = false;
            $template     = get_page_template_slug($postID);

            if (
                !empty($template) &&
                (
                    (is_array($this->settings['template']) && in_array($template, $this->settings['template'])) ||
                    ($this->settings['template'] == $template)
                )
            ) {

                $hasTemplates = true;

            }

        }

        // Final Check
        if ($this->settings['operator'] == 'AND') {

            if (!$hasPost || !$hasPostType || !$hasParent || !$hasTaxonomy || !$hasTemplates) {

                return false;

            } else {

                return true;

            }

        } else {

            if ($hasPost || $hasPostType || $hasParent || $hasTaxonomy || $hasTemplates) {

                return true;

            } else {

                return false;
            }

        }

    }


    /**
     * Callback Method
     *
     */
    private function callback($postID, $name, $attributes)
    {

        // Check if callback exists.
        if (empty($attributes['callback'])) {

            return true;

        }

        // Call self instead of class.
        $attributes['callback'] = str_replace(array('Metabox::', 'Metabox->'), 'self::', $attributes['callback']);

        // Check if is callable.
        if (!is_callable($attributes['callback'])) {

            return true;

        }

        // Data attribute of callback.
        $data = array(
            'postID'     => $postID,
            'metakey'    => $name,
            'metavalue'  => !empty($_POST[$name]) ? $_POST[$name] : '',
            'attributes' => $attributes
        );

        // Callback Function, pass in value as data argument.
        return (call_user_func($attributes['callback'], $data) === false) ? false : true;

    }


    /**
     * Add Metabox Class
     * @param  array $classes
     *
     */
    public function addMetaboxClass($classes)
    {

        $classes[] = 'data-dependency-key';
        return $classes;

    }

}


/**
 * Add Plugin Callback Methods.
 *
 */
foreach (glob(__DIR__ . '/plugins/*.php') as $filename) {

    require_once $filename;

}
