<?php

namespace WordPressMeta;

/**
 * Meta Class
 *
 */
class Meta
{

    /**
     * Query
     * @var array
     */
    public $query = [];


    /**
     * Query Defaults
     * @var array
     */
    private $queryDefaults = [
        'post'      => [],
        'post_type' => [],
        'parent'    => [],
        'taxonomy'  => [],
        'template'  => [],
        'slug'      => '',
        'wp_args'   => [],
    ];


    /**
     * Fields
     * @var array
     */
    public $meta = [];


    /**
     * Posts
     * @var array
     */
    private $posts = [];


    /**
     * Results
     * @var array
     */
    private $results = [];


    /**
     * Argument
     * @var array
     */
    public $arguments = [
        'post_type'      => 'any',
        'post_status'    => 'any',
        'orderby'        => 'title',
        'order'          => 'ASC',
        'posts_per_page' => -1,
    ];


    /**
     * Build
     *
     */
    public function build()
    {
        add_action(
            'init',
            function () {
            // Check for needed `?meta=1` and logged in to even run.
                if (empty($_GET['meta']) || !current_user_can('administrator')) {
                    return;
                }

            // Setup Query/Meta/Arguments/Get Posts
                $this->setupQuery();
                $this->setupMeta();
                $this->setupArguments();
                $this->getPosts();

            // Update/Removal Database call.
                if (!empty($_GET['id']) && !empty($_GET['action'])) {
                    $this->updateDatabase($_GET['action']);
                    return;
                }

            // Show markup.
                $this->showMarkup();
            },
            15
        );
    }


    /**
     * Setup Query
     *
     */
    private function setupQuery()
    {
        if (empty($this->query)) {
            return;
        }

        // If Properties delcared as strings, convert to arrays.
        foreach ($this->query as $name => &$query) {
            if (!empty($query) &&
                !is_array($query) &&
                is_array($this->queryDefaults[$name])
            ) {
                $query = [$query];
            }
        }
    }


    /**
     * Set Fields
     *
     */
    private function setupMeta()
    {
        if (empty($this->meta)) {
            return;
        }

        // $_GET - Specific Meta Field.
        if (!empty($_GET['metakey']) && !empty($_GET['action'])) {
            // Update
            if ($_GET['action'] == 'update') {
                $this->meta = [
                    $_GET['metakey'] => ['*', $_GET['value']]
                ];
                return;
            }

            // Remove
            if ($_GET['action'] == 'remove') {
                $this->meta = [$_GET['metakey'] => '*'];
                return;
            }
        }

        // Check if single value, convert into array.
        if (!is_array($this->meta)) {
            $this->meta = [$this->meta => '*'];
            return;
        }

        // Set if numberic, set keys & wildcard.
        foreach ($this->meta as $key => $value) {
            // Set wildcard selector.
            if (is_numeric($key) && !is_array($value)) {
                unset($this->meta[$key]);
                $this->meta[$value] = '*';
                continue;
            }

            // Build meta array.
            $this->meta[$key] = $value;
        }
    }


    /**
     * Setup Arguments.
     *
     */
    private function setupArguments()
    {
        // Set custom WP arguments.
        if (!empty($this->query['wp_args'])) {
            $this->arguments = array_merge($this->$arguments, $this->query['wp_args']);
        }

        // Get post by slug
        if (!empty($this->query['slug'])) {
            $this->arguments['name'] = $this->query['slug'];
        }

        // Get posts, this trumps everything else.
        if (!empty($this->query['post'])) {
            $this->arguments['post__in'] = $this->query['post'];
        }

        // Get posts in certain post types.
        if (!empty($this->query['post_type'])) {
            $this->arguments['post_type'] = $this->query['post_type'];
        }

        // Get children posts with parent post.
        if (!empty($this->query['parent'])) {
            $this->arguments['post_parent__in'] = $this->query['parent'];
        }

        // Get post with taxonomy terms.
        if (!empty($this->query['taxonomy'])) {
            $taxonomyQuery = [];

            foreach ($this->query['taxonomy'] as $taxomony => $terms) {
                $termCheck       = is_array($terms) ? $terms[0] : $terms;
                $taxonomyQuery[] = [
                    'taxonomy' => $taxomony,
                    'field'    => is_numeric($termCheck) ? 'term_id' : 'slug',
                    'terms'    => $terms,
                ];
            }

            $this->arguments['tax_query'] = $taxonomyQuery;
            $this->arguments['tax_query']['relation'] = 'OR';
        }

        // Get post assigned to template.
        if (!empty($this->query['template'])) {
            $templates = $this->query['template'];
            $templatesArray['relation'] = 'OR';

            foreach ($templates as $template) {
                $templatesArray[] = [
                    'key'     => '_wp_page_template',
                    'value'   => $template,
                    'compare' => 'LIKE',
                ];
            }

            $this->arguments['meta_query'][] = $templatesArray;
        }

        // Get post by meta meta speicified.
        if (!empty($this->meta)) {
            $metaArray['relation'] = 'OR';

            // Loop defined meta meta.
            foreach ($this->meta as $key => $value) {
                $value     = is_array($value) ? $value[0] : $value;
                $metaArray = ['key' => $key];

                if ($value != '*') {
                    $metaArray = array_merge($metaArray, [
                        'value'   => $value,
                        'compare' => '=',
                    ]);
                }

                $this->arguments['meta_query'][] = $metaArray;
            }

            $this->arguments['meta_query']['relation'] = 'OR';
        }

        // $_GET - Specific post, if selected to remove/update.
        if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
            $this->arguments['page_id'] = $_GET['id'];
            unset($this->arguments['post__in']);
        }
    }


    /**
     * Get posts by defined query & meta.
     *
     */
    public function getPosts()
    {
        // Query results.
        $query = new \WP_Query($this->arguments);

        // Pluck only IDs from results in array.
        $this->posts = wp_list_pluck($query->posts, 'post_title', 'ID');
    }


    /**
     * Update Database Results, whether its removal or update of rows.
     *
     */
    public function updateDatabase($action)
    {
        // Only specific actions allowed.
        if ($action != 'remove' && $action != 'update') {
            return;
        }

        // Declare $wpdb global variable.
        global $wpdb;

        // Loop defined posts.
        foreach ($this->posts as $post => $title) {
            // Loop defined meta meta.
            foreach ($this->meta as $key => $value) {
                // Get value, whether its in an update array or individual string value.
                $metaValue = is_array($value) ? $value[0] : $value;
                $result    = false;

                // Where args.
                $where = [
                    'post_id'    => $post,
                    'meta_key'   => $key,
                ];

                // If specific, update where arg.
                if ($metaValue != '*') {
                    $where = array_merge($where, ['meta_value' => $metaValue]);
                }

                // Delete.
                if ($action == 'remove') {
                    $result = $wpdb->delete(
                        $wpdb->postmeta,
                        $where
                    );
                }

                // Update.
                if ($action == 'update' && is_array($value)) {
                    $result = $wpdb->update(
                        $wpdb->postmeta,
                        ['meta_value' => $value[1]],
                        $where
                    );
                }

                // If row effected, add to results list.
                if ($result) {
                    $this->results[$post][$key] = $value;
                }
            }
        }

        // Show markup.
        $this->showMarkup();
    }


    /**
     * Show Markup
     *
     */
    public function showMarkup()
    {
        // Inline styles.
        echo file_get_contents(dirname(__FILE__). '/assets/meta-styles.css');

        // Inline scripts.
        echo '<script>';
        echo file_get_contents(dirname(__FILE__). '/assets/meta-scripts.js');
        echo '</script>';

        // Dashboard view.
        include dirname(__FILE__) . '/views/meta-dashboard.php';

        // Halt everything else.
        die();
    }
}
