=== F1Press ===
Contributors: LimeiraStudio
Tags: Formula1, F1, Grand Prix, aggregation, feed, BBC News, countdown, car, sport, racing, news, widget, Schumacher, Hamilton, Alonso, Rosberg, Vettel, McLaren, Ferrari, Mercedes, Sauber, Williams, sidebar, RSS
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CLS5HQLCVS2G8
Requires at least: 3.0.1
Tested up to: 4.1.1
Stable tag: 2.0


Displays the latest Formula1 news on your blog.

== Description ==

Now you can display information about races, qualifying results and drivers. You can insert a table of results in posts by using shortcodes.


1. Latest Info for ANY Race

[f1press type="race_results" season="2013" round="5"]

This shortcode will be display information about about the race in 2013 year and fifth in a row.
Next shortcode displays the results of the last race of the season:

[f1press type="race_results"]


2. Qualifications Results:

[f1press type="qualifying_results" season="2013" round="5"]

for qualifying results of race No5 in 2013.

OR

[f1press type="qualifying_results"]

for latest qualification results.


3. All races in the season of 2013 with additional info.

[f1press type="season_list" season="2013"]

For the current season:

[f1press type="season_list"]


4. Information about drivers can be display with the following shortcode:

[f1press type="driver_info" id="hamilton, massa, alonso"]

We can choose what driver info will be displayed in the "id" field. For example this shortcode gives to us information about Hamilton, Massa, Alonso. You can display information about any drivers of Formula1 of any years. 

[f1press type="driver_info" id="hamilton, massa, alonso" mode="table"]

The "mode" option chose the type of displaying. Mode "table" displays inormation in table view. For example.

Displays information in listing view with images and Wikipedia links for additional information.

Also, the plugin provides a customizable widget that displays the latest Formula1 news and the countdown to the next race in sidebar.
This plugin uses The Ergast Developer API.

== Installation ==

1. Upload the folder f1press to the /wp-content/plugins/ directory
2. Activate the plugin F1Press through the 'Plugins' menu in WordPress and use shortcodes.
3. Add the widget to your sidebar from Appearance->Widgets and configure the widget options.

== Changelog ==

= 1.0 =

* Initial release

= 1.5 =

* Change RSS Stream to BBC
* Code refactoring