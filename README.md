# [photos.jacklgreenberg.com](https://photos.jacklgreenberg.com)
A photo blog written in PHP.

This site is inspired by [my brother Aaron's blog](https://life.aaronjgreenberg.com). I wrote it in PHP using Mustache as a templater and Medoo as a database engine.

There is also a python script running on my server that listens for ftp uploads using the Watchdog library and reorients and resizes the image using the Python Image Library (PIL).

The site runs on an NGINX server on a Digital Ocean droplet. I use a nifty little snippet that redirects all subdirectory requests to the index.php file and have PHP handle routing requests to the right post.

Thanks Aaron for your help with the Python stuff.

:v:
