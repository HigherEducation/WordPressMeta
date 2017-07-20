# Composer Setup
```
"require": {
    "WordPressMeta": "dev-master"
},
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/HigherEducation/WordPressMeta"
    }
],
```

Install Meta Library project by using composer:
```
composer install
```

To Update:
```
composer update
```

If Composer is not isntall on your machine, run:
```
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
```

Setup Autoloader in `functions.php`:
```
require_once ABSPATH . '../../vendor/autoload.php';
```


# Metabox
Used to build simple custom metabox.

## Initialize
```
use HigherEducation\Metabox;

$metabox = new Metabox();
$metabox->setSettings(array(
   ... Settings
));
$metabox->setFields(array(
    ... Fields
));
$metabox->build();
```

## Settings
```
array(
    'title'      => '',
    'id'         => '',
    'message'    => '',
    'post_type'  => array() || '',
    'post'       => array() || '',
    'parent'     => array() || '',
    'template'   => array() || '',
    'taxonomy'   => array(),
    'location'   => 'normal',
    'priority'   => 'high',
    'dependency' => array()
)
```

##### title
(string) (Required) Title of the meta box.

##### id
(string) (Optional) Unique ID of metabox, if not entered will autogenerate a random number custom-metabox-`1-100000`.

##### message
(string) (Optional) Can contain html tags.

##### location
(string) (Optional) The context within the screen where the boxes should display. Available contexts vary from screen to screen. Post edit screen contexts include 'normal', 'side', and 'advanced'. Comments screen contexts include 'normal' and 'side'. Default `normal`.

##### priority
(string) (Optional) The priority within the context where the boxes should show ('high', 'low'). Default `high`.

##### post_type
(string|array) (Optional) The post type which to show the box. Accepts a single post type or an array of post types. Default `all post` types.

##### post
(string|array) (Optional) Metabox will only display on specific posts entered into array. e.g. `array('242', '234', '12')` or `'12'`

##### parent
(string|array) (Optional) Metabox will only display on children posts of specific parent post(s) entered into array. e.g. `array('242')` or `'242'`

##### taxonomy
(array) (Optional) Metabox will only display on terms that are specified in taxonomies. e.g. `array('category' => 'rankings')` or `array('category' => 'rankings', 'post_tag' => 'schools')` or `array('category' => array('rankings', 'schools'), 'post_tag' => 'schools')`

##### template
(string|array) (Optional) Metabox will only display on specified template. e.g. `array('front-page.php', 'contact-page.php')` or `'front-page.php'`

##### dependency 
(array) (Optional) Metabox dependency based on metafield conditions. Conditionals include `>, <, >=, <=, ==, !=, between, outside`. For `>, <, >=, <=`, only accept numeric values. For `==, !=`, can accept single value or array of values to match. e.g. `value` or `[3, "343", 55, "Value"]`. For `between & outside` only accepts array of values. e.g. `[2, 6]`. 

```
'dependency' => array(
    'key'       => 'metakey',
    'value'     => 'value', // Format: 'value' || '[value, value, ...]'
    'condition' => '>, <, >=, <=, ==, !=, between, outside',
)
```



## Fields
```
$metabox->setFields(array(
    ...
));
```

##### Checkbox
```
'metakey' => array(
    'type'        => 'checkbox',
    'label'       => '', 
    'description' => '', 
    'dependency'  => array(),
    'callback'    => '',
),
```
Checkboxes, values are always `0` & `1`. e.g. If checked `metakey` will have meta value of `1` otherwise it'll be `0`. 


##### Date
```
'metakey' => array(
    'type'        => 'date', 
    'value'       => '', // Format: YYYY-MM-DD
    'label'       => '', 
    'description' => '', 
    'required'    => false,
    'readonly'    => false,
    'pattern'     => '',
    'dependency'  => array(),
    'style'       => '',
    'callback'    => '',
),
```


##### Editor
```
'metakey' => array(
    'type'        => 'editor', 
    'value'       => '',
    'label'       => '', 
    'description' => '', 
    'dependency'  => array(),
    'callback'    => '',
),
```


##### Email
```
'metakey' => array(
    'type'        => 'email', 
    'value'       => '',
    'label'       => '', 
    'placeholder' => '', 
    'description' => '', 
    'required'    => false,
    'readonly'    => false,
    'pattern'     => '',
    'maxlength'   => '',
    'dependency'  => array(),
    'style'       => '',
    'callback'    => '',
),
```


##### Number
```
'metakey' => array(
    'type'        => 'number', 
    'value'       => '',
    'label'       => '', 
    'placeholder' => '', 
    'description' => '', 
    'required'    => false,
    'readonly'    => false,
    'pattern'     => '',
    'min'         => '',
    'max'         => '',
    'maxlength'   => '',
    'dependency'  => array(),
    'style'       => '',
    'callback'    => '',
),
```


##### Radio
```
'metakey' => array(
    'type'        => 'radio', 
    'label'       => '', 
    'description' => '', 
    'dependency'  => array(),
    'options'     => array('Category 1' => 'value', 'Category 1' => 'value', ...),
    'callback'    => '',
),
```


##### Select
```
'metakey' => array(
    'type'        => 'select-multiple', 
    'label'       => '', 
    'description' => '', 
    'required'    => false, 
    'dependency'  => array(),
    'options'     => array('Label' => 'value', 'Label' => 'value', ...),
    'style'       => '',
    'callback'    => '',
),
```


##### Select Multiple
```
'metakey' => array(
    'type'        => 'select-multiple', 
    'label'       => '', 
    'description' => '', 
    'required'    => false, 
    'dependency'  => array(),
    'options'     => array('Label' => 'value', 'Label' => 'value', ...),
    'style'       => '',
    'callback'    => '',
),
```


##### Select2
```
'metakey' => array(
    'type'        => 'select2', 
    'label'       => '', 
    'description' => '', 
    'required'    => false, 
    'dependency'  => array(),
    'options'     => array('Label' => 'value', 'Label' => 'value', ...),
    'callback'    => '',
),
```


##### Select2 Multiple
```
'metakey' => array(
    'type'        => 'select2-multiple', 
    'label'       => '', 
    'description' => '', 
    'required'    => false, 
    'dependency'  => array(),
    'options'     => array('Label' => 'value', 'Label' => 'value', ...),
    'callback'    => '',
),
```


##### Tel
```
'metakey' => array(
    'type'        => 'tel', 
    'value'       => '',
    'label'       => '', 
    'placeholder' => '', 
    'description' => '', 
    'required'    => false,
    'readonly'    => false,
    'pattern'     => '', 
    'maxlength'   => '',
    'dependency'  => array(),
    'style'       => '',
    'callback'    => '',
),
```
For validation, use `pattern` attribute with `tel` input if browser doesn't support HTML5 `tel` validation.


##### Text
```
'metakey' => array(
    'type'        => 'text', 
    'value'       => '',
    'label'       => '', 
    'placeholder' => '', 
    'description' => '', 
    'required'    => false,
    'readonly'    => false,
    'pattern'     => '',
    'maxlength'   => '',
    'dependency'  => array(),
    'style'       => '',
    'callback'    => '',
),
```


##### Textarea
```
'metakey' => array(
    'type'        => 'textarea', 
    'value'       => '',
    'label'       => '', 
    'description' => '', 
    'required'    => false,
    'readonly'    => false,
    'dependency'  => array(),
    'style'       => '',
    'callback'    => '',
),
```


##### URL
```
'metakey' => array(
    'type'        => 'url', 
    'value'       => '',
    'label'       => '', 
    'placeholder' => '', 
    'description' => '', 
    'required'    => false,
    'readonly'    => false,
    'pattern'     => '',
    'maxlength'   => '',
    'dependency'  => array(),
    'style'       => '',
    'callback'    => '',
),
```

##### Snippet
```
'Lorem <strong>ipsum dolor</strong> sit amet, consectetur adipisicing elit. Architecto et repudiandae earum placeat ea, tempore error facilis, assumenda atque accusamus mollitia laborum fugit accusantium. Eligendi <a href="#">unde ratione</a> deleniti corrupti, quae.<hr />',
```
Can be inserted between metakeys to create titles, breaks, messages or additional instructions. 


### Field Properties Information

##### type
(string) (Required)

##### value 
(string) (Optional)

##### label 
(string) (Required)

##### placeholder 
(string) (Optional)

##### description 
(string) (Optional) Can contain html tags.

##### options 
(array) (Optional) e.g. `'options'  => array('Category 1' => 'value', 'Category 1' => 'value', ...)`

##### pattern 
(string) (Optional) The pattern attribute specifies a regular expression that the <input> element's value is checked against. Note: The pattern attribute works with the following input types: text, date, search, url, number, tel, and email. e.g. `[A-Za-z]{3}`

##### required 
(bool) (Optional)

##### readonly 
(bool) (Optional) Note: The readonly attribute works with the following input types: text, date, search, url, number, tel, and email.

##### maxlength
(number) (Optional) Note: The maxlength attribute works with the following input types: text, date, search, url, number, tel, and email.

##### min 
(number) (Optional) Note: The min attribute works with the following input types: number.

##### max 
(number) (Optional) Note: The max attribute works with the following input types: number.

##### dependency 
(array) (Optional) Field dependency based on other metafield conditions. Conditionals include `>, <, >=, <=, ==, !=, between, outside`. For `>, <, >=, <=`, only accept numeric values. For `==, !=`, can accept single value or array of values to match. e.g. `value` or `[3, "343", 55, "Value"]`. For `between & outside` only accepts array of values. e.g. `[2, 6]`. 

##### style 
(string) (Optional) Give the field a little more style love. Work with all fields except, select2s, checkbox, and radio field types. 

##### subfields 
(array) (Optional) Nested children fields. This gets saved as an array. NOTE: `Dependency & Callbacks` attributes don't work with subfield attributes.

##### repeater 
(bool) (Optional) Whether or not nested children subfields are repeated. NOTE: `Editor` fields currently do not work in repeaters.

```
'dependency' => array(
    'key'       => 'metakey',
    'value'     => 'value', // Format: 'value' || '[value, value, ...]'
    'condition' => '>, <, >=, <=, ==, !=, between, outside',
)
```


## Example Usage:
```
$widgetXYZ = new Metabox();
$widgetXYZ->setSettings(array(
   'title'    => 'Title',
   'message'  => '',
   'post_type' => array('post', 'pages'),
   'location' => 'side',
   'priority' => 'high',
));
$widgetXYZ->setFields(array(
    'title' => array(
        'value'       => 'Default Title Name',
        'type'        => 'text',
        'description' => '',
        'label'       => 'Title',
        'placeholder' => 'Title',
    ),
    'cta' => array(
        'type'  => 'text',
        'label' => 'Title',
        'placeholder' => 'tittle',
        'pattern' => '[A-Za-z]{3}',
        'required' => true,
        'dependency' => array(
            'key'       => 'title',
            'value'     => 'value',
            'condition' => '==',
        )
    ),
    'degree_level_id' => array(
        'type'  => 'select',
        'label' => 'Degree Level',
        'options'  => $arrayMap,
    ),
    'category_id' => array(
        'type'  => 'select',
        'label' => 'Category',
        'options'  => array('Category 1' => 'value', 'Category 1' => 'value', ...),  
    ),
    'subject_id' => array(
        'type'  => 'text',
        'label' => 'Degree Level',
        'options'  => array('Category 1' => 'value', 'Category 1' => 'value', ...),   
        'dependency' => array(
            'key'       => 'category_id',
            'value'     => '[1, 6]',
            'condition' => '==',
        )
    ),
));
$widgetXYZ->build();
```


#### Example Usage: Using Class Shorthand Arguments
```
new Metabox(array(
    'settings' => array(
        'title'    => 'Custom Meta Goodies',
        'post_type' => array('post', 'page'), 
        'location' => 'normal',
        'priority' => 'high',
    ),
    'fields'  => array(
        'test_title' => array(
            'type'  => 'text',
            'label' => 'Text Type',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.',
            'placeholder' => 'Enter It Here',
        ),
        'tessst' => array(
            'type'  => 'textarea',
            'label' => 'Textarea',
            'placeholder' => 'cta'
        ),
        'category_id' => array(
            'type'  => 'select',
            'label' => 'Select Options',
            'options'  => array('Category 1' => 'value', 'Category 1' => 'value', ...),  
        ),
        'cheeee' => array(
            'type'  => 'select_multiple',
            'label' => 'Select - Multiple',
            'options'  => array('Category 1' => 'value', 'Category 1' => 'value', ...),  
            'dependency' => array(
                'key'       => 'category_id',
                'value'     => '[1, 6]',
                'condition' => '==',
            )
        ),
        '<span class="label">Checkbox Inputs</span>',
        'test_checkbox' => array(
            'type'  => 'checkbox',
            'label' => 'Do you like mess?',
        ),
        'test_checkbox_2' => array(
            'type'  => 'checkbox',
            'label' => 'Do you like him?',
            'dependency' => array(
                'key'       => 'test_checkbox',
                'value'     => 1,
                'condition' => '==',
            )
        ),
        'test_radio' => array(
            'type'  => 'radio',
            'label' => 'Radio Inputs',
            'options'  => array('Category 1' => 'value', 'Category 1' => 'value', ...),  
        ),
        'test_favorite' => array(
            'type'  => 'datalist',
            'label' => 'Datalist Input',
            'options'  => array('Category 1' => 'value', 'Category 1' => 'value', ...),  
        ),
        'edissstor' => array(
            'type'  => 'editor',
            'label' => 'Editor Type',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Totam corporis alias, aperiam soluta cumque ipsum accusamus nihil libero aliquam, fugiat?',
        ),
    )
));
```

# Meta Class
Used to retrieve meta fields with defined search query/fields. **Requirements to Use:** Logged into Wordpress as an Administrator and add `?meta=1` to the domain url, this brings up the Dashboard once you iniialize class below. 

### Initialize
```
use HigherEducation\Meta;

$meta = new Meta;
$meta->setMeta(array(
    ... Meta
));
$meta->setQuery(array(
    ... Query
));
$meta->init();
```
## Meta
```
array(
    'metakey',
    'metakey' => '3',
    'metakey' => array('searchValue', 'replaceableValue'),
    'metakey' => array('2', '5'),
    'metakey' => array('1', '5'),
    'metakey'
)
```
#### Single Key: `'metakey'`
Pulls in posts with attached `metakey`, no matter the value. Basically equals this `metakey => *`. You may also do this. `'metakey' => '*'`

#### Key with Value: `'metakey' => 'value'` 
Pulls in posts where `metakey` equals `value`. You may also look for metakeys that exists but have no value. `'metakey' => ''`

#### Key with Array Value: `'metakey' => array('searchValue', 'replaceableValue')` 
Pulls in posts like above using the `searchValue`. This allows you to replace `metakey` values from the dashboard.


## Query
```
array(
    'post'      => array(),
    'post_type' => array(),
    'parent'    => array(),
    'taxonomy'  => array('taxonomy' => array('term', 'term')),
    'template'  => array(),
    'slug'      => '',
    'wp_args'   => Refer to: https://codex.wordpress.org/Class_Reference/WP_Query,
)
```
##### post
(string|array) (Optional) Specific posts entered into array. e.g. `array('242', '234', '12')`

##### post_type
(string|array) (Optional) Accepts a single post type or an array of post types. Default is all post types.

##### parent
(string|array) (Optional) Only children posts of specific parent post(s) entered into array. e.g. `array('242')`

##### taxonomy
(array) (Optional) Only terms that are specified in taxonomies. e.g. `array('category' => 'rankings')` or `array('category' => 'rankings', 'post_tag' => 'schools')` or `array('category' => array('rankings', 'schools'), 'post_tag' => 'schools')`

##### template
(string|array) (Optional) Only terms that are specified in template. e.g. `array('front-page.php', 'contact-page.php')`

##### slug
(string) (Optional) Only page specified by slug. e.g. `'page-slug-name'`

##### wp_args
(array) (Optional) Refer to $args for WP_Query: https://codex.wordpress.org/Class_Reference/WP_Query.


#### Example Usage
```
$meta = new Meta;
$meta->setMeta(array(
    'degree_level_id' => 2,
    'editorial_only_page' => array(0, 2)
));
$meta->setQuery(array(
    'post'      => array('5474', '6505', '6504', '6503', '234234322'),
    'post_type' => array('post'),
    'parent'    => array('172'),
    'taxonomy'  => array('category' => array('college-rankings')),
    'template'  => array('page-banner.php'),
    'slug'      => 'page-slug-name',
    'wp_args'   => array( 'category__in' => array( 5, 6 ) ),
));
$meta->init();
```


#### Example Usage: Using Class Shorthand Arguments
```
new Meta(array(
    'query' => array(
        'post'      => array('5474', '6505', '6504', '6503', '234234322'),
        'post_type' => array('post'),
        'parent'    => array('172'),
        'taxonomy'  => array('category' => array('college-rankings')),
        'template'  => array('page-banner.php'),
        'slug'      => 'page-slug-name',
        'wp_args'   => array( 'category__in' => array( 5, 6 ) ),
    ),
    'meta' => array(
        'degree_level_id' => '1', 
        'editorial_only_page'
    )
));
```