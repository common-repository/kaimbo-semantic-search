=== KAIMBO Semantic Search ===
Contributors: Transinsight
Tags: search, semantic, synonym, fuzzy, ontology, highlight, index
Author URI: http://kaimbo.com
Plugin URI: http://wordpress.org/extend/plugins/kaimbo-semantic-search/
Requires at least: 3.4
Tested up to: 3.7.1
Stable tag: trunk
License: GPLv2 or later

KAIMBO semantic search represents a whole new level in website search. Readers will find related concepts, can search in pdfs, similar words.

== Description ==

<a href="http://kaimbo.com" target="_blank">KAIMBO</a> – Semantic Search for WordPress (Beta stage)           

Transinsight is developing the next generation of search technology. Now through our Wordpress plugin KAIMBO, the search boxes capabilities are enhanched to its full potential.

Please watch our short <a href="http://kaimbo.com/" target="_blank">Video Presentation</a>.
  
KAIMBO is a Plug-In for WordPress and provides semantic search. It goes far beyond keyword search and gives you the freedom to use your own personal background knowledge to improve search experience for the visitors of your site. They than can find what they did not without KAIMBO! Give it a try.

There are 4 editions available. The main limitation in the free version is that it will only index 50 pages (in the beta stage still more). You can compare the editions at the http://kaimbo.com/editions page.

Note: The plugin is still in a beta stage. We tested it thoroughly on several wordpress instances. Nevertheless it might happen to not be fully compatible with your environment. We appreciate your feedback very much, and we are prepared to fix the eventually appearing issues fast. You can check the known issues in the FAQ.

Note: The KAIMBO search results will be displayed only when the indexing of the website is finished. Until that the default wordpress search results are shown. This will take around 3 minutes for 100 pages. The admin user is informed about the progress.

Important note 1: The plugin works only on webpages hosted on a domain name accessible from the internet. Localhost, 127.0.0.1, password protected pages will not work, because the search server can not find these to index them, therefore it can not return search results. Only the online-editing feature will work.

Important note 2: The website must allow access to the crawler "Kaimbo 1.0" in the robots.txt. (or allow crawling generally) 

The features summarized:

* Improved Search

 * Highlighting: The searched keywords and concepts are highlighted in the results.

 * Instant re-crawling: If you change the knowledge base or the content of your web site KAIMBO updates the index automatically. If for example a synonym for a product was given it is instantaneously used for the next searches.
 
 * PDF-Indexing: PDF documents like product catalogs are also semantically indexed and found if searched concepts appear in them. This is possible because KAIMBO searches in it's own semantic index rather than in the Wordpress database.
 
 * Advanced keyword search: KAIMBO uses an elaborate algorithm for keyword based search. If your customers do not know the exact spelling of your products, KAIMBO helps to bridge the gap.
 
* Semantic Integration

 * Semantic search: KAIMBO goes beyond keywords and adds meaning to your search. You can bridge the gap between the vocabulary of your users or customers and your world.​ Synonyms and related concepts will be found, ensuring that the users will never miss the page, product or info they are looking for.
 
 * Smart Auto-Completion: In the search-box KAIMBO shows you not only the completed search term but also the related terms used for query expansion.
 
 * Concept highlighting: KAIMBO highlights the concepts from the knowledge base so that you can see at a glance what concepts are connected to meanings.
 
 * Knowledge-base editor: for large installations and huge knowledge-bases this tool allows for tailoring the background knowledge.

* Search Statistics

 * List of top searches: KAIMBO provides a table where you see the most common searches on your site. An unique feature to tailor your content to your visitors or customers.
 
 * Zero results list: this unique feature shows you the most important searches on your site; the ones that did not lead to any results. There you can find what customers were looking for but your site did not give any results.
 
 * List of last searches: You can see the list of latest searches by date. Using this table you can get a view about how often the readers search, what currently interests them.

* Comfort

 * One click installation: Just download and activate the plug-in and all is done. No registration is required! KAIMBO then creates automatically a semantic index over your web site e.g. your blog. The search box now acts intelligently.
 
 * Easy login: Login directly “to your web site”. This unique feature allows to edit KAIMBO's knowledge “in place”. See the movie for more details.
 
 * In place editing: unparalleled speed-up in changing, extending and correcting your site by in place editing.

http://kaimbo.com

When we are showing the list of searches, in the details we present the ip and user agent of the user who executed the seach. This means, we store this data on our servers, and in this case privacy implications have to be considered. From our side, the single purpose to store this, is to present it in the search result lists, and we are not showing the data to any third party. If this is still not save enough for you, please contact us, and we may prepare a plugin version which is not storing and showing the ip data with the searches.

We have put effort to avoid conflicts or incompatibility with other plugins. If you still find a problem, please report it (kaimbo@transinsight.com)

This plugin requires PHP5.

The plugin works with the current latest Firefox, Safari, Chrome, Opera and Internet Explorer. It is possible to use it with Internet explorer 8, but for all design features use Internet Explorer 9 or higher.  

You can see the plugin in action on our websites at http://kaimbo.com and http://transinsight.com .

You can try it out yourself at the <a href="http://playground.kaimbo.com" target="_blank">http://playground.kaimbo.com</a>.

== Installation ==

1. Install it from the plugin repository, or copy it in your wordpress instalation's '/wp-content/plugins/' folder.

2. Activate the plugin through the 'Plugins' menu in WordPress.

 The plugin registers your site to our service, and the indexing starts. By default we do this at 1 page/sec. If your server allows it, you can increase the speed.
 
 You got informed about the process, and you can change the options in the Settings / KAIMBO menu in WordPress.
 
 If you have 60 pages, this will take around 1 minute, and so on. 
 
 The seach will return the default wordpress search results until the indexing is finished ! For the admin user a popup is shown telling how many percent of the indexingS is ready. 

3. You are ready to use the non-semantic features as advanced keyword search, and search in documents.

4. Now for the semantic part:
  - In the Settings / KAIMBO menu select the users who can edit the Knowledge base of your site.

5. Selecting words on your website (select and left click on it, and use the popup menu), you can add concepts and related concepts to your knowlegde base.
  - after this, for example if you added "WordPress" as "wp", users searching "wp" will found pages containing the word WordPress 
  - the more concepts and related concepts you add the easier will the readers find what they are looking for.
  
Important note: The plugin works only on webpages hosted on a domain name accessible from the internet. Localhost, 127.0.0.1, password protected pages will not work, because the search server can not find these to index them, therefore it can not return search results. Only the online-editing feature will work.  
  
After installation:

* In the Settings / KAIMBO menu you will found statistics about what was searched in your page, when, how often.
 
== Frequently Asked Questions ==

= Is the plugin working on localhost, or password protected pages ? =
 The plugin works only on webpages hosted on a domain name accessible from the internet. Localhost, 127.0.0.1, password protected pages will not work, because the search server can not find these to index them, therefore it can not return search results. Only the online-editing feature will work.

= Why don't I see how many results were found for a search ? =
It looks like your theme is not supporting this by default. You can display the count adding the following in your theme's 'search.php' file (`/wp-content/themes/<your-theme>/search.php`):

`<?php
	global $wp_query;
	echo $wp_query->found_posts;
?>`
	
Or already formatted:	
`<?php echo ti_getResultcount(); ?>`

= How can I add concepts and related concepts (synonyms) to the knowledge base ? =

For example the word KAIMBO is related to "search plugin". In this case, if we add the concept "Kaimbo" with the related concept "search plugin" to the knownedge base, visitors searching for one of these will found also the pages which contain the other concept's words.

1. Enable concept editing for the users who will do this.

2. Select a word or words with the mouse, then click on the selection.

3. In the appearing popup, select add as concept. (With add as synonym you can add it to an already existing concept).

4. Now your new concept will be highlighted, it will appear in the search autocompletion, and will be searched together with all its synonyms.

Note: The new or modified concepts will appear in the search results in about five minutes. 

= How can I change the colors of the concept and keyword highlighting ? =

From the version 1.9.0 the colors can be set in the plugin options. If write access is not working, or you want to define it by the theme, you can do it the following way:

These colors are defined in 3 classes. The only thing you have to do, is to add the colors and styles of your wish to these classes in your theme's CSS.
You can also switch to underlining or anything else what CSS can do. 

The background colof of the concepts:
`.TermAnnotation {
  background-color: #EEEEAA;
}`

Backgrounf of the keywords and concepts in the search result page:
`.KeywordHighlight , .TermHighlight { 
	background-color: #EEEEEE;
}`

The color of the searched words in the search results list:
`.KeywordHighlight {
    color: #73990E;
}`

= How to use the online editor ? =

1. Enable editing for the users who will be allowed to edit the website.

2. Select a word or words with the mouse, then click on the selection.

3. In the appearing popup, select "Edit paragraph".

4. An Editing toolbar will appear, and the selected paragraph becomes changeable. When it is finished, click on Save. The page will refresh, showing the saved new content.

Select a paragraph or words in a paragraph with the mouse

= Known issues =

1. Plugin prequisites are not met: 
  We included 3 methods to connect to the search service, curl, file_get_contents, and wp_remote_post. You can see the tests for these at the plugin's setting page. If they are enabled but not working, this suggest a firewall settings problem, which the hosting provider may have overlooked. In one case already was fixed by opening the https (443) port by the hosting provider, you can try that too.
 
2. My admin / page layout is modified by the plugin
 The reported case was solved, but we still may have incompatibilities with certain themes / theme builders. In this case we appreciate if you report us the problem, mentioning the theme name, ideally with saving the page as complete web page, and sending us to kaimbo@transinsight.com. This will be solved also in the near future.

== Other Notes ==

== Changelog ==

= 2.7.3 =

For a certain website configuration, the frontpage was detected as a composite page, and blocked the crawling of the rest of the website.

= 2.7.2 =

The online editor hides the [***] tags during the editing. It is also possible to configure a background color for the edited paragraph.

= 2.7.1 =

For a certain website configuration, the frontpage was detected as a composite page, and blocked the crawling of the rest of the website.

= 2.7.0 =

The composite pages are not indexed anymore, only pages and post. This way there are no wrong links in the search results.

= 2.6.0 =

User informed better if the robots.txt blocks the indexing.
Fix for the concept editing popup window.
Kaimbo options link position works with fixed headers.

= 2.5.0 =

Important update! We removed the prototype library from the plugin, this solves conflict issues with other plugins or themes.

= 2.3.0 =

New feature (enterprise): The pages can be indexed divided by headers having ids. This means jumping directly to the correct part of a long page from the search result link is possible.

New feature: the external page results can be ordered by post date.

= 2.2.5 =

A hook added which allows using a special service URL. 

= 2.2.4 =

More design improvements - popup placing.

Possibility to search the results by date - for enterprise. 

= 2.2.3 =

Design improvements, fixes.

= 2.2.2 =

This update improves the search result display for enterprise users having external website links.

= 2.2.1 =

Highlighting fix for the newly introduced original Wordpress results -> no hinglighting.

= 2.2.0 =

New option, to allow showing the original WordPress excerpt in the search result list.

= 2.1.1 =

This update improves the search result display for enterprise users having external website links.

= 2.1.0 =

The Kaimbo Options and Wp login link can be aligned to the right of the search box now.
CSS changes for the new WP theme.

= 2.0.7 =

Handling of external urls in the search result for Enterprise subcribers.

= 2.0.6 =

Options link appears also if login link is disabled.
Script loading order changed.

= 2.0.5 =

Online editor does not show up for pages having dynamic - not editable - content.
#login and #logout commands can be used in the search box.

= 2.0.4 =

Possibility for enterprise users to have search results from the directly linked external pages (1 depth). 

= 2.0.3 =

Crawling is better regulated.

= 2.0.2 =

Before crawling is finished, there is a slightly better way to display the original wordpress search results, then we did before.

= 2.0.1 =

This version fixes the bug introduced in the last version, where the autocompletion was not always working.

= 2.0.0 ==

A conflict between jQuery and Prototype is handled now, the plugin allows using the $ sign in themes. (even though using it without defining it for the range of code where is used is a bad practice)

= 1.9.7 ==

This update effects the users who had server problems with kaimbo. The prequisite checks are fixed. 

= 1.9.5 = 

Protection against an eventual server failuire. Shorter timeout for requesting kaimbo scripts, the page load time is increased much less than before.

= 1.9.2 =

Adding concepts when the highlighting is turned off is possible.

In the service:
- Fix for deleting concepts using the Vocabulary view.
- Fix for "NOT" queries.

= 1.9.0 =

Easy way to delete concepts from the knowledge base.

Options for enable-disable highlighting and setting the colors.

Queries containing brackets work (AND queries work).

= 1.8.2 =

The last version had an incorrectly opsitioned script, which sometimes caused "header already sent" errors. This is fixed.

= 1.8.1 =

CSS corrections to the previous update.
The page limit and the new version message sometimes appeared unnecessarily.

= 1.8.0 =

Reorganized plugin settings page, better overview.

The search can be tested in the backend, and it will not be activated on the website if the website has more pages than the active crawling limit. 

= 1.7.0 =

A bug is fixed in handling concepts containing quotes or special characters. They are now correctly escaped and usable.

= 1.6.0 =

In this update we increased significantly the speed of the autocompletion in the search box, and several other service calls. Before we used the slower proxied secure requests everywhere, now besides the knowledge base editing we switched to a faster direct calling approach.

= 1.5.0 =

Important update ! The .htaccess settings of several websites prevented loading one of our scripts, thus the concept editing and autocompletion functionality was not working. This update should overcome that problem.

= 1.4.0 =

Modified website registration process after the plugin installation. Website registration is not allowed anymore.

= 1.3.7 =

We saw occasionally problems during the first activation of the plugin. Now we also will recieve the error messages, and using it we will fix the problem.

= 1.3.6 =

The wordpress login, options, and the crawling progress dialog were hidden for several themes. Now they are also attached to the body, not in the searchbox, so they will show up always.

= 1.3.5 =

When installing on a local server, which is not accessible from the internet, the corresponding message is displayed in the plugin settings. (in this case the plugin can not index the website). 

Javascript fix for the Internet Explorer 8.

= 1.3.2 =

Better structured plugin settings.

Easy way to send feedback, report eventual problems.

= 1.3.1 =

After the activation, the indexing process percentage is shown in the backend, and in the frontend for the admin user.

= 1.3.0 =

For the admin users, who are testing the plugin, we show a warning popup saying, that until the crawling process is not finished, we return the default wordpress results.

= 1.2.5 =

Faster autocompletion in the search field.

Timeouts included (3x5 sec), this means we can not block the websites using the plugin, even if the service would hang for some reason.

= 1.2.1 = 

At a free hosting privider, the ssl certification authorities very not correctly set. This is included now in the plugin, so it will still work in these cases. 

New test in the prequisites, check if the hosting privider adds/modifies the results sent by our service. If yes, addressing the hosting provider is necessary, the plugin not work with modified data.   

= 1.2.0 =

Important update. On the websites where the theme differ from the default themes (not having a #content div), the highlighting and the online editor did not work. Now these websites got this feature also, with automatic content div detection.

= 1.1.7 =

New setting in the plugin options. By default, the unlogged users will not see clickable concepts.
This means that after the page is loaded, we don't send a highlighting request to the server.

= 1.1.6 =

Increased stability on the search.

= 1.1.5 =

Increased security against unauthorized modifying the concepts and synonyms, better protected access key.
CSS for the popups.
New button in the plugin settings to reload the rebots.txt.

= 1.1.0 =

This version contains the final prequisite checks and solutions for connecting to the server issues.

= 1.0.17 =

On the hostings which are blocking the connections, there were 1 warning and 1 error displayed, now it is hidden.

= 1.0.16 =

On the hostings which are blocking the connections, there were 1 warning and 1 error displayed, now it is hidden.

= 1.0.15 =

If curl is not enabled or fails, file_get_contents is used, if that fails, wp_remote_post is used.
If none of this works, then there is a problem with the wordpress hosting, like a firewall. In this case the provider has to be contacted.

= 1.0.14 =

In the plugin settings page, the outgoing https port is verified. If this is closed, the plugin can not connect to the search service.

= 1.0.13 =

A "curl" and "file_get_contents" call test is included in the plugin settings page, and we display the corresponding YES or NO.
Currently we use curl. If we found servers where curl fails but file_get_contents works, we implement the fall-back to the second.

= 1.0.12 =

In the bottom of the settings page a new link to show the phpinfo. It is protected from non-admin users.

= 1.0.11 =

Correction in the pagination of results for pages with changing url structure.

= 1.0.10 =

Eventual error messages are hidden from the website, they are logged and displayed in the Kaimbo settings.

= 1.0.9 =

There was a warning message appearing on host with safe_mode enabled in the php, this is fixed now.  

= 1.0.8 =

Improved stability. 

= 1.0.7 =

The access key is stored now in the database.

= 1.0.6 =

One step closer to solve the https connection fail problem.

= 1.0.5 =

On several specific hosting server the https (secure) connection to our service was failing. This wersion should solve this issue. 

= 1.0.4 =
The exact error message is displayed for the "Error connecting to the server problem".

= 1.0.3 =
This version tests the update functionality, because the generated access key was deleted, and several websites failed to reregister after the update.

= 1.0.2 =
Check if php curl module is enabled, and show the corresponding error message, if not.

= 1.0.1 =
After Uninstall and reinstall, the plugin did not allow reregistration. This is solved by the uninstall procedure now. 

== Upgrade Notice ==

== Screenshots ==

01. KAIMBO gives your users a much broader and complete search experience. Search terms are expanded with background knowledge, a vocabulary witch can be tailored to your needs.
02. Search-terms are than expanded with it’s related concepts form the background vocabulary and nicly visualized in the auto-completion menu.
03. Below the the search-box with KAIMBO intalled, you find a really easy way to login to Word press.
04. Just login to your WordPress site without even leaving your web site!
05. Once you loged in below the search-box you finde KAIMBO options.
06. The options provide some very helpful features like a direct shortcut to the WordPress site for the plug-in (backend). Or a direct visualization of the knowledge-base vocabulary.
07. The knowledge-base visualized ...
08    Adding related concepts for a term used in the text to the knowledge-base is simple: just select it, click left mouse and click on “add as concept”.
09. It is very simple to add synonyms, related concepts or spelling variants ...
10. If a concept (like semantic search) is also (part of) a link, a menu alows you to choose while editing your page ...
11. In the backend comprehensive tables show you what are the top queries and – more important – the queries witch did not lead toany search result. That way you can tailor your background vocabulary to address these queries.
12. One of the coolest features of KAIMBO is in-line editing. Just select the word or paragraph you want to edit and do changes in place.
13. On edit parargraph an almost complet editor appears and allows you to apply changes in place without even leaving the page or knowing anything about HTML.
14. KAIMBO is based on 10 years of research in the life sciences. Transinsight is the provider of GoPubMed.com, the first semantic search engine in the internet. Please visit our site to explore more!
