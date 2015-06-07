=== Audit Trail ===
Contributors: johnny5
Donate link: http://urbangiraffe.com/contact/#donate
Tags: admin, audit, log, version, diff
Requires at least: 3.5
Tested up to: 4.2
Stable tag: trunk

Audit Trail is a plugin to keep track of what is going on inside your blog by monitoring administration functions.

== Description ==

Audit Trail is a plugin to keep track of what is going on inside your blog. It does this by recording certain actions (such as who logged in and when) and storing this information in the form of a log. Not only that but it records the full contents of posts (and pages) and allows you to restore a post to a previous version at any time.

To summarise:

* Log of user actions inside your blog - useful for finding out who did what in a multi-user system
* Extensible, allowing other plugins the ability to add and display items in the Audit Trail
* Ability to track registered user page visits
* Fully localized

Audit Trail is available in:

* English
* Estonian by Lembit Kivisik
* Belorussian by Marcis G
* Simplified Chinese by maoanyuan
* German by Andreas Beraz
* Japanese by Chestnut
* Romanian by Mikalay Lisica
* Lithuanian by Nata Strazda

== Installation ==

The plugin is simple to install:

1. Download `audit-trail.zip`
1. Unzip
1. Upload `audit-trail` directory to your `/wp-content/plugins` directory
1. Go to the plugin management page and enable the plugin
1. Configure the plugin from `Management/Audit Trail`

You can find full details of installing a plugin on the [plugin installation page](http://urbangiraffe.com/articles/how-to-install-a-wordpress-plugin/).

== Screenshots ==

1. Audit trail

== Documentation ==

Full documentation can be found on the [Audit Trail Page](http://urbangiraffe.com/plugins/audit-trail/) page.

== Changelog ==

= 1.2.4 =
* Don't include revisions in a post update log entry
* Track plugin activation/deactivation

= 1.2.3 =
* Fix ajax bugs introduced by 1.2.2

= 1.2.2 =
* Fix double-serialization of audit data
* Fix ignore user 0 to ignore all anonymous users
* Small code cleanup

= 1.2.1 =
* Refresh for WP 4
* Italian translation by Massimiliano

= 1.2 =
* Fix pagination
* Add failed login auditing
* Experimental error_log() support to be used alongside fail2ban

= 1.1.16 =
* Fix WP 3.5 warning

= 1.1.15 =
* Restore per-page for logs to 25

= 1.1.14 =
* Clean up all code
* XSS review

= 1.1.13 =
* Lithuanuan
* Russian

= 1.1.12 =
* Add bulk action to bottom of trail list
* Fix pager
* Updated Romanian translation

= 1.1.11 =
* Use WP local time to for audit trail

= 1.1.10 =
* 3.2 compat
* Romanian translation, thanks to Mikalay Lisica

= 1.1.9 =
* Add Japanese translation, thanks to Chestnut
* Fix bug with display post details
* Fix delete button

= 1.1.7 =
* Add Chinese translation, thanks to maoanyuan!

= 1.1.6 =
* Add Belorussian translation

= 1.1.5 =
* Fix actions to monitor

= 1.1.4 =
* Add Estonian
* Put delete item back

= 1.1.3 =
* Remove deep slashes

= 1.1.2 =
* Don't save post differences
* Fix username in CSV export

= 1.1.1 =
* jQueryify
* Remove post edit difference - this has been built into WordPress for several versions now

= 1.1    =
* WP 2.8 compatibility

= 1.0.10 =
* Only include prototype on AT pages

= 1.0.9  =
* WP 2.5 compatibility

= 1.0.8  =
* Show log items according to blog timezone offset

= 1.0.7  =
* Fix favicon.ico logs
* Ignore certain users
* Track failed login attempts

= 1.0.6  =
* Fix warning
* Allow searching by username

= 1.0.5  =
* Fix expiry
* Stop logging auto-saves

= 1.0.4  =
* Support for Admin SSL

= 1.0.3  =
* Fix typos.
* Add option to reverse post edit order

= 1.0    =
* Revised code
* More AJAX action
* Extensible auditors

= 0.3    =
* Made work with different database prefixes

= 0.2    =
* Added versioning history

= 0.1    =
* Initial release
