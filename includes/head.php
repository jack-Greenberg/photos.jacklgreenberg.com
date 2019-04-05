<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?php
            // This allows us to change the title of the blog
            // from the config file
            echo $ms->render('{{ title }}', array('title' => $title));
        ?>
    </title>

    <?php include(dirname(__FILE__).'/meta-tags.php') ?>

    <link rel="stylesheet" href="/build/bundle.css">
</head>
<body>
