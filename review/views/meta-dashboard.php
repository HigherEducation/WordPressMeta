<table class="metabox-results">
    <thead>
        <tr>
            <th>
                <h3>Meta Class</h3>
                <h4>$meta = new Meta();</h4>
                <p>$meta->meta = [
                <br />&nbsp;&nbsp;&nbsp;&nbsp;'meta_key' => 'meta_value',
                <br />&nbsp;&nbsp;&nbsp;&nbsp;'meta_key' => ['meta_value', 'new_value'],
                <br />&nbsp;&nbsp;&nbsp;&nbsp;'meta_key',
                <br />];</p>
                <p>$meta->query = [
                <br />&nbsp;&nbsp;&nbsp;&nbsp;'posts'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; => ['343', 234, 12],
                <br />&nbsp;&nbsp;&nbsp;&nbsp;'post_type' => ['post', 'cpt_post'],
                <br />&nbsp;&nbsp;&nbsp;&nbsp;'parents'&nbsp;&nbsp;&nbsp;&nbsp; => [45, 234, '23'],
                <br />&nbsp;&nbsp;&nbsp;&nbsp;'taxonomy' => ['taxonomy' => ['term_1', 'term_2']],
                <br />&nbsp;&nbsp;&nbsp;&nbsp;'templates' => ['front-page.php'],
                <br />&nbsp;&nbsp;&nbsp;&nbsp;'slug'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; => 'page-slug-name',
                <br />&nbsp;&nbsp;&nbsp;&nbsp;'wp_args'&nbsp;&nbsp;&nbsp;&nbsp; => [], refer to $args for WP_Query.
                <br />];</p>
                <h4>$meta->build();</h4>
            </th>

            <th>
                <h5>Meta</h5>
                <?php
                $hasUpdates  = false;
                    
                // Meta loop.
                if (!empty($this->meta)) {
                    foreach ($this->meta as $key => $value) {
                        $valueHTML = is_array($value) ? $value[0] . ' => ' . $value[1] : $value;
                        echo '<pre><strong>' . $key . '</strong> = ' . $valueHTML . '</pre>';

                        // If has updates.
                        if (is_array($value) && !$hasUpdates) {
                            $hasUpdates = true;
                        }
                    }
                } else {
                    echo '<pre>Meta data not set.</pre>';
                }
                ?>
                <hr />

                <h5>Query</h5>
                <?php
                $hasSettings = false;

                // Loop Queries.
                foreach ($this->query as $setting => $value) {
                    if (empty($value)) {
                        continue;
                    }

                    $hasSettings = true;

                    echo '<h6>' . $setting . '</h6>';
                    echo '<pre>';
                    echo is_array($value) ? print_r($value) : $value;
                    echo '</pre>';
                }

                // Has no settings.
                if (!$hasSettings) {
                    echo '<pre>Query arguments not set, all post(s) queried.</pre>';
                }
                ?>
                <hr />

                <h5>Results</h5>
                <?php
                // Count, Results || Posts, if not 0 posts.
                if (!empty($this->results)) {
                    echo '<pre>' . count($this->results) . ' Post(s) Effected</pre>';
                } elseif (!empty($this->posts) && empty($_GET['action'])) {
                    echo '<pre>' . count($this->posts) . ' Post(s)</pre>';
                } else {
                    echo '<pre>0 Post(s)</pre>';
                }

                // Actions.
                if (empty($_GET['action']) && !empty($this->posts)) {
                    if ($hasUpdates) {
                        $messageUpdate = '\'Update Meta from all ' . count($this->posts) . ' post(s)?\'';
                        echo '<a class="btn-update" 
                                 onclick="return confirmResults(' . $messageUpdate . ')" 
                                 href="' . home_url() . '?meta=1&id=all&action=update">Update All Meta Results</a>';
                    }
                    if (!empty($this->meta)) {
                        $messageRemove = '\'Remove Meta from all ' . count($this->posts) . ' post(s)?\'';
                        echo '<a class="btn-remove"
                                     onclick="return confirmResults(' . $messageRemove . ')" 
                                     href="' . home_url() . '?meta=1&id=all&action=remove">Remove All Meta Results</a>';
                    }
                }

                // Back.
                if (!empty($_GET['id'])) {
                    echo '<a class="btn-back" href="' . home_url() . '?meta=1">Back</a>';
                }
                ?>
            </th>
        </tr>
    </thead>

    <tfoot>
        <tr>
            <td colspan="2">Remove `build` method or `?meta=1` from url to exit this screen.</td>
        </tr>
    </tfoot>

    <?php
    // Results.
    if (!empty($this->results)) :
        foreach ($this->results as $post_id => $meta) :
    ?> 
        <tr>
            <td>
                <strong><?php echo $this->posts[$post_id]; ?> </strong><br />
                <strong>ID: </strong><?php echo $post_id; ?> <br /><br />
                <a href="<?php echo get_permalink($post_id); ?>">View Post</a> - 
                <a href="<?php echo admin_url('post.php?post=' . $post_id . '&action=edit'); ?>">Edit Post</a>
            </td>

            <td>
            <?php
            if (!empty($meta)) {
                foreach ($meta as $key => $value) {
                    echo '<pre class="' . $_GET['action'] . '"><strong>' . $key . '</strong> = ';

                    if ($_GET['action'] == 'remove') {
                        echo is_array($value) ? $value[0] : $value;
                    } elseif ($_GET['action'] == 'update' && is_array($value)) {
                        echo $value[0] . ' => ' . $value[1];
                    }

                    echo '</pre>';
                }
            }
            ?>
            </td>
        </tr>
    <?php
        endforeach;
    elseif (!empty($this->posts) && empty($_GET['action'])) :
        foreach ($this->posts as $post_id => $title) :
            $metaHTML  = '';
            $hasUpdate = false;
            $title     = strip_tags($title);

            // Meta values.
            if (!empty($this->meta)) {
                foreach ($this->meta as $key => $value) {
                    // Setup key & get stored value.
                    $definedValue = is_array($value) ? $value[0] : $value;
                    $storedValue  = get_post_meta($post_id, $key, true);

                    if ((metadata_exists('post', $post_id, $key)) &&
                        ($storedValue || $storedValue == '0' || $storedValue == '') &&
                        ($definedValue == '*' || $definedValue == $storedValue)
                    ) {
                        $metaHTML .= '<pre>';
                        $metaHTML .= '<strong>' . $key . '</strong> = ' . $storedValue;

                        // Remove link.
                        $metaHTML .= ' <a class="link-remove" 
                                          href="' . home_url() . '?meta=1&id=' . $post_id . '&metakey=' . $key . '&action=remove" 
                                          onclick="return confirmResults(\'Remove ' . strtoupper($key) . ' from ' . $title . '?\')">Remove</a>';

                        // Update link.
                        if (is_array($value)) {
                            $hasUpdate = true;
                            $metaHTML .= ' <a class="link-update" 
                                              href="' . home_url() . '?meta=1&id=' . $post_id . '&metakey=' . $key . '&value=' . $value[1] . '&action=update" 
                                              onclick="return confirmResults(\'Update ' . strtoupper($key) . ' from ' . $title . '?\')">Update</a>';
                        }

                        $metaHTML .= '</pre>';
                    }
                }
            } else {
                // Show all metafields.
                $meta = get_post_meta($post_id);

                foreach ($meta as $key => $value) {
                    $metaHTML .= '<pre>';
                    $metaHTML .= '<strong>' . $key . '</strong> = ' . $value[0];
                    $metaHTML .= ' <a class="link-remove" 
                                          href="' . home_url() . '?meta=1&id=' . $post_id . '&metakey=' . $key . '&action=remove" 
                                          onclick="return confirmResults(\'Remove ' . strtoupper($key) . ' from ' . $title . '?\')">Remove</a>';
                    $metaHTML .= '</pre>';
                }
            }
        ?> 

        <tr>
            <td>
                <strong><?php echo $title; ?></strong><br />
                <strong>ID: </strong><?php echo $post_id; ?><br /><br />
                <a href="<?php echo get_permalink($post_id); ?>">View</a> - 
                <a href="<?php echo admin_url('post.php?post=' . $post_id . '&action=edit'); ?>">Edit</a>
                <?php
                if (!empty($this->meta)) {
                    if ($hasUpdate) {
                        echo ' - <a href="' . home_url() . '?meta=1&id=' . $post_id . '&action=update" 
                                    onclick="return confirmResults(\'Update Meta from ' . $title . '?\')">Update</a>';
                    }
                    echo ' - <a href="' . home_url() . '?meta=1&id=' . $post_id . '&action=remove" 
                                onclick="return confirmResults(\'Remove Meta from ' . $title . '?\')">Remove</a>';
                }
                ?>
            </td>
            <td><?php echo $metaHTML; ?></td>
        </tr>

        <?php endforeach;
    endif; ?>

</table>