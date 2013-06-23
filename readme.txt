=== Plugin Name ===
Name: CM Download Manager
Contributors: CreativeMinds (http://www.cminds.com/)
Donate link: http://www.cminds.com/plugins
Tags: Downloads,forum,splunkbase,comments,Apps,Archives,Apps Management,Apps Download,Apps directory,Download Management,Download Directory,Download Plugin,Downloads Plugin,Add-on,Add-on management,Add-on directory,addon,directory plugin,wordpress directory,wordpress directory plugin,directory plugin,link dir,link directory,website plugin directory,counter,download counter,hits,hits counter,rate,rating,customer service,customer support,document management plugin,downlad tracker,download counter,download manager,download monitor,file management plugin,file manager 
Requires at least: 3.2
Tested up to: 3.5
Stable tag: 1.3

Allow users to upload, manage, track and support documents or files in a directory listing structure for others to use and comment 
 
== Description ==
With this plugin you can make a directory where users can upload and manage Files / Downloads / Apps / Add-on / Packages / Plugins / Archives each containing detailed description. It contains download counter and support forum per each download page (like WP Plugin Directory)

**Use-Cases**

* Plugin Directory - Create a plugin directory similar to WordPress Plugins Directory. 
* Download Counter - Count downloads per each download
* Support forum - Support your users while letting them vote and answer existing topics per each download
* Customer Support - Support customers questions 
* File Manager - Manage files in a directory structure

**Features**

* Includes download counter.
* Includes voting per each download.
* Includes Download Categories.
* Member only downloads.
* Admin can manage downloads.
* Built in support forum per each download.
* Templet can be easily customized.
* Image preview for download screens.
* Filter download by internal Search
* Admin can define which file extensions are supported
* User can track from profile his downloads
* User can receive notification on new support questions

**Demo**

* Basic demo [Read Only mode](http://www.cminds.com/cmdownloads/).

**Pro Version**	

[Pro Version](http://www.cminds.com/downloads/cm-download-manager-pro/)
The Pro version adds a layer of powerful features to the CM Download Manager giving the admin better tools to customize the Answers system behavior, adding login support from social networks, adding shortcodes and support for categories and a lot more

* Social Media Registration Integration- Integrates with Facebook &amp; Google+ &amp; LinkedIn - [View Image One](http://www.cminds.com/wp-content/uploads/edd/image1.png) , [View Image 2](http://www.cminds.com/wp-content/uploads/edd/cm-answers-image2.png)
* Shortcodes/Widgets- Generate top contributors, recent updates and most download items list.
* View Restriction- Define per each download if it is open to non logged in users.  If only logged in user are allowed also define which user roles can view download. This can be set by Admin or Admin can give user ability to define. [View Image](http://www.cminds.com/wp-content/uploads/edd/22-06-2013-22-26-17.jpg)
* Password Protection- Protect download with password. [View Image](http://www.cminds.com/wp-content/uploads/edd/22-06-2013-22-33-08.jpg)
* Upload Restrictions- Define which user roles can create new downloads. [View Image](http://www.cminds.com/wp-content/uploads/edd/22-06-2013-22-39-35.jpg)
* URL / Shortcode- Allow to include URL or shortcode in download page instead of uploading a file. This is useful for integration with other plugins and support checkout carts and selling downloads. [Example of EDD integration](http://www.cminds.com/wp-content/uploads/edd/22-06-2013-22-41-44.jpg) , [Example of User Selection](http://www.cminds.com/wp-content/uploads/edd/22-06-2013-22-44-14.jpg) 
* Related Downloads- Show Related downloads in download page. [View Image](http://www.cminds.com/wp-content/uploads/edd/22-06-2013-22-45-53.jpg)
* Downloads Page Tabs- Show more tabs for download descriptions in download page
* Search- Filter search results by date, downloads and username
* Moderate User Comments- Admin can moderate user comments. [View Image](http://www.cminds.com/wp-content/uploads/edd/22-06-2013-22-47-33.jpg) 
* Auto-approve comments and answers from users- Admin can define a list of users which do not need moderation. [View Image](http://www.cminds.com/wp-content/uploads/edd/22-06-2013-22-47-33.jpg)
* Multisite- Supports multisite
* Public User Profile- Automatically generate a public profile page containing the downloads user posted with link to his social media profile. [View Image](http://www.cminds.com/wp-content/uploads/edd/22-06-2013-22-49-22.jpg)
* Gravatar - Ability to show Gravatar near user name and in user profile. [View Image](http://www.cminds.com/wp-content/uploads/edd/22-06-2013-22-49-22.jpg)
* Order Comments - Show comments in download page in ascending or descending order
* Localization Support - Forntend (user side) is localized
* View Count  - Show number of views in download page and control how view count is done (by view or by session)
* Gratitude Message - Does not include Gratitude message in the footer.



[Visit Pro Version Page](http://www.cminds.com/downloads/cm-download-manager-pro/)


**More About this Plugin**
	
You can find more information about CM Download Manager at [CreativeMinds Website](http://www.cminds.com/plugins/).


**More Plugins by CreativeMinds**

* [CM Super ToolTip Glossary](http://wordpress.org/extend/plugins/enhanced-tooltipglossary/) - Easily create Glossary, Encyclopedia or Dictionary of your terms and show tooltip in posts and pages while hovering. Many powerful features. 
* [CM Answers](http://wordpress.org/extend/plugins/cm-answers/) - Allow users to post questions and answers (Q&A) in a stackoverflow style forum which is easy to use, customize and install. w Social integration.
* [CM Invitation Codes](http://wordpress.org/extend/plugins/cm-invitation-codes/) - Allows more control over site registration by adding managed groups of invitation codes. 
* [CM Email Blacklist](http://wordpress.org/extend/plugins/cm-email-blacklist/) - Block users using blacklists domain from registering to your WordPress site.. 
* [CM Multi MailChimp List Manager](http://wordpress.org/extend/plugins/multi-mailchimp-list-manager/) - Allows users to subscribe/unsubscribe from multiple MailChimp lists. 


== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Manage your CM Download Manager from Left Side Admin Menu
4. All download are located on /cmdownloads/
5. User dashboard is located on /cmdownload/dashboard/
6. Before adding first download please define download categories

Note: You must have a call to wp_head() in your template in order for the JS plugin files to work properly.  If your theme does not support this you will need to link to these files manually in your theme (not recommended).

== Frequently Asked Questions ==

= How can I customize look&feel? =
In your template create a directory "CMDM". Inside you can place a structure similar to the one inside "cm-download-manager/views/frontend/". If the file can be found in your template directory, then it will have a priority. Otherwise, the default from plugin directory will be used.


== Screenshots ==

1. Main Download Page
2. Specific Download Page
3. Download Support Forum.
4. Support Forum Functionality.
5. Add download form
6. Download dashboard in user profile


== Changelog ==
= 1.3 = 
* Added info about PRO version

= 1.2 =
* Fixed daysAgo function, added hours/minutes/seconds
* Fixed translations
* Fixed bug with default screenshot not being displayed

= 1.1 =
* Added German and Polish localizations for frontend

= 1.0.3 =
* Fixed bug with not allowing to insert media to posts

= 1.0.2 =
* Change bug with comments display and max width


= 1.0 =
* Initial release

