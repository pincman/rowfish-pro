=== Phoenix Media Rename ===
Contributors: crossi72, ShadowsDweller
Tags: media, file, image, attachment, rename, retitle
Requires at least: 5.0
Tested up to: 6.0.1
Stable tag: 3.8.8
Requires PHP: 7.1
License: GPL3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Donate link: https://paypal.me/crossi72

The Phoenix Media Rename plugin allows you to easily rename (and retitle) your media files, once uploaded.

== Description ==

Greatly improve your SEO: rename your media files with the "Phoenix Media Rename" plugin.

A complete guide to use and configure Phoenix Media Rename is available at [Phoenix Media Rename official page](https://www.eurosoftlab.com/en/phoenix-media-rename/)

== Installation ==

1. Upload `phoenix-media-rename` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. It is done! You can go to any media file single page and will notice the new field "Filename". Bulk edit is also available at the "Media" listing page!

== Frequently Asked Questions ==

= How to rename files translated with WPML? =

Make sure that the WPML Multilingual CMS and WPML Media Translation plugins are activated (you can find more information on WPML in the [WPML official site](https://wpml.org/)).

Add one or more images to your media library, WPML Media Translation will add the localized version to your media library.

Go to WPML -> Media Translation and edit the localized version of your media files, you can change both the image title and the file attached to localized version.

Rename images using Phoenix Media Rename.

Go to your media library, select the rename operation, check all the files you want to rename, edit the filenames and confirm the bulk operation, Phoenix Media Rename and WPML will do all the rest!

Please look at the [screenshoot section](https://wordpress.org/plugins/phoenix-media-rename/screenshots/) to see how the process works.

= How to serialize file names? =

You can serialize file names enclosing the variable parts between { and }, this will cause Phoenix Media Rename to generate a sequence of filenames.

The variable part has to be an integer number, but can start with 0 (i.e. 0023).

Please note: check all the files you want to serialize and write the name for the first one, see screenshot-3.jpg for reference.

= How to avoid removing accents in file name? =

Go to the Phoenix Media Rename settings page and unckeck "Remove accents" option, this will prevent accents sanitization.
Please note: avoid accents sanitization can cause broken URL on some hosting.

= How to avoid processing revisions? =

Go to the Phoenix Media Rename settings page and unckeck "Update Revisions" option, this will prevent revions update.
Please note: avoid revision processing will speed up the rename process, but will cause broken media file link in you revert a post to an older version.

= How to rename a single media? =

Go to the Media section of the admin panel and open a media of your choice. You will see a new field named "Filename" and your current filename in it. Change it to whatever you want the new name to be and hit the "Update" button.

= How to bulk rename medias? =

Go to the Media section of the admin panel, select the "Rename" or "Rename & Retitle" bulk action (depending on if you want the media get retitled too) from the dropdown, check the medias you would like to rename and change their filenames using the "Filename" fields at the last column. When you are done, hit the "Apply" button and let the plugin do its magic!
Please note: the bulk action are only available in List View, switch from Grid View to List View if needed.

= How to rename medias getting the filename from the title of the post they are attached to? =

Go to the Media section of the admin panel, select the "Rename from Post" or "Rename & Retitle from Post" bulk action (depending on if you want the media get retitled too) from the dropdown, check the medias you would like to rename and hit the "Apply" button

= Can I use the plugin to rename medias via code? =

Sure, you can use the "do_rename" static function, located at the Phoenix_Media_Rename class. Prototype: do_rename($attachment_id, $new_filename, $retitle = 0, $title_from_post = 0, $name_from_post = 0, $check_post_parent = true ). On success the function returns 1, and on error - the error message.

= Can I integrate 3rd party plugins? =

Sure, you can use the "pmr_renaming_successful" that fires when the rename process is complete and returns old and new filenames.

Feel free to ask for more custom actions!

Example:

`/**
* my_callback
*
* @param [string] $old_filename
* @param [string] $new_filename
*/
function my_callback( $old_filename, $new_filename ) {
	// your code
}

add_action( 'pmr_renaming_successful', 'my_callback', 10, 2 );`

= Do you need more features? =

If you would like more features, such as automatic renaming, or a dashboard for renaming files, have a look at the freemium plugin [Media File Renamer](https://wordpress.org/plugins/media-file-renamer). Yes, we are friends!

== Screenshots ==

4. screenshot-1.jpg
5. screenshot-2.jpg
6. screenshot-3.jpg

== Changelog ==

= 3.8.8 =
* fixed issue when header constant is empty and trailer constant has a value

= 3.8.7 =
* fixed issue in trailer constant cleaning

= 3.8.6 =
* fixed title in retitle bulk action when filename requires sanitization

= 3.8.5 =
* fixed renaming webp and avif files generated by ShortPixel when the filename contains more than one "-" character

= 3.8.4 =
* fixed renaming webp and avif files generated by ShortPixel

= 3.8.3 =
* fixed renaming webp and avif files generated by ShortPixel

= 3.8.2 =
* fixed "Retitle from post" behavior when there is no post attached to the media

= 3.8.1 =
* added new bulk operation "Retitle from post", that allows to change only the title of the file getting it from the title of the post to which the media is attached

= 3.8.0 =
* added new bulk operation "Retitle", that allows to change only the title of the file

= 3.7.9 =
* fixed compatibility issue with Elementor background images

= 3.7.8 =
* fixed compatibility issue with Elementor custom page templates

= 3.7.7 =
* fixed compatibility issue with Elementor

= 3.7.6 =
* fixed compatibility issue with WMPL

= 3.7.5 =
* fixed compatibility issue with WordPress 6.0

= 3.7.4 =
* added support for Rank Math redirections

= 3.7.3 =
* fixed error in thumbnail regenation from wp-cli for a file renamed using Phoenix Media Rename

= 3.7.2 =
* fixed "filename already present" error when many files could get the same name

= 3.7.1 =
* fixed "filename already present" error when a file has been uploaded in a subfolder (thanks to @niwin for the help addressing the issue)

= 3.7.0 =
* changed "rename from post" management
* fixed some case of infinite loop during bulk rename

= 3.6.0 =
* added option to serialize filename if a file with the target filename is present

= 3.5.1 =
* grouped options "Sanitize filenames" and "Remove accents" and added integrity check

= 3.5.0 =
* added option to convert filename to lowercase when the action "rename from post" or "rename and retitle from post" is selected

= 3.4.10 =
* changed iterator in metadata deserialization (thanks to @alx359 for addressing the issue)

= 3.4.8 =
* minor optimization in ShortPixel metadata management

= 3.4.7 =
* added parameters to add constants to the beginning and end of file names

= 3.4.6 =
* added termination to ajax code

= 3.4.5 =
* prevented initialization of the plugin on frontend

= 3.4.4 =
* added security check in ajax rename process

= 3.4.2 =
* added translation support for title in settings page

= 3.4.0 =
* added option to disable filename sanitization

= 3.3.1 =
* fixed compatibility issue with Elementor

= 3.3.0 =
* added option to create a 301 redirection when a file has been renamed. The option is disabled by default, to enable it visit the Phoenix Media Rename settings page. (thanks to @ortonom for the suggestion)
Please Note: the free plugin [Redirection](https://wordpress.org/plugins/redirection/) is required to add and manage the 301 redirection

= 3.2.5 =
* fixed minor issue in "option debug" management

= 3.2.4 =
* fixed compatibility issue with WPML (many thanks to Diego Pereira for his help)

= 3.2.2 =
* fixed issue when plugin is called via code from frontend

= 3.2.1 =
* fixed compatibility issue with Archivarix External Images Importer

= 3.2.0 =
* changed rename process management
* added new option "debug mode"
* pushed minimum required Wordpress version to 5.0
* pushed minimum required PHP version to 7.1

= 3.1.0 =
* added new bulk operations "Rename from post" and "Rename and retitle from post", that gets the name for the media files from the post they are attached to
* changed database table creation logic (thanks to @rinatkhaziev for the contribution)

= 3.0.5 =
* fixed issue with ShortPixel

= 3.0.4 =
* fixed javascript error on single media page

= 3.0.3 =
* changed filename textbox size in attachment page

= 3.0.1 =
* fixed issue with older php versions

= 3.0.0 =
* added support for filename serialization

= 2.3.0 =
* added action 'pmr_renaming_successful'

= 2.2.5 =
* changed renaming error message to have more information about the source of the error

= 2.2.4 =
* fixed error with old files stored in year- month- based subfolders (many thanks to @jockolad who addressed the issue)

= 2.2.3 =
* fixed distribution error

= 2.2.2 =
* fixed issue with media files organized in subfolders

= 2.2.1 =
* fixed another issue with new big image management introduced in WordPress 5.3

= 2.2.0 =
* fixed issue with new big image management introduced in WordPress 5.3
* fixed issue with shortpixel image optimiser

= 2.1.1 =
* fixed default value for accent sanitization option

= 2.1.0 =
* added option to manage accent sanitization in file name

= 2.0.3 =
* fixed issue with multisite (many thanks to @synetech for addressing the issue)

= 2.0.2 =
* fixed localization support for link to settings page in plugin list

= 2.0.1 =
* added link to settings page in plugin list

= 2.0.0 =
* added settings page (thanks to @mrleif)

= 1.4.0 =
* added support for elementor

= 1.3.4 =
* changed post title management in bulk operations

= 1.3.3 =
* fixed issue with WPML

= 1.3.2 =
* removed unnecessary php warnings from log file (thanks to @alx359)

= 1.3.1 =
* removed unnecessary php warnings from log file (thanks to @alx359)

= 1.3.0 =
* fixed issue with WPML

= 1.2.6 =
* fixed issue with WP Compress plugin

= 1.2.5 =
* disabled submit button during bulk rename
* added js minification

= 1.2.4 =
* fixed error in Smart Slider custom table update

= 1.2.3 =
* fixed error in Smart Slider custom table update

= 1.2.2 =
* optimized sql for Smart Slider custom tables update

= 1.2.1 =
* fixed error in Smart Slider custom table update

= 1.2.0 =
* added support for Smart Slider

= 1.1.4 =
* fixed ajax notification issue on php 7

= 1.1.3 =
* added support for non-latin characters in file name

= 1.1.2 =
* added sanitization to file names

= 1.1.1 =
* added licence for phoenix icon

= 1.1.0 =
* added support for localization

= 1.0.1 =
* fixed author username

= 1.0.0 =
* Initial version

== Upgrade Notice ==

= 3.3.0 =

Added option to create 301 redirections for renamed files

= 3.2.5 =

Fixed compatibility issue with WPML