LG Add CP Tabs - _Automatically add custom CP Tabs (and links) for members when they register!_
===============================================================================================

**This addon is for testing purposes only and is considered a public beta and should be used for testing purposes only!!!**.

**This ExpressionEngine addon requires Morphine (the painless ExpressionEngine framework) _for CP styles)_. Grab the latest version of Morphine from [http://github.com/newism/nsm.morphine.ee_addon](http://github.com/newism/nsm.morphine.ee_addon) and follow the readme instructions to install.**

LG Add CP Tabs is a Multi-site Manager compatible ExpressionEngine extension that allows you to create a set of default CP tabs and links for new members.

Requirements
------------

* **Morphine**: Morphine (the painless ExpressionEngine framework) is required for CP styles. Grab the latest version of Morphine from [http://github.com/newism/nsm.morphine.ee_addon](http://github.com/newism/nsm.morphine.ee_addon) and follow the readme instructions to install.
* **ExpressionEngine**: NSM Quarantine requires ExpressionEngine 1.6.8+. New version update notifications will only be displayed if LG Addon Updater is installed.
* **Server**: Your server must be running PHP5+ or greater on a Linux flavoured OS.

Installation
------------

* Install and activate Morphine (the painless ExpressionEngine framework) available from: [http://github.com/newism/nsm.morphine.ee_addon](http://github.com/newism/nsm.morphine.ee_addon)
* Copy all the downloaded folders into your EE install. Note: you may need to change the <code>system</code> folder to match your EE installation
* Activate the LG Add CP Tabs extension.
* Add default tabs and links to member groups in the extension settings

Updating from 1.0.0
-------------------

Automatic update is currently disabled for this version. To update:

* Copy your existing extension settings
* Delete all references to Lg\_add\_cp\_tabs\_ext from the exp_extensions db table by running the following sql:  
<code>DELETE FROM `exp_extensions` WHERE `class` = 'Lg\_add\_cp\_tabs\_ext'</code>
* Remove:
	* <code>system/extensions/ext.lg\_add\_cp\_tabs\_ext.php</code>
	* <code>system/languages/english/lang.lg\_add\_cp\_tabs\_ext.php</code>
* Install as normal

TODO
----

* Remove Morphine dependency