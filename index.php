<?php
// Main file, where the blog pages are served from. It first checks to see if
// the URI includes anything beyond the trailing slash (i.e. jacklgreenberg.com/1),
// and if it does, looks for an image post with the same number. If it is found,
// just the image is generated. If not, there is a 404. If it is just an
// ordinary request (jacklgreenberg.com), then it shows all the posts
//
// On the NGINX server, I have routed all URIs matching ^/([0-9]+)
// (any numbers) to this page, index.php, preserving arguments:
//
// location ~ ^/([0-9]+) {
//     try_files $uri /index.php$is_args$args;
// }
/*
* Each page requires these two
*/
require(dirname(__FILE__).'/includes/functions.php');
require(dirname(__FILE__).'/includes/head.php');

/*
* Get the request uri, and remove the starting slash
*/
$uri = $_SERVER['REQUEST_URI'];
$request = preg_replace('/\//', '', $uri);

/*
* Function to get and render post data
*/
function render_post($id) {
    global $db;
    global $ms_template;

    $post_data = $db->select("posts", ["caption", "date", "filetype"], [
        "id" => $id
    ])[0];

    $filetype = $post_data["filetype"];
    $caption = $post_data["caption"];
    $date = $post_data["date"];
    $images = array_diff(scandir("images/".strval($id), SCANDIR_SORT_DESCENDING), array('..', '.'));

    $srcset = '';
    $prefix = "/images/" . $id . '/';
    foreach($images as $image) {
        $tmp = explode('-', $image)[1];
        $size = explode('.', $tmp)[0];

        $srcset .= $prefix . $image . ' ' . $size . 'w, ';
    };

    $src = $prefix . $images[0];

    echo $ms_template->render('post', array('id' => $id, 'caption' => $caption, 'date' => $date, 'srcset' => $srcset, 'src' => $src));
};


if ($request == ""):
    /*
    * Render the header
    */
    echo $ms_template->render('header', array('title' => $title, 'author' => $author, 'url' => $url));
    echo("<main>");

    /*
    * Get
    */
    $int_post_numbers = [];
    $post_numbers = array_diff(scandir("images", SCANDIR_SORT_DESCENDING), array('..', '.'));
    foreach($post_numbers as $number) {
        array_push($int_post_numbers, intval($number));
    };

    rsort($int_post_numbers);

    foreach($int_post_numbers as $post) {
        $id = $post;
        render_post($id);
    };

else: // If the URI request includes a number (i.e. jacklgreenberg.com/1)
    echo $ms_template->render('header', array('title' => $title, 'author' => $author, 'url' => $url));
    echo("<main>");

    $id = $request;
    if (in_array($id, scandir("images"))) {
        render_post($id); // Just render that post
    } else {
        echo "Post not found :("; // Or else throw an error
    };
    /*
    * TODO: try using `try`/`catch`/`throw`, maybe it is the right use case, maybe not...
    */

endif;
/*
* These two things are always included
*/
echo("</main>");
require(dirname(__FILE__).'/includes/foot.php');
?>
