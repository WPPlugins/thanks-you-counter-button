=== Thank You Counter Button ===
Contributors: shinephp, Whiler
Donate link: http://www.shinephp.com/donate/
Tags: thanks, thank you, like, counter, button, tracker, dashboard, sidebar, widget, shortcode, statistics, hook
Requires at least: 4.0
Tested up to: 4.6
Stable tag: 1.9.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds 'Thank You' button to every post/page, counts and shows a total number of visitors 'thanks' for post/page.

== Description ==

This is the visitor's 'Thank you' or 'I like it' clicks counter button. Every time a new visitor clicks the "Thank you" button, one point is added to the total "thanks" counter for this post which can be seen at the same button.

The plugin stores its counters in two own MySQL tables. Only one "thank" for this IP-address can be permitted. Plugin can skip all further "Thank you" clicks from this IP-address once it is automatically registered. IP-address click limit can be set to a time interval in seconds.

Plugin has Statistics data table which shows posts list with total thanks quant for every post and time of the latest thank. Rows in the table can be filtered by posting month, category, can be sorted by thanks quant or time of latest thank in the descending or ascending order. Selected post can be viewed or edit directly from this table.

Admin dashboard and sidebar widgets with list of latest thanked or the largests thanked posts (between 3 and 15) are available. Total quant of thanks can be shown. Use widgets control panels to change settings according to your preferences.

The set of shortcodes and content filters is available for this plugin. Visit http://www.shinephp.com/thank-you-counter-button-wordpress-plugin/ for more details.


== Installation ==

Installing procedure:

Attention! 
This plugin will work properly for that WordPress installation only which has "CREATE" permission on its MySQL database. "Create" permission is needed as plugin creates two own DB tables to work with.
1. Deactivate plugin if you have the previous version installed. (It is important requirement for switching to this version from a previous one.)
2. Extract "thanks-you-counter-button.x.x.x.zip" archive content to the "/wp-content/plugins/thanks-you-counter-button" directory.
3. Activate "Thank You Counter Button" plugin via 'Plugins' menu in WordPress admin menu. 
4. Go to the "Settings"-"Thanks CB" menu item and check/change your preferences to customize how this plugin will work for you.

== Screenshots ==
1. screenshot-1.png The example of "Thank You" button in blue color
2. screenshot-2.png The "Thank You" Counter Button Settings Page part 1.

Check more screenshots and "Thank You" button in action at [shinephp.com](http://shinephp.com/thank-you-counter-button-wordpress-plugin/)

== Frequently Asked Questions ==
- Plugin doesn't work. What is wrong? 

  The most probable reason is the MySQL database permissions problem for your WordPress installation. This plugin will work properly for that WordPress installation only which has "CREATE" permission on its MySQL database. "Create" permission is needed as plugin creates two own DB tables to work with.
  From version 1.6.1. plugin writes any database errors into tycb.log file at plugin folder. Look at this file for more information about your problem.

- I updated plugin to the recent version. Why does it shows button or Settings/Statistics pages wrong way.

  Your browser uses old cashed version of CSS files. Please try to reload full page (use F5 or Refresh button).

- Does this plugin work with Wordpress MU or WordPress Multisite?

  Yes, it does. Plugin is tested with WordPress MU 2.9.1 and WordPress Multisite 3.0.1. Thanks to WordPress developers. Separate tables for thanks counters are created for each blog instance where plugin is activated. Every blog has its own plugin settings to manage its presentation and behaviour.

== Changelog ==

= [1.9.3] 30.08.2016 =
* Fix: text domain changed to the thanks-you-counter-button to correspond to WordPress plugin translation requirements
* Fix: "<?" tags at .php files replaced with "<?php" to exclude possible syntax errors at some installations

= [1.9.2] 19.08.2015 =
* "Thanks Stat" widget disappeared from widgets list after update to WordPress 4.3 - fixed.

= [1.9.1] 09.08.2015 =
* Optimizing JavaScript/CSS code loading - it's not necessary now to turn on "Display button on Pages" to show it at the static front page when you use a shortcode for that.
* CSS updated to enhance compatibility with themes, which may override styles for the "button" element.

= 1.9 =
* 24.07.2015
* Widget constructor was updated to provide the  compatibility with WordPress version 4.3
* Ajax calls were refactored to work via WordPress API.
* Plugin's functionality is protected globally by 'manage_options' user capability.
* Code for plugin deletion via WordPress was updated.
* Minimal WordPress version required was changed to 4.0.

= 1.8.8 =
* 28.01.2015
* Fatal error during activation was fixed.
* Compatibility with WordPress 4.1 was marked.
* Serbian translation was added. Thanks to [Ogi](http://firstsiteguide.com/)

= 1.8.7 =
* 25.02.2014
* Brasilian Portuguese translation was added. Thanks to [André Maito](http://www.clickright.com.br)

= 1.8.6 =
* 19.12.2013
* CSS fix was applied for compatibility with WordPress default themes: 2012, 2013, 2014

= 1.8.5 =
* 12.12.2013
* Minor bug fixes
* Spanish translation update. Thanks to Andrew Kurtis [WebHostingHub](http://www.webhostinghub.com/)
* Some English grammar mistake fixes.
* WordPress version compatibility information is updated to 3.8.

= 1.8.4 =
* 19.02.2013
* Link to Detailed Statistics page from settings shortcuts is fixed. 
* WordPress version compatibility information is updated to 3.5.1.

= 1.8.3 =
* 20.10.2012
* Fix for cross-site scripting vulnerability reported by Charlie Eriksen via [Secunia SVCRP](http://secunia.com). 'paged' parameter sent to thankyou_statistics.php via GET request was not sanitized properly.

= 1.8.2 =
* 22.05.2012
* CSS update for compatibility with WordPress 3.4;
* JavaScript code update: Reset counter bug was fixed. More universal button HTML elements selection is used. Previous one didn't work if buttons numeration was started for some reason not from 1.

= 1.8.1 =
* 07.04.2012
- Lithuanian translation is added, thanks to Vincent G.

= 1.8 =
* 02.03.2012
- CSS classes (thanks_quant_for_post, thanks_total_quant_label, thanks_total_quant_value) were added to sidebar widget. Use it to make your widget more attractive.
- Colon is removed from button caption. So add it manually to the button caption, if you need that.
- ShinePHP.com RSS feed box was removed from the plugin Settings page.
- Sidebar widget data could be filtered by category now. Just select needed category from the dropdown list at the widget configuration form.
- Thanks from custom post types are shown in the widgets now.
- TYCB plugin Settings form is updated (check your settings after version update, just in case they were changed suddenly ;) ).
- TYCB Statistics data tab is moved from Settings page to the separate menu item under Tools menu.
- Button image is changed slightly when you hover mouse on it.

= 1.7.4 =
* 16.01.2011
- Chinese Simple translation is added.

= 1.7.3 =
* 02.01.2011
- Bug is fixed. If button is turned off on the home page [nothankyou] shortcode (if used) was visible in the post excerpt at the home page. Thanks to [Whiler](http://blogs.wittwer.fr/whiler/) for discovering that.

= 1.7.2 =
* 21.11.2010
- Turkish translation is added.

= 1.7.1 =
* 04.10.2010
- Italian translation update.

= 1.7 =
* 19.09.2010
- You can use a single global thanks counter button beyond the posts and pages. Place this PHP code &lt;?  echo thanks_button('Thank You', true); ?&gt; somewhere on your blog page using theme files and you and your visitors will see it.
- Uninstall cleanup feature (uninstall.php) is added: plugin deletes all its options and database tables .
- Usage of deprecated since WordPress 3.0 staff is excluded. Plugin is fully compatible with WordPress 3.0 now.
- shinephp.com news section is added to the plugin Settings page.

To look on full changes list visit [this page](http://shinephp.com/thank-you-counter-button-wordpress-plugin/2/#changlog).

== Upgrade Notice ==
= 1.8.6 =
CSS fix was applied for compatibility with WordPress default themes: 2012, 2013, 2014


== Additional Documentation ==
Additional documentation such as content filter hook list, available shortcodes description can be found at this link http://www.shinephp.com/thank-you-counter-button-wordpress-plugin/2/#filterhooks


You can find more information about "Thank You Counter Button" plugin at this page
http://www.shinephp.com/thank-you-counter-button-wordpress-plugin/

I am ready to answer on your questions about plugin usage. Use ShinePHP forum at
http://shinephp.com/community/forum/thank-you-counter-button
plugin page comments or site contact form for it please.
