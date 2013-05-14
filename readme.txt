=== Plugin Name ===
Name: CM Download Manager
Contributors: CreativeMinds (http://www.cminds.com/)
Donate link: http://www.cminds.com/plugins
Tags: Downloads,forum,splunkbase,comments,Apps,Archives,Apps Management,Apps Download,Apps directory,Download Management,Download Directory,Download Plugin,Downloads Plugin,Add-on,Add-on management,Add-on directory,addon,directory plugin,wordpress directory,wordpress directory plugin,directory plugin,link dir,link directory,website plugin directory,counter,download counter,hits,hits counter,rate,rating,customer service,customer support,document management plugin,downlad tracker,download counter,download manager,download monitor,file management plugin,file manager 
Requires at least: 3.2
Tested up to: 3.5
Stable tag: 1.0.1

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

= 1.0 =
* Initial release

